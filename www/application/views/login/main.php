<?php
$params = array(
    'username'=>'',
    'msg'=>'&nbsp;',
    'goto_url'=>'',
);
extract($params, EXTR_SKIP);
?>
<div class="pg main">
<form id="login" method="post" action="/<?=$myurl?><?=(!empty($goto_url) ? '&'.$goto_url : '')?>">
    <label>
        <a class="forgot clearfix" href="/login/forgot">Forgot?</a>
        <p class="hint">Username</p>
        <input id="username" class="watermark txt" type="text" name="lu" placeholder="Username" value="<?php echo $username; ?>" />
    </label>

    <label>
        <p class="hint">Password<p>
        <input class="watermark txt" type="password" name="lp" placeholder="Password" />
    </label>

    <div class="action">
        <button class="button" type="submit">Login</button>
        <button class="button" type="reset">Reset</button>
    </div>

    <div id="msg">
        <span><?=$msg?></span>
    </div>
</form>
</div>
