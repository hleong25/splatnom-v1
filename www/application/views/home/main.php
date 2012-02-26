<?php
$params = array(
    'is_metadata' => false,
    'query' => '',
    'location' => '',
    'need_metadata'=>false,
);

extract($params, EXTR_SKIP);
?>
<div class="pg pg_bottom search">
    <form id="searchit" method="get" action="/menu/search">
        <div class="fq">
            <span class="hint">Look for 'fish tacos' or 'Japanese'</span>
            <input class="jq_watermark query" type="text" name="query" title="Search" value="<?php echo $query; ?>"/>
        </div>
        <div class="fq">
            <span class="hint">Zip code</span>
            <input class="jq_watermark location" type="text" name="location" title="Zip code" value="<?php echo $location; ?>"/>
        </div>
        <button class="search">Search</button>
    </form>
    <div id="results">
    </div>
</div>
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
            <?php
                foreach ($need_metadata as $row)
                {
                    $id = $row['id'];
                    $ts = $row['ts'];
                    $user = $row['username'];
                    $name = $row['name'];
                    $site = $row['links_cnt'];
                    $imgs = $row['imgs_cnt'];

                    echo<<<EOHTML
                    <tr>
                        <td>{$id}</td>
                        <td>{$ts}</td>
                        <td>{$user}</td>
                        <td>{$name}</td>
                        <td>{$site}</td>
                        <td>{$imgs}</td>
                        <td><a href="/menu/edit_metadata/{$id}">Edit</a></td>
                    </tr>
EOHTML;
                }
            ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; // if ($is_metadata) ?>
