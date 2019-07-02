<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

    if (isset($_POST['form']) && !empty($_POST['form'])) {
        $formArray = createArrayFromPostNV();
        //if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);
        //if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);

        switch ($_POST['action']){
            case 'load':
                $sql = "SELECT
                        mymedia_id AS id, mediabox_id AS boxid, name, type, mediatype, mediaurl, thumbnail_url, folder, uploaded, uploaded_ts, duration, filesize, size
                        FROM media_mymedia
                        WHERE office_id = '" . MySQL::filter($_SESSION['office_id']) . "'
                            AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'
                            AND diskArea_id = '" . MySQL::filter($_POST['diskArea']) . "'
                            ORDER BY name ASC";

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
                        'message' => 'Mediafiles loaded!',
                        'result' => $mediaFilesArray
                    );
                }
                break;

            case 'add':
                $imageURL = $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/';
                if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);
                switch ($_POST['type']){
                    case 'local':

                        $diskAreaName = $formArray['diskArea_name'];
                        $mediaFilesArray = $_POST['files'];

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

                            $mediaFilesArray[$i]['size'] = $mediaFilesArray[$i]['type'] == 'video' ? $mediaFilesArray[$i]['size'] : '';

                            $url_prefix = connectionType() . $imageURL; //.$diskAreaName.'/';
                            $temp = explode($url_prefix, $mediaFilesArray[$i]['mediaurl']);
                            $mediaFilesArray[$i]['mediaurl'] = $temp[1];
                            $temp = explode($url_prefix, $mediaFilesArray[$i]['thumbnail_url']);
                            $mediaFilesArray[$i]['thumbnail_url'] = $temp[1];
                            $array_of_values = array_merge($formArray, $mediaFilesArray[$i]);
                            $insertID = MySQL::insert('media_mymedia', $array_of_values);
                            $mediaFilesArray[$i]['id'] = $insertID;
                        }
//printR($mediaFilesArray);
                        $returnArray = array(
                            'type'    => 'success',
                            'message' => 'Selected files uploaded and saved successfully',
                            'result'  => $mediaFilesArray
                        );
                        break;

                    case 'remote':

                        $mediaFilesArray = $_POST['files'];
                        $formArray['createdDate'] = date("Y-m-d H:i:s", time());
                        $array_of_values = array_merge($formArray, $mediaFilesArray[0]);
                        $insertID = MySQL::insert('media_mymedia', $array_of_values);
                        $mediaFilesArray[0]['id'] = $insertID;
//printR($mediaFilesArray);
                        $returnArray = array(
                            'type'    => 'success',
                            'message' => 'Youtube media saved successfully',
                            'result'  => $mediaFilesArray
                        );

                        break;

                    default:
                        $returnArray = array(
                            'type'=>'error',
                            'message' => 'Misspelled data sent to server'
                        );
                        break;
                }

                break;

            case 'delete':

                $postResult = isset($_POST['slides']) ? $_POST['slides'] : array();
                $mediaFilesArray = $_POST['files'];
//if(!empty($postResult))
//printR($postResult);

                $db = count($mediaFilesArray);
                $delID = array();
                for ($i = 0; $i < $db; $i++) {
                    $delID[] = MySQL::filter($mediaFilesArray[$i]['id']);
                }

                $result = MediaMediaBoxFiles::removeFileByIds($delID);
//printR($delID);
//exit;
                //delete files and thumnbs if have phisically
                if ($result) {
                    //update slides state affected by missing files
                    //if is any
                    if (!empty($postResult)) {
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
                            $returnArray = array(
                                'type' => 'error',
                                'message' => 'Can\'t update slide status!');
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
                    $returnArray = array(
                        'type' => 'success',
                        'message' => 'Selected successfully deleted!',
                        'result' => $result );
                } else {
                    $returnArray = array(
                        'type' => 'error',
                        'message' => 'Can\'t delete from database!');

                }

                break;

            case 'addto':

//printR($_POST);
                $ids = array();
                foreach($_POST['files'] as $files)
                    $ids[] = MySQL::filter($files['id']);
                $sql = "
                    UPDATE media_mymedia
                    SET `mediabox_id` = ".MySQL::filter($_POST['groupid'])."
                    WHERE `mymedia_id` IN (".implode(',', $ids).")
                ";
//printR($sql);
                $result = MySQL::execute($sql);
//printR($result);
                if($result)
                    $returnArray = array(
                        'type'=>'success',
                        'message' => 'Successfully added to group!'
                    );
                else
                    $returnArray = array(
                        'type'=>'error',
                        'message' => 'Can\'t added to group!'
                    );
                break;

            case 'removefrom':
//printR($_POST);
                foreach($_POST['files'] as $files)
                    $ids[] = MySQL::filter($files['id']);
                $sql = "
                    UPDATE media_mymedia
                    SET `mediabox_id` = 0
                    WHERE `mymedia_id` IN (".implode(',', $ids).")
                ";
                $result = MySQL::execute($sql);
                if($result)
                    $returnArray = array(
                        'type'=>'success',
                        'message' => 'Successfully removed from group!'
                    );
                else
                    $returnArray = array(
                        'type'=>'error',
                        'message' => 'Can\'t removed from group!'
                    );
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
?>