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
    'addy1'=>'',
    'addy2'=>'',
    'city'=>'',
    'state'=>'',
    'zip'=>'',
    'numbers'=>'',
    'hours'=>'',
);

$params = array(
    'dbg' => array(),
    'err_msgs' => array(),
    'info' => $params_info,
    'mdts' => $params_mdts,
);

extract($params, EXTR_SKIP);

?>
<?php
if (getPermissions('admin')) {
?>
<div class="pg">
    <div class="heading">Admin control</div>
    <div class="data">
        <input type="button" value="Delete Menu" onclick="js_menu.purgeMenu('/menu/purge/<?php echo $id; ?>');" />
    </div>
    <hr />
</div>
<?php } // if (getPermissions('admin')) ?>
<form id="staging" enctype="multipart/form-data" method="post" action="/<?php echo $myurl; ?>" onsubmit="return js_menu.formOnSubmit(this);">
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
        <span>website: </span><a href="http://<?php echo $info['site_addy']; ?>" target="_blank"><?php echo $info['site_addy']; ?></a>
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
    <input type="submit" value="Submit"/>
</div>
<div class="pg pg_bottom">
    <div class="heading onToggle" <?php echo $toggleEvent; ?>>Business Information</div>
    <div class="data toggle">
        <input class="jq_watermark" type="text" name="info_name" title="Name of the place" value="<?php echo $info['name']; ?>"/>
        <br/>
        <input class="jq_watermark" type="text" name="info_addy1" title="Address or Intersection 1" value="<?php echo $info['addy1']; ?>"/>
        <input class="jq_watermark" type="text" name="info_addy2" title="Address or Intersection 2" value="<?php echo $info['addy2']; ?>"/>
        <br/>
        <input class="jq_watermark" type="text" name="info_city" title="City" value="<?php echo $info['city']; ?>"/>
        <input class="jq_watermark" type="text" name="info_state" title="State" value="<?php echo $info['state']; ?>"/>
        <input class="jq_watermark" type="text" name="info_zip" title="Zip" value="<?php echo $info['zip']; ?>"/>
        <br/>
        <textarea class="jq_watermark phone_numbers" rows="5"  name="info_numbers" title="Phone numbers"><?php echo $info['numbers']; ?></textarea>
        <br/>
        <textarea class="jq_watermark hours" rows="5" name="info_hours" title="Hours of operation"><?php echo $info['hours']; ?></textarea>
    </div>
</div>
<?php foreach ($mdts as $idx => $mdt) { ?>
<div class="pg pg_bottom menu">
    <div class="heading onToggle" <?php echo $toggleEvent; ?>>Menu <span class="menu_name"><?php echo $mdt['name']; ?></span>
    </div>
    <div class="pg_bottom controller">
        <input type="button" value="Add menu" onclick="js_menu.addNewMenu(this);" />
        <input type="button" value="Remove menu" onclick="js_menu.removeNewMenu(this);" />
    </div>
    <div class="data toggle">
        <div class="pg_bottom group_info">
            <input type="hidden" name="mdt[]" value="@mdt@"/>
            <input class="jq_watermark" type="text" name="mdt[]" title="Group (ie. Appetizers)" onchange="js_menu.menuNameOnChange(this);" value="<?php echo $mdt['name']; ?>" />
            <br/>
            <input class="jq_watermark" type="text" name="mdt[]" title="Group notes" value="<?php echo $mdt['notes']; ?>" />
        </div>
        <div class="pg_bottom subheading onToggle" <?php echo $toggleEvent; ?>>Menu items</div>
        <div class="menu_group toggle">
            <?php foreach ($mdt['items'] as $item_idx => $item) { ?>
            <div class="menu_item">
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
    <input type="hidden" name="mdt[]" value="@end_of_mdt@"/>
</div>
<?php } // foreach ($mdts as $idx => $mdt) ?>
</form>
<div class="pg"><br/><br/><pre><?php if (isset($dbg)) var_export($dbg); ?></pre></div>

