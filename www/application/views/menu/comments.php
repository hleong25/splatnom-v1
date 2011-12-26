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
?>
<div class="pg menu_nav">
    <span class="menu_subnav">&raquo;</span> <a href="/menu/view/<?=$menu_id?>">Menu</a>
    <?php if (!empty($menu_str)): ?>
        <span class="menu_subnav">&raquo;</span> <a href="/menu/comments/<?=$menu_id?>"><?=$menu_str?></a>
    <?php endif; //if (!empty($menu_str): ?>
    <?php if (!empty($section_str)): ?>
        <span class="menu_subnav">&raquo;</span> <a href="/menu/comments/<?=$menu_id?>/<?=$section_id?>"><?=$section_str?></a>
    <?php endif; //if (!empty($section_str): ?>
    <?php if (!empty($item_str)): ?>
        <span class="menu_subnav">&raquo;</span> <a href="/menu/comments/<?=$menu_id?>/<?=$section_id?>/<?=$item_id?>"><?=$item_str?></a>
    <?php endif; //if (!empty($item_str): ?>
    <br/>
</div>

