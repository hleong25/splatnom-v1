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

    function query($query)
    {
        if (LOG_QUERY)
            error_log($query);

        $rst = parent::query($query);
        $rst->setFetchMode(PDO::FETCH_ASSOC);

        return $rst;
    }
}
