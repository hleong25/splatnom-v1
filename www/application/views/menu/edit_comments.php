<?php
$params = array
(
    'is_logged_in'=>false,
    'menu_id'=>0,
    'section_id'=>0,
    'item_id'=>0,
    'menu_str'=>'',
    'section_str'=>'',
    'item_str'=>'',
);

extract($params, EXTR_SKIP);

$slug = array
(
    'menu'=>Util::slugify($menu_str),
    'section'=>Util::slugify($section_str),
    'item'=>Util::slugify($item_str),
);

?>
<div class="pg menu_nav">
    <span>Adding comments to <a href="/menu/view/<?=$menu_id?>-<?=$slug['menu']?>"><?=$menu_str?></a></span>
    <?php if (!empty($section_str) && !empty($item_str)): ?>
        <br/>
        <span>Tagging them to <a href="/menu/images/<?=$menu_id?>-<?=$slug['menu']?>/<?=$section_id?>-<?=$slug['section']?>/<?=$item_id?>-<?=$slug['item']?>">(<?=$section_str?>) <?=$item_str?></a></span>
    <?php endif; //if (!empty($section_str) && !empty($item_str)): ?>
</div>

