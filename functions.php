<?php

session_start();

if ( ! isset( $content_width ) )
	$content_width = 1000;

if ( ! function_exists( 'cultural_setup' ) ) :
function cultural_setup() {

	/*
     * Make theme available for translation.
     * Translations can be filed in the /languages/ directory.
     */
	load_theme_textdomain( 'cultural', get_template_directory() . '/languages' );

	/**
     * Add styles to post editor (editor-style.css)
     */
	add_editor_style();

	// Add default posts and comments RSS feed links to head.
	add_theme_support( 'automatic-feed-links' );

	/*
     * Enable support for Post Formats.
     * See http://codex.wordpress.org/Post_Formats
     */
	add_theme_support( 'post-formats', array( 'aside', 'chat', 'image', 'gallery', 'link', 'video', 'quote', 'audio', 'status' ) );

	/*
     * Enable support for Post Thumbnails on posts and pages.
     *
     * @link http://codex.wordpress.org/Function_Reference/add_theme_support#Post_Thumbnails
     */
	add_theme_support( 'post-thumbnails' );

	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'cultural' ),
        'mobile' => __( 'Mobile Menu', 'cultural' )
	) );

    /*
     * Switch default core markup for search form, comment form, and comments
     * to output valid HTML5.
     */
    add_theme_support( 'html5', array( 'comment-list', 'comment-form', 'search-form', 'gallery', 'caption' ) );


	// filtra os padroes dos uploads
	update_option( 'image_default_align', 'center' );
}
add_action( 'after_setup_theme', 'cultural_setup' );
endif;

/**
 * Enqueue scripts and styles.
 */
function cultural_scripts() {

    wp_enqueue_style( 'cultural-style', get_stylesheet_uri() );

    /* Lovely Brick Fonts for body and titles */
    wp_enqueue_style( 'brick-fonts', '//brick.a.ssl.fastly.net/Open+Sans:400,700,400i,700i/Aleo:400,700,400i,700i' );

    /* JUDO Font Awesome for the icons */
    wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css' );

    wp_enqueue_script( 'respond', get_template_directory_uri() . '/js/min/respond-min.js', '', '1.4.0' );

    wp_enqueue_script( 'imagesloaded', get_template_directory_uri() . '/js/min/imagesloaded-min.js', '', '3.1.8', true );

    wp_enqueue_script( 'masonry', get_template_directory_uri() . '/js/min/masonry-min.js', '', '3.1.5', true );

    wp_enqueue_script( 'responsive-nav', get_template_directory_uri() . '/js/min/responsive-nav-min.js', array( 'jquery' ), '1.0.32', true );

    /* Modernizr */
    wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/modernizr.js', '', '2.6.2' );

    /* Load the comment reply JavaScript. */
    if ( is_singular() && get_option( 'thread_comments' ) && comments_open() )
        wp_enqueue_script( 'comment-reply' );

}
add_action( 'wp_enqueue_scripts', 'cultural_scripts' );

/**
 * Register widgets areas
 *
 * @link http://codex.wordpress.org/Function_Reference/register_sidebar
 */
function cultural_widgets_init() {
  	register_sidebar( array (
  		'name' => __( 'Primary Widget Area', 'cultural' ),
  		'description' => '',
  		'id' => 'primary-widget-area',
  		'before_widget' => '<aside id="%1$s" class="widget  %2$s">',
  		'after_widget' => "</aside>",
  		'before_title' => '<h3 class="widget__title">',
  		'after_title' => '</h3>',
  	) );
}
add_action( 'widgets_init', 'cultural_widgets_init' );

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

?>
