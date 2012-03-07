<?php
$params = array(
    'dbg'=>false,
    'err_msg'=>'',
    'imported_menus'=>array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg menus">
<?php if (!empty($err_msg)): ?>
    <span style="color: red;"><?=$err_msg?></span>
<?php endif; ?>
<?php if (empty($imported_menus)): ?>
    <span>No menus imported</span>
<?php else: ?>
    <ul class="imported">
        <?php foreach ($imported_menus as $menu):
            $link = "/menu/edit_metadata/{$menu['id']}";
            $name = $menu['name'];
        ?>
            <li><a href="<?=$link?>"><?=$name?></a></li>
        <?php endforeach; ?>
    </ul>
<?php endif; ?>
</div>
<?php if (!empty($dbg)): ?>
<div class="pg"><pre><?=var_export($dbg)?></pre></div>
<?php endif; ?>
