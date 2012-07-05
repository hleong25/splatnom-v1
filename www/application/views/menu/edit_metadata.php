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
    'total_items' => 0,
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
<div class="edit"><form id="edit_mdt" enctype="multipart/form-data" method="post" action="/<?=$myurl?>">

    <div class="action">
        <input class="button" type="button" value="Refresh" data-action="refresh" data-url="/<?=$myurl?>" />
        <input class="button" type="button" value="View Menu" data-action="view" data-url="/menu/view/<?=$id?>" />
        <input class="button" type="button" value="Export Menu" data-action="export" data-url="/export/menus/<?=$id?>" />
        <hr/>
        <input id="force_db_fetch" style="width: 1em; display: inline;" type="checkbox" name="force_reload" />
        <label for="force_db_fetch">refresh db</label>
        <input class="button" type="submit" value="Save Menu" data-action="save" />
        <?php if($is_admin): ?>
            <input class="button" type="button" value="Delete Menu" data-action="delete" data-url="/menu/purge/<?=$id?>" />
        <?php endif; //if($is_admin): ?>
        <hr/>
        <span>Sections</span>
        <input class="button" type="button" value="Hide all" data-action="hideall" />
        <input class="button" type="button" value="Show all" data-action="showall" />
        <hr/>
        <div class="status">
            <span>Status: </span>
            <?php if ($is_admin): ?>
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
            <?php else:
                foreach ($info['status'] as $status)
                {
                    $label = $status['status'];
                    $selected = $status['selected'] == 1;

                    if ($status['selected'] == 1)
                    {
                        echo<<<EOHTML
                            <strong>{$status['status']}</strong>
                            <input type="hidden" name="info_status" value="{$status['status']}" />
EOHTML;
                        break;
                    }
                }
            ?>
            <?php endif; ?>
        </div>
        <div class="stats">
            <p>Items: <span id="stats_items"><?=$info['total_items']?><span></p>
        </div>
    </div>

    <div class="reminder">Mission here is to just enter data for now. If thinking too much about db and metadata, make it a textarea.</div>

    <div class="err_msgs">
        <?=implode('<br/>', $err_msgs)?>
    </div>
<?php /*
    NOTE: the import function is no longer supported here
          it will be supported in the /import/list section

    <div class="import">
        <div class="heading onToggle">Import menu</div>
        <div class="data toggle">
            <span>Choose the import menu file: </span><input class="file" type="file" name="import_file"/><br/>
            <input class="chkImport" type="checkbox" name="import_file_opts[]" value="infos"/><span>Overwrite business info</span><br/>
            <input class="chkImport" type="checkbox" name="import_file_opts[]" value="links"/><span>Append links</span><br/>
            <input class="chkImport" type="checkbox" name="import_file_opts[]" value="menus"/><span>Append menus</span><br/>
        </div>
    </div>
*/?>
    <div class="images">
        <div class="heading onToggle">Images <a class="button img_add" href="/images/upload/<?=$id?>">Add photos</a></div>
        <div class="data toggle">
            <div class="new_imgs">
            <?php
                foreach ($imgs as $img)
                {
                    $filename = $img['filename'];

                    $img_link = "/images/get/menu/org/{$id}/{$filename}";
                    $thumbnail_link = "/images/get/menu/sm/{$id}/{$filename}";

                    echo<<<EOHTML
                        <a href="$img_link" target="_blank"><img class="" src="$thumbnail_link" /></a>
EOHTML;
                }
            ?>
            </div>
        </div>
    </div>

    <div class="links">
        <script type="tmpl/link" id="tmpl_link">
            <div class="link_item">
                <input type="hidden" name="link[]" value="@link@"/>
                <input type="text" class="jq_watermark link_url" style="width: 35em;" name="link[]" title="Link" value=""/>
                <input type="text" class="jq_watermark link_lbl" style="width: 15em;" name="link[]" title="Label" value=""/>
                <button class="link_add">Add link</button>
                <button class="link_remove">Remove link</button>
            </div>
        </script>
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
                            <input type="text" class="jq_watermark" style="width: 35em;" name="link[]" title="Link" value="{$link['url']}"/>
                            <input type="text" class="jq_watermark" style="width: 15em;" name="link[]" title="Label" value="{$link['label']}"/>
                            <button class="link_add">Add link</button>
                            <button class="link_remove">Remove link</button>
                        </div>
EOHTML;
                }
            ?>
        </div>
    </div>

    <div class="info">
        <div class="heading onToggle">Business Information</div>
        <div class="data toggle">
            <input class="jq_watermark" type="text" style="width: 25em;" name="info_name" title="Name of the place" value="<?=$info['name']?>"/>
            <br/>
            <input class="jq_watermark" type="text" style="width: 35em;" name="info_notes" title="Notes" value="<?=$info['notes']?>"/>
            <br/>
            <textarea class="jq_watermark address" rows="5"  name="info_address" title="Address"><?=$info['address']?></textarea>
            <br/>
            <input class="search_address" type="button" value="Google search address" />
            <input class="jq_watermark" type="text" style="width: 400px;" title="javascript get lat long" value="javascript:void(prompt('',gApplication.getMap().getCenter()));"/>
            <br/>
            <input class="jq_watermark" type="text" style="width: 15em;" name="info_latitude" title="Latitude" value="<?=$info['latitude']?>"/>
            <input class="jq_watermark" type="text" style="width: 15em;" name="info_longitude" title="Longitude" value="<?=$info['longitude']?>"/>
            <span class="latlong_info">If latitude is in (###,###) format, it will parse it to lat/long.</span>
            <br/>
            <textarea class="jq_watermark phone_numbers" rows="5"  name="info_numbers" title="Phone numbers"><?=$info['numbers']?></textarea>
            <br/>
            <textarea class="jq_watermark hours" rows="5" name="info_hours" title="Hours of operation"><?=$info['hours']?></textarea>
        </div>
    </div>


<script type="tmpl/menu" id="tmpl_menu">
    <div class="menu">
        <!-- new menu -->
        <div class="heading onToggle">
            <div class="left clearfix">
                <button class="move_up">Move up</button>
                <button class="move_down">Move down</button>
                Menu <span class="menu_name"></span>
            </div>
            <div class="right">
                <button class="menu_add">Add menu</button>
                <button class="menu_remove">Remove menu</button>
            </div>
        </div>
        <div class="data toggle">
            <div class="group_info">
                <!-- new group info -->
                <input type="hidden" name="mdt[]" value="@mdt@"/>
                <input type="hidden" class="sid" name="mdt[]" value=""/>
                <input type="text" class="jq_watermark menu_name" name="mdt[]" title="Group (ie. Appetizers)" value="" />
                <br/>
                <textarea class="jq_watermark menu_notes" name="mdt[]" title="Group notes"></textarea>
            </div>
            <div class="subheading">Menu items</div>
            <div class="menu_group">
                <span class="menu_group_info">Item can be parsed with {item}[@@{price}[@@{notes}[@@{attrs=S}]]].<br/>Ctrl+Up/Down to move up/down.</span><br/>
            </div>
        </div>
        <input type="hidden" name="mdt[]" value="@end_of_mdt@"/>
    </div>
</script>

<script type="tmpl/item" id="tmpl_item">
    <div class="menu_item">
        <!-- new menu item -->
        <button class="btnitem item_up">Move down</button>
        <button class="btnitem item_down">Move down</button>
        <input type="hidden" name="mdt[]" value="@item@"/>
        <input type="hidden" class="mid" name="mdt[]" value=""/>
        <textarea class="jq_watermark item_label" name="mdt[]" title="Label" rows="1"></textarea>
        <textarea class="jq_watermark item_price" name="mdt[]" title="Price" rows="1"></textarea>
        <textarea class="jq_watermark item_notes" name="mdt[]" title="Notes" rows="1"></textarea>
        <input type="hidden" name="mdt[]" value="@item_attr@"/>
        <input type="hidden" name="mdt[]" value="is_spicy"/>
        <input type="checkbox" name="mdt[]" ><img src="/img/spicy.png" alt="Spicy!" title="Spicy!"/></input>
        <button class="btnitem item_add">Add item</button>
        <button class="btnitem item_remove">Remove item</button>
    </div>
</script>

<?php foreach ($mdts as $mdt): ?>
    <div class="menu">
        <div class="heading onToggle">
            <div class="left clearfix">
                <button class="move_up">Move up</button>
                <button class="move_down">Move down</button>
                Menu <span class="menu_name"><?php echo $mdt['name']; ?></span>
            </div>
            <div class="right">
                <button class="menu_add">Add menu</button>
                <button class="menu_remove">Remove menu</button>
            </div>
        </div>
        <div class="data toggle">
            <div class="group_info">
                <!-- <?php echo "menu_id={$id} AND section_id={$mdt['section_id']}"; ?> -->
                <input type="hidden" name="mdt[]" value="@mdt@"/>
                <input type="hidden" class="sid" name="mdt[]" value="<?=$mdt['section_id']?>"/>
                <input type="text" class="jq_watermark menu_name" name="mdt[]" title="Group (ie. Appetizers)" value="<?=$mdt['name']?>" />
                <br/>
                <textarea class="jq_watermark menu_notes" name="mdt[]" title="Group notes"><?=$mdt['notes']?></textarea>
            </div>
            <div class="subheading">Menu items</div>
            <div class="menu_group">
                <span class="menu_group_info">Item can be parsed with {item}[@@{price}[@@{notes}[@@{attrs=S}]]].<br/>Ctrl+Up/Down to move up/down.</span><br/>
                <?php foreach ($mdt['items'] as $item_idx => $item): ?>
                <div class="menu_item">
                    <!-- <?php echo "menu_id={$id} AND section_id={$mdt['section_id']} AND ordinal_id={$item_idx}"; ?> -->
                    <button class="btnitem item_up">Move down</button>
                    <button class="btnitem item_down">Move down</button>
                    <input type="hidden" name="mdt[]" value="@item@"/>
                    <input type="hidden" class="mid" name="mdt[]" value="<?=$item['metadata_id']?>"/>
                    <textarea class="jq_watermark item_label" name="mdt[]" title="Label" rows="1"><?=$item['label']?></textarea>
                    <textarea class="jq_watermark item_price" name="mdt[]" title="Price" rows="1"><?=$item['price']?></textarea>
                    <textarea class="jq_watermark item_notes" name="mdt[]" title="Notes" rows="1"><?=$item['notes']?></textarea>
                    <input type="hidden" name="mdt[]" value="@item_attr@"/>
                    <input type="hidden" name="mdt[]" value="is_spicy"/>
                    <input type="checkbox" name="mdt[]" <?=(!empty($item['is_spicy']))?'CHECKED':''?>><img src="/img/spicy.png" alt="Spicy!" title="Spicy!"/></input>
                    <button class="btnitem item_add">Add item</button>
                    <button class="btnitem item_remove">Remove item</button>
                </div>
                <?php endforeach; // foreach ($mdt['items'] as $item_idx => $item) ?>
            </div>
        </div>
        <input type="hidden" name="mdt[]" value="@end_of_mdt@"/>
    </div>
<?php endforeach; //foreach ($mdts as $mdt): ?>
</form></div>
