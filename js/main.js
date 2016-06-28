jQuery(document).ready(function( $ ) {

    // Navigation tabs
    $( "ul.toggle-bar a" ).click( function() {
        var tab_id = $( this ).attr( "data-tab" );

		if (typeof(tab_id) == 'undefined') return true; 

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
            closeMarkup: '<span class="mfp-close fa fa-close"></span>',
            image:{
                titleSrc: function(item){
                    var cap = $(item.el).parents('figure').find('figcaption').html();
                    return  cap ? cap : '&nbsp;';
                }
            },
            gallery:{
                enabled:true,
                arrowMarkup: '<button type="button" class="mfp-arrow mfp-arrow-%dir%"><span  title="%title%" class="fa fa-arrow-%dir% mfp-prevent-close"></span></button>', // markup of an arrow button
                tPrev: 'Anterior', // title for left button
                tNext: 'Próxima', // title for right button
                tCounter: '%curr% de %total%' // markup of counter
            },
        });
    });

   $("#share-buttons a.facebook").data('href', 'http://www.facebook.com/sharer/sharer.php?u=' + encodeURIComponent(document.location.toString()) );
   $("#share-buttons a.twitter").data('href', 'http://twitter.com/intent/tweet?status=' + encodeURIComponent(document.title + "\n" + document.location.toString()) );
   $("#share-buttons a.gplus").data('href', 'https://plus.google.com/share?url=' + encodeURIComponent(document.location.toString()) );

   $('.js-share').click(function() {
        window.open( $(this).data('href'), 'Compartilhar', 'width=500, height=500');
    });

});
