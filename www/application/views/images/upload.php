<?php
$params = array
(
//    'id'=>0,
//    'info'=>false,
    'menu_url'=>'/home/menu',
    'is_upload'=>false,
    'is_err'=>false,
);

extract($params, EXTR_SKIP);
?>
<div class="pg info">
    <span>Upload images for <?=$info['name']?>.</span>
    <br/>
    <a href="<?=$menu_url?>">Go back to menu</a>
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
    <br/>
    <?php if ($is_err): ?>
        <span class="err" style="">Failed to upload image.  Try again or notify the admin!</span>
    <?php else: ?>
        <span>Thanks for uploading these images!!!</span>
        <br/>
        <a href="<?=$menu_url?>">Go back to menu</a>
    <?php endif; // if ($is_err) ?>
</div>
<?php endif; // if ($is_upload) ?>
