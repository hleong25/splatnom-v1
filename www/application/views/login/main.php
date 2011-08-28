<div class="pg">
    <form id="login" method="post" action="/login/main">
        <input id="username" class="jq_watermark" type="text" name="lu" title="Username" value="<?php echo $username; ?>" /><br/>
        <input class="jq_watermark" type="password" name="lp" title="Password" /><br/>
        <input type="hidden" name="from_url" value="<?php //echo $url; ?>" />
        <input type="submit" value="Login" />
    </form>
    <div id="msg">
        <span><?php echo $msg; ?></span>
    </div>
</div>