<?php
$params = array(
    'dbg'=>false,
    'output'=>false,
    'menus'=>array(),
);

extract($params, EXTR_SKIP);

if($output === 'json'):
    echo json_encode($menus);
else:
?>
<div class="pg">
    <form id="lstmenus" method="post" action="/export/menus">
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
            <?php foreach ($menus as $row):
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
                    <td><a class="btnDownload" href="/export/menus/<?=$id?>">Download</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <button type="submit">Download checked items</button>
    </div>
    </form>
</div>
<?php if (!empty($dbg)): ?><div class="pg"><pre><?=var_export($dbg)?></pre></div><?php endif; ?>
<?php endif; //output style ?>
