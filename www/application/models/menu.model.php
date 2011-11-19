<?php

class MenuModel
    extends Model
{
    function saveNewMenu($urls)
    {
        /*
           select *
           from tblPendingMenu menu
           left join tblPendingMenuImages menu_img on menu.id = menu_img.pendingmenu_id
         */

        $this->beginTransaction();

        $query =<<<EOQ
            INSERT INTO tblPendingMenu (
                site_addy1,
                site_addy2,
                site_addy3,
                site_addy4,
                site_addy5
            )
            VALUES (
                :url1,
                :url2,
                :url3,
                :url4,
                :url5
            )
EOQ;

        $params = array(
            ':url1' => $urls[0],
            ':url2' => $urls[1],
            ':url3' => $urls[2],
            ':url4' => $urls[3],
            ':url5' => $urls[4],
        );

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
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
        $files = Util::handle_upload_files();

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
                site_addy1,
                site_addy2,
                site_addy3,
                site_addy4,
                site_addy5
            FROM tblPendingMenu
            WHERE id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        $info = $prepare->fetch(PDO::FETCH_ASSOC);
        if (empty($info)) return false;
        $prepare->closeCursor();
        unset($prepare);

        $menu['sites'][] = $info['site_addy1'];
        $menu['sites'][] = $info['site_addy2'];
        $menu['sites'][] = $info['site_addy3'];
        $menu['sites'][] = $info['site_addy4'];
        $menu['sites'][] = $info['site_addy5'];

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
            INSERT INTO tblMenu(
                mode_id
            )
            VALUES (
                (SELECT id FROM vMenuStatus WHERE menu_status='new')
            )
EOQ;

        $rst = $this->query($query);
        if (!$rst)
        {
            $this->log_dberr($rst, __FILE__, __LINE__);
            return false;
        }

        $menu_id = $this->lastInsertId();

        $query =<<<EOQ
            INSERT IGNORE INTO tblMenuLinks(
                menu_id,
                url
            ) VALUES (
                :menu_id,
                :url
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

        foreach ($pending_menu['sites'] as $url)
        {
            if (empty($url))
                continue;

            $rsts[] = $prepare->bindValue(':url', $url);
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
            $rsts[] = $prepare->bindValue(':file_img', $file_img);
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

        $this->commit();

        // move pending images to menu directory
        $menu_img_path = OS_MENU_PATH . DS . $menu_id;
        if (mkdir($menu_img_path) == false)
        {
            Util::logit("Failed to create menu directory: {$menu_img_path}", __FILE__, __LINE__);
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

    function createMenu()
    {
        $query =<<<EOQ
            INSERT INTO tblMenu(
                mode_id
            )
            VALUES (
                (SELECT id FROM vMenuStatus WHERE menu_status='new')
            )
EOQ;

        $rst = $this->query($query);
        if (!$rst)
        {
            $this->log_dberr($rst, __FILE__, __LINE__);
            return false;
        }

        $menu_id = $this->lastInsertId();

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

    function updateMenu($id, $status)
    {
        $query =<<<EOQ
            UPDATE tblMenu
            SET mode_id = (SELECT id FROM vMenuStatus WHERE menu_status = :status)
            WHERE id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':id'=>$id, ':status'=>$status), __FILE__, __LINE__);
        if (!$prepare) return false;

        return true;
    }

    function updateMenuInfo($info)
    {
        $query =<<<EOQ
            INSERT INTO tblMenuInfo_us
            (
                menu_id,
                name,
                notes,
                address,
                latitude, longitude,
                numbers,
                hours
            )
            VALUES
            (
                :id,
                :name,
                :notes,
                :address,
                :latitude, :longitude,
                :numbers,
                :hours
            )
            ON DUPLICATE KEY UPDATE
                name = :u_name,
                notes = :u_notes,
                address = :u_address,
                latitude = :u_latitude,
                longitude = :u_longitude,
                numbers = :u_numbers,
                hours = :u_hours
EOQ;

        $prepare = $this->prepareAndExecute($query, $info, __FILE__, __LINE__);
        if (!$prepare) return false;

        return true;
    }

    function getMenuInfo($id)
    {
        $menu_id = array(':id' => $id);

        $query =<<<EOQ
            SELECT
                ms.menu_status AS status,
                NOT ISNULL(m.id) AS selected
            FROM vMenuStatus ms
            LEFT JOIN tblMenu m ON m.id = :id AND ms.id = m.mode_id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        $status = $prepare->fetchAll(PDO::FETCH_ASSOC);
        $prepare->closeCursor();
        unset($prepare);

        $query =<<<EOQ
            SELECT
                name,
                notes,
                address,
                latitude, longitude,
                numbers,
                hours
            FROM tblMenuInfo_us
            WHERE menu_id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        $rst = $prepare->fetchAll(PDO::FETCH_ASSOC);
        $info = array_shift($rst);

        if (empty($info))
        {
            $info = array(
                'name'=>'',
                'notes'=>'',
                'address'=>'',
                'latitude'=>'',
                'longitude'=>'',
                'numbers'=>'',
                'hours'=>''
            );
        }

        $info['status'] = $status;
        return $info;
    }

    function getMenuLinks($id)
    {
        $menu_id = array(':id' => $id);

        $query =<<<EOQ
            SELECT
                label,
                url
            FROM tblMenuLinks
            WHERE menu_id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        $menu_links = $prepare->fetchAll(PDO::FETCH_ASSOC);

        $links = array();
        foreach ($menu_links as $link)
            $links[] = array(
                'label'=>$link['label'],
                'url'=>$link['url'],
            );

        return $links;
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

    function updateMenuSectionAndMetadata($id, &$datas)
    {
        $this->beginTransaction();

        $section_ids = array();
        if (!$this->updateSection($id, $datas, $section_ids))
            return false;

        $metadata_ids = array();
        if (!$this->updateMetadata($id, $datas, $metadata_ids))
            return false;

        $this->commit();
        return true;
    }

    function updateSection($id, &$datas, &$section_ids)
    {
        $query =<<<EOQ
            UPDATE tblMenuSection_new
            SET
                ordinal = :ordinal,
                name = :name,
                notes = :notes
            WHERE section_id = :section_id
            AND menu_id = :menu_id
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $query =<<<EOQ
            UPDATE tblMenuSection_new
            SET
                ordinal = -1
            WHERE section_id = :section_id
            AND menu_id = :menu_id
EOQ;

        $prepareHack = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $insertSections = array();
        foreach ($datas as &$section)
        {
            $section_id = $section['section_id'];
            $ordinal = $section['ordinal'];
            $name = $section['name'];
            $notes = $section['notes'];

            // update the row to be something way different.
            // NOTE: it's because mysql < 5.3 does not support
            //       PDO::MYSQL_ATTR_FOUND_ROWS
            $rsts[] = $prepareHack->bindValue(':menu_id', $id);
            $rsts[] = $prepareHack->bindValue(':section_id', $section_id);
            $rsts[] = $prepareHack->execute();

            // now for the real update!
            $rsts[] = $prepare->bindValue(':menu_id', $id);
            $rsts[] = $prepare->bindValue(':section_id', $section_id);
            $rsts[] = $prepare->bindValue(':ordinal', $ordinal);
            $rsts[] = $prepare->bindValue(':name', $name);
            $rsts[] = $prepare->bindValue(':notes', $notes);
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

            $rowCnt = $prepare->rowCount();
            if ($rowCnt === 0)
                $insertSections[] = &$section;
            else
                $section_ids[] = $section_id;
        }

        if (!empty($insertSections))
            return $this->insertSection($id, $insertSections, $section_ids);

        return true;
    }

    function insertSection($id, &$datas, &$section_ids)
    {
        $query =<<<EOQ
            INSERT INTO tblMenuSection_new
            (
                menu_id,
                ordinal,
                name,
                notes
            )
            VALUES
            (
                :menu_id,
                :ordinal,
                :name,
                :notes
            )
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        foreach ($datas as &$section)
        {
            $ordinal = $section['ordinal'];
            $name = $section['name'];
            $notes = $section['notes'];

            $rsts[] = $prepare->bindValue(':menu_id', $id);
            $rsts[] = $prepare->bindValue(':ordinal', $ordinal);
            $rsts[] = $prepare->bindValue(':name', $name);
            $rsts[] = $prepare->bindValue(':notes', $notes);
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

            // grab the new section_id and set it back to the data
            $section_id = $this->lastInsertId();
            $section['section_id'] = $section_id;
            $section_ids[] = $section_id;
        }

        return true;
    }

    function updateMetadata($id, &$datas, &$metadata_ids)
    {
        $query =<<<EOQ
            UPDATE tblMenuMetadata_new
            SET
                ordinal = :ordinal,
                label = :label,
                price = :price,
                notes = :notes
            WHERE metadata_id = :metadata_id
            AND menu_id = :menu_id
            AND section_id = :section_id
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $query =<<<EOQ
            UPDATE tblMenuMetadata_new
            SET
                ordinal = -1
            WHERE metadata_id = :metadata_id
            AND menu_id = :menu_id
            AND section_id = :section_id
EOQ;

        $prepareHack = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $insertMetadatas = array();
        foreach ($datas as &$section)
        {
            $section_id = $section['section_id'];

            foreach ($section['items'] as &$metadata)
            {
                $metadata_id = $metadata['metadata_id'];
                $ordinal = $metadata['ordinal'];
                $label = $metadata['label'];
                $price = $metadata['price'];
                $notes = $metadata['notes'];

                // update the row to be something way different.
                // NOTE: it's because mysql < 5.3 does not support
                //       PDO::MYSQL_ATTR_FOUND_ROWS
                $rsts[] = $prepareHack->bindValue(':metadata_id', $metadata_id);
                $rsts[] = $prepareHack->bindValue(':menu_id', $id);
                $rsts[] = $prepareHack->bindValue(':section_id', $section_id);
                $rsts[] = $prepareHack->execute();

                // now for the real update!
                $rsts[] = $prepare->bindValue(':metadata_id', $metadata_id);
                $rsts[] = $prepare->bindValue(':menu_id', $id);
                $rsts[] = $prepare->bindValue(':section_id', $section_id);
                $rsts[] = $prepare->bindValue(':ordinal', $ordinal);
                $rsts[] = $prepare->bindValue(':label', $label);
                $rsts[] = $prepare->bindValue(':price', $price);
                $rsts[] = $prepare->bindValue(':notes', $notes);
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

                $rowCnt = $prepare->rowCount();
                if ($rowCnt === 0)
                    $insertMetadatas[$section_id][$ordinal] = &$metadata;
                else
                    $metadata_ids[] = $metadata_id;
            }
        }

        if (!empty($insertMetadatas))
            return $this->insertMetadata($id, $insertMetadatas, $metadata_ids);

        return true;
    }

    function insertMetadata($id, &$datas, &$metadata_ids)
    {
        /*
            $datas = array(
                [section_id 1] = array(
                    [metadata ordinal 0] = array(
                        label, price, notes
                    )
                    [metadata ordinal 1] = array(
                        label, price, notes
                    )
                    [metadata ordinal 2] = array(
                        label, price, notes
                    )
                )
                [section_id 2] = array(
                    [metadata ordinal 0] = array(
                        label, price, notes
                    )
                    [metadata ordinal 1] = array(
                        label, price, notes
                    )
                    [metadata ordinal 2] = array(
                        label, price, notes
                    )
                )
            )
        */
        $query =<<<EOQ
            INSERT INTO tblMenuMetadata_new
            (
                menu_id,
                section_id,
                ordinal,
                label,
                price,
                notes
            )
            VALUES
            (
                :menu_id,
                :section_id,
                :ordinal,
                :label,
                :price,
                :notes
            )
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        foreach ($datas as $section_id => &$items)
        {
            foreach ($items as &$metadata)
            {
                $ordinal = $metadata['ordinal'];
                $label = $metadata['label'];
                $price = $metadata['price'];
                $notes = $metadata['notes'];

                $rsts[] = $prepare->bindValue(':menu_id', $id);
                $rsts[] = $prepare->bindValue(':section_id', $section_id);
                $rsts[] = $prepare->bindValue(':ordinal', $ordinal);
                $rsts[] = $prepare->bindValue(':label', $label);
                $rsts[] = $prepare->bindValue(':price', $price);
                $rsts[] = $prepare->bindValue(':notes', $notes);
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

                // grab the new metadata_id and set it back to the data
                $metadata_id = $this->lastInsertId();
                $metadata['metadata_id'] = $metadata_id;
                $metadata_ids[] = $metadata_id;
            }

        }

        return true;
    }

    function getSection($menu_id)
    {
        $query =<<<EOQ
            SELECT *
            FROM tblMenuSection_new
            WHERE menu_id = :menu_id
            ORDER BY ordinal
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

            $sections[] = array(
                'section_id' => $section_id,
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
            FROM tblMenuMetadata_new
            WHERE menu_id = :menu_id
            AND section_id = :section_id
            ORDER BY ordinal
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare)
            return false;

        $mdts = array();
        foreach ($sections as $section_info)
        {
            $section_id = $section_info['section_id'];

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
                $metadata_id = $row['metadata_id'];
                $label = $row['label'];
                $price = $row['price'];
                $notes = $row['notes'];

                // create the menus metadata
                $mdts[$section_id]['items'][] = array(
                    'metadata_id' => $metadata_id,
                    'label' => $label,
                    'price' => $price,
                    'notes' => $notes,
                );
            }
        }

        return $mdts;
    }

    function updateMenuLinks($menu_id, $links)
    {
        $this->beginTransaction();

        $query =<<<EOQ
            UPDATE tblMenuLinks
            SET keep = 0
            WHERE menu_id = :menu_id
EOQ;

        $rst = $this->prepareAndExecute($query, array('menu_id'=>$menu_id), __FILE__, __LINE__);
        if (!$rst)
            return false;
        $rst->closeCursor();
        unset($rst);

        $query =<<<EOQ
            INSERT INTO tblMenuLinks(
                menu_id,
                url,
                label,
                keep
            )
            VALUES (
                :menu_id,
                :url,
                :label,
                1
            )
            ON DUPLICATE KEY UPDATE
                label = :u_label,
                keep = 1
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare)
            return false;

        $rst = $prepare->bindValue(':menu_id', $menu_id);
        if (!$rst)
        {
            $this->log_dberr($rst, __FILE__, __LINE__);
            return false;
        }

        foreach ($links as $link)
        {
            $rsts[] = $prepare->bindValue(':url', $link['url']);
            $rsts[] = $prepare->bindValue(':label', $link['label']);
            $rsts[] = $prepare->bindValue(':u_label', $link['label']);
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

        $prepare->closeCursor();
        unset($prepare);

        $query =<<<EOQ
            DELETE FROM tblMenuLinks
            WHERE menu_id = :menu_id
            AND keep = 0
EOQ;

        $rst = $this->prepareAndExecute($query, array('menu_id'=>$menu_id), __FILE__, __LINE__);
        if (!$rst)
            return false;

        $this->commit();
        return true;
    }
}
