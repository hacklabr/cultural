<?php

/**
 * Adds a class selector for the widgets
 *
 */
function cultural_in_widget_form($t, $return, $instance) {
    $instance = wp_parse_args((array) $instance, array('title' => '', 'text' => '', 'style' => 'default'));
    if (!isset($instance['style']))
        $instance['style'] = null;
    ?>
    <p>
        <label for="<?php echo $t->get_field_id('style'); ?>"><?php _e('Estilo:', 'cultural'); ?></label>
        <select id="<?php echo $t->get_field_id('style'); ?>" name="<?php echo $t->get_field_name('style'); ?>">
            <option <?php selected($instance['style'], 'default'); ?> value="default"><?php _e('Default', 'cultural'); ?></option>
            <option <?php selected($instance['style'], 'taped'); ?>value="taped"><?php _e('Taped', 'cultural'); ?></option>
            <option <?php selected($instance['style'], 'folded'); ?> value="folded"><?php _e('Folded', 'cultural'); ?></option>
        </select>
    </p>
    <?php
    $retrun = null;
    return array($t, $return, $instance);
}

function cultural_in_widget_form_update($instance, $new_instance, $old_instance) {
    $instance['style'] = $new_instance['style'];
    return $instance;
}

function cultural_dynamic_sidebar_params($params) {
    global $wp_registered_widgets;
    $widget_id = $params[0]['widget_id'];
    $widget_obj = $wp_registered_widgets[$widget_id];
    $widget_opt = get_option($widget_obj['callback'][0]->option_name);
    $widget_num = $widget_obj['params'][0]['number'];
    if (isset($widget_opt[$widget_num]['style']))
        $style = $widget_opt[$widget_num]['style'];
    else
        $style = '';
    $params[0]['before_widget'] = preg_replace('/class="/', 'class="widget--' . $style . ' ', $params[0]['before_widget'], 1);
    return $params;
}

add_action('in_widget_form', 'cultural_in_widget_form', 5, 3);
add_filter('widget_update_callback', 'cultural_in_widget_form_update', 5, 3);
add_filter('dynamic_sidebar_params', 'cultural_dynamic_sidebar_params');
