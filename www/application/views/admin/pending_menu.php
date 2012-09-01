<?php
$search_args = array(
    'name' => '',
    'location' => '',
);

$params = array(
    'pending_id' => '',
    'err_msg' => '',
    'sites' => array(),
    'imgs' => array(),
    'search_arg' => $search_args,
    'search_msg' => '',
    'search_rst' => array(),
);

extract($params, EXTR_SKIP);
?>
<div class="pg pg_bottom" style="text-align: right;">
    <input class="button" type="button" value="Refresh" onclick="location.href='/<?php echo $myurl; ?>'"/>
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
            <label>
                <span class="hint">Name</span>
                <input class="watermark" type="textbox" name="name" placeholder="Name" value="<?php echo $search_arg['name']; ?>" />
            </label>
            <label>
                <span class="hint">Location</span>
                <input class="watermark" type="textbox" name="location" placeholder="Location" value="<?php echo $search_arg['location']; ?>" />
            </label>
            <label>
                <span class="hint">&nbsp;</span>
                <input class="button" type="submit" value="Submit"/>
            </label>
        </form>
    </div>
    <div class="search">
        <span class="search_msg"><?=$search_msg?></span>
        <?php if (!empty($search_rst)): ?>
            <table class="tblDefault">
                <thead>
                    <td>#</td>
                    <td>name</td>
                    <td>address</td>
                    <td>distance</td>
                    <td>score</td>
                </thead>
                <tbody>
                <?php foreach ($search_rst as $idx => $place):
                        $menu_id = $place['menu_id'];
                        $distance = sprintf('%0.2f', $place['distance']);
                        $score = sprintf('%0.2f', $place['score']);

                        if ($score < 0.5)
                            break;

                        $name = $place['name'];
                        $slug = Util::slugify($name);
                        $link = '<a href="/menu/view/'.$menu_id.'-%s">%s</a>';


                        $id = sprintf($link, $slug, $menu_id);
                        $idx_no = sprintf($link, $slug, $idx+1);
                        $name = sprintf($link, $slug, $name);
                        $address = sprintf($link, $slug, $place['address']);
                        $distance = sprintf($link, $slug, $distance);
                        $score = sprintf($link, $slug, $score);
                ?>
                    <tr>
                        <td><?=$idx_no?></td>
                        <td><?=$name?></td>
                        <td><?=$address?></td>
                        <td><?=$distance?></td>
                        <td><?=$score?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>
<div class="pg pg_bottom">
    <div class="heading">&nbsp;</div>
    <div class="data">
        <input class="button" type="button" value="New Menu Approve!!!" onclick="location.href='/admin/transfer_menu/<?php echo $pending_id; ?>'" />
        <input class="button" type="button" value="Cancel" onclick="location.href='/<?php echo $myurl; ?>'"/>
    </div>
    <div class="msg">
        <span class="err"><?php echo $err_msg; ?></span>
    </div>
</div>
