<?php
$sqlTest = "
    SELECT point AS result, training_id AS id
    FROM training_slideshow_score
    WHERE u_id = ".$_SESSION['u_id']." AND archive = 0 AND testtype = 2 AND point > 0
";
$res = MySQL::query($sqlTest, false, false);
foreach($res as $row){
    $training = new TrainingTraining($row['id']);
    echo'
    <li class="mbcholder span2 lightgreyB">
        <div class="mbHeader ">
            <div class="name test">'.$training->getDBField('title').'</div>
            <div class="clearfix"></div>
            <h2>'.$row['result'].' point</h2>
        </div>
    </li>
    ';
}

//$testPoint = empty($res[0]['result']) ? '0' : $res[0]['result'];
//$training = new TrainingTraining(21);
?>
