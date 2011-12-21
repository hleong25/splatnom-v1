<?php
require_once('dbquery.class.php');

class Model
    extends DBQuery
{
	function __construct()
    {
        $dsn = sprintf('mysql:dbname=%s;host=%s', DB_NAME, DB_HOST);
        $username = DB_USERNAME;
        $password = DB_PASSWORD;
        $options = null;

        parent::__construct($dsn, $username, $password, $options);
	}

	function __destruct()
    {
        // empty
	}

    function areDbResultsGood($rsts, $file, $line)
    {
        if (!is_array($rsts))
        {
            $rsts = array($rsts);
        }

        foreach ($rsts as $rst)
        {
            if (!$rst)
            {
                $this->log_dberr($rst, $file, $line);
                return false;
            }
        }

        return true;
    }
}
