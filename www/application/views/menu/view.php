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
            <input type="button" value="export menu" onclick="location.href='/menu/export/{$id}/json'" />
            <input type="button" value="edit menu" onclick="location.href='/menu/edit_metadata/{$id}'" />
        </div>
EOHTML;
}
?>
<div class="pg biz_info">
    <div class="name"><?php echo $info['name']; ?></div>
    <div class="details"><?php echo nl2br($info['notes']); ?></div>
    <br/>
    <div class="address"><?php echo nl2br($info['address']); ?></div>
    <br/>
    <div class="phone"><?php echo nl2br($info['numbers']); ?></div>
    <br/>
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
                $css_empty = empty($item['notes']) ? 'empty' : '';

                $forkit_url = "/menu/forkit/{$id}/{$mdt['section_id']}/{$item['metadata_id']}";
                $forkit_msg = 'Stick a fork in it if you like this item!';

                echo<<<EOHTML
                    <div class="group {$css}">
                        <div class="g_panel">
                            <a class="forkit" href="{$forkit_url}"><img src="/images/fork" title="{$forkit_msg}"/></a>
                        </div>
                        <div class="g_info">
                            <div class="label">{$item['label']}</div>
                            <div class="price">{$item['price']}</div>
                            <div class="clear"></div>
                            <div class="notes {$css_empty}">{$item['notes']}</div>
                        </div>
                        <div class="clear"></div>
                    </div>
EOHTML;
            }
        ?>
        </div>
    </div>
<?php }// foreach ($mdts as $mdt) ?>
</div>
