<?php
$params = array(
    'dbg' => false,
    'user_info' => false,
    'is_admin' => false,
    'is_metadata' => false,
    'pending_menus' => array(),
    'ready_menus' => array(),
);

extract($params, EXTR_SKIP);

?>
<div class="pg">
<div class="info">
    <span>Hi <?=$user_info['username']?>!</span>
</div>
    <form id="invite" method="post" action="/user/invite">
<div class="invite">
        <label>
            <span class="hint">Friend's Email</span>
            <input type="text" class="watermark" name="friend" placeholder="Friend's email"/>
        </label>
        <label>
            <span class="hint">&nbsp;</span>
            <input class="button" type="submit" value="Invite a friend!"/>
        </label>
</div>
    </form>
<div class="menus">
    <div class="pending">
        <?php if(!empty($pending_menus)): ?>
            <span>Pending menu list:</span>
            <table class="tblDefault">
                <thead>
                    <td>id</td>
                    <td>timestamp</td>
                    <td>site</td>
                    <td>imgs</td>
                    <?php if($is_admin): ?><td></td><?php endif; ?>
                </thead>
                <tbody>
                <?php
                    foreach ($pending_menus as $menu)
                    {
                        $id = $menu['id'];
                        $row_id = "row_{$id}";
                        $ts = date('m.d.y', strtotime($menu['ts']));
                        $cnt_sites = 0;

                        $cnt_sites += !empty($menu['site_addy1']) ? 1 : 0;
                        $cnt_sites += !empty($menu['site_addy2']) ? 1 : 0;
                        $cnt_sites += !empty($menu['site_addy3']) ? 1 : 0;
                        $cnt_sites += !empty($menu['site_addy4']) ? 1 : 0;
                        $cnt_sites += !empty($menu['site_addy5']) ? 1 : 0;

                        $link_view = $is_admin ? "<td><a href=\"/admin/pending_menu/{$id}\">View</a></td>" : '';

                        echo<<<EOHTML
                        <tr id="{$row_id}">
                            <td>{$id}</td>
                            <td>{$ts}</td>
                            <td>{$cnt_sites}</td>
                            <td>{$menu['cnt_imgs']}</td>
                            {$link_view}
                        </tr>
EOHTML;
                    }
                ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
    <div class="ready">
        <?php if(!empty($ready_menus)): ?>
            <span>Menu list:</span>
            <table class="tblDefault">
                <thead>
                    <td>id</td>
                    <td>date</td>
                    <td>status</td>
                    <td>name</td>
                    <td>address</td>
                    <td>metadata</td>
                </thead>
                <tbody>
                <?php foreach ($ready_menus as $menu):
                    $menu_id = $menu['id'];
                    $name = $menu['name'];
                    list($date, $time) = explode(' ', $menu['ts']);
                    $status = $menu['menu_status'];
                    $address = $menu['address'];

                    $slug = Util::slugify($name);

                    $edit_menu = $is_metadata ? "<a href=\"/menu/edit_metadata/{$menu['id']}\">edit</a>" : '';
                ?>
                    <tr>
                        <td><?=$menu_id?></td>
                        <td><?=$date?></td>
                        <td><?=$status?></td>
                        <td><?=$name?></td>
                        <td><?=$address?></td>
                        <td><?=$edit_menu?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</div>
<?php if(!empty($dbg)): ?>
<div class="pg"><pre><?=var_export($dbg,false)?></pre></div>
<?php endif; ?>
