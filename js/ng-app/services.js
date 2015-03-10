(function(angular){
    var app = angular.module('CulturalTheme');

    app.factory('searchService', ['$rootScope', '$q', '$http', '$log', function($rootScope, $q, $http, $log) {
        var svc = {}, //this, the service
            filtersSkeleton = {
            startDate : moment(),
            endDate : moment().add(60, 'days'),
            linguagens: [],
            classificacoes: []
        };

        svc.data = angular.copy(filtersSkeleton);

        svc.reset = function(){
            svc.data = angular.copy(filtersSkeleton);
        };

        svc.submit = function(){
            var deferred = $q.defer();
            var searchParams = {
                '@select': 'id,singleUrl,name,subTitle,type,shortDescription,terms,classificacaoEtaria,project.name,project.singleUrl,occurrences',
                '@page': 1,
                '@limit': 10,
                '@files': '(header.header,avatar.avatarBig):url',
                '@from': svc.data.startDate.format('YYYY-MM-DD'),
                '@to': svc.data.endDate.format('YYYY-MM-DD')
            };

            if(svc.data.linguagens && svc.data.linguagens.length){
                searchParams['term:linguagem'] = 'IN(' + svc.data.linguagens.sort().toString() + ')';
            }

            if(svc.data.classificacoes && svc.data.classificacoes.length){
                searchParams.classificacaoEtaria = 'IN(' + svc.data.classificacoes.sort().toString() + ')';
            }
            $log.debug('searchParams:', searchParams);
            $http({
                method: 'GET',
                cache: true,
                url: vars.apiUrl + 'event/findByLocation/',
                params: searchParams
            }).success(function(results){
                deferred.resolve(results);
            });

            return deferred.promise;
        };

        return svc;
    }]);

})(window.angular);
