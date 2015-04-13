<?php

class MCEntityRelationMetabox {

    protected static $metabox_config = array(
        'mc-entity-relation', // slug do metabox
        'Linkar com a entidade da plataforma ' . MAPASCULTURAIS_NAME , // título do metabox
        array('post', 'page'), // array('post','page','etc'), // post types
        'normal' // onde colocar o metabox
    );

    static function init() {
        add_action('add_meta_boxes', array(__CLASS__, 'addMetaBox'));
        add_action('save_post', array(__CLASS__, 'savePost'));
    }

    static function addMetaBox() {
        if(!is_array(self::$metabox_config[2])){
            self::$metabox_config[2] = array(self::$metabox_config[2]);
        }

        foreach(self::$metabox_config[2] as $post_type){
            add_meta_box(
                self::$metabox_config[0],
                self::$metabox_config[1],
                array(__CLASS__, 'metabox'),
                $post_type,
                self::$metabox_config[3]

            );
        }
    }


    static function filterValue($meta_key, $value){
        return $value;
    }

    static function metabox(){
        global $post;

        wp_nonce_field( 'save_'.__CLASS__, __CLASS__.'_noncename' );

        $metadata = get_metadata('post', $post->ID, 'mc-entity-relation', true);

        ?>
        <p>
            Linkar a url de uma entidade da plataforma <?php echo MAPASCULTURAIS_NAME ?> a um post,
            adiciona ao final do post um link de "saiba mais" que leva à página da entidade na plataforma e
            substitui a url da entidade pela url deste post em páginas do site como a busca de evento.
        </p>
        <input type="text" name="<?php echo __CLASS__ ?>[mc-entity-relation]" value="<?php echo $metadata; ?>" style="width:100%" placeholder="Informe aqui a url incluindo o http://"/> <?php
    }

    static function savePost($post_id) {
        // verify if this is an auto save routine.
        // If it is our form has not been submitted, so we dont want to do anything
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE)
            return;

        // verify this came from the our screen and with proper authorization,
        // because save_post can be triggered at other times

        if (!wp_verify_nonce(isset($_POST[__CLASS__.'_noncename']) ? $_POST[__CLASS__.'_noncename'] : '', 'save_'.__CLASS__))
            return;


        // Check permissions
        if ('page' == $_POST['post_type']) {
            if (!current_user_can('edit_page', $post_id))
                return;
        }
        else {
            if (!current_user_can('edit_post', $post_id))
                return;
        }

        // OK, we're authenticated: we need to find and save the data
        if(isset($_POST[__CLASS__])){
            foreach($_POST[__CLASS__] as $meta_key => $value)
                update_post_meta($post_id, $meta_key, self::filterValue($meta_key, $value));
        }
    }


}


MCEntityRelationMetabox::init();