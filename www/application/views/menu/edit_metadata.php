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
        <input type="button" value="Delete Menu" onclick="js_menu.purgeMenu('/menu/purge/<?php echo $id; ?>');" />
        <input type="button" value="Hide all sections" onclick="js_menu.hideAll();" />
        <input type="button" value="Show all sections" onclick="js_menu.showAll();" />
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
    <div class="heading onToggle" <?php echo $toggleEvent; ?>>Information</div>
    <div class="data toggle">
        <span>id: </span><span><?php echo $id; ?></span>
        <br/>
        <?php
            foreach ($links as $link)
            {
                $url = $link['url'];
                $label = $link['label'];
                if (empty($label))
                    $label = $url;
                echo<<<EOHTML
                    <span>website: </span><a href="{$url}" target="_blank">{$label}</a>
                    <br/>
EOHTML;
            }
        ?>
        <br/>
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
        <input class="jq_watermark" type="text" name="info_latlong" title="Parse Lat/Long" value=""/>
        <br/>
        <input class="jq_watermark" type="text" name="info_latitude" title="Latitude" value="<?php echo $info['latitude']; ?>"/>
        <input class="jq_watermark" type="text" name="info_longitude" title="Longitude" value="<?php echo $info['longitude']; ?>"/>
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
    <div class="pg_bottom controller toggle">
        <input type="submit" value="Save Menu"/>
        <input type="button" value="Add menu" onclick="js_menu.addNewMenu(this);" />
        <input type="button" value="Remove menu" onclick="js_menu.removeNewMenu(this);" />
    </div>
    <div class="data toggle">
        <div class="pg_bottom group_info">
            <!-- <?php echo "menu_id={$id} AND section_id={$idx}"; ?> -->
            <input type="hidden" name="mdt[]" value="@mdt@"/>
            <input class="jq_watermark" type="text" name="mdt[]" title="Group (ie. Appetizers)" onchange="js_menu.menuNameOnChange(this);" value="<?php echo $mdt['name']; ?>" />
            <br/>
            <input class="jq_watermark" type="text" name="mdt[]" title="Group notes" value="<?php echo $mdt['notes']; ?>" />
        </div>
        <div class="pg_bottom subheading onToggle" <?php echo $toggleEvent; ?>>Menu items</div>
        <div class="menu_group toggle">
            <?php foreach ($mdt['items'] as $item_idx => $item) { ?>
            <div class="menu_item">
                <!-- <?php echo "menu_id={$id} AND section_id={$idx} AND ordinal_id={$item_idx}"; ?> -->
                <input type="hidden" name="mdt[]" value="@item@"/>
                <input class="jq_watermark" type="text" name="mdt[]" title="Item" value="<?php echo $item['item']; ?>"/>
                <input class="jq_watermark" type="text" name="mdt[]" title="Price" value="<?php echo $item['price']; ?>"/>
                <input class="jq_watermark" type="text" name="mdt[]" title="Notes" value="<?php echo $item['notes']; ?>"/>
                <input type="button" value="Add item" onclick="js_menu.addNewMenuItem(this);" />
                <input type="button" value="Remove item" onclick="js_menu.removeNewMenuItem(this);" />
            </div>
            <?php } // foreach ($mdt['items'] as $item_idx => $item) ?>
        </div>
    </div>
    <div class="pg_bottom controller toggle">
        <input type="submit" value="Save Menu"/>
        <input type="button" value="Add menu" onclick="js_menu.addNewMenu(this);" />
        <input type="button" value="Remove menu" onclick="js_menu.removeNewMenu(this);" />
    </div>
    <input type="hidden" name="mdt[]" value="@end_of_mdt@"/>
</div>
<?php } // foreach ($mdts as $idx => $mdt) ?>
</form>
<?php if(!empty($dbg)) { ?>
<div class="pg"><br/><br/><pre><?php var_export($dbg); ?></pre></div>
<?php } ?>

