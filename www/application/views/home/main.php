<div class="pg pg_bottom search">
    <form id="searchit" enctype="multipart/form-data" method="post" action="/ws/search">
        <input class="jq_watermark" type="text" name="query" title="Name of the place"/>
        <input class="jq_watermark" type="text" name="location" title="Location"/>
        <input type="submit" value="Search" />
    </form>
    <div id="results">
    </div>
</div>
<div class="pg pg_bottom new_menus">
<?php
if (empty($recently_added))
{
    echo<<<EOHTML
        <span>No menus added...</span>
        <a href="/menu/new">Click here to help me out!</a>
EOHTML;
} // if (empty($recently_added))
else
{
    foreach ($recently_added as $menu)
    {
        echo<<<EOHTML
        <div class="new_added">
            <a href="/menu/show/{$menu['id']}">
                <img class="new_img" src="/ws/getimage/{$menu['file_img']}" />
                <span class="new_name">{$menu['name']}</span>
            </a>
        </div>
EOHTML;
    } //foreach ($recently_added as $menus)
} // else
?>
</div>
<?php if (getPermissions('metadata')) { ?>
<div class="pg pg_bottom metadata">
    <div class="heading">Need metadata</div>
    <div class="data">
        <table id="metadata">
            <thead>
                <td>id</td>
                <td>timestamp</td>
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
                    $site = $row['site_addy'];
                    $imgs = $row['imgs_cnt'];

                    echo<<<EOHTML
                    <tr>
                        <td>{$id}</td>
                        <td>{$ts}</td>
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
<?php } // if (getPermissions('metadata')) ?>
