<?php

session_start();

// CONGELADO

include dirname(__FILE__) . '/includes/congelado-functions.php';
include dirname(__FILE__) . '/includes/html.class.php';
include dirname(__FILE__) . '/includes/utils.class.php';
include dirname(__FILE__) . '/includes/opengraph.php';

if (!function_exists('get_theme_option')) {

    function get_theme_option($option_name) {
        $option = wp_parse_args(
            get_option('theme_options'), get_theme_default_options()
        );
        return isset($option[$option_name]) ? $option[$option_name] : false;
    }

}

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
            'primary' => __('Primary Menu', 'cultural'),
            'secondary' => __('Secondary Menu', 'cultural'),
            'mobile' => __('Mobile Menu', 'cultural')
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
    global $wp_query;
    if (is_category() && $wp_query->query['template'] === 'events') {

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


//    wp_deregister_script('jquery'); // Remove WordPress core's jQuery
    wp_register_script('jquery'); //, '//ajax.googleapis.com/ajax/libs/jquery/2.1.3/jquery.min.js', false, null, false);
//    add_filter('script_loader_src', 'theme_jquery_local_fallback', 10, 2);


    wp_enqueue_style('cultural-style', get_stylesheet_uri());

    /* JUDO Font Awesome for the icons */
    wp_enqueue_style('font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css');

    wp_enqueue_script('respond', get_template_directory_uri() . '/js/min/respond-min.js', '', '1.4.0');

    wp_enqueue_script('imagesloaded', get_template_directory_uri() . '/js/min/imagesloaded-min.js', '', '3.1.8', true);

    wp_enqueue_script('masonry', get_template_directory_uri() . '/js/min/masonry-min.js', '', '3.1.5', true);

    wp_enqueue_script('responsive-nav', get_template_directory_uri() . '/js/min/responsive-nav-min.js', array('jquery'), '1.0.32', true);

    wp_enqueue_script('slider', get_template_directory_uri() . '/js/min/idangerous.swiper-min.js', array('jquery'), '1.0.32', true);

    /* Modernizr */
    wp_enqueue_script('modernizr', get_template_directory_uri() . '/js/modernizr.js', '', '2.6.2');

    wp_enqueue_script('main', get_template_directory_uri() . '/js/main.js', array('imagesloaded', 'masonry'), '', true);

    /* Load the comment reply JavaScript. */
    if (is_singular() && get_option('thread_comments') && comments_open())
        wp_enqueue_script('comment-reply');


    //_pr(get_queried_object());


    $savedFilters = get_theme_option('mapasculturaisconfiguration');
    //var_dump(array_keys($savedFilters['classificacaoEtaria']));
    $configModel = MapasCulturaisConfiguration::getConfigModel();
    foreach ($savedFilters as $key => $data) {
        if ($configModel[$key]->type === 'entity') {
            foreach ($data as $id => $json) {
                $data[$id] = json_decode($json);
            }
        } elseif (is_array($data)) {
            $data = array_keys($data);
        } elseif ($key == 'geoDivisions') {
            $data = json_decode($data);
        }
        $savedFilters[$key] = $data;
    }


    $vars = array(
        'generalFilters' => $savedFilters,
        'linguagens' => $savedFilters['linguagens'],
        'classificacoes' => $savedFilters['classificacaoEtaria'],
        'apiUrl' => API_URL
    );

    if (is_category()) {
        $category = get_queried_object();
        $vars['catid'] = $category->cat_ID;
        $vars['categoryFilters'] = get_option("category_{$category->cat_ID}");
    }

    wp_localize_script('main', 'vars', $vars);
}

add_action('wp_enqueue_scripts', 'cultural_scripts');

//ANGULAR APP ASSETS FROM THEATRO MUNICIPAL
if (!is_admin()) {
    add_action('wp_print_scripts', function () {
        wp_enqueue_script('is_mobile', get_bloginfo('template_directory') . '/js/lib/is_mobile.js', array('jquery'), null, false);

//        if(is_home() || is_archive() && get_post_type() === 'evento'){
        //ANGULAR
        wp_enqueue_script('moment', get_bloginfo('template_directory') . '/js/lib/moment.js', array('jquery'), null, false);
        wp_enqueue_script('moment-ptbr', get_bloginfo('template_directory') . '/js/lib/moment.pt-br.js', array('moment'), null, false);

        wp_enqueue_script('angular-core', get_bloginfo('template_directory') . '/js/lib/angular.min.js', array('moment-ptbr'), null, false);
        //angular stable: wp_enqueue_script('angular-core', 'https://ajax.googleapis.com/ajax/libs/angularjs/1.2.22/angular.min.js', array('moment-ptbr'), null, false);
        wp_enqueue_script('angular-ui-router', get_bloginfo('template_directory') . '/js/lib/angular-ui-router.js', array('angular-core'), null, false);
        //wp_enqueue_script('angular-resource', '//ajax.googleapis.com/ajax/libs/angularjs/1.2.15/angular-resource.min.js', array('angular-route'), null, false);

        wp_enqueue_script('daterangepicker', get_bloginfo('template_directory') . '/js/lib/daterangepicker.js', array('angular-core'), null, false);
        wp_enqueue_script('angular-daterangepicker', get_bloginfo('template_directory') . '/js/lib/angular-daterangepicker.js', array('angular-core'), null, false);

        wp_enqueue_script('angular-sanitize', get_bloginfo('template_directory') . '/js/lib/angular-sanitize.js', array('angular-core'), null, false);

        wp_enqueue_script('angular-app', get_bloginfo('template_directory') . '/js/ng-app/app.js', array('angular-core'), null, false);
        wp_enqueue_script('angular-app-services', get_bloginfo('template_directory') . '/js/ng-app/services.js', array('angular-app'), null, false);
        wp_enqueue_script('angular-app-controllers', get_bloginfo('template_directory') . '/js/ng-app/controllers.js', array('angular-app-services'), null, false);

        //LOCALIZE
        wp_localize_script('angular-core', 'Directory', array(
            'url' => get_bloginfo('template_directory'),
            'site' => get_bloginfo('wpurl')
        ));
    });

    add_action('wp_print_styles', function() {
        wp_enqueue_style('daterange', get_bloginfo('template_directory') . '/js/lib/daterangepicker-bs3.css');
    });
}

/**
 * Register widgets areas
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function cultural_widgets_init() {
    register_sidebar(array(
        'name' => __('Header Widget Area', 'cultural'),
        'description' => __('Appears in the header area, under the magnifying glass icon', 'cultural'),
        'id' => 'header-widget-area',
        'before_widget' => '<aside id="%1$s" class="widget  %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget__title">',
        'after_title' => '</h3>',
    ));

    register_sidebar(array(
        'name' => __('Content Widget Area', 'cultural'),
        'description' => '',
        'id' => 'content-widget-area',
        'before_widget' => '<aside id="%1$s" class="widget  %2$s">',
        'after_widget' => "</aside>",
        'before_title' => '<h3 class="widget__title">',
        'after_title' => '</h3>',
    ));
}

add_action('widgets_init', 'cultural_widgets_init');

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Custom functions that act independently of the theme templates.
 */
require get_template_directory() . '/inc/extras.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

require get_template_directory() . '/inc/category-colors.php';

/**
 * Extra classes for the widgets
 */
require get_template_directory() . '/inc/widgets-extra-classes.php';

/**
 * Handle Mapas Culturais support
 */
require get_template_directory() . '/inc/mapas-culturais.php';

add_filter('nav_menu_link_attributes', function($attr, $item) {
    if ($item->object == 'category') {
        $cat_data = get_option('category_' . $item->object_id);
        $cat_color = $cat_data['color'];

        $attr['style'] = 'background-color:' . $cat_color;
    }
    return $attr;
}, 10, 4);
