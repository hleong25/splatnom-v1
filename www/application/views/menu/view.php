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
    'forkits' => array(),
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

                $section_id = $mdt['section_id'];
                $metadata_id = $item['metadata_id'];

                // notes
                $notes_css = empty($item['notes']) ? 'empty' : '';

                // fork its
                $forkit_msg = 'Stick a fork in it if you like this item!';

                if (isset($forkits[$section_id][$metadata_id]))
                {
                    $forkit = $forkits[$section_id][$metadata_id];

                    if ($forkit['me'])
                    {
                        // if this forkit is me... then the "after" is everything before me
                        $forkit_url = array
                        (
                            'now'   => "/menu/unforkit/{$id}/{$section_id}/{$metadata_id}",
                            'after' => "/menu/forkit/{$id}/{$section_id}/{$metadata_id}",
                        );

                        $forkit_css = array
                        (
                            'now'   => 'forkit',
                            'after' => '',
                        );

                        $forkit_cnt = array
                        (
                            'now'   => $forkit['cnt'],
                            'after' => $forkit['cnt']-1,
                        );
                    }
                    else
                    {
                        // this forkit is not me, so the "after" is everything after me
                        $forkit_url = array
                        (
                            'now'   => "/menu/forkit/{$id}/{$section_id}/{$metadata_id}",
                            'after' => "/menu/unforkit/{$id}/{$section_id}/{$metadata_id}",
                        );

                        $forkit_css = array
                        (
                            'now'   => '',
                            'after' => 'forkit',
                        );

                        $forkit_cnt = array
                        (
                            'now'   => $forkit['cnt'],
                            'after' => $forkit['cnt']+1,
                        );
                    }

                }
                else
                {
                    $forkit_url = array
                    (
                        'now'   => "/menu/forkit/{$id}/{$section_id}/{$metadata_id}",
                        'after' => "/menu/unforkit/{$id}/{$section_id}/{$metadata_id}",
                    );

                    $forkit_css = array
                    (
                        'now'   => '',
                        'after' => 'forkit',
                    );

                    $forkit_cnt = array
                    (
                        'now'   => '',
                        'after' => 1,
                    );
                }

                if ($forkit_cnt['after'] < 1)
                    $forkit_cnt['after'] = '';

                echo<<<EOHTML
                    <div class="group {$css}">
                        <div class="g_panel">
                            <div class="forkit now">
                                <span class="cnt">{$forkit_cnt['now']}</span>
                                <img class="{$forkit_css['now']}" src="/images/fork" title="{$forkit_msg}" onclick="js_menu.forkit(this, '{$forkit_url['now']}');"/>
                            </div>
                            <div class="forkit after">
                                <span class="cnt">{$forkit_cnt['after']}</span>
                                <img class="{$forkit_css['after']}" src="/images/fork" title="{$forkit_msg}" onclick="js_menu.forkit(this, '{$forkit_url['after']}');"/>
                            </div>
                        </div>
                        <div class="g_info">
                            <div class="label">{$item['label']}</div>
                            <div class="price">{$item['price']}</div>
                            <div class="clear"></div>
                            <div class="notes {$notes_css}">{$item['notes']}</div>
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
