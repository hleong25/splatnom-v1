<?php
$params = array(
    'user' => 'User',
    'verifyCode' => '00000',
);

extract($params, EXTR_SKIP);

$url_verify = "http://www.splatnom.com/user/verify/{$verifyCode}";
?>
<html><body>
    <div style="text-align:center; ">
    <p>Hai <?=$user?>,</p>
    <p>Thanks for registerting with Splatnom! Please verify your by clicking <a href="<?=$url_verify?>">here</a>!</p>
    <br/>
    <p>Or click copy and paste this link: <a href="<?=$url_verify?>"><?=$url_verify?></a></p>
    <br/>
    <p>kthxbye</p>
    </div>
</body></html>
