<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

    $returnData = array();

    switch ($_POST['action'])
    {
        case 'load':

            if (((int)$trainingId = getRequest('trainingId'))>0)
            {
                $trainingTrainingObject = new TrainingTraining($trainingId);
                $main = array(
                    'id'                =>  $trainingTrainingObject->getDBField('training_id'),
                    'name'              =>  $trainingTrainingObject->getDBField('title'),
                    'description'       =>  $trainingTrainingObject->getDBField('description'),
                    'cover'             =>  $trainingTrainingObject->getDBField('cover'),
                    'authors'           =>  $trainingTrainingObject->getDBField('authors'),
                    'darab'             =>  $trainingTrainingObject->getSlideShowCount(),
                    'activeState'       =>  $trainingTrainingObject->getDBField('activeState')
                );

                $tree = TrainingSlideShow::toHierarchy($trainingTrainingObject->getId());

                $returnData = array('main'=>array($main),'result'=>$tree);
            }
            else
                $returnData = array('type'=>'error','message'=>'Training ID is empty!');

            printSortResult($returnData);
            break;

        case 'update':
            $dbtr = new DBTransaction();

            $detailsData = createArrayFromPostNV('details');
            $formData = createArrayFromPostNV('form');
            $isNew = !$formData['id'];
            $error = false;

            if ($isNew)
            {
                $trainingTrainingObject = new TrainingTraining(null);
                $trainingTrainingObject->setDBField('owner',$_SESSION['u_id']);
                $trainingTrainingObject->setDBField('office_id',$_SESSION['office_id']);
                $trainingTrainingObject->setDBField('office_nametag',$_SESSION['office_nametag']);
                $trainingTrainingObject->setDBField('parent_id',0);
                $trainingTrainingObject->setDBField('diskArea_id',$formData['diskArea_id']);
                $trainingTrainingObject->setDBField('activeState','draft');
            }
            else
                $trainingTrainingObject = new TrainingTraining($formData['id']);

            //common
            $trainingTrainingObject->setDBField('cover',$detailsData['cover']);
            $trainingTrainingObject->setDBField('title',purifyString($detailsData['name'],true));
            $trainingTrainingObject->setDBField('authors',(int)$detailsData['authors']);
            $trainingTrainingObject->setDBField('description',purifyString($detailsData['description']));

            if ($trainingTrainingObject->save())
            {
                foreach (TrainingTraining::getInstances($trainingTrainingObject) as $trainingInstance)
                {
                    /**
                     * @var $trainingInstance TrainingTraining
                     */
                    $trainingInstance->setDBField('cover',$detailsData['cover']);
                    $trainingInstance->setDBField('title',purifyString($detailsData['name'],true));
                    $trainingInstance->setDBField('authors',(int)$detailsData['authors']);
                    $trainingInstance->setDBField('description',purifyString($detailsData['description']));
                    if (!$trainingInstance->save()) $error = true;
                }


                $result = array(array(
                    'id'        =>  $trainingTrainingObject->getId(),
                    'title'     =>  $trainingTrainingObject->getDBField('title'),
                    'cover'     =>  $trainingTrainingObject->getDBField('cover'),
                    'authors'     =>  $trainingTrainingObject->getDBField('authors'),
                    'state'     =>  $trainingTrainingObject->getDBField('activeState'),
                    'status'    =>  $isNew?'new':'update'
                ));


                $returnData = array('type'=>'success','message'=>'Master training '.(($isNew)?'created':'updated').' successfully!','result'=>$result);
            }
            else
            {
                $error = true;
                $returnData = array('type'=>'error','message'=>'Training save failed!');
            }

            if (!$error) $dbtr->destroy();

            printSortResult($returnData);
            break;

        case 'list':
            if ($rows = MySQL::resultArray(MySQL::executeQuery('SELECT training_id AS id, title, cover, authors, activeState AS state FROM training_training WHERE office_id = "'.MySQL::filter($_SESSION['office_id']).'" AND office_nametag = "'.MySQL::filter($_SESSION['office_nametag']).'" AND parent_id = 0 AND activeState = "ready" ORDER BY title'),MySQL::fmAssoc))
                $returnData['result'] = $rows;
            else
                $returnData = array('type' => 'error', 'message' => 'There is no trainings');

            printSortResult($returnData);
            break;

        case 'updateStatus':
            $formData = createArrayFromPostNV('form');

            if (Access::getAccessLevel() >= 5)
            {
                $access = false;
                if (Access::getAccessLevel() == 5)
                {
                    if ($_POST['status'] == 'review' or $_POST['status'] == 'draft')
                        $access = true;
                }
                elseif (Access::getAccessLevel()>5)
                {
                    $access = true;
                }

                if ($access)
                {
                    $dbtr = new DBTransaction();                $error = false;

                    $trainingTrainingObject = new TrainingTraining($formData['id']);
                    $trainingTrainingObject->setDBField('activeState', MySQL::filter($_POST['status']));

                    if ($trainingTrainingObject->save())
                    {
                        foreach (TrainingTraining::getInstances($trainingTrainingObject) as $trainingInstance)
                        {
                            /**
                             * @var $trainingInstance TrainingTraining
                             */
                            $trainingInstance->setDBField('activeState', MySQL::filter($_POST['status']));
                            if (!$trainingInstance->save()) $error = true;
                        }
                    }
                    else
                        $error = true;

                    if (!$error)
                    {
                        $dbtr->destroy();

                        $returnData = array('type' => 'success', 'message' => 'Training status updated');
                    }
                    else
                        $returnData = array('type' => 'error', 'message' => 'Can\'t update Training status');

                }
                else
                    $returnData = array('type' => 'error', 'message' => 'Can\'t update Training status (access denied)');
            }
            else
            {
                $returnData = array('type' => 'error', 'message' => 'Can\'t update Training status (access denied)');
            }

            printSortResult($returnData);
            break;

        case 'deletemaster':


            $formData = createArrayFromPostNV('form');
            $trainingId = $formData['id'];
            if ($trainingId>0)
            {
                $trainingTrainingObject = new TrainingTraining($trainingId);
                if ($trainingTrainingObject->getDBField('parent_id')== 0 and $trainingTrainingObject->remove())
                {
                    $returnData = array('type' => 'success', 'message' => 'Master training remove success!!');
                }
                else
                {
                    $returnData = array('type' => 'error', 'message' => 'Master training remove failed!!');
                }
            }
            else
            {
                $returnData = array('type' => 'error', 'message' => 'Master training remove failed!!');
            }

            printSortResult($returnData);
            break;

        case 'delete':
        case 'toArray':
            $formData = createArrayFromPostNV('form');
            $testData = array();
            $trainingId = $formData['training_id'];

            if (is_array(getRequest('test')))
                foreach (getRequest('test') as $rowId=>$rowData)
                    foreach ($rowData as $_rowData)
                    {
                        if ($_rowData['name'])  $testData[$rowId][$_rowData['name']] = $_rowData['value'];
                    }

            if (((int)$trainingId = $formData['id'])>0)
            {
                $dbtr = new DBTransaction();

                $error = false; $sumCredit = 0; $masterTrainingSlideShowIds= array();
                if (TrainingSlideShow::toArray($_POST['toArray'], $trainingId,$_SESSION['office_id'],$_SESSION['office_nametag']) !== true)
                    $returnData = array('type' => 'error', 'message' => 'Can\'t save Training slideshows hierarchie');
                else
                {

                    if (($sumCredit = _saveHierarchyWrapper($testData,$trainingId)) === false)  $error = true;

                    if ($error)
                        $returnData = array('type' => 'error', 'message' => 'Training slideshows hierarchie save error!');
                    else
                    {
                        //sumcredit beallitasa a treningen, es az instanceokon is!

                        $trainingTrainingObject = new TrainingTraining($trainingId);
                        $trainingTrainingObject->setDBField('credit',(int)$sumCredit);

                        if ($trainingTrainingObject->save())
                        {
                            foreach (TrainingTraining::getInstances($trainingTrainingObject) as $trainingInstance)
                            {
                                /**
                                 * @var $trainingInstance TrainingTraining
                                 */
                                $trainingInstance->setDBField('credit',(int)$sumCredit);
                                if (!$trainingInstance->save()) $error = true;
                                else
                                {
                                    if (!TrainingSlideShow::toArray($_POST['toArray'], $trainingInstance->getId(),$_SESSION['office_id'],$_SESSION['office_nametag'])) $error = true;
                                    else
                                    {
                                        if (_saveHierarchyWrapper($testData,$trainingInstance->getId())===false) $error = true;
                                    }
                                }
                            }
                        }

                        if (!$error)
                        {
                            $dbtr->destroy();
                            $returnData = array('type' => 'success', 'message' => 'Training slideshows hierarchie saved!');
                        }
                        else
                        {
                            $returnData = array('type' => 'error', 'message' => 'Training save error!');
                        }
                    }
                }
            }
            else
                $returnData = array('type' => 'error', 'message' => 'Training id is empty!');

            printSortResult($returnData);
            break;
    }

    /**
     * @param $testData
     * @param $trainingId
     * @return bool|int
     */
    function _saveHierarchyWrapper($testData,$trainingId)
    {
        $error = false; $sumCredit = 0;

        foreach ($testData as $testDataRow)
        {
            $trainingSlideShowObject = TrainingSlideShow::getObjectByTrainingIdAndSlideShowId($trainingId,$testDataRow['id']);

            if ($testDataRow['type'])
            {
                $trainingSlideShowObject->setDBField('type',$testDataRow['type']);
                $trainingSlideShowObject->setDBField('testtype',$testDataRow['testtype']);
                $trainingSlideShowObject->setDBField('repetable',$testDataRow['repetable']);
                $trainingSlideShowObject->setDBField('credit',$sumCredit+=$testDataRow['credit']);
            }
            else
            {
                $trainingSlideShowObject->setDBField('type',0);
                $trainingSlideShowObject->setDBField('testtype',0);
                $trainingSlideShowObject->setDBField('repetable',$testDataRow['repetable']);
                $trainingSlideShowObject->setDBField('credit',0);
            }

            if (!$trainingSlideShowObject->save())
                $error = true;
        }

        if ($error) return false;
        return $sumCredit;
    }

    exit;

?>