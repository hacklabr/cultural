<?php
/**
 * A featured content slider
 *
 * @package cultural
 */
?>

<?php
$args = array('ignore_sticky_posts' => 1, 'posts_per_page' => '4');

$featured_posts = Cultural_Hightlights::getHighlightedQuery();

if ($featured_posts->have_posts()) :
    ?>
    <div class="swiper js-swiper">
        <h3 class="slider-title"><i class="fa fa-bullhorn"></i> <?php _e('Destaque', 'cultural'); ?></h3>
        <div class="swiper-wrapper">
            <?php while ($featured_posts->have_posts()) : $featured_posts->the_post(); ?>
                <article class="swiper-slide">
                    <?php if (has_post_thumbnail()) {
                        $post_thumbnail_id = get_post_thumbnail_id();
                        $post_thumbnail_url = wp_get_attachment_url( $post_thumbnail_id );
                        ?>
                        <img class="attachment-post-thumbnail size-post-thumbnail wp-post-image size-full" src="<?php echo $post_thumbnail_url; ?>">
                    <?php } ?>
                    <div class="slide-content">
                        <?php the_title(sprintf('<h1 class="entry-title"><a href="%s" rel="bookmark">', esc_url(get_permalink())), '</a></h1>'); ?>
                        <div class="entry-summary">
                            <?php the_excerpt(); ?>
                        </div>
                        <a href="<?php the_permalink(); ?>" class="read-more"><i class="fa fa-align-left"></i> <?php _e('Mais informações', 'cultural'); ?></a>
                    </div>
                </article>
            <?php endwhile; ?>
        </div>
        <?php if($featured_posts->found_posts > 1): ?>
            <div class="swiper__pagination"></div>
        <?php else: ?>
            <div class="swiper__pagination" style="display:none"></div>
        <?php endif; ?>
    </div>
<?php endif; ?>
<?php wp_reset_postdata(); ?>