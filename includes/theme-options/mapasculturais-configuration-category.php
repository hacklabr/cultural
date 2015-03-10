<?php

define('API_URL', 'http://spcultura.prefeitura.sp.gov.br/api/');

if(!class_exists('MapasCulturaisConfiguration')){
    require 'mapasculturais-configuration.php';
}


/** Add Colorpicker Field to "Add New Category" Form **/
function mapasculturais_category_edit( $term ) {
    ?>
    <style>
    .thumb {
        width: 72px;
        height: 72px;
        background-color:#ccc;
        margin-right: 5px;
    }
    </style>

    <?php
        $options = wp_parse_args(get_option('theme_options'), get_theme_default_options());
        $availableFilters = $options['mapasculturaisconfiguration'];
        $selectedFilters = get_option("category_{$term->term_id}");

    ?>

    <tr>
        <th><h2>Mapas Culturais</h2></th>
        <td valign="bottom"><h4>Esta categoria está associada aos seguintes filtros:</h4></td>
    </tr>

    <!--tr>
        <th>Palavra-Chave</th>
        <td><input type="text" name="<?php echo 'mapasculturais_category[keyword]'; ?>"  value="<?php echo htmlspecialchars($selectedFilters['keyword']); ?>" style="width:80%"></td>
    </tr>

    <tr>
        <th>Eventos Verificados com Selo</th>
        <td><input type="checkbox" name="<?php echo 'mapasculturais_category[verified]'; ?>"  <?php if($selectedFilters['verified']) echo 'checked'; ?>></td>
    </tr-->

    <?php

    foreach(MapasCulturaisConfiguration::getConfigModel() as $c):

        if(!$availableFilters[$c->key]) {
            if($c->type === 'header'){
                ?><tr><th colspan="2"><?php _e($c->label, "cultural"); ?></th></tr><?php
            }
            continue;
        }

        if($c->type === 'entity'){
            foreach($availableFilters[$c->key] as $id => $json){
                $c->data[$id] = json_decode($json);
            }
        }elseif($c->type !== 'header'){
            $c->data = array_keys($availableFilters[$c->key]);
        }

        $metaName = 'mapasculturais_category[' . $c->key . ']';
        $metaValue = $selectedFilters[$c->key];

        ?>

        <tr>
            <th>
                <?php _e($c->label, "cultural"); ?>
            </th>
            <td>
                <?php if($c->type === 'entity'): ?>
                    <?php foreach($c->data as $entity): ?>
                        <label>
                            <a href="<?php echo $entity->singleUrl; ?>" target="_blank">
                                <?php
                                if(!empty($entity->{'@files:avatar.avatarSmall'})){
                                    $avatarUrl = $entity->{'@files:avatar.avatarSmall'}->url;
                                }else{
                                    $avatarUrl = API_URL . '../assets/img/avatar--' . substr($c->key, 0, -1) . '.png';
                                }
                                ?>
                                <img class="thumb" src="<?php echo $avatarUrl; ?>" align="left" alt="Ver Página">
                            </a>
                            <input type="checkbox" name="<?php echo "{$metaName}[{$entity->id}]"; ?>"  <?php if($metaValue[$entity->id]) echo 'checked'; ?> >
                            <strong><?php echo $entity->name; ?></strong>
                            <?php if($entity->endereco):?>
                                - <?php echo $entity->endereco; ?>
                            <?php endif; ?>
                            <br>Tipo: <?php echo $entity->type->name; ?>
                            <br>
                            <?php if(!empty($entity->terms->area)):?>
                                Área(s) de atuação: <?php echo implode(', ', $entity->terms->area); ?>
                            <?php endif; ?>
                            <br>
                            <?php if(!empty($entity->terms->tag)):?>
                                Tags: <?php echo implode(', ', $entity->terms->tag); ?>
                            <?php endif; ?>
                        </label>
                        <br>
                        <br>
                    <?php endforeach; ?>

                <?php elseif($c->type !== 'header'): ?>

                    <?php foreach($c->data as $d): ?>
                        <label>
                            <input type="checkbox" name="<?php echo "{$metaName}[{$d}]"; ?>"  <?php if($metaValue[$d]) echo 'checked'; ?> >
                            <?php echo $d; ?>
                        </label>
                        <br>
                    <?php endforeach; ?>

                <?php endif; ?>
            </td>
        </tr>
    <?php endforeach; ?>
<?php
}
add_action( 'category_edit_form_fields', 'mapasculturais_category_edit', 11 );



/** Save Category Meta **/
function mapasculturais_category_save( $term_id ) {
    echo "category_$term_id";
    if ( isset( $_POST['mapasculturais_category'] ) ) {
        $t_id = $term_id;
        $cat_meta = get_option( "category_$t_id");
        $cat_keys = array_keys($_POST['mapasculturais_category']);
        foreach ($cat_keys as $key){
            if (isset($_POST['mapasculturais_category'][$key])){
                $cat_meta[$key] = $_POST['mapasculturais_category'][$key];
            }
        }
        //save the option array
        update_option( "category_$t_id", $cat_meta );
    }
}
add_action( 'edited_category', 'mapasculturais_category_save' );
add_action( 'created_category', 'mapasculturais_category_save', 11, 1 );
