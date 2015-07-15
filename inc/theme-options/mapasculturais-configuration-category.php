<?php
require_once 'mapasculturais-configuration.php';


/** Add Colorpicker Field to "Add New Category" Form **/
function mapasculturais_category_edit( $term ) {
    $selectedFilters = get_option("category_{$term->term_id}");

    ?>
    <tr>
        <td>
            <input type='hidden' name="<?php echo MapasCulturaisConfiguration::OPTION_NAME ?>[use_events]" value="0">
            <label>
                <input id="category-use-events" type="checkbox" name="<?php echo MapasCulturaisConfiguration::OPTION_NAME ?>[use_events]" value="1" <?php if(isset($selectedFilters['use_events']) && $selectedFilters['use_events']) echo 'checked="checked"' ?>> Usar agenda de eventos
            </label>
        </td>
    </tr>
    <tr id="category-events-filter">
        <td valign="bottom" colspan="2">
            <h4>Esta categoria está associada aos seguintes filtros:</h4>
            <?php MapasCulturaisConfiguration::printForm($term->term_id, $selectedFilters); ?>
        </td>
    </tr>
<?php
}
add_action( 'category_edit_form_fields', 'mapasculturais_category_edit', 11 );


/** Add Colorpicker Field to "Add New Category" Form * */
function mapasculturais_category_add($taxonomy) {
    ?>
    <div class="form-field">
        <input type='hidden' name="<?php echo MapasCulturaisConfiguration::OPTION_NAME ?>[use_events]" value="0">
        <label>
            <input id="category-use-events" type="checkbox" name="<?php echo MapasCulturaisConfiguration::OPTION_NAME ?>[use_events]" value="1" <?php if(isset($selectedFilters['use_events']) && $selectedFilters['use_events']) echo 'checked="checked"' ?>> Usar agenda de eventos
        </label>
    </div>
    <?php
}

add_action('category_add_form_fields', 'mapasculturais_category_add', 11);

/** Save Category Meta **/
function mapasculturais_category_save( $term_id ) {
    if ( isset( $_POST[MapasCulturaisConfiguration::OPTION_NAME] ) ) {
        $t_id = $term_id;
        $cat_meta = $_POST[MapasCulturaisConfiguration::OPTION_NAME];
        
        //save the option array
        update_option( "category_$t_id", $cat_meta );
    }
}
add_action( 'edited_category', 'mapasculturais_category_save' );
add_action( 'created_category', 'mapasculturais_category_save', 11, 1 );
