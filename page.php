<?php
get_header();
the_post();
?>

<div class="content  content--sidebarless">

    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="hentry-wrap">
            <h1 class="entry-title"><?php the_title(); ?></h1>
            <div class="entry-content">
                <?php the_content(); ?>
                <?php wp_link_pages('before=<div class="page-link">' . __('Páginas:', 'cultural') . '&after=</div>') ?>
            </div><!-- /entry-content -->
        </div>
    </article><!-- /page-<?php the_ID(); ?> -->

    <?php comments_template('', true); ?>
</div><!-- /content -->

<?php get_footer(); ?>