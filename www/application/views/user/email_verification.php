<?php
$params = array(
    'user' => 'User',
    'verify_url' => '',
);

extract($params, EXTR_SKIP);

$verify_url = 'http://'.$verify_url;
?>
<html><body>
    <div style="text-align:center; ">
    <p>Hai <?=$user?>,</p>
    <p>Thanks for registerting with Splatnom! Please verify your by clicking <a href="<?=$verify_url?>">here</a>!</p>
    <br/>
    <p>Or click copy and paste this link: <a href="<?=$verify_url?>"><?=$verify_url?></a></p>
    <br/>
    <p>kthxbye</p>
    </div>
</body></html>
