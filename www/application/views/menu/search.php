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
    <form id="searchit" method="get" action="/menu/search">
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
if (!empty($places))
{
    echo<<<EOHTML
        <table class="tblDefault">
            <thead>
                <td>#</td>
                <td>name</td>
                <td>address</td>
                <td>distance</td>
                <td>score</td>
            </thead>
            <tbody>
EOHTML;

    foreach ($places as $idx=>$place)
    {
        $menu_id = $place['menu_id'];
        $distance = sprintf('%0.2f', $place['distance']);
        $score = sprintf('%0.2f', $place['score']);

        if ($score < 0.5)
            break;

        $link = '<a href="/menu/view/'.$menu_id.'">%s</a>';

        $id = sprintf($link, $menu_id);
        $idx_no = sprintf($link, $idx+1);
        $name = sprintf($link, $place['name']);
        $address = sprintf($link, $place['address']);
        $distance = sprintf($link, $distance);
        $score = sprintf($link, $score);

        echo<<<EOHTML
            <tr>
                <td>{$idx_no}</td>
                <td>{$name}</td>
                <td>{$address}</td>
                <td>{$distance}</td>
                <td>{$score}</td>
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
<div class="pg"><pre><?php var_export($places); ?></pre></div>
