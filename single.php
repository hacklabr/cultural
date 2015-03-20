<?php
/**
 * The Template for displaying all single posts.
 *
 * @package cultural
 *
 */
get_header();
?>

<div class="content  content--sidebar">

    <?php while (have_posts()) : the_post(); ?>

        <?php get_template_part('content', 'single'); ?>

        <?php
        // If comments are open or we have at least one comment, load up the comment template
        if (comments_open() || '0' != get_comments_number())
            comments_template('', true);
        ?>

    <?php endwhile; // end of the loop. ?>

    <?php cultural_post_nav(); ?>

</div><!-- #content .site-content --><!-- #primary .content-area -->

<?php get_sidebar('content'); ?>
<?php get_footer(); ?>