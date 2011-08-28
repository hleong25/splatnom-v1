<form id="searchit" enctype="multipart/form-data" method="post" action="/admin/staging/<?php echo $staging_id; ?>" onsubmit="return js_admin.formOnSubmit(this);">
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
        <input class="jq_watermark" type="text" name="info_num1" title="Phone"/>
        <br/>
        <input class="jq_watermark" type="text" name="info_num2" title="Fax"/>
    </div>
</div>
<div class="pg">
    <div class="heading onToggle">Hours</div>
    <div class="data toggle">
        <table id="hours">
            <thead>
                <td>Mon</td>
                <td>Tues</td>
                <td>Wed</td>
                <td>Thurs</td>
                <td>Fri</td>
                <td>Sat</td>
                <td>Sun</td>
            </thead>
            <tbody>
            <?php
                for ($ii = 0; $ii < 6; $ii++)
                {
                    $css = '';
                    if ($ii == 0)
                        $css = 'class="template"';
                    
                    echo<<<EOHTML
                    <tr {$css} >
                        <td><input class="" type="checkbox" name="hours[@id][mon]" /></td>
                        <td><input class="" type="checkbox" name="hours[@id][tues]" /></td>
                        <td><input class="" type="checkbox" name="hours[@id][wed]" /></td>
                        <td><input class="" type="checkbox" name="hours[@id][thurs]" /></td>
                        <td><input class="" type="checkbox" name="hours[@id][fri]" /></td>
                        <td><input class="" type="checkbox" name="hours[@id][sat]" /></td>
                        <td><input class="" type="checkbox" name="hours[@id][sun]" /></td>
                    </tr>
EOHTML;
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
</form>
<pre><?php var_export($post); ?></pre>