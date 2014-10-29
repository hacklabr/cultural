<?php
/**
 * Template Name: Events
 * For listing events
 *
 * @package cultural
 */
?>

<?php get_header(); the_post(); ?>

    <div class="content  content--full">
        <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
            <div class="hentry-wrap">
                <div class="entry-content">
                    <?php the_content(); ?>
                    <?php wp_link_pages('before=<div class="page-link">' . __( 'Pages:', 'cultural' ) . '&after=</div>') ?>
                </div>
            </div>
        </article>

        <div class="filter-bar  cf">
            <form method="get" class="filter  filter-date" action="<?php echo home_url( '/' ); ?>">
                <div class="event__date  event__date--start">
                    <label for="event__start-date">De</label>
                    <input type="text" id="event__start-date" class="date ng-valid hasDatepicker ng-dirty" ng-model="data.event.from" ui-date="dateOptions" ui-date-format="yy-mm-dd" placeholder="00/00/0000" readonly>
                </div>
                <div class="event__date">
                    <label for="event__start-date">A</label>
                    <input type="text" id="event__end-date" class="date ng-valid hasDatepicker ng-dirty" ng-model="data.event.to" ui-date="dateOptions" ui-date-format="yy-mm-dd" placeholder="00/00/0000" readonly>
                </div>
            </form>

            <div class="filter">
                <span class="label">Linguagem</span>
                <div class="dropdown">
                    <div class="placeholder">Selecione as linguagens</div>
                    <div class="submenu-dropdown">
                        <ul class="lista-de-filtro select">
                            <!-- ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Artes Circenses</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Artes Integradas</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Artes Visuais</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Audiovisual</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Cinema</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Cultura Digital</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Cultura Indígena</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Cultura Tradicional</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Curso ou Oficina</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Dança</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Exposição</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Hip Hop</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Livro e Literatura</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Música Erudita</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Música Popular</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Outros</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Palestra, Debate ou Encontro</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Rádio</span>
                            </li><!-- end ngRepeat: linguagem in linguagens --><li ng-repeat="linguagem in linguagens" ng-class="{'selected':isSelected(data.event.linguagens, linguagem.id)}" ng-click="toggleSelection(data.event.linguagens, linguagem.id)" class="ng-scope">
                                <span class="ng-binding">Teatro</span>
                            </li><!-- end ngRepeat: linguagem in linguagens -->
                        </ul>
                    </div>
                </div>
            </div>

            <div class="filter">
                <span class="label">Classificação</span>
                <div id="classificacao" class="dropdown">
                    <div class="placeholder">Selecione a classificação</div>
                    <div class="submenu-dropdown">
                        <ul class="lista-de-filtro select">
                            <!-- ngRepeat: classificacao in classificacoes --><li ng-repeat="classificacao in classificacoes" ng-class="{'selected':isSelected(data.event.classificacaoEtaria, classificacao.id)}" ng-click="toggleSelection(data.event.classificacaoEtaria, classificacao.id)" class="ng-scope">
                                <span class="ng-binding">Livre</span>
                            </li><!-- end ngRepeat: classificacao in classificacoes --><li ng-repeat="classificacao in classificacoes" ng-class="{'selected':isSelected(data.event.classificacaoEtaria, classificacao.id)}" ng-click="toggleSelection(data.event.classificacaoEtaria, classificacao.id)" class="ng-scope">
                                <span class="ng-binding">10 anos</span>
                            </li><!-- end ngRepeat: classificacao in classificacoes -->
                            <li ng-repeat="classificacao in classificacoes" ng-class="{'selected':isSelected(data.event.classificacaoEtaria, classificacao.id)}" ng-click="toggleSelection(data.event.classificacaoEtaria, classificacao.id)" class="ng-scope">
                                <span class="ng-binding">12 anos</span>
                            </li><!-- end ngRepeat: classificacao in classificacoes -->
                            <li ng-repeat="classificacao in classificacoes" ng-class="{'selected':isSelected(data.event.classificacaoEtaria, classificacao.id)}" ng-click="toggleSelection(data.event.classificacaoEtaria, classificacao.id)" class="ng-scope">
                                <span class="ng-binding">14 anos</span>
                            </li><!-- end ngRepeat: classificacao in classificacoes -->
                            <li ng-repeat="classificacao in classificacoes" ng-class="{'selected':isSelected(data.event.classificacaoEtaria, classificacao.id)}" ng-click="toggleSelection(data.event.classificacaoEtaria, classificacao.id)" class="ng-scope">
                                <span class="ng-binding">16 anos</span>
                            </li><!-- end ngRepeat: classificacao in classificacoes -->
                            <li ng-repeat="classificacao in classificacoes" ng-class="{'selected':isSelected(data.event.classificacaoEtaria, classificacao.id)}" ng-click="toggleSelection(data.event.classificacaoEtaria, classificacao.id)" class="ng-scope">
                                <span class="ng-binding">18 anos</span>
                            </li><!-- end ngRepeat: classificacao in classificacoes -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="events-list  grid  js-masonry" data-masonry-options='{ "columnWidth": ".grid-sizer", "gutter": ".gutter-sizer", "itemSelector": ".event" }'>
            <div class="grid-sizer"></div>
            <div class="gutter-sizer"></div>

            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
            <?php get_template_part( 'partials/event' ); ?>
        </div>

        <?php comments_template('', true); ?>
    </div>

<?php get_footer(); ?>
