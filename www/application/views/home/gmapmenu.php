<?php
$params = array(
    'menus' => array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg" id="map_canvas"></div>
<script type="text/javascript">
    var $menus = <?=json_encode($menus)?>;
</script>
