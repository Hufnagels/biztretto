<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

    $returnData = array();

    $formData = createArrayFromPostNV('form');

    switch ($_POST['action'])
    {
        case 'load':
            $instanceList = TrainingTraining::getInstances(new TrainingTraining($formData['id']));
            $first = false;
            $result=array();
            $timeout1=$timeout2=$wtimeout1=$wtimeout2=null;

            foreach ($instanceList as $instanceTrainingObject)
            {
                if (!$first)
                {
                    $first = true;
                    /**
                     * @var $instanceTrainingObject TrainingTraining
                     */
                    if ($slideShowObjects = TrainingSlideShow::getObjectsByTrainingId(($instanceTrainingObject->getId())))
                    {
                        $timeout1 = $slideShowObjects[0]->getDBField('timeout1');
                        $timeout2 = $slideShowObjects[0]->getDBField('timeout2');
                        $wtimeout1 = $slideShowObjects[0]->getDBField('wtimeout1');
                        $wtimeout2 = $slideShowObjects[0]->getDBField('wtimeout2');
                    }
                }


                $result[]=array(
                        'id'=>$instanceTrainingObject->getDBField('training_id'),
                        'group'=>$instanceTrainingObject->getDBField('traininggroups'),
                        'groupcount'=>$instanceTrainingObject->getDBField('traininggroups')?count(@explode(',',$instanceTrainingObject->getDBField('traininggroups'))):0,
                        'startDate'=>$instanceTrainingObject->getDBField('startDate'),
                        'endDate'=>$instanceTrainingObject->getDBField('endDate'),
                        'timeout1'=>$timeout1,
                        'timeout2'=>$timeout2,
                        'wtimeout1'=>$wtimeout1,
                        'wtimeout2'=>$wtimeout2
                );

            }
            $returnData = array('type'=>'success',
                                'message'=> 'Trainings loaded successfully',
                                'result' => $result
                                );

            printSortResult($returnData);

            break;

        case 'delete':
            $formData = createArrayFromPostNV('form');
            $trainingId = getRequest('id');

            if ($trainingId>0)
            {
                $trainingTrainingObject = new TrainingTraining($trainingId);

                if ($trainingTrainingObject->remove())
                {
                    $returnData = array('type'=>'success','message'=>'training remove success');
                }
                else
                {
                    $returnData = array('type'=>'success','message'=>'training remove error!');
                }
            }
            else
                $returnData = array('type'=>'success','message'=>'training id is empty!');

            printSortResult($returnData);

            break;
        case 'new':
            $formData = createArrayFromPostNV('form');
            $masterTrainingId = $formData['id'];

            if ($clone = TrainingTraining::createNewInstance(new TrainingTraining($masterTrainingId)))
            {
                $returnData = array('type'=>'success',
                    'message'=> 'New training created successfully',
                    'result' => array(
                        array(
                            'id' => $clone->getId(),
                            'group'     => '',
                            'users'     => '',
                            'startDate' => '',
                            'endDate'   => '',
                            'timeout1'  => '',
                            'timeout2'  => '',
                            'wtimeout1' => '',
                            'wtimeout2' => ''

                        )
                    )
                );
            }
            else
            {
                $returnData = array('type'=>'error','message'=> 'Publication not created!');
            }

            printSortResult($returnData);

            break;
        case 'changeData':

            $trainingId = getRequest('pk');

            if ($trainingId>0 and strlen($name=getRequest('name'))>0)
            {
                $dbtr = new DBTransaction();

                $trainingTrainingObject = new TrainingTraining($trainingId);
                $value = getRequest('value');

                if ($name == 'startDate')   $trainingTrainingObject->setDBField('startDate',$value?$value:null);
                if ($name == 'endDate')     $trainingTrainingObject->setDBField('endDate',$value?$value:null);
                if ($name == 'traininggroups')     $trainingTrainingObject->setDBField('traininggroups',$value);

                $error = false;

                if ($trainingTrainingObject->save())
                {
                    foreach (TrainingSlideShow::getObjectsByTrainingId($trainingTrainingObject->getId()) as $slideShowObject)
                    {
                        if ($name == 'startDate')   $slideShowObject->setDBField('startDate',$value?$value:null);
                        if ($name == 'endDate')     $slideShowObject->setDBField('endDate',$value?$value:null);
                        if ($name == 'timeout1')    $slideShowObject->setDBField('timeout1',$value?$value:null);
                        if ($name == 'timeout2')    $slideShowObject->setDBField('timeout2',$value?$value:null);
                        if ($name == 'wtimeout1')   $slideShowObject->setDBField('wtimeout1',$value?$value:null);
                        if ($name == 'wtimeout2')   $slideShowObject->setDBField('wtimeout2',$value?$value:null);
                        if (!$slideShowObject->save())  $error = true;
                    }

                    if (!$error)
                    {
                        $dbtr->destroy(); //commit

                        $returnData = array('type'=>'success','message'=>'Training saveed successfully!');
                    }
                    else
                    {
                        $returnData = array('type'=>'error','message'=>'Training save failed!');
                    }
                }
                else
                    $returnData = array('type'=>'error','message'=>'Training save failed!');
            }
            else
                $returnData = array('type'=>'error','message'=>'Training save failed!');

            printSortResult($returnData);
         break;
    }

    exit;
?>