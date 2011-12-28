<?php
$params = array
(
//    'id'=>0,
//    'info'=>false,
    'is_upload'=>false,
    'is_err'=>false,
    'new_imgs'=>array(),
    'tags'=>array(),
    'menu_id'=>0,
    'section_id'=>0,
    'item_id'=>0,
    'menu_str'=>'',
    'section_str'=>'',
    'item_str'=>'',
);

extract($params, EXTR_SKIP);

$slug_menu_name = Util::slugify($info['name']);

$slug = array
(
    'menu'=>Util::slugify($menu_str),
    'section'=>Util::slugify($section_str),
    'item'=>Util::slugify($item_str),
);

?>
<div class="pg info">
    <span>Adding images to <a href="/menu/view/<?=$menu_id?>-<?=$slug['menu']?>"><?=$menu_str?></a></span>
    <?php if (!empty($section_str) && !empty($item_str)): ?>
        <br/>
        <span>Tagging them to <a href="/menu/images/<?=$menu_id?>-<?=$slug['menu']?>/<?=$section_id?>-<?=$slug['section']?>/<?=$item_id?>-<?=$slug['item']?>">(<?=$section_str?>) <?=$item_str?></a></span>
    <?php endif; //if (!empty($section_str) && !empty($item_str)): ?>
    <br/>
    <br/>
</div>
<div class="pg upload">
<form id="upload_photos" enctype="multipart/form-data" method="post" action="/<?=$myurl?>" >
    <div class="new_img">
        <input class="file" type="file" name="imgs[]"/>
    </div>
    <div class="new_img">
        <input class="file" type="file" name="imgs[]"/>
    </div>
    <div class="new_img">
        <input class="file" type="file" name="imgs[]"/>
    </div>
    <div class="new_img">
        <input class="file" type="file" name="imgs[]"/>
    </div>
    <div class="new_img">
        <input class="file" type="file" name="imgs[]"/>
    </div>
    <input id="btnAddMore" type="button" value="Add more"/>
    <br/>
    <br/>
    <input type="submit" value="Submit" />
</form>
</div>
<?php if ($is_upload): ?>
<div class="pg msg">
    <br/>
    <?php if ($is_err): ?>
        <span class="err" style="">Failed to upload image.  Try again or notify the admin!</span>
    <?php endif; // if ($is_err) ?>
</div>
<?php if (!empty($new_imgs)): ?>
<div class="pg uploaded_photos">
    <hr/>
    <br/>
<?php
    foreach ($new_imgs as $img)
    {
        $filename = $img['filename'];

        $img_link = "/images/get/menu/org/{$id}/{$filename}";
        $thumbnail_link = "/images/get/menu/md/{$id}/{$filename}";

        echo<<<EOHTML
            <div class="new_img">
                <img src="$thumbnail_link" />
            </div>
EOHTML;
    }
?>
</div>
<?php endif; //if (!empty($new_imgs)): ?>
<?php endif; // if ($is_upload) ?>
<script type="text/javascript">
    <?php
        // TODO: extract parameter in script tag
        //       http://wowmotty.blogspot.com/2010/04/get-parameters-from-your-script-tag.html
        //       http://feather.elektrum.org/book/src.html
    ?>
    var menu_tags = <?=json_encode($tags)?>;
</script>
