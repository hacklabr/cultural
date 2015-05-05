<?php

class Cultural_Hightlights {

    const FRONT_PAGE_SLUG = '_FRONT_PAGE_';
    const OPTION_NAME = 'cultural_highlights';

    protected static $idsCache = array();

    static function getOption($key) {
        $option = wp_parse_args(
            get_option(self::OPTION_NAME), self::getDefaultOptions()
        );

        return isset($option[$key]) ? $option[$key] : false;
    }


    protected static function _getPostIdsByConfig($key, $type) {
        if(!$key){
            $key = self::getCurrentPageKey();
        }

        $cid = $key . $type;
        if(isset(self::$idsCache[$cid])){
            return self::$idsCache[$cid];
        }

        $option = self::getOption($key);

        if (!isset($option[$type]) || !$option[$type]) {
            return array();
        }

        $post_ids = array();

        foreach (explode("\n", $option[$type]) as $url){
            $url = trim($url);
            $post_id = url_to_postid($url);
            if($post_id){
                $post_ids[] = $post_id;
            }
        }

        self::$idsCache[$cid] = $post_ids;

        return $post_ids;
    }

    protected static function _getArgsByConfig($key, $type){
        $post_ids = self::_getPostIdsByConfig($key, $type);
        $args = null;
        if($post_ids){
            $args = array(
                'ignore_sticky_posts' => true,
                'post__in' => $post_ids,
                'posts_per_page' => -1,
                'order' => 'ASC',
                'orderby' => 'post__in'
            );
        }elseif($type === 'highlights'){
            $args = array(
                'ignore_sticky_posts' => true,
                'posts_per_page' => 4
            );

            if($key !== self::FRONT_PAGE_SLUG){
                $args['category_name'] = $key;
            }
        }

        return $args;
    }

    protected static function _getPostsByConfig($key, $type) {
        if(!$key){
            $key = self::getCurrentPageKey();
        }

        $args = self::_getArgsByConfig($key, $type);

        if($args){
            return get_posts($args);
        }else{
            return array();
        }
    }

    protected static function _getQueryByConfig($key, $type) {
        if(!$key){
            $key = self::getCurrentPageKey();
        }

        $args = self::_getArgsByConfig($key, $type);

        if($args){
            return new WP_Query($args);
        }else{
            return null;
        }
    }

    static function getCurrentPageKey(){
        if(is_category()){
            $cat = get_queried_object();
            $key = $cat->slug;
        }else{
            $key = Cultural_Hightlights::FRONT_PAGE_SLUG;
        }

        return $key;
    }

    static function getHighlightedPosts($key = null) {
        return self::_getPostsByConfig($key, 'highlights');
    }

    static function getFixedPosts($key = null) {
        return self::_getPostsByConfig($key, 'fixed');
    }

    static function getHighlightedQuery($key = null) {
        return self::_getQueryByConfig($key, 'highlights');
    }

    static function getFixedQuery($key = null) {
        return self::_getQueryByConfig($key, 'fixed');
    }

    static function getHighlightedPostIds($key = null) {
        return self::_getPostIdsByConfig($key, 'highlights');
    }

    static function getFixedPostIds($key = null) {
        return self::_getPostIdsByConfig($key, 'fixed');
    }

    static function init() {
        add_action('admin_init', array(__CLASS__, 'initAdmin'));
        add_action('admin_menu', array(__CLASS__, 'addMenu'));
    }

    static function initAdmin() {
        register_setting(self::OPTION_NAME, self::OPTION_NAME);
    }

    static function addMenu() {
        $topLevelMenuLabel = __('Post destacados', 'cultural');
        $page_title = __('Post destacados', 'cultural');
        $menu_title = __('Post destacados', 'cultural');

        /* Top level menu */
        add_submenu_page('highlights', $page_title, $menu_title, 'manage_options', 'highlights', array(__CLASS__, 'renderPage'));
        add_menu_page($topLevelMenuLabel, $topLevelMenuLabel, 'manage_options', 'highlights', array(__CLASS__, 'renderPage'));
    }

    static function getDefaultOptions() {
        $item_cgf = array(
            'highlights' => '',
            'fixed' => ''
        );

        $result = array(
            self::FRONT_PAGE_SLUG => $item_cgf
        );

        foreach (get_terms('category') as $term) {
            $result[$term->slug] = $item_cgf;
        }

        return $result;
    }

    static function renderPage() {
        $homeObject = new stdClass;

        $homeObject->slug = self::FRONT_PAGE_SLUG;
        $homeObject->name = __('Página principal', 'cultural');

        $terms = array_merge(array($homeObject), get_terms('category'));

        // Crie o formulário. Abaixo você vai ver exemplos de campos de texto, textarea e checkbox. Crie quantos você quiser
        ?>
        <style>
            .highlights { border-bottom:1px solid #bbb; }
            .highlights section { float:left; margin:10px; width:450px; }
            .highlights textarea { width:100%; height:100px; }
        </style>
        <div class="wrap span-20">
            <h2><?php _e('Destaques', 'cultural'); ?></h2>

            <?php if (isset($_GET['settings-updated']) && $_GET['settings-updated'] == 'true'): ?>
                <div id="message" class="updated below-h2">
                    <p>Configurações salvas.</p>
                    <div class="clear"></div>
                </div>
                <script type="text/javascript">
                    setTimeout(function () {
                        jQuery('#message').slideUp('fast');
                    }, 2000);
                </script>
            <?php endif; ?>

            <form action="options.php" method="post" class="clear prepend-top">
                <p class="alignright">
                    <input type="submit" class="button-primary" value="<?php _e('Salvar modificações', 'cultural'); ?>" />
                </p>
                <?php settings_fields(self::OPTION_NAME); ?>
                <div class="span-20 ">
                    <p class="help">
                        <?php _e("Cole na caixas abaixo as URLs dos posts que você deseja destacar para cada seção do site, uma URL por linha, na ordem que você deseja que apareça.<br>Deixe em branco se desejar que sejam destacados os últimos posts.",'cultural'); ?>
                    </p>
                    <?php
                    foreach ($terms as $term):
                        $name = function($option_name) use($term) {
                            echo Cultural_Hightlights::OPTION_NAME . "[{$term->slug}][{$option_name}]";
                        };

                        $option = Cultural_Hightlights::getOption($term->slug);
                        ?>
                        <div class="highlights">
                            <h3><?php echo $term->name ?></h3>
                            <section class="js-posts">
                                <strong><?php _e('Posts destacados', 'cultural'); ?></strong><br>
                                <textarea name="<?php echo $name('highlights') ?>"><?php echo $option['highlights'] ?></textarea>
                            </section>

                            <section class="js-posts">
                                <strong><?php _e('Posts fixos', 'cultural'); ?></strong><br>
                                <textarea name="<?php echo $name('fixed') ?>"><?php echo $option['fixed'] ?></textarea>
                            </section>
                            <div class="clear"></div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <p class="textright clear prepend-top">
                    <input type="submit" class="button-primary" value="<?php _e('Salvar modificações', 'cultural'); ?>" />
                </p>
            </form>
        </div>

        <?php
    }

}

Cultural_Hightlights::init();
