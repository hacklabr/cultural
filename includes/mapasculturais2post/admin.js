(function ($) {

    $(function () {
        var imageImportationSpinnerCount = 0;

        $('body').on('change', '.js-image-checkbox', function(){
            var $this = $(this);
            if($this.is(':checked')){
                $this.parent().addClass('selected');
            }else{
                $this.parent().removeClass('selected');
            }
        });

        function importImagesFeedback(){
            $('#mc-import-image--feedback').fadeIn('fast').delay(5000).fadeOut('fast');
        }

        function importImages(selector){
            var $checkboxes = $(selector);
            imageImportationSpinnerCount += $checkboxes.length;

            $checkboxes.each(function(i){
                var imageUrl = $(this).val();

                var data = {
                    image_url: imageUrl,
                    action: 'mapas_get_event_image',
                    post_id: $('#post_ID').val()
                };

                setTimeout(function(){
                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: data,
                        complete: function (request, status) {
                            imageImportationSpinnerCount--;

                            if(imageImportationSpinnerCount === 0){
                                importImagesFeedback();
                            }
                        }
                    });

                }, i*30);
            });
        }

        setInterval(function(){
            if(imageImportationSpinnerCount > 0){
                $('#mc-import-image--search-spinner').css('display', 'inline-block');
            }else{
                $('#mc-import-image--search-spinner').css('display', 'none');
            }
        },10);

        $('body').on('click', '#mc-import-image-all', function(){
            importImages('.mc-image input');
        });

        $('body').on('click', '#mc-import-image-selected', function(){
            importImages('.mc-image input:checked');
        });

        $('body').on('click', '#mc-import-image--search-button', function () {
            var  parsed;

            function parseUrl(url) {
                var id, entity, m, entityTypeName;

                m = url.match(/https?:\/\/[^\/]*\/(agent(e|es)?|proje(tos?|ct)|espac(os?|e)|event(os?)?)(\/single)?\/([0-9]+)\/?/);
                if (m) {
                    id = parseInt(m.slice(-1));
                    if (/agent(e|es)?/.test(m[1])) {
                        entity = 'agent';
                        entityTypeName = 'Agente';

                    } else if (/espac(os?|e)/.test(m[1])) {
                        entity = 'space';
                        entityTypeName = 'Espaço';

                    } else if (/proje(tos?|ct)/.test(m[1])) {
                        entity = 'project';
                        entityTypeName = 'Projeto';

                    } else if (/event(os?)?/.test(m[1])) {
                        entity = 'event';
                        entityTypeName = 'Evento';
                    }

                    return {entity: entity, id: id, entityTypeName: entityTypeName};
                } else {
                    return null;
                }
            }

            parsed = parseUrl($('#mc-import-image--search-url').val());

            if (parsed) {
                var $spinner = $('#mc-import-image--search-spinner');
                var $container = $('#mc-import-image--result-container');

                $container.html('');
                $spinner.css('display', 'inline-block');

                $.get(mc.apiUrl + parsed.entity + '/findOne', {
                    '@select': 'id,name,files',
                    'id': 'EQ(' + parsed.id + ')'
                }, function(r){
                    $spinner.css('display', 'none');
                    var template = document.getElementById('mc-import-template').innerHTML;
                    var entity = {
                        id: r.id,
                        name: r.name,
                        typeName: parsed.entityTypeName,
                        images: []
                    };

                    if(r.files.avatar){
                        entity.images.push({
                            group: 'avatar',
                            url: r.files.avatar.url,
                            thumbUrl: r.files.avatar.files.avatarSmall.url
                        });
                    }

                    if(r.files.gallery){
                        r.files.gallery.forEach(function(f){
                            entity.images.push({
                                group: 'gallery',
                                url: f.url,
                                thumbUrl: f.files.galleryThumb.url
                            });
                        });
                    }


                    if(r){
                        $container.html(Mustache.render(template, entity));
                    }else{
                        alert('Não encontrado');
                    }
                });
            }
            //@select=id,name,files & id = EQ(237) & @files = (avatar, avatar.avatarSmall, gallery, gallery.galleryThumb).url
        });

        $('#mapas_button').click(function () {

            var mapas_request;
            var mapas_check_url;
            var mapas_get_images;

            $('#mapas_fields').hide();
            $('#mapas_loading').show();

            $.ajax({
                type: 'POST',
                url: ajaxurl,
                //dataType: 'json',
                data: {
                    event_url: $('#mapas_url_evento').val(),
                    action: 'mapas_check_event_url'
                },
                complete: function (request) {
                    if (request.responseText != 'ok') {
                        alert('URL inválida');
                        return;
                    }

                    $.ajax({
                        type: 'POST',
                        url: ajaxurl,
                        data: {
                            event_url: $('#mapas_url_evento').val(),
                            action: 'mapas_get_event_info'
                        },
                        complete: function (request, status) {


                            if ('success' == status) {

                                if (confirm('Você tem certeza que deseja atualizar as informações a partir do SP Cultura? Isso irá apagar qualquer alterção manual que você tenha feito.')) {
                                    var event_info = request.responseJSON;

                                    $('#mapas_titutlo').val(event_info.title);
                                    $('#mapas_descri_curta').val(event_info.short_description);
                                    $('#mapas_descri_completa').val(event_info.description);
                                    $('#mapas_data').val(event_info.date);
                                    $('#mapas_hora').val(event_info.time);
                                    $('#mapas_local').val(event_info.place);
                                    $('#mapas_local_id').val(event_info.place_id);

                                    // Get Images
                                    $('#mapas_loading').hide();
                                    $('#mapas_loading_images').show();

                                    if (event_info.photos.length > 0) {
                                        for (var foto = 0; foto < event_info.photos.length; foto++) {
                                            //console.log(event_info.photos[foto]);

                                            $.ajax({
                                                type: 'POST',
                                                url: ajaxurl,
                                                data: {
                                                    image_url: event_info.photos[foto],
                                                    action: 'mapas_get_event_image',
                                                    post_id: $('#post_ID').val()
                                                },
                                                complete: function (request, status) {

                                                    if (foto == event_info.photos.length) {
                                                        $('#mapas_loading').hide();
                                                        $('#mapas_loading_images').hide();
                                                        $('#mapas_fields').show();
                                                    }

                                                }
                                            });

                                        }
                                    } else {
                                        $('#mapas_loading').hide();
                                        $('#mapas_loading_images').hide();
                                        $('#mapas_fields').show();
                                    }

                                }

                            }

                        }
                    });

                }
            });

            $('#mapas_loading').hide();
            $('#mapas_loading_images').hide();
            $('#mapas_fields').show();

        });

    });
})(jQuery);

