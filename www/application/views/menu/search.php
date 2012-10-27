<?php
$params = array(
    'query'=>'',
    'location'=>'',
    'msg'=>false,
    'places'=>array(),
    'dbg' => false,
);

extract($params, EXTR_SKIP);

?>
<div class="pg">
    <div class="pg_bottom search">
        <form id="searchit" method="get" action="/menu/search">
            <label>
                <span class="hint">Look for 'fish tacos' or 'Japanese'</span>
                <input class="watermark query" type="text" name="query" placeholder="Search" value="<?php echo $query; ?>"/>
            </label>
            <label>
                <span class="hint">Location</span>
                <input class="watermark location" type="text" name="location" placeholder="Location" value="<?php echo $location; ?>"/>
            </label>
            <label>
                <span class="hint">&nbsp;</span>
                <button class="button search" type="submit">Search</button>
            </label>
        </form>
    </div>
    <div class="msg">
        <span><?=$msg;?></span>
    </div>
    <div class="found">
    <?php if (!empty($places)): ?>
        <ul class="lstplaces">
            <?php foreach ($places as $idx => $place):
                $idx_no = $idx + 1;

                $menu_id = $place['menu_id'];
                $distance = sprintf('%0.2f', $place['distance']);
                $score = sprintf('%0.2f', $place['score']);

                if ($score < 0.5)
                    break;

                $name = $place['name'];
                $addy = $place['address'];
                $open = nl2br($place['hours']);

                $slug = Util::slugify($name);
                $link = '<a href="/menu/view/'.$menu_id.'-%s">%s</a>';

                $name = sprintf($link, $slug, $name);
            ?>
                <li id="place<?=$idx_no?>" class="place">
                    <span class="place_no"><?=$idx_no?>.</span>
                    <div class="info">
                        <span class="name"><?=$name?></span><br/>
                        <span class="addy"><?=$addy?></span><br/>
                        <span class="hours"><?=$open?></span>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php else: ?>
        <p>
            Nothing was found, try the <a href="/home/gmapmenu" class="showlink">map menus</a> feature to see what we have.
        </p>
    <?php endif; ?>
    </div>
</div>
<?php if (!empty($dbg)): ?>
<div class="pg"><pre><?=var_export($dbg)?></pre></div>
<?php endif; ?>
