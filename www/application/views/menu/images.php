<?php
$params = array
(
    'selected_img'=>'',
    'imgs'=>array(),
);

extract($params, EXTR_SKIP);

?>
<?php if (empty($imgs)): ?>
<div class="pg">
    <span>No images in this menu...</span>
</div>
<?php else: //if (empty($imgs)): ?>
<div class="pg view">
    <div class="img">
    <?php
        $view_img = "/images/get/menu/lg/{$id}/{$selected_img}";
    ?>
    <img class="view_img" src="<?=$view_img?>" />
    </div>
    <br/>
</div>
<div class="pg imgs">
<?php
    foreach ($imgs as $img)
    {
        $filename = $img['filename'];
        //$width = $img['width'];
        //$height = $img['height'];

        $img_link = "/menu/images/{$id}/&view={$filename}";
        $thumbnail_link = "/images/get/menu/sm/{$id}/{$filename}";

        echo<<<EOHTML
            <a href="$img_link"><img class="pv_img" src="$thumbnail_link" /></a>
EOHTML;
    }
?>
</div>
<?php endif; //if (empty($imgs)): ?>
