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
                    Pendingmenu_id,
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

    //function createNew($info)
    //{
        //$this->beginTransaction();
        
        //$query = <<<EOQ
            //INSERT INTO tblPlaceContact(
                //name,
                //addy1, addy2,
                //city, state, zip,
                //phone
            //)
            //VALUES (
                //:name,
                //:addy1, :addy2,
                //:city, :state, :zip,
                //:phone
            //)
//EOQ;
        
        //$prepare = $this->prepareAndExecute($query, $info, __FILE__, __LINE__);
        //if (!$prepare) return false;
        
        //$this->commit();
        
        //$new_id = $this->lastInsertId();
        //return $new_id;
    //}
    
    //function saveUploadedImages($new_id)
    //{
        //$this->beginTransaction();
        
        //$query =<<<EOQ
            //INSERT INTO tblPlaceImages(
                //place_id,
                //file_img
            //)
            //VALUES (
                //:id,
                //:img
            //)
//EOQ;
        
        //$prepare = $this->prepare_log($query, __FILE__, __LINE__);
        //if (!$prepare) return false;
        
        //$files = handle_upload_files();
        ////logit($files);
        
        //$params = array (
            //':id' => $new_id,
            //':img' => ''
        //);
        
        //foreach ($files as $img)
        //{
            //$params[':img'] = $img;
            
            //$rst = $this->execute_log($prepare, $params, __FILE__, __LINE__);
        //}
        
        //$this->commit();
        
        //return true;
    //}
    
    //function getMenu($id)
    //{
        //$query =<<<EOQ
            //SELECT 
                //name,
                //addy1, addy2,
                //city, state, zip,
                //phone,
                //modified
            //FROM tblPlaceContact
            //WHERE id = :id
//EOQ;
        
        //$prepare = $this->prepareAndExecute($query, array(':id' => $id), __FILE__, __LINE__);
        //if (!$prepare) return false;
        
        //return $prepare->fetch(PDO::FETCH_ASSOC);
    //}
    
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
}
