<?php
require_once 'mapasculturais-configuration.php';


/** Add Colorpicker Field to "Add New Category" Form **/
function mapasculturais_category_edit( $term ) {        
    $selectedFilters = get_option("category_{$term->term_id}");

    ?>
    <tr>
        <td valign="bottom" colspan="2">
            <h4>Esta categoria está associada aos seguintes filtros:</h4>
            <?php 
                MapasCulturaisConfiguration::printForm($term->term_id, $selectedFilters);
            ?>
        </td>
    </tr>
<?php
}
add_action( 'category_edit_form_fields', 'mapasculturais_category_edit', 11 );



/** Save Category Meta **/
function mapasculturais_category_save( $term_id ) {
    echo "category_$term_id";
    if ( isset( $_POST[MapasCulturaisConfiguration::OPTION_NAME] ) ) {
        $t_id = $term_id;
        $cat_meta = get_option( "category_$t_id");
        $cat_keys = array_keys($_POST[MapasCulturaisConfiguration::OPTION_NAME]);
        foreach ($cat_keys as $key){
            if (isset($_POST[MapasCulturaisConfiguration::OPTION_NAME][$key])){
                $cat_meta[$key] = $_POST[MapasCulturaisConfiguration::OPTION_NAME][$key];
            }
        }
        //save the option array
        update_option( "category_$t_id", $cat_meta );
    }
}
add_action( 'edited_category', 'mapasculturais_category_save' );
add_action( 'created_category', 'mapasculturais_category_save', 11, 1 );
