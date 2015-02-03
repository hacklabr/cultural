<?php
class MapasCulturaisConfiguration {
    const NAME = "MC";

    protected static $nameClass;
    protected static $nameGroup;
    protected static $options;
    protected static $widgetsName;

    static function init() {
        self::$nameClass = strtolower(__CLASS__);
        self::$nameGroup = strtolower(__CLASS__) . 'group';

        add_action( 'admin_init', array( __CLASS__, 'optionsInit' ) );
        add_action( 'admin_menu', array( __CLASS__, 'menu' ) );

        wp_enqueue_script( 'mapasculturais-configuration', get_template_directory_uri() . '/js/mapasculturais-configuration.js', array('jquery'), '', true );
        wp_enqueue_script( 'mapasculturais-configuration', 'https://raw.githubusercontent.com/ehynds/jquery-ui-multiselect-widget/1.13/src/jquery.multiselect.js', array('jquery'), '', true );


    }

    static function optionsInit() {
        register_setting( self::$nameGroup, self::$nameClass, array( __CLASS__, 'optionsValidation') );
    }

    static function menu() {
        add_menu_page (
            self::NAME,
            self::NAME,
            'manage_options',
            self::$nameClass,
            array( __CLASS__, 'callbackPage' )
        );
    }

    static function optionsValidation($input) {
        return $input;
    }

    static function callbackPage() {
        define('API_URL', 'http://spcultura.prefeitura.sp.gov.br/api/');
        if(DCache::exists('API', 'config', 15 * 60)){
            _pr('PEGOU DO CACHE' . date('h:i:s'));
            $config = DCache::get('API', 'config');
        }else{
            //$linguagens = json_decode(file_get_contents(API_URL . 'term/list/linguagem'));
            //$geoDivisions = json_decode(file_get_contents(API_URL . 'geoDivision/list/includeData:1'));
            $eventDescription = json_decode(file_get_contents(API_URL . 'event/describe'));
            $agents = json_decode(file_get_contents(API_URL . 'agent/find/?@select=id,singleUrl,name,type,shortDescription,terms&@files=(avatar.avatarSmall):url&@order=name%20ASC'));
            $spaces = json_decode(file_get_contents(API_URL . 'space/find/?@select=id,singleUrl,name,type,shortDescription,terms&@files=(avatar.avatarSmall):url&@order=name%20ASC'));

            $config = [
                //'linguagens' => $linguagens,
                //'geoDivisions' => $geoDivisions,
                'eventDescription' => $eventDescription,
                'agents' => $agents,
                'spaces' => $spaces
            ];

            DCache::set('API', 'config', $config);

        }
        _pr($config);
        ?>
        <div class="wrap span-20">
            <h2><?php echo __('Configuração dos Mapas Culturais', 'cultural'); ?></h2>

            <form action="options.php" method="post" class="clear prepend-top">

                <?php settings_fields('theme_options_options'); ?>
                <?php
                    $options = wp_parse_args(get_option('theme_options'), get_theme_default_options());
                    $selfOptions = $options[self::$nameClass];
                ?>

                <div class="span-20 ">

                    <?php //////////// Edite a partir daqui //////////  ?>

                    <h3><?php _e("Configuração da API de Eventos", 'cultural'); ?></h3>

                    <div class="span-6 last">
                        <label for="linguagens"><strong><?php _e("Linguagens", "cultural"); ?></strong></label><br/>
                        <select id="linguagens" class="text" name="theme_options[<?php echo self::$nameClass; ?>][linguagens]" data-selected="<?php echo htmlspecialchars($selfOptions['linguagens']); ?>" style="width: 80%">
                        </select>
                        <br/><br/>
                        <label for="wellcome_title"><strong><?php _e("Twitter", "cultural"); ?></strong></label><br/>
                        <input type="text" id="wellcome_title" class="text" name="theme_options[social_networks][twitter]" value="<?php echo htmlspecialchars($options['social_networks']['facebook']); ?>" style="width: 80%"/>
                        <br/><br/>
                        <label for="asd"><strong><?php _e("RSS", "cultural"); ?></strong></label><br/>
                        <input type="text" id="asd" class="text" name="theme_options[rss]" value="<?php echo htmlspecialchars($options['rss']); ?>" style="width: 80%"/>
                        <br/><br/>
                    </div>

                    <?php ///// Edite daqui pra cima ////  ?>

                </div>

                <p class="textright clear prepend-top">
                    <input type="submit" class="button-primary" value="<?php _e('Save Changes', 'cultural'); ?>" />
                </p>
            </form>
         </div>
        <?php
    }
}
MapasCulturaisConfiguration::init();