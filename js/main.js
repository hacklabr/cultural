$(function(){
    var $container = $('.js-masonry');
    $container.imagesLoaded( function() {
        $container.masonry();
    });
});
