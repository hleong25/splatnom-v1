<div id="search_user" class="pg">
    <form id="search_user" enctype="multipart/form-data" method="get" action="/admin/find_user">
        <input class="jq_watermark query_user" type="text" name="query_user" title="Username or Email address" value="<?php echo $query_user; ?>"/>
        <input type="submit" value="Search" />
    </form>
</div>
<br/>
<?php 
if ($query_result !== null)
{
?>
<div id="" class="pg">
    <span>Searching for '<?php echo $query_user; ?>' returned <?php echo count($query_result); ?> results.</span>
    <br/>
    <br/>
    <table id="user_results">
        <thead>
            <td>id</td>
            <td>username</td>
            <td></td>
        </thead>
        <tbody>
        <?php
            foreach ($query_result as $row)
            {
                $id = $row['id'];
                $name = $row['username'];
                
                echo<<<EOHTML
                <tr>
                    <td>{$id}</td>
                    <td><a href="/admin/user_profile/{$id}">{$name}</a></td>
                    <td>
                        [Edit] [Remove]
                    </td>
                </tr>
EOHTML;
            }
        ?>
        </tbody>
    </table>
</div>
<?php 
} // if ($query_result === null)
?>