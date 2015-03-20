<?php
class MapasCulturaisConfiguration {

    protected static $optionName;
    protected static $optionGroupName;

    static function init() {
        self::$optionName = strtolower(__CLASS__);
        self::$optionGroupName = strtolower(__CLASS__) . 'group';

        add_action('admin_init', function() {
            register_setting(self::$optionGroupName, self::$optionName, array(__CLASS__, 'optionsValidation'));
        });

        add_action('admin_menu', function() {
            if ($_GET['page'] == 'mapasculturaisconfiguration') {
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
            add_menu_page(
                "Mapas Culturais", "Mapas Culturais", 'manage_options', self::$optionName, array(__CLASS__, 'contentOutput')
            );
        });
    }

    static function optionsValidation($input) {
        // Se necessário, faça aqui alguma validação ao salvar seu formulário
        return $input;
    }

    static function getConfigModel() {

        $savedFilters = get_theme_option('mapasculturaisconfiguration');
        if (false && !empty($savedFilters['geoDivisions'])) {
            $geoDivisions_encoded = $savedFilters['geoDivisions'];
        } else {
            $_geoDivisions = wp_remote_get(MAPASCULTURAIS_API_URL . 'geoDivision/list/', array('timeout' => '120'));
            $geoDivisions_encoded = $_geoDivisions['body'];
        }
        $geoDivisions = json_decode($geoDivisions_encoded);

        $configs = array(
            'linguagens' => (object) array('order' => 0, 'key' => 'linguagens', 'label' => 'Linguagens', 'data' => array()),
            'classificacaoEtaria' => (object) array('order' => 1, 'key' => 'classificacaoEtaria', 'label' => 'Classificação Etária', 'data' => array()),
            'geoDivisions' => (object) array('order' => 2, 'key' => 'geoDivisions', 'label' => 'Divisões Geográficas:', 'data' => $geoDivisions, 'type' => 'header'),
            'agent' => (object) array('order' => count($geoDivisions) + 3 + 1, 'key' => 'agent', 'label' => 'Agentes', 'data' => array(), 'type' => 'entity'),
            'space' => (object) array('order' => count($geoDivisions) + 3 + 2, 'key' => 'space', 'label' => 'Espaços', 'data' => array(), 'type' => 'entity'),
            'project' => (object) array('order' => count($geoDivisions) + 3 + 3, 'key' => 'project', 'label' => 'Projetos', 'data' => array(), 'type' => 'entity')
        );

        $i = 0;
        foreach ($geoDivisions as $geoDivision) {
            $i++;
            $configs[$geoDivision->metakey] = (object) array('order' => $configs['geoDivisions']->order + $i, 'key' => $geoDivision->metakey, 'label' => $geoDivision->name, 'data' => array());
        }

        uasort($configs, function($a, $b) {
            return $a->order > $b->order;
        });

        return $configs;
    }

    static function fetchApiData() {
        // @TODO remover chamada por entidades
        $cacheGroup = 'API';
        $cacheId = 'configs';

        if (DCache::exists($cacheGroup, $cacheId, 60 * 60 * 24)) {
            _pr('PEGOU DO CACHE ' . date('h:i:s'));

            $configs = DCache::get($cacheGroup, $cacheId);
        } else {

            $configs = self::getConfigModel();

            $defaultRequest = function($urlPath){
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

            DCache::set($cacheGroup, $cacheId, $configs);
        }

        return $configs;
    }

    static protected function metaName($key) {
        return 'theme_options[' . self::$optionName . '][' . $key . ']';
    }

    static function getMetaValue($key) {
        $options = wp_parse_args(get_option('theme_options'), get_theme_default_options());
        $selfOptions = $options[self::$optionName];
        return $selfOptions[$key];
    }

    static function getSelectedEntities(){
        $selectedEntities = array(
            'agent' => self::getMetaValue('agent'),
            'space' => self::getMetaValue('space'),
            'project' => self::getMetaValue('project')
        );

        $decode_entity_json = function($e){
            return json_decode($e);
        };

        $selectedEntities['agent'] = array_map($decode_entity_json, is_array($selectedEntities['agent']) ? $selectedEntities['agent'] : array());
        $selectedEntities['space'] = array_map($decode_entity_json, is_array($selectedEntities['space']) ? $selectedEntities['space'] : array());
        $selectedEntities['project'] = array_map($decode_entity_json, is_array($selectedEntities['project']) ? $selectedEntities['project'] : array());

        return $selectedEntities;
    }

    static function contentOutput() {
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
                <input type="hidden" value="{{json}}" name="theme_options[<?php echo self::$optionName ?>][{{entity}}][{{id}}]" />

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
                            <h4>Selo</h4>
                            <label>
                                <input type="hidden"   name="theme_options[<?php echo self::$optionName ?>][verified]"  value="0">
                                <input type="checkbox" name="theme_options[<?php echo self::$optionName ?>][verified]"  value="1" <?php if (self::getMetaValue('verified')) echo 'checked'; ?>>
                                Retornar somente eventos verificados com selo
                            </label>

                            <h4>Classificação etária</h4>
                            <ul>
                                <?php
                                $metaValue = self::getMetaValue('classificacaoEtaria');
                                foreach ($classificacaoEtaria->data as $d):
                                    ?>
                                    <li>
                                        <label>
                                            <input type="hidden"   name="theme_options[<?php echo self::$optionName ?>][classificacaoEtaria][<?php echo $d ?>]"  value="0">
                                            <input type="checkbox" name="theme_options[<?php echo self::$optionName ?>][classificacaoEtaria][<?php echo $d ?>]"  value="1" <?php if ($metaValue[$d]) echo 'checked'; ?> >
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
                                $metaValue = self::getMetaValue('linguagens');
                                foreach ($linguagens->data as $d):
                                    ?>
                                    <li>
                                        <label>
                                            <input type="hidden"   name="theme_options[<?php echo self::$optionName ?>][linguagens][<?php echo $d ?>]"  value="0">
                                            <input type="checkbox" name="theme_options[<?php echo self::$optionName ?>][linguagens][<?php echo $d ?>]"  value="1" <?php if ($metaValue[$d]) echo 'checked'; ?> >
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
                            $metaValue = self::getMetaValue($geoDivisionMetadata->metakey);
                            ?>
                            <div class='config-section checkbox-list'>
                                <h4><?php echo $geoDivisionMetadata->name ?></h4>
                                <ul>
                                    <?php foreach ($geoDivision->data as $d): ?>
                                        <li>
                                            <label>
                                                <input type="hidden"   name="theme_options[<?php echo self::$optionName ?>][<?php echo $geoDivisionMetadata->metakey ?>][<?php echo $d ?>]"  value="0">
                                                <input type="checkbox" name="theme_options[<?php echo self::$optionName ?>][<?php echo $geoDivisionMetadata->metakey ?>][<?php echo $d ?>]"  value="1" <?php if ($metaValue[$d]) echo 'checked'; ?> >
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
                <p class="textright clear prepend-top">
                    <input type="submit" class="button-primary" value="<?php _e('Salvar', 'cultural'); ?>" />
                </p>
            </form>

        </div>
        <?php
    }

}

MapasCulturaisConfiguration::init();
