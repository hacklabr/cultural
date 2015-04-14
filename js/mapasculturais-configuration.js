(function ($) {
    $(function () {
        var $entityContainer,
            template = document.getElementById('template-entity') ? document.getElementById('template-entity').innerHTML : null;


        // category filter

        if($('#category-use-events').length){
            if($('#category-use-events').is(':checked')){
                $('#category-events-filter').show();
            }else{
                $('#category-events-filter').hide();
            }

            $('#category-use-events').change(function(){
                if($('#category-use-events').is(':checked')){
                    $('#category-events-filter').show();
                }else{
                    $('#category-events-filter').hide();
                }
            });
        }

        // create tabs
        $('#mapasculturais-config-tabs').tabs();

        // reder list of selected item
        for (var entityName in selectedEntities) {
            $entityContainer = $('#' + entityName + '-container');
            for(var id in selectedEntities[entityName]){
                console.log(entityName, id, selectedEntities[entityName]);
                var e = selectedEntities[entityName][id];
                e.json = JSON.stringify(e);
                var $e = Mustache.render(template, e);
                $entityContainer.append($e);
            }
        }

        // remove entity from list of selected entities
        $('body').on('click', '.js-entity-container .js-remove', function (e) {
            var $entity = $(this).parents('.js-entity-list-item');
            var entityName = $entity.find('.js-name').html();
            if (confirm("Deseja remover \"" + entityName + "\" da lista?")) {
                $entity.remove();
            }
        });


        // add a selected entity to list of selected entity
        $('body').on('click', '.js-add-entity-to-list', function (e) {
            var data = $(this).data('item');
            data.json = JSON.stringify(data);
            var entity = data.entity;
            var $container = $('#' + entity + '-container');

            var html = Mustache.render(template, data);

            $container.append(html);
        });

        // search entity input
        $('.entity-autocomplete').each(function () {
            var $this = $(this);

            $this.autocomplete({
                delay: 500,
                minLength: 1,
                source: function (request, response) {
                    var term = request.term.replace(/ /g, '%');
                    var QUERY = {
                        '@keyword': term,
                        '@order': 'name ASC',
                        '@select': 'id,type,name,shortDescription,terms',
                        '@files': '(avatar.avatarSmall):url'
                    };

                    if ($this.data('entity') === 'space') {
                        QUERY['@select'] += ',endereco';
                    }

                    if(selectedEntities[$this.data('entity') + 'Ids'] && selectedEntities[$this.data('entity') + 'Ids'].length > 0){
                        QUERY['id'] = 'IN(' + selectedEntities[$this.data('entity') + 'Ids'] + ')';
                    }

                    $.get(vars.apiUrl + $this.data('entity') + '/find', QUERY, function (data) {
                        response(data);
                    });
                },
                focus: function (event, ui) {
                    // prevent autocomplete from updating the textbox
                    event.preventDefault();
                },
                select: function (event, ui) {
                    // prevent autocomplete from updating the textbox
                    event.preventDefault();
                }
            }).data("ui-autocomplete")._renderItem = function (ul, item) {
                var template = document.getElementById('template-autocomplete').innerHTML;

                var data = {
                    id: item.id,
                    avatarUrl: item['@files:avatar.avatarSmall'] ? item['@files:avatar.avatarSmall'].url : vars.apiUrl + '../assets/img/avatar--' + $this.data('entity') + '.png',
                    name: item.name,
                    type: item.type.name,
                    tags: item.terms.tag ? item.terms.tag.join(', ') : null,
                    areas: item.terms.area ? item.terms.area.join(', ') : null,
                    entity: $this.data('entity')
                };

                var $article = $(Mustache.render(template, data));
                $article.data('item', data);

                return $("<li></li>").append($article).appendTo(ul);
            };

            if(selectedEntities[$this.data('entity') + 'Ids'] && selectedEntities[$this.data('entity') + 'Ids'].length > 0){
                $this.focus(function(){
                    $this.autocomplete('search', '%');
                });
            }
        });
    });
})(jQuery);

