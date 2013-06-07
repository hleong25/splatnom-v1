<?php
$params_info = array(
    'status'=>array(),
    'name'=>'',
    'notes'=>'',
    'address'=>'',
    'latitude'=>0,
    'longitude'=>0,
    'dates'=>'',
    'imgs'=>array(),
);

$params = array(
    'dbg' => array(),
    'event_id' => 0,
    'info' => $params_info,
    'vendors' => array(),
);

extract($params, EXTR_SKIP);

$cover_img = '';
$cover_img_full = '';
if (!empty($info['cover_img']['file_img']))
{
    $cover_img = $info['cover_img']['file_img'];
    $cover_img_full = "/images/get/event/org/$event_id/$cover_img";
}

?>
<div class="pg">
    <div class="cover_img">
        <img src="<?=$cover_img_full?>"/>
    </div>

    <div class="info">
        <p class="name"><?=$info['name']?></p>
        <p class="notes"><?=$info['notes']?></p>
    </div>

    <div class="list_vendors">
        <ul class="list_vendors">
        <?php foreach ($vendors as $vendor):
            $ordinal = $vendor['ordinal'];
            $name = $vendor['name'];
            $style_detailed = $vendor['is_detailed'] ? 'detailed' : '';
        ?>
            <li class="goto_vendor">
                <a class="goto_vendor <?=$style_detailed?>" href="#<?=$ordinal?>"><?=$name?></a>
            </li>
        <?php endforeach; // foreach ($vendors as $vendor): ?>
        </ul>
    </div>

<?php foreach ($vendors as $vendor):
    $ordinal = $vendor['ordinal'];
    $name = $vendor['name'];
    $section = $vendor['section'];
    $description = $vendor['description'];

    if (!empty($section))
        $section = " ($section)";
?>
    <a name="<?=$ordinal?>"></a>
    <ul class="vendor">
        <li class="name">
            <span class="name"><?=$name?><?=$section?></span>
        </li>
        <li class="description">
            <?=$description?>
        </li>
    </ul>
<?php endforeach; // foreach ($vendors as $vendor): ?>

    <p class="warning">
        ** Information subject to change without notice **
    </p>
</div>
<?php if (false && !empty($info)): ?><pre class="pg"><?=var_export($info)?></pre><?php endif; ?>
<?php if (false && !empty($vendors)): ?><pre class="pg"><?=var_export($vendors)?></pre><?php endif; ?>
