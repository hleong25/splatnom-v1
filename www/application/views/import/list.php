<?php
$params = array(
    'dbg'=>false,
    'local_file'=>'',
    'remote_site'=>'',
    'remote_menus'=>array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg">
    <div class="local">
        <form id="import_config" method="post" action="/import/local">
            <input class="jq_watermark txtLocalFile" type="text" title="Full path of local file" name="local_file" value="<?=$local_file?>"/>
            <button class="button" type="submit">Import local file!</button>
        </form>
    </div>
    <hr/>
    <div class="remote">
        <form id="import_config" method="get" action="/<?=$myurl?>">
            <input class="jq_watermark txtRemoteSite" type="text" title="Remote site to grab menus, ie. www.splatnom.com" name="remote_site" value="<?=$remote_site?>"/>
            <button class="button" type="submit">Get remote list!</button>
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
                    <td>mod timestamp</td>
                    <td>user</td>
                    <td>name</td>
                    <td>address</td>
                    <td></td>
                </thead>
                <tbody>
                <?php foreach ($remote_menus as $row):
                    $id = $row['id'];
                    $ts = $row['ts'];
                    $mod_ts = $row['mod_ts'];
                    $user = $row['username'];
                    $name = $row['name'];
                    $addy = $row['address'];

                    $ts = nl2br($ts);
                    $mod_ts = nl2br($mod_ts);
                ?>
                    <tr>
                        <td><input class="menu_id" type="checkbox" name="menu_ids[]" value="<?=$id?>"/></td>
                        <td><?=$id?></td>
                        <td class="ts"><?=$ts?></td>
                        <td class="ts"><?=$mod_ts?></td>
                        <td><?=$user?></td>
                        <td><a href="http://<?=$remote_site?>/menu/view/<?=$id?>" target="_blank"><?=$name?></a></td>
                        <td><?=$addy?></td>
                        <td><a class="button" href="/import/menus/<?=$remote_site?>/<?=$id?>">Import</a></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <button class="button" type="submit">Import checked items</button>
        </div>
        </form>
        <?php endif; //if (!empty($remote_menus)): ?>
    </div>
</div>
<?php if (!empty($dbg)): ?><div class="pg"><pre><?=var_export($dbg)?></pre></div><?php endif; ?>
