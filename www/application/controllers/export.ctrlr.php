<?php

class ExportController
    extends Controller
{
    function isRemoteCall()
    {
        $request = false;
        $key = false;

        if (function_exists('apache_request_headers'))
        {
            $request = apache_request_headers();
            $key = 'X-SPLATNOM-REMOTE';
        }
        else
        {
            $request = $_SERVER;
            $key = 'HTTP_X_SPLATNOM_REMOTE';
        }

        if (isset($request[$key]) && !empty($request[$key]))
        {
            $remote = $request[$key];
            return true;
        }

        return false;
    }

    function onAction_list($output=null)
    {
        if (!$this->isRemoteCall() && !Util::getPermissions('admin'))
        {
            $this->redirect('/home/main');
            return;
        }

        $this->m_bRender = empty($output);

        $this->addCss('export/export.list');

        $this->addJqueryUi();
        $this->addJs('export/export.list');

        $export = $this->Export;

        $menus = $export->getMenus();

        $this->set('output', $output);
        $this->set('menus', $menus);
        //$this->set('dbg', $menus);
    }

    function onAction_menus($id=null)
    {
        if (!$this->isRemoteCall() && !Util::getPermissions('admin'))
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

        $this->m_bRender = false;

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
                'id' => $menu_id,
                'info' => $info,
                'links' => $links,
                'imgs' => $imgs,
                'metadatas' => $mdts,
            );

            $this->export_normalize($out);
            $export['menus'][] = $out;
        }

        $menu_archive = new ExportArchiver();
        $res = $menu_archive->init();
        if (!empty($res))
        {
            $menu_archive->build($export);
            $menu_archive->output();
            return;
        }
        else
        {
            $dbg = array('id'=>$id, 'post'=>$_POST, 'menu_ids'=>$menu_ids);
            $dbg = $export;
            $this->set('dbg', $dbg);
        }
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

        // reset the array of images to be just image name
        foreach ($datas['imgs'] as $idx => $img)
        {
            $datas['imgs'][$idx] = $img['filename'];
        }
    }
}

class ExportArchiver
{
    private $m_zfile = false;
    private $m_zobj = false;

    function __construct()
    {
        // empty
    }

    function __destruct()
    {
        if (!empty($this->m_zobj))
            @$this->m_zobj->close();

        if (!empty($this->m_zfile))
            @unlink($this->m_zfile);
    }

	function init()
    {
        // create temp file
        $this->m_zfile = tempnam(OS_TEMP_PATH, 'zip');
        if ($this->m_zfile === false)
        {
            Util::logit("Error creating temp file '{$this->m_zfile}'.", __FILE__, __LINE__);
            return false;
        }

        $this->m_zobj = new ZipArchive();
        $ret = $this->m_zobj->open($this->m_zfile, (ZipArchive::CREATE | ZIPARCHIVE::OVERWRITE));
        if ($ret !== TRUE)
        {
            $err_str = $this->getZipError($ret);
            Util::logit("Error creating zip file '{$this->m_zfile}'. Error: {$err_str}", __FILE__, __LINE__);
            @unlink($this->m_zfile);
            return false;
        }

        return true;
    }

    function getZipError($errno)
    {
        switch ($errno)
        {
            case ZIPARCHIVE::ER_EXISTS:
                return 'ZIPARCHIVE::ER_EXISTS';
            case ZIPARCHIVE::ER_INCONS:
                return 'ZIPARCHIVE::ER_INCONS';
            case ZIPARCHIVE::ER_INVAL:
                return 'ZIPARCHIVE::ER_INVAL';
            case ZIPARCHIVE::ER_MEMORY:
                return 'ZIPARCHIVE::ER_MEMORY';
            case ZIPARCHIVE::ER_NOENT:
                return 'ZIPARCHIVE::ER_NOENT';
            case ZIPARCHIVE::ER_NOZIP:
                return 'ZIPARCHIVE::ER_NOZIP';
            case ZIPARCHIVE::ER_OPEN:
                return 'ZIPARCHIVE::ER_OPEN';
            case ZIPARCHIVE::ER_READ:
                return 'ZIPARCHIVE::ER_READ';
            case ZIPARCHIVE::ER_SEEK:
                return 'ZIPARCHIVE::ER_SEEK';
            default:
                return "Unknown error number {$errno}.";
        }
    }

    function build($export_data)
    {
        $build_txt = "\xEF\xBB\xBF".var_export($export_data, true);
        $this->addFromString('build.txt', $build_txt);

        $img_model = new ImagesModel();

        $menus = $export_data['menus'];
        foreach ($menus as $idx => $menu)
        {
            $menu_id = $menu['id'];

            $info = $menu['info'];
            $name = $info['name'];

            $path = "{$menu_id}-".Util::slugify($name);
            $this->addEmptyDir($path);

            $json = json_encode($menu);
            $this->addFromString($path.'/menu.json.txt', $json);

            $img_dst_path = $path.DS.'img';
            $this->addEmptyDir($img_dst_path);
            $menu_images = $menu['imgs'];
            foreach ($menu_images as $menu_img)
            {
                $img_info = $img_model->getMenuImage($menu_id, $menu_img);
                $img_src = $img_info['path'] . DS . $img_info['filename'];
                $img_dst = $img_dst_path . DS . $img_info['filename'];

                $this->addFile($img_src, $img_dst);
            }
        }
    }

    function output()
    {
        $this->m_zobj->close();

        $filename = uniqid('menu_export.').'.zip';

        header('Content-Type: application/zip');
        header('Content-Transfer-Encoding: binary');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        header('Content-Length: ' . filesize($this->m_zfile));
        header('X-SPLATNOM-FILENAME: '.$filename.'');
        readfile($this->m_zfile);

        return true;
    }

    function addEmptyDir($dirname)
    {
        $res = $this->m_zobj->addEmptyDir($dirname);

        if (empty($res))
            Util::logit("Failed to add directory '{$dirname}'.", __FILE__, __LINE__);
    }

    function addFromString($localname, $contents)
    {
        $res = $this->m_zobj->addFromString($localname, $contents);

        if (empty($res))
            Util::logit("Failed to add from string '{$localname}'.", __FILE__, __LINE__);
    }

    function addFile($src, $localname)
    {
        $res = $this->m_zobj->addFile($src, $localname);

        if (empty($res))
            Util::logit("Failed to add file '{$src}' to '{$localname}'.", __FILE__, __LINE__);
    }
}
