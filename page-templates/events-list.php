<?php
/**
 * Template Name: Events
 * For listing events
 *
 * @package cultural
 */
if(is_category()){
    $category = get_category(get_query_var('cat'));
    $cat_data = get_option('category_' . $category->cat_ID);
    $cat_color = $cat_data['color'];
}
?>

<?php get_header(); ?>

<div class="content  content--full" ng-controller="eventsController">
    <?php if(is_page() && get_the_content()): the_post();?>
    <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
        <div class="hentry-wrap">
            <div class="entry-content">
                <?php the_content(); ?>
                <?php wp_link_pages('before=<div class="page-link">' . __('Páginas:', 'cultural') . '&after=</div>') ?>
            </div>
        </div>
    </article>
    <?php endif; ?>

    <div class="filter-bar cf" <?php if(is_category()): ?> style="background: <?php echo $cat_color ?>" <?php endif; ?>>
        <div class="filter">
            <label>
                <span class="label"><?php _e('Palavra-Chave', 'cultural'); ?></span>
                <input type="text" ng-model="keyword" class="placeholder keyword">
            </label>
        </div>

        <div class="filter">
            <span class="label"><?php _e('Data', 'cultural'); ?></span>
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

        <div ng-if="data.classificacoes" class="filter">
            <span class="label"><?php _e('Classificação', 'cultural'); ?></span>
            <div id="classificacao" class="dropdown">
                <div class="placeholder">
                    <span ng-if="!svc.data.classificacoes.length"><?php _e('Selecione a classificação', 'cultural'); ?></span>
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

    <h3 class="aligncenter texcenter" ng-if="!loading">{{events.length}} {{events.length == 1 ? '<?php _e('evento encontrado', 'cultural'); ?>' : '<?php _e('eventos encontrados', 'cultural'); ?>'}}</h3>

    <div ng-if="loading">
        <div class="spinner-bars">
            <div class="rect1"></div>
            <div class="rect2"></div>
            <div class="rect3"></div>
            <div class="rect4"></div>
            <div class="rect5"></div>
        </div>
    </div>

    <div class="events-list grid js-events-masonry" ng-show="!loading">
        <div class="grid-sizer"></div>
        <div class="gutter-sizer"></div>

        <div class="event  event-container" ng-repeat="event in events" repeat-done="updateMasonry()">
            <figure ng-if="event['@files:header.header']" class="event__image" style="background:transparent" >
                <img src="{{event['@files:header.header'].url}}" alt="{{event.name}}" style="width:100%"/>
            </figure>
            <div class="event-data">
                <h1 class="event__title">
                    {{event.name}}
                    <!--<a href="{{event.singleUrl}}" target="_blank"><i class="fa fa-external-link"></i></a>-->
                    <span class="event__subtitle">{{event.subTitle}}</span>
                </h1>


                <div class="event__occurrences" ng-repeat="occs in event.occurrences" ng-if="occs.inPeriod">
                    <div class="event__venue">
                        <a href="{{occs.space.singleUrl}}">{{occs.space.name}}</a>
                    </div>
                    <div class="event__time">{{occs.rule.description}}</div>
                    <!--a href="#" class="js-more-occurrences"><i class="fa fa-plus-circle"></i></a-->
                </div>

                <div class="event__languages" style="margin: -10px 0 10px 0">
                    <h4 class="event__languages--title">{{event.terms.linguagem.length == 1 ? '<?php _e('Linguagem', 'cultural'); ?>' : '<?php _e('Linguagens', 'cultural'); ?>'}}:</h4> {{event.terms.linguagem.join(', ')}}
                </div>
                <span class="event__classification">{{event.classificacaoEtaria}}</span>

                <div class="event__price">
                    <span class="fa-stack">
                        <i class="fa fa-circle fa-stack-2x"></i>
                        <i class="fa fa-usd fa-stack-1x fa-inverse"></i>
                    </span>
                    {{event.occurrences[0].rule.price}}
                </div>

                <div ng-if="event.traducaoLibras == 'Sim' && event.descricaoSonora == 'Sim'" class="event__accessibility"><strong>acessibilidade:</strong> Tradução para LIBRAS, Áudio descrição</div>
                <div ng-if="event.traducaoLibras == 'Sim' && event.descricaoSonora != 'Sim'" class="event__accessibility"><strong>acessibilidade:</strong> Tradução para LIBRAS</div>
                <div ng-if="event.traducaoLibras != 'Sim' && event.descricaoSonora == 'Sim'" class="event__accessibility"><strong>acessibilidade:</strong> Áudio descrição</div>

                <div ng-if="event.project.name">
                    <h4>projeto:</h4>
                    <a href="{{event.project.singleUrl}}">{{event.project.name}}</a>
                </div>
                <div ng-if="event.owner.name">
                    <h4>publicado por:</h4>
                    <a href="{{event.owner.singleUrl}}">{{event.owner.name}}</a>
                </div>
                <a href="{{event.singleUrl}}" target="_blank" class="event__info"><?php _e('Mais informações', 'cultural'); ?></a>
            </div>
        </div>

    </div>

    <?php comments_template('', true); ?>
</div>

<?php
get_footer();
