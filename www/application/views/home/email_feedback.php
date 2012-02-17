<?php
$params = array(
    'name' => '',
    'email' => '',
    'msg' => '',
);

extract($params, EXTR_SKIP);

?>
<html><body>
    <span>name: <strong><?=$name?></strong></span>
    <br/>
    <span>email: <strong><?=$email?></strong></span>
    <br/>
    <br/>
    <div><?=$msg?></div>
    <br/>
</body></html>
