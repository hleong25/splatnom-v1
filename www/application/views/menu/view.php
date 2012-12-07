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
    'img_taggit_cnt' => array(),
);

extract($params, EXTR_SKIP);

function img_taggit_cnt_helper($img_taggit_cnt, $img_url, $section_id, $metadata_id)
{
    $url_image = "/menu/images/{$img_url}";

    $cnt = @$img_taggit_cnt[$section_id][$metadata_id];

    $out_frag =<<<EOHTML
    <a class="img_mdt" href="%s">
        <img class="img_panel" src="%s"/><span class="img_cnt">%s images</span>
    </a>
EOHTML;

    $img_icon = '/img/menu.imgs.light.sm.gif';

    $out_frag = sprintf(
        $out_frag,
        $img_url, $img_icon, $cnt
    );

    return $out_frag;
}

function forkit_helper($forkits, $menu_id, $section_id, $metadata_id)
{
    $url_forkit   = "/menu/forkit/{$menu_id}/{$section_id}/{$metadata_id}";
    $url_unforkit = "/menu/unforkit/{$menu_id}/{$section_id}/{$metadata_id}";

    $forkit = @$forkits[$section_id][$metadata_id];
    if (empty($forkit))
    {
        $forkit = array('cnt'=>0, 'me'=>false);
    }

    $cnt = $forkit['cnt'];

    $custom_hide_style = 'display: none;';
    $custom_show_style = 'display: inline;';

    $out_frag =<<<eohtml
    <a class="forkit" href="%s" style="%s">
        <img class="img_panel" src="%s" title="fork it if you like it!"/><span class="forkit_cnt">%s forkits</span>
    </a>
eohtml;

    if ($forkit['me'])
    {
        $cnt_after = $cnt - 1;
        if (false && $cnt_after < 1)
            $cnt_after = '';

        $forkit_now = sprintf(
            $out_frag,
            $url_unforkit, $custom_show_style, '/img/menu.forkit.dark.sm.gif', $cnt
        );

        $forkit_after = sprintf(
            $out_frag,
            $url_forkit, $custom_hide_style, '/img/menu.forkit.light.sm.gif', $cnt_after
        );
    }
    else
    {
        $cnt_after = $cnt + 1;
        if (false && $cnt < 1)
            $cnt = '';

        $forkit_now = sprintf(
            $out_frag,
            $url_forkit, $custom_show_style, '/img/menu.forkit.light.sm.gif', $cnt
        );

        $forkit_after = sprintf(
            $out_frag,
            $url_unforkit, $custom_hide_style, '/img/menu.forkit.dark.sm.gif', $cnt_after
        );
    }

    return array($forkit_now, $forkit_after);
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
<?=get_html_searchit($location)?>
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
            <a class="link_imgs" href="<?=$section_image_url?>">View All</a>
        </li>
        <li class="menu_notes">
            <?=nl2br($mdt['notes'])?>
        </li>
        <li class="menu_items">
        <table class="menu_items">
        <?php foreach ($mdt['items'] as $item):

            if ($item['is_hide'])
                continue;

            $row_cnt = 3; // 3 = number of rows when you have name, comments, and panel
            $col_cnt = 2; // 2 = number of columns if there is a single line in price

            $is_header = $item['is_header'];
            $is_nopanel = $item['is_nopanel'];

            $metadata_id = $item['metadata_id'];
            $metadata_label = $item['label'];
            $slug['item'] = Util::slugify($metadata_label);

            // notes
            $notes_css = empty($item['notes']) ? 'empty' : '';

            // fork its
            list($forkit_now, $forkit_after) = forkit_helper($forkits, $id, $section_id, $metadata_id);

            // item links
            $base_item_url = "{$id}-{$slug['menu']}/{$section_id}-{$slug['section']}/{$metadata_id}-{$slug['item']}";
            $item_image_url = "/menu/images/{$base_item_url}";

            // image link with taggit count
            $mdt_image_link = img_taggit_cnt_helper($img_taggit_cnt, $item_image_url, $section_id, $metadata_id);

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

            if (empty($item_notes))
            {
                $row_cnt--; // no notes means no row
            }

            if (substr_count($item['price'], "\n") > 0)
            {
                $col_cnt = 1; // prices are multiple lines, so comments should be 1 column
            }

        ?>
            <?php if ($is_header): ?>
                <tr class="header">
                    <td class="name">
                        <a href="<?=$item_image_url?>"><?=$metadata_label?></a>
                    </td>
                    <td class="price">
                        <?=$item_price?>
                    </td>
                </tr>
            <?php else: ?>
                <tr class="info">
                    <td class="name">
                        <a href="<?=$item_image_url?>"><?=$metadata_label?></a>
                        <?=$img_spicy?>
                        <?=$img_veggie?>
                    </td>
                    <td class="price" rowspan="<?=$row_cnt?>">
                        <?=$item_price?>
                    </td>
                </tr>
                <?php if (!empty($item_notes)): ?>
                <tr class="info">
                    <td class="comments" colspan="<?=$col_cnt?>">
                        <?=$item_notes?>
                    </td>
                </tr>
                <?php endif; ?>
                <tr class="info">
                    <td class="user_actions">
                        <?php if (!$is_nopanel): ?>
                            <?=$forkit_now?>
                            <?=$forkit_after?>
                            <?=$mdt_image_link?>
                        <?php endif; //if (!$is_nopanel): ?>
                    </td>
                </tr>
            <? endif; // if ($is_header): ?>
        <?php endforeach; //foreach ($mdts as $mdt): ?>
        </table>
        </li>
    </ul>
<?php endforeach; // foreach ($mdts as $mdt) ?>
</div>
