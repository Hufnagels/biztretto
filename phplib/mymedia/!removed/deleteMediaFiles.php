<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

if (isset($_POST['form']) && !empty($_POST['form'])) {

    //check if fulltextsearch returned error!!!
    $postResult = $_POST['slides'];
    if (isset($postResult['error']) && $postResult['error'] == 'Misspelled data sent to server!') {
        $returnData = array('error' => 'Misspelled data sent to server!');
    } else {
        //delete mediagroup
        if (isset($_POST['selected']) && !empty($_POST['selected'])) {
            $formArray = createArrayFromPostNV();

            $mediaFilesArray = $_POST['selected'];
            $db = count($mediaFilesArray);
            $delID = array();
            for ($i = 0; $i < $db; $i++) {
                $delID[] = MySQL::filter($mediaFilesArray[$i]['id']);
            }

            $result = MediaMediaBoxFiles::removeFileByIds($delID);

            if ($result == true) {
                //update slides state affected by missing files
                //if is any
                if (!isset($postResult['error'])) {
                    $source_by_id = array();
                    foreach ($postResult as $row) {
						foreach ($row['slides'] as $row1)
							$source_by_id[$row1['id']] = (int)$row1['id'];
                    }

                    $source_by_id = array_values($source_by_id);
                    asort($source_by_id);

                    $datetime = date('Y-m-d H:i:s', time());
                    $sqlToArray = "
                        UPDATE slide_slides SET missingContent = 'Media content were deleted on " . MySQL::filter($datetime) . "'
                        WHERE slides_id IN (" . MySQL::filter(implode(',', $source_by_id)) . ") AND office_id = " . (int)$_SESSION['office_id'] . " AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'";

                    $updateResult = MySQL::execute($sqlToArray);
                    if ($updateResult == false)
                        $returnData = array('error' => 'Can\'t update slide status!');
                }

                //delete files and thumnbs if have
                $path = IMGPATH . $_SESSION['office_nametag'] . '/' . $formArray['diskArea_name'] . '/';

                for ($i = 0; $i < $db; $i++) {
                    if ($mediaFilesArray[$i]['mediatype'] == 'local') {                        
                        $delFiles = glob($path . pathinfo($mediaFilesArray[$i]['mediaelement'], PATHINFO_FILENAME) . '.*');

                        foreach ($delFiles as $k => $v)
                            rrmdir($delFiles[$k]);

                        //video or image thumb
                        switch ($mediaFilesArray[$i]['type']) {
                            case 'image':
                            case 'video':
                                $thumbname = pathinfo($mediaFilesArray[$i]['mediaelement'], PATHINFO_FILENAME) . '.jpg';
                                $delThumb = $path . 'thumbnail/' . $thumbname;
//printR($delThumb);
                                rrmdir($delThumb);
                                break;
                        }
                    }
                    //local
                }
                //for
                $returnData = array('result' => $result, 'message' => 'Successfully deleted!');
            } else {
                $returnData = array('error' => 'Can\'t delete from database!');
            }
        } else {
            $returnData = array('error' => 'Misspelled data sent to server! no files');
        }
    }
} else { //if form data is empty
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
//$resF['result'] = $mediaFilesArray;
$json = json_encode($returnData, true);
echo $json;
?>