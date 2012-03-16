<?php
$params = array(
    'user' => 'theuser',
    'password' => 'raspberry',
);

extract($params, EXTR_SKIP);

?>
<html><body>
<div style="text-align:center; ">
    <p>Hai <?=$user?>!! Your password has been changed. If you didn't do it, email me real quick!<p>
    <br/>
    <p>Userame: <strong><?=$user?></strong></p>
    <p>Password: <strong><?=$password?></strong></p>
</div>
</body></html>
