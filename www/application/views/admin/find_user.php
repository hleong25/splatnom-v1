<?php
$params = array(
    'query_user' => '',
    'query_result' => array(),
);
extract($params, EXTR_SKIP);
?>
<div id="search_user" class="pg">
    <form id="search_user" enctype="multipart/form-data" method="get" action="/admin/find_user">
        <input class="jq_watermark query_user" type="text" name="query_user" title="Username or Email address" value="<?php echo $query_user; ?>"/>
        <input type="submit" value="Search" />
    </form>
</div>
<br/>
<?php if (!empty($query_result)): ?>
<div id="search" class="pg">
    <span>Searching for '<?php echo $query_user; ?>' returned <?php echo count($query_result); ?> results. (max results is 100)</span>
    <br/>
    <br/>
    <table class="tblDefault">
        <thead>
            <td>id</td>
            <td>username</td>
            <td>email</td>
            <td>firstname</td>
            <td>lastname</td>
        </thead>
        <tbody>
        <?php foreach ($query_result as $row):
                $id = $row['id'];
                $name = $row['username'];
                $email = $row['email'];
                $firstname = $row['firstname'];
                $lastname = $row['lastname'];

                $user_link = "/admin/user_profile/{$id}";
        ?>
                <tr>
                    <td><a href="<?=$user_link?>"><?=$id?></a></td>
                    <td><a href="<?=$user_link?>"><?=$name?></a></td>
                    <td><a href="<?=$user_link?>"><?=$email?></a></td>
                    <td><a href="<?=$user_link?>"><?=$firstname?></a></td>
                    <td><a href="<?=$user_link?>"><?=$lastname?></a></td>
                </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
