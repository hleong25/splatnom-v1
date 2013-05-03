<?php

class ImportController
    extends Controller
{
    function onAction_list($remote_site=null)
    {
        if (!Util::getPermissions('admin'))
        {
            $this->redirect('/home/main');
            return;
        }
        else if (!empty($_GET) && isset($_GET['remote_site']))
        {
            $this->redirect('/import/list/'.$_GET['remote_site']);
            return;
        }

        $this->addCss('import/import.list');

        $this->addJqueryUi();
        $this->addJs('jquery.watermark.min', WEB_PATH_OTHER);
        $this->addJs('import/import.list');

        $import = $this->Import;

        $this->set('remote_site', $remote_site);

        if (!empty($remote_site))
        {
            $list = $import->getList($remote_site);
            $this->set('remote_menus', $list);
        }
    }

    function onAction_local()
    {
        if (!Util::getPermissions('admin'))
        {
            $this->redirect('/home/main');
            return;
        }

        $local_file = @$_POST['local_file'];

        if (!file_exists($local_file))
        {
            $this->redirect('/import/list');
            return;
        }

        $this->addCss('import/import.local');

        $imported_menus = $this->import_menu_file($local_file);
        if (empty($imported_menus))
        {
            return;
        }

        $this->set('imported_menus', $imported_menus);
        //$this->set('dbg', $imported_menus);
    }

    function onAction_menus($remote_site=null, $id=null)
    {
        if (!Util::getPermissions('admin'))
        {
            $this->redirect('/home/main');
            return;
        }

        if (empty($remote_site))
        {
            $this->redirect('/import/list');
            return;
        }
        else if (empty($id) && empty($_POST['menu_ids']))
        {
            $this->redirect('/import/list/'.$remote_site);
            return;
        }

        $this->addCss('import/import.menus');

        $menu_ids = array();

        if (!empty($id))
            $menu_ids[] = $id;

        if (!empty($_POST['menu_ids']))
            $menu_ids = $_POST['menu_ids'];

        if (empty($menu_ids))
        {
            $this->redirect('/import/list'.$remote_site);
            return;
        }

        $import = $this->Import;

        $this->set('remote_site', $remote_site);

        $import_menus = $import->getMenus($remote_site, $menu_ids);
        //$this->set('dbg', $import_menus);
        $this->set('remote_menus', $import_menus);

        $imported_menus = $this->import_menu_file($import_menus);
        if (empty($imported_menus))
        {
            return;
        }

        $this->set('imported_menus', $imported_menus);
        //$this->set('dbg', $imported_menus);
    }

    function import_menu_file($file)
    {
        $importer = new ImportArchiver($file);
        $bInit = $importer->init();

        if ($bInit !== true)
        {
            $err_msg = "Failed to init import file: {$file}";
            $this->set('err_msg', $err_msg);
            return false;
        }

        $menus = $importer->unzip();

        $imported_menus = array();
        foreach ($menus as $menu)
        {
            $imported = $this->import_helper($menu);
            if (empty($imported))
            {
                $err_msg = "Failed to import menus: {$file}";
                $this->set('err_msg', $err_msg);
                Util::logit($err_msg, __FILE__, __LINE__);
                return false;
            }

            $imported_menus[] = $imported;
        }

        return $imported_menus;
    }

    function import_helper($menu)
    {
        $data = $menu['data'];
        $imgs = $menu['imgs'];

        ImportModel::menu_normalize($data);

        $menu = new MenuModel();
        $new_id = $menu->createMenu();

        // set menu status as 'purge' first.
        // if everthing is good, then set it to 'new'.
        $import = $menu->updateMenu($new_id, 'purge');
        if (!$import)
        {
            $this->set('err_msg', 'Failed to set status for imported menu');
            return false;
        }

        // import info
        $info = $data['info'];
        $q_info = &$info;

        $import = $menu->updateMenuInfo($new_id, $q_info);
        if (!$import)
        {
            $this->set('err_msg', 'Failed to import info');
            return false;
        }

        // import links
        $links = $data['links'];
        $q_links = &$links;
        $import = $menu->updateMenuLinks($new_id, $q_links);
        if (!$import)
        {
            $this->set('err_msg', 'Failed to import links');
            return false;
        }

        // import metadata
        $mdts = $data['metadatas'];
        $q_mdts = &$mdts;
        $import = $menu->updateMenuSectionAndMetadata($new_id, $q_info, $q_mdts);
        if (!$import)
        {
            $this->set('err_msg', 'Failed to import menu metadata');
            return false;
        }

        // create menu image directory
        $menu_img_path = OS_MENU_PATH . DS . $new_id;
        if (mkdir($menu_img_path) == false)
        {
            $err_msg = "Failed to create menu directory: {$menu_img_path}";
            Util::logit($err_msg, __FILE__, __LINE__);
            $this->set('err_msg', $err_msg);
            return false;
        }

        // transfer the images to the image directory
        $uploader = new UploadHandler($menu_img_path);
        $uploaded_imgs = array();

        if (!empty($imgs))
        {
            foreach ($imgs as $imported_img)
            {
                $tmp_name = $imported_img;
                $mime = mime_content_type($tmp_name); // TODO: might need to change this to finfo_file
                $file_ext = pathinfo($tmp_name, PATHINFO_EXTENSION);

                $import_img_file = array
                (
                    'tmp_name'=>$tmp_name,
                    'mime'=>$mime,
                    'file_ext'=>$file_ext,
                );

                $stats = $uploader->upload_helper_image($import_img_file, false);
                if (empty($stats))
                {
                    Util::logit("Failed to import image. File '{$tmp_name}', mime-type '{$mime}'", __FILE__, __LINE__);
                    return false;
                }

                $uploaded_imgs[] = $stats;
            }

            // import the images to the database
            $user_id = Util::getUserId();
            $db_imgs = $menu->insertMenuImages($new_id, $user_id, $uploaded_imgs);
            if (!$db_imgs)
            {
                Util::logit('Failed to import images to database.', __FILE__, __LINE__);
                //$this->set('err_msg', 'Failed to import menu images');
                return false;
            }
        }

        // setup the img_taggits
        $img_taggits = $data['img_taggits'];
        $q_img_taggits = &$img_taggits;
        $this->fix_imported_image_taggits($q_img_taggits, $uploaded_imgs, $q_mdts);

        // let's tag the images it!
        foreach ($q_img_taggits as &$img_taggits)
        {
            $new_filename = $img_taggits['new_img_name'];

            if (empty($new_filename))
                continue;

            $menu->updateTaggitsImage($new_id, $new_filename, $img_taggits, array());
        }

        // finally... set menu status as 'new' first.
        $import = $menu->updateMenu($new_id, 'new');
        if (!$import)
        {
            $this->set('err_msg', 'Failed to set status for imported menu');
            return false;
        }

        return array('id'=>$new_id, 'name'=>$info['name']);
    }

    function fix_imported_image_taggits(&$import_taggits, $uploaded_imgs, $db_mdts)
    {
        // fix the filename -> when files are uploaded here, it gets a new name.
        // we need to get that new name
        foreach ($import_taggits as $file_img => &$img_taggits)
        {
            $img_taggits['new_img_name'] = '';

            foreach ($img_taggits as &$taggit)
            {
                $taggit['new_img_name'] = '';

                foreach ($uploaded_imgs as $img)
                {
                    if (Util::strEndsWith($img['source_filename'], $file_img))
                    {
                        // yes -- the new_img_name is in two places.
                        // why?? I don't know... it's late
                        $taggit['new_img_name'] = $img['filename'];
                        $img_taggits['new_img_name'] = $img['filename'];
                    }
                }
            }
        }

        // go through all taggits and find the section_id and metadata_id for the corresponding section and metadata
        foreach ($import_taggits as $file_img => &$img_taggits)
        {
            foreach ($img_taggits as &$taggit)
            {
                // set a default sid/mid
                $taggit['sid'] = 0;
                $taggit['mid'] = 0;

                // go through all sections
                foreach ($db_mdts as &$db_section)
                {
                    // we found a matching section, let's go through all metadata
                    if ($db_section['name'] == $taggit['section'])
                    {
                        $sid = $db_section['section_id'];

                        // go through all metadata items from the section
                        foreach ($db_section['items'] as &$db_metadata)
                        {
                            // we found a matching metadata from the section
                            if ($db_metadata['label'] == $taggit['metadata'])
                            {
                                $mid = $db_metadata['metadata_id'];

                                // wow... we finally found the sid/mid
                                $taggit['sid'] = $sid;
                                $taggit['mid'] = $mid;
                            }
                        }
                    }
                }
            }

        }
    }
}

class ImportArchiver
{
    private $m_purge = true;
    private $m_zfile = false;
    private $m_zobj = false;

    function __construct($src, $purge=true)
    {
        $this->m_purge = $purge == true;
        $this->m_zfile = $src;
    }

    function __destruct()
    {
        if (!empty($this->m_zobj))
            @$this->m_zobj->close();

        if ($this->m_purge && !empty($this->m_zfile))
            @unlink($this->m_zfile);
    }

	function init()
    {
        $this->m_zobj = new ZipArchive();
        $ret = $this->m_zobj->open($this->m_zfile);
        if ($ret !== TRUE)
        {
            $err_str = $this->getZipError($ret);
            Util::logit("Error opening zip file '{$this->m_zfile}'. Error: {$err_str}", __FILE__, __LINE__);
            $this->m_purge = false;
            //@unlink($this->m_zfile);
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

    function unzip()
    {
        $dst = substr($this->m_zfile, 0, -4); // remove .zip extension

        if (file_exists($dst))
        {
            Util::logit("Unzip directory '{$dst}' already exists. Going to purge it.");
            Util::purge_dir($dst);
        }

        $res = $this->m_zobj->extractTo($dst);

        if ($res !== true)
        {
            Util::logit("Failed to unzip: {$this->m_zfile}");
            return false;
        }

        /*
            NOTE: unzipped export structure

            /root/
                + dir1/
                    + img/ {optional}
                        + file1.jpg
                        + file2.jpg
                    + menu.json.txt
                + dir2/
                    + menu.json.txt

        */

        $files = scandir($dst);

        $menus = array();
        foreach ($files as $file)
        {
            if ($file === '.' || $file === '..')
                continue;

            $new_menu_path = "{$dst}/{$file}";
            if (!is_dir($new_menu_path))
                continue;

            $json_file = "{$new_menu_path}/menu.json.txt";
            if (!file_exists($json_file))
                continue;

            $contents = file_get_contents($json_file);
            $data = json_decode($contents, true);

            if (!isset($data['info']))
            {
                Util::logit("Failed to decode file: {$json_file}");
                continue;
            }

            $menu = array();
            $menu['data'] = $data;
            $menu['imgs'] = array();

            $img_path = "{$new_menu_path}/img";
            $img_files = scandir($img_path);
            foreach ($img_files as $img)
            {
                if ($file === '.' || $file === '..')
                    continue;

                $img_file = "{$img_path}/{$img}";

                if (!is_file($img_file))
                    continue;

                $img_ext = pathinfo($img, PATHINFO_EXTENSION);
                $img_ext = strtolower($img_ext);

                switch ($img_ext)
                {
                    case 'jpg':
                    case 'jpeg':
                    case 'gif':
                    case 'png':
                        $menu['imgs'][] = $img_file;
                }
            }

            $menus[] = $menu;
        }

        return $menus;
    }

}
