<?php
if(!defined('API_URL')){
    define('API_URL', 'http://spcultura.prefeitura.sp.gov.br/api/');
}

class MapasCulturaisConfiguration {

    protected static $optionName;
    protected static $optionGroupName;

    static function init() {
        self::$optionName = strtolower(__CLASS__);
        self::$optionGroupName = strtolower(__CLASS__) . 'group';

        add_action( 'admin_init', function(){
            register_setting( self::$optionGroupName, self::$optionName, array( __CLASS__, 'optionsValidation') );
        } );

        add_action( 'admin_menu', function(){
            if($_GET['page'] == 'mapasculturaisconfiguration'){
                wp_enqueue_script('jquery-ui-tabs');
                wp_enqueue_script('jquery-ui-autocomplete');

                wp_enqueue_style('jui', get_stylesheet_directory_uri() . '/css/jquery-ui-1.11.4.custom/jquery-ui.min.css');
            }
            add_menu_page(
                "Mapas Culturais",
                "Mapas Culturais",
                'manage_options',
                self::$optionName,
                array( __CLASS__, 'contentOutput' )
            );
        } );
    }

    static function optionsValidation($input) {
        // Se necessário, faça aqui alguma validação ao salvar seu formulário
        return $input;
    }

    static function getConfigModel(){

        $savedFilters = get_theme_option('mapasculturaisconfiguration');
        if(false && !empty($savedFilters['geoDivisions'])){
            $geoDivisions_encoded = $savedFilters['geoDivisions'];
        }else{
            $_geoDivisions = wp_remote_get(API_URL . 'geoDivision/list/', array('timeout'=>'120'));
            $geoDivisions_encoded = $_geoDivisions['body'];
        }
        $geoDivisions = json_decode($geoDivisions_encoded);

        $configs = array(
           'linguagens' => (object) array('order' => 0, 'key' => 'linguagens', 'label' => 'Linguagens', 'data' => array()),
           'classificacaoEtaria' => (object) array('order' => 1, 'key' => 'classificacaoEtaria', 'label' => 'Classificação Etária', 'data' => array()),
           'geoDivisions' => (object) array('order' => 2, 'key' => 'geoDivisions', 'label' => 'Divisões Geográficas:', 'data' => $geoDivisions, 'type' => 'header'),
           'agents' => (object) array('order' => count($geoDivisions)+3+1, 'key' => 'agents', 'label' => 'Agentes', 'data' => array(), 'type' => 'entity'),
           'spaces' => (object) array('order' => count($geoDivisions)+3+2, 'key' => 'spaces', 'label' => 'Espaços', 'data' => array(), 'type' => 'entity'),
           'projects' => (object) array('order' => count($geoDivisions)+3+3, 'key' => 'projects', 'label' => 'Projetos', 'data' => array(), 'type' => 'entity')
        );

        $i=0;
        foreach($geoDivisions as $geoDivision){
            $i++;
            $configs[$geoDivision->metakey] = (object) array('order' => $configs['geoDivisions']->order+$i,'key' => $geoDivision->metakey, 'label' => $geoDivision->name, 'data' => array());
        }

        uasort($configs, function($a, $b){
            return $a->order > $b->order;
        });

        return $configs;
    }

    static function fetchApiData($debug = false, $limit = null){

        $cacheGroup = 'API';
        $cacheId = 'configs';

        if(DCache::exists($cacheGroup, $cacheId, 60 * 60 * 24)){

            if($debug){
                _pr('PEGOU DO CACHE ' . date('h:i:s'));
            }

            $configs = DCache::get($cacheGroup, $cacheId);

        }else{

            $configs = self::getConfigModel();

            $defaultRequest = function($urlPath, $appendSelect='') use ($limit) {
                $defaultQueryParameters = array(
                    '@select' => 'id,singleUrl,name,type,shortDescription,terms' . ',' . $appendSelect,
                    '@files' =>'(avatar.avatarSmall):url',
                    '@order' =>'name%20ASC'
                );
                if($limit) {
                    $defaultQueryParameters['@limit'] = $limit;
                }
                $queryString = '';
                foreach($defaultQueryParameters as $key => $val){
                    $queryString .= '&' . $key . '=' . $val;
                }
                $defaultRequestArgs = array('timeout'=>'120');
                $response = wp_remote_get(API_URL . $urlPath . '?' . $queryString, $defaultRequestArgs);
                return json_decode($response['body']);
            };

            $configs['linguagens']->data = $defaultRequest('term/list/linguagem/');

            $eventDescription = $defaultRequest('event/describe/');
            $configs['classificacaoEtaria']->data = array_values((array) $eventDescription->classificacaoEtaria->options);

            $configs['agents']->data = $defaultRequest('agent/find/');
            $configs['spaces']->data = $defaultRequest('space/find/', 'endereco');
            $configs['projects']->data = $defaultRequest('project/find/');

            $geoDivisions = $defaultRequest('geoDivision/list/includeData:1/');
            foreach($geoDivisions as $geoDivision){
                $configs[$geoDivision->metakey]->data = $geoDivision->data;
            }

            DCache::set($cacheGroup, $cacheId, $configs);
        }

        //_pr($configs);

        return $configs;
    }

    static protected function metaName($key){
        return 'theme_options[' . self::$optionName . '][' . $key . ']';
    }

    static function getMetaValue($key){
        $options = wp_parse_args(get_option('theme_options'), get_theme_default_options());
        $selfOptions = $options[self::$optionName];
        return $selfOptions[$key];
    }

    static function contentOutput() {
        $configs = self::fetchApiData($debug=true, $limit=20);
        _pr(array_keys($configs));
        extract($configs);


        ?>
        <style>
        .thumb {
            width: 72px;
            height: 72px;
            background-color:#ccc;
            margin-right: 5px;
        }

        .config-section {
            float:left;
            margin:0 1em;
        }

        .checkbox-list ul {
            padding-right:15px;
            max-height:300px;
            overflow:auto;
        }
        .checkbox-list ul li {
            white-space: nowrap;
        }
        </style>
        <script type="text/javascript">
            (function($){
                $(function(){
                    $('#mapasculturais-config-tabs').tabs();
                });
            })(jQuery);
        </script>
        <div class="wrap span-20">
            <h2><?php _e('Filtros da API do Mapas Culturais', 'cultural'); ?></h2>
            <p>
                <?php _e('Configure aqui quais eventos a API do Mapas Culturais deve retornar para o site.', 'cultural'); ?>
            </p>

            <form action="options.php" method="post" class="clear prepend-top">
                <?php settings_fields('theme_options_options'); ?>
                <p class="textright clear prepend-top">
                    <input type="submit" class="button-primary" value="<?php _e('Salvar', 'cultural'); ?>" />
                </p>

                <div id="mapasculturais-config-tabs">
                    <ul>
                        <li><a href="#tab-geral">Geral</a></li>
                        <li><a href="#tab-recorte-geografico">Recorte geográfico</a></li>
                        <li><a href="#tab-agentes">Agentes Culturais</a></li>
                        <li><a href="#tab-espacos">Espaços</a></li>
                        <li><a href="#tab-projetos">Projetos/Editais</a></li>
                    </ul>

                    <div id="tab-geral" class='config-tab'>
                        <div class='config-section'>
                            <h4>Classificação etária</h4>
                            <ul>
                            <?php
                            $metaValue = self::getMetaValue('classificacaoEtaria');
                            foreach($classificacaoEtaria->data as $d): ?>
                                <li>
                                    <label>
                                        <input type="hidden"   name="theme_options[<?php echo self::$optionName ?>][classificacaoEtaria][<?php echo $d ?>]"  value="0">
                                        <input type="checkbox" name="theme_options[<?php echo self::$optionName ?>][classificacaoEtaria][<?php echo $d ?>]"  value="1" <?php if($metaValue[$d]) echo 'checked'; ?> >
                                        <?php echo $d; ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                            </ul>

                            <h4>Selo</h4>
                            <label>
                                <input type="hidden"   name="theme_options[<?php echo self::$optionName ?>][verified]"  value="0">
                                <input type="checkbox" name="theme_options[<?php echo self::$optionName ?>][verified]"  value="1" <?php if(self::getMetaValue('verified')) echo 'checked'; ?>>
                                Retornar somente eventos verificados com selo
                            </label>
                        </div>
                        <div class='config-section checkbox-list'>
                            <h4>Linguagens</h4>
                            <ul>
                            <?php
                            $metaValue = self::getMetaValue('linguagens');
                            foreach($linguagens->data as $d): ?>
                                <li>
                                    <label>
                                        <input type="hidden"   name="theme_options[<?php echo self::$optionName ?>][linguagens][<?php echo $d ?>]"  value="0">
                                        <input type="checkbox" name="theme_options[<?php echo self::$optionName ?>][linguagens][<?php echo $d ?>]"  value="1" <?php if($metaValue[$d]) echo 'checked'; ?> >
                                        <?php echo $d; ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                            </ul>
                        </div>
                        <div class='clear'></div>
                    </div>
                    <div id="tab-recorte-geografico">
                        recorte geográfico
                    </div>
                    <div id="tab-agentes">
                        agentes
                    </div>
                    <div id="tab-espacos">
                        espaços
                    </div>
                    <div id="tab-projetos">
                        projetos
                    </div>
                </div>
            </form>

                <div class="span-20 ">
                    <?php //////////// Edite a partir daqui //////////  ?>
                    <div class="span-6 last">
                        <?php foreach($configs as $c):
                            $metaName = 'theme_options[' . self::$optionName . '][' . $c->key . ']'; ?>


                            <?php if($c->type === 'entity') echo '<h1>'; else echo '<strong>';  ?>
                                <?php _e($c->label, "cultural"); ?>
                            <?php if($c->type === 'entity') echo '</h1>'; else echo '</strong>';  ?>
                            <br>
                            <?php switch($c->type):
                                      case 'header': ?>
                                    <input type="text"  name="<?php echo $metaName; ?>"  value="<?php echo htmlspecialchars(json_encode($c->data)); ?>">
                                    <br>
                                    <?php break; ?>
                                <?php case 'entity': ?>
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

                                            <input type="checkbox" name="<?php echo "{$metaName}[{$entity->id}]"; ?>"  <?php if($metaValue[$entity->id]) echo 'checked'; ?> value="<?php echo htmlspecialchars(json_encode($entity)); ?>">

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
                                    <br>
                                    <?php break; ?>
                            <?php endswitch; ?>
                        <?php endforeach; ?>
                    </div>

                    <?php ///// Edite daqui pra cima ////  ?>

                </div>

                <p class="textright clear prepend-top">
                    <input type="submit" class="button-primary" value="<?php _e('Salvar', 'cultural'); ?>" />
                </p>
            </form>
         </div>
        <?php
    }
}
MapasCulturaisConfiguration::init();