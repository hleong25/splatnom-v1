<?php
$params = array
(
    'selected_img'=>array('filename'=>'','id'=>0),
    'imgs'=>array(),
    'taggits'=>array(),
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
        <div class="autocomplete">
            <label for="tags">This looks delicious!!! Can you tell me what it is? </label><br/>
            <input type="textbox" id="tags" class="jq_watermark" title="taggit"></input>
            <div class="tag_group template">
                <img class="remove_tag" src="/img/minus.png" onclick="return js_menu.taggit_remove(this);"/>
                <span class="label"></span>
                <input type="hidden" name="add[]" value="1"/>
                <input type="hidden" name="sid[]" value=""/>
                <input type="hidden" name="mid[]" value=""/>
            </div>
        </div>
        <form class="taggit" enctype="multipart/form-data" method="post" action="/menu/taggit/<?=$id?>/<?=$selected_img['filename']?>" >
            <input type="hidden" name="backurl" value="<?=$myurl?>"/>
            <input class="save_taggits" type="submit" value="Save!"/>
            <?php foreach ($taggits as $taggit): ?>
                <div class="tag_group">
                    <img class="remove_tag" src="/img/minus.png" onclick="return js_menu.taggit_remove(this);"/>
                    <span class="label">(<?=$taggit['section']?>) <?=$taggit['metadata']?></span>
                    <input type="hidden" name="add[]" value="1"/>
                    <input type="hidden" name="sid[]" value="<?=$taggit['sid']?>"/>
                    <input type="hidden" name="mid[]" value="<?=$taggit['mid']?>"/>
                </div>
            <?php endforeach; //foreach ($taggits as $taggit): ?>
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
<script type="text/javascript">
    <?php
        // TODO: extract parameter in script tag
        //       http://wowmotty.blogspot.com/2010/04/get-parameters-from-your-script-tag.html
        //       http://feather.elektrum.org/book/src.html
    ?>
    var menu_tags = <?=json_encode($tags)?>;
</script>
