<?php
$params = array(
    'username' => '',
    'email' => '',
    'firstname' => '',
    'lastname' => '',
);

extract($params, EXTR_SKIP);

?>
<html><body>
    <div style="text-align:center; ">
    <p>Hai <?=$firstname?> (<?=$email?>), <?=$username?> wants to invite you to this a super exclusive nom nom group called <a href="http://www.splatnom.com">Splatnom</a>.</p>
    <p>Join us so we can nom on some delicous food =D</p>
    <br/>
    <p>kthxbye</p>
    </div>
</body></html>
