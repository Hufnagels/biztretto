<?php

    /*********** Specify database *************/
//$db_connect = array('host' => 'localhost','user' => 'pisti','pass' => 'mancika','db' => 'ingatlan');
    define ("DB_HOST", "localhost"); // set database host
    define ("DB_USER", "pisti"); // set database user
    define ("DB_PASS", "kiskacsa"); // set database password
    define ("DB_NAME", "skilldev_default"); // set database name
    define ("DB_PREFIX", "skilldev_"); // set database name

//DO NOT EDIT ANYTHING BELOW!
    $username = "pisti";
    $password = "kiskacsa";
    $hostname = "localhost";
    $database = "skill_dev";
    $dbhandle = mysql_connect($hostname, $username, $password) or die("Unable to connect to MySQL");
    $selected = mysql_select_db($database, $dbhandle) or die("Could not select $database");
    mysql_query("SET character_set_results = 'utf8', character_set_client = 'utf8', character_set_connection = 'utf8', character_set_database = 'utf8', character_set_server = 'utf8'", $dbhandle) or die("Unable to connect to MySQL");
?>