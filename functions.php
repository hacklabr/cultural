<?php

session_start();

if ( ! isset( $content_width ) )
	$content_width = 1000;

add_action( 'after_setup_theme', 'cultural_setup' );
function cultural_setup() {


    require_once( get_template_directory() . '/inc/customizr.php' );


	// torna o tema traduzível
	load_theme_textdomain( 'cultural', get_template_directory() . '/languages' );


	// os arquivos de tradução ficam na pasta /languages/
	$locale = get_locale();
	$locale_file = get_template_directory() . "/languages/$locale.php";
	if ( is_readable( $locale_file ) )
		require_once( $locale_file );


	// adiciona estilo ao editor
	add_editor_style();


	// adiciona os links pros feeds padrão
	add_theme_support( 'automatic-feed-links' );

	/**
	 * Add excerpt to pages
	 */
	add_post_type_support( 'page', 'excerpt' );


	// adiciona o suporte aos formatos de post
	add_theme_support( 'post-formats', array( 'aside', 'chat', 'image', 'gallery', 'link', 'video', 'quote', 'audio', 'status' ) );


	// adiciona suporte a imagens destacadas
	add_theme_support( 'post-thumbnails' );


	// registra o menu
	register_nav_menus( array(
		'primary' => __( 'Primary Menu', 'cultural' ),
        'mobile' => __( 'Mobile Menu', 'cultural' )
	) );


	// chama os javascripts
	add_action( 'template_redirect', 'cultural_load_scripts' );


    add_action( 'wp_enqueue_scripts', 'cultural_enqueue_styles' );


	// registra as áreas de widgets
	add_action( 'widgets_init', 'cultural_register_sidebars' );


	// remove o estilo padrão das galerias
	add_filter( 'use_default_gallery_style', '__return_false' );


	// filtra os padroes dos uploads
	update_option( 'image_default_align','center' );
}

/**
 * Enqueue custom JS using wp_enqueue_script()
 *
 */
function cultural_load_scripts() {

	/**
	 * Respond.js
	 * Media queries polyfill
	 */
	wp_enqueue_script( 'respond', get_template_directory_uri() . '/js/respond.js', '', '1.1.0' );

    wp_enqueue_script( 'responsive-nav', get_template_directory_uri() . '/js/responsive-nav.min.js', array( 'jquery' ), '1.0.32', true );

	/* Modernizr */
	wp_enqueue_script( 'modernizr', get_template_directory_uri() . '/js/modernizr.js', '', '2.6.2' );

	/* Load the comment reply JavaScript. */
	if ( is_singular() && get_option( 'thread_comments' ) && comments_open() )
		wp_enqueue_script( 'comment-reply' );
}


function cultural_enqueue_styles() {

    /* Lovely Google Fonts for body and titles */
    wp_enqueue_style( 'google-fonts', 'http://fonts.googleapis.com/css?family=Sanchez:400italic,400' );

    /* JUDO Font Awesome for the icons */
    wp_enqueue_style( 'font-awesome', '//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css' );

}


function cultural_wp_title( $title, $sep ) {
	global $paged, $page;

	if ( is_feed() )
		return $title;

	// Add the site name.
	$title .= get_bloginfo( 'name' );

	// Add the site description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		$title = "$title $sep $site_description";

	// Add a page number if necessary.
	if ( $paged >= 2 || $page >= 2 )
		$title = "$title $sep " . sprintf( __( 'Page %s', 'cultural' ), max( $paged, $page ) );

	return $title;
}
add_filter( 'wp_title', 'cultural_wp_title', 10, 2 );


function cultural_share() {
	global $post; ?>
		<div class="entry-share  cf">
            <input type="text" class="share-shortlink" value="<?php echo wp_get_shortlink( get_the_ID() ); ?>" onclick="this.focus(); this.select();" readonly="readonly" />

            <?php if ( ! post_password_required() && ( comments_open() || '0' != get_comments_number() ) ) : ?>
                <a href="<?php comments_link(); ?>" class="comments-link"><i class="fa fa-comment"></i> <?php echo comments_number( __('Leave a reply','cultural'), __('One comment','cultural'), __('% comments','cultural') ); ?></a>
            <?php endif; ?>
    	</div>
   	<?php
}


function cultural_postedby() {
	global $post;
?>
	<span class="author vcard">
        <?php if ( is_multi_author() ) {
        	the_author_posts_link();
        } else {
        	the_author();
        } ?>
    </span>
   	<?php
}


// filtra a data para que o human_time_diff apenas apareça em posts com menos de um mês
function cultural_the_time() {
	global $post;

	$time = mysql2date( 'G', $post->post_date );
	$time_diff = time() - $time; ?>

	<span class="entry-date">
		<?php if ( ! is_single() && ( $time_diff > 0 && $time_diff < 30*24*60*60 ) )
			printf( __( '%s ago', 'cultural' ), human_time_diff( get_the_time( 'U' ), current_time( 'timestamp' ) ) );
		else
			the_time( get_option( 'date_format' ) );
		?>
	</span>
<?php }


function cultural_content_nav( $nav_id ) {
	global $wp_query;

	$nav_class = 'paging-navigation';
	if ( is_single() )
		$nav_class = 'post-navigation';

	?>
	<nav role="navigation" id="<?php echo $nav_id; ?>" class="<?php echo $nav_class; ?>">
		<h1 class="assistive-text"><?php _e( 'Post navigation', 'cultural' ); ?></h1>

	<?php if ( is_single() ) : ?>

		<?php previous_post_link( '<div class="nav-previous">%link</div>', '<i class="fa fa-arrow-left"></i> %title' ); ?>
		<?php next_post_link( '<div class="nav-next">%link</div>', '<i class="fa fa-arrow-right"></i> %title' ); ?>

	<?php elseif ( $wp_query->max_num_pages > 1 && ( is_home() || is_archive() || is_search() ) ) : ?>

		<?php if ( get_next_posts_link() ) : ?>
			<div class="nav-previous"><?php next_posts_link( sprintf( __('%s Older posts', 'cultural' ), '<i class="fa fa-arrow-left"></i>' ) ); ?></div>
		<?php endif; ?>

		<?php if ( get_previous_posts_link() ) : ?>
			<div class="nav-next"><?php previous_posts_link( sprintf( __('%s Newer posts', 'cultural' ), '<i class="fa fa-arrow-right"></i>' ) ); ?></div>
		<?php endif; ?>

	<?php endif; ?>

	</nav><!-- /<?php echo $nav_id; ?> -->
	<?php
}


// ativa as áreas de widgets
function cultural_register_sidebars() {
	// lateral 1
  	register_sidebar( array (
  		'name' => __( 'Primary Widget Area','cultural' ),
  		'description' => __( 'Its the coloured one.','cultural' ),
  		'id' => 'primary-widget-area',
  		'before_widget' => '<aside id="%1$s" class="widget widget--negative %2$s">',
  		'after_widget' => "</aside>",
  		'before_title' => '<h3 class="widget__title">',
  		'after_title' => '</h3>',
  	) );

}



/**
 * Adiciona os favicons aos pingbacks & trackbacks
 *
 */
function cultural_get_favicon( $url = '' ) {
	if ( ! empty ( $url ) )
		$url = parse_url( $url );

	$url = 'http://www.google.com/s2/favicons?domain=' . $url['host'];

	return $url;
}


function cultural_comment( $comment, $args, $depth ) {
    $GLOBALS['comment'] = $comment;
    switch ( $comment->comment_type ) :
        case 'pingback' :
        case 'trackback' :
    ?>
    <li class="pingback">
       	<?php if(function_exists('cultural_get_favicon')) { ?><img src="<?php echo cultural_get_favicon( $comment->comment_author_url ); ?>" alt="Favicon" class="favicon" /><?php } ?><?php comment_author_link(); ?><?php edit_comment_link( sprintf( __( '%s Edit', 'cultural' ), '<i class="fa fa-pencil"></i>' ) ); ?>
    <?php
            break;
        default :
    ?>
    <li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">
        <article id="comment-<?php comment_ID(); ?>" class="comment-container">
            <div class="comment-content">
            	<?php comment_text(); ?>
            </div><!-- /comment-content -->

            <footer class="comment-meta vcard">
            	<div class="comment-author-avatar">
            		<?php echo get_avatar( $comment, 96 ); ?>
            	</div>
            	<cite class="fn">
	            	<?php echo get_comment_author_link(); ?>
            	</cite>
            	<?php comment_reply_link( array_merge( $args, array( 'reply_text' => '<i class="fa fa-reply"></i>', 'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>
            	<a href="<?php echo esc_url( get_comment_link( $comment->comment_ID ) ); ?>" title="<?php printf( __( '%1$s at %2$s', 'cultural' ), get_comment_date(), get_comment_time() ); ?>" class="comment-permalink"><i class="fa fa-check"></i><span class="assistive-text"><? _e('Permalink', 'cultural'); ?></span></a>
            	<?php edit_comment_link( sprintf( __( '%s Edit', 'cultural' ), '<i class="fa fa-pencil"></i>' ) ); ?>
            </footer>

            <?php if ( $comment->comment_approved == '0' ) : ?>
            	<em class="comment-on-hold"><?php _e( 'Your comment is awaiting moderation.', 'cultural' ); ?></em>
            <?php endif; ?>
        </article><!-- /comment -->

    <?php
            break;
    endswitch;
}


add_filter('img_caption_shortcode', 'cultural_img_caption_shortcode',10,3);
function cultural_img_caption_shortcode($val, $attr, $content = null) {
	extract(shortcode_atts(array(
		'id'	=> '',
		'align'	=> '',
		'width'	=> '',
		'caption' => ''
	), $attr));

	if ( 1 > (int) $width || empty($caption) )
		return $val;

	$capid = '';
	if ( $id ) {
		$id = esc_attr($id);
		$capid = 'id="figcaption_'. $id . '" ';
		$id = 'id="' . $id . '" aria-labelledby="figcaption_' . $id . '" ';
	}

	return '<figure ' . $id . 'class="wp-caption ' . esc_attr($align) . '">' . do_shortcode( $content ) . '<figcaption ' . $capid
	. 'class="wp-caption-text">' . $caption . '</figcaption></figure>';
}


/**
 * Returns true if a blog has more than 1 category
 *
 * @since Jecebel 3.0
 */
function cultural_categorized_blog() {
	if ( false === ( $all_the_cool_cats = get_transient( 'all_the_cool_cats' ) ) ) {
		// Create an array of all the categories that are attached to posts
		$all_the_cool_cats = get_categories( array(
			'hide_empty' => 1,
		) );

		// Count the number of categories that are attached to the posts
		$all_the_cool_cats = count( $all_the_cool_cats );

		set_transient( 'all_the_cool_cats', $all_the_cool_cats );
	}

	if ( '1' != $all_the_cool_cats ) {
		// This blog has more than 1 category so cultural_categorized_blog should return true
		return true;
	} else {
		// This blog has only 1 category so cultural_categorized_blog should return false
		return false;
	}
}


/**
 * Flush out the transients used in cultural_categorized_blog
 *
 * @since Jecebel 3.0
 */
function cultural_category_transient_flusher() {
	// Like, beat it. Dig?
	delete_transient( 'all_the_cool_cats' );
}
add_action( 'edit_category', 'cultural_category_transient_flusher' );
add_action( 'save_post', 'cultural_category_transient_flusher' );


/**
 * Verifica se o usuário preencheu sua descrição e só então a mostra
 *
 */
function cultural_user_description( $description ) {

	if ( !empty( $description ) ) {

		$output = '<div class="member-description">';
		$output .= $description;
		$output .= '</div>';

		echo $output;

	}
}

/**
 * Return the post format (if not Standard)
 *
 */
function cultural_the_format() {

	global $post;

	$format = get_post_format();
	$pretty_format = get_post_format_string($format);
	$permalink = get_permalink();

	if( $format ) echo '<a href="' . $permalink . '" class="entry-format">' . $pretty_format . '</a>';

}


/**
 * Add an option to gallery images with no links
 */
add_shortcode( 'gallery', 'new_gallery_shortcode' );
function new_gallery_shortcode($attr) {
	global $post, $wp_locale;

	$output = gallery_shortcode($attr);

	if($attr['link'] == "none") {
		$output = preg_replace(array('/<a[^>]*>/', '/<\/a>/'), '', $output);
	}

	return $output;
}

/**
 * A default menu for when they don't have one set.
 * Everybody sees a page list + the admins see a message about setting up the
 * menus in admin
 *
 */
function default_menu() { ?>

    <ul id="menu-main" class="menu--main  menu  cf">
        <?php if ( is_user_logged_in() && current_user_can( 'level_10' ) ) : ?>
            <li><a href="<?php bloginfo('url'); ?>/wp-admin/nav-menus.php"><?php _e('Hey admin, don\'t forget to set up a menu!', 'cultural' ); ?></a></li>
        <?php endif; ?>
        <?php wp_list_pages('title_li='); ?>
        <?php wp_list_categories('title_li='); ?>
    </ul>

<?php }



?>
