<?php
/**
 * Template Name: Events
 * For listing events
 *
 * @package cultural
 */
?>

<?php get_header(); the_post(); ?>

    <div class="content  content--full">
        <div class="filter-bar">
            Filtros
        </div>
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="hentry-wrap">
                <div class="entry-content">
                    <?php the_content(); ?>
                    <?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'cultural' ) . '&after=</div>') ?>
                </div>
            </div>
        </article>

        <div class="events-list  grid  js-masonry" data-masonry-options='{ "columnWidth": ".grid-sizer", "gutter": ".gutter-sizer", "itemSelector": ".event" }'>
            <div class="grid-sizer"></div>
            <div class="gutter-sizer"></div>

            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
        </div>

        <?php comments_template('', true); ?>
    </div>

<?php get_footer(); ?>
