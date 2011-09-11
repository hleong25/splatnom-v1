<?php
$params = array(
    'username'=>'',
    'msg'=>'',
    'goto_url'=>'',
);
extract($params, EXTR_SKIP);
?>
<div class="pg">
    <form id="login" method="post" action="/<?php echo $myurl.'&'.$goto_url; ?>">
        <input id="username" class="jq_watermark" type="text" name="lu" title="Username" value="<?php echo $username; ?>" /><br/>
        <input class="jq_watermark" type="password" name="lp" title="Password" /><br/>
        <input type="submit" value="Login" />
    </form>
    <div id="msg">
        <span><?php echo $msg; ?></span>
    </div>
</div>
