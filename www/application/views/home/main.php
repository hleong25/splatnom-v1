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
        <input class="jq_watermark" type="text" name="query" title="Search" value="<?php echo $query; ?>"/>
        <input class="jq_watermark" type="text" name="location" title="Zip code" value="<?php echo $location; ?>"/>
        <input type="submit" value="Search" />
    </form>
    <div id="results">
    </div>
</div>
<div class="pg pg_bottom new_menus">
<?php
if (empty($ready_menus))
{
    echo<<<EOHTML
        <span>No menus added...</span>
        <a href="/menu/new">Click here to help me out!</a>
EOHTML;
}
else // if (empty($ready_menus)
{
    echo<<<EOHTML
        <table class="tblDefault">
            <thead>
                <td>id</td>
                <td>name</td>
                <td>address</td>
            </thead>
            <tbody>
EOHTML;

    foreach ($ready_menus as $menu)
    {
        $menu_id = $menu['id'];
        $name = $menu['name'];
        $slug = Util::slugify($name);

        $link = '<a href="/menu/view/'.$menu_id.'-%s">%s</a>';

        $id = sprintf($link, $slug, $menu['id']);
        $name = sprintf($link, $slug, $name);
        $address = sprintf($link, $slug, $menu['address']);

        $slug = Util::slugify($name);

        echo<<<EOHTML
            <tr>
                <td>{$id}</td>
                <td>{$name}</td>
                <td>{$address}</td>
            </tr>
EOHTML;
    }

    echo<<<EOHTML
            </tbody>
        </table>
EOHTML;

}
?>
</div>
<?php if ($is_metadata && !empty($need_metadata)) : ?>
<div class="pg pg_bottom metadata">
    <div class="heading">Need metadata</div>
    <div class="data">
        <table class="tblDefault">
            <thead>
                <td>id</td>
                <td>timestamp</td>
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
                    $name = $row['name'];
                    $site = $row['links_cnt'];
                    $imgs = $row['imgs_cnt'];

                    echo<<<EOHTML
                    <tr>
                        <td>{$id}</td>
                        <td>{$ts}</td>
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
