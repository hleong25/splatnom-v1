<?php
$params = array(
    'dbg' => false,
    'user_info' => false,
);

extract($params, EXTR_SKIP);

?>
<div class="pg">
<div class="info">
    <span>Hi <?=$user_info['username']?>!</span>
</div>
<div class="invite">
    <form id="invite" method="post" action="/user/invite">
        <input type="text" class="jq_watermark" name="friend" title="Friend's email"/>
        <input type="submit" value="Invite a friend!"/>
    </form>
</div>
</div>
<?php if(!empty($dbg)): ?>
<div class="pg"><pre><?=var_export($dbg,false)?></pre></div>
<?php endif; ?>
