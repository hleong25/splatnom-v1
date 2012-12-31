<?php

$params = array(
    'share_ok' => false,
    'login_url' => '',
);

extract($params, EXTR_SKIP);
?>
<?php if (empty($share_ok)): ?>
    <p>Please <a href="<?=$login_url?>">login</a></p>
<?php endif; // share_ok ?>
<?php if(!empty($dbg)): ?>
<pre><?=var_export($dbg);?></pre>
<?php endif; //dbg ?>
