<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

if (isset($_POST['form']) && !empty($_POST['form'])) {
    $formArray = array();
    for ($i = 0; $i < count($_POST['form']); $i++) {
        $formArray[$_POST['form'][$i]['name']] = $_POST['form'][$i]['value'];
    }

    //save newly added attach
    if (isset($_POST['save']) && !empty($_POST['save'])) {

        $slideShowId = $formArray['id'];

        $attachArray = $_POST['save'];

        //check if slide is template
        $tA = array();
        foreach ($attachArray as $row)
            $tA[] = (int)$row;

        $slideSlideShowObject = new SlideSlideShow($slideShowId);
        $slideSlideShowObject->setDBField('attachment',trim(implode(',',$tA)));
        $result = $slideSlideShowObject->save();


        if ($result == true)
            $slidesArray = array('result' => 'success', 'message' => 'Attachment saved');
        else
            $slidesArray = array('error' => 'Attachment cant be saved');

        printSortResult($slidesArray);
    }

    //load attachment list
    if (isset($_POST['load']) && $_POST['load'] == '') {
        $sqlSlideshow = "SELECT attachment FROM slide_slideshow WHERE slideshow_id = " . MySQL::filter($formArray['id']) . " AND office_id = " . MySQL::filter($_SESSION['office_id']) . " AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' LIMIT 1";
        $result = MySQL::query($sqlSlideshow, false, false);

        if ($result[0]['attachment'] !== '') {
            $medialist = $result[0]['attachment'];
			if (!$medialist) $medialist='-1';
			
            $sqlMediaFiles = "SELECT DISTINCT mym.mymedia_id AS id, mym.name, mym.type, mym.mediatype, mym.mediaurl, mym.thumbnail_url, mym.uploaded, mym.uploaded_ts, mym.duration, mym.filesize
      FROM media_mymedia mym
      WHERE mym.mymedia_id IN (" . MySQL::filter($medialist) . ") AND mym.office_id = " . MySQL::filter($_SESSION['office_id']) . " AND mym.office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' ORDER BY mym.mymedia_id";

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
                $mediaFilesArray[$i] = array(
                    "id" => $query[$i]['id'],
                    "name" => $query[$i]['name'],
                    "type" => $query[$i]['type'],
                    "mediatype" => $query[$i]['mediatype'],
                    "mediaurl" => $query[$i]['mediaurl'],
                    "thumbnail_url" => $su,
                    "uploaded" => $query[$i]['uploaded'], //date( "Y.m.d." , $query[$i]['uploaded']),
                    "uploaded_ts" => $query[$i]['uploaded_ts']
                );
                if (!empty($query[$i]['duration']))
                    $mediaFilesArray[$i]["duration"] = $query[$i]['duration'];
                if (!empty($query[$i]['filesize']))
                    $mediaFilesArray[$i]["filesize"] = $query[$i]['filesize'];
            }
            $resF['result'] = $mediaFilesArray;

            printSortResult($resF);

        } else {
            $resF['result'] = array();
            printSortResult($resF);
        }
    }
} else {
    printSortResult();
}
?>