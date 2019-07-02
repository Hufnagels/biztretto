<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once  ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once  ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

$boxId = MySQL:: filter($_POST['mediaGroup']);
$diskAreaId = MySQL:: filter($_POST['diskArea']);
if (isset($_POST['diskArea']) && isset($_POST['mediaGroup'])) {
    $allfiles = ($boxId == 'all') ? '' : "mbf.mediabox_id = " . $boxId . " AND";

    $sqlMediaFiles = "SELECT DISTINCT mym.mymedia_id AS id, mym.name, mym.type, mym.mediatype, mym.mediaurl, mym.thumbnail_url, mym.uploaded, mym.uploaded_ts, mym.duration, mym.filesize, mym.size
    FROM media_mymedia mym
      LEFT JOIN media_mediaboxfiles mbf 
      ON mbf.mymedia_id = mym.mymedia_id
      WHERE " . MySQL::filter($allfiles) . " mym.office_id = " . MySQL::filter($_SESSION['office_id']) . " AND mym.office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' AND mym.diskArea_id = " . MySQL::filter($diskAreaId) . " ORDER BY mbf.mediaboxFiles_id";

    $query = MySQL::query($sqlMediaFiles, false, false);


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
            case 'powerpoint':
                $su = "ppt-grey.png";
                break;
            default:
                $su = $query[$i]['thumbnail_url'];
                break;
        }
        $vw = 0;
        $vh = 0;
        if($query[$i]['size']!== FILTER_FLAG_EMPTY_STRING_NULL){
            $w=explode(',',$query[$i]['size']);
            $vw=$w[0];
            $vh=$w[1];
        }
        $mediaFilesArray[$i] = array(
            "id" => $query[$i]['id'],
            "name" => $query[$i]['name'],
            "type" => $query[$i]['type'],
            "mediatype" => $query[$i]['mediatype'],
            "mediaurl" => $query[$i]['mediaurl'],
            "thumbnail_url" => $su,
            "uploaded" => $query[$i]['uploaded'], //date( "Y.m.d." , $query[$i]['uploaded']),
            "uploaded_ts" => $query[$i]['uploaded_ts'],
            "videoWidth" => $vw,
            "videoHeight" => $vh
        );
        if (!empty($query[$i]['duration']))
            $mediaFilesArray[$i]["duration"] = $query[$i]['duration'];
        if (!empty($query[$i]['filesize']))
            $mediaFilesArray[$i]["filesize"] = $query[$i]['filesize'];

    }
} else {
    $mediaFilesArray = array('error' => 'Misspelled data sent to server!');
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
$resF = array('result' => $mediaFilesArray);
$json = json_encode($resF, true);
echo $json;
?>