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
            '/login',
            '/menu/edit_metadata',
            '/menu/new',
            '/menu/purge',
            '/user',
        );

        $site = Util::getTopLevelDomain();
        $sitemap = "http://{$site}/sitemap.xml";

        $this->set('disallow', $disallow);
        $this->set('sitemap', $sitemap);
    }

    function onAction_sitemap_xml()
    {
        $this->m_bRender = false;

        // xml file
        header('Content-type: text/xml');

        $all_menus = $this->Seo->getAllMenus();

        $site = Util::getTopLevelDomain();

        $sitemap_urls = array();
        foreach ($all_menus as $menu)
        {
            $id = $menu['id'];
            $name = $menu['name'];
            $ts = $menu['ts'];
            $mod_ts = $menu['mod_ts'];

            $slug = Util::slugify($name);

            if ($mod_ts < $ts)
                $mod_ts = $ts;

            $mod_ts = substr($mod_ts, 0, 10);

            $url = "http://{$site}/menu/view/{$id}-{$slug}";

            $sitemap_urls[] = array(
                'loc' => $url,
                'lastmod' => $mod_ts,
            );
        }

        $this->set('sitemap_urls', $sitemap_urls);
    }

}
