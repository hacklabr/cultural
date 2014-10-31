<?php $featured_posts = new WP_Query( array( 'ignore_sticky_posts' => 1, 'posts_per_page' => '4' ) );
if ( $featured_posts->have_posts() ) : ?>
    <div class="featured-posts">
        <?php while($featured_posts->have_posts()) : $featured_posts->the_post(); ?>
            <article class="feature">
                <?php if ( has_post_thumbnail() ) the_post_thumbnail( 'medium' ); ?>
                <div class="feature-content">
                    <h2 class="feature__title"><?php the_title(); ?></h2>
                    <div class="job__description">
                        <p><span>A collection of essays on Seinfeld episodes</span></p>
                        <p><span>Jekyll</span></p>
                    </div>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </div>
            </article>
        <?php endwhile; ?>
    </div><!-- /featured-posts -->
<?php endif; ?>
<?php wp_reset_postdata(); ?>
