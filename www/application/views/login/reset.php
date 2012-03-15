<?php
$params = array(
    'err'=>'',
    'msg'=>'',
    'username'=>'',
    'reset_code'=>'',
    'is_reset'=>false,
);

extract($params, EXTR_SKIP);
?>
<div class="pg">
<?php if (empty($is_reset)): ?>
    <p class="msg">The princess is here, but I need to know who you are and tell me the secret code to pass the gate!!!</p>
    <form method="post" action="/<?=$myurl?>">
        <input class="jq_watermark txt" type="text" title="Username" name="username" value="<?=$username?>"/><br/>
        <input class="jq_watermark txt" type="text" title="Reset code" name="reset_code" value="<?=$reset_code?>"/><br/>
        <br/>
        <input class="jq_watermark txt" type="password" title="New password" name="password" value=""/><br/>
        <input class="jq_watermark txt" type="password" title="Confirm password" name="password2" value=""/><br/>
        <br/>
        <input class="button" type="submit" name="resetme" value="Reset me!" />
    </form>
    <?php if (!empty($err)): ?>
    <div id="err">
        <p><?=$err?></p>
        <p>If you are still having trouble, contact the monkeys by sending us feedback with the issue</p>
    </div>
    <?php endif; // if (!empty($err)): ?>
<?php else: ?>
    <p class="msg">Password reset was successful, but the princess is in another castle!</p>
    <p class="msg">You can now <a href="/login/main">login</a>.</p>
<?php endif; //if (empty($is_reset)): ?>
</div>
