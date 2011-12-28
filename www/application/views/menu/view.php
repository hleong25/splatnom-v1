<?php

$params_info = array(
    'status'=>array(),
    'name'=>'',
    'notes'=>'',
    'address'=>'',
    'latitude'=>0,
    'longitude'=>0,
    'numbers'=>'',
    'hours'=>'',
);

$params = array(
    'dbg' => array(),
    'is_metadata' => false,
    'info' => $params_info,
    'links' => array(),
    'mdts' => array(),
    'forkits' => array(),
);

extract($params, EXTR_SKIP);

function forkit_helper($forkits, $id, $section_id, $metadata_id)
{
    if (isset($forkits[$section_id][$metadata_id]))
    {
        $forkit = $forkits[$section_id][$metadata_id];

        if ($forkit['me'])
        {
            // if this forkit is me... then the "after" is everything before me
            $forkit_url = array
            (
                'now'   => "/menu/unforkit/{$id}/{$section_id}/{$metadata_id}",
                'after' => "/menu/forkit/{$id}/{$section_id}/{$metadata_id}",
            );

            $forkit_css = array
            (
                'now'   => 'forkit',
                'after' => '',
            );

            $forkit_cnt = array
            (
                'now'   => $forkit['cnt'],
                'after' => $forkit['cnt']-1,
            );
        }
        else
        {
            // this forkit is not me, so the "after" is everything after me
            $forkit_url = array
            (
                'now'   => "/menu/forkit/{$id}/{$section_id}/{$metadata_id}",
                'after' => "/menu/unforkit/{$id}/{$section_id}/{$metadata_id}",
            );

            $forkit_css = array
            (
                'now'   => '',
                'after' => 'forkit',
            );

            $forkit_cnt = array
            (
                'now'   => $forkit['cnt'],
                'after' => $forkit['cnt']+1,
            );
        }

    }
    else
    {
        $forkit_url = array
        (
            'now'   => "/menu/forkit/{$id}/{$section_id}/{$metadata_id}",
            'after' => "/menu/unforkit/{$id}/{$section_id}/{$metadata_id}",
        );

        $forkit_css = array
        (
            'now'   => '',
            'after' => 'forkit',
        );

        $forkit_cnt = array
        (
            'now'   => '',
            'after' => 1,
        );
    }

    return array($forkit_url, $forkit_css, $forkit_cnt);
}

$slug = array
(
    'menu'=>Util::slugify($info['name']),
    'section'=>'',
    'item'=>'',
);

?>
<?php if ($is_metadata): ?>
<div class="pg pg_bottom ismdt">
    <a class="button" href="/menu/export/<?=$id?>/json">export menu</a>
    <a class="button" href="/menu/edit_metadata/<?=$id?>">edit menu</a>
</div>
<?php endif; //if ($is_metadata) ?>
<div class="pg biz_info">
    <div class="name"><?=$info['name']?></div>
    <div class="details"><?=nl2br($info['notes'])?></div>
    <br/>
    <div class="address"><?=nl2br($info['address'])?></div>
    <br/>
    <div class="phone"><?=nl2br($info['numbers'])?></div>
    <br/>
    <div class="hours"><?=nl2br($info['hours'])?></div>
</div>
<div class="pg links">
<?php
    foreach ($links as $link)
    {
        $label = $link['label'];
        $url = $link['url'];

        if (empty($label)) $label = $url;

        echo<<<EOHTML
            <div class="link">Link: <a target="_blank" href="{$url}">{$label}</a></div>
EOHTML;
    }
?>
</div>
<div class="pg user_actions">
    <br/>
    <a class="button" href="/images/upload/<?=$id?>-<?=$slug['menu']?>">Add photos</a>
    <a class="button" href="/menu/images/<?=$id?>-<?=$slug['menu']?>">View photos</a>
    <br/>
    <br/>
    <a class="button" href="/menu/edit_comments/<?=$id?>-<?=$slug['menu']?>">Add comments</a>
    <a class="button" href="/menu/comments/<?=$id?>-<?=$slug['menu']?>">View comments</a>
    <br/>
    <br/>
</div>
<div class="pg menus">
<?php foreach ($mdts as $mdt):
    $section_id = $mdt['section_id'];
    $section_name = $mdt['name'];
    $slug['section'] = Util::slugify($section_name);

    $base_section_url = "{$id}-{$slug['menu']}/{$section_id}-{$slug['section']}";
    $section_comment_url = "/menu/comments/{$base_section_url}";
    $section_photo_url = "/menu/images/{$base_section_url}";
?>
    <div class="menu">
        <div class="info heading">
            <div class="h_name">
                <?=$section_name?>
                <a href="<?=$section_photo_url?>"><img src="/img/camera.png" /></a>
                <a href="<?=$section_comment_url?>"><img src="/img/balloon.png" /></a>
            </div>
            <div class="h_notes"><?=$mdt['notes']?></div>
        </div>
        <div class="items">
        <?php
            $b_alt = false;
            foreach ($mdt['items'] as $item)
            {
                $b_alt = !$b_alt;
                $css = $b_alt ? 'zalt' : '';

                $metadata_id = $item['metadata_id'];
                $metadata_label = $item['label'];
                $slug['item'] = Util::slugify($metadata_label);

                // notes
                $notes_css = empty($item['notes']) ? 'empty' : '';

                // fork its
                $forkit_msg = 'Stick a fork in it if you like this item!';
                list($forkit_url, $forkit_css, $forkit_cnt) = forkit_helper($forkits, $id, $section_id, $metadata_id);
                if ($forkit_cnt['after'] < 1)
                    $forkit_cnt['after'] = '';

                // item links
                $base_item_url = "{$id}-{$slug['menu']}/{$section_id}-{$slug['section']}/{$metadata_id}-{$slug['item']}";
                $item_comment_url = "/menu/comments/{$base_item_url}";
                $item_photo_url = "/menu/images/{$base_item_url}";

                echo<<<EOHTML
                    <div class="group {$css}">
                        <div class="g_panel">
                            <div class="forkit now">
                                <span class="cnt">{$forkit_cnt['now']}</span>
                                <img class="{$forkit_css['now']}" src="/img/fork.png" title="{$forkit_msg}" onclick="js_menu.forkit(this, '{$forkit_url['now']}');"/>
                            </div>
                            <div class="forkit after">
                                <span class="cnt">{$forkit_cnt['after']}</span>
                                <img class="{$forkit_css['after']}" src="/img/fork.png" title="{$forkit_msg}" onclick="js_menu.forkit(this, '{$forkit_url['after']}');"/>
                            </div>
                        </div>
                        <div class="g_info">
                            <div class="label">{$metadata_label}</div>
                            <div class="pictures"><a href="{$item_photo_url}"><img src="/img/camera.png" /></a></div>
                            <div class="comments"><a href="{$item_comment_url}"><img src="/img/balloon.png" /></a></div>
                            <div class="price">{$item['price']}</div>
                            <div class="clear"></div>
                            <div class="notes {$notes_css}">{$item['notes']}</div>
                        </div>
                        <div class="clear"></div>
                    </div>
EOHTML;
            }
        ?>
        </div>
    </div>
<?php endforeach; // foreach ($mdts as $mdt) ?>
</div>
