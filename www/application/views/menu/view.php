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
    'imgs' => array(),
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
    <label><span class="hint">Location</span>
        <input class="watermark location" type="text" name="location" placeholder="Location" value="<?=$location?>"/>
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
    <td class="spacer" colspan="2">
        <p class="name"><a href="/<?=$myurl?>"><?=$info['name']?></a></p>
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
<?php foreach ($links as $link):
    $label = $link['label'];
    $url = $link['url'];

    if (empty($label)) $label = $url;
?>
    <tr><td>
        <p class="link">Link: <a target="_blank" href="<?=$url?>"><?=$label?></a></p>
    </td></tr>
<?php endforeach; // foreach ($links as $link): ?>
</table>
</div>

<?php if ($is_metadata): ?>
<div class="pg ismdt">
    <a class="button export" href="/export/menus/<?=$id?>-<?=$slug['menu']?>">export menu</a>
    <a class="button editmenu" href="/menu/edit_metadata/<?=$id?>-<?=$slug['menu']?>">edit menu</a>
</div>
<?php endif; //if ($is_metadata) ?>

<div class="pg themenu clearfix">

    <div class="sidebar pg_bottom">
        <ul class="navbar pg_bottom">
            <li class="nav_header">
                <img src="/img/menu.home.black.gif"/>
                <a class="nav_item" href="#0"><span>Our Menus</span></a>
            </li>
            <?php foreach ($mdts as $mdt):
                $section_id = $mdt['section_id'];
                $section_name = $mdt['name'];
            ?>
            <li class="nav_item">
                <img src="/img/menu.forkit.light.gif"/>
                <a class="nav_item" href="#<?=$section_id?>"><?=$section_name?></a>
            </li>
            <?php endforeach; ?>
        </ul>
        <ul class="img_gallery pg_bottom">
            <li class="img_header">
                <img src="/img/menu.imgs.black.gif"/>
                <a class="img_item" href="/menu/images/<?=$id?>-<?=$slug['menu']?>"><span>Gallery</span></a>
            </li>
            <li class="img_item">
                <?php $img_cnt = 0; foreach ($imgs as $img):
                    $img_cnt++;
                    if ($img_cnt > 6) break; // maximum of 6 images

                    $img_id     = $img['id'];
                    $img_file   = $img['filename'];
                    $img_width  = $img['width'];
                    $img_height = $img['height'];

                    $sm_img_url  = "/images/get/menu/sm/$id/$img_file";
                    $org_img_url = "/menu/images/$id-{$slug['menu']}/$img_file";
                ?>
                    <a href="<?=$org_img_url?>"><img src="<?=$sm_img_url?>"></a>
                <?php endforeach; ?>
            </li>
            <li class="img_item">
                <a class="button addimg" href="/images/upload/<?=$id?>-<?=$slug['menu']?>">Add More Images</a>
            </li>
            <li class="img_item empty"></li>
        </ul>
    </div>

<?php foreach ($mdts as $mdt):
    $section_id = $mdt['section_id'];
    $section_name = $mdt['name'];
    $slug['section'] = Util::slugify($section_name);

    $base_section_url = "{$id}-{$slug['menu']}/{$section_id}-{$slug['section']}";
    $section_image_url = "/menu/images/{$base_section_url}";
?>
    <a name="<?=$section_id?>"></a>
    <ul class="menu <?=$section_id?>">
        <li class="menu_header">
            <span class="name"><?=$section_name?></span>
            <span class="link_imgs">View All</span>
        </li>
        <li class="menu_notes">
            <?=nl2br($mdt['notes'])?>
        </li>
        <li class="menu_items">
        <table class="menu_items">
        <?php foreach ($mdt['items'] as $item):

            if ($item['is_hide'])
                continue;

            $is_header = $item['is_header'];
            $is_nopanel = $item['is_nopanel'];

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

            // if veggie
            $img_veggie = '';
            if ($item['is_veggie'])
                $img_veggie = '<img class="item_attr" src="/img/veggie.png" alt="Veggie!" title="Veggie!"/>';

            $item_price = nl2br($item['price']);
            $item_notes = nl2br($item['notes']);

        ?>
            <?php if ($is_header): ?>
                <tr class="header">
                    <td class="item_panel">
                    </td>
                    <td class="item_info1">
                        <span class="goright clearfix"><?=$item_price?></span>
                        <?=$metadata_label?>
                    </td>
                </tr>
            <?php else: ?>
                <tr class="item_info1">
                    <td class="item_panel" rowspan="2">
                        <?php if (!$is_nopanel): ?>
                            <img src="/img/menu.imgs.light.gif"/>
                            <img src="/img/menu.forkit.light.gif"/>
                        <?php endif; //if (!$is_nopanel): ?>
                    </td>
                    <td class="item_info1">
                        <span class="goright clearfix"><?=$item_price?></span>
                        <?=$metadata_label?>
                        <?=$img_spicy?>
                        <?=$img_veggie?>
                    </td>
                </tr>
                <tr class="item_info2">
                    <td class="item_info2">
                        <?=$item_notes?>
                    </td>
                </tr>
            <? endif; // if ($is_header): ?>
        <?php endforeach; //foreach ($mdts as $mdt): ?>
        </table>
        </li>
    </ul>
<?php endforeach; // foreach ($mdts as $mdt) ?>
</div>
