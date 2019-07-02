<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

$sql = "SELECT
  slideshow_id, name, description
  FROM slide_slideshow 
  WHERE office_id = '" . MySQL::filter($_SESSION['office_id']) . "' AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'  ORDER BY name ASC";

$query = MySQL::query($sql, false, false);
$db = count($query);
$slideShowArray = array();

for ($j = 0; $j < $db; $j++) {
    $slideShowArray[$j] = array(
        "id" => $query[$j]['slideshow_id'],
        "name" => $query[$j]['name'],
        "doname" => str_replace(' ', '', strtolower($query[$j]['name'])),
        'description' => $query[$j]['description']
    );
}

$_SESSION['LAST_ACTIVITY'] = time();

//header 
header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-Disposition: inline; filename="files.json"');
// Prevent Internet Explorer from MIME-sniffing the content-type:
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Credentials:false');
header('Access-Control-Allow-Headers:Content-Type, Content-Range, Content-Disposition, Content-Description');
header('Access-Control-Allow-Origin:*');
header('Content-type: application/json');
header('Expires:Thu, 19 Nov 1981 08:52:00 GMT');
header('Keep-Alive:timeout=15, max=100');
header('Pragma:no-cache');
$resF = array('result' => $slideShowArray);
$json = json_encode($resF, true);
echo $json;
?>