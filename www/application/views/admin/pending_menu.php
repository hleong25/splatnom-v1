<div class="pg pg_bottom" style="text-align: right;">
    <input type="button" value="Refresh" onclick="location.href='/<?php echo $myurl; ?>'"/>
    <br/>
    <span>Mission here is to just search if the menu has been entered before.  Just do a simple search.</span>
</div>
<div class="pg pg_bottom">
    <div class="heading">Information</div>
    <div class="data">
         <span>pending id: </span><span><?php echo $pending_id; ?></span>
        <br/>
        <?php
            foreach ($sites as $site)
            {
                if (empty($site))
                    continue;

                echo<<<EOHTML
                <span>website: </span><a href="{$site}" target="_blank">{$site}</a>
                <br/>
EOHTML;
            }
        ?>
        <br/>
        <div class="new_imgs">
        <?php
            foreach ($imgs as $img)
            {
                $img_link = "/images/get/pending/org/{$pending_id}/{$img['filename']}";
                $thumbnail_link = "/images/get/pending/sm/{$pending_id}/{$img['filename']}";
                echo<<<EOHTML
                    <a href="$img_link" target="_blank"><img class="menu" src="$thumbnail_link" /></a>
EOHTML;
            }
        ?>
        </div>
    </div>
</div>
<div class="pg pg_bottom">
    <div class="heading">Search</div>
    <div class="data">
        <form id="pending" enctype="multipart/form-data" method="post" action="/<?php echo $myurl; ?>" > 
            <input type="hidden" name="action" value="search" />
            <input class="jq_watermark" type="textbox" name="name" title="Name" value="<?php echo $search_arg['name']; ?>" />
            <input class="jq_watermark" type="textbox" name="location" title="Location" value="<?php echo $search_arg['location']; ?>" />
            <input type="submit" value="Submit"/>
        </form>
        <span class="search_result">
            <?php
                if (isset($search_rst))
                    echo 'Search resulted in '.count($search_rst).' rows.';
            ?>
        </span>
    </div>
</div>
<div class="pg pg_bottom">
    <div class="heading">&nbsp;</div>
    <div class="data">
        <input type="button" value="New Menu Approve!!!" onclick="location.href='/admin/transfer_menu/<?php echo $pending_id; ?>'" />
        <input type="button" value="Cancel" onclick="location.href='/<?php echo $myurl; ?>'"/>
    </div>
    <div class="msg">
        <span class="err"><?php echo $err_msg; ?></span>
    </div>
</div>
<div class="pg"><pre><?php var_export($_GET); ?></pre></div>
