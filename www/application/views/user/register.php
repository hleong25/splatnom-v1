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
    <p>Thanks for registering! Your information is safe and encrypted. We won't do anything bad with it.</p>
    <p class="small">Unless SONPA (Secret Order of Ninja-Pirates Association) offers me a special membership...</p>
    <br/>
    <form id="new_user" enctype="multipart/form-data" method="post" action="/<?php echo $myurl; ?>">
        <input class="watermark fname" type="text" name="fname" placeholder="First name" value="<?php echo $fname; ?>" />
        <input class="watermark lname" type="text" name="lname" placeholder="Last name" value="<?php echo $lname; ?>" />
        <br/>
        <br/>
        <input class="watermark email" type="text" name="email" placeholder="Email" value="<?php echo $email; ?>" />
        <input class="watermark email" type="text" name="email2" placeholder="Confirm email" value="<?php echo $email2; ?>" />
        <br/>
        <br/>
        <input class="watermark username" type="text" name="username" placeholder="Username" value="<?php echo $username; ?>"/>
        <br/>
        <input class="watermark password" type="password" name="password" placeholder="Password" value=""/>
        <input class="watermark password" type="password" name="password2" placeholder="Confirm Password" value=""/><br/>
        <br/>
        <?=recaptcha_get_html(RECAPTCHA_PUBLIC_KEY)?>
        <br/>
        <input class="button" type="submit" value="Register!" />
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
