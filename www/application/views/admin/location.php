<?php
$params = array(
    'dbg'=>false,
    'q_gmap_query'=>'',
    'q_zip'=>'',
    'q_citystate'=>'',
    'q_address' => '',
    'q_lat'=>'',
    'q_long'=>'',
    'q_radius'=>'2',
    'found_locations'=>array(),
    'nearby_query'=>array(),
    'map_request'=>array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg">
    <form id="bygmap" enctype="multipart/form-data" method="get" action="/admin/location/gmap">
        <label>
            <span class="hint">Map Query</span>
            <input class="watermark gmap" type="text" name="query" placeholder="Query" value="<?php echo $q_gmap_query; ?>"/>
        </label>
        <label>
            <span class="hint">&nbsp;</span>
            <input class="button" type="submit" value="Search Maps" />
        </label>
    </form>
</div>
<?php /*
<div class="pg">
    <form id="byzip" enctype="multipart/form-data" method="get" action="/admin/location/zip">
        <label>
            <span class="hint">Zip code</span>
            <input class="watermark" type="text" name="zip" placeholder="Zip code" value="<?php echo $q_zip; ?>"/>
        </label>
        <label>
            <span class="hint">&nbsp;</span>
            <input class="button" type="submit" value="Search zip code" />
        </label>
    </form>
</div>
<div class="pg">
    <form id="bycitystate" enctype="multipart/form-data" method="get" action="/admin/location/citystate">
        <label>
            <span class="hint">City, State</span>
            <input class="watermark" type="text" name="citystate" placeholder="City, State" value="<?php echo $q_citystate; ?>"/>
        </label>
        <label>
            <span class="hint">&nbsp;</span>
            <input class="button" type="submit" value="Search city/state" />
        </label>
    </form>
</div>
<div class="pg">
    <form id="byaddress" enctype="multipart/form-data" method="get" action="/admin/location/address">
        <label>
            <span class="hint">Address</span>
            <input class="watermark" type="text" name="address" placeholder="Address" value="<?php echo $q_address; ?>"/>
        </label>
        <label>
            <span class="hint">&nbsp;</span>
            <input class="button" type="submit" value="Search by address" /><span> *it really just parses the city/state</span>
        </label>
    </form>
</div>
*/ ?>
<div class="pg">
    <form id="bylatlong" enctype="multipart/form-data" method="get" action="/admin/location/latlong">
        <label>
            <span class="hint">Latitude</span>
            <input class="watermark" type="text" name="lat" placeholder="Latitude" value="<?php echo $q_lat; ?>"/>
        </label>
        <label>
            <span class="hint">Longitude</span>
            <input class="watermark" type="text" name="long" placeholder="Longitude" value="<?php echo $q_long; ?>"/>
        </label>
        <label>
            <span class="hint">Radius</span>
            <input class="watermark" type="text" name="radius" placeholder="Radius" value="<?php echo $q_radius; ?>"/>
        </label>
        <label>
            <span class="hint">&nbsp;</span>
            <input class="button" type="submit" value="Search lat/long" />
        </label>
    </form>
</div>
<div class="pg">
    <?php if (!empty($found_locations)) { ?>
    <br/>
    <table class="tblDefault">
        <thead>
            <td>latitude</td>
            <td>longitude</td>
            <td>zip</td>
            <td>city</td>
            <td>state</td>
            <td>score</td>
        </thead>
        <tbody>
        <?php
            foreach ($found_locations as $row)
            {
                echo<<<EOHTML
                <tr>
                    <td>{$row['latitude']}</td>
                    <td>{$row['longitude']}</td>
                    <td>{$row['zip']}</td>
                    <td>{$row['city']}</td>
                    <td>{$row['state']}</td>
                    <td>{$row['score']}</td>
                </tr>
EOHTML;
            }
        ?>
        </tbody>
    </table>
    <?php } // if (!empty($found_locations)) ?>
    <?php if (!empty($nearby_query)) { ?>
    <br/>
    <table class="tblDefault">
        <thead>
            <td>latitude</td>
            <td>longitude</td>
            <td>zip</td>
            <td>city</td>
            <td>state</td>
            <td>distance</td>
        </thead>
        <tbody>
        <?php
            foreach ($nearby_query as $row)
            {
                echo<<<EOHTML
                <tr>
                    <td>{$row['latitude']}</td>
                    <td>{$row['longitude']}</td>
                    <td>{$row['zip']}</td>
                    <td>{$row['city']}</td>
                    <td>{$row['state']}</td>
                    <td>{$row['distance']}</td>
                </tr>
EOHTML;
            }
        ?>
        </tbody>
    </table>
    <?php } // if (!empty($nearby_query)) ?>
    <?php if (!empty($map_request)): ?>
    <br/>
    <table class="tblDefault">
        <thead>
            <td>idx</td>
            <td>geocodeQualityCode</td>
            <td>latitude</td>
            <td>longitude</td>
            <td>country</td>
            <td>state</td>
            <td>city</td>
            <td>county</td>
            <td></td>
            <td></td>
        </thead>
        <tbody>
        <?php foreach ($map_request as $idx_row => $row):
            $lat = $row['latLng']['lat'];
            $lng = $row['latLng']['lng'];
            $link_splatnom = 'http://splatnom.local/admin/location/latlong';
            $link_google = 'https://maps.google.com/maps';
            $gmap_zoom = 13;
        ?>
                <tr>
                    <td><?=$idx_row?></td>
                    <td><?=$row['geocodeQualityCode']?></td>
                    <td><?=$lat?></td>
                    <td><?=$lng?></td>
                    <td><?=$row['adminArea1']?></td>
                    <td><?=$row['adminArea3']?></td>
                    <td><?=$row['adminArea5']?></td>
                    <td><?=$row['adminArea4']?></td>
                    <td><a target="_blank" href="<?=$link_splatnom?>?lat=<?=$lat?>&long=<?=$lng?>&radius=<?=$q_radius?>">Splatnom lat/long</a></td>
                    <td><a target="_blank" href="<?=$link_google?>?q=<?=$lat?>,+<?=$lng?>&z=<?=$gmap_zoom?>">Google Maps</a></td>
                </tr>
        <?php endforeach; //foreach ($nearby_query as $row): ?>
        </tbody>
    </table>
    <?php endif; // if (!empty($map_request)) ?>
</div>
<?php
if (!empty($dbg))
{
    $sdbg = print_r($dbg, true);
    echo<<<EOHTML
        <div class="dbg"><pre>{$sdbg}</pre></div>
EOHTML;
}
?>
