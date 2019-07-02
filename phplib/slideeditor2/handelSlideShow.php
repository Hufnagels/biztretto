<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

$returnData = array();
if (isset($_POST['form']) && !empty($_POST['form'])) {
    $formArray = createArrayFromPostNV();
    switch (getRequest('action'))
    {
        case 'list':
            $additional = '';
            if (isset($_POST['diskArea']) && $_POST['diskArea'] !== '') {
                $additional = "AND ss.diskArea_id = " . MySQL::filter($_POST['diskArea']);
            }

            $sql = "
                SELECT
                    ss.slideshow_id, ss.name, ss.description, COUNT(sa.slides_id) AS darab,
                    IF ((SELECT slideshow_id FROM training_slideshow 
                      INNER JOIN training_training 
                      ON training_training.training_id = training_slideshow.training_id
                      WHERE training_training.activeState <> 'draft' AND training_slideshow.slideshow_id=ss.slideshow_id LIMIT 1)>0,1,0) AS readonly
                  FROM slide_slideshow ss
                  LEFT JOIN slide_slides sa
                    ON ss.slideshow_id = sa.slideshow_id
                    WHERE ss.office_id = " . MySQL::filter($_SESSION['office_id']) . " AND
                        ss.office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'
                        AND ss.isEnabled = 1 " . $additional . "
                        GROUP BY ss.slideshow_id
                        ORDER BY ss.name ASC";

            $query = MySQL::query($sql, false, false);
            $slideShowArray = array();
            if(count($query)){
                foreach ($query as $row) {
                    $slideShowArray[] = array(
                        'id' => $row['readonly'] ? '' : $row['slideshow_id'],
                        'name' => $row['name'],
                        'readonly' => $row['readonly'],
                        'doname' => str_replace(' ', '', strtolower($row['name'])),
                        'count' => ($row['darab'] == NULL ? 0 : $row['darab']),
                        'type' => 'normal',
                        'description' => ($row['description'] == NULL ? '' : $row['description'])
                    );
                }
                $returnData = array('type'=>'success',
                                    'message'=>'slideshowlist loaded',
                                    'result' => $slideShowArray);
            } else
                $returnData = array('type'=>'warning',
                                    'message'=>'no slideshows');

            break;

        case 'load':
            $showid = MySQL::filter($_POST['id']);
            $sql = "
                SELECT * FROM slide_slides
                WHERE slideshow_id = ".$showid." AND
                    office_id = " . MySQL::filter($_SESSION['office_id']) . " AND
                    office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'
                    ORDER BY badge";

            $query = MySQL::query($sql, false, false);
            $slideShowArray = array();
//printR($query);
            if (!empty($query)) {
                foreach ($query as $row) {

                    $slideShowArray[] = array(
                        'id' => $row['slides_id'],
                        'type' => $row['type'],
                        'templateType' => $row['templateType'],
                        'html' => htmlspecialchars_decode($row['html']), //stripslashes($row['html']),//base64_decode($row['html']),
                        'tag' => $row['tag'],
                        'badge' => $row['badge'],
                        'error' => $row['missingContent'],
                        'slideLevel' => $row['slideLevel'],
                        'description' => htmlspecialchars_decode($row['description']),
                        'templateOption' => ($row['templateOption'] == NULL ? NULL : json_decode($row['templateOption'], true)),
                        'background' => $row['background'] == NULL ? '' : $row['background']
                    );
                };

                $returnData = array('type'=>'success',
                                    'message'=>'slideshowlist loaded',
                                    'result' => $slideShowArray);

            } else
                $returnData = array('type'=>'warning',
                                    'message'=>'No slides yet',
                                    'result' => $slideShowArray);

            break;

        case 'new':
            $formArray['createdDate'] = date("Y-m-d H:i:s", time());
            if (isset($formArray['id'])) unset($formArray['id']);
            if (isset($formArray['name'])) unset($formArray['name']);
            if (isset($formArray['diskArea'])) unset($formArray['diskArea']);

            $name = MySQL::filter(purifyString($_POST['data'][0]['name']));
            $description = MySQL::filter(purifyString($_POST['data'][0]['description']));
            $slideShowArray = array('name' => $name, 'description' => $description, 'diskArea_id'=>$_POST['data'][0]['diskArea']);

            $array_of_values = array_merge($formArray, $slideShowArray);
//printR($_POST);
//printR($array_of_values);
//exit;
            $insertID = MySQL::insert('slide_slideshow', $array_of_values);
            if (is_numeric($insertID))
            {
                $diskAreaId = (int)$array_of_values['diskArea_id'];
                /*
                 * TODO
                 */
                if ($diskAreaId<0)
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
                $returnData = array('result' => array(array('name' => $_POST['data'][0]['name'],
                                                      'id' => $insertID,
                                                      'description' => $description,
                                                      'count' => 0))
                );
            }
            else
                $returnData = array('type'=>'error',
                                    'message'=> 'Slideshow save was unsuccessfull!');
            break;

        case 'rename':
            $slideShowId = MySQL::filter($formArray['id']);
            $name = MySQL::filter($_POST['value']);
            $sortName = str_replace(' ', '', normalize_special_characters(strtolower($name)));

            $slideSlideShowObject = new SlideSlideShow($slideShowId);
            $slideSlideShowObject->setDBField('name',$name);
            $result = $slideSlideShowObject->save();
//printR($result);
            if ($result)
                $returnData = array('type'=> 'success',
                                    'message' => 'Successfully deleted!');
            else
                $returnData = array('type'=>'error',
                                    'message' => 'Slideshow rename was unsuccessfull!');
            break;
        case 'delete':
            $slideShowId = MySQL::filter($_POST['id']);
            $slideSlideShowObject = new SlideSlideShow($slideShowId);
            //$slideSlideShowObject->setDBField('name',$name);
            $result = $slideSlideShowObject->remove();

            if ($result)
                $returnData = array('type'=> 'success',
                                    'message' => 'Slideshow successfully deleted!');
            else
                $returnData = array('type'=>'error',
                                    'message' => 'Slideshow delete was unsuccessfull!');
            break;
        case 'duplicate':
            $returnData = array('type'=>'error',
                                'message' => 'not implemented!');
            break;
    }
} else {
    $returnData = array('type'=>'error',
                        'message' => 'Slideshow can\'t be loaded!');

}
$_SESSION['LAST_ACTIVITY'] = time();
printSortResult($returnData);
exit;
?>