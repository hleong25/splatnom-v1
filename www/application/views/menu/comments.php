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
    'comments'=>array(),
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
    <span class="menu_subnav">&raquo;</span> <a href="/menu/view/<?=$menu_id?>-<?=$slug['menu']?>">Menu</a>
    <?php if (!empty($menu_str)): ?>
        <span class="menu_subnav">&raquo;</span> <a href="/menu/comments/<?=$menu_id?>-<?=$slug['menu']?>"><?=$menu_str?></a>
    <?php endif; //if (!empty($menu_str): ?>
    <?php if (!empty($section_str)): ?>
        <span class="menu_subnav">&raquo;</span> <a href="/menu/comments/<?=$menu_id?>-<?=$slug['menu']?>/<?=$section_id?>-<?=$slug['section']?>"><?=$section_str?></a>
    <?php endif; //if (!empty($section_str): ?>
    <?php if (!empty($item_str)): ?>
        <span class="menu_subnav">&raquo;</span> <a href="/menu/comments/<?=$menu_id?>-<?=$slug['menu']?>/<?=$section_id?>-<?=$slug['section']?>/<?=$item_id?>-<?=$slug['item']?>"><?=$item_str?></a>
    <?php endif; //if (!empty($item_str): ?>
    <br/>
</div>
<div class="pg add_comments">
    <?php if (!empty($section_str) && !empty($item_str)): ?>
        <span>Add comments to <a href="/menu/edit_comments/<?=$menu_id?>-<?=$slug['menu']?>/<?=$section_id?>-<?=$slug['section']?>/<?=$item_id?>-<?=$slug['item']?>">(<?=$section_str?>) <?=$item_str?></a></span>
    <?php else: ?>
        <span>Add comments to <a href="/menu/edit_comments/<?=$menu_id?>-<?=$slug['menu']?>"><?=$menu_str?></a></span>
    <?php endif; //if (!empty($section_str) && !empty($item_str)): ?>
</div>

<div class="pg comment">
</div>

<div class="pg comments_list">
    <?php if (empty($comments)): ?>
        <span>Nothing to talk about... maybe you can help by adding a comment. =D</span>
    <?php else: //if (empty($comments)): ?>
        <br/>
        <?php
            foreach ($comments as $item_comment)
            {
                $comment_id = $item_comment['comment_id'];
                $name = Util::formatViewingName($item_comment['username'], $item_comment['firstname'], $item_comment['lastname']);

                $img_id = $item_comment['img_id'];
                $img_file = $item_comment['file_img'];
                $thumbnail_link = "/images/get/menu/md/{$menu_id}/{$img_file}";

                if (empty($img_id))
                    continue;

                echo<<<EOHTML
                    <div class="" style="margin: 1em; border: 1px solid black; ">
                        <img class="" style="border: 1px solid black;" src="{$thumbnail_link}"/>
                    </div>
EOHTML;
            }
        ?>
    <?php endif; //if (empty($comments)): ?>

</div>
<?php
// TODO: remove below
return
?>
<div class="pg comments">
    <?php if (empty($comments)): ?>
        <span>Nothing to talk about... maybe you can help by adding a comment. =D</span>
    <?php else: //if (empty($comments)): ?>
        <br/>
        <?php
            foreach ($comments as $item_comment)
            {
                $comment_id = $item_comment['comment_id'];
                $name = Util::formatViewingName($item_comment['username'], $item_comment['firstname'], $item_comment['lastname']);

                $talk = nl2br($item_comment['comment']);

                $ctaggits = '';
                if (isset($taggits[$comment_id]))
                {
                    // we have taggits for this comment!
                    foreach ($taggits[$comment_id] as $ct)
                    {
                        $slug_section = $ct['sid'].'-'.Util::slugify($ct['section']);
                        $slug_metadata = $ct['mid'].'-'.Util::slugify($ct['metadata']);

                        $ctaggits .=<<<EOTAGHTML
                            <div class="taggit">
                                <a href="/menu/comments/{$menu_id}-{$slug['menu']}/{$slug_section}/{$slug_metadata}">({$ct['section']}) {$ct['metadata']}</a>
                            </div>
EOTAGHTML;
                    }
                }

                echo<<<EOHTML
                    <div class="comment">
                        <div class="user">
                            {$name} says:
                        </div>
                        <div class="says">
                            {$talk}
                        </div>
                        <div class="taggits">{$ctaggits}</div>
                    </div>
                    <hr/>
EOHTML;
            }
        ?>
    <?php endif; //if (empty($comments)): ?>
</div>
