<?php

// Begin the session
session_start();
ob_start();//Hook output buffer
  require_once('db.php');
  require_once('class.base.php');
  //require_once('../include/class.json.php');
ob_end_clean();//Clear output buffer
//print_r($_GET);
$key = array_keys($_GET);
// To avoid case conflicts, make the input uppercase and check against the session value
// If it's correct, echo '1' as a string
if(strtoupper($_GET[$key[0]]) == strtoupper($_SESSION['random_number']))
	echo 'true';
// Else echo '0' as a string
else
	echo 'false';

//print_r($key);
?>