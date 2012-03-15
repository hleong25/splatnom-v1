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
        <?php if(!empty($msg)): ?>
            <div id="msg">
                <span><?php echo $msg; ?></span>
            </div>
        <?php endif; ?>
        <div class="help">
            <a href="/login/forgot">Forgot password?</a>
        </div>
        <input id="username" class="jq_watermark txt" type="text" name="lu" title="Username" value="<?php echo $username; ?>" /><br/>
        <input class="jq_watermark txt" type="password" name="lp" title="Password" /><br/>
        <div class="action">
            <input class="button" type="submit" value="Login" />
            <input class="button" type="reset" value="Reset" />
        </div>
    </form>
</div>
