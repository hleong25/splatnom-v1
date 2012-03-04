<?php
$params = array(
    'dbg'=>false,
);
?>
<?php if (!empty($dbg)): ?>
<div class="pg"><pre><?=var_export($dbg)?></pre></div>
<?php endif; ?>
