<?php
/**
 * Custom functions that act independently of the theme templates
 *
 * Eventually, some of the functionality here could be replaced by core features
 *
 * @package cultural
 */

/**
 * Filters wp_title to print a neat <title> tag based on what is being viewed.
 *
 * @param string $title Default title text for current view.
 * @param string $sep Optional separator.
 * @return string The filtered title.
 */
function cultural_wp_title($title, $sep) {
    if (is_feed()) {
        return $title;
    }

    global $page, $paged;

    // Add the blog name
    $title .= get_bloginfo('name', 'display');

    // Add the blog description for the home/front page.
    $site_description = get_bloginfo('description', 'display');
    if ($site_description && ( is_home() || is_front_page() )) {
        $title .= " $sep $site_description";
    }

    // Add a page number if necessary:
    if (( $paged >= 2 || $page >= 2 ) && !is_404()) {
        $title .= " $sep " . sprintf(__('Page %s', '_s'), max($paged, $page));
    }

    return $title;
}

add_filter('wp_title', 'cultural_wp_title', 10, 2);

/**
 * A default menu for when they don't have one set.
 * Everybody sees a page list + the admins see a message about setting up the
 * menus in admin
 */
function default_menu() {
    ?>

    <ul id="menu-main" class="menu--main  menu  cf">
        <?php if (is_user_logged_in() && current_user_can('level_10')) : ?>
            <li><a href="<?php bloginfo('url'); ?>/wp-admin/nav-menus.php"><?php _e('Admin, não se esqueça de configurar um menu', 'cultural'); ?></a></li>
        <?php endif; ?>
        <?php wp_list_pages('title_li='); ?>
        <?php wp_list_categories('title_li='); ?>
    </ul>

    <?php
}

/**
 * Add an option to gallery images with no links
 */
function new_gallery_shortcode($attr) {
    global $post, $wp_locale;

    $output = gallery_shortcode($attr);

    if ($attr['link'] == "none") {
        $output = preg_replace(array('/<a[^>]*>/', '/<\/a>/'), '', $output);
    }

    return $output;
}

add_shortcode('gallery', 'new_gallery_shortcode');

/**
 * Get Favicons from Google (for Pingbacks!)
 */
function cultural_get_favicon($url = '') {
    if (!empty($url))
        $url = parse_url($url);

    $url = 'http://www.google.com/s2/favicons?domain=' . $url['host'];

    return $url;
}

/**
 * Sets the authordata global when viewing an author archive.
 *
 * This provides backwards compatibility with
 * http://core.trac.wordpress.org/changeset/25574
 *
 * It removes the need to call the_post() and rewind_posts() in an author
 * template to print information about the author.
 *
 * @global WP_Query $wp_query WordPress Query object.
 * @return void
 */
function cultural_setup_author() {
    global $wp_query;

    if ($wp_query->is_author() && isset($wp_query->post)) {
        $GLOBALS['authordata'] = get_userdata($wp_query->post->post_author);
    }
}

add_action('wp', 'cultural_setup_author');

/**
 * Add a lock icon before the protected posts title
 */
function cultural_private_titles($format) {
    return '%s <i class="fa  fa-lock"></i>';
}

add_filter('private_title_format', 'cultural_private_titles');
add_filter('protected_title_format', 'cultural_private_titles');
