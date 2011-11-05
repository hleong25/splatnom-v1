<?php

class WsModel
    extends Model
{
    function search()
    {
        $query = 'SELECT * FROM tblPlaceContact';
        $rows = $this->query($query);
        $rows->setFetchMode(PDO::FETCH_ASSOC);
        return var_export($rows->fetchAll(), true);
    }

}
