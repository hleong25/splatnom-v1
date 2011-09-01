<?php
$toggleEvent  = 'onclick="js_admin.toggleOnClick(this); "';
$toggleEvent .= 'onmouseover="js_admin.toggleOnHoverIn(this); "';
$toggleEvent .= 'onmouseout="js_admin.toggleOnHoverOut(this); "';
?>
<form id="staging" enctype="multipart/form-data" method="post" action="/admin/staging/<?php echo $staging_id; ?>" onsubmit="return js_admin.formOnSubmit(this);">
<div class="pg pg_bottom" style="text-align: right;">
    <input type="button" value="Refresh" onclick="location.href='/admin/staging/<?php echo $staging_id; ?>'"/>
    <br/>
    <span>Mission here is to just enter data for now. If thinking too much about db and metadata, make it a textarea.</span>
</div>
<div class="pg pg_bottom">
    <div class="heading onToggle" <?php echo $toggleEvent; ?>>Information</div>
    <div class="data toggle">
        <span>staging id: </span><span><?php echo $staging_id; ?></span>
        <br/>
        <span>website: </span><a href="http://<?php echo $site; ?>" target="_blank"><?php echo $site; ?></a>
        <br/>
        <div class="new_imgs">
        <?php
            foreach ($imgs as $img)
            {
                $img_link = "/ws/getimage/{$img}";
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
        <input class="jq_watermark" type="text" name="info_name" title="Name of the place"/>
        <br/>
        <input class="jq_watermark" type="text" name="info_addy1" title="Address or Intersection 1"/>
        <input class="jq_watermark" type="text" name="info_addy2" title="Address or Intersection 2"/>
        <br/>
        <input class="jq_watermark" type="text" name="info_city" title="City"/>
        <input class="jq_watermark" type="text" name="info_state" title="State"/>
        <input class="jq_watermark" type="text" name="info_zip" title="Zip"/>
        <br/>
        <textarea class="jq_watermark phone_numbers" rows="5"  name="info_numbers" title="Phone numbers"></textarea>
        <br/>
        <textarea class="jq_watermark hours" rows="5" name="info_hours" title="Hours of operation"></textarea>
    </div>
</div>
<?php foreach ($menus as $idx => $menu) { ?>
<div class="pg pg_bottom menu">
    <div class="heading onToggle" <?php echo $toggleEvent; ?>>Menu <span class="menu_name"><?php echo $menu['name']; ?></span>
    </div>
    <div class="pg_bottom controller">
        <input type="button" value="Add menu" onclick="js_admin.addNewMenu(this);" />
        <input type="button" value="Remove menu" onclick="js_admin.removeNewMenu(this);" />
    </div>
    <div class="data toggle">
        <div class="pg_bottom group_info">
            <input type="hidden" name="menu[]" value="@menu@"/>
            <input class="jq_watermark" type="text" name="menu[]" title="Group (ie. Appetizers)" onchange="js_admin.menuNameOnChange(this);" value="<?php echo $menu['name']; ?>" />
            <br/>
            <input class="jq_watermark" type="text" name="menu[]" title="Group notes" value="<?php echo $menu['notes']; ?>" />
        </div>
        <div class="pg_bottom subheading onToggle" <?php echo $toggleEvent; ?>>Menu items</div>
        <div class="menu_group toggle">
            <?php foreach ($menu['items'] as $item_idx => $item) { ?>
            <div class="menu_item">
                <input type="hidden" name="menu[]" value="@item@"/>
                <input class="jq_watermark" type="text" name="menu[]" title="Item" value="<?php echo $item['item']; ?>"/>
                <input class="jq_watermark" type="text" name="menu[]" title="Price" value="<?php echo $item['price']; ?>"/>
                <input class="jq_watermark" type="text" name="menu[]" title="Notes" value="<?php echo $item['notes']; ?>"/>
                <input type="button" value="Add item" onclick="js_admin.addNewMenuItem(this);" />
                <input type="button" value="Remove item" onclick="js_admin.removeNewMenuItem(this);" />
            </div>
            <?php } // foreach ($menu['items'] as $item_idx => $item) ?>
        </div>
    </div>
    <input type="hidden" name="menu[]" value="@end_of_menu@"/>
</div>
<?php } // foreach ($menus as $idx => $menu) ?>
</form>
<div class="pg"><br/><br/><pre><?php var_export($dbg); ?></pre></div>