<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

//egy diskarea-hoz tartozo mediabox lista legyujtesre
$sql = "SELECT 
  mediabox_id, name
  FROM media_mediabox 
  WHERE office_id = '" . MySQL::filter($_SESSION['office_id']) . "' AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' AND diskArea_id = '" . MySQL::filter($_POST['diskArea']) . "' ORDER BY name ASC";

//egy diskarea-hoz tartozo mediabox lista legyujtesre,
//valamint hogy összesen, illetve egyenkent mennyi file van
$sql = "
SELECT box.mediabox_id AS boxid, box.name AS name, COUNT(media.mymedia_id) AS darab
  FROM media_mediabox box
  LEFT JOIN media_mymedia media
    ON box.mediabox_id = media.mediabox_id
    WHERE box.office_id = " . MySQL::filter($_SESSION['office_id']) . " AND box.office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' AND box.diskArea_id = '" . MySQL::filter($_POST['diskArea']) . "' GROUP BY box.mediabox_id ORDER BY box.name ASC";

$query = MySQL::query($sql, false, false);
$db = count($query);
$mediaBoxesArray = array();
$i = 0;

//egy diskarea-hoz tartozo mediabox lista legyujtesre,
//valamint hogy összesen, illetve egyenkent mennyi file van
//all files
$sql2 = "
SELECT COUNT(mymedia_id) AS badge FROM media_mymedia
    WHERE office_id = " . MySQL::filter($_SESSION['office_id']) . " AND diskArea_id = '" . MySQL::filter($_POST['diskArea']) . "'";

$query2 = MySQL::query($sql2, false, false);

$mediaBoxesArray[0] = array(
    "id" => 'all',
    "name" => 'All files',
    "doname" => 'all files',
    "badge" => $query2[0]['badge']
);

//mediaboxes and files count
for ($j = 0; $j < $db; $j++) {
    $nameTag = $query[$j]['name'];
    $idTag = $query[$j]['boxid'];
    $badge = $query[$j]['darab'];
    $doname = str_replace(' ', '', strtolower($nameTag));
    $mediaBoxesArray[$j + 1] = array(
        "id" => $idTag,
        "name" => $nameTag,
        "doname" => $doname,
        "badge" => $badge
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
$resF = array('result' => $mediaBoxesArray);
$json = json_encode($resF, true);
echo $json;
?>