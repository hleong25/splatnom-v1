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
    <span class="menu_subnav">&raquo;</span> <a href="/menu/view/<?=$menu_id?>-<?=$slug['menu']?>">Menu</a>
    <?php if (!empty($menu_str)): ?>
        <span class="menu_subnav">&raquo;</span> <a href="/menu/comments/<?=$menu_id?>-<?=$slug['menu']?>"><?=$menu_str?></a>
    <?php endif; //if (!empty($menu_str): ?>
    <?php if (!empty($section_str)): ?>
        <span class="menu_subnav">&raquo;</span> <a href="/menu/comments/<?=$menu_id?>-<?=$slug['menu']?>/<?=$section_id?>-<?=$slug['section']?>"><?=$section_str?></a>
    <?php endif; //if (!empty($section_str): ?>
    <?php if (!empty($item_str)): ?>
        <span class="menu_subnav">&raquo;</span> <a href="/menu/comments/<?=$menu_id?>-<?=$slug['menu']?>/<?=$section_id?>-<?=$slug['section']?>/<?=$item_id?>-<?=$slug['item']?>"><?=$item_str?></a>
    <?php endif; //if (!empty($item_str): ?>
    <br/>
</div>
<div class="pg add_comments">
    <?php if (!empty($section_str) && !empty($item_str)): ?>
        <span>Add comments to <a href="/menu/edit_comments/<?=$menu_id?>-<?=$slug['menu']?>/<?=$section_id?>-<?=$slug['section']?>/<?=$item_id?>-<?=$slug['item']?>">(<?=$section_str?>) <?=$item_str?></a></span>
    <?php else: ?>
        <span>Add comments to <a href="/menu/edit_comments/<?=$menu_id?>-<?=$slug['menu']?>"><?=$menu_str?></a></span>
    <?php endif; //if (!empty($section_str) && !empty($item_str)): ?>
</div>
