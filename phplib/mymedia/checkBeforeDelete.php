<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

if (isset($_POST['form']) && !empty($_POST['form'])) {

    $formArray = createArrayFromPostNV();

    if (isset($_POST['check']) && !empty($_POST['check'])) {

        if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);

        //find affected mediaelement datas
        $db = count($_POST['check']);
        $searchArray = array();
        for ($i = 0; $i < $db; $i++) {
            $diskArea_id = MySQL::filter($_POST['check'][$i]['did']);
            $mymedia_id = MySQL::filter($_POST['check'][$i]['id']);
            $searchArray[] = $mymedia_id;
        }
        $searchString = implode(',', $searchArray);
        $sqlMedia = "
            SELECT mediatype, mediaurl, mymedia_id
            FROM media_mymedia
            WHERE mymedia_id IN (" . MySQL::filter($searchString) . ") AND office_id = " . (int)$_SESSION['office_id'] . " AND diskArea_id = " . MySQL::filter($formArray['diskArea_id']);

        //founded files data
        $preResult = MySQL::query($sqlMedia, false, false);

        //create search string for FULLTEXT search
        $searchString = '';
        if (!empty($preResult)) {
            $db = count($preResult);
            $searchArray = array();
            for ($i = 0; $i < $db; $i++) {
                if ($preResult[$i]['mediatype'] == 'local') {
                    $lf = explode('/', $preResult[$i]['mediaurl']);
                    $searchArray[] = $lf[1];
                } else {
                    $yt = explode('=', $preResult[$i]['mediaurl']);
                    $searchArray[] = $yt[1];
                }
            }
            $searchString = implode(' ', $searchArray);

            $sqlFULLTEXT = "
                SELECT ss.slides_id, ss.slideshow_id, ss.tag, ss.description, ss.badge, s.name
                FROM slide_slides ss
                RIGHT JOIN slide_slideshow s
                ON ss.slideshow_id = s.slideshow_id
                WHERE MATCH (ss.html) AGAINST( '" . MySQL::filter($searchString) . "' IN BOOLEAN MODE) AND ss.office_id = " . (int)$_SESSION['office_id'] . " AND ss.office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' ORDER BY s.slideshow_id ASC";

            $postResult = MySQL::query($sqlFULLTEXT, false, false);

            if (!empty($postResult)) {
                $mediaFilesArray = array();
                foreach ($postResult as &$row) {
                    $mediaFilesArray[$row['slideshow_id']]['name'] = $row['name'];
                    $mediaFilesArray[$row['slideshow_id']]['slides'][] = array(
                        'badge' => $row['badge'],
                        'tag' => $row['tag'],
                        'description' => htmlspecialchars_decode($row['description']),
                        'id' => $row['slides_id']
                    );
                }
                $mediaFilesArray = array_values($mediaFilesArray);
                $returnArray = array(
                    'type'=>'success',
                    'message' => 'Files checked!',
                    'result' => $mediaFilesArray
                );
            } else {
                $returnArray = array(
                    'type'=>'info',
                    'message' => 'No files affected in slides!'
                );

            }
        } else {
            $returnArray = array(
                'type'=>'error',
                'message' => 'Nothing to check!'
            );

        }
    } else {
        $returnArray = array(
            'type'=>'error',
            'message' => 'Nothing to check!'
        );
    }
} else {
    $returnArray = array(
        'type'=>'error',
        'message' => 'Misspelled data sent to server!'
    );

}
    $_SESSION['LAST_ACTIVITY'] = time();
    printSortResult($returnArray);
    exit;
?>