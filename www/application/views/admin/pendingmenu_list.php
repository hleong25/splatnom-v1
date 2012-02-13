<div class="pg">
    <table class="tblDefault">
        <thead>
            <td>id</td>
            <td>timestamp</td>
            <td>user</td>
            <td>site</td>
            <td>imgs</td>
            <td></td>
            <td></td>
        </thead>
        <tbody>
        <?php
            foreach ($new_menus as $menu)
            {
                $id = $menu['id'];
                $row_id = "row_{$id}";
                $ts = date("m.d.y G:i:s", strtotime($menu["ts"]));
                $cnt_sites = 0;
                $user = $menu['username'];

                $cnt_sites += !empty($menu['site_addy1']) ? 1 : 0;
                $cnt_sites += !empty($menu['site_addy2']) ? 1 : 0;
                $cnt_sites += !empty($menu['site_addy3']) ? 1 : 0;
                $cnt_sites += !empty($menu['site_addy4']) ? 1 : 0;
                $cnt_sites += !empty($menu['site_addy5']) ? 1 : 0;

                echo<<<EOHTML
                <tr id="{$row_id}">
                    <td>{$id}</td>
                    <td>{$ts}</td>
                    <td>{$user}</td>
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
</div>
