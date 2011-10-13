<?php
$toggleEvent  = 'onclick="js_menu.toggleOnClick(this); "';
$toggleEvent .= 'onmouseover="js_menu.toggleOnHoverIn(this); "';
$toggleEvent .= 'onmouseout="js_menu.toggleOnHoverOut(this); "';

$params_mdts[] = array(
    'name'=>'',
    'notes'=>'',
    'items'=>array(array(
        'item'=>'',
        'price'=>'',
        'notes'=>'',
    )),
);

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
    'err_msgs' => array(),
    'is_admin' => false,
    'info' => $params_info,
    'links' => array(),
    'mdts' => $params_mdts,
);

extract($params, EXTR_SKIP);

?>
<?php
if ($is_admin) {
?>
<form id="edit_mdt" enctype="multipart/form-data" method="post" action="/<?php echo $myurl; ?>" onsubmit="return js_menu.formOnSubmit(this);">
<div class="pg">
    <div class="heading">Admin control</div>
    <div class="data">
        <input type="submit" value="Save Menu"/>
        <input type="button" value="View Menu" onclick="js_menu.view('/menu/view/<?php echo $id; ?>');" />
        <input type="button" value="Delete Menu" onclick="js_menu.purgeMenu('/menu/purge/<?php echo $id; ?>');" />
        <input type="button" value="Hide all sections" onclick="js_menu.hideAll();" />
        <input type="button" value="Show all sections" onclick="js_menu.showAll();" />
        <br/>
        <br/>
        <div class="">
            <span>Status: </span>
            <select name="info_status">
                <?php
                    foreach ($info['status'] as $status)
                    {
                        $label = $status['status'];
                        $selected = ($status['selected'] == 1) ? 'selected' : '';

                        echo<<<EOHTML
                            <option value="{$label}" {$selected}>{$label}</option>
EOHTML;
                    }
                ?>
            </select>
        </div>
    </div>
    <hr />
</div>
<?php } // if ($is_admin) ?>
<div class="pg pg_bottom" style="text-align: right;">
    <input type="button" value="Refresh" onclick="location.href='/<?php echo $myurl; ?>'"/>
    <br/>
    <span>Mission here is to just enter data for now. If thinking too much about db and metadata, make it a textarea.</span>
</div>
<div class="pg pg_bottom err_msgs">
<?php
    $errs = implode('<br/>', $err_msgs);
    echo $errs;
?>
</div>
<div class="pg pg_bottom">
    <div class="heading onToggle" <?php echo $toggleEvent; ?>>Images</div>
    <div class="data toggle">
        <div class="new_imgs">
        <?php
            foreach ($imgs as $img)
            {
                $img_link = "/ws/getimage/{$id}@{$img}";
                echo<<<EOHTML
                    <a href="$img_link" target="_blank"><img class="menu" src="$img_link" /></a>
EOHTML;
            }
        ?>
        </div>
    </div>
</div>
<div class="pg pg_bottom">
    <div class="heading onToggle" <?php echo $toggleEvent; ?>>Links</div>
    <div class="data toggle">
        <?php
            if (empty($links))
                $links[] = array('url'=>'', 'label'=>'');

            foreach ($links as $link)
            {
                echo<<<EOHTML
                    <div class="link_item">
                        <input type="hidden" name="link[]" value="@link@"/>
                        <input class="jq_watermark" type="text" name="link[]" title="Link" value="{$link['url']}"/>
                        <input class="jq_watermark" type="text" name="link[]" title="Label" value="{$link['label']}"/>
                        <input type="button" value="Add link" onclick="js_menu.addLink(this);" />
                        <input type="button" value="Remove link" onclick="js_menu.removeLink(this);" />
                    </div>
EOHTML;
            }
        ?>
    </div>
</div>
<div class="pg pg_bottom">
    <div class="heading onToggle" <?php echo $toggleEvent; ?>>Business Information</div>
    <div class="data toggle">
        <input class="jq_watermark" type="text" name="info_name" title="Name of the place" value="<?php echo $info['name']; ?>"/>
        <br/>
        <input class="jq_watermark" type="text" name="info_notes" title="Notes" value="<?php echo $info['notes']; ?>"/>
        <br/>
        <textarea class="jq_watermark address" rows="5"  name="info_address" title="Address"><?php echo $info['address']; ?></textarea>
        <br/>
        <input type="button" value="Google search address" onclick="return js_menu.googleSearchAddress();" />
        <input class="jq_watermark" type="text" style="width: 400px;" title="javascript get lat long" value="javascript:void(prompt('',gApplication.getMap().getCenter()));"/>
        <br/>
        <input class="jq_watermark" type="text" name="info_latitude" title="Latitude" value="<?php echo $info['latitude']; ?>"/>
        <input class="jq_watermark" type="text" name="info_longitude" title="Longitude" value="<?php echo $info['longitude']; ?>"/>
        <span class="latlong_info">If latitude is in (###,###) format, it will parse it to lat/long.</span>
        <br/>
        <textarea class="jq_watermark phone_numbers" rows="5"  name="info_numbers" title="Phone numbers"><?php echo $info['numbers']; ?></textarea>
        <br/>
        <textarea class="jq_watermark hours" rows="5" name="info_hours" title="Hours of operation"><?php echo $info['hours']; ?></textarea>
    </div>
</div>
<?php foreach ($mdts as $idx => $mdt) { ?>
<div class="pg pg_bottom menu">
    <div class="menu_ctrl">
        <input type="button" value="Move up" onclick="js_menu.moveMenu(this, -1);" />
        <input type="button" value="Move down" onclick="js_menu.moveMenu(this, 1);" />
    </div>
    <div class="heading onToggle" <?php echo $toggleEvent; ?>>
        Menu <span class="menu_name"><?php echo $mdt['name']; ?></span>
    </div>
    <div class="data toggle">
        <div class="pg_bottom group_info">
            <!-- <?php echo "menu_id={$id} AND section_id={$idx}"; ?> -->
            <input type="hidden" name="mdt[]" value="@mdt@"/>
            <input class="jq_watermark" type="text" name="mdt[]" title="Group (ie. Appetizers)" onchange="js_menu.menuNameOnChange(this);" value="<?php echo $mdt['name']; ?>" />
            <br/>
            <input class="jq_watermark" type="text" name="mdt[]" title="Group notes" value="<?php echo $mdt['notes']; ?>" />
        </div>
        <div class="pg_bottom subheading">Menu items</div>
        <div class="menu_group">
            <span class="menu_group_info">Item can be parsed with {item}[@@{price}[@@{notes}]].<br/>Ctrl+Up/Down to move up/down.</span><br/><br/>
            <?php foreach ($mdt['items'] as $item_idx => $item) { ?>
            <div class="menu_item">
                <!-- <?php echo "menu_id={$id} AND section_id={$idx} AND ordinal_id={$item_idx}"; ?> -->
                <input type="hidden" name="mdt[]" value="@item@"/>
                <input class="jq_watermark" type="text" name="mdt[]" title="Item" value="<?php echo $item['item']; ?>"/>
                <input class="jq_watermark" type="text" name="mdt[]" title="Price" value="<?php echo $item['price']; ?>"/>
                <input class="jq_watermark" type="text" name="mdt[]" title="Notes" value="<?php echo $item['notes']; ?>"/>
                <input type="button" value="Add item" onclick="js_menu.addMenuItem(this);" />
                <input type="button" value="Remove item" onclick="js_menu.removeMenuItem(this);" />
            </div>
            <?php } // foreach ($mdt['items'] as $item_idx => $item) ?>
        </div>
    </div>
    <div class="pg_bottom controller toggle">
        <input type="submit" value="Save Menu"/>
        <input type="button" value="Add menu" onclick="js_menu.addMenu(this);" />
        <input type="button" value="Remove menu" onclick="js_menu.removeMenu(this);" />
    </div>
    <input type="hidden" name="mdt[]" value="@end_of_mdt@"/>
</div>
<?php } // foreach ($mdts as $idx => $mdt) ?>
</form>
<?php if(!empty($dbg)) { ?>
<div class="pg"><br/><br/><pre><?php var_export($dbg); ?></pre></div>
<?php } ?>

