<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once  ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');


if (isset($_POST['form']) && !empty($_POST['form'])) {
    //save new slide
    if (isset($_POST['new']) && !empty($_POST['new']['result'])) {
        $formArray = createArrayFromPostNV();

        $formArray['createdDate'] = date("Y-m-d H:i:s", time());
        $formArray['slideshow_id'] = $formArray['id'];

        $slideShowId = (int)$formArray['id'];

        if (isset($formArray['id'])) unset($formArray['id']);
        if (isset($formArray['name'])) unset($formArray['name']);
        if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);

        //set base slide data
        $inside_id = MySQL::filter($_POST['new']['result'][0]['id']);
        $slideArray = $_POST['new']['result'][0];

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
        $slideSlidesObject->setDBField('htmlForSlideshow', $array_of_values['htmlForSlideshow']);
        $slideSlidesObject->setDBField('templateType', $array_of_values['templateType']);
        $slideSlidesObject->save();

        $result = false;

        if ($slideSlidesObject->getId() > 0) {
            $result = true;
            $insertID = $slideSlidesObject->getId();
        }

        if ($result)
            $slidesArray = array('result' => 'success', 'id' => $insertID, 'message' => 'Slide successfully saved');
        else
            $slidesArray = array('error' => 'Slide cant be saved');

        SlideSlides::toArray($_POST['toArray']);

        printSortResult($slidesArray);
    }
    /////////////////////////////////////////////////////////////////////////////////
    //update slide
    if (isset($_POST['update']) && !empty($_POST['update']['result'])) {
        $formArray = array();
        for ($i = 0; $i < count($_POST['form']); $i++) {
            $formArray[$_POST['form'][$i]['name']] = $_POST['form'][$i]['value'];
        }

        $slideShowId = (int)$formArray['id'];

        $formArray['slides_id'] = MySQL::filter($_POST['update']['result'][0]['id']);
        if (isset($formArray['id'])) unset($formArray['id']);
        if (isset($formArray['name'])) unset($formArray['name']);
        if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);

        unset($_POST['update']['result'][0]['id']);

        $slideArray = $_POST['update']['result'][0];

        $htStr = htmlentities(($slideArray['html']), ENT_QUOTES | ENT_IGNORE, 'UTF-8');
        $slideArray['html'] = $htStr;

        $htStr = htmlentities(($slideArray['html2']), ENT_QUOTES | ENT_IGNORE, 'UTF-8');
        $slideArray['htmlForSlideshow'] = $htStr;
        unset($slideArray['html2']);

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
        $slideSlidesObject->setDBField('htmlForSlideshow', $slideArray['htmlForSlideshow']);
        $slideSlidesObject->setDBField('templateType', $slideArray['templateType']);

        $result = $slideSlidesObject->save();

        if ($result == true)
            $slidesArray = array('result' => 'success', 'message' => 'Succesfully updated');
        else
            $slidesArray = array('error' => 'Slide cant be updated');

        SlideSlides::toArray($_POST['toArray']);

        printSortResult($slidesArray);
    }
/////////////////////////////////////////////////////////////////////////////////
    //delete slide
    if (isset($_POST['delete']) && !empty($_POST['delete'])) {
        $formArray = createArrayFromPostNV();

        $slideShowId = $_POST['delete'];

        /*
        $formArray['slides_id'] = MySQL::filter($_POST['delete']);
        if (isset($formArray['id'])) unset($formArray['id']);
        if (isset($formArray['name'])) unset($formArray['name']);
        if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);
        unset($_POST['delete']);
        */

        $slideSlidesObject = new SlideSlides($slideShowId);

        $result = $slideSlidesObject->remove();

        if ($result == true)
            $slidesArray = array('result' => 'success', 'message' => 'Succesfully deleted');
        else
            $slidesArray = array('error' => 'Slide cant be deleted');

        SlideSlides::toArray($_POST['toArray']);

        printSortResult($slidesArray);
    }

/////////////////////////////////////////////////////////////////////////////////
    //sort slide
    if (isset($_POST['sort'])) {
        $formArray = createArrayFromPostNV();

        if (isset($formArray['id'])) unset($formArray['id']);
        if (isset($formArray['name'])) unset($formArray['name']);
        if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);

        $idArray = $_POST['sort'];

        if (SlideSlides::toArray($_POST['toArray']))
            $slidesArray = array('result' => 'success', 'message' => 'Succesfully updated slide order');
        else
            $slidesArray = array('error' => 'Slide order cant be updated');

        printSortResult($slidesArray);
    }

} else {
    printSortResult();
}
?>