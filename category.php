<?php get_header(); ?>

	<div class="content">
	    <?php if ( have_posts() ) : ?>

<?php
    $cat_id = get_cat_ID('Uncategorized');
    $cat_data = get_option("category_$cat_id");
    echo $cat_data['color'];
?>

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


  <script>
        $(function(){
            var container = document.querySelector( '.js-masonry' );
            var msnry;
            // initialize Masonry after all images have loaded
            imagesLoaded( container, function() {
                msnry = new Masonry( container );
            });
        });
    </script>

<?php get_footer(); ?>
