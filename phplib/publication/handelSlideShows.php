<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once  ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

$returnData = array();

if (isset($_POST['form']) && !empty($_POST['form'])) {
    $formArray = createArrayFromPostNV();

    //load slideshow list
    if (isset($_POST['action']) && !empty($_POST['action'])) {
        if (isset($formArray['name'])) unset($formArray['name']);
        if (isset($formArray['id'])) unset($formArray['id']);
        if (isset($formArray['authors'])) unset($formArray['authors']);
        if (isset($formArray['description'])) unset($formArray['description']);
//printR( $_POST );
        if (isset($formArray['owner'])) unset($formArray['owner']);
        $formArray['diskArea_id'] = $_POST['folderId'];
        $sqlSS = "SELECT *
                  FROM slide_slideshow
                  WHERE diskArea_id = ".$_POST['folderId']."
                    AND office_id = ".$_SESSION['office_id']."
                    AND office_nametag ='".$_SESSION['office_nametag']."'
                    ORDER BY name";

        $resultSS = MySQL::query($sqlSS,false,false);
        if (!empty($resultSS)) {

            foreach ($resultSS as $row) {
                $cover = (strlen(str_replace(' ', '', $row['cover'])) == 0 ? '' : $row['cover']);
                $returnData[] = array(
                    'id' => $row['slideshow_id'],
                    'name' => $row['name'],
                    'description' => htmlspecialchars_decode($row['description']),
                    'cover' => $cover
                );
            };
//print_r($returnData);
        } else {
            $returnData = array('error' => 'Slideshows can\'t be loaded!');
        }
        printResult($returnData);
    }
} else {
    printSortResult();
}