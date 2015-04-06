    <footer class="site-footer">
        <h1 class="site-title"><a href="<?php echo esc_url(home_url('/')); ?>" title="<?php echo esc_attr(get_bloginfo('description')); ?>" rel="home"><?php bloginfo('name'); ?></a></h1>
        <h2 class="site-description"><?php bloginfo('description'); ?></h2>
        <style>
            .regua-marcas li span { color:white; }
            .regua-marcas li a { margin:3px; }
        </style>
        <?php wp_nav_menu(array(
            'theme_location' => 'regua',
            'walker' => new Regua_Menu_Walker,
            'items_wrap' => '<ul id="%1$s" class="regua-marcas %2$s">%3$s</ul>'
        )); ?>
    </footer><!-- /site-footer -->
    </div><!-- /main -->
    <?php wp_footer(); ?>
</body>
</html>
