<?php

class ExportController
    extends Controller
{
    function onAction_list()
    {
        if (!Util::getPermissions('admin'))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->addCss('table');

        $export = $this->Export;

        $menus = $export->getMenus();

        $this->set('menus', $menus);
        //$this->set('dbg', $menus);
    }

    function onAction_menus($id=null)
    {
        if (!Util::getPermissions('admin'))
        {
            $this->redirect('/home/main');
            return;
        }

        if (empty($id) && empty($_POST['menu_ids']))
        {
            $this->redirect('/export/list');
            return;
        }

        $menu_ids = array();

        if (!empty($id))
            $menu_ids[] = $id;

        if (!empty($_POST['menu_ids']))
            $menu_ids = $_POST['menu_ids'];

        if (empty($menu_ids))
        {
            $this->redirect('/export/list');
            return;
        }

        $export = array();
        $export['version'] = 1;
        $export['menus'] = array();

        $menu = new MenuModel();
        foreach ($menu_ids as $menu_id)
        {
            $info = $menu->getMenuInfo($menu_id);
            $links = $menu->getMenuLinks($menu_id);
            $imgs = $menu->getMenuImgs($menu_id);
            $sections = $menu->getSection($menu_id);
            $mdts = $menu->getMetadata($menu_id, $sections);

            $out = array(
                'info' => $info,
                'links' => $links,
                'imgs' => $imgs,
                'metadatas' => $mdts,
            );

            $this->export_normalize($out);
            $export['menus'][] = $out;
        }

        $dbg = array('id'=>$id, 'post'=>$_POST, 'menu_ids'=>$menu_ids);
        $dbg = $export;
        $this->set('dbg', $dbg);
    }

    // same as menu::export_normalize
    function export_normalize(&$datas)
    {
        // clear status
        unset($datas['info']['status']);

        // clear the section_id and metadata_id
        foreach ($datas['metadatas'] as &$mtd)
        {
            $mtd['section_id'] = -1;

            foreach ($mtd['items'] as &$item)
            {
                $item['metadata_id'] = -1;
            }
        }
    }
}
