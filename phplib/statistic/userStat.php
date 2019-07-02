<?php
    /*
    function recursive_array_search($needle,$haystack) {
        foreach($haystack as $key=>$value) {
            $current_key=$key;
            if($needle===$value OR (is_array($value) && recursive_array_search($needle,$value))) {
                return $current_key;
            }
        }
        return false;
    }
*/

    $formData = createArrayFromPostNV('form');

    $userid = getRequest('userid');

	$userid = $_SESSION['u_id'];
	
    $sql = "SELECT traininggroup_id FROM user_traininggroupusers WHERE u_id = ".$userid;

    $rows = MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc);

    $usersArray = array();

    $main = $instanceResults = array();

    foreach($rows as $row){

        $trainingGroupId = $row['traininggroup_id'];

        $trainingInstances = TrainingTraining::getInstanceObjectsByTrainingGroupId($trainingGroupId);

        foreach ($trainingInstances as $trainingInstance)
        {
            /**
             * @var $trainingInstance TrainingTraining
             */
			 
            $masterTrainingObject = new TrainingTraining($trainingInstance->getDBField('parent_id'));

            $main[] = array(
                'id'        => $trainingInstance->getId(),
                'title'     => $masterTrainingObject->getDBField('title'),
                'authors'   => $masterTrainingObject->getDBField('authors')
            );

            $stat = Statistics::getStatByMasterTraining($masterTrainingObject,$trainingInstance);

            foreach($stat['trainingrates'] as $rate => $trainingrates){
                foreach($trainingrates as $u_id  => $v)
                    if($v['u_id'] != $userid)
                        unset($stat['trainingrates'][$rate][$u_id]);

                $stat['trainingrates'][$rate] = array_values($stat['trainingrates'][$rate]);
            }
//printR($_SESSION);
            $examResult = '';
            foreach($stat['trainingrates'] as $k => $v)
                $examResult = $k;
//printR($stat['trainingrates']);

            $stat1 = Statistics::getStatByUser( $userid, array('training_id'=>$trainingInstance->getId() ) );
//printR($stat1);

            $tempArray =
                array(
                    'avg_exan_result'=> $stat['avarageexamresult'],
                    //'ratings'=>array('sum'=>array('avg'=>4,'cnt'=>137),5=>82,4=>35,3=>10,2=>7,1=>3),
                    'trainingrates'=> $examResult,
					//array(
                    //    1 => array('result' => (array)$stat['trainingrates'][1]),
                    //    2 => array('result' => (array)$stat['trainingrates'][2]),
                    //    3 => array('result' => (array)$stat['trainingrates'][3]),
                    //    4 => array('result' => (array)$stat['trainingrates'][4]),
                    //    5 => array('result' => (array)$stat['trainingrates'][5])
                    //),
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
                    'sumcredit' => $stat1[$trainingInstance->getId()]['data']['meta']['sumcredit']
                );

            foreach($stat['traininguser'] as $k  => $v)
                if($v['u_id'] != $userid)
                    unset($stat['traininguser'][$k]);
            $stat['traininguser'] = array_values($stat['traininguser']);
            $tempArray['traininguser'] = array('result' => $stat['traininguser']);

            $tempArray['in_progress'] = 0;
            foreach($stat['inprogressuser'] as $k )
                if($k['u_id'] == $userid)
                    $tempArray['in_progress'] = 1;

            foreach($stat['inprogressuser'] as $k  => $v)
                if($v['u_id'] != $userid)
                    unset($stat['inprogressuser'][$k]);
            $stat['inprogressuser'] = array_values($stat['inprogressuser']);
            $tempArray['in_progressuser'] = array('result' => $stat['inprogressuser']);

            $tempArray['finished'] = 0;
            foreach($stat['finisheduser'] as $k )
                if($k['u_id'] == $userid)
                    $tempArray['finished'] = 1;

            foreach($stat['finisheduser'] as $k  => $v)
                if($v['u_id'] != $userid)
                    unset($stat['finisheduser'][$k]);
            $stat['finisheduser'] = array_values($stat['finisheduser']);
            $tempArray['finisheduser'] = array('result' => $stat['finisheduser']);

            $instanceResults[$trainingInstance->getId()] = $tempArray;
/**/			

//exit;
        }
//exit;
    }
//printR( $userid );
//exit;
//$_SESSION['u_id'] = $userid;
    if (!count($main))
        $returnData = array(
            'message' => 'User has no trainings to solve',
            'type'    => 'warning'
        );
    else

        $returnData = array(
            'message' => 'Trainings loaded',
            'type'    => 'success',
            'main'    => $main,
            'result'  => $instanceResults
        );

    printSortResult($returnData);
?>