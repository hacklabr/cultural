<?php

class CulturalConfigModel {

    static $_ORDER = 0;
    public $order;
    public $key;
    public $label;
    public $type;
    public $data;

    public function __construct($key, $label, $type, $data = array()) {
        $this->order = self::$_ORDER++;
        $this->key = $key;
        $this->label = $label;
        $this->type = $type;
        $this->data = $data;
    }

}

class MapasCulturaisConfiguration {

    const OPTION_NAME = 'mapasculturais-configuration';
    const TRANSIENT_TIMEOUT = 30;

    static function init() {

        add_action('init', function() {
            // define as constantes com os valores padrão se estas não forem configuradas no wp-config.php
            define('MAPASCULTURAIS_URL', MapasCulturaisConfiguration::getValue('URL'));
            define('MAPASCULTURAIS_API_URL', MAPASCULTURAIS_URL . 'api/');
            define('MAPASCULTURAIS_NAME', 'SP Cultura');
            define('TRANSIENTE_TIMEOUT_EVENT_INFO', 24 * 60 * 60);
        });

        add_action('admin_init', function() {
            register_setting('theme_options_options', self::OPTION_NAME, array(__CLASS__, 'optionsValidation'));
        });

        add_action('admin_menu', function() {
            if (isset($_GET['page']) && $_GET['page'] == self::OPTION_NAME || isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'category') {
                self::enqueueScripts();
            }
            add_menu_page(
                    "Mapas Culturais", "Mapas Culturais", 'manage_options', self::OPTION_NAME, array(__CLASS__, 'contentOutput')
            );
        });
    }
    
    static function enqueueScripts(){
        wp_enqueue_script('jquery-ui-tabs');
        wp_enqueue_script('jquery-ui-autocomplete');
        wp_enqueue_script('mustache', get_stylesheet_directory_uri() . '/js/lib/mustache.js');
        wp_enqueue_script('mapasculturais-configuration', get_stylesheet_directory_uri() . '/js/mapasculturais-configuration.js');

        wp_localize_script('mapasculturais-configuration', 'selectedEntities', self::getSelectedEntities());


        wp_localize_script('jquery-ui-autocomplete', 'vars', array(
            'apiUrl' => MAPASCULTURAIS_API_URL
        ));

        wp_enqueue_style('jui', get_stylesheet_directory_uri() . '/css/jquery-ui-1.11.4.custom/jquery-ui.min.css');    
    }

    static function optionsValidation($input) {
        // Se necessário, faça aqui alguma validação ao salvar seu formulário
        if (!empty($input['URL'])) {
            $input['URL'] = trim($input['URL']);
            if (!preg_match('#^https?:\/\/.*#', $input['URL'])) {
                $input['URL'] = 'http://' . $input['URL'];
            }

            if (substr($input['URL'], -1) != '/') {
                $input['URL'] .= '/';
            }
        }
        return $input;
    }

    static function getTransientID($key) {
        return 'transient:' . md5(MAPASCULTURAIS_URL) . ':' . $key;
    }

    static function getConfigModel() {
        $transient_id = self::getTransientID('geoDivisions');

        $geoDivisions = get_transient($transient_id);

        if (!$geoDivisions) {
            $_geoDivisions = wp_remote_get(MAPASCULTURAIS_API_URL . 'geoDivision/list/', array('timeout' => '120'));

            $geoDivisions_encoded = $_geoDivisions['body'];

            $geoDivisions = json_decode($geoDivisions_encoded);

            set_transient($transient_id, $geoDivisions, self::TRANSIENT_TIMEOUT);
        }

        $configs = array(
            'URL' => new CulturalConfigModel('URL', 'URL da instalação do Mapas Culturais', 'header', ''),
            'verified' => new CulturalConfigModel('verified', 'Resultados Verificados', 'header', false),
            'linguagens' => new CulturalConfigModel('linguagens', 'Linguagens', 'header'),
            'classificacaoEtaria' => new CulturalConfigModel('classificacaoEtaria', 'Classificação Etária', 'header'),
            'geoDivisions' => new CulturalConfigModel('geoDivisions', 'Divisões Geográficas', 'header', $geoDivisions),
            'agent' => new CulturalConfigModel('agent', 'Agentes', 'entity'),
            'space' => new CulturalConfigModel('space', 'Espaços', 'entity'),
            'project' => new CulturalConfigModel('project', 'Projetos', 'entity')
        );


        foreach ($geoDivisions as $geoDivision) {
            $configs[$geoDivision->metakey] = new CulturalConfigModel($geoDivision->metakey, $geoDivision->name, 'header');
        }


        uasort($configs, function($a, $b) {
            return $a->order > $b->order;
        });

        return $configs;
    }

    static function fetchApiData() {
        $transient_id = self::getTransientID('apiData');
        $configs = get_transient($transient_id);
        if (!$configs) {
            $configs = self::getConfigModel();

            $defaultRequest = function($urlPath) {
                $defaultRequestArgs = array('timeout' => '120');
                $response = wp_remote_get(MAPASCULTURAIS_API_URL . $urlPath, $defaultRequestArgs);

                return json_decode($response['body']);
            };

            $configs['linguagens']->data = $defaultRequest('term/list/linguagem/');

            $eventDescription = $defaultRequest('event/describe/');
            $configs['classificacaoEtaria']->data = array_values((array) $eventDescription->classificacaoEtaria->options);

            $geoDivisions = $defaultRequest('geoDivision/list/includeData:1/');

            foreach ($geoDivisions as $geoDivision) {
                $configs[$geoDivision->metakey]->data = $geoDivision->data;
            }

            set_transient($transient_id, $configs, self::TRANSIENT_TIMEOUT);
        }
        return $configs;
    }

    static function getOption() {
        return wp_parse_args(get_option(self::OPTION_NAME));
    }

    static function getValue($key, $options = null) {
        if(is_null($options)){
            $options = self::getOption();
        }

        return isset($options[$key]) ? $options[$key] : null;
    }

    static function getSelectedEntities() {
        $decode_entity_json = function($e) {
            return json_decode(stripslashes($e));
        };
        
        $get_id = function($e){
            return $e->id;
        };
        
        $selectedEntities = array(
            'agent' => self::getValue('agent'),
            'space' => self::getValue('space'),
            'project' => self::getValue('project')
        );

        $selectedEntities['agent'] = array_map($decode_entity_json, is_array($selectedEntities['agent']) ? $selectedEntities['agent'] : array());
        $selectedEntities['space'] = array_map($decode_entity_json, is_array($selectedEntities['space']) ? $selectedEntities['space'] : array());
        $selectedEntities['project'] = array_map($decode_entity_json, is_array($selectedEntities['project']) ? $selectedEntities['project'] : array());
        
        if(isset($_GET['taxonomy']) && $_GET['taxonomy'] == 'category' && isset($_GET['tag_ID'])){
            $selectedFilters = get_option("category_{$_GET['tag_ID']}");
            
            $selectedEntities = array(
                'agentIds' => array_values(array_map($get_id, $selectedEntities['agent'])),
                'spaceIds' => array_values(array_map($get_id, $selectedEntities['space'])),
                'projectIds' => array_values(array_map($get_id, $selectedEntities['project'])),
                
                'agent' => self::getValue('agent', $selectedFilters),
                'space' => self::getValue('space', $selectedFilters),
                'project' => self::getValue('project', $selectedFilters)
            );
            
            $selectedEntities['agent'] = array_map($decode_entity_json, is_array($selectedEntities['agent']) ? $selectedEntities['agent'] : array());
            $selectedEntities['space'] = array_map($decode_entity_json, is_array($selectedEntities['space']) ? $selectedEntities['space'] : array());
            $selectedEntities['project'] = array_map($decode_entity_json, is_array($selectedEntities['project']) ? $selectedEntities['project'] : array());
        }


        return $selectedEntities;
    }

    static function printForm($category_id = null, $categoryOptions = null) {
        
        $configs = self::fetchApiData();
        
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

            .ui-autocomplete {
                max-height: 550px;
                min-width:350px;
                max-width:700px;
                overflow-y: auto;
                /* prevent horizontal scrollbar */
                overflow-x: hidden;
            }

            .entity-list-item { border-bottom: 1px solid #ddd; cursor: pointer; padding:10px; }
            .entity-list-item img { float:left; margin-right:12px;  }
            .entity-list-item--name { font-weight: bold; }
            .entity-list-item label { font-weight: bold; }

            .entity-container .entity-list-item { border: 1px solid #dfdfdf; margin:5px; width: 350px; min-height:120px; float:left; cursor: default;}
            .entity-container .entity-list-item:hover { background-color: #dfdfdf;  }
            .entity-container .entity-list-item .js-remove { float:right; background:red; color:white; width:20px; height:20px; text-decoration: none;}

            ul.ui-autocomplete, ul.ui-autocomplete article { max-width: 500px; }
        </style>

        <!-- template do resultado da busca por agentes -->
        <script id="template-autocomplete" type="text/html">
            <article class='entity-list-item js-add-entity-to-list'>
                {{#avatarUrl}}
                <img src="{{avatarUrl}}" width='72' height='72'/>
                {{/avatarUrl}}

                <div class='entity-list-item--name'>{{name}}</div>
                <div class='entity-list-item--type'><label>Tipo:</label> <span>{{type}}</span></div>

                {{#endereco}}
                <div class='entity-list-item--endereco'><label>Endereço:</label> <span>{{endereco}}</span></div>
                {{/endereco}}

                {{#areas}}
                <div class='entity-list-item--taxonomy'><label>Áreas de atuação:</label> <span>{{areas}}</span></div>
                {{/areas}}

                <div class='entity-list-item--taxonomy'><label>Tags:</label> <span>{{tags}}</span></div>

                <div class='clear'></div>
            </article>
        </script>

        <script id="template-entity" type="text/html">
            <article class='entity-list-item js-entity-list-item'>
                <a href="#" class="js-remove" title="Remover"></a>
                <input type="hidden" value="{{json}}" name="<?php echo self::OPTION_NAME ?>[{{entity}}][{{id}}]" />

                {{#avatarUrl}}
                <img src="{{avatarUrl}}" width='72' height='72'/>
                {{/avatarUrl}}

                <div class='entity-list-item--name js-name'>{{name}}</div>
                <div class='entity-list-item--type'><label>Tipo:</label> <span>{{type}}</span></div>

                {{#endereco}}
                <div class='entity-list-item--endereco'><label>Endereço:</label> <span>{{endereco}}</span></div>
                {{/endereco}}

                {{#areas}}
                <div class='entity-list-item--taxonomy'><label>Áreas de atuação:</label> <span>{{areas}}</span></div>
                {{/areas}}

                <div class='entity-list-item--taxonomy'><label>Tags:</label> <span>{{tags}}</span></div>

                <div class='clear'></div>
            </article>
        </script>
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
                    <?php if (!$category_id || !self::getValue('verified')): ?>
                        <h4>Selo</h4>
                        <label>
                            <input type="hidden"   name="<?php echo self::OPTION_NAME ?>[verified]"  value="0">
                            <input type="checkbox" name="<?php echo self::OPTION_NAME ?>[verified]"  value="1" <?php if (self::getValue('verified', $categoryOptions)) echo 'checked'; ?>>
                            Retornar somente eventos verificados com selo
                        </label>
                    <?php endif; ?>

                    <h4>Classificação etária</h4>
                    <ul>
                        <?php
                        $generalMetaValue = self::getValue('classificacaoEtaria');                        

                        $_selected = 0;
                        foreach ($classificacaoEtaria->data as $d){
                            if(isset($generalMetaValue[$d]) && $generalMetaValue[$d]){
                                $_selected++;
                            }
                        }

                        $metaValue = self::getValue('classificacaoEtaria', $categoryOptions);
                        
                        foreach ($classificacaoEtaria->data as $d):
                            if($category_id && $_selected && !$generalMetaValue[$d]){
                                continue;
                            }
                            ?>
                            <li>
                                <label>
                                    <input type="hidden"   name="<?php echo self::OPTION_NAME ?>[classificacaoEtaria][<?php echo $d ?>]"  value="0">
                                    <input type="checkbox" name="<?php echo self::OPTION_NAME ?>[classificacaoEtaria][<?php echo $d ?>]"  value="1" <?php if (isset($metaValue[$d]) && $metaValue[$d]) echo 'checked'; ?> >
                                    <?php echo $d; ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class='config-section checkbox-list'>
                    <h4>Linguagens</h4>
                    <ul>
                        <?php
                        $generalMetaValue = self::getValue('linguagens');                        

                        $_selected = 0;
                        foreach ($linguagens->data as $d){
                            if(isset($generalMetaValue[$d]) && $generalMetaValue[$d]){
                                $_selected++;
                            }
                        }

                        $metaValue = self::getValue('linguagens', $categoryOptions);
                        foreach ($linguagens->data as $d):
                            if($category_id && $_selected && !$generalMetaValue[$d]){
                                continue;
                            }
                            ?>
                            <li>
                                <label>
                                    <input type="hidden"   name="<?php echo self::OPTION_NAME ?>[linguagens][<?php echo $d ?>]"  value="0">
                                    <input type="checkbox" name="<?php echo self::OPTION_NAME ?>[linguagens][<?php echo $d ?>]"  value="1" <?php if (isset($metaValue[$d]) && $metaValue[$d]) echo 'checked'; ?> >
                                    <?php echo $d; ?>
                                </label>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <div class='clear'></div>
            </div>
            <div id="tab-recorte-geografico">
                <?php
                foreach ($geoDivisions->data as $geoDivisionMetadata):
                    $geoDivision = ${$geoDivisionMetadata->metakey};
                    
                    $generalMetaValue = self::getValue($geoDivisionMetadata->metakey);                        

                    $_selected = 0;
                    foreach ($geoDivision->data as $d){
                        if(isset($generalMetaValue[$d]) && $generalMetaValue[$d]){
                            $_selected++;
                        }
                    }
                    
                    $metaValue = self::getValue($geoDivisionMetadata->metakey, $categoryOptions);
                    ?>
                    <div class='config-section checkbox-list'>
                        <h4><?php echo $geoDivisionMetadata->name ?></h4>
                        <ul>
                            <?php 
                            foreach ($geoDivision->data as $d): 
                                if($category_id && $_selected && !$generalMetaValue[$d]){
                                    continue;
                                }
                                ?>
                                <li>
                                    <label>
                                        <input type="hidden"   name="<?php echo self::OPTION_NAME ?>[<?php echo $geoDivisionMetadata->metakey ?>][<?php echo $d ?>]"  value="0">
                                        <input type="checkbox" name="<?php echo self::OPTION_NAME ?>[<?php echo $geoDivisionMetadata->metakey ?>][<?php echo $d ?>]"  value="1" <?php if (isset($metaValue[$d]) && $metaValue[$d]) echo 'checked'; ?> >
                                        <?php echo $d; ?>
                                    </label>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endforeach; ?>
                <div class='clear'></div>
            </div>
            <div id="tab-agentes">
                <input  type='text' placeholder="Buscar Agente" class='entity-autocomplete' data-entity='agent'/>
                <div id="agent-container" class="entity-container js-entity-container"></div>
                <div class='clear'></div>
            </div>
            <div id="tab-espacos">
                <input  type='text' placeholder="Buscar Espaço" class='entity-autocomplete' data-entity='space'/>
                <div id="space-container" class="entity-container js-entity-container"></div>
                <div class='clear'></div>
            </div>
            <div id="tab-projetos">
                <input  type='text' placeholder="Buscar Projeto/Edital" class='entity-autocomplete' data-entity='project'/>
                <div id="project-container" class="entity-container js-entity-container"></div>
                <div class='clear'></div>
            </div>
        </div>
        <?php
    }

    static function contentOutput() {
        ?>
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
                <?php if (MAPASCULTURAIS_URL): ?>
                    <?php self::printForm() ?>

                <?php endif; ?>
                <label> URL da instalação do mapas culturais <input type="text" name="<?php echo self::OPTION_NAME ?>[URL]" value="<?php echo self::getValue('URL') ?>"></label>
                <p class="textright clear prepend-top">
                    <input type="submit" class="button-primary" value="<?php _e('Salvar', 'cultural'); ?>" />
                </p>
            </form>

        </div>
        <?php
    }

}

MapasCulturaisConfiguration::init();
