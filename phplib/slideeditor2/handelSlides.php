<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once  ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');


if (isset($_POST['form']) && !empty($_POST['form'])) {
    $formArray = createArrayFromPostNV();
    switch (getRequest('action'))
    {
        case 'new':
            $formArray['createdDate'] = date("Y-m-d H:i:s", time());
            $formArray['slideshow_id'] = $formArray['id'];

            $slideShowId = (int)$formArray['id'];

            if (isset($formArray['id'])) unset($formArray['id']);
            if (isset($formArray['name'])) unset($formArray['name']);
            if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);

            //set base slide data
            $inside_id = MySQL::filter($_POST['data'][0]['id']);
            $slideArray = $_POST['data'][0];

            $htStr = htmlentities(($slideArray['html']), ENT_QUOTES | ENT_IGNORE, 'UTF-8');
            $slideArray['html'] = $htStr;

            $htStr = htmlentities(($slideArray['html2']), ENT_QUOTES | ENT_IGNORE, 'UTF-8');
            $slideArray['htmlForSlideshow'] = $htStr;
            unset($slideArray['html2']);

            $htStr2 = htmlentities(purifyString($slideArray['description']), ENT_QUOTES | ENT_IGNORE, 'UTF-8');
            $slideArray['description'] = $htStr2;

            $slideSlidesObject = new SlideSlides(null);

            //check if slide is template
            if ($slideArray['type'] == 'template') { // && !empty($slideArray['templateOption'])){
                $tA = array();
                foreach ($slideArray['templateOption'] as $row)
                    $tA[] = $row;
                $slideArray['templateOption'] = json_encode($tA);
                $slideSlidesObject->setDBField('templateOption', json_encode($tA));
            }

            $array_of_values = array_merge($formArray, $slideArray);
//printR($array_of_values);
            //exit;

            $insertID = '';

            $slideSlidesObject->setDBField('office_id', $_SESSION['office_id']);
            $slideSlidesObject->setDBField('office_nametag', $_SESSION['office_nametag']);
            $slideSlidesObject->setDBField('owner', $_SESSION['u_id']);
            $slideSlidesObject->setDBField('slideshow_id', $array_of_values['slideshow_id']);
            $slideSlidesObject->setDBField('id', $array_of_values['id']);
            $slideSlidesObject->setDBField('html', $array_of_values['html']);
            $slideSlidesObject->setDBField('slideLevel', $array_of_values['slideLevel']);
            $slideSlidesObject->setDBField('tag', $array_of_values['tag']);
            $slideSlidesObject->setDBField('badge', $array_of_values['badge']);
            $slideSlidesObject->setDBField('type', $array_of_values['type']);
            $slideSlidesObject->setDBField('description', $array_of_values['description']);
            $slideSlidesObject->setDBField('htmlForSlideshow', $array_of_values['html2']);
            $slideSlidesObject->setDBField('templateType', $array_of_values['templateType']);
            $slideSlidesObject->setDBField('background', $array_of_values['background']);
            $slideSlidesObject->save();

            $result = false;

            if ($slideSlidesObject->getId() > 0) {
                $result = true;
                $insertID = $slideSlidesObject->getId();
            }
//printR($slideSlidesObject->getId());
            if ($result){
                $sql = "
                SELECT * FROM slide_slides
                WHERE slides_id = ".$insertID." AND
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
                }
                $slidesArray = array('type' => 'success',
                                     'message' => 'Slide successfully saved',
                                     'id' => $insertID,
                                     'result'=> $slideShowArray);
            } else
                $slidesArray = array('type'=>'error',
                                     'message' => 'Slide cant be saved');

            SlideSlides::toArray($_POST['toArray']);
            break;

        case 'update':
            $slideShowId = (int)$formArray['id'];

            $formArray['slides_id'] = MySQL::filter($_POST['data'][0]['id']);
            if (isset($formArray['id'])) unset($formArray['id']);
            if (isset($formArray['name'])) unset($formArray['name']);
            if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);

            unset($_POST['data'][0]['id']);

            $slideArray = $_POST['data'][0];

            $htStr = htmlentities(($slideArray['html']), ENT_QUOTES | ENT_IGNORE, 'UTF-8');
            $slideArray['html'] = $htStr;

            $htStr = htmlentities(($slideArray['html2']), ENT_QUOTES | ENT_IGNORE, 'UTF-8');
            $slideArray['html2'] = $htStr;
            //unset($slideArray['html2']);

            $htStr2 = htmlentities(purifyString($slideArray['description']), ENT_QUOTES | ENT_IGNORE, 'UTF-8');
            $slideArray['description'] = $htStr2;

            $slideSlidesObject = new SlideSlides($formArray['slides_id']);

            if ($slideArray['type'] == 'template') { // && !empty($slideArray['templateOption'])){
                $tA = array();
                $tA = $slideArray['templateOption'];

                $slideArray['templateOption'] = json_encode($tA);
                $slideSlidesObject->setDBField('templateOption', json_encode($tA));
            }

            $slideSlidesObject->setDBField('type', $slideArray['type']);
            $slideSlidesObject->setDBField('badge', $slideArray['badge']);
            $slideSlidesObject->setDBField('tag', $slideArray['tag']);
            $slideSlidesObject->setDBField('html', $slideArray['html']);
            $slideSlidesObject->setDBField('answare', $slideArray['answare']);
            $slideSlidesObject->setDBField('description', $slideArray['description']);
            $slideSlidesObject->setDBField('htmlForSlideshow', $slideArray['html2']);
            $slideSlidesObject->setDBField('templateType', $slideArray['templateType']);
            $slideSlidesObject->setDBField('background', $slideArray['background']);

            $result = $slideSlidesObject->save();

            if ($result == true)
                $slidesArray = array('type' => 'success',
                                     'message' => 'Succesfully updated');
            else
                $slidesArray = array('type'=>'error',
                                     'message' => 'Slide cant be updated');

            SlideSlides::toArray($_POST['toArray']);
            break;

        case 'delete':
            $slideId = MySQL::filter($_POST['data']);
//printR($_POST); exit;
            /*
            $formArray['slides_id'] = MySQL::filter($_POST['delete']);
            if (isset($formArray['id'])) unset($formArray['id']);
            if (isset($formArray['name'])) unset($formArray['name']);
            if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);
            unset($_POST['delete']);
            */

            $slideSlidesObject = new SlideSlides($slideId);

            $result = $slideSlidesObject->remove();

            if ($result == true)
                $slidesArray = array('type' => 'success',
                                     'message' => 'Succesfully deleted');
            else
                $slidesArray = array('type'=>'error',
                                     'message' => 'Slide cant be deleted');

            SlideSlides::toArray($_POST['toArray']);
            break;

        case 'sort':
            if (isset($formArray['id'])) unset($formArray['id']);
            if (isset($formArray['name'])) unset($formArray['name']);
            if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);

            $idArray = $_POST['data'];

            if (SlideSlides::toArray($_POST['toArray']))
                $slidesArray = array('type' => 'success',
                                     'message' => 'Succesfully updated slide order');
            else
                $slidesArray = array('type'=>'error',
                                     'message' => 'Slide order cant be updated');
            break;
    }
    $_SESSION['LAST_ACTIVITY'] = time();

} else {
    $slidesArray = array('type'=>'error',
                         'message' => 'Slide order cant be updated');
}
    printSortResult($slidesArray);
exit;
?>