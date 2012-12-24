<?php
$params = array(
    'menus_tmpl_data' => array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg" id="map_canvas"></div>
<script type="tmpl/places" id="tmpl_places">
    <div class="gmap" style="margin-bottom: 1em;">
        <p class="name"><a class="showlink" href="${link}">${name}</a></p>
        <p class="addy">${addy}</p>
    </div>
</script>
<script type="text/javascript">
    var $menus_tmpl_data = <?=json_encode($menus_tmpl_data)?>;
</script>
