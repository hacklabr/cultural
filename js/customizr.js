/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */
( function( $ ) {

    wp.customize( 'highlight_color', function( value ) {
        value.bind( function( newval ) {
            $('.menu--main').css('background-color', newval );
            $('.box--about').css('background-color', newval ).css('border-color', newval );
            $('.box--news').css('border-color', newval );
            $('.entry-title a').css('color', newval );
            $('.entry-format').css('background-color', newval );
        } );
    } );

} )( jQuery );
