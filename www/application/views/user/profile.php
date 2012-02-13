<?php
$params = array(
    'dbg' => false,
    'user_info' => false,
    'pending_menus' => array(),
    'ready_menus' => array(),
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
                    <td></td>
                    <td></td>
                </thead>
                <tbody>
                <?php
                    foreach ($pending_menus as $menu)
                    {
                        $id = $menu['id'];
                        $row_id = "row_{$id}";
                        $ts = date("m.d.y G:i:s", strtotime($menu["ts"]));
                        $cnt_sites = 0;

                        $cnt_sites += !empty($menu['site_addy1']) ? 1 : 0;
                        $cnt_sites += !empty($menu['site_addy2']) ? 1 : 0;
                        $cnt_sites += !empty($menu['site_addy3']) ? 1 : 0;
                        $cnt_sites += !empty($menu['site_addy4']) ? 1 : 0;
                        $cnt_sites += !empty($menu['site_addy5']) ? 1 : 0;

                        echo<<<EOHTML
                        <tr id="{$row_id}">
                            <td>{$id}</td>
                            <td>{$ts}</td>
                            <td>{$cnt_sites}</td>
                            <td>{$menu['cnt_imgs']}</td>
                            <td><a href="/admin/pending_menu/{$id}">View</a></td>
                            <td><a href="/admin/pendingmenu_list/purge/{$id}" onclick="return js_admin.remove_row({$id}, '{$row_id}');">Remove</a></td>
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
                    <td>ts</td>
                    <td>status</td>
                    <td>name</td>
                    <td>address</td>
                </thead>
                <tbody>
                <?php
                    foreach ($ready_menus as $menu)
                    {
                        $menu_id = $menu['id'];
                        $name = $menu['name'];
                        $slug = Util::slugify($name);

                        $link = '<a href="/menu/view/'.$menu_id.'-%s">%s</a>';

                        $id = sprintf($link, $slug, $menu['id']);
                        $ts = sprintf($link, $slug, $menu['ts']);
                        $status = sprintf($link, $slug, $menu['menu_status']);
                        $name = sprintf($link, $slug, $name);
                        $address = sprintf($link, $slug, $menu['address']);

                        $slug = Util::slugify($name);

                        echo<<<EOHTML
                            <tr>
                                <td>{$id}</td>
                                <td>{$ts}</td>
                                <td>{$status}</td>
                                <td>{$name}</td>
                                <td>{$address}</td>
                            </tr>
EOHTML;
                    }
                ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
</div>
<?php if(!empty($dbg)): ?>
<div class="pg"><pre><?=var_export($dbg,false)?></pre></div>
<?php endif; ?>