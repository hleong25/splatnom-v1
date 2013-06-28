<?php
$params = array
(
    'is_upload'=>false,
    'is_err'=>false,
    'event_id'=>0,
    'event_str'=>'',
    'new_imgs'=>array(),
);

extract($params, EXTR_SKIP);

$slug = array
(
    'event'=>Util::slugify($event_str),
);

?>
<p class="pg info">Adding images to <a class="showlink" href="/event/view/<?=$event_id?>-<?=$slug['event']?>"><?=$event_str?></a></p>
<br/>
<div class="pg upload">
<form id="upload_photos" enctype="multipart/form-data" method="post" action="/<?=$myurl?>" >
    <ul class="upload_files">
        <script type="tmpl/add_img" id="tmpl_add_img">
            <li class="file"><input class="file" type="file" name="imgs[]"/></li>
        </script>
        <li class="file"><input class="file" type="file" name="imgs[]"/></li>
        <li class="file"><input class="file" type="file" name="imgs[]"/></li>
        <li class="file"><input class="file" type="file" name="imgs[]"/></li>
        <li class="file"><input class="file" type="file" name="imgs[]"/></li>
        <li class="file"><input class="file" type="file" name="imgs[]"/></li>
    </ul>
    <input id="btnAddMore" class="button" type="button" value="Add more"/>
    <input class="button" type="submit" value="Submit" />
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

        $img_link = "/images/get/event/org/{$event_id}/{$filename}";
        $thumbnail_link = "/images/get/event/md/{$event_id}/{$filename}";

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

