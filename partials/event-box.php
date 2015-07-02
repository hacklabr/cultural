<?php
global $__event_url, $__image;
$link_url = get_post_meta(get_the_ID(), 'mc-entity-relation', true);

$image = $__image;
$url = $__event_url;

if($link_url == $url){
    $event = MapasCulturais2Post::getEventInfoFromAPI($url, false, $__image);

}else{
    $event = MapasCulturais2Post::getEventInfoFromAPIProxy($url, false, $__image);
}

if(!$event){
    return;
}

$price = '';
$same_price = true;

foreach ($event->occurrences as $i => $occ) {
    if ($i > 0 && $price != $occ->price) {
        $same_price = false;
    }
    $price = $occ->price;
}
?>
<div class="event-container">
    <?php if ($event->files->$image): ?>
        <figure class="event__image">
            <img src="<?php echo $event->files->$image ?>" alt="<?php echo $event->name ?>" />
        </figure>
    <?php endif; ?>
    <div class="event-data">
        <h1 class="event__title">
            <?php echo $event->name ?>
            <?php if($event->subTitle): ?>
                <span class="event__subtitle"><?php echo $event->subTitle ?></span>
            <?php endif; ?>
        </h1>
        <?php foreach ($event->occurrences as $occ): ?>
            <div class="event__occurrences">
                <div class="event__venue"><a href="<?php echo $occ->space->singleUrl ?>"><?php echo $occ->space->name ?></a></div>
                <div class="event__time"><?php echo $occ->description ?></div>
                <?php if (!$same_price && $occ->price): ?>
                    <div class="event__price">
                        <span class="fa-stack">
                            <i class="fa fa-circle fa-stack-2x"></i>
                            <i class="fa fa-usd fa-stack-1x fa-inverse"></i>
                        </span>
                        <?php echo $occ->price ?>
                    </div>
                <?php endif; ?>
            </div>
        <?php endforeach; ?>


        <span class="event__classification"><?php echo $event->classificacaoEtaria ?></span>


        <?php if ($same_price): ?>
            <div class="event__price">
                <span class="fa-stack">
                    <i class="fa fa-circle fa-stack-2x"></i>
                    <i class="fa fa-usd fa-stack-1x fa-inverse"></i>
                </span>
                <?php echo $price ? $price : __('Não informado', 'cultural') ?>
            </div>
        <?php endif; ?>

        <div><strong>projeto:</strong> <a href="<?php echo $event->project->singleUrl ?>" class="ng-binding"><?php echo $event->project->name ?></a></div>
        <div><strong>publicado por:</strong> <a href="<?php echo $event->owner->singleUrl ?>" class="ng-binding"><?php echo $event->owner->name ?></a></div>



        <a href="<?php echo $event->singleUrl ?>" class="event__info"><?php _e('Mais informações', 'cultural'); ?></a>

    </div>
</div>
