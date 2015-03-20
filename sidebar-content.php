<?php if (is_active_sidebar('content-widget-area')) : ?>
    <div class="asides  asides-content  widget-area">
        <?php
        if (is_active_sidebar('content-widget-area')) {
            dynamic_sidebar('content-widget-area');
        }
        ?>
    </div><!-- /asides -->
<?php endif; ?>