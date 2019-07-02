<?php
/**
 * User adatok betoltese, visszateres JSON objektummal, ami az adatbazis user_u tabla mezoit tartalmazza
 */
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
include ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
include ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

$imageURL = $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/';
$jsonResultArray = array();

$user = new User($_SESSION['u_id']);
$userData = $user->getDBFields();

//itt kitakaritom ami nem kell
unset($userData['pwd'],$userData['userlevel']);

$jsonResultArray['result']=$userData;

header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-Disposition: inline; filename="files.json"');
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Credentials:false');
header('Access-Control-Allow-Headers:Content-Type, Content-Range, Content-Disposition, Content-Description');
header('Access-Control-Allow-Origin:*');
header('Content-type: application/json');
header('Expires:Thu, 19 Nov 1981 08:52:00 GMT');
header('Keep-Alive:timeout=15, max=100');
header('Pragma:no-cache');
echo json_encode($jsonResultArray, true);
?>