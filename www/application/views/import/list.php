<?php
$params = array(
    'dbg'=>false,
    'remote_site'=>'',
    'remote_menus'=>array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg">
    <form id="import_config" method="get" action="/import/list">
        <input type="text" name="remote_site" value="<?=$remote_site?>"/>
        <button type="submit">Connect!</button>
    </form>
    <br/>
    <?php if (!empty($remote_menus)): ?>
    <form id="lstmenus" method="post" action="/import/menus/<?=$remote_site?>">
    <div id="list">
        <table class="tblDefault">
            <thead>
                <td><input id="chkAll" type="checkbox" style="display:none;"/></td>
                <td>id</td>
                <td>timestamp</td>
                <td>user</td>
                <td>name</td>
                <td>address</td>
                <td></td>
            </thead>
            <tbody>
            <?php foreach ($remote_menus as $row):
                $id = $row['id'];
                $ts = $row['ts'];
                $user = $row['username'];
                $name = $row['name'];
                $addy = $row['address'];

                $ts = explode(' ', $ts);
                $ts = $ts[0];
            ?>
                <tr>
                    <td><input class="menu_id" type="checkbox" name="menu_ids[]" value="<?=$id?>"/></td>
                    <td><?=$id?></td>
                    <td><?=$ts?></td>
                    <td><?=$user?></td>
                    <td><?=$name?></td>
                    <td><?=$addy?></td>
                    <td><a href="/import/menus/<?=$id?>">Import</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">Import checked items</button>
    </div>
    </form>
    <?php endif; //if (!empty($remote_menus)): ?>
</div>
<?php if (!empty($dbg)): ?><div class="pg"><pre><?=var_export($dbg)?></pre></div><?php endif; ?>
