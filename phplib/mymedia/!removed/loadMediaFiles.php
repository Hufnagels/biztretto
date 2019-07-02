<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

$sql = "SELECT
  mymedia_id AS id, name, type, mediatype, mediaurl, thumbnail_url, folder, uploaded, uploaded_ts, duration, filesize, size
  FROM media_mymedia 
  WHERE office_id = '" . MySQL::filter($_SESSION['office_id']) . "' AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' AND diskArea_id = '" . MySQL::filter($_POST['diskArea']) . "' ORDER BY name ASC";

$query = MySQL::query($sql, false, false);

$db = count($query);
$mediaFilesArray = array();
for ($i = 0; $i < $db; $i++) {
    switch ($query[$i]['type']) {
        case 'audio':
            $su = "audio-grey.png";
            break;
        //case 'video': $su = "http://img.skillbi.local/160x120.gif"; break;
        case 'pdf':
            $su = "pdf-grey.png";
            break;
        case 'excel':
            $su = "excel-grey.png";
            break;
        case 'word':
            $su = "doc-grey.png";
            break;
        //case 'image': $su = "http://img.skillbi.local/160x120.gif"; break;
        default:
            $su = $query[$i]['thumbnail_url'];
            break;
    }

    $mediaFilesArray[$i] = array(
        "id" => $query[$i]['id'],
        "name" => $query[$i]['name'],
        "type" => $query[$i]['type'],
        "mediatype" => $query[$i]['mediatype'],
        "mediaurl" => $query[$i]['mediaurl'],
        "thumbnail_url" => $su,
        //"folder" => $query[$i]['folder'],
        "uploaded" => $query[$i]['uploaded'], //date( "Y.m.d." , $query[$i]['uploaded']),
        "uploaded_ts" => $query[$i]['uploaded_ts']

    );
    if (!empty($query[$i]['duration']))
        $mediaFilesArray[$i]["duration"] = $query[$i]['duration'];
    if (!empty($query[$i]['filesize']))
        $mediaFilesArray[$i]["filesize"] = $query[$i]['filesize'];

    //}
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
$resF['result'] = $mediaFilesArray;
$json = json_encode($resF, true);
echo $json;
?>