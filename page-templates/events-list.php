<?php
/**
 * Template Name: Events
 * For listing events
 *
 * @package cultural
 */
?>

<?php get_header(); ?>

<div class="content  content--full" ng-controller="eventsController">
    <?php if(is_page()): the_post();?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="hentry-wrap">
            <div class="entry-content">
                <?php the_content(); ?>
                <?php wp_link_pages('before=<div class="page-link">' . __('Pages:', 'cultural') . '&after=</div>') ?>
            </div>
        </div>
    </article>
    <?php endif; ?>

    <div class="filter-bar  cf">
        <style>
            .testApplyClass{

            }
            .testCancelClass{

            }
            .daterangepicker td.active, .daterangepicker td.active:hover {
                background-color: #b01116; /*#357ebd;*/
                border-color: #810d10 transparent transparent transparent; /*#3071a9;*/
                color: #fff;
            }
            .daterangepicker td.in-range {
                background:  #f4efef;/*#ebf4f8;*/
                border-radius: 0px;
            }
            .daterangepicker .calendar.left td.in-range {
                border-radius: 4px 0 0 4px;
            }
            .daterangepicker .calendar.right td.in-range {
                border-radius: 0 4px 4px 0;
            }
            .daterangepicker .calendar th.available:not(.active):hover,
            .daterangepicker .calendar td.available:not(.active):hover{
                background: #e4cfcf;/*#eee;*/
            }
            .js-cancel{display:none;}
            .daterangepicker .ranges{display:block!important;}
            .daterangepicker {display:none;}
            .date--picker {position: relative;}
            .date--picker i {
                left: 10px;
                margin-right: 5px;
                position: absolute;
                top: 15px;
                z-index: 20;
            }
            .date--picker input{
                width:100%;
                letter-spacing: 1px;
            }
        </style>

        <div class="filter">
            <span class="label">Data</span>
            <div class="date--picker">
                <i class="fa fa-calendar"></i>
                <!--

                Fonte do plugin jQuery Bootstrap: https://github.com/dangrossman/bootstrap-daterangepicker

                Diretiva Angular para o plugin: https://github.com/fragaria/angular-daterangepicker

                !important: O template, já um pouco modificado do daterangepicker está na linha 48 de js/lib/daterangpicker.js
                @TODO: Passar o template pra fora, aqui para o hipertexto

                -->
                <input class="form-control date-picker date" ng-model="dateRange"
                       date-range-picker="{
                       format:'DD/MMMM',
                       separator: '  a  ',
                       locale: {
                       applyLabel: 'Aplicar',
                       cancelLabel: 'Cancelar',
                       fromLabel: 'De',
                       toLabel: 'Até'
                       },
                       applyClass: 'testApplyClass btn-primary btn-xs',
                       cancelClass: 'testCancelClass btn-xs',
                       }"
                       style="padding-left:38px"
                       onfocus="this.blur()"
                       >
            </div>
        </div>

        <div ng-if="data.linguagens.length > 1" class="filter">
            <span class="label">Linguagem</span>
            <div class="dropdown">
                <div class="placeholder">
                    <span ng-if="!svc.data.linguagens.length">Selecione as linguagens</span>
                    <span ng-if="svc.data.linguagens.length">{{svc.data.linguagens.join(', ')}}</span>
                </div>
                <div class="submenu-dropdown">
                    <ul class="lista-de-filtro select">
                        <li ng-repeat="linguagem in data.linguagens" ng-class="{'selected': linguagem.active}" ng-click="toggleListItem('linguagens', linguagem)" class="ng-scope">
                            <span>{{linguagem.name}}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div ng-if="data.classificacoes" class="filter">
            <span class="label">Classificação</span>
            <div id="classificacao" class="dropdown">
                <div class="placeholder">
                    <span ng-if="!svc.data.classificacoes.length">Selecione a classificação</span>
                    <span ng-if="svc.data.classificacoes.length">{{svc.data.classificacoes.join(', ')}}</span>
                </div>
                <div class="submenu-dropdown">
                    <ul class="lista-de-filtro select">
                        <li ng-repeat="classificacao in data.classificacoes" ng-class="{'selected': classificacao.active}" ng-click="toggleListItem('classificacoes', classificacao)" class="ng-scope">
                            <span class="ng-binding">{{classificacao.name}}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
<span>{{events.length}} eventos encontrados</span>
    <div class="events-list grid js-events-masonry">
        <div class="grid-sizer"></div>
        <div class="gutter-sizer"></div>

        <div class="event  event-container" ng-repeat="event in events" repeat-done="updateMasonry()">
            <figure ng-if="event['@files:avatar.avatarBig']" class="event__image" style="background:#EDEDEC; background-image:url({{event['@files:avatar.avatarBig'].url}}); " >
                <img ng-src="{{event['@files:avatar.avatarBig'].url}}" alt="{{event.name}}" />
            </figure>
            <div class="event-data">
                <h1 class="event__title">
                    {{event.name}}
                    <!--<a href="{{event.singleUrl}}" target="_blank"><i class="fa fa-external-link"></i></a>-->
                    <span class="event__subtitle">{{event.subTitle}}</span>
                </h1>

                <div class="event__occurrences" ng-repeat="occs in event.occurrences" ng-if="$index <= 2">
                    <div class="event__venue">
                        {{occs.space.name}}
                        <!--a href="{{occs.space.singleUrl}}" target="_blank"><i class="fa fa-external-link"></i></a-->
                    </div>
                    <div class="event__time">{{occs.rule.description}}</div>
                    <!--a href="#" class="js-more-occurrences"><i class="fa fa-plus-circle"></i></a-->
                </div>

                <div style="margin: -10px 0 10px 0">
                    Linguagem(ns): {{event.terms.linguagem.join(', ')}}
                </div>
                <span class="event__classification">{{event.classificacaoEtaria}}</span>
                <div class="event__price">
                    <span class="fa-stack">
                        <i class="fa fa-circle fa-stack-2x"></i>
                        <i class="fa fa-usd fa-stack-1x fa-inverse"></i>
                    </span>
                    {{event.occurrences[0].rule.price}}
                </div>
                <div><strong>publicado por:</strong> <a href="{{event.owner.singleUrl}}">{{event.owner.name}}</a></div>
                <a href="{{event.singleUrl}}" target="_blank" class="event__info">Mais informações</a>
            </div>
        </div>

    </div>

    <div ng-if="loading">
        carregando
    </div>

    <?php comments_template('', true); ?>
</div>

<?php get_footer(); ?>