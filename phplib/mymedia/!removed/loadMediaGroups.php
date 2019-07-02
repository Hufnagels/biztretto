<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

if (isset($_POST['form']) && !empty($_POST['form'])) {
    $formArray = createArrayFromPostNV();
    if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);
    if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);

    switch ($_POST['action']){
         case 'load':
             $sql = "SELECT
                    mediabox_id, name
                    FROM media_mediabox
                    WHERE office_id = '" . MySQL::filter($_SESSION['office_id']) . "' AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' AND diskArea_id = '" . MySQL::filter($_POST['diskArea_id']) . "' ORDER BY name ASC";

             $result = MySQL::query($sql, false, false);

             if (!$result){
                 $returnArray = array(
                     'type'=>'error',
                     'message' => 'Can\'t load mediagroups'
                 );

             } else {
                 $db = count($result);
                 $mediaBoxesArray = array();

                 for ($j = 0; $j < $db; $j++) {
                     $nameTag = $result[$j]['name'];
                     $idTag = $result[$j]['mediabox_id'];
                     $doname = str_replace(' ', '', strtolower($nameTag));
                     $mediaBoxesArray[$j] = array(
                         "idTag" => $idTag,
                         "nameTag" => $nameTag,
                         "doname" => $doname,
                         "files" => array()
                     );
                 }
                 $returnArray = array(
                     'type'=>'success',
                     'message' => 'OKSA mediagroups loaded!',
                     'result' => $mediaBoxesArray
                 );
             }


             break;
        default:
            $returnArray = array(
                'type'=>'error',
                'message' => 'Misspelled data sent to server0'
            );
            break;
    }
} else {
    $returnArray = array(
        'type' => 'error',
        'message' => 'Misspelled data sent to server!'
    );
}
$_SESSION['LAST_ACTIVITY'] = time();
printSortResult($returnArray);
exit;


$html = '';
$html .= '<li class="viewAll" ><div class="mbcholder selected" data-object-id="viewAll"><div class="mbHeader"><span>view all</span><div class="pointer-right"></div></div></div></li>';


$sql = "SELECT
  mediabox_id, name
  FROM media_mediabox 
  WHERE office_id = '" . MySQL::filter($_SESSION['office_id']) . "' AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' AND diskArea_id = '" . MySQL::filter($_POST['diskArea']) . "' ORDER BY name ASC";

$query = MySQL::query($sql, false, false);
$db = count($query);
$mediaBoxesArray = array();

for ($j = 0; $j < $db; $j++) {
    $nameTag = $query[$j]['name'];
    $idTag = $query[$j]['mediabox_id'];
    $doname = str_replace(' ', '', strtolower($nameTag));
    $mediaBoxesArray[$j] = array(
        "idTag" => $idTag,
        "nameTag" => $nameTag,
        "doname" => $doname
    );
    $mediaFilesArray = array();
    $sqlMediaFiles = "SELECT DISTINCT mym.mymedia_id AS id, mym.name, mym.type, mym.mediatype, mym.mediaurl, mym.thumbnail_url, mym.uploaded, mym.uploaded_ts, mym.duration, mym.filesize
  FROM media_mymedia mym
    LEFT JOIN media_mediaboxfiles mbf 
    ON mbf.mymedia_id = mym.mymedia_id
    WHERE mbf.mediabox_id = " . MySQL::filter($idTag) . " AND mym.office_id = " . MySQL::filter($_SESSION['office_id']) . " AND mym.office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' ORDER BY mbf.mediaboxFiles_id";
    $mymquery = MySQL::query($sqlMediaFiles, false, false);
    $db2 = count($mymquery);

    for ($i = 0; $i < $db2; $i++) {
        switch ($mymquery[$i]['type']) {
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
                $su = $mymquery[$i]['thumbnail_url'];
                break;
        }
        $mediaFilesArray[$i] = array(
            "id" => $mymquery[$i]['id'],
            "name" => $mymquery[$i]['name'],
            "type" => $mymquery[$i]['type'],
            "mediatype" => $mymquery[$i]['mediatype'],
            "mediaurl" => $mymquery[$i]['mediaurl'],
            "thumbnail_url" => $su,
            "uploaded" => $mymquery[$i]['uploaded'], //date( "Y.m.d." , $query[$i]['uploaded']),
            "uploaded_ts" => $mymquery[$i]['uploaded_ts']
        );
        if (!empty($query[$i]['duration']))
            $mediaFilesArray[$i]["duration"] = $query[$i]['duration'];
        if (!empty($query[$i]['filesize']))
            $mediaFilesArray[$i]["filesize"] = $query[$i]['filesize'];


    }
    $mediaBoxesArray[$j]['files'] = $mediaFilesArray;

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