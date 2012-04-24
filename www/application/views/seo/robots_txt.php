<?php
$params = array(
    'disallow' => array(),
    'sitemap' => '',
);

extract($params, EXTR_SKIP);

if (empty($disallow))
{
    // make sure there's atleast one item
    $disallow[] = '';
}

?>
User-agent: *

<?php foreach ($disallow as $item): ?>
Disallow: <?=$item?>

<?php endforeach; // disallow ?>

Sitemap: <?=$sitemap?>
