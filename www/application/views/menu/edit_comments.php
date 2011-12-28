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
        <span>Tagging them to <a href="/menu/images/<?=$menu_id?>-<?=$slug['menu']?>/<?=$section_id?>-<?=$slug['section']?>/<?=$item_id?>-<?=$slug['item']?>">(<?=$section_str?>) <?=$item_str?></a></span>
    <?php endif; //if (!empty($section_str) && !empty($item_str)): ?>
</div>
<form id="edit_comments" enctype="multipart/form-data" method="post" action="/<?=$myurl?>" >
<div class="pg my_comments">
    <input type="hidden" name="mid" value="<?=$menu_id?>"/>
    <input type="hidden" name="cid" value="<?=$comment_id?>"/>
    <textarea class="user_comment jq_watermark" name="comments" title="Tell me something something about this."><?=$post_comments?></textarea>
    <br/>
    <input class="save_comment" type="submit" value="Save!"/>
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
