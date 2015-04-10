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

    $(function(){
      var mySwiper = $('.js-swiper').swiper({
            loop: true,
            autoplay: 8000,
            pagination : '.swiper__pagination',
            paginationClickable : true,
        });
    });

    // inicializa a galeria

    $('.gallery').each(function(){
        $(this).magnificPopup({
            delegate: 'a', // child items selector, by clicking on it popup will open
            type: 'image',
            closeMarkup: '<span class="mfp-close icon icon-close"></span>',
            image:{
                titleSrc: function(item){
                    var cap = $(item.el).parents('figure').find('figcaption').html();
                    return  cap ? cap : '&nbsp;';
                }
            },
            gallery:{
                enabled:true,
                arrowMarkup: '<button title="%title%" type="button" class="mfp-arrow mfp-arrow-%dir%"><span class="icon icon-arrow-%dir% mfp-prevent-close"></span></button>', // markup of an arrow button
                tPrev: 'Anterior', // title for left button
                tNext: 'Próxima', // title for right button
                tCounter: '%curr% de %total%' // markup of counter
            },
        });
    });



});
