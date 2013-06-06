<?php
$params_info = array(
    'status'=>array(),
    'name'=>'',
    'notes'=>'',
    'address'=>'',
    'latitude'=>0,
    'longitude'=>0,
    'dates'=>'',
    'imgs'=>array(),
);

$params = array(
    'dbg' => array(),
    'err_msgs' => array(),
    'google_api_key' => '',
    'event_id' => 0,
    'info' => $params_info,
    'vendors' => array(),
);

extract($params, EXTR_SKIP);


$cover_img = '';
$cover_img_thmb = '';
$cover_img_full = '';
if (!empty($info['cover_img']['file_img']))
{
    $cover_img = $info['cover_img']['file_img'];
    $cover_img_thmb = "/images/get/event/md/$event_id/$cover_img";
    $cover_img_full = "/images/get/event/org/$event_id/$cover_img";
}

if (empty($vendors))
{
    $vendors[] = array(
        'vendor_id' => 0,
        'name' => '',
        'description' => '',
    );
}

?>
<div class="pg edit"><form id="edit_event" enctype="multipart/form-data" method="post" action="/<?=$myurl?>">

    <?php /*
    <div class="action">
        <input class="button" type="button" value="Refresh" data-action="refresh" data-url="/<?=$myurl?>" />
        <input class="button" type="button" value="View Event" data-action="view" data-url="/event/view/<?=$id?>" />
        <input class="button" type="button" value="Export Event" data-action="export" data-url="/export/menus/<?=$id?>" />
        <hr/>
        <input id="force_db_fetch" style="width: 1em; display: inline;" type="checkbox" name="force_reload" />
        <label for="force_db_fetch">refresh db</label>
        <input class="button" type="submit" value="Save Event" data-action="save" />
        <?php if($is_admin): ?>
            <input class="button" type="button" value="Delete Event" data-action="delete" data-url="/event/purge/<?=$id?>" />
        <?php endif; //if($is_admin): ?>
        <hr/>
        <div class="status pg_bottom">
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
            <p>Input Cnt: <span id="input_items"><span></p>
        </div>
    </div>
    */?>

    <div class="reminder">Mission here is to just enter data for now. If thinking too much about db and metadata, make it a textarea.</div>

    <div class="err_msgs">
        <?=implode('<br/>', $err_msgs)?>
    </div>

    <p class="save">
        <input type="submit" name="Save"/>
        <a href="/<?=$myurl?>">Refresh</a>
    </p>
    <br/>

    <div class="info">
        <?php
            $view_url = '/event/view/'.$event_id.'-'.Util::slugify($info['name']);
        ?>
        <div class="heading onToggle">Event Information - <a target="_blank" href="<?=$view_url?>">View event</a></div>
        <div class="data toggle">
            <input class="watermark" type="text" style="width: 25em;" name="info_name" placeholder="Name of the event" value="<?=$info['name']?>"/>
            <br/>
            <input class="watermark" type="text" style="width: 35em;" name="info_notes" placeholder="Notes" value="<?=$info['notes']?>"/>
            <br/>
            <textarea class="watermark address" rows="5"  name="info_address" placeholder="Address"><?=$info['address']?></textarea>
            <br/>
            <input class="find_latlong" type="button" value="Get lat/long for address" /><span class="latlong_js">javascript:alert(window.gApplication.getMap().getCenter());</span>
            <br/>
            <input class="latlong_js_msg" type="textbox" placeholder="Open MapQuest API" value="" readonly>
            <br/>
            <input class="watermark info_latitude" type="text" style="width: 15em;" name="info_latitude" placeholder="Latitude" value="<?=$info['latitude']?>"/>
            <input class="watermark info_longitude" type="text" style="width: 15em;" name="info_longitude" placeholder="Longitude" value="<?=$info['longitude']?>"/>
            <input class="map_addy" type="button" value="Google Map lat/long and addy" data-google-api-key="<?=$google_api_key?>"/>
            <br/>
            <textarea class="watermark dates" rows="5" name="info_dates" placeholder="Event dates"><?=$info['dates']?></textarea>
            <br/>
            <p class="cover_img">Cover image:
                <input type="hidden" name="info_cover_img" value="<?=$cover_img?>"/>
                <a target="_blank" href="<?=$cover_img_full?>"><img src="<?=$cover_img_thmb?>" /></a>
            </p>
        </div>
    </div>

    <div class="vendors">

    <script type="tmpl/vendor" id="tmpl_vendor">
        <div class="vendor_info">
            <input type="hidden" name="vendor[]" value="@vendor@"/>
            <input type="hidden" name="vendor[]" value="0"/>
            <p class="vendor_name">Name: <input class="vendor_name" type="text" name="vendor[]" value="" placeholder="Name"/></p>
            <p class="vendor_desc">Description: <textarea class="vendor_desc" name="vendor[]" rows="5" placeholder=""></textarea></p>
            <div class="vendor_action">
                <button class="vendor_add">Add Vendor</button>
                <button class="vendor_delete">Delete Vendor</button>
            </div>
        </div>
    </script>

        <?php foreach ($vendors as $vendor): ?>
            <div class="vendor_info">
                <input type="hidden" name="vendor[]" value="@vendor@"/>
                <input type="hidden" name="vendor[]" value="<?=$vendor['vendor_id']?>"/>
                <p class="vendor_name">Name: <input class="vendor_name" type="text" name="vendor[]" value="<?=$vendor['name']?>" placeholder="Name"/></p>
                <p class="vendor_desc">Description: <textarea class="vendor_desc" name="vendor[]" rows="10" placeholder=""><?=$vendor['description']?></textarea></p>
                <div class="vendor_action">
                    <button class="vendor_add">Add Vendor</button>
                    <button class="vendor_delete">Delete Vendor</button>
                </div>
            </div>
        <?php endforeach; // foreach ($vendors as $vendor) ?>
    </div>
</form></div>
