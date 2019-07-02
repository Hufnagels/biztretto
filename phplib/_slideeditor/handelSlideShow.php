<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');

include ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

$returnData = array();
if (isset($_POST['form']) && !empty($_POST['form'])) {

    //save new slideshow
    if (isset($_POST['save']) && !empty($_POST['save'])) {
        $formArray = array();
        for ($i = 0; $i < count($_POST['form']); $i++) {
            $formArray[$_POST['form'][$i]['name']] = $_POST['form'][$i]['value'];
        }
        $formArray['createdDate'] = date("Y-m-d H:i:s", time());
        if (isset($formArray['id'])) unset($formArray['id']);
        if (isset($formArray['name'])) unset($formArray['name']);

        $name = MySQL::filter(purifyString($_POST['save'][0]['name']));
        $description = MySQL::filter(purifyString($_POST['save'][0]['description']));
        $slideShowArray = array('name' => $name, 'description' => $description);

        $array_of_values = array_merge($formArray, $slideShowArray);

        $insertID = MySQL::insert('slide_slideshow', $array_of_values);
        if (is_numeric($insertID))
        {
            $diskAreaId = (int)$array_of_values['diskArea'];
            if ($diskAreaId>0)
            {
                $mediaBoxObject = new MediaMediaBox(null);
                $mediaBoxObject->setDBField('diskArea_id',$diskAreaId);
                $mediaBoxObject->setDBField('office_id',$_SESSION['office_id']);
                $mediaBoxObject->setDBField('office_nametag',$_SESSION['office_nametag']);
                $mediaBoxObject->setDBField('name',$name?$name:$insertID.'. slideshow');
                $mediaBoxObject->setDBField('owner',$_SESSION['u_id']);
                $mediaBoxObject->save();

                if ($mediaBoxObject->getId()>0)
                {
                    MediaMediaBox::connectSlideShowToMediaBox($insertID,$mediaBoxObject->getId());
                }
            }
            $returnData = array('name' => $_POST['save'][0]['name'], 'id' => $insertID);
        }
        else
            $returnData = array('error' => 'Slideshow save was unsuccessfull!');

        printResult($returnData);

    }

    //rename existing slideshow
    if (isset($_POST['rename']) && !empty($_POST['rename'])) {
        $formArray = createArrayFromPostNV();

        $slideShowId = MySQL::filter($formArray['id']);
        $name = $_POST['rename'][0]['name'];
        $sortName = str_replace(' ', '', normalize_special_characters(strtolower($name)));

        $slideSlideShowObject = new SlideSlideShow($slideShowId);
        $slideSlideShowObject->setDBField('name',$name);
        $result = $slideSlideShowObject->save();

        if ($result)
            $returnData = array('sortname' => $sortName, 'name' => $name, 'id' => $slideShowId, 'message' => 'Successfully renamed!');
        else
            $returnData = array('error' => 'Slideshow rename was unsuccessfull!');

        printSortResult($returnData);
    }

    //load existing slideshow
    if (isset($_POST['load']) && !empty($_POST['load'])) {
        $formArray = array();
        for ($i = 0; $i < count($_POST['form']); $i++) {
            $formArray[MySQL::filter($_POST['form'][$i]['name'])] = MySQL::filter($_POST['form'][$i]['value']);
        }
        if (isset($formArray['name'])) unset($formArray['name']);
        if (isset($formArray['id'])) unset($formArray['id']);
        if (isset($formArray['owner'])) unset($formArray['owner']);

        if (isset($formArray['diskArea'])) unset($formArray['diskArea']);

        $formArray['slideshow_id'] = MySQL::filter($_POST['load']);

        $options = array(
            'table' => 'slide_slides',
            'fields' => '*',
            'condition' => $formArray,
            'conditionExtra' => '', //"name LIKE '%".$newDiskareaName['name']."%'",
            'order' => 'badge',
            'limit' => 100
        );
        $result = MySQL::select($options);

        if ($result) {
            foreach ($result as $row) {

                $returnData[] = array(
                    'id' => $row['slides_id'],
                    'type' => $row['type'],
                    'templateType' => $row['templateType'],
                    'html' => htmlspecialchars_decode($row['html']), //stripslashes($row['html']),//base64_decode($row['html']),
                    'tag' => $row['tag'],
                    'badge' => $row['badge'],
                    'error' => $row['missingContent'],
                    'slideLevel' => $row['slideLevel'],
                    'description' => htmlspecialchars_decode($row['description']),
                    'templateOption' => ($row['templateOption'] == NULL ? NULL : json_decode($row['templateOption'], true))
                );
            }
            ;

        } else {
            $returnData = array('error' => 'Slideshow can\'t be loaded!');
        }

        printResult($returnData);
    }

} else {
    $returnData = array('error' => 'Slideshow can\'t be loaded!');
    printSortResult($returnData);
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
$resF['result'] = $returnData;
$json = json_encode($resF, true);
echo $json;
?>