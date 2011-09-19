<?php
$params = array(
    'q_zip'=>'',
    'q_citystate'=>'',
    'q_lat'=>'',
    'q_long'=>'',
    'q_radius'=>'2',
    'nearby_query'=>array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg">
    <form id="byzip" enctype="multipart/form-data" method="get" action="/admin/location/zip">
        <input class="jq_watermark" type="text" name="zip" title="Zip code" value="<?php echo $q_zip; ?>"/>
        <input type="submit" value="Search zip code" />
    </form>
</div>
<div class="pg">
    <form id="bycitystate" enctype="multipart/form-data" method="get" action="/admin/location/citystate">
        <input class="jq_watermark" type="text" name="citystate" title="City, State" value="<?php echo $q_citystate; ?>"/>
        <input type="submit" value="Search city/state" />
    </form>
</div>
<div class="pg">
    <form id="bylatlong" enctype="multipart/form-data" method="get" action="/admin/location/latlong">
        <input class="jq_watermark" type="text" name="lat" title="Latitude" value="<?php echo $q_lat; ?>"/>
        <input class="jq_watermark" type="text" name="long" title="Longitude" value="<?php echo $q_long; ?>"/>
        <input class="jq_watermark" type="text" name="radius" title="Radius" value="<?php echo $q_radius; ?>"/>
        <input type="submit" value="Search lat/long" />
    </form>
    <?php if (!empty($nearby_query)) { ?>
    <table id="latlong">
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
</div>
<div class="pg dbg"><pre><?php var_export($dbg); ?></pre></div>
