<?php

/** Add Colorpicker Field to "Add New Category" Form * */
function category_form_custom_field_add($taxonomy) {
    ?>
    <div class="form-field">
        <label for="category_custom_color"><?php _e('Cor destacada', 'cultural'); ?></label>
        <input name="cat_meta[color]" class="colorpicker" type="text" value="" />
        <p class="description"><?php _e('Selecione uma cor destacada', 'cultural'); ?></p>
    </div>
    <?php
}

add_action('category_add_form_fields', 'category_form_custom_field_add', 10);

/** Add New Field To Category * */
function extra_category_fields($tag) {
    $t_id = $tag->term_id;
    $cat_meta = get_option("category_$t_id");
    ?>
    <tr class="form-field">
        <th scope="row" valign="top"><label for="meta-color"><?php _e('Cor destacada', 'cultural'); ?></label></th>
        <td>
            <input type="text" name="cat_meta[color]" class="colorpicker" value="<?php echo (isset($cat_meta['color'])) ? $cat_meta['color'] : '#fff'; ?>" />
            <p class="description"><?php _e('Selecione uma cor destacada', 'cultural'); ?></p>
        </td>
    </tr>
    <?php
}

add_action('category_edit_form_fields', 'extra_category_fields');

/** Save Category Meta * */
function save_extra_category_fields($term_id) {

    if (isset($_POST['cat_meta'])) {
        $t_id = $term_id;
        $cat_meta = get_option("category_$t_id");
        $cat_keys = array_keys($_POST['cat_meta']);
        foreach ($cat_keys as $key) {
            if (isset($_POST['cat_meta'][$key])) {
                $cat_meta[$key] = $_POST['cat_meta'][$key];
            }
        }
        //save the option array
        update_option("category_$t_id", $cat_meta);
    }
}

add_action('edited_category', 'save_extra_category_fields');
add_action('created_category', 'save_extra_category_fields', 11, 1);

/** Enqueue Color Picker * */
function colorpicker_enqueue() {
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('colorpicker-js', get_stylesheet_directory_uri() . '/js/colorpicker.js', array('wp-color-picker'));
}

add_action('admin_enqueue_scripts', 'colorpicker_enqueue');
