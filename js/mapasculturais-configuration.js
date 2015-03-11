(function($){
    var MC = {};
    MC.apiURL = 'http://localhost/api';

    MC.createMultiselect = function(selector, url){
        $.getJSON(url, function(data){
             $.each(data, function(key, value){
                var option = $('<option value="' + value + '">' + value + '</option>');
                $(selector).append(option);
             });
             $(selector).attr('multiple', true);
             //$(selector).multiselect();
        });
    };

    $(function(){
        alert('teste');

//        console.log(vars);
//        console.log('generalFilters', vars.generalFilters);
//        console.log('categoryFilters', vars.categoryFilters);

        //MC.createMultiselect('#linguagens', MC.apiURL + '/term/list/linguagem');

//        $('.js-entity input:checked').each(function(){
//            $(this).parent().find('.js-entity-data').attr('name', $(this))
//        });
    });
})(jQuery);

