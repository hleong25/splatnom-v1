<?php
$params = array
(
    'post_comments'=>'',
    'taggits'=>array(),
    'tags'=>array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg">
<form id="useradd" enctype="multipart/form-data" method="post" action="/<?=$myurl?>" >
    <textarea class="comment jq_watermark" name="comments" title="Tell me what you think about this."><?=$post_comments?></textarea>
    <br/>
    <br/>
    <span>Help me visualize it: </span><input type="file" name="img" />
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
</form>
</div>
<script type="text/javascript">
    <?php
        // TODO: extract parameter in script tag
        //       http://wowmotty.blogspot.com/2010/04/get-parameters-from-your-script-tag.html
        //       http://feather.elektrum.org/book/src.html
    ?>
    var menu_tags = <?=json_encode($tags)?>;
</script>
