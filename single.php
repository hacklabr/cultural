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
        $entity_url = get_post_meta(get_the_ID(), 'mc-entity-relation', true);
        if($entity_url && MapasCulturais2Post::parseEventUrl($entity_url)):
            global $__event_url, $__image;
            $__image = 'avatar.avatarBig';
            $__event_url = $entity_url;
            ?>
            <?php get_template_part('partials/event-box'); ?>
        <?php endif; ?>

        <?php
        // If comments are open or we have at least one comment, load up the comment template
        if (comments_open() || '0' != get_comments_number())
            comments_template('', true);
        ?>

    <?php endwhile; // end of the loop. ?>

</div><!-- #content .site-content --><!-- #primary .content-area -->

<?php get_sidebar('content'); ?>
<?php 
get_footer(); 
