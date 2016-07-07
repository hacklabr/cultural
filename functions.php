<?php
session_start();

function inc($file){
    require __DIR__ . '/inc/' . $file;
}

// metaboxes
inc('metaboxes/link.php');
inc('metaboxes/mapasculturais-entity-relation.php');

// post types
inc('post-types/marca.php');

// theme options
inc('theme-options/destaques.php');
inc('theme-options/mapasculturais-configuration.php');
inc('theme-options/mapasculturais-configuration-category.php');

// plugin do mapas culturais
inc('mapasculturais2post/mapasculturais2post.php');

// proxy da api do mapas cuturais
inc('mapasculturais-api-proxy.php');

// Custom template tags for this theme.
inc('template-tags.php');

// Custom functions that act independently of the theme templates.
inc('extras.php');

// Customizer additions.
inc('customizer.php');
inc('category-colors.php');

// Extra classes for the widgets
inc('widgets-extra-classes.php');

if (!isset($content_width))
    $content_width = 1000;

if (!function_exists('cultural_setup')) :

    function cultural_setup() {

        /*
         * Make theme available for translation.
         * Translations can be filed in the /languages/ directory.
         */
        load_theme_textdomain('cultural', get_template_directory() . '/languages');

        /**
         * Add styles to post editor (editor-style.css)
         */
        add_editor_style();

        // Add default posts and comments RSS feed links to head.
        add_theme_support('automatic-feed-links');

        /*
         * Enable support for Post Formats.
         * See http://codex.wordpress.org/Post_Formats
         */
        add_theme_support('post-formats', array('aside', 'chat', 'image', 'gallery', 'link', 'video', 'quote', 'audio', 'status'));

        /*
         * Enable support for Post Thumbnails on posts and pages.
         *
         * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
         */
        add_theme_support('post-thumbnails');

        register_nav_menus(array(
            'primary' => __('Menu Principal', 'cultural'),
            'secondary' => __('Menu Secundário', 'cultural'),
            'mobile' => __('Menu Mobile', 'cultural')
        ));

        /*
         * Switch default core markup for search form, comment form, and comments
         * to output valid HTML5.
         */
        add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption'));

        // Setup the WordPress core custom background feature.
        add_theme_support('custom-background', apply_filters('cultural_custom_background_args', array(
            'default-color' => '#f5f5f5'
        )));

        // filtra os padroes dos uploads
        update_option('image_default_align', 'center');
    }

    add_action('after_setup_theme', 'cultural_setup');
endif;


/*
 *  ================ REWRITE RULES ================ *
 */
add_action('generate_rewrite_rules', function ($wp_rewrite) {
    // URL no formato http://theatro/orquestra-sinfonica/videos terão o primeiro
    // nível como sendo o post_name e o segundo o template correspondente pra exibicao
    $new_rules = array(
        "^eventos/([^/]+)$" => "index.php?template=events&category_name=" . $wp_rewrite->preg_index(1),
    );
    $wp_rewrite->rules = $new_rules + $wp_rewrite->rules;

    return $wp_rewrite;
});

add_filter('query_vars', function ($public_query_vars) {
    $public_query_vars[] = "template";
    return $public_query_vars;
});

add_action('template_redirect', function() {

    if (is_category() && get_query_var('template') === 'events') {

        include __DIR__ . '/page-templates/events-list.php';
        die;
    }
});

function theme_jquery_local_fallback($src, $handle) {
    static $add_jquery_fallback = false;

    if ($add_jquery_fallback) {
        echo '<script>window.jQuery || document.write(\'<script src="' . get_template_directory_uri() . '/assets/js/vendor/jquery-1.10.2.min.js"><\/script>\')</script>' . "\n";
        $add_jquery_fallback = false;
    }

    if ($handle === 'jquery') {
        $add_jquery_fallback = true;
    }

    return $src;
}

/**
 * Enqueue scripts and styles.
 */
function cultural_scripts() {

    wp_enqueue_script('jquery');

    wp_enqueue_style('cultural-style', get_stylesheet_uri(), array('magnific-popup'));

    $js_lib_path = get_bloginfo('template_directory') . (WP_DEBUG ? '/js/lib/' : '/js/min/');

    /* JUDO Font Awesome for the icons */
    wp_enqueue_style('font-awesome', get_bloginfo('template_directory') . '/css/font-awesome-4.3.0/css/font-awesome.min.css');

    wp_enqueue_script('event-emmiter', $js_lib_path . 'EventEmitter.js', array('jquery'), '3.1.8', true);

    wp_enqueue_script('imagesloaded', $js_lib_path . 'imagesloaded.pkgd.js', array('jquery', 'event-emmiter'), '3.1.8', true);

    wp_enqueue_script('masonry', $js_lib_path . 'masonry.pkgd.js', '', '3.1.5', true);

    wp_enqueue_script('responsive-nav', $js_lib_path . 'responsive-nav.js', array('jquery'), '1.0.32', true);

    /* Modernizr */
    wp_enqueue_script('modernizr', $js_lib_path . 'modernizr.js', '', '2.6.2');

    wp_enqueue_script('magnific-popup', $js_lib_path . 'jquery.magnific-popup.js', array('jquery'), '2.6.2');
    wp_enqueue_style('magnific-popup', get_bloginfo('template_directory') . '/css/magnific-popup.css');

    wp_enqueue_script('slider', get_bloginfo('template_directory') . '/js/min/idangerous.swiper-min.js', array('jquery'), '1.0.32', true);
    wp_enqueue_script('main', get_bloginfo('template_directory') . '/js/main.js', array('imagesloaded', 'masonry'), '', true);

    /* Load the comment reply JavaScript. */
    if (is_singular() && get_option('thread_comments') && comments_open())
        wp_enqueue_script('comment-reply');


    //_pr(get_queried_object());


    $savedFilters = MapasCulturaisConfiguration::getOption();
    //var_dump(array_keys($savedFilters['classificacaoEtaria']));
    $configModel = MapasCulturaisConfiguration::getConfigModel();

    $empty = [];

    foreach ($savedFilters as $key => $data) {
        if ($configModel[$key]->type === 'entity') {
            foreach ($data as $id => $json) {
                $data[$id] = json_decode($json);
            }
        } elseif (is_array($data)) {
            $_data = array_keys(array_filter($data, function($e) {
                    if ($e){
                        return $e;
                    }
            }));

            if($_data){
                $data = $_data;
            }else{
                $data = array_keys($data);
                $empty[$key] = true;
            }
        }

        $savedFilters[$key] = $data;

    }

    $savedFilters['empty'] = $empty;

    $geoDivisions = array();

    foreach ($savedFilters as $key => $val) {
        if (substr($key, 0, 3) === 'geo') {
            unset($savedFilters[$key]);
            $geoDivisions[$key] = $val;
        }
    }

    $savedFilters['geoDivisions'] = $geoDivisions;

    $vars = array(
        'generalFilters' => $savedFilters,
//        'linguagens' => $savedFilters['linguagens'],
//        'classificacoes' => $savedFilters['classificacaoEtaria'],
//        'geoDivisions' => $geoDivisions,
        'apiUrl' => MapasCulturaisApiProxy::getProxyURL()
    );


    if (is_category()) {
        $category = get_queried_object();

        $catFilters = array('geoDivisions' => array());


        foreach (get_option("category_{$category->cat_ID}") as $key => $options) {
            if (substr($key, 0, 3) === 'geo' && $options) {
                $catFilters['geoDivisions'][$key] = array_keys(array_filter($options));

            } elseif (in_array($key, array('linguagens', 'classificacaoEtaria'))) {
                $catFilters[$key] = array();
                foreach ($options as $name => $val) {
                    if ($val) {
                        $catFilters[$key][] = $name;
                    }
                }
            } else {
                $catFilters[$key] = $options;
            }
        }

        $vars['catid'] = $category->cat_ID;
        $vars['categoryFilters'] = $catFilters;
    }

    wp_localize_script('main', 'vars', $vars);
}

add_action('wp_enqueue_scripts', 'cultural_scripts');

//ANGULAR APP ASSETS FROM THEATRO MUNICIPAL
if (!is_admin()) {
    add_action('wp_print_scripts', function () {
        $js_lib_path = get_bloginfo('template_directory') . (WP_DEBUG ? '/js/lib/' : '/js/min/');

        wp_enqueue_script('is_mobile', get_bloginfo('template_directory') . '/js/lib/is_mobile.js', array('jquery'), null, false);

//        if(is_home() || is_archive() && get_post_type() === 'evento'){
        //ANGULAR
        wp_enqueue_script('moment', $js_lib_path . 'moment.js', array('jquery'), null, false);
        wp_enqueue_script('moment-ptbr', $js_lib_path . 'moment.pt-br.js', array('moment'), null, false);

        wp_enqueue_script('angular-core', $js_lib_path . 'angular.js', array('moment-ptbr'), null, false);

        wp_enqueue_script('angular-ui-router', $js_lib_path . 'angular-ui-router.js', array('angular-core'), null, false);
        //wp_enqueue_script('angular-resource', '//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular-resource.min.js', array('angular-route'), null, false);

        wp_enqueue_script('daterangepicker', get_bloginfo('template_directory') . '/js/lib/daterangepicker.js', array('angular-core'), null, false);
        wp_enqueue_script('angular-daterangepicker', get_bloginfo('template_directory') . '/js/lib/angular-daterangepicker.js', array('angular-core'), null, false);

        wp_enqueue_script('angular-sanitize', $js_lib_path . 'angular-sanitize.js', array('angular-core'), null, false);

        wp_enqueue_script('angular-app', get_bloginfo('template_directory') . '/js/ng-app/app.js', array('angular-core'), null, false);
        wp_enqueue_script('angular-app-services', get_bloginfo('template_directory') . '/js/ng-app/services.js', array('angular-app'), null, false);
        wp_enqueue_script('angular-app-controllers', get_bloginfo('template_directory') . '/js/ng-app/controllers.js', array('angular-app-services', 'main'), null, false);

        //LOCALIZE
        wp_localize_script('angular-core', 'Directory', array(
            'url' => get_bloginfo('template_directory'),
            'site' => get_bloginfo('wpurl')
        ));
    });


    add_action('wp_print_styles', function() {
        wp_deregister_style('cultural-style');
        wp_register_style('daterange', get_bloginfo('template_directory') . '/js/lib/daterangepicker-bs3.css');
        wp_register_style('cultural-style', get_stylesheet_uri(), array('daterange', 'magnific-popup'));
        wp_enqueue_style('cultural-style');
    }, 0);
}

/**
 * Register widgets areas
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function cultural_widgets_init() {
    register_sidebar(array(
        'name' => __('Área de widget do header', 'cultural'),
        'description' => __('Aparece na área do header, sob o ícone da lupa', 'cultural'),
        'id' => 'header-widget-area',
        'before_widget' => '<aside id="%1$s" class="widget  %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget__title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('Área de widget do conteúdo', 'cultural'),
        'description' => '',
        'id' => 'content-widget-area',
        'before_widget' => '<aside id="%1$s" class="widget  %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget__title">',
        'after_title' => '</h3>',
    ));
}

add_action('widgets_init', 'cultural_widgets_init');

// add_filter('nav_menu_link_attributes', function($attr, $item) {
//     if ($item->object == 'category') {
//         $cat_data = get_option('category_' . $item->object_id);
//         $cat_color = $cat_data['color'];
//         $attr['style'] = 'background-color:' . $cat_color;
//     }
//     return $attr;
// }, 10, 4);
