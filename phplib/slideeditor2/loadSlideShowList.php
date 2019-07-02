<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');


$additional = '';
if (isset($_POST['diskArea']) && $_POST['diskArea'] !== '') {
    $additional = "AND ss.diskArea_id = " . MySQL::filter($_POST['diskArea']);
}

$sql = "
SELECT 
    ss.slideshow_id, ss.name, ss.description, COUNT(sa.slides_id) AS darab,
    IF ((SELECT slideshow_id FROM training_slideshow WHERE training_slideshow.slideshow_id=ss.slideshow_id LIMIT 1)>0,1,0) AS readonly
  FROM slide_slideshow ss
  LEFT JOIN slide_slides sa
    ON ss.slideshow_id = sa.slideshow_id
    WHERE ss.office_id = " . MySQL::filter($_SESSION['office_id']) . " AND ss.office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' AND ss.isEnabled = 1 " . $additional . " GROUP BY ss.slideshow_id ORDER BY ss.name ASC";

$query = MySQL::query($sql, false, false);
$slideShowArray = array();

foreach ($query as $row) {
    $slideShowArray[] = array(
        'id' => $row['slideshow_id'],
        'name' => $row['name'],
        'readonly' => $row['readonly'],
        'doname' => str_replace(' ', '', strtolower($row['name'])),
        'count' => ($row['darab'] == NULL ? 0 : $row['darab']),
        'type' => 'normal',
        'description' => ($row['description'] == NULL ? '' : $row['description'])
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