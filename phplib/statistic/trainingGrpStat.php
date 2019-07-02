<?php

$formData = createArrayFromPostNV('form');

$trainingGroupId = getRequest('groupid');

$main = $instanceResults = array();

$trainingInstances = TrainingTraining::getInstanceObjectsByTrainingGroupId($trainingGroupId);

foreach ($trainingInstances as $trainingInstance)
{
    /**
     * @var $trainingInstance TrainingTraining
     */
    $masterTrainingObject = new TrainingTraining($trainingInstance->getDBField('parent_id'));

    $main[] = array(
        'id'        => $masterTrainingObject->getId(),
        'title'     => $masterTrainingObject->getDBField('title'),
        'authors'   => $masterTrainingObject->getDBField('authors')
    );

    $stat = Statistics::getStatByMasterTraining($masterTrainingObject,$trainingInstance);

    $instanceResults[$trainingInstance->getId()] =
        array(
            'avg_exan_result'=> $stat['avarageexamresult'],
            //'ratings'=>array('sum'=>array('avg'=>4,'cnt'=>137),5=>82,4=>35,3=>10,2=>7,1=>3),
            'trainingrates'=>array(
                1 => array('result' => (array)$stat['trainingrates'][1]),
                2 => array('result' => (array)$stat['trainingrates'][2]),
                3 => array('result' => (array)$stat['trainingrates'][3]),
                4 => array('result' => (array)$stat['trainingrates'][4]),
                5 => array('result' => (array)$stat['trainingrates'][5])
            ),
            'title'     => $masterTrainingObject->getDBField('title'),
            'traininguser'=>array('result' => $stat['traininguser']),
            'total_users'=>count($stat['totaluser']),
            'in_progress'=>count($stat['inprogress']),
            'in_progressuser'=>array('result' => $stat['inprogressuser']),
            'finished'=>count($stat['finished']),
            'finisheduser'=>array('result' => $stat['finisheduser']),
            'successful_exam'=>count($stat['trainingrates'][2])+count($stat['trainingrates'][3])+count($stat['trainingrates'][4])+count($stat['trainingrates'][5]), //users count( 2-5 )
            'failed_exam'=>count($stat['trainingrates'][1]), //users count( 1 )
            'attachments'=>$stat['attachments'],
            'created' => $stat['created'],
            'lastmod' => $stat['updated'],
        );

}

    if (!count($main))
        $returnData = array(
            'message' => 'This group has no trainings to solve',
            'type'    => 'warning'
        );
    else

        $returnData = array(
            'message' => 'Trainings loaded, select from list',
            'type'    => 'success',
            'main'    => $main,
            'result'  => $instanceResults
        );

printSortResult($returnData);
?>