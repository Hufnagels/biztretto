<?php
if (!isset($_SESSION['li'])) { session_start(); }
include ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_header_text.php');
include ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_include_base.php');
include ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_auth.php');

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