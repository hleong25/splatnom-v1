<?php
$params = array(
    'code' => '',
    'username' => '',
    'err_msg' => false,
);

extract($params, EXTR_SKIP);
?>
<div class="pg">
    <p>To verify your account, please login.</p>
    <form id="new_user" enctype="multipart/form-data" method="post" action="/<?php echo $myurl; ?>">
        <label>
            <span class="hint">Username</span>
            <input class="watermark txt username" type="text" name="username" placeholder="Username" value="<?php echo $username; ?>"/>
        </label>
        <label>
            <span class="hint">Password</span>
            <input class="watermark txt password" type="password" name="password" placeholder="Password" value=""/>
        </label>
        <br/>
        <label>
            <span class="hint">&nbsp;</span>
            <input class="button" type="submit" value="Submit!" />
        </label>
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
