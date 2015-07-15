<?php

class MapasCulturaisApiProxy {

    static function init() {
        $request_url = 'http' . (isset($_SERVER['HTTPS']) ? 's' : '') . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $proxy_url = self::getProxyURL();

        if (preg_match('#^' . preg_quote($proxy_url) . '#', $request_url)) {
            $query = str_replace($proxy_url, MAPASCULTURAIS_API_URL, $request_url);
            $response = self::fetch($query);
            self::response($response);
            die;
        }
    }

    static function getProxyURL(){
        $blog_url = get_bloginfo('url');

        if (substr($blog_url, -1) !== '/') {
            $blog_url .= '/';
        }

        $proxy_url = $blog_url . 'MAPI/';

        return $proxy_url;
    }

    static function fetch($query) {
        $cache_id = 'MAPI:' . md5($query);

        if(!$response = wp_cache_get($cache_id)){

            $rs = wp_remote_get($query, array('timeout' => '120'));

            $response = new stdClass;

            $response->responseCode = $rs['response']['code'];
            $response->responseMessage = $rs['response']['message'];

            $response->apiMetadata = isset($rs['headers']['api-metadata']) ? $rs['headers']['api-metadata'] : false;
            $response->contentType = $rs['headers']['content-type'];
            if ($response->responseCode == 200 && $response->contentType == "application/json") {
                $bodyObject = json_decode($rs['body']);

                self::replaceEntityURLs($bodyObject);

                $response->body = json_encode($bodyObject);
            } else {

                $response->body = $rs['body'];
            }

            wp_cache_set($cache_id, $response,'',120);
        }

        return $response;
    }

    static function response($response) {
        if ($response->apiMetadata) {
            header('api-metadata: ' . $response->apiMetadata);
        }

        if ($response->contentType) {
            header('content-type: ' . $response->contentType);
        }

        if ($response->responseCode) {
            if(function_exists('http_response_code')){
                http_response_code($response->responseCode);
            }else{
                header("HTTP/1.1 {$response->responseCode} {$response->responseMessage}");
            }
        }

        echo $response->body;

        exit;
    }


    static $objects = array();

    static function replaceEntityURLs(&$object) {
        global $wpdb;
        self::mapObjects($object);

        $urls = implode("','",array_map(function($e){
            return addslashes($e[0]->singleUrl);
        }, array_filter(self::$objects, function($e){
            return (bool) $e[0]->singleUrl;
        })));

        $query = "SELECT post_ID, meta_value FROM {$wpdb->postmeta} WHERE meta_value IN('{$urls}')";

        if($wpdb->use_mysqli){
            $rs = mysqli_query($wpdb->dbh, $query);

            while($obj = @mysqli_fetch_object($rs)){
                $post_permalink = get_permalink($obj->post_ID);
                if(isset(self::$objects[$obj->meta_value]) && is_array(self::$objects[$obj->meta_value])){
                    foreach(self::$objects[$obj->meta_value] as $o){
                        $o->singleUrl = $post_permalink;
                    }
                }
            }
        }else{
            $rs = mysql_query($query);

            while($obj = @mysql_fetch_object($rs)){
                $post_permalink = get_permalink($obj->post_ID);
                if(isset(self::$objects[$obj->meta_value]) && is_array(self::$objects[$obj->meta_value])){
                    foreach(self::$objects[$obj->meta_value] as $o){
                        $o->singleUrl = $post_permalink;
                    }
                }
            }
        }

    }

    static function mapObjects(&$object) {
        if(is_array($object)){
            foreach($object as $i => $obj){
                self::mapObjects($obj);
            }
        }else if(is_object($object)){
            foreach ($object as $prop => $val){
                if($prop == 'singleUrl'){
                    if(!isset(self::$objects[$val])){
                        self::$objects[$val] = array();
                    }
                    self::$objects[$val][] = $object;

                }else if(is_array($val) || is_object($val)){
                    self::mapObjects($val);
                }
            }
        }
    }

}


MapasCulturaisApiProxy::init();