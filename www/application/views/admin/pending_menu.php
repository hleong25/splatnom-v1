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
        <span>website: </span><a href="http://<?php echo $site; ?>" target="_blank"><?php echo $site; ?></a>
        <br/>
        <div class="new_imgs">
        <?php
            foreach ($imgs as $img)
            {
                $img_link = "/ws/getimage/{$img}";
                echo<<<EOHTML
                    <a href="$img_link" target="_blank"><img class="menu" src="$img_link" /></a>
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
</div>

