(function(angular){
    var app = angular.module('CulturalTheme');

    app.factory('searchService', ['$rootScope', '$q', '$http', '$log', function($rootScope, $q, $http, $log) {
        var svc = {}, //this, the service
            filtersSkeleton = {
            startDate : moment(),
            endDate : moment().add(15, 'days'),
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
                '@select': 'id,singleUrl,name,subTitle,type,shortDescription,terms,classificacaoEtaria,traducaoLibras,descricaoSonora,owner.name,owner.singleUrl,project.name,project.singleUrl,occurrences.{rule,space.{id,name,singleUrl,shortDescription}}',
//                '@page': 1,
//                '@limit': 10,
                '@files': '(header.header,avatar.avatarBig):url',
                '@from': svc.data.startDate.format('YYYY-MM-DD'),
                '@to': svc.data.endDate.format('YYYY-MM-DD')
            };

            if(svc.data.keyword){
                searchParams['@keyword'] = svc.data.keyword;
            }

            var spaces, projects, agents;
            
            
            // SELOS
            if(vars.categoryFilters && vars.categoryFilters.selos && vars.categoryFilters.selos.length){
                console.log('1');
                searchParams['@seals'] = vars.categoryFilters.selos.sort().toString();
            
            }else if(!vars.generalFilters.empty.selos && vars.generalFilters.selos && vars.generalFilters.selos.length){
                console.log('2');
                searchParams['@seals'] = vars.generalFilters.selos.sort().toString();
            }
            
            // LINGUAGENS
            // se tem filtro selecionado na busca
            if(svc.data.linguagens && svc.data.linguagens.length){
                //console.log('0');
                searchParams['term:linguagem'] = 'IN(' + svc.data.linguagens.sort().toString() + ')';

            // ou se está numa categoria tem filtro configurado para a mesma
            }else {
                if(vars.categoryFilters && vars.categoryFilters.linguagens && vars.categoryFilters.linguagens.length){
                    //console.log('1');
                    searchParams['term:linguagem'] = 'IN(' + vars.categoryFilters.linguagens.sort().toString() + ')';

                }else if(!vars.generalFilters.empty.linguagens && vars.generalFilters.linguagens && vars.generalFilters.linguagens.length){
                    //console.log('2');
                    searchParams['term:linguagem'] = 'IN(' + vars.generalFilters.linguagens.sort().toString() + ')';
                }
            }

            // CLASSIFICAÇÃO
            // se tem filtro selecionado na busca
            if(svc.data.classificacoes && svc.data.classificacoes.length){
                searchParams.classificacaoEtaria = 'IN(' + svc.data.classificacoes.sort().toString() + ')';
            // ou se está numa categoria tem filtro configurado para a mesma
            }else {

                if(vars.categoryFilters && vars.categoryFilters.classificacaoEtaria && vars.categoryFilters.classificacaoEtaria.length){
                    searchParams.classificacaoEtaria = 'IN(' + vars.categoryFilters.classificacaoEtaria.sort().toString() + ')';

                }else if(!vars.generalFilters.empty.classificacaoEtaria && vars.generalFilters.classificacaoEtaria && vars.generalFilters.classificacaoEtaria.length){
                    searchParams.classificacaoEtaria = 'IN(' + vars.generalFilters.classificacaoEtaria.sort().toString() + ')';

                }

            }

            if(vars.categoryFilters && vars.categoryFilters.space){
                spaces = Object.keys(vars.categoryFilters.space).map(function(e){ return '@Space:' + e; });
                searchParams['space'] = "IN(" + spaces + ")";
            }else if(vars.generalFilters.space){
                spaces = Object.keys(vars.generalFilters.space).map(function(e){ return '@Space:' + e; });
                searchParams['space'] = "IN(" + spaces + ")";
            }

            if(vars.categoryFilters && vars.categoryFilters.agent){
                agents = Object.keys(vars.categoryFilters.agent).map(function(e){ return '@Agent:' + e; });
                searchParams['owner'] = "IN(" + agents + ")";
            }else if(vars.generalFilters.agent){
                agents = Object.keys(vars.generalFilters.agent).map(function(e){ return '@Agent:' + e; });
                searchParams['owner'] = "IN(" + agents + ")";
            }

            if(vars.categoryFilters && vars.categoryFilters.project){
                projects = Object.keys(vars.categoryFilters.project).map(function(e){ return '@Project:' + e; });
                searchParams['project'] = "IN(" + projects + ")";
            }else if(vars.generalFilters.project){
                projects = Object.keys(vars.generalFilters.project).map(function(e){ return '@Project:' + e; });
                searchParams['project'] = "IN(" + projects + ")";
            }

            // geodivisions

            for(var geo in vars.generalFilters.geoDivisions){
                var divisions = [];
                if(vars.categoryFilters && vars.categoryFilters.geoDivisions && vars.categoryFilters.geoDivisions[geo] && vars.categoryFilters.geoDivisions[geo].length){
                    divisions = vars.categoryFilters.geoDivisions[geo];
                }else if(!vars.generalFilters.empty[geo]){
                    divisions = vars.generalFilters.geoDivisions[geo];
                }

                if(divisions.length){
                    searchParams['space:' + geo] = 'IN(' + divisions.toString() + ')';
                }
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
