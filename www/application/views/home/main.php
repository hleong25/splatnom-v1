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
<?=get_html_searchit($location)?>
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
