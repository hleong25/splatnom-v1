<?php
$params = array(
    'query'=>'',
    'location'=>'',
);

extract($params, EXTR_SKIP);
?>
<div class="pg pg_bottom search">
    <form id="searchit" enctype="multipart/form-data" method="post" action="/menu/search">
        <input class="jq_watermark" type="text" name="query" title="Name of the place" value="<?php echo $query; ?>"/>
        <input class="jq_watermark" type="text" name="location" title="Location" value="<?php echo $location; ?>"/>
        <input type="submit" value="Search" />
    </form>
    <div id="results">
    </div>
</div>

