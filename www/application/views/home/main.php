<?php
$params = array(
    'is_admin' => false,
    'is_metadata' => false,
    'query' => '',
    'location' => '',
    'need_metadata'=>false,
);

extract($params, EXTR_SKIP);
?>
<div class="pg pg_bottom logo"></div>
<div class="pg pg_bottom search">
<form id="searchit" method="get" action="/menu/search">
    <label>
        <span class="hint">Look for 'fish tacos' or 'Japanese'</span>
        <input class="watermark query" type="text" name="query" placeholder="Search" value="<?php echo $query; ?>"/>
    </label>
    <label>
        <span class="hint">Location</span>
        <input class="watermark location" type="text" name="location" placeholder="Location" value="<?php echo $location; ?>"/>
    </label>
    <label>
        <span class="hint">&nbsp;</span>
        <button class="button search" type="submit">Search</button>
    </label>
</form>
</div>
<?php /*
<div class="pg pg_bottom new_menus">
<?php if (empty($ready_menus)): ?>
    <span>No menus added...</span>
    <a href="/menu/new">Click here to help me out!</a>
<?php else: ?>
    <?php foreach ($ready_menus as $menu):
        $menu_id = $menu['id'];
        $name = $menu['name'];
        $address = $menu['address'];
        $slug = Util::slugify($name);

        $address = nl2br($address);

        $link = '<a href="/menu/view/'.$menu_id.'-%s">%s</a>';

        $id = sprintf($link, $slug, $menu_id);
        $name = sprintf($link, $slug, $name);
        $address = sprintf($link, $slug, $address);

        $slug = Util::slugify($name);
    ?>
        <div class="new">
            <span class="name"><?=$name?></span><br/>
            <span class="addy"><?=$address?></span>
        </div>
    <?php endforeach; ?>
<?php endif; ?>
</div>
*/?>
<?php if ($is_metadata && !empty($need_metadata)) : ?>
<div class="pg pg_bottom metadata">
    <div class="heading">Need metadata</div>
    <div class="data">
        <table class="tblDefault">
            <thead>
                <td>id</td>
                <td>timestamp</td>
                <td>user</td>
                <td>name</td>
                <td>site</td>
                <td>imgs</td>
                <td></td>
            </thead>
            <tbody>
            <?php foreach ($need_metadata as $row):
                $id = $row['id'];
                $ts = $row['ts'];
                $user = $row['username'];
                $name = $row['name'];
                $site = $row['links_cnt'];
                $imgs = $row['imgs_cnt'];
            ?>
                <tr>
                    <td><?=$id?></td>
                    <td><?=$ts?></td>
                    <td><?=$user?></td>
                    <td><?=$name?></td>
                    <td><?=$site?></td>
                    <td><?=$imgs?></td>
                    <td>
                        <a class="menu edit" href="/menu/edit_metadata/<?=$id?>">Edit</a>
                        <?php if ($is_admin === true): ?>
                            <a class="menu purge" href="/menu/purge/<?=$id?>">Delete</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; // foreach ($need_metadata as $row): ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; // if ($is_metadata) ?>
