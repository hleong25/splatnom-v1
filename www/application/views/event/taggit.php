<?php
$params_info = array(
    'status'=>array(),
    'name'=>'',
    'notes'=>'',
    'address'=>'',
    'latitude'=>0,
    'longitude'=>0,
    'dates'=>array(),
    'imgs'=>array(),
);

$params = array(
    'dbg' => array(),
    'is_admin' => false,
    'is_metadata' => false,
    'err_msgs' => array(),
    'event_id' => 0,
    'info' => $params_info,
    'vendor_info' => array(),
    'taggits' => array(),
);

extract($params, EXTR_SKIP);

?>
<div class="pg">
    <div class="info">
        <p><a href="/<?=$myurl?>"><?=$info['name']?> - <?=$vendor_info['name']?></a></p>
    </div>
    <form id="taggits" enctype="multipart/form-data" method="post" action="/<?=$myurl?>">
    <button class="">Save!</button>
        <input type="hidden" name="save_taggits" />
    <ul class="images">
    <?php
        foreach ($taggits as $img_id => $tag):
            $file_img = $tag['file_img'];

            $link_img_org = "/images/get/event/org/$event_id/$file_img";
            $link_img_md  = "/images/get/event/md/$event_id/$file_img";

            $is_tagged = !empty($tag['vendor_id']) ? 'CHECKED' : '';
    ?>
        <li class="img">
            <a target="_blank" href="<?=$link_img_org?>"><img src="<?=$link_img_md?>"></a>
            <label class="img_taggit">
                <input type="checkbox" name="taggits[<?=$img_id?>]" <?=$is_tagged?> /> Taggit!
            </label>
        </li>
    <?php endforeach; // foreach ($taggits as $tag): ?>
    </ul>
    </form>
</div>
