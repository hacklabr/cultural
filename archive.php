<?php get_header(); ?>

	<div class="content">
    <?php if ( have_posts() ) : ?>
        <header class="page-header"
        <?php if ( is_category() ) :
            $category = get_category(get_query_var('cat'));
            $cat_data = get_option('category_' . $category->cat_ID );
            $cat_color = $cat_data['color'];
            echo 'style="background-color:' . $cat_color .'"';
        endif; ?>>
            <h1 class="page-title">
                <i class="fa fa-archive"></i>
                <?php
                    if ( is_category() ) :
                        single_cat_title();

                    elseif ( is_tag() ) :
                        single_tag_title();

                    elseif ( is_author() ) :
                        printf( __( 'Author: %s', '_s' ), '<span class="vcard">' . get_the_author() . '</span>' );

                    elseif ( is_day() ) :
                        printf( __( 'Day: %s', '_s' ), '<span>' . get_the_date() . '</span>' );

                    elseif ( is_month() ) :
                        printf( __( 'Month: %s', '_s' ), '<span>' . get_the_date( _x( 'F Y', 'monthly archives date format', '_s' ) ) . '</span>' );

                    elseif ( is_year() ) :
                        printf( __( 'Year: %s', '_s' ), '<span>' . get_the_date( _x( 'Y', 'yearly archives date format', '_s' ) ) . '</span>' );

                    elseif ( is_tax( 'post_format', 'post-format-aside' ) ) :
                        _e( 'Asides', '_s' );

                    elseif ( is_tax( 'post_format', 'post-format-gallery' ) ) :
                        _e( 'Galleries', '_s' );

                    elseif ( is_tax( 'post_format', 'post-format-image' ) ) :
                        _e( 'Images', '_s' );

                    elseif ( is_tax( 'post_format', 'post-format-video' ) ) :
                        _e( 'Videos', '_s' );

                    elseif ( is_tax( 'post_format', 'post-format-quote' ) ) :
                        _e( 'Quotes', '_s' );

                    elseif ( is_tax( 'post_format', 'post-format-link' ) ) :
                        _e( 'Links', '_s' );

                    elseif ( is_tax( 'post_format', 'post-format-status' ) ) :
                        _e( 'Statuses', '_s' );

                    elseif ( is_tax( 'post_format', 'post-format-audio' ) ) :
                        _e( 'Audios', '_s' );

                    elseif ( is_tax( 'post_format', 'post-format-chat' ) ) :
                        _e( 'Chats', '_s' );

                    else :
                        _e( 'Archives', '_s' );

                    endif;
                ?>
            </h1>
        </header><!-- .page-header -->

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
