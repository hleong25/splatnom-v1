<div class="pg">
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
            foreach ($new_menus as $menu)
            {
                $id = $menu['id'];
                $row_id = "row_{$id}";
                $ts = date("m.d.y G:i:s", strtotime($menu["ts"]));
                $site = $menu['site_addy'];

                echo<<<EOHTML
                <tr id="{$row_id}">
                    <td>{$id}</td>
                    <td>{$ts}</td>
                    <td><a href="http://{$site}">{$site}</a></td>
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
