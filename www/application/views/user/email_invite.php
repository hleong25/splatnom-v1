<?php
$params = array(
    'invite_url' => '',
    'username' => '',
    'email' => '',
    'firstname' => '',
    'lastname' => '',
);

extract($params, EXTR_SKIP);

$invite_url = 'http://'.$invite_url;
?>
<html><body>
    <div style="text-align:center; ">
    <p>Hai!!! <?=$username?> (aka <?=$email?>) wants to invite you to this a super exclusive nom nom group called <a href="<?=$invite_url?>">Splatnom</a>.</p>
    <p>Join us so we can nom on some delicous food =D</p>
    <br/>
    <p>kthxbye</p>
    </div>
</body></html>
