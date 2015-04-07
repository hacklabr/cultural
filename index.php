<?php get_header(); ?>

<div class="content">
    <?php get_template_part('partials/slider'); ?>

    <?php if (have_posts()) : ?>

        <div class="grid  js-masonry" data-masonry-options='{ "columnWidth": ".grid-sizer", "gutter": ".gutter-sizer", "itemSelector": ".hentry", "stamp": ".sticky" }'>
            <div class="grid-sizer"></div>
            <div class="gutter-sizer"></div>

            <?php get_template_part('partials/loop') ?>

        </div><!-- /grid -->

        <?php cultural_paging_nav(); ?>

    <?php else : ?>

        <?php get_template_part('content', 'none'); ?>

    <?php endif; ?>
</div><!-- /content -->

<?php get_footer(); ?>