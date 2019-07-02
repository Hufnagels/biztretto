<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');

$imageURL = $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/';

if (isset($_POST['form']) && !empty($_POST['form'])) {
    //youtube video
    if (isset($_POST['remote']) && !empty($_POST['remote'])) {

        $formArray = createArrayFromPostNV();

        if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);

        $mediaFilesArray = $_POST['remote'][0];
        $array_of_values = array_merge($formArray, $mediaFilesArray);

        $mediaMediaBoxFiles = new MediaMediaBoxFiles(null);
        $mediaMediaBoxFiles->setDBField('diskArea_id', $array_of_values['diskArea_id']);
        $mediaMediaBoxFiles->setDBField('office_id', $_SESSION['office_id']);
        $mediaMediaBoxFiles->setDBField('office_nametag', $_SESSION['office_nametag']);
        $mediaMediaBoxFiles->setDBField('owner', $_SESSION['u_id']);
        $mediaMediaBoxFiles->setDBField('type', $array_of_values['type']);
        $mediaMediaBoxFiles->setDBField('mediatype', $array_of_values['mediatype']);
        $mediaMediaBoxFiles->setDBField('name', $array_of_values['name']);
        $mediaMediaBoxFiles->setDBField('uploaded_ts', $array_of_values['uploaded_ts']);
        $mediaMediaBoxFiles->setDBField('uploaded', $array_of_values['uploaded']);
        $mediaMediaBoxFiles->setDBField('thumbnail_url', $array_of_values['thumbnail_url']);
        $mediaMediaBoxFiles->setDBField('mediaurl', $array_of_values['mediaurl']);
        $mediaMediaBoxFiles->setDBField('duration', $array_of_values['duration']);
        $mediaMediaBoxFiles->save();

        $mediaFilesArray['id'] = $mediaMediaBoxFiles->getId();
    }
    //locally saved files
    if (isset($_POST['local']) && !empty($_POST['local'])) {
        $formArray = createArrayFromPostNV();

        $diskAreaName = $formArray['diskArea_name'];

        if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);

        $mediaFilesArray = $_POST['local'];

        $db = count($mediaFilesArray);
        for ($i = 0; $i < $db; $i++) {
            switch ($mediaFilesArray[$i]['type']) {
                case 'image':
                case 'word':
                case 'excel':
                case 'powerpoint':
                case 'pdf':
                    $size = $mediaFilesArray[$i]['additional'];
                    unset($mediaFilesArray[$i]['additional']);
                    $mediaFilesArray[$i]['filesize'] = $size;
                    break;
                case 'audio':
                case 'video':
                    $duration = $mediaFilesArray[$i]['additional'];
                    unset($mediaFilesArray[$i]['additional']);
                    $mediaFilesArray[$i]['duration'] = $duration;
                    break;
            }
            $formArray['createdDate'] = date("Y-m-d H:i:s", time());

            $mediaFilesArray[$i]['folder'] = $diskAreaName;

            $url_prefix = connectionType() . $imageURL; //.$diskAreaName.'/';
            $temp = explode($url_prefix, $mediaFilesArray[$i]['mediaurl']);
            $mediaFilesArray[$i]['mediaurl'] = $temp[1];
            $temp = explode($url_prefix, $mediaFilesArray[$i]['thumbnail_url']);
            $mediaFilesArray[$i]['thumbnail_url'] = $temp[1];
            $array_of_values = array_merge($formArray, $mediaFilesArray[$i]);
            $insertID = MySQL::insert('media_mymedia', $array_of_values);
            $mediaFilesArray[$i]['id'] = $insertID;
        }
    }
} else {
    $mediaFilesArray = array('error' => 'Misspelled data sent to server!');
}
$_SESSION['LAST_ACTIVITY'] = time();
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
$json = json_encode($mediaFilesArray, true);
echo $json;
?>