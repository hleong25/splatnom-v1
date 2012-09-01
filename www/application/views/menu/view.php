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
    'location' => '',
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

// setup status info
$is_ready = false;
$curr_status = 'not ready';
foreach ($info['status'] as $info_status)
{
    $status = $info_status['status'];
    $is_selected = $info_status['selected'] == 1;

    if ($status === 'ready')
        $is_ready = $is_selected;

    if ($is_selected)
        $curr_status = $status;
}
?>
<div class="pg pg_bottom search">
<form id="searchit" method="get" action="/menu/search">
    <label><span class="hint">Look for 'fish tacos' or 'Japanese'</span>
        <input class="watermark query" type="text" name="query" placeholder="Search" value=""/>
    </label>
    <label><span class="hint">Zip code</span>
        <input class="watermark location" type="text" name="location" placeholder="Zip code" value="<?=$location?>"/>
    </label>
    <label><span class="hint">&nbsp;</span>
        <button class="button search" type="submit">Search</button>
    </label>
</form>
</div>

<?php if (!$is_ready): ?>
<div class="notready">
    <span>The status of this menu is '<?=$curr_status?>'.<br/>Just like the cake... this menu is a lie.</span>
</div>
<?php endif; ?>

<div class="pg biz_info">
<table>
<tr>
    <td class="spacer">
        <p class="name"><a name="info"><?=$info['name']?></a></p>
        <p class="details"><?=nl2br($info['notes'])?></p>
    </td>
</tr>
<tr>
    <td class="spacer" style="width: 50%;">
        <p class="address"><?=nl2br($info['address'])?></p>
        <p class="phone"><?=nl2br($info['numbers'])?></p>
    </td>
    <td>
        <p class="hours"><?=nl2br($info['hours'])?></p>
    </td>
</tr>
</table>
</div>

<?php if ($is_metadata): ?>
<div class="pg ismdt">
    <a class="button" href="/export/menus/<?=$id?>">export menu</a>
    <a class="button" href="/menu/edit_metadata/<?=$id?>">edit menu</a>
</div>
<?php endif; //if ($is_metadata) ?>

<div class="pg themenu">
<table><tbody><tr>
<td class="sidebar">
    <div class="navbar">
        <div class="nav_header">
            <img src="/img/menu.home.black.gif"/>
            <a class="nav_item" href="#0"><span>Our Menus</span></a>
        </div>
        <?php foreach ($mdts as $mdt):
            $section_id = $mdt['section_id'];
            $section_name = $mdt['name'];
        ?>
        <div class="nav_item">
            <p>
                <img src="/img/menu.forkit.light.gif"/>
                <a class="nav_item" href="#<?=$section_id?>"><?=$section_name?></a>
            </p>
        </div>
        <?php endforeach; ?>
    </div>
</td>
<td class="menu">
<?php foreach ($mdts as $mdt):
    $section_id = $mdt['section_id'];
    $section_name = $mdt['name'];
    $slug['section'] = Util::slugify($section_name);

    $base_section_url = "{$id}-{$slug['menu']}/{$section_id}-{$slug['section']}";
    $section_image_url = "/menu/images/{$base_section_url}";
?>
    <div class="menu <?=$section_id?>">
        <a name="<?=$section_id?>"/>
        <div class="menu_header">
            <p class="name"><?=$section_name?></p>
            <p class="link_imgs clearfix">View All</p>
        </div>
        <p class="menu_notes"><?=nl2br($mdt['notes'])?></p>
        <div class="items">
        <?php foreach ($mdt['items'] as $item):

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
            $item_image_url = "/menu/images/{$base_item_url}";

            // if spicy
            $img_spicy = '';
            if ($item['is_spicy'])
                $img_spicy = '<img class="item_attr" src="/img/spicy.png" alt="Spicy!" title="Spicy!"/>';

            $item_notes = nl2br($item['notes']);
        ?>
            <div class="group clearfix">
                <div class="g_panel">
                    <span class="item_imgs"><img src="/img/menu.imgs.light.gif"/></span>
                    <span class="forkit"><img src="/img/menu.forkit.light.gif"/></span>
                </div>
                <div class="g_info">
                    <p class="g_info1">
                        <span class="label"><a href="<?=$item_image_url?>"><?=$metadata_label?></a><?=$img_spicy?></span>
                        <span class="price clearfix"><?=$item['price']?></span>
                    </p>
                    <div class="g_info2">
                        <div class="notes <?=$notes_css?>"><?=$item_notes?></div>
                    </div>
                </div>
            </div>
        <?php endforeach; //foreach ($mdt['items'] as $item) ?>
        </div>
    </div>
<?php endforeach; // foreach ($mdts as $mdt) ?>
</td>
</tr></tbody></table>
</div>

<?php return; ?>

<div class="pg">
<?php if ($is_metadata): ?>
<div class="ismdt">
    <a class="button" href="/export/menus/<?=$id?>">export menu</a>
    <a class="button" href="/menu/edit_metadata/<?=$id?>">edit menu</a>
</div>
<?php endif; //if ($is_metadata) ?>

<div class="biz_info">
    <div class="name"><a name="info"><?=$info['name']?></a></div>
    <div class="details"><?=nl2br($info['notes'])?></div>
    <?=$this->addAddThis()?>
    <br/>
    <div class="address"><?=nl2br($info['address'])?></div>
    <br/>
    <div class="phone"><?=nl2br($info['numbers'])?></div>
    <br/>
    <div class="hours"><?=nl2br($info['hours'])?></div>
    <br/>
    <div class="images"><a href="/menu/images/<?=$id?>-<?=$slug['menu']?>">Images</a></div>
</div>

<div class="links">
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

    <?php /* no comments for now
    <div class="user_actions notoc">
        <br/>
        <a class="button" href="/menu/edit_comments/<?=$id?>-<?=$slug['menu']?>">Add comments</a>
        <a class="button" href="/menu/comments/<?=$id?>-<?=$slug['menu']?>">View comments</a>
        <br/>
        <br/>
    </div>
    */?>

<div class="navbar">
<?php // if you change this, you must change stickynavbar?>
    <div class="nav_item">
        <a class="nav_item" href="#info">Business Info</a>
    </div>
<?php foreach ($mdts as $mdt):
    $section_id = $mdt['section_id'];
    $section_name = $mdt['name'];
?>
    <div class="nav_item">
        <span class="lnkspc">&nbsp;|&nbsp;</span>
        <a class="nav_item" href="#<?=$section_id?>"><?=$section_name?></a>
    </div>
<?php endforeach; ?>
</div>

<div class="pg stickynavbar" style="display: none;">
<?php // if you change this, you must change navbar?>
    <div class="nav_item">
        <a class="nav_item" href="#info">Business Info</a>
    </div>
<?php foreach ($mdts as $mdt):
    $section_id = $mdt['section_id'];
    $section_name = $mdt['name'];
?>
    <div class="nav_item">
        <span class="lnkspc">&nbsp;|&nbsp;</span>
        <a class="nav_item" href="#<?=$section_id?>"><?=$section_name?></a>
    </div>
<?php endforeach; ?>
</div>

<div class="menus">
<?php foreach ($mdts as $mdt):
    $section_id = $mdt['section_id'];
    $section_name = $mdt['name'];
    $slug['section'] = Util::slugify($section_name);

    $base_section_url = "{$id}-{$slug['menu']}/{$section_id}-{$slug['section']}";
    $section_image_url = "/menu/images/{$base_section_url}";
?>
    <div class="menu">
        <a name="<?=$section_id?>"/>
        <div class="info zhead">
            <div class="h_name">
                <a href="<?=$section_image_url?>"><?=$section_name?></a>
            </div>
            <div class="h_notes"><?=nl2br($mdt['notes'])?></div>
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
                $item_image_url = "/menu/images/{$base_item_url}";

                // if spicy
                $img_spicy = '';
                if ($item['is_spicy'])
                    $img_spicy = '<img class="item_attr" src="/img/spicy.png" alt="Spicy!" title="Spicy!"/>';

                $item_notes = nl2br($item['notes']);

                echo<<<EOHTML
                    <div class="group clearfix {$css}">
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
                            <div class="g_info1">
                                <span class="label"><a href="{$item_image_url}">{$metadata_label}</a>{$img_spicy}</span>
                                <span class="price clearfix">{$item['price']}</span>
                            </div>
                            <div class="g_info2">
                                <div class="notes {$notes_css}">{$item_notes}</div>
                            </div>
                        </div>
                    </div>
EOHTML;
            }
        ?>
        </div>
    </div>
<?php endforeach; // foreach ($mdts as $mdt) ?>
</div>
