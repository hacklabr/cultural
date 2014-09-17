<?php if ( is_active_sidebar( 'primary-widget-area' ) ) : ?>

	<div class="asides  widget-area">
		<?php if ( is_active_sidebar( 'primary-widget-area' ) )
			dynamic_sidebar( 'primary-widget-area' );
		?>
	</div><!-- /asides -->

<?php endif; ?>
