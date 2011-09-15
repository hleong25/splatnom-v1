<?php

class MenuModel
    extends Model
{
    function saveNewMenu($addy)
    {
        /*
           select *
           from tblPendingMenu menu
           left join tblPendingMenuImages menu_img on menu.id = menu_img.pendingmenu_id
         */

        $this->beginTransaction();

        $query =<<<EOQ
            INSERT INTO tblPendingMenu (
                    site_addy
                    )
            VALUES (
                    :site
                   )
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':site' => $addy), __FILE__, __LINE__);
        if (!$prepare) return false;

        $new_id = $this->lastInsertId();

        $query =<<<EOQ
            INSERT INTO tblPendingMenuImages (
                    pendingmenu_id,
                    file_img
                    )
            VALUES (
                    :new_id,
                    :file_img
                   )
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $new_menu_img = array(':new_id' => $new_id, ':file_img' => '');
        $files = handle_upload_files();

        foreach ($files as $img)
        {
            $new_menu_img[':file_img'] = $img;

            $rst = $this->execute_log($prepare, $new_menu_img, __FILE__, __LINE__);
        }

        $this->commit();

        return true;
    }

    function getPendingMenuCount()
    {
        $query =<<<EOQ
            SELECT COUNT(*) AS cnt
            FROM tblPendingMenu
EOQ;

        $rst = $this->query($query);
        $cnt = (int) $rst->fetchColumn();

        return $cnt;
    }

    function getPendingMenus()
    {
        $query =<<<EOQ
            SELECT menu.*, COUNT(imgs.pendingmenu_id) as cnt_imgs
            FROM tblPendingMenu menu
            LEFT JOIN tblPendingMenuImages imgs ON menu.id = imgs.pendingmenu_id
            GROUP BY menu.id
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll();

        foreach ($rows as $key => $row)
        {
            $site = $row['site_addy'];
            if (stristr($site, 'http://'))
            {
                // if PHP >= 5.3 then do
                // $site = stristr($site, 'http://', true)

                $site = substr($site, 7);
            }

            if (stristr($site, 'https://'))
            {
                // if PHP >= 5.3 then do
                // $site = stristr($site, 'https://', true)

                $site = substr($site, 8);
            }

            $rows[$key]['site_addy'] = $site;
        }

        return $rows;
    }

    function purgePendingMenu($id)
    {
        // 1. find images in db
        // 2. cache img files
        // 3. delete from tblPendingMenuImages
        // 4. delete from tblPendingMenus
        // 5. move cached img files to purge storage

        $this->beginTransaction();

        $menu_id = array(':id' => $id);

        $query =<<<EOQ
            SELECT
                id,
                file_img
            FROM tblPendingMenuImages
            WHERE pendingmenu_id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        $purge_items = array();
        $new_files_rows = $prepare->fetchAll(PDO::FETCH_ASSOC);
        if (!$prepare)
        {
            $this->log_dberr($prepare, __FILE__, __LINE__);
            return false;
        }

        // cache to purge items
        foreach ($new_files_rows as $row)
        {
            $file_src = OS_UPLOAD_PATH . DS . $row['file_img'];
            $file_dst = OS_PURGE_PATH . DS . $row['file_img'];

            $purge_items[] = array
            (
                'id' => $row['id'],
                'src' => $file_src,
                'dst' => $file_dst
            );
        }

        $query =<<<EOQ
            DELETE FROM tblPendingMenuImages
            WHERE id = :id
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        foreach ($purge_items as $row)
        {
            $rst = $prepare->bindValue(':id', $row['id']);
            if (!$rst)
            {
                $this->log_dberr($rst, __FILE__, __LINE__);
                return false;
            }

            $rst = $prepare->execute();
            if (!$rst)
            {
                $this->log_dberr($rst, __FILE__, __LINE__);
                return false;
            }
        }

        $query =<<<EOQ
            DELETE FROM tblPendingMenu
            WHERE id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        // finally -- move files to purge storage
        foreach ($purge_items as $row)
        {
            $file_src = $row['src'];
            $file_dst = $row['dst'];

            @rename($file_src, $file_dst);
        }

        $this->commit();

        return true;
    }

    function getPendingMenu($id)
    {
        $menu = array();
        $menu_id = array(':id' => $id);

        $query =<<<EOQ
            SELECT
                site_addy
            FROM tblPendingMenu
            WHERE id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        $info = $prepare->fetch(PDO::FETCH_ASSOC);
        if (empty($info)) return false;
        $prepare->closeCursor();
        unset($prepare);

        $menu['site'] = $info['site_addy'];
        if (stristr($info['site_addy'], 'http://'))
        {
            // if PHP >= 5.3 then do
            // $menu['site'] = stristr($info['site_addy'], 'http://', true)

            $menu['site'] = substr($info['site_addy'], 7);
        }

        if (stristr($info['site_addy'], 'https://'))
        {
            // if PHP >= 5.3 then do
            // $menu['site'] = stristr($info['site_addy'], 'https://', true)

            $menu['site'] = substr($info['site_addy'], 8);
        }

        $query =<<<EOQ
            SELECT
                file_img
            FROM tblPendingMenuImages
            WHERE pendingmenu_id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        $imgs = $prepare->fetchAll(PDO::FETCH_ASSOC);
        $menu['imgs'] = array();
        foreach ($imgs as $img)
            $menu['imgs'][] = $img['file_img'];

        return $menu;
    }

    function pendingMenuApproved($pending_id)
    {
        /*
            select
                m.id, m.ts, vs.menu_status, m.site_addy, mi.*
            from tblMenu m
            left join vMenuStatus vs on m.mode_id = vs.id
            left join tblMenuImages mi on m.id = mi.menu_id
        */

        $pending_menu = $this->getPendingMenu($pending_id);
        if ($pending_menu == false)
            return false;

        $this->beginTransaction();

        $query =<<<EOQ
            SELECT id, menu_status
            FROM vMenuStatus
EOQ;

        $rst = $this->query($query);
        $rows = $rst->fetchAll(PDO::FETCH_ASSOC);
        $cacheMenuStatus = array();
        foreach ($rows as $row)
            $cacheMenuStatus[$row['menu_status']] = $row['id'];

        if (empty($cacheMenuStatus['new'])) return false;

        $query =<<<EOQ
            INSERT INTO tblMenu(
                mode_id,
                site_addy
            )
            VALUES (
                :mode_id,
                :site
            )
EOQ;

        $params = array(':mode_id'=>$cacheMenuStatus['new'], ':site'=>$pending_menu['site']);

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $menu_id = $this->lastInsertId();

        $query =<<<EOQ
            INSERT INTO tblMenuImages(
                menu_id,
                file_img
            ) VALUES (
                :menu_id,
                :file_img
            )
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $rst = $prepare->bindValue(':menu_id', $menu_id);
        if (!$rst)
        {
            $this->log_dberr($rst, __FILE__, __LINE__);
            return false;
        }

        foreach ($pending_menu['imgs'] as $file_img)
        {
            $rst = $prepare->bindValue(':file_img', $file_img);
            if (!$rst)
            {
                $this->log_dberr($rst, __FILE__, __LINE__);
                return false;
            }

            $rst = $prepare->execute();
            if (!$rst)
            {
                $this->log_dberr($rst, __FILE__, __LINE__);
                return false;
            }
        }

        $this->commit();

        // move pending images to menu directory
        $menu_img_path = OS_MENU_PATH . DS . $menu_id;
        if (mkdir($menu_img_path) == false)
        {
            logit("Failed to create menu directory: {$menu_img_path}", __FILE__, __LINE__);
            return false;
        }

        foreach ($pending_menu['imgs'] as $file_img)
        {
            $file_src = OS_UPLOAD_PATH . DS . $file_img;
            $file_dst = $menu_img_path . DS . $file_img;

            @rename($file_src, $file_dst);
        }

        // it's all good now... let's purge the pending info...
        $this->purgePendingMenu($pending_id);

        return $menu_id;
    }

    function purgeMenu($id)
    {
        $menu_id = array(':id' => $id);

        $query =<<<EOQ
            UPDATE tblMenu
            SET mode_id = (SELECT id FROM vMenuStatus WHERE menu_status = 'purge')
            WHERE id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        return true;
    }

    function getMenuInfo($id)
    {
        $menu_id = array(':id' => $id);

        $query =<<<EOQ
            SELECT
                name,
                addy1, addy2,
                city, state, zip,
                numbers,
                hours
            FROM tblInfo_us
            WHERE menu_id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        $rst = $prepare->fetchAll(PDO::FETCH_ASSOC);
        $info = array_shift($rst);

        $info['site_addy'] = 'www.not_done.com';

        if (!empty($info['site_addy']))
        {
            if (stristr($info['site_addy'], 'http://'))
            {
                // if PHP >= 5.3 then do
                // $menu['site'] = stristr($info['site_addy'], 'http://', true)

                $info['site_addy'] = substr($info['site_addy'], 7);
            }

            if (stristr($info['site_addy'], 'https://'))
            {
                // if PHP >= 5.3 then do
                // $menu['site'] = stristr($info['site_addy'], 'https://', true)

                $info['site_addy'] = substr($info['site_addy'], 8);
            }
        }

        return $info;
    }

    function updateMenuInfo($info)
    {
        $query =<<<EOQ
            INSERT INTO tblInfo_us
            (
                menu_id,
                name,
                addy1, addy2,
                city, state, zip,
                numbers,
                hours
            )
            VALUES
            (
                :id,
                :name,
                :addy1, :addy2,
                :city, :state, :zip,
                :numbers,
                :hours
            )
            ON DUPLICATE KEY UPDATE
                name = :u_name,
                addy1 = :u_addy1,
                addy2 = :u_addy2,
                city = :u_city,
                state = :u_state,
                zip = :u_zip,
                numbers = :u_numbers,
                hours = :u_hours
EOQ;

        $prepare = $this->prepareAndExecute($query, $info, __FILE__, __LINE__);
        if (!$prepare) return false;

        return true;
    }

    function getMenuImgs($id)
    {
        $menu_id = array(':id' => $id);

        $query =<<<EOQ
            SELECT
                file_img
            FROM tblMenuImages
            WHERE menu_id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        $menu_imgs = $prepare->fetchAll(PDO::FETCH_ASSOC);

        $imgs = array();
        foreach ($menu_imgs as $img)
            $imgs[] = $img['file_img'];

        return $imgs;
    }

    function updateMenuSectionAndMetadata($id, $datas)
    {
        $this->beginTransaction();

        if (!$this->truncateMetadata($id))
            return false;

        if (!$this->truncateSection($id))
            return false;

        if (!$this->insertSection($id, $datas))
            return false;

        if (!$this->insertMetadata($id, $datas))
            return false;

        $this->commit();
        return true;
    }

    function truncateSection($id)
    {
        $query =<<<EOQ
            DELETE FROM tblMenuSection WHERE menu_id = :id;
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':id'=>$id), __FILE__, __LINE__);
        if (!$prepare) return false;

        return true;
    }

    function truncateMetadata($id)
    {
        $query =<<<EOQ
            DELETE FROM tblMenuMetadata WHERE menu_id = :id;
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':id'=>$id), __FILE__, __LINE__);
        if (!$prepare) return false;

        return true;
    }

    function insertSection($id, $datas)
    {
        $query =<<<EOQ
            INSERT INTO tblMenuSection
            (
                menu_id,
                section_id,
                name,
                notes
            )
            VALUES
            (
                :menu_id,
                :section_id,
                :name,
                :notes
            )
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        foreach ($datas as $idx_section => $section)
        {
            $rsts[] = $prepare->bindValue(':menu_id', $id);
            $rsts[] = $prepare->bindValue(':section_id', $idx_section);
            $rsts[] = $prepare->bindValue(':name', $section['name']);
            $rsts[] = $prepare->bindValue(':notes', $section['notes']);
            $rsts[] = $prepare->execute();

            // results check..
            foreach ($rsts as $rst)
            {
                if (!$rst)
                {
                    $this->log_dberr($rst, __FILE__, __LINE__);
                    return false;
                }
            }

            unset($rsts);
        }

        return true;
    }

    function insertMetadata($id, $datas)
    {
        $query =<<<EOQ
            INSERT INTO tblMenuMetadata
            (
                menu_id,
                section_id,
                ordinal_id,
                label,
                price,
                notes
            )
            VALUES
            (
                :menu_id,
                :section_id,
                :ordinal_id,
                :label,
                :price,
                :notes
            )
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        foreach ($datas as $idx_section => $section)
        {
            $rsts[] = $prepare->bindValue(':menu_id', $id);
            $rsts[] = $prepare->bindValue(':section_id', $idx_section);

            foreach ($section['items'] as $idx_mdt => $mdt)
            {
                $rsts[] = $prepare->bindValue(':ordinal_id', $idx_mdt);
                $rsts[] = $prepare->bindValue(':label', $mdt['item']);
                $rsts[] = $prepare->bindValue(':price', $mdt['price']);
                $rsts[] = $prepare->bindValue(':notes', $mdt['notes']);

                $rsts[] = $prepare->execute();
            }

            // results check..
            foreach ($rsts as $rst)
            {
                if (!$rst)
                {
                    $this->log_dberr($rst, __FILE__, __LINE__);
                    return false;
                }
            }

            unset($rsts);
        }

        return true;
    }

    function getSection($menu_id)
    {
        $query =<<<EOQ
            SELECT *
            FROM tblMenuSection
            WHERE menu_id = :menu_id
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':menu_id'=>$menu_id), __FILE__, __LINE__);
        if (!$prepare)
            return false;

        $rst = $prepare->fetchAll(PDO::FETCH_ASSOC);
        $sections = array();

        foreach ($rst as $row)
        {
            $section_id = $row['section_id'];
            $name = $row['name'];
            $notes = $row['notes'];

            $sections[$section_id] = array(
                'name' => $name,
                'notes' => $notes,
            );
        }

        return $sections;
    }

    function getMetadata($menu_id, $sections)
    {
        $query =<<<EOQ
            SELECT *
            FROM tblMenuMetadata
            WHERE menu_id = :menu_id
            AND section_id = :section_id
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare)
            return false;

        $mdts = array();
        foreach ($sections as $section_id => $section_info)
        {
            $rsts[] = $prepare->bindValue(':menu_id', $menu_id);
            $rsts[] = $prepare->bindValue(':section_id', $section_id);

            $rsts[] = $prepare->execute();

            // results check..
            foreach ($rsts as $rst)
            {
                if (!$rst)
                {
                    $this->log_dberr($rst, __FILE__, __LINE__);
                    return false;
                }
            }

            unset($rsts);

            // copy the section info
            $mdts[$section_id] = $section_info;

            $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row)
            {
                $ordinal_id = $row['ordinal_id'];
                $label = $row['label'];
                $price = $row['price'];
                $notes = $row['notes'];

                // create the menus metadata
                $mdts[$section_id]['items'][] = array(
                    'item' => $label,
                    'price' => $price,
                    'notes' => $notes,
                );
            }
        }

        return $mdts;
    }
}
