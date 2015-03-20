<!doctype html>
<!--[if IE 7]>
<html class="ie ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html class="ie ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
    <!--<![endif]-->
    <head>
        <meta charset="<?php bloginfo('charset'); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <title><?php wp_title('&mdash;', true, 'right'); ?></title>
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <meta name="viewport" content="width=device-width">

        <link rel="pingback" href="<?php bloginfo('pingback_url'); ?>">

        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>

        <?php do_action('before'); ?>

        <header class="site-header wrap">
            <a href="#main" title="<?php esc_attr_e('Skip to content', 'cultural'); ?>" class="assistive-text"><?php _e('Skip to content', 'cultural'); ?></a>

            <ul class="toggle-bar">
                <li><a href="#tabs-1" class="current main-toggle" data-tab="tab-1"><i class="fa fa-list-ul"></i></a></li>
                <?php if (is_active_sidebar('header-widget-area')) : ?>
                    <li><a href="#tab-2" class="highlights-toggle" data-tab="tab-2"><i class="fa fa-search"></i></a></li>
                <?php endif; ?>
                <li><a href="#tab-3" class="calendar-toggle" data-tab="tab-3"><i class="fa fa-calendar"></i></a></li>
            </ul>

            <div id="tabs" class="toggle-tabs">
                <div class="site-header-inside">
                    <!-- Logo, description and main navigation -->
                    <div id="tab-1" class="tab-content current animated fadeIn">
                        <div class="branding">
                            <a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('name', 'display')); ?>" rel="home">
                                <?php
                                $logo = get_theme_mod('site_logo');
                                if ($logo == ''):
                                    ?>
                                    <h1 class="site-title"><?php bloginfo('name'); ?></h1>
                                <?php else: ?>
                                    <img src="<?php echo esc_url($logo); ?>" alt="<?php bloginfo('name'); ?>" title="<?php bloginfo('name'); ?>" />
                                <?php endif; ?>
                            </a>
                        </div>
                        <nav class="access cf js-access" role="navigation">
                            <?php
                            if (wp_is_mobile()) :
                                wp_nav_menu(array('theme_location' => 'mobile', 'container' => false, 'menu_class' => 'menu--mobile  menu', 'fallback_cb' => false));
                            else :
                                wp_nav_menu(array('theme_location' => 'primary', 'container' => false, 'menu_class' => 'menu--main  menu', 'fallback_cb' => 'default_menu'));
                                wp_nav_menu(array('theme_location' => 'secondary', 'container' => false, 'menu_class' => 'menu--sub  menu', 'fallback_cb' => false));
                                ?>
                            <?php endif; ?>
                        </nav>
                    </div>

                    <?php if (is_active_sidebar('header-widget-area')) : ?>
                        <div id="tab-2" class="tab-content animated fadeIn">
                            <?php dynamic_sidebar('header-widget-area'); ?>
                        </div>
                    <?php endif; ?>

                    <div id="tab-3" class="tab-content animated fadeIn">
                        <?php get_template_part('inc/featured-posts'); ?>
                        <div class="tab__description">
                            <a href="#"><i class="fa fa-arrow-right"></i> Ver mais eventos</a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="main  cf">
