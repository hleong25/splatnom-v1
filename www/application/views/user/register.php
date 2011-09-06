<?php
if (!empty($_SESSION['id']))
    return;
?>
<div class="pg">
    <form id="new_user" enctype="multipart/form-data" method="post" action="/<?php echo $myurl; ?>">
        <input class="jq_watermark username" type="text" name="username" title="Username" value="<?php echo $username; ?>"/><br/>
        <input class="jq_watermark password" type="password" name="password" title="Password" value=""/><br/>
        <input type="submit" value="Register!" />
    </form>
</div>
<?php
if ($err_msg !== false)
{
?>
<div class="pg error_msg">
    <span><?php echo $err_msg; ?></span>
</div>
<?php
} // if ($err_msg !== false)
?>
