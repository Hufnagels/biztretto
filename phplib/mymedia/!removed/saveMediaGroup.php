<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
$returnData=array();

if (isset($_POST['form']) && !empty($_POST['form'])) {

    //save new mediagroup
    if (isset($_POST['save']) && !empty($_POST['save'])) {
        $formArray = createArrayFromPostNV();
        if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);
        $formArray['createdDate'] = date("Y-m-d H:i:s", time());
        $mediaGroupName = MySQL::filter($_POST['save']);
        $mediaGroupArray = array('name' => $mediaGroupName);
        $array_of_values = array_merge($formArray, $mediaGroupArray);

        $mediaMediaBoxObject = new MediaMediaBox(null);
        $mediaMediaBoxObject->setDBField('office_id',$_SESSION['office_id']);
        $mediaMediaBoxObject->setDBField('office_nametag',$_SESSION['office_nametag']);
        $mediaMediaBoxObject->setDBField('owner',$_SESSION['u_id']);
        $mediaMediaBoxObject->setDBField('name',$array_of_values['name']);
        $mediaMediaBoxObject->save();
        $insertID = $mediaMediaBoxObject->getId();
        if ($insertID>0)
            $returnData = array('resultId' => $insertID);
        else
            $returnData = array('error' => 'Mediabox save was unsuccessfull!');

    }

    //rename new mediagroup
    if (isset($_POST['rename'][0]) && !empty($_POST['rename'][0])) {
        $formArray = array();
        for ($i = 0; $i < count($_POST['form']); $i++) {
            $formArray[$_POST['form'][$i]['name']] = $_POST['form'][$i]['value'];
        }
        if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);

        //$formArray['createdDate'] = date( "Y-m-d H:i:s" , time() );
        $mediaGroupName = MySQL::filter($_POST['rename'][0]['name']);
        $mediaGroupId = MySQL::filter($_POST['rename'][0]['id']);

        $sortname = normalize_special_characters(strtolower($mediaGroupName));
        $sortname = str_replace(' ', '', $sortname);

        $mediaGroupArray = array('mediabox_id' => $mediaGroupId);
        $array_of_conditions = array_merge($formArray, $mediaGroupArray);
        $whatToSet = array('name' => $mediaGroupName);

        $result = MySQL::update('media_mediabox', $whatToSet, $array_of_conditions);

        if ($result)
            $returnData = array('sortname' => $sortname, 'newname' => $mediaGroupName);
        else
            $returnData = array('error' => 'Mediabox rename was unsuccessfull!');


    }

    //delete mediagroup
    if (isset($_POST['delete']) && $_POST['delete'] !== '') {
        $formArray = createArrayFromPostNV();

        if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);

        $delID = MySQL::filter($_POST['delete']);

        $mediaMediaBoxObject = new MediaMediaBox($delID);
        $result = $mediaMediaBoxObject->remove();

        if ($result)
            $returnData = array('result' => $result);
        else
            $returnData = array('error' => 'Mediabox delete unsuccessfull!');
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