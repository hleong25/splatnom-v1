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
        <label>
            <span class="hint">Username</span>
            <input class="watermark txt" type="text" placeholder="Username" name="username" value="<?=$username?>"/><br/>
        </label>
        <label>
            <span class="hint">Reset code</span>
            <input class="watermark txt" type="text" placeholder="Reset code" name="reset_code" value="<?=$reset_code?>"/><br/>
        </label>
        <label>
            <span class="hint">New password</span>
            <input class="watermark txt" type="password" placeholder="New password" name="password" value=""/><br/>
        </label>
        <label>
            <span class="hint">Confirm password</span>
            <input class="watermark txt" type="password" placeholder="Confirm password" name="password2" value=""/><br/>
        </label>
        <label>
            <span class="hint">&nbsp;</span>
            <input class="button" type="submit" name="resetme" value="Reset me!" />
        </label>
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
