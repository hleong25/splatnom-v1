<form id="edit_user" enctype="multipart/form-data" method="post" action="/admin/user_profile/<?php echo $info['id']; ?>">
<input type="hidden" name="_edit_user" />
<div class="pg info">
    <span id="whoami"><?php echo $whoami; ?></span>
</div>
<br/>
<div class="pg permissions">
    <span class="heading">User Permissions</span>
    <br/>
<?php
    foreach ($permissions as $key => $val)
    {
        $chk = $val ? 'checked' : '';
        
        echo<<<EOHTML
            <input class="chkPerm" type="checkbox" name="perms[]" value="{$key}" {$chk} /><span class="chkPerm">{$key}</span><br/>
EOHTML;
    }
?>
</div>
<br/>
<div class="pg edit_control">
    <input type="submit" value="Save" />
    <input type="reset" value="Reset" />
</div>
</form>
set permissions here
<br/>

<pre><?php var_export($info); ?></pre>
<pre><?php var_export($permissions); ?></pre>