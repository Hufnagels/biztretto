<?php

$formData = createArrayFromPostNV('form');

$masterTrainingId = getRequest('trainingId');

$stat = Statistics::getStatByMasterTraining($masterTrainingObject = new TrainingTraining($masterTrainingId));

$returnData = array(
    'message'=>'training loaded',
    'type'=>'success',
    'main'=>array(
        array(
            'id'        => $masterTrainingObject->getId(),
            'title'     => $masterTrainingObject->getDBField('title'),
            'authors'   => $masterTrainingObject->getDBField('authors')
        )
    ),
    'result'=>array(
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
        ))


);

printSortResult($returnData);
?>