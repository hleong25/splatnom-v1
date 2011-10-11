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
    'info' => $params_info,
    'links' => array(),
    'mdts' => array(),
);

extract($params, EXTR_SKIP);

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
        <?php foreach($mdt['items'] as $item) { ?>
            <div class="group">
                <div class="item"><?php echo $item['item']; ?></div>
                <div class="price"><?php echo $item['price']; ?></div>
                <div class="clear"></div>
                <div class="notes"><?php echo $item['notes']; ?></div>
            </div>
        <?php } // foreach($mdt['items'] as $item) ?>
        </div>
    </div>
<?php }// foreach ($mdts as $mdt) ?>
</div>
<div class="pg dbg"><pre><?php var_export($dbg); ?></pre></div>
