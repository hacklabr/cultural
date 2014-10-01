<?php
/**
 * Contains methods for customizing the theme customization screen.
 *
 * @link http://codex.wordpress.org/Theme_Customization_AP
 */

class Cultural_Customize {
   /**
    * This hooks into 'customize_register' (available as of WP 3.4) and allows
    * you to add new sections and controls to the Theme Customize screen.
    *
    * Note: To enable instant preview, we have to actually write a bit of custom
    * javascript. See live_preview() for more.
    *
    * @see add_action('customize_register',$func)
    * @param \WP_Customize_Manager $wp_customize
    * @link http://ottopress.com/2012/how-to-leverage-the-theme-customizer-in-your-own-themes
    */
   public static function register ( $wp_customize ) {

      //2. Register new settings to the WP database...
      $wp_customize->add_setting( 'highlight_color', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
         array(
            'default' => '#c0392b', //Default setting/value to save
            'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
            'capability' => 'edit_theme_options', //Optional. Special permissions for accessing this setting.
            'transport' => 'postMessage', //What triggers a refresh of the setting? 'refresh' or 'postMessage' (instant)?
         )
      );

      //3. Finally, we define the control itself (which links a setting to a section and renders the HTML controls)...
      $wp_customize->add_control( new WP_Customize_Color_Control( //Instantiate the color control class
         $wp_customize, //Pass the $wp_customize object (required)
         'mytheme_highlight_color', //Set a unique ID for the control
         array(
            'label' => __( 'Highlight Color', 'cultural' ), //Admin-visible name of the control
            'section' => 'colors', //ID of the section this control should render in (can be one of yours, or a WordPress default section)
            'settings' => 'highlight_color', //Which setting to load and manipulate (serialized is okay)
            'priority' => 10, //Determines the order this control appears in for the specified section
         )
      ) );

      $wp_customize->add_setting( 'site_logo',
          array(
              'default' => '',
          )
      );
      $wp_customize->add_control( new WP_Customize_Image_Control(
          $wp_customize,
          'upload_site_logo',
          array(
              'label' => __( 'Site Logo', 'cultural' ),
              'section' => 'title_tagline',
              'settings' => 'site_logo',
              'priority' => 10,
          ) )
      );

    $wp_customize->add_section( 'cultural_typography', array(
        'title'          => __( 'Typography', 'cultural' ),
        'priority'       => 35,
    ) );

    $wp_customize->add_setting( 'title_type', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
       array(
          'default' => 'aleo', //Default setting/value to save
          'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
          'transport' => 'postMessage'
       )
    );

    $wp_customize->add_control( 'title_type', array(
        'label'   => __( 'Title Type', 'cultural' ),
        'section' => 'cultural_typography',
        'type'    => 'select',
        'choices'    => array(
            'serif' => 'Serif',
            'sansserif' => 'Sans Serif',
            'slab' => 'Slab',
    )
    ));

    $wp_customize->add_setting( 'body_type', //No need to use a SERIALIZED name, as `theme_mod` settings already live under one db record
       array(
          'default' => 'sansserif', //Default setting/value to save
          'type' => 'theme_mod', //Is this an 'option' or a 'theme_mod'?
          'transport' => 'postMessage'
       )
    );

    $wp_customize->add_control( 'body_type', array(
        'label'   => __( 'Body Type', 'cultural' ),
        'section' => 'cultural_typography',
        'type'    => 'select',
        'choices'    => array(
            'sansserif' => 'Sans Serif',
            'serif' => 'Serif',
    )
    ));

      //4. We can also change built-in settings by modifying properties. For instance, let's make some stuff use live preview JS...
      $wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
      $wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
      $wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';
      $wp_customize->get_setting( 'background_color' )->transport = 'postMessage';
   }

    /**
    * This will output the custom WordPress settings to the live theme's WP head.
    *
    * Used by hook: 'wp_head'
    *
    * @see add_action('wp_head',$func
    */
    public static function enqueue_fonts() {

        switch ( get_theme_mod( 'body_type' ) ) {
            case 'sansserif':
                wp_enqueue_style( 'opensans', '//brick.a.ssl.fastly.net/Open+Sans:400,700,400i,700i' );
            break;
            case 'serif':
                wp_enqueue_style( 'crimson', '//brick.a.ssl.fastly.net/Crimson:400,700,400i,700i' );
            break;
        }

        switch ( get_theme_mod( 'title_type' ) ) {
            case 'serif':
                wp_enqueue_style( 'crimson', '//brick.a.ssl.fastly.net/Crimson:400,700,400i,700i' );
            break;
            case 'sansserif':
                wp_enqueue_style( 'montserrat', '//brick.a.ssl.fastly.net/Montserrat:400,700' );
            break;
            case 'slab':
                wp_enqueue_style( 'bitter', '//brick.a.ssl.fastly.net/Bitter:400,700,400i,700i' );
            break;
        }

    }

   /**
    * This will output the custom WordPress settings to the live theme's WP head.
    *
    * Used by hook: 'wp_head'
    *
    * @see add_action('wp_head',$func
    */
   public static function header_output() {

   $fft = get_theme_mod( 'title_type' );

    switch ( get_theme_mod( 'title_type' ) ) {
        case 'serif':
            $fft = 'Crimson, serif';
        break;
        case 'sansserif':
            $fft = 'Montserrat, sans-serif';
        break;
        case 'slab':
            $fft = 'Bitter, serif';
        break;
    }

    $ffb = get_theme_mod( 'body_type' );

    switch ( get_theme_mod( 'body_type' ) ) {
        case 'sansserif':
            $ffb = 'Open Sans, sans-serif';
        break;
        case 'serif':
            $ffb = 'Crimson, serif';
        break;
    }
    ?>

    <!--Customizer CSS-->
    <style type="text/css">
        .site-title,.access,.entry-title,.button,h1,h2,h3,h4,h5,h6 { font-family: <?php echo $fft ?>; }
        body { font-family: <?php echo $ffb ?>; }
    </style>
    <!--/Customizer CSS-->
      <?php
   }

   /**
    * This outputs the javascript needed to automate the live settings preview.
    * Also keep in mind that this function isn't necessary unless your settings
    * are using 'transport'=>'postMessage' instead of the default 'transport'
    * => 'refresh'
    *
    * Used by hook: 'customize_preview_init'
    *
    * @see add_action('customize_preview_init',$func
    */
   public static function live_preview() {
      wp_enqueue_script(
           'cultural-customizer', // Give the script a unique ID
           get_template_directory_uri() . '/js/customizer.js', // Define the path to the JS file
           array(  'jquery', 'customize-preview' ), // Define dependencies
           '', // Define a version (optional)
           true // Specify whether to put in footer (leave this true)
      );
   }
}

// Setup the Theme Customizer settings and controls...
add_action( 'customize_register' , array( 'Cultural_Customize' , 'register' ) );

// Output custom CSS to live site
add_action( 'wp_head' , array( 'Cultural_Customize' , 'header_output' ) );

// Enqueue live preview javascript in Theme Customizer admin screen
add_action( 'customize_preview_init' , array( 'Cultural_Customize' , 'live_preview' ) );

add_action( 'wp_enqueue_scripts', array( 'Cultural_Customize' , 'enqueue_fonts' ) );
