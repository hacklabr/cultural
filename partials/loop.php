<?php
$printed_ids = array();

if(!is_paged()){
    $q = Cultural_Hightlights::getFixedQuery();
    if($q){
        while ($q->have_posts()){
            $q->the_post();
            $printed_ids[] = $q->post->ID;
            get_template_part('content', 'grid');
        }
    }

    wp_reset_query();
}

while (have_posts()){
    the_post();
    $id = get_the_ID();
    if(!in_array($id, $printed_ids)){
        get_template_part('content', 'grid');
    }

}