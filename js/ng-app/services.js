(function(angular){
    var app = angular.module('CulturalTheme');

    app.factory('searchService', ['$rootScope', '$q', '$http', '$log', function($rootScope, $q, $http, $log) {
        var svc = {}, //this, the service
            filtersSkeleton = {
            startDate : moment(),
            endDate : moment().add(60, 'days'),
        };

        svc.data = angular.copy(filtersSkeleton);

        svc.reset = function(){
            svc.data = angular.copy(filtersSkeleton);
        };

        svc.submit = function(){
            var deferred = $q.defer();
            var startDate = svc.data.startDate.format('YYYY-MM-DD');
            var endDate = svc.data.endDate.format('YYYY-MM-DD');
            var url = 'http://spcultura.prefeitura.sp.gov.br/api/event/findByLocation/?&@from=2015-02-26&@to=2015-03-26&@select=id,singleUrl,name,type,shortDescription,terms,classificacaoEtaria,project.name,project.singleUrl,occurrences&@files=(avatar.avatarMedium):url&@page=1&@limit=10&@order=name%20ASC';

            $http({method: 'GET', cache: true, url: url})
                .success(function(results){
                    deferred.resolve(results);
                });

            return deferred.promise;
        };

        return svc;
    }]);

})(window.angular);
