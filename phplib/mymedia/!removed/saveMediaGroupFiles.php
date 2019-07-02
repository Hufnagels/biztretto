<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
$returnData = array();

if (isset($_POST['form']) && !empty($_POST['form'])) {

    //save newly added files to selected mediagroup
    if (isset($_POST['type']) && $_POST['type'] == 'save') {
        $formArray = createArrayFromPostNV();

        if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);

        $formArray['createdDate'] = date("Y-m-d H:i:s", time());
        //selected mediagroup
        $mediaGroupID = MySQL::filter($_POST['selected']);
        //insert each file to db
        $db = count($_POST['result']);

        for ($i = 0; $i < $db; $i++) {
            $tempIDs = MySQL::filter($_POST['result'][$i]['id']);
            $mediaFilesArray = array('mymedia_id' => $tempIDs, 'mediabox_id' => $mediaGroupID);
            $array_of_values = array_merge($formArray, $mediaFilesArray);

            $mediaMediaBoxFiles = new MediaMediaBoxFiles(null);
            $mediaMediaBoxFiles->setDBField('diskArea_id',$array_of_values['diskArea_id']);
            $mediaMediaBoxFiles->setDBField('office_id',$_SESSION['office_id']);
            $mediaMediaBoxFiles->setDBField('office_nametag',$_SESSION['office_nametag']);
            $mediaMediaBoxFiles->setDBField('owner',$_SESSION['u_id']);
            $mediaMediaBoxFiles->setDBField('mymedia_id',$array_of_values['mymedia_id']);
            $mediaMediaBoxFiles->setDBField('mediabox_id',$array_of_values['mediabox_id']);
            $mediaMediaBoxFiles->save();

            $insertID = $mediaMediaBoxFiles->getId();
        }

        $returnData = array('result' => $insertID);
    }

    //delete linked files from selected mediagroup
    if (isset($_POST['type']) && $_POST['type'] == 'delete') {
        $formArray = createArrayFromPostNV();

        if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);

        $formArray['createdDate'] = date("Y-m-d H:i:s", time());

        //selected mediagroup
        $mediaGroupID = MySQL::filter($_POST['selected']);
        $tempIDs = MySQL::filter($_POST['result'][0]['id']);

        $sqlDelete = "DELETE from media_mediaboxfiles WHERE mymedia_id = " . MySQL::filter($tempIDs) . " AND mediabox_id = " . MySQL::filter($mediaGroupID) . " AND office_id = " . MySQL::filter($_SESSION['office_id']);

        $result = MySQL::execute($sqlDelete);

        $returnData = array('result' => $result);
    }
} else {
    $returnData = array('error' => 'Misspelled data sent to server!');
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
$json = json_encode($returnData, true);
echo $json;
?>