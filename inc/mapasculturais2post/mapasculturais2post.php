<?php

class MapasCulturais2Post {

    const EVENT_FIELDS = 'id,singleUrl,name,subTitle,project.id,project.name,project.singleUrl,owner.id,owner.name,owner.singleUrl,classificacaoEtaria,traducaoLibras,descricaoSonora,shortDescription,description,occurrences';

    static function init() {


        // hook para salvar infos do metabox
        add_action('save_post', array(__CLASS__, 'savePost'));

        // hooks ajax para pegar informações no mapas culturais
        add_action('wp_ajax_mapas_check_event_url', array(__CLASS__, 'ajaxCheckEventUrl'));
        add_action('wp_ajax_mapas_get_event_info', array(__CLASS__, 'ajaxGetEventInfo'));
        add_action('wp_ajax_mapas_get_event_image', array(__CLASS__, 'ajaxGetEventImages'));

        add_filter('http_request_host_is_external', array(__CLASS__, 'addExternalSafeURL'), 0, 3);

        add_action('admin_enqueue_scripts', array(__CLASS__, 'addAdminPostJS'));

        // add shortcode
        add_shortcode('evento', array(__CLASS__, 'shortcode'));

        add_action('media_buttons', array(__CLASS__, 'mediaButton'), 1000);
    }

    static function addExternalSafeURL($false, $host, $url) {
        return strpos($url, MAPASCULTURAIS_URL) === 0;
    }

    static function mediaButton() {
        add_thickbox();
        ?>

        <style>
            .mc-image { float:left; margin:5px; border: 5px solid transparent; padding: 0}

            .mc-image.selected { border-color: green; }
            /*.mc-image:hover { border-color: blue; }*/
            .mc-image input { display:none; }
        </style>

        <script id="mc-import-template" type="text/html">
            <h2>Imagens do {{typeName}} {{name}}</h2>
            <p>
                <a id="mc-import-image-all" class="button">Importar todas</a>
                <a id="mc-import-image-selected" class="button">Importar selecionadas</a>
                <span id="mc-import-image--search-spinner" class="spinner" style="display:none;"></span>
                <span id="mc-import-image--feedback" style="display:none; float:right;">Imagens inportadas e anexadas ao post.</span>
            </p>
            {{#images}}
            <label class="mc-image">
                <input type="checkbox" value="{{url}}" class="js-image-checkbox"/>
                <img src="{{thumbUrl}}" data-full-url="{{url}}">
            </label>
            {{/images}}
        </script>

        <div id="mc-import-images" style="display:none;">
            <h2>
                <?php _e('Importar imagens da plataforma '); ?> <?php echo MAPASCULTURAIS_NAME ?>
            </h2>
            <p>
                <label>
                    <input id="mc-import-image--search-url" type="text" placeholder="<?php _e("Url do evento, projeto, agente ou espaço", 'cultural') ?>" style="width:350px;">
                </label>
                <a id="mc-import-image--search-button" class="button"> Buscar Imagens </a>
            </p>
            <span id="mc-import-image--import-spinner" class="spinner" style="display:none;"></span>

            <div id="mc-import-image--result-container">

            </div>
        </div>

        <a href="#TB_inline?width=768&height=750&inlineId=mc-import-images" class="thickbox button">
            <?php _e("Importar imagens da plataforma"); ?> <?php echo MAPASCULTURAIS_NAME; ?>
        </a>
        <?php
    }

    static function addAdminPostJS($hook) {
        global $post;

        if ($hook == 'post-new.php' || $hook == 'post.php') {
            if ('post' === $post->post_type || 'noticias' === $post->post_type) {

                wp_enqueue_script('mustache', get_stylesheet_directory_uri() . '/js/min/mustache.js');
                wp_enqueue_script('mapasculturais2post', get_stylesheet_directory_uri() . '/includes/mapasculturais2post/admin.js');

                wp_localize_script('mapasculturais2post', 'mc', array(
                    'apiUrl' => MAPASCULTURAIS_API_URL
                ));
            }
        }
    }

    static function ajaxGetEventImages() {
        require_once('importimages.php');
        $post_id = $_POST['post_id'];
        $post = get_post($post_id);
        $image_url = $_POST['image_url'];
        $post_title = str_replace(dirname($image_url) . '/', '', $image_url);
        global $wpdb;

        $checkExists = $wpdb->get_var($wpdb->prepare("SELECT COUNT(ID) FROM $wpdb->posts WHERE post_parent = %d AND post_type = 'attachment' AND post_title = %s", $post_id, $post_title));

        if (!$checkExists) {
            $newatt = array(
                'post_date_gmt' => $post->post_date_gmt,
                'post_title' => str_replace(dirname($image_url) . '/', '', $image_url),
                'post_status' => 'publish',
                'post_parent' => $post_id,
                'post_type' => 'attachment'
            );

            process_attachment($newatt, $image_url);
        }

        die;
    }

    static function ajaxGetEventInfo() {

        header('Content-Type: application/json');
        $event_url = $_POST['event_url'];
        $event_info = self::getEventInfoFromAPI($event_url);
        echo json_encode($event_info);
        die;
    }

    static function ajaxCheckEventUrl() {
        $event_url = $_POST['event_url'];
        $valid = self::parseEventUrl($event_url);

        if ($valid)
            echo 'ok';
        else
            echo 'erro';

        die;
    }

    static function getEventInfoFromAPIProxy($event_url, $use_transient = false, $files = 'avatar,gallery', $select = self::EVENT_FIELDS) {
        return self::_getEventInfo(true, $event_url, $use_transient, $files, $select);
    }

    static function getEventInfoFromAPI($event_url, $use_transient = false, $files = 'avatar,gallery', $select = self::EVENT_FIELDS) {
        return self::_getEventInfo(false, $event_url, $use_transient, $files, $select);
    }

    static function _getEventInfo($use_proxy, $event_url, $use_transient = false, $files = 'avatar,gallery', $select = self::EVENT_FIELDS) {

        $transient_key = 'event_info:' . md5("$event_url:$files:$select:$use_proxy");

        if ($use_transient && !current_user_can('edit_posts')) {
            $event_info = get_transient($transient_key);
            if ($event_info) {
                return $event_info;
            }
        }

        if ($event_id = self::parseEventUrl($event_url)) {
            if($use_proxy){
                $event_json = self::getEventJSONFromAPIProxy($event_id, $files, $select);

            }else{
                $event_json = self::getEventJSONFromAPI($event_id, $files, $select);
            }

            if ($event_json) {
                $event_info = self::parseEventJSON($event_json);

                if ($use_transient) {
                    set_transient($transient_key, $event_info, TRANSIENTE_TIMEOUT_EVENT_INFO);
                }

                return $event_info;
            }
        }

        return false;
    }

    static function parseOccurrences($occurrences) {
        $date_format = get_option('date_format');
        return array_map(function($e) use($date_format) {
            $occ = new stdClass;

            $occ->startsAt = @$e->rule->startsAt;
            $occ->endsAt = @$e->rule->endsAt;

            $_startsOn = new DateTime(@$e->rule->startsOn->date);
            $occ->startsOn = $_startsOn->format($date_format);

            $_endsOn = new DateTime(@$e->rule->endsOn->date);
            $occ->endsOn = $_endsOn->format($date_format);

            $occ->duration = @$e->rule->duration;
            $occ->price = trim(@$e->rule->price);

            $occ->description = @$e->rule->description;

            $space = new stdClass;

            $space->id = $e->space->id;
            $space->name = $e->space->name;
            $space->shortDescription = $e->space->shortDescription;
            $space->singleUrl = $e->space->singleUrl;

            if ($e->space->avatar) {
                $space->avatar = new stdClass;

                $space->avatar->url = @$e->space->avatar->url;
                $space->avatar->files = new stdClass;

                foreach ($e->space->avatar as $group => $f) {
                    if(is_object($f)){
                        $space->avatar->files->$group = $f->url;
                    }
                }
            }

            $occ->space = $space;

            return $occ;
        }, $occurrences);
    }

    static function parseEventJSON($event_json) {
        $event = json_decode($event_json);
        if ($event->occurrences) {
            $event->occurrences = self::parseOccurrences($event->occurrences);
        }
        $files = new stdClass;
        foreach ($event as $prop => $val) {
            if (substr($prop, 0, 7) === '@files:') {
                $file_prop = substr($prop, 7);
                $files->$file_prop = $val->url;
                unset($event->$prop);
            }
        }

        if ($files) {
            $event->files = $files;
        }

        return $event;
    }

    static function parseEventUrl($event_url) {
        if (preg_match('#' . preg_quote(MAPASCULTURAIS_URL) . 'evento?\/(id:)?(?<id>[0-9]+)(\/.*)?#', $event_url, $match)) {
            return $match['id'];
        } else {
            return false;
        }
    }


    static function getEventJSONFromAPIProxy($event_id, $files = 'avatar,gallery', $select = self::EVENT_FIELDS) {
        $result = MapasCulturaisApiProxy::fetch(MAPASCULTURAIS_URL . "api/event/findOne/?id=EQ({$event_id})&@select={$select}&@files=({$files}):url");
        return $result->body;
    }

    static function getEventJSONFromAPI($event_id, $files = 'avatar,gallery', $select = self::EVENT_FIELDS) {
        $url = MAPASCULTURAIS_URL . "api/event/findOne/?id=EQ({$event_id})&@select={$select}&@files=({$files}):url";
        $rs = wp_remote_get($url, array('timeout' => '120'));

        $json = false;
        if ($rs['response']['code'] == 200) {
            if (isset($rs['body']) && $rs['body']) {
                $json = $rs['body'];
            }
        } else {
            $json = false;
        }
        return $json;

    }

    static function savePost($post_id) {
        // verify if this is an auto save routine.
        // If it is our form has not been submitted, so we dont want to do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times

        if (!isset($_POST[__CLASS__ . '_noncename']) || !wp_verify_nonce($_POST[__CLASS__ . '_noncename'], 'save_' . __CLASS__))
            return;


        // Check permissions
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return;
        }
        else {
            if (!current_user_can('edit_post', $post_id))
                return;
        }

        // OK, we're authenticated: we need to find and save the data
        if (isset($_POST[__CLASS__])) {
            foreach ($_POST[__CLASS__] as $meta_key => $value)
                update_post_meta($post_id, $meta_key, $value);
        }
    }

    /**
     * Custom functions that act independently of the theme templates
     *
     * Eventually, some of the functionality here could be replaced by core features
     *
     * @package cultural
     */

    /**
     * Shortcode to display an event
     *
     */
    static function shortcode($atts) {

        extract(shortcode_atts(array(
            'type' => 'post',
            'order' => 'date',
            'orderby' => 'title',
            'posts' => -1,
            'color' => '',
            'fabric' => '',
            'category' => '',
                ), $atts));

        $url = isset($atts[0]) ? trim($atts[0]) : null;


        if (!$url) { // se não foi informado uma url
            if (current_user_can('edit_post')) {
                return "<div class='shortcode-error'>Informe a url do evento dentro do tag evento da seguinte forma: <strong>[evento http://" . MAPASCULTURAIS_URL . "evento/0000]</strong></div>";
            }
        } else { // se a url foi informada
            global $__event_url, $__image;

            if (substr($url, -1) != '/') {
                $url .= '/';
            }

            $__image = 'avatar.avatarBig';
            $__event_url = $url;

            ob_start();
            get_template_part('partials/event-box');
            return ob_get_clean();
        }
    }



}

MapasCulturais2Post::init();
