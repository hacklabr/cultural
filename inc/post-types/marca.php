<?php

// Hook into the 'init' action
add_action('init', function(){
    add_image_size('regua', 120, 60, false);

    $labels = array(
        'name' => _x('Régua de Marcas', 'Post Type General Name', 'cultural'),
        'singular_name' => _x('Marca', 'Post Type Singular Name', 'cultural'),
        'menu_name' => __('Régua de Marcas', 'cultural'),
        'name_admin_bar' => __('Régua de Marcas', 'cultural'),
        'parent_item_colon' => __('', 'cultural'),
        'all_items' => __('Todas as marcas', 'cultural'),
        'add_new_item' => __('Adicionar nova marca', 'cultural'),
        'add_new' => __('Adicionar nova', 'cultural'),
        'new_item' => __('Nova marca', 'cultural'),
        'edit_item' => __('Editar nova marca', 'cultural'),
        'update_item' => __('Atualizar marca', 'cultural'),
        'view_item' => __('Ver marca', 'cultural'),
        'search_items' => __('Procurar marca', 'cultural'),
        'not_found' => __('Não encontrado', 'cultural'),
        'not_found_in_trash' => __('Não encontrado na lixeira', 'cultural'),
    );
    $args = array(
        'label' => __('marca', 'cultural'),
        'description' => __('Marcas que aparecem no rodapé do site', 'cultural'),
        'labels' => $labels,
        'supports' => array('title', 'thumbnail',),
        'taxonomies' => array(),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'menu_icon' => 'dashicons-format-image',
        'show_in_admin_bar' => false,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => false,
        'exclude_from_search' => true,
        'publicly_queryable' => true,
        'rewrite' => false,
        'capability_type' => 'post',
    );
    register_post_type('marca', $args);

    register_nav_menu('regua', 'Régua de Marcas');
    // add menu

    class Regua_Menu_Walker extends Walker_Nav_Menu{

        public function start_lvl(&$output, $depth = 0, $args = array()) { }

        public function end_lvl(&$output, $depth = 0, $args = array()) { }

        public function start_el(&$output, $item, $depth = 0, $args = array(), $id = 0) {
            if($depth === 0){
                if($item->url && trim($item->url) != "#"){
                    $output .= "<li><a href=\"{$item->url}\"><h3>{$item->title}</h3></a>";
                    
                }else{
                    $output .= "<li><h3>{$item->title}</h3>";
                }
            }else{
                $attachment_id = get_post_thumbnail_id($item->object_id);
                $src = wp_get_attachment_image_src($attachment_id, 'regua');

                $marca_url = get_post_meta($item->object_id, 'link', true);

                $item_title = esc_attr($item->title);

                if(isset($src[0])){
                    $output .= "<a href=\"{$marca_url}\" title=\"{$item_title}\"><img src=\"{$src[0]}\" width=\"{$src[1]}\" height=\"{$src[2]}\" alt=\"{$item_title}\"></a> ";
                }else{
                    $output .= "<a href=\"{$marca_url}\" title=\"{$item_title}\">{$item->title}</a> ";
                }
            }

        }

        public function end_el(&$output, $item, $depth = 0, $args = array()) {
            if($depth === 0){
                $output .= "</li>";
            }
        }
    }

}, 0);

