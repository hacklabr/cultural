/**
 * This file adds some LIVE to the Theme Customizer live preview. To leverage
 * this, set your custom settings to 'postMessage' and then add your handling
 * here. Your javascript should grab settings from customizer controls, and
 * then make any necessary changes to the page using jQuery.
 */
( function( $ ) {

    wp.customize( 'highlight_color', function( value ) {
        value.bind( function( newval ) {
            $('a:hover,a:focus,a:active,.toggle-bar a:hover,.toggle-bar a.current,.area-title a:hover,.entry__content h1,.comment-content h1,.entry__content h2,.comment-content h2,.entry__content h3,.comment-content h3,.entry__content h4,.comment-content h4,.entry__content h5,.comment-content h5,.entry__content h6,.comment-content h6').css('color', newval );
            $('.menu .current-menu-item > a,.menu .current-page-ancestor > a,.menu .current-menu-ancestor > a,.menu--main a:hover,.entry__categories a,.page-header').css('background-color', newval );
        } );
    } );

    wp.customize( 'title_type', function( value ) {
        value.bind( function( newval ) {
            $('.entry-title').css('font-family', newval );
        } );
    } );

} )( jQuery );
