<?php
Class openGraph {
    public $properties = array();

    public function __construct() {
        add_action('wp_head', array( $this, 'addMetaTags' ) );
    }

    public function addMetaTags() {
        global $post, $wp, $properties;
        /*
         * og: inicial
         */
        $this->properties = array(
            'og:type' => 'article',
            'og:url' => home_url( $wp->request ),
            'og:site_name' => get_bloginfo( 'name' ),
            'og:locale' => 'pt_BR'
        );

        /*
         *
         * og:title
         * Título do "post" que ira aparecer no PopUP do facebook e na Timeline
         *
         */
        if ( is_single() ) :
            $this->properties['og:title'] = get_the_title();
        elseif ( is_category() ) :
            $this->properties['og:title'] = single_cat_title('', false);
        elseif ( is_tag() ) :
            $this->properties['og:title'] = single_tag_title('', false);
        else :
            $this->properties['og:title'] = get_bloginfo( 'name' );
        endif;

        /*
         *
         * og:image
         * Thumbnail do "post" que ira aparecer no PopUP do facebook e na Timeline
         *
         */
        if ( is_single() && has_post_thumbnail( $post->ID ) ) :
            $image = wp_get_attachment_image_src( get_post_thumbnail_id($post->ID), 'full' );
            if ( !empty( $image[0] ) ) :
                $this->properties['og:image'] = $image[0];
            else :
                $this->properties['og:image'] = get_stylesheet_directory_uri() . '/img/logo-facebook.png';
            endif;
        endif;

        /*
         *
         * og:description
         * Descrição do "post" se tiver em um ou do site
         *
         */
        if ( is_single() && !empty(get_the_excerpt()) ) :
            $this->properties['og:description'] = get_the_excerpt();
        else :
            $this->properties['og:description'] = get_bloginfo( 'description' );
        endif;

        /*
         * Faz um loop de todos os ogs existentes e adiciona ao head do HTML
         */
        foreach ( $this->properties as $og => $og_value ) :
            if ( $og === 'og:url' or $og === 'og:image') :
                echo "<meta name=\"{$og}\" content=\"{$og_value}\" />\n" ;
            else :
                $og_value = utils::htmlentities( $og_value );
                echo "<meta name=\"{$og}\" content=\"{$og_value}\" />\n" ;
            endif;
        endforeach;
    }
}
$openGraph = new openGraph();
