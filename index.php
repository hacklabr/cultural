<?php get_header(); ?>

	<div class="content">
        <?php get_template_part( 'partials/slider' ); ?>

	    <?php if ( have_posts() ) : ?>

            <div class="grid  js-masonry" data-masonry-options='{ "columnWidth": ".grid-sizer", "gutter": ".gutter-sizer", "itemSelector": ".hentry", "stamp": ".sticky" }'>
                <div class="grid-sizer"></div>
                <div class="gutter-sizer"></div>

            <?php /* Start the Loop */ ?>
            <?php while ( have_posts() ) : the_post(); ?>

                <?php
                    /* Include the Post-Format-specific template for the content.
                     * If you want to override this in a child theme, then include a file
                     * called content-___.php (where ___ is the Post Format name) and that will be used instead.
                     */
                    get_template_part( 'content', 'grid' );
                ?>

            <?php endwhile; ?>

            </div><!-- /grid -->

            <?php cultural_paging_nav(); ?>

        <?php else : ?>

            <?php get_template_part( 'content', 'none' ); ?>

        <?php endif; ?>
	</div><!-- /content -->

<?php get_footer(); ?>
