<?php

define('API_URL', 'http://spcultura.prefeitura.sp.gov.br/api/');

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

        $geoDivisions = json_decode(wp_remote_get(API_URL . 'geoDivision/list/', ['timeout'=>'120'])['body']);

        $configs = [
           'linguagens' => (object) ['order' => 0, 'key' => 'linguagens', 'label' => 'Linguagens', 'data' => [] ],
           'classificacaoEtaria' => (object) ['order' => 1, 'key' => 'classificacaoEtaria', 'label' => 'Classificação Etária', 'data' => [] ],
           'geoDivisions' => (object) ['order' => 2, 'key' => 'geoDivisions', 'label' => 'Divisões Geográficas:', 'data' => [], 'type' => 'header' ],
           'agents' => (object) ['order' => count($geoDivisions)+3+1, 'key' => 'agents', 'label' => 'Agentes', 'data' => [], 'type' => 'entity' ],
           'spaces' => (object) ['order' => count($geoDivisions)+3+2, 'key' => 'spaces', 'label' => 'Espaços', 'data' => [], 'type' => 'entity'],
           'projects' => (object) ['order' => count($geoDivisions)+3+3, 'key' => 'projects', 'label' => 'Projetos', 'data' => [], 'type' => 'entity']
        ];

        $i=0;
        foreach($geoDivisions as $geoDivision){
            $i++;
            $configs[$geoDivision->metakey] = (object) ['order' => $configs['geoDivisions']->order+$i,'key' => $geoDivision->metakey, 'label' => $geoDivision->name, 'data' => [] ];
        }

        uasort($configs, function($a, $b){
            return $a->order > $b->order;
        });

        return $configs;
    }

    static function fetchApiData($debug = false, $limit = null){

        $cacheGroup = 'API1';
        $cacheId = 'configs';

        if(DCache::exists($cacheGroup, $cacheId, 60 * 60)){

            if($debug){
                _pr('PEGOU DO CACHE ' . date('h:i:s'));
            }

            $configs = DCache::get($cacheGroup, $cacheId);

        }else{

            $configs = self::getConfigModel();

            $defaultRequest = function($urlPath, $appendSelect='') use ($limit) {
                $defaultQueryParameters = [
                    '@select' => 'id,singleUrl,name,type,shortDescription,terms' . ',' . $appendSelect,
                    '@files' =>'(avatar.avatarSmall):url',
                    '@order' =>'name%20ASC'
                ];
                if($limit) {
                    $defaultQueryParameters['@limit'] = $limit;
                }
                $queryString = '';
                foreach($defaultQueryParameters as $key => $val){
                    $queryString .= '&' . $key . '=' . $val;
                }
                $defaultRequestArgs = ['timeout'=>'120'];
                return json_decode(wp_remote_get(API_URL . $urlPath . '?' . $queryString, $defaultRequestArgs)['body']);
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

    static function contentOutput() {

        $configs = self::fetchApiData($debug=true, $limit=10);
        ?>
        <style>
        .thumb {
            width: 72px;
            height: 72px;
            background-color:#ccc;
            margin-right: 5px;
        }
        </style>
        <div class="wrap span-20">
            <h2><?php echo __('Configuração dos Mapas Culturais', 'cultural'); ?></h2>

            <form action="options.php" method="post" class="clear prepend-top">
                <?php settings_fields('theme_options_options'); ?>
                <?php
                    $options = wp_parse_args(get_option('theme_options'), get_theme_default_options());
                    $selfOptions = $options[self::$optionName];
                ?>

                <div class="span-20 ">

                    <?php //////////// Edite a partir daqui //////////  ?>

                    <h3><?php _e("Configuração da API de Eventos", 'cultural'); ?></h3>

                    <p class="textright clear prepend-top">
                        <input type="submit" class="button-primary" value="<?php _e('Salvar', 'cultural'); ?>" />
                    </p>

                    <div class="span-6 last">
                        <label>
                            <strong>Palavra-Chave</strong> <br>
                            <input type="text" name="<?php echo 'theme_options[' . self::$optionName . '][keyword]'; ?>"  value="<?php echo htmlspecialchars($selfOptions['keyword']); ?>" style="width:80%">
                        </label>
                        <br><br>
                        <label>
                            <input type="checkbox" name="<?php echo 'theme_options[' . self::$optionName . '][verified]'; ?>"  <?php if($selfOptions['verified']) echo 'checked'; ?>>
                            <strong>Somente Eventos Verificados com Selo</strong>
                        </label>
                        <br><br>
                        <?php foreach($configs as $c):
                            $metaName = 'theme_options[' . self::$optionName . '][' . $c->key . ']';
                            $metaValue = $selfOptions[$c->key]; ?>

                            <?php if($c->type === 'entity') echo '<h1>'; else echo '<strong>';  ?>
                                <?php _e($c->label, "cultural"); ?>
                            <?php if($c->type === 'entity') echo '</h1>'; else echo '</strong>';  ?>
                            <br>
                            <?php switch($c->type):
                                      case 'header': ?>
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
                                <?php default: ?>
                                    <?php foreach($c->data as $d): ?>
                                        <label>
                                            <input type="checkbox" name="<?php echo "{$metaName}[{$d}]"; ?>"  <?php if($metaValue[$d]) echo 'checked'; ?> >
                                            <?php echo $d; ?>
                                        </label>
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