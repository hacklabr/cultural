<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

	<header class="entry-header">
		<?php cultural_the_time(); ?>
		<?php cultural_the_format(); ?>
		<?php edit_post_link( sprintf( __( '%s Edit', 'cultural' ), '<i class="fa fa-pencil"></i>' ) ); ?>
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( __('Read, comment and share &ldquo;%s&rdquo;', 'cultural'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
	</header><!-- /entry-header -->

	<?php if( is_search() ) : ?>

		<div class="entry-summary">
			<?php the_excerpt(); ?>
		</div><!-- /entry-summary -->

	<?php else : ?>

		<div class="entry-content cf">
			<?php the_content( __( 'To be continued&hellip;', 'cultural' ) ); ?>
			<?php wp_link_pages( 'before=<div class="page-link">' . __( 'Pages:', 'cultural' ) . '&after=</div>' ) ?>
		</div><!-- /entry-content -->

	<?php endif; ?>

	<footer class="entry-footer">
		<?php if ( 'post' == get_post_type() ) : // Hide category and tag text for pages on Search ?>
			<?php
				/* translators: used between list items, there is a space after the comma */
				$categories_list = get_the_category_list( __( ', ', 'cultural' ) );
				if ( $categories_list && cultural_categorized_blog() ) :
			?>
			<span class="cat-links">
				<?php printf( __( 'Posted in %1$s', 'cultural' ), $categories_list ); ?>
			</span>
			<?php endif; // End if categories ?>

			<?php if( get_the_tag_list() )
				echo get_the_tag_list('<span class="entry-tags">',' ','</span><!-- /entry-tags -->');
			?>
		<?php endif; // End if 'post' == get_post_type() ?>

		<?php cultural_share(); ?>
	</footer><!-- /entry-footer -->

</article><!-- /post-<?php the_ID(); ?> -->
