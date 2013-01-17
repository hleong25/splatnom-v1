<?php

$params = array(
    'is_json' => false,
    'json_data' => array(),
    'status' => false,
    'from_url' => '',
    'login_url' => '',
    'fb_error' => false,
);

extract($params, EXTR_SKIP);
?>
<?php if (!$is_json): ?>
<div class="pg">
    <?php if (empty($status)): ?>
        <div class="login">
            <p>To share, please <a class="showlink" href="<?=$login_url?>">login</a> to Facebook.</p>
            <p>Click <a class="showlink" href="<?=$from_url?>">here</a> to go back.</p>
        </div>
    <?php else: ?>
        <div class="shared">
            <p>Thanks for sharing!</p>
            <p>Click <a class="showlink" href="<?=$from_url?>">here</a> to go back.</p>
        </div>
    <?php endif; // share_ok ?>
    <?php if (!empty($fb_error)): ?>
        <div class="error fb_error">
            <p><?=$fb_error?></p>
        </div>
    <?php endif; //if (!empty($fb_error)): ?>
</div>
<?php else: ?>
<?php echo json_encode($json_data); ?>
<?php endif; // if (empty($is_json)): ?>
<?php if (!empty($dbg)): ?><pre class="pg"><?=var_export($dbg)?></pre><?php endif; ?>
