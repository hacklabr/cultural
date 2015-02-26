(function(angular){

    var app = angular.module('CulturalTheme');

    app.controller('eventsController', ['$rootScope', '$scope', '$log', '$location', '$timeout', 'searchService', '$sce', function(
                                            $rootScope,   $scope,   $log,   $location,   $timeout,   searchService,   $sce){
        $scope.linguagens = vars.linguagens;
        $scope.classificacoes = vars.classificacoes;

        searchService.submit().then(receiveSearch);

        function receiveSearch(events){
            console.log(events);
            $scope.events = events;
        }

        var first = false;
        $scope.updateMasonry = function(){
            var $container = jQuery('.js-masonry');
            if(!first) first = true; else $container.masonry('destroy');
            // initialize Masonry after all images have loaded
            $container.imagesLoaded(function() {
                console.log('eventsController: $scope.updateMasonry() ');
                $container.masonry({
                    //"itemSelector": '.event-container',
                    "gutter": 0
                });
            });
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
