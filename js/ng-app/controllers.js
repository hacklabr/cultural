(function(angular){

    var app = angular.module('CulturalTheme');

    app.controller('eventsController', ['$rootScope', '$scope', '$log', '$location', '$timeout', 'searchService', '$sce', function(
                                            $rootScope,   $scope,   $log,   $location,   $timeout,   searchService,   $sce){

        $scope.data = {
            linguagens: vars.linguagens.map(function(el, i){ return {id: i, name: el}; }),
            classificacoes: vars.classificacoes.map(function(el, i){ return {id: i, name: el}; })
        };

        searchService.submit().then(receiveSearch);

        function receiveSearch(events){
            $log.debug('receiveSearch events', events);
            $scope.events = events;
        }

        $scope.updateMasonry = function(){
            var $container = jQuery('.js-masonry');
            // initialize Masonry after all images have loaded
            $container.imagesLoaded(function() {
                $container.masonry('destroy');
                $container.masonry({"columnWidth": ".grid-sizer", "gutter": ".gutter-sizer", "itemSelector": ".event"});
                console.log('eventsController: $scope.updateMasonry() ');
            });
        };

        $scope.toggleListItem = function(list, item){
            console.log($scope.data[list]);
            $scope.data[list].some(function(i){
                if(i === item){
                    i.active = !i.active;
                    console.log(i, item);
                    if(i.active){
                        searchService.data[list].push(i.name);
                    }else{
                        searchService.data[list].some(function(j, jindex){
                            if(j === item.name){
                                searchService.data[list].splice(jindex, 1);
                            }
                        });
                    }
                }
            });
            searchService.submit().then(receiveSearch);
            console.log($scope.data[list]);
        };

    }]);



    app.controller('eventListController', ['$rootScope', '$scope', '$log', '$location', '$timeout', 'searchService', '$sce', function(
                                            $rootScope,   $scope,   $log,   $location,   $timeout,   searchService,   $sce){

        function receiveSearch (events){
            //$scope.events = events;
            for(var event in events){
                events[event].dateFormatted = moment(events[event].programacao_plain_date[0]).format('DD.MM.YYYY');
                events[event].timeFormatted = events[event].programacao.hora[0];
                events[event].excerpt = $sce.trustAsHtml(events[event].excerpt);
            }
            events.sort(function(a,b){
                return moment(a.programacao_plain_date[0]).diff(b.programacao_plain_date[0],'days');
            });
            $scope.events = events;
        }
        $rootScope.$on('searchDataChange', function(){
            searchService.submit().then(receiveSearch);
        });
        var first = false;
        $scope.updateMasonry = function(){
            var $container = jQuery('.results--content');
            if(!first) first = true; else $container.masonry('destroy');
            // initialize Masonry after all images have loaded
            $container.imagesLoaded(function() {
                $container.masonry({
                    "itemSelector": '.masonry-item',
                    "gutter": 0
                });
            });

            $scope.removeFilters = function(){
                $rootScope.$broadcast('removeFilters');
            };

            $scope.isFiltered = function(){
                return searchService.isFiltered();
            };
        };
    }]);

    app.directive('repeatDone', function() {
        return function(scope, element, attrs) {
            if (scope.$last) { // all are rendered
                scope.$eval(attrs.repeatDone);
            }
        };
    });
})(window.angular);
