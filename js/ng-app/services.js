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
                '@select': 'id,singleUrl,name,subTitle,type,shortDescription,terms,classificacaoEtaria,owner.name,owner.singleUrl,project.name,project.singleUrl,occurrences',
//                '@page': 1,
//                '@limit': 10,
                '@files': '(header.header,avatar.avatarBig):url',
                '@from': svc.data.startDate.format('YYYY-MM-DD'),
                '@to': svc.data.endDate.format('YYYY-MM-DD')
            };

            var spaces, projects, agents;

            console.log(vars.categoryFilters.linguagens);

            // LINGUAGENS
            // se tem filtro selecionado na busca
            if(svc.data.linguagens && svc.data.linguagens.length){
                searchParams['term:linguagem'] = 'IN(' + svc.data.linguagens.sort().toString() + ')';

            // ou se está numa categoria tem filtro configurado para a mesma
            }else if(vars.categoryFilters && vars.categoryFilters.linguagens && Object.keys(vars.categoryFilters.linguagens).length){
                searchParams['term:linguagem'] = 'IN(' + Object.keys(vars.categoryFilters.linguagens).sort().toString() + ')';
            }

            // CLASSIFICAÇÃO
            // se tem filtro selecionado na busca
            if(svc.data.classificacoes && svc.data.classificacoes.length){
                searchParams.classificacaoEtaria = 'IN(' + svc.data.classificacoes.sort().toString() + ')';

            // ou se está numa categoria tem filtro configurado para a mesma
            }else if(vars.categoryFilters && vars.categoryFilters.classificacaoEtaria && Object.keys(vars.categoryFilters.classificacaoEtaria).length){
                searchParams.classificacaoEtaria = 'IN(' + Object.keys(vars.categoryFilters.classificacaoEtaria).sort().toString() + ')';
            }

            if(vars.categoryFilters.space){
                spaces = Object.keys(vars.categoryFilters.space).map(function(e){ return '@Space:' + e; });
                searchParams['space'] = "IN(" + spaces + ")";
            }else if(vars.generalFilters.space){
                spaces = Object.keys(vars.generalFilters.space).map(function(e){ return '@Space:' + e; });
                searchParams['space'] = "IN(" + spaces + ")";
            }

            if(vars.categoryFilters.agent){
                agents = Object.keys(vars.categoryFilters.agent).map(function(e){ return '@Agent:' + e; });
                searchParams['owner'] = "IN(" + agents + ")";
            }else if(vars.generalFilters.agent){
                agents = Object.keys(vars.generalFilters.agent).map(function(e){ return '@Agent:' + e; });
                searchParams['owner'] = "IN(" + agents + ")";
            }

            if(vars.categoryFilters.project){
                projects = Object.keys(vars.categoryFilters.project).map(function(e){ return '@Project:' + e; });
                searchParams['project'] = "IN(" + projects + ")";
            }else if(vars.generalFilters.project){
                projects = Object.keys(vars.generalFilters.project).map(function(e){ return '@Project:' + e; });
                searchParams['project'] = "IN(" + projects + ")";
            }

            // ESPAÇOS
            // se tem filtro selecionado na busca
            if(svc.data.classificacoes && svc.data.classificacoes.length){
                searchParams.classificacaoEtaria = 'IN(' + svc.data.classificacoes.sort().toString() + ')';

            // ou se está numa categoria tem filtro configurado para a mesma
            }else if(vars.categoryFilters && vars.categoryFilters.classificacaoEtaria && Object.keys(vars.categoryFilters.classificacaoEtaria).length){
                searchParams.classificacaoEtaria = 'IN(' + Object.keys(vars.categoryFilters.classificacaoEtaria).sort().toString() + ')';
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
