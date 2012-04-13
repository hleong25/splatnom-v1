<?php
$default_info = array(
    'name'=>'',
    'address'=>'',
);

$default_id_names = array(
    'menu_id'=>'',
    'section_id'=>'',
    'metadata_id'=>'',
    'menu'=>'',
    'section'=>'',
    'metadata'=>'',
);

$default_selected_img = array(
    'filename'=>'',
    'id'=>0,
);

$params = array(
    'info'=>$default_info,
    'id_names'=>$default_id_names,
    'imgs'=>array(),
    'selected_img'=>$default_selected_img,
    'taggits'=>array(),
    'tags'=>array(),
);

extract($params, EXTR_SKIP);

$menu_id = $id_names['menu_id'];

// setup status info
$is_ready = false;
$curr_status = 'not ready';
foreach ($info['status'] as $info_status)
{
    $status = $info_status['status'];
    $is_selected = $info_status['selected'] == 1;

    if ($status === 'ready')
        $is_ready = $is_selected;

    if ($is_selected)
        $curr_status = $status;
}
?>

<?php if (!$is_ready): ?>
<div class="notready">
    <span>The status of this menu is '<?=$curr_status?>'.<br/>Just like the cake... this menu is a lie.</span>
</div>
<?php endif; ?>

<div class="pg">
<div class="info">
    <p><?=$info['name']?></p>
    <p><?=nl2br($info['address'])?></p>

</div>

<div class="view">
    <div class="img">
    <?php if (!empty($imgs)): ?>
        <a href="/images/get/menu/org/<?=$menu_id?>/<?=$selected_img['filename']?>" target="_blank">
            <img class="view_img" src="/images/get/menu/lg/<?=$menu_id?>/<?=$selected_img['filename']?>" />
        </a>
    <?php endif; //if (!empty($imgs): ?>
    </div>
</div>

<div class="imgs">
<?php foreach ($imgs as $img):
    $filename = $img['filename'];

    $img_link = '/menu/images/'.$menu_id;

    if (!empty($section_id))
        $img_link .= "/{$section_id}";

    if (!empty($item_id))
        $img_link .= "/{$item_id}";

    $img_link .= "/{$filename}";

    $thumbnail_link = "/images/get/menu/md/{$menu_id}/{$filename}";

    $selected_pv_css = '';
    if ($selected_img['filename'] === $filename)
        $selected_pv_css = 'selected_pv_img';

?>
    <div class="img">
        <a href="<?=$img_link?>"><img class="pv_img <?=$selected_pv_css?>" src="<?=$thumbnail_link?>" /></a>
    </div>
<?php endforeach; // foreach ($imgs as $img) ?>
</div>
<br/>
<pre><?=var_export($info)?></pre>
<pre><?=var_export($id_names)?></pre>
<pre><?=var_export($imgs)?></pre>
</div>
