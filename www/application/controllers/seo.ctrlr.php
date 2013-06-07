<?php

class SeoController
    extends Controller
{
    function onAction_robots_txt()
    {
        $this->m_bRender = false;

        // plain text file
        header('Content-Type: text/plain');

        $disallow = array(
            '/admin',
            '/event/edit',
            '/login',
            '/menu/edit_metadata',
            '/menu/new',
            '/menu/purge',
            '/user',
        );

        $site = Util::getDomain();
        $sitemap = "http://{$site}/sitemap.xml";

        $this->set('disallow', $disallow);
        $this->set('sitemap', $sitemap);
    }

    function onAction_sitemap_xml()
    {
        $this->m_bRender = false;

        // xml file
        header('Content-type: text/xml');

        $all_items = $this->Seo->getAllSeoItems();

        $site = Util::getDomain();

        $sitemap_urls = array();
        foreach ($all_items as $item)
        {
            $type = $item['type'];
            $id = $item['id'];
            $name = $item['name'];
            $ts = $item['ts'];
            $mod_ts = $item['mod_ts'];

            $slug = Util::slugify($name);

            if ($mod_ts < $ts)
                $mod_ts = $ts;

            $mod_ts = substr($mod_ts, 0, 10);

            $url = "http://{$site}/{$type}/view/{$id}-{$slug}";

            $sitemap_urls[] = array(
                'loc' => $url,
                'lastmod' => $mod_ts,
            );
        }

        $this->set('sitemap_urls', $sitemap_urls);
    }

}
