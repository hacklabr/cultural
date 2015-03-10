
(function(angular){

    var app = angular.module('CulturalTheme', [
//        'ui.router',
        'daterangepicker'
    ]);

    app.config(['$logProvider', function($logProvider) {
        $logProvider.debugEnabled(false);
//        $urlRouterProvider.otherwise("/");
//        $stateProvider
//            .state('home', {
//                url: '/',
//                views : {
//                    'filter-bar': {
//                        templateUrl: Directory.url+'/parts/programacao/filter-bar.html',
//                        controller : 'filterController'
//                    },
//                    'events': {
//                        templateUrl: Directory.url+'/parts/programacao/results.html',
//                        controller : 'eventListController'
//                    }
//                }
//            });
    }]);

    angular.element(document).ready(function(){
        angular.bootstrap(document, ['CulturalTheme']);
    });

    app.run(['$rootScope', '$location', '$log', function($rootScope, $location, $log){
        $rootScope.url = Directory.url;
        $rootScope.site = Directory.site;
        $rootScope.openSingle = function(event){
            location.href=Directory.site+'/evento/'+event.name;
        };

        $rootScope.isMobile = isMobile;
        //pra testar mobile no desktop:
        if($location.$$absUrl.indexOf('mobile')!== -1){
            $rootScope.isMobile.any = function(){return true};
        }
    }]);


})(window.angular);
