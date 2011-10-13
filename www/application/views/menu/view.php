<?php

$params_info = array(
    'status'=>array(),
    'name'=>'',
    'notes'=>'',
    'address'=>'',
    'latitude'=>0,
    'longitude'=>0,
    'numbers'=>'',
    'hours'=>'',
);

$params = array(
    'dbg' => array(),
    'is_metadata' => false,
    'info' => $params_info,
    'links' => array(),
    'mdts' => array(),
);

extract($params, EXTR_SKIP);

?>
<?php
if ($is_metadata)
{
    echo<<<EOHTML
        <div class="pg pg_bottom ismdt">
            <input type="button" value="edit menu" onclick="location.href='/menu/edit_metadata/{$id}'" />
        </div>
EOHTML;
}
?>
<div class="pg info">
    <div class="name"><?php echo $info['name']; ?></div>
    <div class="details"><?php echo nl2br($info['notes']); ?></div>
    <div class="address"><?php echo nl2br($info['address']); ?></div>
    <div class="phone"><?php echo nl2br($info['numbers']); ?></div>
    <div class="hours"><?php echo nl2br($info['hours']); ?></div>
</div>
<div class="pg links">
<?php
    foreach ($links as $link)
    {
        $label = $link['label'];
        $url = $link['url'];

        if (empty($label)) $label = $url;

        echo<<<EOHTML
            <div class="link"><a target="_blank" href="{$url}">{$label}</a></div>
EOHTML;
    }
?>
</div>
<div class="pg menus">
<?php foreach ($mdts as $mdt) { ?>
    <div class="menu">
        <div class="info heading">
            <div class="h_name"><?php echo $mdt['name']; ?></div>
            <div class="h_notes"><?php echo $mdt['notes']; ?></div>
        </div>
        <div class="items">
        <?php
            $b_alt = false;
            foreach ($mdt['items'] as $item)
            {
                $b_alt = !$b_alt;
                $css = $b_alt ? 'zalt' : '';
                $notes = '';

                if (!empty($item['notes']))
                    $notes = "<div class=\"notes\">{$item['notes']}</div>";

                echo<<<EOHTML
                    <div class="group {$css}">
                        <div class="item">{$item['item']}</div>
                        <div class="price">{$item['price']}</div>
                        <div class="clear"></div>
                        {$notes}
                    </div>
EOHTML;
            }
        ?>
        </div>
    </div>
<?php }// foreach ($mdts as $mdt) ?>
</div>
