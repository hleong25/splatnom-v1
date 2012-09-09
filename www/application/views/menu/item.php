<?php
$params = array(
    'menu_id'=>0,
    'item'=>array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg nav">
    <a href="/menu/view/<?=$menu_id?>">Back to <?=$item['place_name']?></a>
</div>
<div class="pg info">
    <span class="name"><?=$item['label']?></span><br/>
    <span class="notes"><?=$item['notes']?></span>
</div>
<div class="pg add">
    <input type="button" value="Add photos"></input>
    <input type="button" value="Add reviews"></input>
</div>
<div class="pg photos">
    <div class="list">
        henry leong
    </div>
    <div class="view">
        hello world
    </div>
</div>
<div class="pg reviews">
    what up!!
</div>
