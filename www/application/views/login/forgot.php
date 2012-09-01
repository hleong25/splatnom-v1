<?php
$params = array(
    'reset_sent'=>false,
    'err_msg'=>'',
);

extract($params, EXTR_SKIP);
?>
<div class="pg">
<?php if (empty($reset_sent)): ?>
    <p class="help">Oh noes!!! Did you forget your password? I'll help, but you gotta tell me something...</p>
    <br/>
    <form method="post" action="/<?=$myurl?>">
        <label>
            <span class="hint">Username</span>
            <input id="username" class="watermark txt" type="text" placeholder="Username" name="username" value=""/>
        </label>
        <br/>
        <br/>
        <input class="button" type="submit" name="resetme" value="Reset me!" />
    </form>
    <span class="err"><?=$err_msg?></span>
<?php else: ?>
    <p class="msg">
        Check your email in a few minutes! The secret reset code was sent to it.
        <br/>
        Once you got the code, you may enter it <a href="/login/reset">here</a>
    </p>
<?php endif; //if (empty($reset_sent)): ?>
</div>
