<?php
$params = array(
    'new_menu_done' => false,
    'is_user' => false,
    'err_msg' => false,
);

extract($params, EXTR_SKIP);
?>
<?php if (!empty($new_menu_done)): ?>
<div id="thanks">
    <span>Thank you for the menu!</span>
    <br/>
    <p>The menu is waiting to be approved. Patience is a virtue.</p>
</div>
<?php endif; ?>
<div id="new_menu" class="pg">
    <span id="help">Help us by telling us about your favorite menu!</span>
    <br/>
    <?php if ($is_user !== true): ?>
        <span id="err">Login to keep track of this menu.</span>
        <br/>
    <?php endif; ?>
    <?php if (!empty($err_msg)): ?>
        <span id="err"><?=$err_msg?></span>
        <br/>
    <?php endif; ?>
    <br/>
    <form id="frmNewMenu" enctype="multipart/form-data" method="post" action="/menu/new">
        <div class="group_new">
            <div class="header">Web urls</div>
            <div class="hint">Include sites that are associated with this menu.</div>
            <input class="watermark site_menu" type="text" name="url[]" placeholder="Site"/>
            <input class="watermark site_menu" type="text" name="url[]" placeholder="Site"/>
            <input class="watermark site_menu" type="text" name="url[]" placeholder="Site"/>
            <input class="watermark site_menu" type="text" name="url[]" placeholder="Site"/>
            <input class="watermark site_menu" type="text" name="url[]" placeholder="Site"/>
        </div>
        <div class="group_new">
            <div class="header">Upload menus</div>
            <div class="hint">Supported files include: gif, jpg and png.</div>
            <input class="file" type="file" name="imgs[]"/>
            <input class="file" type="file" name="imgs[]"/>
            <input class="file" type="file" name="imgs[]"/>
            <input class="file" type="file" name="imgs[]"/>
            <input class="file" type="file" name="imgs[]"/>
            <input id="btnAddMore" class="button" type="button" value="Add more"/>
        </div>
        <input class="button" type="submit" value="Submit" />
    </form>
</div>
