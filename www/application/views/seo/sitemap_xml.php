<?php
$params = array(
    'sitemap_urls'=>array(),
);

extract($params, EXTR_SKIP);

echo '<?xml version="1.0" encoding="UTF-8"?>';
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach($sitemap_urls as $sitemap): ?>
    <url>
        <loc><?=$sitemap['loc']?></loc>
        <lastmod><?=$sitemap['lastmod']?></lastmod>
    </url>
<?php endforeach; // sitemap_urls ?>
</urlset>
