<article <?php post_class(); ?>>
    <?php if ('' != get_the_post_thumbnail()) { ?>
        <figure class="entry__image">
            <?php the_post_thumbnail('large'); ?>
            <?php cultural_thumb_caption() ?>
        </figure>
    <?php } ?>
    <header class="entry-header">
        <?php cultural_categories(); ?>
        <?php cultural_the_format(); ?>
        <?php the_title(sprintf('<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h1>'); ?>
        <?php cultural_the_time(); ?>
        <?php cultural_the_post_author(); ?>
    </header><!-- /entry-header -->

    <?php if (!is_singular()) : ?>

        <div class="entry-summary">
            <?php the_excerpt(); ?>
        </div><!-- /entry-summary -->

    <?php else : ?>

        <div class="entry__content">
            <?php the_content(__('Continua&hellip;', 'cultural')); ?>
            <?php wp_link_pages('before=<div class="page-link">' . __('Páginas:', 'cultural') . '&after=</div>') ?>
        </div><!-- /entry-content -->

    <?php endif; ?>

    <?php cultural_entry_footer(); ?>
</article><!-- /post-<?php the_ID(); ?> -->
