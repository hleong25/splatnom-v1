<?php
$params = array(
    'query'=>'',
    'location'=>'',
    'msg'=>false,
    'places'=>array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg pg_bottom search">
    <form id="searchit" enctype="multipart/form-data" method="post" action="/menu/search">
        <input class="jq_watermark" type="text" name="query" title="Search" value="<?php echo $query; ?>"/>
        <input class="jq_watermark" type="text" name="location" title="Zip code" value="<?php echo $location; ?>"/>
        <input type="submit" value="Search" />
    </form>
</div>
<div class="pg pg_bottom msg">
    <span><?php echo $msg; ?></span>
</div>
<div class="pg pg_bottom results">
<?php
if (empty($places))
{
    echo<<<EOHTML
        <span>No placesfound...</span>
EOHTML;
}
else // if (empty($places)
{
    echo<<<EOHTML
        <table class="tblDefault">
            <thead>
                <td>id</td>
                <td>name</td>
                <td>address</td>
                <td>distance</td>
            </thead>
            <tbody>
EOHTML;

    foreach ($places as $place)
    {
        $distance = sprintf('%0.2f', $place['distance']);
        echo<<<EOHTML
            <tr>
                <td>{$place['menu_id']}</td>
                <td>{$place['name']}</td>
                <td>{$place['address']}</td>
                <td>{$distance}</td>
            </tr>
EOHTML;
    }

    echo<<<EOHTML
            </tbody>
        </table>
EOHTML;

}
?>
</div>
<div class="pg"><pre><?php //var_export($places); ?></pre></div>
