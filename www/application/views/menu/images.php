<?php
$params = array
(
    'is_logged_in'=>false,
    'menu_id'=>0,
    'section_id'=>0,
    'item_id'=>0,
    'menu_str'=>'',
    'section_str'=>'',
    'item_str'=>'',
    'selected_img'=>array('filename'=>'','id'=>0),
    'imgs'=>array(),
    'taggits'=>array(),
    'tags'=>array(),
);

extract($params, EXTR_SKIP);

$slug = array
(
    'menu'=>Util::slugify($menu_str),
    'section'=>Util::slugify($section_str),
    'item'=>Util::slugify($item_str),
);

?>
<div class="pg menu_nav">
    <span class="menu_subnav">&raquo;</span> <a href="/menu/view/<?=$menu_id?>-<?=$slug['menu']?>">Menu</a>
    <?php if (!empty($menu_str)): ?>
        <span class="menu_subnav">&raquo;</span> <a href="/menu/images/<?=$menu_id?>-<?=$slug['menu']?>"><?=$menu_str?></a>
    <?php endif; //if (!empty($menu_str): ?>
    <?php if (!empty($section_str)): ?>
        <span class="menu_subnav">&raquo;</span> <a href="/menu/images/<?=$menu_id?>-<?=$slug['menu']?>/<?=$section_id?>-<?=$slug['section']?>"><?=$section_str?></a>
    <?php endif; //if (!empty($section_str): ?>
    <?php if (!empty($item_str)): ?>
        <span class="menu_subnav">&raquo;</span> <a href="/menu/images/<?=$menu_id?>-<?=$slug['menu']?>/<?=$section_id?>-<?=$slug['section']?>/<?=$item_id?>-<?=$slug['item']?>"><?=$item_str?></a>
    <?php endif; //if (!empty($item_str): ?>
    <br/>
    <?php if ($is_logged_in && !empty($menu_str)): ?>
        <br/>
        <?php if (!empty($section_str) && !empty($item_str)): ?>
            <a class="button" href="/images/upload/<?=$menu_id?>-<?=$slug['menu']?>/<?=$section_id?>-<?=$slug['section']?>/<?=$item_id?>-<?=$slug['item']?>">Add images to (<?=$section_str?>) <?=$item_str?></a>
        <?php else: //if (!empty($section_str) && !empty($item_str)): ?>
            <a class="button" href="/images/upload/<?=$menu_id?>-<?=$slug['menu']?>">Add images to <?=$menu_str?></a>
        <?php endif; //if (!empty($section_str) && !empty($item_str)): ?>
    <?php endif; //if (!empty($menu_str): ?>
</div>
<div class="pg imgs">
<?php
    foreach ($imgs as $img)
    {
        $filename = $img['filename'];

        $img_link = '/menu/images/'.$menu_id;

        if (!empty($section_id))
            $img_link .= "/{$section_id}";

        if (!empty($item_id))
            $img_link .= "/{$item_id}";

        $img_link .= "/{$filename}";

        $thumbnail_link = "/images/get/menu/sm/{$menu_id}/{$filename}";

        $selected_pv_css = '';
        if ($selected_img['filename'] === $filename)
            $selected_pv_css = 'selected_pv_img';

        echo<<<EOHTML
            <a href="{$img_link}"><img class="pv_img {$selected_pv_css}" src="{$thumbnail_link}" /></a>
EOHTML;
    }
?>
</div>
<?php if (empty($imgs)): ?>
<div class="pg">
    <span>No images in this menu...</span>
</div>
<?php else: //if (empty($imgs)): ?>
<div class="pg view">
    <div class="img">
        <a href="<?="/images/get/menu/org/{$menu_id}/{$selected_img['filename']}"?>" target="_blank">
            <img class="view_img" src="<?="/images/get/menu/lg/{$menu_id}/{$selected_img['filename']}"?>" />
        </a>
    </div>
    <?php if ($is_logged_in === true): ?>
    <div class="tag">
        <div class="autocomplete">
            <label for="tags">This looks delicious!!! What's that? </label><br/>
            <input type="textbox" id="tags" class="jq_watermark" title="taggit"></input>
            <div class="tag_group template">
                <span class="label"></span>
                <input type="hidden" name="add[]" value="1"/>
                <input type="hidden" name="sid[]" value=""/>
                <input type="hidden" name="mid[]" value=""/>
            </div>
        </div>
        <form class="taggit" enctype="multipart/form-data" method="post" action="/menu/taggit/<?=$menu_id?>/<?=$selected_img['filename']?>" >
            <input type="hidden" name="backurl" value="<?=$myurl?>"/>
            <input class="save_taggits" type="submit" value="Save!"/>
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
    <?php endif; //if (empty($taggits)): ?>
</div>
<div class="clear"></div>
<?php endif; //if (empty($imgs)): ?>
<div class="pg comments">
    <?php
        // TODO: Add comments
    ?>
</div>
<script type="text/javascript">
    <?php
        // TODO: extract parameter in script tag
        //       http://wowmotty.blogspot.com/2010/04/get-parameters-from-your-script-tag.html
        //       http://feather.elektrum.org/book/src.html
    ?>
    var menu_tags = <?= $is_logged_in ? json_encode($tags) : '[]'?>;
</script>
