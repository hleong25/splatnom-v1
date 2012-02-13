<?php
$params = array(
    'new_menu_done' => false,
    'is_user' => false,
);

extract($params, EXTR_SKIP);
?>
<div id="new_menu" class="pg">
<?php
if ($new_menu_done === false)
{
?>
    <span id="help">Help us by telling us about your favorite menu!</span>
    <br/>
    <?php if ($is_user !== true): ?>
        <span id="err">Login to keep track of this menu.</span>
    <?php endif; ?>
    <br/>
    <br/>
    <form id="frmNewMenu" enctype="multipart/form-data" method="post" action="/menu/new">
        <fieldset id="menu_urls">
            <legend style="padding: 0px 3px;">Web urls</legend>
            <input class="jq_watermark site_menu" type="text" name="url[]" title="Site1"/>
            <input class="jq_watermark site_menu" type="text" name="url[]" title="Site2"/>
            <input class="jq_watermark site_menu" type="text" name="url[]" title="Site3"/>
            <input class="jq_watermark site_menu" type="text" name="url[]" title="Site4"/>
            <input class="jq_watermark site_menu" type="text" name="url[]" title="Site5"/>
        </fieldset>
        <br/>
        <br/>
        <fieldset id="menu_files">
            <legend style="padding: 0px 3px;">Upload menus</legend>
            <div class="new_img">
                <input class="file" type="file" name="imgs[]"/>
            </div>
            <div class="new_img">
                <input class="file" type="file" name="imgs[]"/>
            </div>
            <div class="new_img">
                <input class="file" type="file" name="imgs[]"/>
            </div>
            <div class="new_img">
                <input class="file" type="file" name="imgs[]"/>
            </div>
            <div class="new_img">
                <input class="file" type="file" name="imgs[]"/>
            </div>
            <input id="btnAddMore" type="button" value="Add more"/>
        </fieldset>
        <br/>
        <input type="submit" value="Submit" />
    </form>
<?php
} // if ($new_menu_done === false)
else
{
?>
    <span id="msg">Thank you for the menu!</span>
    <br/>
    <p>The menu is waiting to be approved. Patience is a virtue.</p>
    <br/>
    <br/>
    <a href="/menu/new">More menus??? Schanks you very much!</a>
<?php
} // else
?>
</div>
