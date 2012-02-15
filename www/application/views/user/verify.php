<?php
$params = array(
    'code' => '',
    'username' => '',
    'err_msg' => false,
);

extract($params, EXTR_SKIP);
?>
<div class="pg">
    <span>To verify your account, please login.</span>
    <form id="new_user" enctype="multipart/form-data" method="post" action="/<?php echo $myurl; ?>">
        <input class="jq_watermark username" type="text" name="username" title="Username" value="<?php echo $username; ?>"/>
        <br/>
        <input class="jq_watermark password" type="password" name="password" title="Password" value=""/>
        <br/>
        <input type="submit" value="Submit!" />
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
