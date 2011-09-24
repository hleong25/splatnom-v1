<?php

class DBQuery
    extends PDO
{
    function __construct ($dsn, $username, $passwd, $options = null)
    {
        parent::__construct($dsn, $username, $passwd, $options);

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    function log_dberr($stmt, $file, $lineno)
    {
        if (get_class($stmt) === 'PDOStatement')
        {
            $err_code = $stmt->errorCode();
            $err_info = $stmt->errorInfo();
        }
        else
        {
            $err_code = $this->errorCode();
            $err_info = $this->errorInfo();
        }

//        error_log('errorcode: ' . var_export($err_code, true));
//        error_log('errorinfo: ' . var_export($err_info, true));

        if ($err_code === 0)
            return false;

        $info = end($err_info);
        error_log("({$file}:{$lineno}) SQL Error({$err_code}): {$info}");
        return true;
    }

    function query_log($query, $file, $lineno)
    {
        $rst = $this->query($query);
        if (!$rst)
            $this->log_dberr($rst, $file, $lineno);

        return $rst;
    }

    function prepare_log($query, $file, $lineno)
    {
        $prepare = $this->prepare($query);
        if (!$prepare)
            $this->log_dberr($prepare, $file, $lineno);

        return $prepare;
    }

    function execute_log($prepare, $data, $file, $lineno)
    {
        $rst = $prepare->execute($data);
        if (!$rst)
            $this->log_dberr($rst, $file, $lineno);

        return $rst;
    }

    function prepareAndExecute($query, $data, $file, $lineno)
    {
        $prepare = $this->prepare_log($query, $file, $lineno);
        if (!$prepare) return false;

        $rst = $this->execute_log($prepare, $data, $file, $lineno);
        if (!$rst) return false;

        return $prepare;
    }
}
