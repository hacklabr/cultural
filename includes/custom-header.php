<?php

add_action( 'after_setup_theme', 'SLUG_custom_header_setup' );

function SLUG_custom_header_setup() {

    define('HEADER_TEXTCOLOR', '000000');
    define('HEADER_IMAGE_WIDTH', 980); 
    define('HEADER_IMAGE_HEIGHT', 176);

    add_theme_support(
        'custom-header', 
        array('wp-head-callback' => 'SLUG_custom_header', 'admin-head-callback' => 'SLUG_admin_custom_header')
    );

    register_default_headers( array(
        'Mundo' => array(
            'url' => '%s/img/headers/image001.jpg',
            'thumbnail_url' => '%s/img/headers/image001-thumbnail.jpg',
        ),
        'Árvores' => array(
            'url' => '%s/img/headers/image002.jpg',
            'thumbnail_url' => '%s/img/headers/image002-thumbnail.jpg',
            'description' => 'barco'
        ),
        'Caminho' => array(
            'url' => '%s/img/headers/image003.jpg',
            'thumbnail_url' => '%s/img/headers/image003-thumbnail.jpg',
        ),
    ) );

}

if (!function_exists('SLUG_custom_header')) :

    function SLUG_custom_header() {
        ?><style type="text/css">
            #branding {
                background: url(<?php header_image(); ?>);
            }
                
            #branding, #branding a, #branding a:hover {
                color: #<?php header_textcolor(); ?> !important;
            }
            #branding a:hover {
                text-decoration: none; 
            }
            #description { 
                filter: alpha(opacity=60);
                opacity: 0.6;
            }
        
        </style><?php

    }

endif;

if (!function_exists('SLUG_admin_custom_header')) :

    function SLUG_admin_custom_header() {
        ?><style type="text/css">
        
           #headimg {
                padding:55px 10px;
                width: 960px !important;
                height: 66px !important;
                min-height: 66px !important;
            }
        
            #headimg h1 {
                font-size:36px;
                line-height:44px;
                font-weight:normal !important;
                margin: 0px;
                margin: 0 10px;            
            }
        
            #headimg h1 a {
                text-decoration: none !important;
            }
        
            #headimg #desc { 
                font-style: italic; 
                font-size: 16px; 
                margin: 0 10px;
                filter: alpha(opacity=60);
                opacity: 0.6;
            }

        </style><?php
    }

endif;

?>
