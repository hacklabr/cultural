<?php get_header(); ?>
<div class="content">
    <?php if (have_posts()) : ?>
        <header class="page-header"
        <?php
        if (is_category()) :
            $category = get_category(get_query_var('cat'));
            $cat_data = get_option('category_' . $category->cat_ID);
            $cat_color = $cat_data['color'];
            echo 'style="background-color:' . $cat_color . '"';
        endif;
        ?>>
            <h1 class="page-title">
                <i class="fa fa-archive"></i>
                <?php
                if (is_category()) :
                    single_cat_title();

                elseif (is_tag()) :
                    single_tag_title();

                elseif (is_author()) :
                    printf(__('Author: %s', 'cultural'), '<span class="vcard">' . get_the_author() . '</span>');

                elseif (is_day()) :
                    printf(__('Day: %s', 'cultural'), '<span>' . get_the_date() . '</span>');

                elseif (is_month()) :
                    printf(__('Month: %s', 'cultural'), '<span>' . get_the_date(_x('F Y', 'monthly archives date format', 'cultural')) . '</span>');

                elseif (is_year()) :
                    printf(__('Year: %s', 'cultural'), '<span>' . get_the_date(_x('Y', 'yearly archives date format', 'cultural')) . '</span>');

                elseif (is_tax('post_format', 'post-format-aside')) :
                    _e('Asides', 'cultural');

                elseif (is_tax('post_format', 'post-format-gallery')) :
                    _e('Galleries', 'cultural');

                elseif (is_tax('post_format', 'post-format-image')) :
                    _e('Images', 'cultural');

                elseif (is_tax('post_format', 'post-format-video')) :
                    _e('Videos', 'cultural');

                elseif (is_tax('post_format', 'post-format-quote')) :
                    _e('Quotes', 'cultural');

                elseif (is_tax('post_format', 'post-format-link')) :
                    _e('Links', 'cultural');

                elseif (is_tax('post_format', 'post-format-status')) :
                    _e('Statuses', 'cultural');

                elseif (is_tax('post_format', 'post-format-audio')) :
                    _e('Audios', 'cultural');

                elseif (is_tax('post_format', 'post-format-chat')) :
                    _e('Chats', 'cultural');

                else :
                    _e('Archives', 'cultural');

                endif;
                ?>
            </h1>
        </header><!-- .page-header -->

        <?php get_template_part('partials/slider'); ?>

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