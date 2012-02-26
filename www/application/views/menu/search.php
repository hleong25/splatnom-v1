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
    <div class="search">
        <form id="searchit" method="get" action="/menu/search">
            <div class="fq">
                <span class="hint">Look for 'fish tacos' or 'Japanese'</span>
                <input class="jq_watermark query" type="text" name="query" title="Search" value="<?php echo $query; ?>"/>
            </div>
            <div class="fq">
                <span class="hint">Zip code</span>
                <input class="jq_watermark location" type="text" name="location" title="Zip code" value="<?php echo $location; ?>"/>
            </div>
            <button class="search">Search</button>
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
                    <span class="place_no clearfix"><?=$idx_no?>.</span>
                    <div class="info">
                        <span class="name"><?=$name?></span><br/>
                        <span class="addy"><?=$addy?></span><br/>
                        <span class="hours"><?=$open?></span>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
    </div>
</div>
<?php if (!empty($dbg)): ?>
<div class="pg"><pre><?=var_export($dbg)?></pre></div>
<?php endif; ?>
