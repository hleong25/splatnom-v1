<?php
$params_mdts[] = array(
    'section_id'=>'',
    'name'=>'',
    'notes'=>'',
    'items'=>array(array(
        'metadata_id'=>'',
        'section_id'=>'',
        'label'=>'',
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
    'is_metadata' => false,
    'info' => $params_info,
    'links' => array(),
    'mdts' => $params_mdts,
);

extract($params, EXTR_SKIP);

?>
<?php
if ($is_admin) {
?>
<form id="edit_mdt" enctype="multipart/form-data" method="post" action="/<?php echo $myurl; ?>" >
<div class="pg">
    <div class="heading">Admin control</div>
    <div class="data">
        <input type="button" value="Delete Menu" onclick="js_menu.purgeMenu('/menu/purge/<?php echo $id; ?>');" />
        <br/>
    </div>
    <br/>
</div>
<?php } // if ($is_admin) ?>
<?php
if ($is_metadata) {
?>
<div class="pg">
    <div class="heading">Metadata control</div>
    <div class="data">
        <input type="submit" value="Save Menu"/>
        <input type="button" value="Export Menu" onclick="js_menu.export('/menu/export/<?php echo $id; ?>/json');"; />
        <input type="button" value="View Menu" onclick="js_menu.view('/menu/view/<?php echo $id; ?>');" />
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
<?php } // if ($is_metadata) ?>
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
    <div class="heading onToggle">Import menu</div>
    <div class="data toggle">
        <span>Choose the import menu file: </span><input class="file" type="file" name="import_file"/><br/>
        <input class="chkImport" type="checkbox" name="import_file_opts[]" value="infos"/><span>Overwrite business info</span><br/>
        <input class="chkImport" type="checkbox" name="import_file_opts[]" value="links"/><span>Append links</span><br/>
        <input class="chkImport" type="checkbox" name="import_file_opts[]" value="menus"/><span>Append menus</span><br/>
    </div>
</div>
<div class="pg pg_bottom">
    <div class="heading onToggle">Images</div>
    <div class="data toggle">
        <div class="new_imgs">
        <?php
            foreach ($imgs as $img)
            {
                $filename = $img['filename'];
                $width = $img['width'];
                $height = $img['height'];

                $img_link = "/images/menu/{$id}/{$filename}";
                $thumbnail_link = "/images/menu_sm/{$id}/{$filename}";

                $thumbnail_size = ImageresizeUtil::resizeDimension($width, $height, 100, 100);

                echo<<<EOHTML
                    <a href="$img_link" target="_blank"><img class="menu" src="$thumbnail_link" width="{$thumbnail_size['width']}" height="{$thumbnail_size['height']}" /></a>
EOHTML;
            }
        ?>
        </div>
    </div>
</div>
<div class="pg pg_bottom">
    <div class="heading onToggle">Links</div>
    <div class="data toggle">
        <?php
            if (empty($links))
                $links[] = array('url'=>'', 'label'=>'');

            foreach ($links as $link)
            {
                echo<<<EOHTML
                    <div class="link_item">
                        <input type="hidden" name="link[]" value="@link@"/>
                        <input type="text" class="jq_watermark" name="link[]" title="Link" value="{$link['url']}"/>
                        <input type="text" class="jq_watermark" name="link[]" title="Label" value="{$link['label']}"/>
                        <input type="button" class="link_add" value="Add link" />
                        <input type="button" class="link_remove" value="Remove link" />
                    </div>
EOHTML;
            }
        ?>
    </div>
</div>
<div class="pg pg_bottom">
    <div class="heading onToggle">Business Information</div>
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
<?php foreach ($mdts as $mdt) { ?>
<div class="pg pg_bottom menu">
    <div class="menu_ctrl">
        <input type="button" value="Move up" onclick="js_menu.moveMenu(this, -1);" />
        <input type="button" value="Move down" onclick="js_menu.moveMenu(this, 1);" />
    </div>
    <div class="heading onToggle">
        Menu <span class="menu_name"><?php echo $mdt['name']; ?></span>
    </div>
    <div class="data toggle">
        <div class="pg_bottom group_info">
            <!-- <?php echo "menu_id={$id} AND section_id={$mdt['section_id']}"; ?> -->
            <input type="hidden" name="mdt[]" value="@mdt@"/>
            <input type="hidden" name="mdt[]" value="<?php echo $mdt['section_id']; ?>"/>
            <input type="text" class="jq_watermark menu_name" name="mdt[]" title="Group (ie. Appetizers)" value="<?php echo $mdt['name']; ?>" />
            <br/>
            <input class="jq_watermark" type="text" name="mdt[]" title="Group notes" value="<?php echo $mdt['notes']; ?>" />
        </div>
        <div class="pg_bottom subheading">Menu items</div>
        <div class="menu_group">
            <span class="menu_group_info">Item can be parsed with {item}[@@{price}[@@{notes}]].<br/>Ctrl+Up/Down to move up/down.</span><br/><br/>
            <?php foreach ($mdt['items'] as $item_idx => $item) { ?>
            <div class="menu_item">
                <!-- <?php echo "menu_id={$id} AND section_id={$mdt['section_id']} AND ordinal_id={$item_idx}"; ?> -->
                <input type="hidden" name="mdt[]" value="@item@"/>
                <input type="hidden" name="mdt[]" value="<?php echo $item['metadata_id']; ?>"/>
                <input type="text" class="jq_watermark" name="mdt[]" title="Label" value="<?php echo $item['label']; ?>"/>
                <input type="text" class="jq_watermark" name="mdt[]" title="Price" value="<?php echo $item['price']; ?>"/>
                <input type="text" class="jq_watermark" name="mdt[]" title="Notes" value="<?php echo $item['notes']; ?>"/>
                <input type="button" class="menuitem_add" value="Add item" />
                <input type="button" class="menuitem_remove" value="Remove item" />
            </div>
            <?php } // foreach ($mdt['items'] as $item_idx => $item) ?>
        </div>
    </div>
    <div class="pg_bottom controller toggle">
        <input type="submit" value="Save Menu"/>
        <input type="button" class="menu_add" value="Add menu" />
        <input type="button" class="menu_remove" value="Remove menu" />
    </div>
    <input type="hidden" name="mdt[]" value="@end_of_mdt@"/>
</div>
<?php } // foreach ($mdts as $idx => $mdt) ?>
</form>
<?php if(!empty($dbg)) { ?>
<div class="pg"><br/><br/><pre><?php var_export($dbg); ?></pre></div>
<?php } ?>

