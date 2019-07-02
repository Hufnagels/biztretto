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
                    mediabox_id, name
                    FROM media_mediabox
                    WHERE office_id = '" . MySQL::filter($_SESSION['office_id']) . "' AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' AND diskArea_id = '" . MySQL::filter($_POST['diskArea_id']) . "' ORDER BY name ASC";

                $result = MySQL::query($sql, false, false);

                if (!$result){
                    $returnArray = array(
                        'type'=>'error',
                        'message' => 'Can\'t load mediagroups'
                    );

                } else {
                    $db = count($result);
                    $mediaBoxesArray = array();

                    for ($j = 0; $j < $db; $j++) {
                        $nameTag = $result[$j]['name'];
                        $idTag = $result[$j]['mediabox_id'];
                        $doname = str_replace(' ', '', strtolower(normalize_special_characters($nameTag)));
                        $mediaBoxesArray[$j] = array(
                            "idTag" => $idTag,
                            "nameTag" => $nameTag,
                            "doname" => $doname,
                            "files" => array()
                        );
                    }
                    $returnArray = array(
                        'type'=>'success',
                        'message' => 'Mediagroups loaded!',
                        'result' => $mediaBoxesArray
                    );
                }


                break;
            case 'add':

                $formArray['createdDate'] = date("Y-m-d H:i:s", time());

                $mediaGroupName = MySQL::filter($_POST['groupname']);
                $mediaGroupArray = array('name' => $mediaGroupName);
                $array_of_values = array_merge($formArray, $mediaGroupArray);

                $mediaMediaBoxObject = new MediaMediaBox(null);
                $mediaMediaBoxObject->setDBField('office_id',$_SESSION['office_id']);
                $mediaMediaBoxObject->setDBField('office_nametag',$_SESSION['office_nametag']);
                $mediaMediaBoxObject->setDBField('owner',$_SESSION['u_id']);
                $mediaMediaBoxObject->setDBField('name',$array_of_values['name']);
                $mediaMediaBoxObject->setDBField('diskArea_id',$array_of_values['diskArea_id']);
                $mediaMediaBoxObject->save();
                $insertID = $mediaMediaBoxObject->getId();
                if ($insertID>0)
                    $returnArray = array(
                        'type'      => 'success',
                        'message'   => 'Mediabox created successfully!',
                        'result'    => array('id'  => $insertID)
                    );
                else
                    $returnArray = array(
                        'type'      => 'success',
                        'message'   => 'Mediabox can\'t be created!'
                    );


                break;

            case 'delete':

                $delID = MySQL::filter($_POST['id']);

                $mediaMediaBoxObject = new MediaMediaBox($delID);
                $result = $mediaMediaBoxObject->remove();

                if ($result)
                    $returnArray = array(
                        'type'      => 'success',
                        'message'   => 'Mediabox delete was successfully'
                    );
                else
                    $returnArray = array(
                        'type'      => 'error',
                        'message'   => 'Mediabox delete was unsuccessfull'
                    );

                break;

            case 'rename':
                $mediaGroupName = MySQL::filter($_POST['value']);
                $mediaGroupId = MySQL::filter($_POST['pk']);

                $sortname = normalize_special_characters(strtolower($mediaGroupName));
                $sortname = str_replace(' ', '', $sortname);

                $mediaGroupArray = array('mediabox_id' => $mediaGroupId);
                $array_of_conditions = array_merge($formArray, $mediaGroupArray);
                $whatToSet = array('name' => $mediaGroupName);

                $result = MySQL::update('media_mediabox', $whatToSet, $array_of_conditions);

                if ($result)
                    $returnArray = array(
                        'type'      => 'success',
                        'message'   => 'Mediabox rename was successfully'
                    );
                else
                    $returnArray = array(
                        'type'      => 'success',
                        'message'   => 'Mediabox rename was unsuccessfull!'
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