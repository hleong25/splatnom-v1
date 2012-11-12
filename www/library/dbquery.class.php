<?php

class DBQuery
    extends PDO
{
    function __construct ($dsn, $username, $passwd, $options = null)
    {
        try
        {
            parent::__construct($dsn, $username, $passwd, $options);
        }
        catch (PDOException $e)
        {
            $code = $e->getCode();
            $msg = $e->getMessage();
            $trace = $e->getTraceAsString();

            Util::logit("PDOException Error Code: $code. Reason: $msg\n$trace");
            print "Error: $msg<br/>";
            die();
        }

        $this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_WARNING);
    }

    function log_dberr($err_obj, $file, $lineno)
    {
        if (get_class($err_obj) === 'PDOStatement')
        {
            $err_code = $err_obj->errorCode();
            $err_info = $err_obj->errorInfo();
        }
        else if (get_class($err_obj) === 'PDOException')
        {
            $err_code = $err_obj->getCode();
            $err_info = $err_obj->getMessage();
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
