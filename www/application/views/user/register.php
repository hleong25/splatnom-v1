<?php
if (!empty($_SESSION['id']))
    return;

$params = array(
    'fname'=>'',
    'lname'=>'',
    'email'=>'',
    'email2'=>'',
    'username'=>'',
    'err_msg'=>false,
);
extract($params, EXTR_SKIP);
?>
<div class="pg">
    <form id="new_user" enctype="multipart/form-data" method="post" action="/<?php echo $myurl; ?>">
        <input class="jq_watermark fname" type="text" name="fname" title="First name" value="<?php echo $fname; ?>" />
        <input class="jq_watermark lname" type="text" name="lname" title="Last name" value="<?php echo $lname; ?>" />
        <br/>
        <input class="jq_watermark email" type="text" name="email" title="Email" value="<?php echo $email; ?>" />
        <br/>
        <input class="jq_watermark email" type="text" name="email2" title="Confirm email" value="<?php echo $email2; ?>" />
        <br/>
        <br/>
        <input class="jq_watermark username" type="text" name="username" title="Username" value="<?php echo $username; ?>"/>
        <br/>
        <input class="jq_watermark password" type="password" name="password" title="Password" value=""/>
        <br/>
        <input class="jq_watermark password" type="password" name="password2" title="Confirm Password" value=""/><br/>
        <br/>
        <input type="submit" value="Register!" />
    </form>
</div>
<?php
if ($err_msg !== false)
{
    echo<<<EOHTML
        <br/>
        <div class="pg error_msg">
            <span>{$err_msg}</span>
        </div>
EOHTML;
} // if ($err_msg !== false)
?>
