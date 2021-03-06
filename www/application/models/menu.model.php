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

        $user_id = Util::getUserId();
        if (!$user_id) $user_id = 0;

        $this->beginTransaction();

        $query =<<<EOQ
            INSERT INTO tblPendingMenu (
                user_id,
                site_addy1,
                site_addy2,
                site_addy3,
                site_addy4,
                site_addy5
            )
            VALUES (
                :user_id,
                :url1,
                :url2,
                :url3,
                :url4,
                :url5
            )
EOQ;

        $params = array(
            ':user_id' => $user_id,
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
                user_id,
                file_img,
                width,
                height
            )
            VALUES (
                :new_id,
                :user_id,
                :file_img,
                :width,
                :height
            )
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $new_menu_img = array(':new_id' => $new_id, ':user_id' => $user_id, ':file_img' => '', ':width' => 0, ':height' => 0);
        $new_path = OS_UPLOAD_PATH . DS . $new_id;
        $files = Util::handle_upload_files($new_path);

        foreach ($files as $img)
        {
            $new_menu_img[':file_img'] = $img['filename'];
            $new_menu_img[':width'] = $img['width'];
            $new_menu_img[':height'] = $img['height'];

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

    function getPendingMenus($user_id=null)
    {
        $params = array();
        $aes_key = SQL_AES_KEY;

        if (empty($user_id))
        {
            // get all pending menus
            $query =<<<EOQ
                SELECT
                    menu.*,
                    COUNT(imgs.pendingmenu_id) as cnt_imgs,
                    AES_DECRYPT(user.username, '{$aes_key}_username') AS username
                FROM tblPendingMenu menu
                LEFT JOIN tblPendingMenuImages imgs ON menu.id = imgs.pendingmenu_id
                LEFT JOIN tblUsers user ON menu.user_id = user.id
                GROUP BY menu.id
EOQ;
        }
        else
        {
            // get users pending menu
            $query =<<<EOQ
                SELECT
                    menu.*,
                    COUNT(imgs.pendingmenu_id) as cnt_imgs,
                    AES_DECRYPT(user.username, '{$aes_key}_username') AS username
                FROM tblPendingMenu menu
                LEFT JOIN tblPendingMenuImages imgs ON menu.id = imgs.pendingmenu_id
                LEFT JOIN tblUsers user ON menu.user_id = user.id
                WHERE menu.user_id = :user_id
                GROUP BY menu.id
EOQ;

            $params['user_id'] = $user_id;
        }

        $rst = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
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
                user_id,
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

        $menu['user_id'] = $info['user_id'];

        $menu['sites'][] = $info['site_addy1'];
        $menu['sites'][] = $info['site_addy2'];
        $menu['sites'][] = $info['site_addy3'];
        $menu['sites'][] = $info['site_addy4'];
        $menu['sites'][] = $info['site_addy5'];

        $query =<<<EOQ
            SELECT
                user_id,
                file_img,
                width,
                height
            FROM tblPendingMenuImages
            WHERE pendingmenu_id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        $imgs = $prepare->fetchAll(PDO::FETCH_ASSOC);
        $menu['imgs'] = array();
        foreach ($imgs as $img)
            $menu['imgs'][] = array
            (
                'user_id' => $img['user_id'],
                'filename' => $img['file_img'],
                'width' => $img['width'],
                'height' => $img['height'],
            );

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
                mode_id,
                user_id
            )
            VALUES (
                (SELECT id FROM vMenuStatus WHERE menu_status='new'),
                :user_id
            )
EOQ;

        $rst = $this->prepareAndExecute($query, array(':user_id'=>$pending_menu['user_id']), __FILE__, __LINE__);
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
                user_id,
                file_img,
                width,
                height
            ) VALUES (
                :menu_id,
                :user_id,
                :file_img,
                :width,
                :height
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

        $current_user_id = Util::getUserId();

        foreach ($pending_menu['imgs'] as $file_img)
        {
            $user_id = $file_img['user_id'];
            if (!$user_id) $user_id = $current_user_id;

            $rsts[] = $prepare->bindValue(':user_id', $user_id);
            $rsts[] = $prepare->bindValue(':file_img', $file_img['filename']);
            $rsts[] = $prepare->bindValue(':width', $file_img['width']);
            $rsts[] = $prepare->bindValue(':height', $file_img['height']);
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
            $file_src = OS_UPLOAD_PATH . DS . $pending_id . DS . $file_img['filename'];
            $file_dst = $menu_img_path . DS . $file_img['filename'];

            $rename_ok = @rename($file_src, $file_dst);
            if (!$rename_ok)
                Util::logit("Failed to move '{$file_src}' to '{$file_dst}'", __FILE__, __LINE__);
        }

        // it's all good now... let's purge the pending info...
        $this->purgePendingMenu($pending_id);

        return $menu_id;
    }

    function createMenu()
    {
        $query =<<<EOQ
            INSERT INTO tblMenu SET
                mod_ts = CURRENT_TIMESTAMP,
                mode_id = (SELECT id FROM vMenuStatus WHERE menu_status='new'),
                user_id = :user_id
EOQ;

        $rst = $this->prepareAndExecute($query, array(':user_id'=>Util::getUserId()), __FILE__, __LINE__);
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
            UPDATE tblMenu SET
                mode_id = (SELECT id FROM vMenuStatus WHERE menu_status = :status),
                mod_ts = CURRENT_TIMESTAMP
            WHERE id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, array(':id'=>$id, ':status'=>$status), __FILE__, __LINE__);
        if (!$prepare) return false;

        return true;
    }

    function updateMenuInfo($id, $info)
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

        $params = array();
        $params[':id'] = $id;

        $params[':name']        = $info['name'];
        $params[':notes']       = $info['notes'];
        $params[':address']     = $info['address'];
        $params[':latitude']    = $info['latitude'];
        $params[':longitude']   = $info['longitude'];
        $params[':numbers']     = $info['numbers'];
        $params[':hours']       = $info['hours'];

        $params[':u_name']      = $info['name'];
        $params[':u_notes']     = $info['notes'];
        $params[':u_address']   = $info['address'];
        $params[':u_latitude']  = $info['latitude'];
        $params[':u_longitude'] = $info['longitude'];
        $params[':u_numbers']   = $info['numbers'];
        $params[':u_hours']     = $info['hours'];

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
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
            SELECT COUNT(*) AS total_items
            FROM tblMenuMetadata mm
            INNER JOIN tblMenuSection ms ON mm.section_id = ms.section_id
            WHERE mm.menu_id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        $total_items = (int) $prepare->fetchColumn();
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
            FROM tblMenu m
            LEFT JOIN tblMenuInfo_us i ON m.id = i.menu_id
            WHERE m.id = :id
EOQ;

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        $info = $prepare->fetch(PDO::FETCH_ASSOC);

        if (empty($info))
            return false;

        $info['status'] = $status;
        $info['total_items'] = $total_items;
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
        /*
            NOTE: This might have "zombie" items because during
            taggits, if the section_id or metadata_id is not valid,
            it will insert a "0", so during this query, it will
            return it in this result
        */

        $query =<<<EOQ
            SELECT
                id,
                file_img AS filename,
                width,
                height
            FROM tblMenuImages
            WHERE menu_id = :id
EOQ;

        $menu_id = array(':id' => $id);

        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;

        $menu_imgs = $prepare->fetchAll(PDO::FETCH_ASSOC);

        return $menu_imgs;
    }

    function updateMenuSectionAndMetadata($id, $info, &$datas)
    {
        $this->beginTransaction();

        $section_ids = array();
        if (!$this->updateSection($id, $datas, $section_ids))
            return false;

        if (!$this->removeUnusedSection($id, $section_ids))
            return false;

        if (!$this->updateMetadata($id, $datas))
            return false;

        if (!$this->updateSearch($id, $info, $datas))
            return false;

        $this->commit();
        return true;
    }

    function updateSection($id, &$datas, &$section_ids)
    {
        $query =<<<EOQ
            UPDATE tblMenuSection
            SET
                ordinal = :ordinal,
                name = :name,
                notes = :notes
            WHERE section_id = :section_id
            AND menu_id = :menu_id
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $insertSections = array();
        foreach ($datas as &$section)
        {
            $section_id = $section['section_id'];
            $ordinal = $section['ordinal'];
            $name = $section['name'];
            $notes = $section['notes'];

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
            INSERT INTO tblMenuSection
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

//    function updateMetadata_db($id, &$datas)
//    {
//        $query =<<<EOQ
//            INSERT INTO tblMenuMetadataValues
//            SET
//                metadata_id = :metadata_id,
//                `key` = :key,
//                keyindex = :keyindex,
//                `value` = :value
//            ON DUPLICATE KEY UPDATE
//                `value` = :u_value
//EOQ;
//
//        $prepareInsertMdtValue = $this->prepare_log($query, __FILE__, __LINE__);
//        if (!$prepareInsertMdtValue) return false;
//
//        foreach ($datas as &$section)
//        {
//            $section_id = $section['section_id'];
//
//            foreach ($section['items'] as &$metadata)
//            {
//                $metadata_id = @$metadata['metadata_id'];
//                $ordinal = @$metadata['ordinal'];
//
//                foreach ($metadata as $key => $value)
//                {
//                    if ($key === 'metadata_id')
//                    {
//                        // skip these keys cause it's not needed
//                        continue;
//                    }
//
//                    // normalize the non-string types
//                    if (is_bool($value))
//                    {
//                        $value = $value ? 'true' : 'false';
//                    }
//                    else if (is_numeric($value))
//                    {
//                        $value = "$value";
//                    }
//
//                    $rsts[] = $prepareInsertMdtValue->bindValue(':metadata_id', $metadata_id);
//                    $rsts[] = $prepareInsertMdtValue->bindValue(':key', $key);
//
//                    if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
//                    unset($rsts);
//
//                    $array_values = Util::str_split_unicode($value, 255);
//
//                    if (empty($array_values))
//                    {
//                        // add an empty item to the array
//                        $array_values[] = '';
//                    }
//
//                    foreach ($array_values as $key_index => $value_chunk)
//                    {
//                        echo "mid($metadata_id) key($key) index($key_index) value($value)<br/>";
//                        $rsts[] = $prepareInsertMdtValue->bindValue(':keyindex', $key_index);
//                        $rsts[] = $prepareInsertMdtValue->bindValue(':value', $value_chunk);
//                        $rsts[] = $prepareInsertMdtValue->bindValue(':u_value', $value_chunk);
//                        $rsts[] = $prepareInsertMdtValue->execute();
//
//                        if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
//                        unset($rsts);
//                    } // foreach ($array_values as $key_index => $value_chunk)
//                } // foreach ($metadata as $key => $value)
//            } // foreach ($section['items'] as &$metadata)
//        } // foreach ($datas as &$section)
//
//        return true;
//    }

    function updateMetadata($id, &$datas)
    {
        $query =<<<EOQ
            DELETE FROM tblMenuMetadata
            WHERE menu_id = :menu_id
            AND section_id = :section_id
EOQ;

        $prepareRemoveAllSection = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepareRemoveAllSection) return false;

        $query =<<<EOQ
            INSERT INTO tblMenuMetadata
            SET
                menu_id = :menu_id,
                section_id = :section_id,
                ordinal = -1
EOQ;

        $prepareInsertMdtNewId = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepareInsertMdtNewId) return false;

        $query =<<<EOQ
            INSERT INTO tblMenuMetadata
            SET
                menu_id = :menu_id,
                section_id = :section_id,
                metadata_id = :metadata_id,
                ordinal = -1
EOQ;

        $prepareInsertMdt = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepareInsertMdt) return false;

        $query =<<<EOQ
            UPDATE tblMenuMetadataValues
            SET `value` = ''
            WHERE metadata_id = :metadata_id
            AND `key` = :key
EOQ;

        $prepareClearMdtValue = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepareClearMdtValue) return false;

        $query =<<<EOQ
            INSERT INTO tblMenuMetadataValues
            SET
                metadata_id = :metadata_id,
                `key` = :key,
                keyindex = :keyindex,
                `value` = :value
            ON DUPLICATE KEY UPDATE
                `value` = :u_value
EOQ;

        $prepareInsertMdtValue = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepareInsertMdtValue) return false;

        $query =<<<EOQ
            UPDATE tblMenuMetadata tmm
            INNER JOIN tblMenuMetadataValues tmmv ON tmm.metadata_id = tmmv.metadata_id AND tmmv.key = 'ordinal'
            SET tmm.ordinal = tmmv.value
            WHERE tmm.menu_id = :menu_id
EOQ;

        $prepareUpdateMdtOrdinals = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepareUpdateMdtOrdinals) return false;

        foreach ($datas as &$section)
        {
            $section_id = $section['section_id'];

            $rsts[] = $prepareRemoveAllSection->bindValue(':menu_id', $id);
            $rsts[] = $prepareRemoveAllSection->bindValue(':section_id', $section_id);
            $rsts[] = $prepareRemoveAllSection->execute();

            if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
            unset($rsts);

            foreach ($section['items'] as &$metadata)
            {
                $metadata_id = @$metadata['metadata_id'];
                $ordinal = @$metadata['ordinal'];

                if (empty($metadata_id) || ($metadata_id < 0))
                {
                    $rsts[] = $prepareInsertMdtNewId->bindValue(':menu_id', $id);
                    $rsts[] = $prepareInsertMdtNewId->bindValue(':section_id', $section_id);
                    $rsts[] = $prepareInsertMdtNewId->execute();
                    $metadata_id = $this->lastInsertId();

                    // set the metadata_id back to the original structure
                    $metadata['metadata_id'] = $metadata_id;
                }
                else
                {
                    $rsts[] = $prepareInsertMdt->bindValue(':menu_id', $id);
                    $rsts[] = $prepareInsertMdt->bindValue(':metadata_id', $metadata_id);
                    $rsts[] = $prepareInsertMdt->bindValue(':section_id', $section_id);
                    $rsts[] = $prepareInsertMdt->execute();
                }

                if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
                unset($rsts);

                foreach ($metadata as $key => $value)
                {
                    if ($key === 'metadata_id')
                    {
                        // skip these keys cause it's not needed
                        continue;
                    }

                    $rsts[] = $prepareClearMdtValue->bindValue(':metadata_id', $metadata_id);
                    $rsts[] = $prepareClearMdtValue->bindValue(':key', $key);
                    $rsts[] = $prepareClearMdtValue->execute();

                    if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
                    unset($rsts);

                    // normalize the non-string types
                    if (is_bool($value))
                    {
                        $value = $value ? 'true' : 'false';
                    }
                    else if (is_numeric($value))
                    {
                        $value = "$value";
                    }

                    $rsts[] = $prepareInsertMdtValue->bindValue(':metadata_id', $metadata_id);
                    $rsts[] = $prepareInsertMdtValue->bindValue(':key', $key);

                    if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
                    unset($rsts);

                    $array_values = Util::str_split_unicode($value, 255);

                    if (empty($array_values))
                    {
                        // add an empty item to the array
                        $array_values[] = '';
                    }

                    foreach ($array_values as $key_index => $value_chunk)
                    {
                        $rsts[] = $prepareInsertMdtValue->bindValue(':keyindex', $key_index);
                        $rsts[] = $prepareInsertMdtValue->bindValue(':value', $value_chunk);
                        $rsts[] = $prepareInsertMdtValue->bindValue(':u_value', $value_chunk);
                        $rsts[] = $prepareInsertMdtValue->execute();

                        if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
                        unset($rsts);
                    } // foreach ($array_values as $key_index => $value_chunk)
                } // foreach ($metadata as $key => $value)
            } // foreach ($section['items'] as &$metadata)
        } // foreach ($datas as &$section)

        // finally... udpate the ordinals
        $prepareUpdateMdtOrdinals->bindValue(':menu_id', $id);
        $rsts[] = $prepareUpdateMdtOrdinals->execute();

        if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
        unset($rsts);

        return true;
    }

    function removeUnusedSection($id, $section_ids)
    {
        if (empty($section_ids))
            return true;

        $query_in = implode(',', array_fill(0, count($section_ids), '?'));
        $query =<<<EOQ
            DELETE FROM tblMenuSection
            WHERE menu_id = ?
            AND section_id NOT IN ({$query_in})
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $rst = $prepare->bindValue(1, $id);
        foreach ($section_ids as $idx => $id)
        {
            // bindValue is 1-based
            $rst = $prepare->bindValue($idx+2, $id);
            if (!$rst) return false;
        }

        $rst = $prepare->execute();
        if (!$rst) return false;

        return true;
    }

    function updateSearch($id, $info, &$datas)
    {
        $query =<<<EOQ
            INSERT INTO tblMenuSearch
            SET
                menu_id = :menu_id,
                section_id = :section_id,
                metadata_id = :mdt_id,
                search_text = :search_text
            ON DUPLICATE KEY UPDATE
                search_text = :u_search_text
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        foreach ($datas as &$section)
        {
            $section_id = @$section['section_id'];

            foreach ($section['items'] as &$metadata)
            {
                $metadata_id = @$metadata['metadata_id'];

                foreach ($metadata as $key => $value)
                {
                    if ($key != 'label')
                        continue;

                    $search_text = sprintf('%s | %s | %s',
                            $info['name'],
                            $section['name'],
                            $value
                        );

                    $params = array(
                        ':menu_id' => $id,
                        ':section_id' => $section_id,
                        ':mdt_id' => $metadata_id,
                        ':search_text' => $search_text,
                        ':u_search_text' => $search_text,
                    );

                    $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
                    if (!$prepare) return false;
                }
            }
        }

        return true;
    }

    function getSection($menu_id)
    {
        $query =<<<EOQ
            SELECT *
            FROM tblMenuSection
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

//    function getMetadata_old($menu_id, $sections)
//    {
//        $query =<<<EOQ
//            SELECT *
//            FROM tblMenuMetadata
//            WHERE menu_id = :menu_id
//            AND section_id = :section_id
//            ORDER BY ordinal
//EOQ;
//
//        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
//        if (!$prepare)
//            return false;
//
//        $mdts = array();
//        foreach ($sections as $section_info)
//        {
//            $section_id = $section_info['section_id'];
//
//            $rsts[] = $prepare->bindValue(':menu_id', $menu_id);
//            $rsts[] = $prepare->bindValue(':section_id', $section_id);
//
//            $rsts[] = $prepare->execute();
//
//            // results check..
//            foreach ($rsts as $rst)
//            {
//                if (!$rst)
//                {
//                    $this->log_dberr($rst, __FILE__, __LINE__);
//                    return false;
//                }
//            }
//
//            unset($rsts);
//
//            $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);
//            foreach ($rows as $row)
//            {
//                $metadata_id = $row['metadata_id'];
//                $label = $row['label'];
//                $price = $row['price'];
//                $notes = $row['notes'];
//                $is_spicy = (bool)$row['is_spicy'];
//
//                // create the menus metadata
//                $section_info['items'][] = array(
//                    'metadata_id' => $metadata_id,
//                    'label' => $label,
//                    'price' => $price,
//                    'notes' => $notes,
//                    'is_spicy' => $is_spicy,
//                );
//            }
//
//            $mdts[] = $section_info;
//        }
//
//        return $mdts;
//    }

    function getMetadata($menu_id, $sections)
    {
        // use the GROUP_CONCAT to group the rows
        $query =<<<EOQ
            SELECT
                mdt.menu_id,
                mdt.section_id,
                mdt.metadata_id,
                mdt.ordinal,
                mdt_values.key,
                GROUP_CONCAT(mdt_values.value ORDER BY mdt_values.keyindex ASC SEPARATOR '') AS `value`
            FROM tblMenuMetadata mdt
            LEFT JOIN tblMenuMetadataValues mdt_values ON mdt.metadata_id = mdt_values.metadata_id
            WHERE mdt.menu_id = :menu_id
            AND mdt.section_id = :section_id
            AND mdt.ordinal >= 0
            GROUP BY mdt_values.metadata_id, mdt_values.key
            ORDER BY mdt.ordinal, mdt_values.metadata_id, mdt_values.key
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
            if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
            unset($rsts);

            $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row)
            {
                $metadata_id = $row['metadata_id'];
                $ordinal = $row['ordinal'];
                $key = $row['key'];
                $value = $row['value'];

                // set the right data-type to the value
                switch ($key)
                {
                    case 'is_hide':
                    case 'is_header':
                    case 'is_nopanel':
                    case 'is_spicy':
                    case 'is_veggie':
                        $value = $value === 'true' ? true : false;
                        break;
                }

                // create the array if it's not been created
                if (!isset($section_info['items'][$ordinal]))
                {
                    $section_info['items'][$ordinal] = array(
                        'metadata_id' => $metadata_id,
                        'ordinal' => $ordinal,
                        'label' => '',
                        'price' => '',
                        'notes' => '',
                        'is_hide' => false,
                        'is_header' => false,
                        'is_nopanel' => false,
                        'is_spicy' => false,
                        'is_veggie' => false,
                    );
                }

                $section_info['items'][$ordinal][$key] = $value;
            }

            $mdts[] = $section_info;
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

    function isValidMetadataId($menu_id, $section_id, $metadata_id)
    {
        $query =<<<EOQ
            SELECT COUNT(*) AS cnt
            FROM tblMenuMetadata
            WHERE metadata_id = :metadata_id
            AND menu_id = :menu_id
            AND section_id = :section_id
EOQ;

        $params = array(
            ':menu_id' => $menu_id,
            ':section_id' => $section_id,
            ':metadata_id' => $metadata_id,
        );

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare)
            return false;

        $cnt = (int) $prepare->fetchColumn();
        if ($cnt === 0)
        {
            Util::logit("Metadata id not found. mid({$menu_id}) sid({$section_id}) {$metadata_id}", __FILE__, __LINE__);
            return false;
        }

        return true;
    }

    function forkit($user_id, $menu_id, $section_id, $metadata_id)
    {
        $query =<<<EOQ
            INSERT IGNORE INTO tblMenuForkit
            (
                menu_id,
                section_id,
                metadata_id,
                user_id
            )
            VALUES
            (
                :menu_id,
                :section_id,
                :metadata_id,
                :user_id
            )
EOQ;

        $params = array(
            ':menu_id' => $menu_id,
            ':section_id' => $section_id,
            ':metadata_id' => $metadata_id,
            ':user_id' => $user_id,
        );

        $rst = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$rst)
            return false;

        return true;
    }

    function unforkit($user_id, $menu_id, $section_id, $metadata_id)
    {
        $query =<<<EOQ
            DELETE FROM tblMenuForkit
            WHERE menu_id = :menu_id
            AND section_id = :section_id
            AND metadata_id = :metadata_id
            AND user_id = :user_id
EOQ;

        $params = array(
            ':menu_id' => $menu_id,
            ':section_id' => $section_id,
            ':metadata_id' => $metadata_id,
            ':user_id' => $user_id,
        );

        $rst = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$rst)
            return false;

        return true;
    }

    function getForkit($menu_id, $user_id)
    {
        $query =<<<EOQ
            SELECT
                section_id,
                metadata_id,
                user_id,
                ts
            FROM tblMenuForkit
            WHERE menu_id = :menu_id
            ORDER BY section_id, metadata_id
EOQ;

        $params = array(
            ':menu_id' => $menu_id,
        );

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare)
            return false;

        $forkits = array();
        $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row)
        {
            $sid = $row['section_id'];
            $mid = $row['metadata_id'];
            $uid = $row['user_id'];
            $ts = $row['ts'];

            if (!isset($forkits[$sid]))
                $forkits[$sid] = array();

            if (!isset($forkits[$sid][$mid]))
                $forkits[$sid][$mid] = array('cnt'=>0, 'me'=>false);

            $forkits[$sid][$mid]['cnt'] += 1;

            if ($uid == $user_id)
                $forkits[$sid][$mid]['me'] = true;
        }

        return $forkits;
    }

    function getMenuItem($menu_id, $section_id, $metadata_id)
    {
        $query =<<<EOQ
            SELECT
                m.name AS place_name,
                s.name AS section_name,
                mdtv.key,
                GROUP_CONCAT(mdtv.value ORDER BY mdtv.keyindex ASC SEPARATOR '') as `value`
            FROM tblMenuMetadata mdt
            INNER JOIN tblMenuSection s ON mdt.section_id = s.section_id
            INNER JOIN tblMenuInfo_us m ON mdt.menu_id = m.menu_id
            INNER JOIN tblMenuMetadataValues mdtv ON mdt.metadata_id = mdtv.metadata_id
            WHERE mdt.metadata_id = :metadata_id
            AND mdt.menu_id = :menu_id
            AND mdt.section_id = :section_id
            GROUP BY mdtv.metadata_id, mdtv.key
EOQ;

        $params = array(
            ':menu_id' => $menu_id,
            ':section_id' => $section_id,
            ':metadata_id' => $metadata_id,
        );

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare)
            return false;

        $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);

        // convert key/value to properties
        $item = array(
            'place_name' => $rows[0]['place_name'],
            'section_name' => $rows[0]['section_name'],
        );

        foreach ($rows as $row)
        {
            $key = $row['key'];
            $value = $row['value'];
            $item[$key] = $value;
        }

        return $item;
    }

    function insertMenuImages($menu_id, $user_id, $imgs)
    {
        $this->beginTransaction();

        $query =<<<EOQ
            INSERT INTO tblMenuImages(
                menu_id,
                user_id,
                file_img,
                width,
                height
            ) VALUES (
                :menu_id,
                :user_id,
                :file_img,
                :width,
                :height
            )
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $rsts[] = $prepare->bindValue(':menu_id', $menu_id);
        $rsts[] = $prepare->bindValue(':user_id', $user_id);
        if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
        unset($rsts);

        // make imgs an array if it's not
        if (!is_array($imgs))
            $imgs = array($imgs);

        $out_imgs = array();
        foreach ($imgs as $img)
        {
            $rsts[] = $prepare->bindValue(':file_img', $img['filename']);
            $rsts[] = $prepare->bindValue(':width', $img['width']);
            $rsts[] = $prepare->bindValue(':height', $img['height']);
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

            $img_id = $this->lastInsertId();

            $out_imgs[] = array
            (
                'img_id' => $img_id,
                'filename' => $img['filename'],
                'width' => $img['width'],
                'height' => $img['height'],
            );
        }

        $this->commit();
        return $out_imgs;
    }

    function getMenuSectionImgs($id, $section_id)
    {
        /*
            NOTE: This might have "zombie" items because during
            taggits, if the metadata_id is not valid, it will insert
            a "0", so during this query, it will return it in this
            result
        */

        $query =<<<EOQ
            SELECT DISTINCT
                i.id,
                i.file_img AS filename,
                i.width,
                i.height
            FROM tblMenuImages i
            INNER JOIN tblTaggitsImage t ON i.menu_id = t.menu_id AND i.id = t.img_id
            WHERE i.menu_id = :menu_id
            AND t.section_id = :section_id
EOQ;

        $params = array(':menu_id' => $id, ':section_id' => $section_id);

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $menu_imgs = $prepare->fetchAll(PDO::FETCH_ASSOC);

        return $menu_imgs;
    }

    function getMenuItemImgs($id, $section_id, $item_id)
    {
       $query =<<<EOQ
            SELECT
                i.id,
                i.file_img AS filename,
                i.width,
                i.height
            FROM tblMenuImages i
            INNER JOIN tblTaggitsImage t ON i.menu_id = t.menu_id AND i.id = t.img_id
            WHERE i.menu_id = :menu_id
            AND t.section_id = :section_id
            AND t.metadata_id = :item_id
EOQ;

        $params = array(':menu_id' => $id, ':section_id' => $section_id, ':item_id' => $item_id);

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $menu_imgs = $prepare->fetchAll(PDO::FETCH_ASSOC);

        return $menu_imgs;
    }

    function getMenuTags($menu_id)
    {
        $query =<<<EOQ
            SELECT
                ms.section_id AS sid,
                ms.name AS section,
                mi.metadata_id AS mid,
                GROUP_CONCAT(mv.value ORDER BY mv.keyindex ASC SEPARATOR '') AS label
            FROM tblMenuSection ms
            INNER JOIN tblMenuMetadata mi ON (ms.menu_id = mi.menu_id) AND (ms.section_id = mi.section_id)
            LEFT JOIN tblMenuMetadataValues mv ON (mi.metadata_id = mv.metadata_id) AND (mv.key = 'label')
            WHERE ms.menu_id = :menu_id
            GROUP BY mv.metadata_id, mv.key
            ORDER BY ms.ordinal, mi.ordinal
EOQ;

        $params = array(':menu_id' => $menu_id);

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $tags = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $tags;
    }

    function getImgFromArray($filename, $imgs)
    {
        foreach ($imgs as $img)
        {
            $img_file = $img['filename'];
            if ($filename === $img_file)
            {
                return $img;
            }
        }

        return false;
    }

    function updateTaggitsImage($menu_id, $img_file, $add_taggits, $remove_taggits)
    {
        $this->beginTransaction();

        $bCommit = $this->removeTaggitsImage($menu_id, $img_file, $remove_taggits);
        if (!$bCommit) return false;

        $bCommit = $this->addTaggitsImage($menu_id, $img_file, $add_taggits);
        if (!$bCommit) return false;

        $this->commit();
        return true;
    }

    function addTaggitsImage($menu_id, $img_file, $taggits)
    {
        if (count($taggits) === 0) return true;

        $img_query =<<<EOQ
            SELECT id
            FROM tblMenuImages
            WHERE menu_id = :i_menu_id
            AND file_img = :i_file_img
EOQ;

        $query =<<<EOQ
            INSERT INTO tblTaggitsImage(
                menu_id,
                section_id,
                metadata_id,
                img_id
            ) VALUES (
                :menu_id,
                :section_id,
                :metadata_id,
                ({$img_query})
            )
            ON DUPLICATE KEY UPDATE ts = CURRENT_TIMESTAMP
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        /*
            WARNING: The 'ignore' will NOT cause errors if the values
            are wrong.  It will just put the default value.

            Might need to do a post process thing to remove invalid
            rows.
        */

        $rsts[] = $prepare->bindValue(':menu_id', $menu_id);
        $rsts[] = $prepare->bindValue(':i_menu_id', $menu_id);
        $rsts[] = $prepare->bindValue(':i_file_img', $img_file);
        if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
        unset($rsts);

        foreach ($taggits as $taggit)
        {
            $rsts[] = $prepare->bindValue(':section_id', $taggit['sid']);
            $rsts[] = $prepare->bindValue(':metadata_id', $taggit['mid']);
            $rsts[] = $prepare->execute();

            if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
            unset($rsts);
        }

        return true;
    }

    function removeTaggitsImage($menu_id, $img_file, $taggits)
    {
        if (count($taggits) === 0) return true;

        $img_query =<<<EOQ
            SELECT id
            FROM tblMenuImages
            WHERE menu_id = :i_menu_id
            AND file_img = :i_file_img
EOQ;

        $query =<<<EOQ
            DELETE FROM tblTaggitsImage
            WHERE menu_id = :menu_id
            AND section_id = :section_id
            AND metadata_id = :metadata_id
            AND img_id = ({$img_query})
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $rsts[] = $prepare->bindValue(':menu_id', $menu_id);
        $rsts[] = $prepare->bindValue(':i_menu_id', $menu_id);
        $rsts[] = $prepare->bindValue(':i_file_img', $img_file);
        if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
        unset($rsts);

        foreach ($taggits as $taggit)
        {
            $rsts[] = $prepare->bindValue(':section_id', $taggit['sid']);
            $rsts[] = $prepare->bindValue(':metadata_id', $taggit['mid']);
            $rsts[] = $prepare->execute();

            if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
            unset($rsts);
        }

        return true;
    }

    function getTaggitsByImageFile($menu_id, $img_file)
    {
        $query =<<<EOQ
            SELECT
                s.section_id AS sid,
                s.name AS section,
                m.metadata_id AS mid,
                GROUP_CONCAT(mv.value ORDER BY mv.keyindex ASC SEPARATOR '') AS metadata
            FROM tblTaggitsImage t
            INNER JOIN tblMenuImages i ON t.menu_id = i.menu_id AND t.img_id = i.id
            INNER JOIN tblMenuSection s ON t.section_id = s.section_id
            INNER JOIN tblMenuMetadata m ON t.metadata_id = m.metadata_id
            LEFT JOIN tblMenuMetadataValues mv ON (m.metadata_id = mv.metadata_id) AND (mv.key = 'label')
            WHERE t.menu_id = :menu_id
            AND i.file_img = :img_file
            GROUP BY mv.metadata_id, mv.key
EOQ;

        $params = array(':menu_id'=>$menu_id, ':img_file'=>$img_file);

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $taggits = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $taggits;
    }

    function getTaggitCntForImages($menu_id)
    {
        $query =<<<EOQ
            SELECT
                m.section_id,
                m.metadata_id,
                COUNT(t.img_id) AS tag_cnt
            FROM tblMenuMetadata m
            LEFT JOIN tblTaggitsImage t ON m.metadata_id = t.metadata_id
            WHERE m.menu_id = :menu_id
            GROUP BY m.section_id, m.metadata_id
EOQ;

        $params = array(':menu_id'=>$menu_id);

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $taggit_cnt = array();
        $rows = $prepare->fetchAll(PDO::FETCH_ASSOC);

        foreach ($rows as $row)
        {
            $sid = $row['section_id'];
            $mid = $row['metadata_id'];
            $cnt = $row['tag_cnt'];

            if (!isset($taggit_cnt[$sid]))
                $taggit_cnt[$sid] = array();

            if (!isset($taggit_cnt[$sid][$mid]))
                $taggit_cnt[$sid][$mid] = 0;

            $taggit_cnt[$sid][$mid] = $cnt;
        }

        return $taggit_cnt;
    }

    function getIdAndNames($menu_id, $section_id, $item_id)
    {
        if (is_null($section_id))
            $section_id = 'null';

        if (is_null($item_id))
            $item_id = 'null';

        // Note sure if this is "correct" after add tblMenuMetadataValues
        $query =<<<EOQ
            SELECT
                i.menu_id AS menu_id,
                i.name AS menu,
                s.section_id AS section_id,
                s.name AS section,
                m.metadata_id AS item_id,
                GROUP_CONCAT(mv.value ORDER BY mv.keyindex ASC SEPARATOR '') AS item
            FROM tblMenuInfo_us i
            LEFT JOIN tblMenuSection s ON i.menu_id = s.menu_id AND s.section_id = :section_id
            LEFT JOIN tblMenuMetadata m ON i.menu_id = m.menu_id AND s.section_id = m.section_id AND m.metadata_id = :metadata_id
            LEFT JOIN tblMenuMetadataValues mv ON (m.metadata_id = mv.metadata_id) AND (mv.key = 'label')
            WHERE i.menu_id = :menu_id
            GROUP BY mv.metadata_id, mv.key
EOQ;

        $params = array
        (
            ':menu_id' => $menu_id,
            ':section_id' => $section_id,
            ':metadata_id' => $item_id,
        );

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $row = $prepare->fetch(PDO::FETCH_ASSOC);
        if (empty($row))
            return false;

        return $row;
    }

    function updateMenuComments($comment_id, $menu_id, $user_id, $img_id, $comment)
    {
        $is_insert = $comment_id < 0;

        if ($is_insert)
        {
            $new_id = $this->insertMenuComments($menu_id, $user_id, $img_id, $comment);
            return $new_id;
        }

        $query =<<<EOQ
            UPDATE tblMenuComments
            SET
                edit_ts = CURRENT_TIMESTAMP,
                comment = :comment
            WHERE comment_id = :comment_id
            AND menu_id = :menu_id
            AND user_id = :user_id
EOQ;

        $params = array
        (
            ':comment_id' => $comment_id,
            ':menu_id' => $menu_id,
            ':user_id' => $user_id,
            ':comment' => $comment,
        );

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        return $comment_id;
    }

    function insertMenuComments($menu_id, $user_id, $img_id, $comment)
    {
        $query =<<<EOQ
            INSERT INTO tblMenuComments
            SET
                menu_id = :menu_id,
                user_id = :user_id,
                img_id = :img_id,
                comment = :comment
EOQ;

        $params = array
        (
            ':menu_id' => $menu_id,
            ':user_id' => $user_id,
            ':img_id' => $img_id,
            ':comment' => $comment,
        );

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $new_id = $this->lastInsertId();
        return $new_id;
    }

    function updateTaggitsComment($menu_id, $comment_id, $add_taggits, $remove_taggits)
    {
        $this->beginTransaction();

        $bCommit = $this->removeTaggitsComment($menu_id, $comment_id, $remove_taggits);
        if (!$bCommit) return false;

        $bCommit = $this->addTaggitsComment($menu_id, $comment_id, $add_taggits);
        if (!$bCommit) return false;

        $this->commit();
        return true;
    }

    function addTaggitsComment($menu_id, $comment_id, $taggits)
    {
        if (count($taggits) === 0) return true;

        $query =<<<EOQ
            INSERT INTO tblTaggitsComment(
                menu_id,
                section_id,
                metadata_id,
                comment_id
            ) VALUES (
                :menu_id,
                :section_id,
                :metadata_id,
                :comment_id
            )
            ON DUPLICATE KEY UPDATE ts = CURRENT_TIMESTAMP
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        /*
            WARNING: The 'ignore' will NOT cause errors if the values
            are wrong.  It will just put the default value.

            Might need to do a post process thing to remove invalid
            rows.
        */

        $rsts[] = $prepare->bindValue(':menu_id', $menu_id);
        $rsts[] = $prepare->bindValue(':comment_id', $comment_id);
        if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
        unset($rsts);

        foreach ($taggits as $taggit)
        {
            $rsts[] = $prepare->bindValue(':section_id', $taggit['sid']);
            $rsts[] = $prepare->bindValue(':metadata_id', $taggit['mid']);
            $rsts[] = $prepare->execute();

            if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
            unset($rsts);
        }

        return true;
    }

    function removeTaggitsComment($menu_id, $comment_id, $taggits)
    {
        if (count($taggits) === 0) return true;

        $query =<<<EOQ
            DELETE FROM tblTaggitsImage
            WHERE menu_id = :menu_id
            AND section_id = :section_id
            AND metadata_id = :metadata_id
            AND comment_id = :comment_id
EOQ;

        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;

        $rsts[] = $prepare->bindValue(':menu_id', $menu_id);
        $rsts[] = $prepare->bindValue(':comment_id', $comment_id);
        if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
        unset($rsts);

        foreach ($taggits as $taggit)
        {
            $rsts[] = $prepare->bindValue(':section_id', $taggit['sid']);
            $rsts[] = $prepare->bindValue(':metadata_id', $taggit['mid']);
            $rsts[] = $prepare->execute();

            if (!$this->areDbResultsGood($rsts, __FILE__, __LINE__)) return false;
            unset($rsts);
        }

        return true;
    }

    function getTaggitsByCommentId($menu_id, $comment_id)
    {
        // Note sure if this is "correct" after add tblMenuMetadataValues
        $query =<<<EOQ
            SELECT
                s.section_id AS sid,
                s.name AS section,
                m.metadata_id AS mid,
                GROUP_CONCAT(mv.value ORDER BY mv.keyindex ASC SEPARATOR '') AS metadata
            FROM tblTaggitsComment t
            INNER JOIN tblMenuComments c ON t.menu_id = c.menu_id AND t.comment_id = c.comment_id
            INNER JOIN tblMenuSection s ON t.section_id = s.section_id
            INNER JOIN tblMenuMetadata m ON t.metadata_id = m.metadata_id
            LEFT JOIN tblMenuMetadataValues mv ON (m.metadata_id = mv.metadata_id) AND (mv.key = 'label')
            WHERE t.menu_id = :menu_id
            AND c.comment_id = :comment_id
            GROUP BY mv.metadata_id, mv.key
EOQ;

        $params = array(':menu_id'=>$menu_id, ':comment_id'=>$comment_id);

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $taggits = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $taggits;
    }

    function getMenuComments($menu_id)
    {
        $aes_key = SQL_AES_KEY;

        $query =<<<EOQ
            SELECT
                c.comment_id,
                c.reply_id,
                c.ts,
                c.edit_ts,

                c.user_id,
                AES_DECRYPT(u.username, '{$aes_key}_username') AS username,
                AES_DECRYPT(u.firstname, '{$aes_key}_firstname') AS firstname,
                AES_DECRYPT(u.lastname, '{$aes_key}_lastname') AS lastname,

                c.img_id,
                i.file_img,

                c.comment
            FROM tblMenuComments c
            LEFT JOIN tblUsers u ON c.user_id = u.id
            LEFT JOIN tblMenuImages i ON c.img_id = i.id AND c.menu_id = i.menu_id
            WHERE c.menu_id = :menu_id
EOQ;

        $params = array(':menu_id'=>$menu_id);

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $comments = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $comments;
    }

    function getMenuSectionComments($menu_id, $section_id)
    {
        $aes_key = SQL_AES_KEY;

        /*
            NOTE: This might have "zombie" items because during
            taggits, if the metadata_id is not valid, it will insert
            a "0", so during this query, it will return it in this
            result
        */

        $query =<<<EOQ
            SELECT DISTINCT
                c.comment_id,
                c.reply_id,
                c.ts,
                c.edit_ts,

                c.user_id,
                AES_DECRYPT(u.username, '{$aes_key}_username') AS username,
                AES_DECRYPT(u.firstname, '{$aes_key}_firstname') AS firstname,
                AES_DECRYPT(u.lastname, '{$aes_key}_lastname') AS lastname,

                c.img_id,
                i.file_img,

                c.comment
            FROM tblMenuComments c
            INNER JOIN tblTaggitsComment t ON c.menu_id = t.menu_id AND c.comment_id = t.comment_id
            LEFT JOIN tblUsers u ON c.user_id = u.id
            LEFT JOIN tblMenuImages i ON c.img_id = i.id AND c.menu_id = i.menu_id
            WHERE c.menu_id = :menu_id
            AND t.section_id = :section_id
EOQ;

        $params = array(':menu_id' => $menu_id, ':section_id' => $section_id);

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $comments = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $comments;
    }

    function getMenuItemComments($menu_id, $section_id, $item_id)
    {
        $aes_key = SQL_AES_KEY;

        /*
            NOTE: This might have "zombie" items because during
            taggits, if the metadata_id is not valid, it will insert
            a "0", so during this query, it will return it in this
            result
        */

        $query =<<<EOQ
            SELECT DISTINCT
                c.comment_id,
                c.reply_id,
                c.ts,
                c.edit_ts,

                c.user_id,
                AES_DECRYPT(u.username, '{$aes_key}_username') AS username,
                AES_DECRYPT(u.firstname, '{$aes_key}_firstname') AS firstname,
                AES_DECRYPT(u.lastname, '{$aes_key}_lastname') AS lastname,

                c.img_id,
                i.file_img,

                c.comment
            FROM tblMenuComments c
            INNER JOIN tblTaggitsComment t ON c.menu_id = t.menu_id AND c.comment_id = t.comment_id
            LEFT JOIN tblUsers u ON c.user_id = u.id
            LEFT JOIN tblMenuImages i ON c.img_id = i.id AND c.menu_id = i.menu_id
            WHERE c.menu_id = :menu_id
            AND t.section_id = :section_id
            AND t.metadata_id = :item_id
EOQ;

        $params = array(':menu_id' => $menu_id, ':section_id' => $section_id, ':item_id' => $item_id);

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $comments = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $comments;
    }

    function getTaggitsCommentByMenuId($menu_id)
    {
        // Note sure if this is "correct" after add tblMenuMetadataValues
        $query =<<<EOQ
            SELECT
                c.comment_id,
                s.section_id AS sid,
                s.name AS section,
                m.metadata_id AS mid,
                GROUP_CONCAT(mv.value ORDER BY mv.keyindex ASC SEPARATOR '') AS metadata
            FROM tblTaggitsComment t
            INNER JOIN tblMenuComments c ON t.menu_id = c.menu_id AND t.comment_id = c.comment_id
            INNER JOIN tblMenuSection s ON t.section_id = s.section_id
            INNER JOIN tblMenuMetadata m ON t.metadata_id = m.metadata_id
            LEFT JOIN tblMenuMetadataValues mv ON (m.metadata_id = mv.metadata_id) AND (mv.key = 'label')
            WHERE t.menu_id = :menu_id
            GROUP BY mv.metadata_id, mv.key
EOQ;

        $params = array(':menu_id'=>$menu_id);

        $prepare = $this->prepareAndExecute($query, $params, __FILE__, __LINE__);
        if (!$prepare) return false;

        $taggits = $prepare->fetchAll(PDO::FETCH_ASSOC);
        return $taggits;
    }
}
