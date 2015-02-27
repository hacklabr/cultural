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

            $http({method: 'GET', cache: true, url: vars.apiUrl + 'event/findByLocation/', params: {
                    '@select': 'id,singleUrl,name,subTitle,type,shortDescription,terms,classificacaoEtaria,project.name,project.singleUrl,occurrences',
                    '@page': 1,
                    '@limit': 10,
                    '@files': '(header.header,avatar.avatarBig):url',
                    '@from': startDate,
                    '@to': endDate
                }}).success(function(results){
                    deferred.resolve(results);
                });

            return deferred.promise;
        };

        return svc;
    }]);

})(window.angular);
