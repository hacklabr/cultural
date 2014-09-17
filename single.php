<?php
/**
 * The Template for displaying all single posts.
 *
 * @package historiasqueficam
 *
 */

get_header(); ?>

		<div class="content">

		<?php while ( have_posts() ) : the_post(); ?>

			<?php get_template_part( 'content', 'single' ); ?>

			<?php
				// If comments are open or we have at least one comment, load up the comment template
				if ( comments_open() || '0' != get_comments_number() )
					comments_template( '', true );
			?>

		<?php endwhile; // end of the loop. ?>

		<?php cultural_content_nav( 'nav-below' ); ?>

		</div><!-- #content .site-content --><!-- #primary .content-area -->

<?php get_footer(); ?>
