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
    'is_logged_in'=>false,
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

$slug = array
(
    'menu'=>Util::slugify($info['name']),
    'section'=>'',
    'item'=>'',
);
?>

<?php if (!$is_ready): ?>
<div class="notready">
    <span>The status of this menu is '<?=$curr_status?>'.<br/>Just like the cake... this menu is a lie.</span>
</div>
<?php endif; ?>

<div class="pg">
<div class="info">
    <p class="name"><a href="/menu/view/<?=$menu_id?>-<?=$slug['menu']?>"><?=$info['name']?></a></p>
    <p class="addy"><?=nl2br($info['address'])?></p>
</div>

<div class="imgs_info">
    <?php
        $cnt_imgs = count($imgs);
        if ($cnt_imgs == 0):
            $imgs_msg = 'No images found';
        elseif ($cnt_imgs == 1):
            $imgs_msg = 'Found 1 image';
        else:
            $imgs_msg = "Found {$cnt_imgs} images";
        endif;
    ?>
    <span><?=$imgs_msg?></span>
</div>

<div class="view">
    <div class="img">
    <?php if (!empty($imgs)): ?>
        <a href="/images/get/menu/org/<?=$menu_id?>/<?=$selected_img['filename']?>" target="_blank">
            <img class="view_img" src="/images/get/menu/<?=$selected_img['preview']['size']?>/<?=$menu_id?>/<?=$selected_img['filename']?>" width="<?=$selected_img['preview']['width']?>" height="<?=$selected_img['preview']['height']?>" />
        </a>

        <?php if ($is_logged_in === true): ?>
        <div class="tag">
            <div class="autocomplete">
                <script type="tmpl/taggit" id="tmpl_taggit">
                    <div class="tag_group">
                        <span class="label">${label}</span>
                        <input type="hidden" name="add[]" value="1"/>
                        <input type="hidden" name="sid[]" value="${sid}"/>
                        <input type="hidden" name="mid[]" value="${mid}"/>
                    </div>
                </script>
            </div>
            <form class="taggit" enctype="multipart/form-data" method="post" action="/menu/taggit/images/<?=$menu_id?>/<?=$selected_img['filename']?>" >
                <input type="hidden" name="backurl" value="<?=$myurl?>"/>
                <input class="save_taggits" type="submit" value="Save!"/>
                <input type="textbox" id="tags" class="jq_watermark" title="This looks delicious!!! What's that?"></input>
                <br/>
                <div class="current_tags">
                <?php foreach ($taggits as $taggit): ?>
                    <div class="tag_group">
                        <a href="/menu/images/<?=$menu_id?>-<?=$slug['menu']?>/<?=$taggit['sid']?>-<?=Util::slugify($taggit['section'])?>/<?=$taggit['mid']?>-<?=Util::slugify($taggit['metadata'])?>">
                            <span class="label">(<?=$taggit['section']?>) <?=$taggit['metadata']?></span>
                        </a>
                        <input type="hidden" name="add[]" value="1"/>
                        <input type="hidden" name="sid[]" value="<?=$taggit['sid']?>"/>
                        <input type="hidden" name="mid[]" value="<?=$taggit['mid']?>"/>
                    </div>
                <?php endforeach; //foreach ($taggits as $taggit): ?>
                </div>
            </form>
        </div>
        <?php else: //if ($is_logged_in): ?>
        <div class="current_tags">
            <?php foreach ($taggits as $taggit): ?>
                <div class="tag_group need_login">
                    <a href="/menu/images/<?=$menu_id?>-<?=$slug['menu']?>/<?=$taggit['sid']?>-<?=Util::slugify($taggit['section'])?>/<?=$taggit['mid']?>-<?=Util::slugify($taggit['metadata'])?>">
                        <span class="label">(<?=$taggit['section']?>) <?=$taggit['metadata']?></span>
                    </a>
                </div>
            <?php endforeach; //foreach ($taggits as $taggit): ?>
        </div>
        <?php endif; //if ($is_logged_in): ?>

        <?php if (empty($taggits) && !$is_logged_in): ?>
        <div class="notags">
            <span>Wow... I don't know what this is, but it looks delicious!! Can you login and help me find out what it is?? Please!!! With a cherry on top =D</span>
        </div>
        <?php endif; //if (empty($taggits) && !$is_logged_in): ?>

    <?php endif; //if (!empty($imgs): ?>
    </div>
</div>

<div class="imgs">
<?php foreach ($imgs as $img):
    $filename = $img['filename'];

    $img_link = "/menu/images/{$menu_id}-{$slug['menu']}";

    if (!empty($section_id))
        $img_link .= "/{$section_id}";

    if (!empty($item_id))
        $img_link .= "/{$item_id}";

    $img_link .= "/{$filename}";

    $preview = $img['preview'];
    $thumbnail_link = "/images/get/menu/{$preview['size']}/{$menu_id}/{$filename}";

    $selected_pv_css = '';
    if ($selected_img['filename'] === $filename)
        $selected_pv_css = 'selected_pv_img';

?>
    <div class="img">
        <a href="<?=$img_link?>"><img class="pv_img <?=$selected_pv_css?>" src="<?=$thumbnail_link?>" width="<?=$preview['width']?>" height="<?=$preview['height']?>" /></a>
    </div>
<?php endforeach; // foreach ($imgs as $img) ?>
</div>
<?php /*
<pre><?=var_export($info)?></pre>
<pre><?=var_export($id_names)?></pre>
<pre><?=var_export($imgs)?></pre>
*/ ?>
</div>
<script type="text/javascript">
    <?php
        // TODO: extract parameter in script tag
        //       http://wowmotty.blogspot.com/2010/04/get-parameters-from-your-script-tag.html
        //       http://feather.elektrum.org/book/src.html
    ?>
    var menu_tags = <?= $is_logged_in ? json_encode($tags) : '[]'?>;
</script>
