<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

    if (isset($_POST['form']) && !empty($_POST['form'])) {
        $formArray = createArrayFromPostNV();
        if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);
        //if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);

        switch ($_POST['action']){
            case 'load':
                $sql = "SELECT
                        mymedia_id AS id, mediabox_id AS boxid, name, type, mediatype, mediaurl, thumbnail_url, folder, uploaded, uploaded_ts, duration, filesize, size
                        FROM media_mymedia
                        WHERE office_id = '" . MySQL::filter($_SESSION['office_id']) . "'
                            AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'
                            AND diskArea_id = '" . MySQL::filter($_POST['diskArea']) . "'
                            AND mediatype = 'local'";
                break;

            case 'loadgroup':
                $sql = "SELECT
                        mymedia_id AS id, mediabox_id AS boxid, name, type, mediatype, mediaurl, thumbnail_url, folder, uploaded, uploaded_ts, duration, filesize, size
                        FROM media_mymedia
                        WHERE office_id = '" . MySQL::filter($_SESSION['office_id']) . "'
                            AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'
                            AND diskArea_id = '" . MySQL::filter($_POST['diskArea']) . "'
                            AND mediabox_id = '" . MySQL::filter($_POST['groupid']) . "'
                            AND mediatype = 'local'";
                break;
            default:
                $returnArray = array(
                    'type'=>'error',
                    'message' => 'Misspelled data sent to server0'
                );
                $_SESSION['LAST_ACTIVITY'] = time();
                printSortResult($returnArray);
                exit;
                break;
        }

        $result = MySQL::query($sql, false, false);

        if (!$result){
            $returnArray = array(
                'type'=>'error',
                'message' => 'Can\'t load mediafiles'
            );

        } else {
            $db = count($result);
            $mediaFilesArray = array();
            for ($i = 0; $i < $db; $i++) {
                switch ($result[$i]['type']) {
                    case 'audio':
                        $su = "audio-grey.png";
                        break;
                    case 'pdf':
                        $su = "pdf-grey.png";
                        break;
                    case 'excel':
                        $su = "excel-grey.png";
                        break;
                    case 'word':
                        $su = "doc-grey.png";
                        break;
                    default:
                        $su = $result[$i]['thumbnail_url'];
                        break;
                }

                $vw = 0;
                $vh = 0;
                if($result[$i]['size']!== FILTER_FLAG_EMPTY_STRING_NULL){
                    $w=explode(',',$result[$i]['size']);
                    $vw=$w[0];
                    $vh=$w[1];
                }

                $mediaFilesArray[$i] = array(
                    "id"            => $result[$i]['id'],
                    "name"          => $result[$i]['name'],
                    "type"          => $result[$i]['type'],
                    "mediatype"     => $result[$i]['mediatype'],
                    "mediaurl"      => $result[$i]['mediaurl'],
                    "thumbnail_url" => $su,
                    "boxid"         => $result[$i]['boxid'],
                    "uploaded"      => $result[$i]['uploaded'], //date( "Y.m.d." , $query[$i]['uploaded']),
                    "uploaded_ts"   => $result[$i]['uploaded_ts'],
                    "videoWidth"    => $vw,
                    "videoHeight"   => $vh

                );
                if (!empty($result[$i]['duration']))
                    $mediaFilesArray[$i]["duration"] = $result[$i]['duration'];
                if (!empty($result[$i]['filesize']))
                    $mediaFilesArray[$i]["filesize"] = $result[$i]['filesize'];

                //}
            }
            $returnArray = array(
                'type'=>'success',
                'message' => 'OKSA mediafiles loaded!',
                'result' => $mediaFilesArray
            );
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
?>