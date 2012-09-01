<?php
$p_info = array(
    'id' => -1,
    'userame' => '',
    'firstname' => '',
    'lastname' => '',
    'email' => '',
);

$params = array(
    'info' => $p_info,
    'permissions' => array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg info">
    <div id="id"><span class="info">id</span><span class="val"><?php echo $info['id']; ?></span></div>
    <div id="username"><span class="info">user name</span><span class="val"><?php echo $info['username']; ?></span></div>
    <div id="fname"><span class="info">first name</span><span class="val"><?php echo $info['firstname']; ?></span></div>
    <div id="lname"><span class="info">last name</span><span class="val"><?php echo $info['lastname']; ?></span></div>
    <div id="email"><span class="info">email</span><span class="val"><?php echo $info['email']; ?></span></div>
</div>
<br/>
<form id="edit_user" enctype="multipart/form-data" method="post" action="/admin/user_profile/<?php echo $info['id']; ?>">
<input type="hidden" name="uid" value="<?=$info['id']?>" />
<div class="pg permissions">
    <span class="heading">User Permissions</span>
    <br/>
<?php
    foreach ($permissions as $key => $val)
    {
        $chk = $val ? 'checked' : '';

        echo<<<EOHTML
            <label>
                <input class="chkPerm" type="checkbox" name="perms[]" value="{$key}" {$chk} />
                <span class="chkPerm">{$key}</span>
            </label>
EOHTML;
    }
?>
</div>
<br/>
<div class="pg edit_control">
    <input class="button" type="submit" value="Save" />
    <input class="button" type="reset" value="Reset" />
</div>
</form>
