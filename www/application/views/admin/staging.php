<form id="staging" enctype="multipart/form-data" method="post" action="/admin/staging/<?php echo $staging_id; ?>" onsubmit="return js_admin.formOnSubmit(this);">
<div class="pg" style="text-align: right;">
    <input type="button" value="Refresh" onclick="location.href='/admin/staging/<?php echo $staging_id; ?>'"/>
    <br/>
    <span>Mission here is to just enter data for now. If thinking too much about db and metadata, make it a textarea.</span>
    <br/>
    <br/>
</div>
<div class="pg">
    <div class="heading onToggle">Information</div>
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
<div class="pg">
    <div class="heading onToggle">Business Information</div>
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
<div class="pg menu">
    <div class="heading onToggle">Menu <span class="menu_name"></span>
    </div>
    <div class="controller">
        <input type="button" value="Add menu" onclick="js_admin.addNewMenu(this);" />
        <input type="button" value="Remove menu" onclick="js_admin.removeNewMenu(this);" />
    </div>
    <div class="data toggle">
        <input type="hidden" name="menu[]" value="@menu@"/>
        <input class="jq_watermark" type="text" name="menu[]" title="Group (ie. Appetizers)" onchange="js_admin.menuNameOnChange(this);" />
        <br/>
        <input class="jq_watermark" type="text" name="menu[]" title="Group notes" />
        <br/>
        <div class="subheading onToggle">Menu items</div>
        <div class="menu_group toggle">
            <div class="menu_item">
                <input type="hidden" name="menu[]" value="@item@"/>
                <input class="jq_watermark" type="text" name="menu[]" title="Item"/>
                <input class="jq_watermark" type="text" name="menu[]" title="Price"/>
                <input class="jq_watermark" type="text" name="menu[]" title="Notes"/>
                <input type="button" value="Add item" onclick="js_admin.addNewMenuItem(this);" />
                <input type="button" value="Remove item" onclick="js_admin.removeNewMenuItem(this);" />
            </div>
        </div>
    </div>
    <input type="hidden" name="menu[]" value="@end_of_menu@"/>
    <br/>
</div>
</form>
<div class="pg"><br/><br/><pre><?php var_export($post); ?></pre></div>