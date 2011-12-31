<?php
$params = array
(
    'err_msg'=>false,
    'menu_id'=>0,
    'section_id'=>0,
    'item_id'=>0,
    'menu_str'=>'',
    'section_str'=>'',
    'item_str'=>'',
    'tags'=>array(),
    'comment_id'=>-1,
    'post_comments'=>'',
    'taggits'=>array(),
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
    <span>Adding comments to <a href="/menu/view/<?=$menu_id?>-<?=$slug['menu']?>"><?=$menu_str?></a></span>
    <?php if (!empty($section_str) && !empty($item_str)): ?>
        <br/>
        <span>Tagging them to <a href="/menu/comments/<?=$menu_id?>-<?=$slug['menu']?>/<?=$section_id?>-<?=$slug['section']?>/<?=$item_id?>-<?=$slug['item']?>">(<?=$section_str?>) <?=$item_str?></a></span>
    <?php endif; //if (!empty($section_str) && !empty($item_str)): ?>
    <?php if (!empty($err_msg)): ?>
        <br/>
        <br/>
        <span class="error"><?=$err_msg?></span>
    <?php endif; //if (!empty($err_msg)): ?>
</div>
<br/>
<form id="edit_comments" enctype="multipart/form-data" method="post" action="/<?=$myurl?>" >
<div class="pg my_comments">
    <input class="save_comment" type="submit" value="Save!"/>
    <br/>
    <input type="hidden" name="menu_id" value="<?=$menu_id?>"/>
    <input type="hidden" name="cid" value="<?=$comment_id?>"/>
    <textarea class="user_comment jq_watermark" name="comments" title="Tell me something something about this."><?=$post_comments?></textarea>
    <br/>
    <br/>
    <div class="autocomplete">
        <label for="tags">You make this sound soooo goodo!!  Tell me what's in it!</label><br/>
        <input type="textbox" id="tags" class="jq_watermark" title="taggit"></input>
        <div class="tag_group template">
            <span class="label"></span>
            <input type="hidden" name="add[]" value="1"/>
            <input type="hidden" name="sid[]" value=""/>
            <input type="hidden" name="mid[]" value=""/>
        </div>
    </div>
    <div class="taggits">
        <?php foreach ($taggits as $taggit): ?>
            <div class="tag_group">
                <a href="/menu/comments/<?=$menu_id?>-<?=$slug['menu']?>/<?=$taggit['sid']?>-<?=Util::slugify($taggit['section'])?>/<?=$taggit['mid']?>-<?=Util::slugify($taggit['metadata'])?>">
                    <span class="label">(<?=$taggit['section']?>) <?=$taggit['metadata']?></span>
                </a>
                <input type="hidden" name="add[]" value="1"/>
                <input type="hidden" name="sid[]" value="<?=$taggit['sid']?>"/>
                <input type="hidden" name="mid[]" value="<?=$taggit['mid']?>"/>
            </div>
        <?php endforeach; //foreach ($taggits as $taggit): ?>
    </div>
</div>
</form>
<script type="text/javascript">
    <?php
        // TODO: extract parameter in script tag
        //       http://wowmotty.blogspot.com/2010/04/get-parameters-from-your-script-tag.html
        //       http://feather.elektrum.org/book/src.html
    ?>
    var menu_tags = <?=json_encode($tags)?>;
</script>
