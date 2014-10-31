<?php
/**
 * A featured content slider
 *
 * @package cultural
 */
?>

<?php $featured_posts = new WP_Query( array( 'ignore_sticky_posts' => 1, 'posts_per_page' => '4' ) );
if ( $featured_posts->have_posts() ) : ?>
    <div class="swiper  js-swiper">
        <h3 class="slider-title"><i class="fa fa-bullhorn"></i> <?php _e( 'Featured', 'cultural' ); ?></h3>
        <div class="swiper-wrapper">
            <?php while($featured_posts->have_posts()) : $featured_posts->the_post(); ?>
                <article class="swiper-slide">
                    <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'large' ); ?>
                    <div class="slide-content">
                        <?php the_title( sprintf( '<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url( get_permalink() ) ), '</a></h1>' ); ?>
                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="read-more"><i class="fa fa-align-left"></i> <?php _e( 'Read more', 'cultural' ); ?></a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        <div class="swiper__pagination"></div>
    </div>
<?php endif; ?>
<?php wp_reset_postdata(); ?>
