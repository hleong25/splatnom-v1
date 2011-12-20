<?php
$params = array
(
    'selected_img'=>array('filename'=>'','id'=>0),
    'imgs'=>array(),
    'tags'=>array(),
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
        <img class="view_img" src="<?="/images/get/menu/lg/{$id}/{$selected_img['filename']}"?>" />
    </div>
    <div class="tag">
        <script type="text/javascript">
            var menu_tags = <?=json_encode($tags)?>;
        </script>
        <div class="autocomplete">
            <label for="tags">taggit: </label>
            <input type="textbox" id="tags"></input>
            <div class="tag_group template">
                <img src="/img/minus.png" onclick="return js_menu.taggit_remove(this);"/>
                <span class="label"></span>
                <input type="hidden" name="sid[]" value=""/>
                <input type="hidden" name="mid[]" value=""/>
            </div>
        </div>
        <form id="taggit" enctype="multipart/form-data" method="post" action="/menu/tag/<?=$id?>/<?=$selected_img['filename']?>" >
        </form>
    </div>
</div>
<div class="clear"></div>
<div class="pg imgs">
<?php
    foreach ($imgs as $img)
    {
        $filename = $img['filename'];
        //$width = $img['width'];
        //$height = $img['height'];

        $img_link = "/menu/images/{$id}/{$filename}";
        $thumbnail_link = "/images/get/menu/sm/{$id}/{$filename}";

        echo<<<EOHTML
            <a href="$img_link"><img class="pv_img" src="$thumbnail_link" /></a>
EOHTML;
    }
?>
</div>
<?php endif; //if (empty($imgs)): ?>
