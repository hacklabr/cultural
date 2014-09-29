jQuery(document).ready(function( $ ) {

    // Navigation tabs
    $( "ul.toggle-bar a" ).click( function() {
        var tab_id = $( this ).attr( "data-tab" );

        $( "ul.toggle-bar li a" ).removeClass( "current" );
        $( ".tab-content" ).removeClass( "current" );

        $( this ).addClass( "current" );
        $( "#"+tab_id ).addClass( "current" );
        return false;
    } );

    var $container = $('.js-masonry');
    $container.imagesLoaded( function() {
        $container.masonry();
    });
});
