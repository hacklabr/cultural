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
        <h1 class="event__title"><?php echo $event->name ?> <span class="event__subtitle"><?php echo $event->subTitle ?></span></h1>
        <?php foreach ($event->occurrences as $occ): ?>
            <div class="event__occurrences">
                <div class="event__venue"><?php echo $occ->space->name ?></div>
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
                <?php echo $price ? $price : 'Não informado' ?>
            </div>
        <?php endif; ?>
        <a href="<?php echo $event->singleUrl ?>" class="event__info">Mais informações</a>
    </div>
</div>
