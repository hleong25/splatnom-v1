<?php

$params_info = array(
    'status'=>array(),
    'name'=>'',
    'notes'=>'',
    'address'=>'',
    'latitude'=>0,
    'longitude'=>0,
    'numbers'=>'',
    'hours'=>'',
);

$params = array(
    'dbg' => array(),
    'info' => $params_info,
    'links' => array(),
    'mdts' => array(),
);

extract($params, EXTR_SKIP);

?>
<div class="pg">
</div>
<div class="pg dbg"><pre><?php var_export($dbg); ?></pre></div>
