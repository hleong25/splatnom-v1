<?php

class MenuModel
    extends Model
{
    function saveNewMenu($addy)
    {
        /*
            select * 
            from tblNewMenu menu
            left join tblNewMenuImages menu_img on menu.id = menu_img.newmenu_id
         */
        
        $this->beginTransaction();
        
        $query =<<<EOQ
            INSERT INTO tblNewMenu (
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
            INSERT INTO tblNewMenuImages (
                newmenu_id,
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
    
    function createNew($info)
    {
        $this->beginTransaction();
        
        $query = <<<EOQ
            INSERT INTO tblPlaceContact(
                name,
                addy1, addy2,
                city, state, zip,
                phone
            )
            VALUES (
                :name,
                :addy1, :addy2,
                :city, :state, :zip,
                :phone
            )
EOQ;
        
        $prepare = $this->prepareAndExecute($query, $info, __FILE__, __LINE__);
        if (!$prepare) return false;
        
        $this->commit();
        
        $new_id = $this->lastInsertId();
        return $new_id;
    }
    
    function saveUploadedImages($new_id)
    {
        $this->beginTransaction();
        
        $query =<<<EOQ
            INSERT INTO tblPlaceImages(
                place_id,
                file_img
            )
            VALUES (
                :id,
                :img
            )
EOQ;
        
        $prepare = $this->prepare_log($query, __FILE__, __LINE__);
        if (!$prepare) return false;
        
        $files = handle_upload_files();
        //logit($files);
        
        $params = array (
            ':id' => $new_id,
            ':img' => ''
        );
        
        foreach ($files as $img)
        {
            $params[':img'] = $img;
            
            $rst = $this->execute_log($prepare, $params, __FILE__, __LINE__);
        }
        
        $this->commit();
        
        return true;
    }
    
    function getMenu($id)
    {
        $query =<<<EOQ
            SELECT 
                name,
                addy1, addy2,
                city, state, zip,
                phone,
                modified
            FROM tblPlaceContact
            WHERE id = :id
EOQ;
        
        $prepare = $this->prepareAndExecute($query, array(':id' => $id), __FILE__, __LINE__);
        if (!$prepare) return false;
        
        return $prepare->fetch(PDO::FETCH_ASSOC);
    }
    
    function purgeNewMenu($id)
    {
        // 1. find images in db
        // 2. cache img files
        // 3. delete from tblNewMenuImages
        // 4. delete from tblNewMenus
        // 5. move cached img files to purge storage
        
        $this->beginTransaction();
        
        $menu_id = array(':id' => $id);
        
        $query =<<<EOQ
            SELECT 
                id, 
                file_img
            FROM tblNewMenuImages
            WHERE newmenu_id = :id
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
            DELETE FROM tblNewMenuImages
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
            DELETE FROM tblNewMenu
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
    
    function getNewMenu($id)
    {
        $menu = array();
        $menu_id = array(':id' => $id);
        
        $query =<<<EOQ
            SELECT 
                site_addy
            FROM tblNewMenu
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
            FROM tblNewMenuImages
            WHERE newmenu_id = :id
EOQ;
        
        $prepare = $this->prepareAndExecute($query, $menu_id, __FILE__, __LINE__);
        if (!$prepare) return false;
        
        $imgs = $prepare->fetchAll(PDO::FETCH_ASSOC);
        $menu['imgs'] = array();
        foreach ($imgs as $img)
            $menu['imgs'][] = $img['file_img'];
        
        return $menu;
    }
}