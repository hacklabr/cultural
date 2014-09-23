<?php
/**
 * The template part for displaying results in a masonry grid.
 *
 * @package cultural
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
	<header class="entry-header">
        <?php
            /* translators: used between list items, there is a space after the comma */
            $categories_list = get_the_category_list( __( ' ', 'cultural' ) );
            if ( $categories_list && cultural_categorized_blog() ) :
        ?>
        <span class="entry__categories">
            <?php echo $categories_list; ?>
        </span>
        <?php endif; // End if categories ?>
		<?php cultural_the_format(); ?>
		<?php edit_post_link( sprintf( __( '%s Edit', 'cultural' ), '<i class="fa fa-pencil"></i>' ) ); ?>
		<h1 class="entry-title"><a href="<?php the_permalink(); ?>" title="<?php printf( __('Read, comment and share &ldquo;%s&rdquo;', 'cultural'), the_title_attribute('echo=0') ); ?>" rel="bookmark"><?php the_title(); ?></a></h1>
	</header><!-- /entry-header -->

	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- /entry-summary -->

	<footer class="entry-footer">
        <?php cultural_the_time(); ?>
        <a href="<?php the_permalink(); ?>" title="<?php printf( __( 'Permalink to &ldquo;%s&rdquo;', 'cultural' ), the_title_attribute('echo=0') ); ?>" rel="bookmark" class="u-pull-right"><i class="fa fa-link"></i></a>
	</footer><!-- /entry-footer -->
</article><!-- /post-<?php the_ID(); ?> -->
