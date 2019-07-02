<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

$protocol = connectionType();
$imageURL = $protocol . DOMAINTAG;

$ts = MySQL::filter($_POST['ts']);
$userId = (int)$_SESSION['u_id'];
$trainingId = (int)$_POST['training'];
$slideShowId = (int)$_POST['slideshow'];
$slideId = (int)$_POST['slide'];
$officeId = (int)$_SESSION['office_id'];
$officeNameTag = MySQL::filter($_SESSION['office_nametag']);


$trainingType = 'training';

if(!isset($_SESSION['office_nametag'])){
	preg_match('/([^.]+)\\' . DOMAINTAG_PREGSTRING . '/', $_SERVER['SERVER_NAME'], $matches);
	$userId = 1;
	$officeId = 1;
	$officeNameTag = $matches[1];
	$trainingType = 'public';
	$formArray0 = array();
	$formArray0['database'] = DB_PREFIX.$matches[1];
	MySQL::connect(DB_HOST, DB_USER, DB_PASS, ((isset($formArray0['database'])) and $formArray0['database']) ? $formArray0['database'] : null);
}

$templateType = MySQL::filter($_POST['template']);
$lft = (int)$_POST['lft'];
$badge = (int)$_POST['badge'];
$token = MySQL::filter($_POST['auth_token']);

$trainingSlideshow = TrainingSlideShow::getObjectByTrainingIdAndSlideShowId($trainingId, $slideShowId);

$trainingSlideshowIsTest = $trainingSlideshow->getDBField('type');

//create formArray
$formArray = array();
$formArray['office_nametag'] = $officeNameTag;
$formArray['slideshow_id'] = $slideShowId;
$formArray['templateType'] = $templateType;
$formArray['lft'] = $lft;

//////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////
//select the affected slides data from db
///////////////////////////////////////////////////////////////////
$options = array(
    'table' => 'slide_slides',
    'fields' => 'slides_id, slideLevel, templateOption, answare',
    'condition' => array(
        'slideshow_id' => $slideShowId,
        'office_id' => $officeId,
        'office_nametag' => $officeNameTag,
        'badge' => $badge
    ),
    'conditionExtra' => '',
    'order' => 'badge',
    'limit' => 1
);
$result = MySQL::select($options);

///////////////////////////////////////////////////////////////////
// iranyitas adatai, melyikre ugorjon a pontszam fuggvenyeben
///////////////////////////////////////////////////////////////////
$optionArray = $answareArray = json_decode($result[0]['templateOption'], true);
/*
print '$optionArray';
printR($result);
*/
///////////////////////////////////////////////////////////////////
//template szerinti helyes valasz
///////////////////////////////////////////////////////////////////
$answareArray = json_decode($result[0]['answare'], true);

$rightAnsware = array();
$sendedAnsware = array();

///////////////////////////////////////////////////////////////////
//az elerhető maximális pontszam mindig 100% az adott tesztnel
///////////////////////////////////////////////////////////////////
$maxpoint = 100 * $result[0]['slideLevel'];
/*
print '$result';
printR($result);
print 'post';
printR($_POST);
*/
$totalPoint = $totalPoint2 = $totalPoint3 = $pointPerAnsware = 0;
///////////////////////////////////////////////////////////////////
//teszt tipusonkent mas és mas a helyes valasz
//és a kuldott valasz
//itt dolgozzuk fel a kuldott valaszt
//összehasonlitassal
///////////////////////////////////////////////////////////////////

switch ($formArray['templateType']) {
    ///////////////////////////////////////////////////////////////////
    case 'radio':

        foreach ($answareArray as $row)
            $rightAnsware[] = $row['value'];
        //pontszám jó válaszért
        $goodAnsware = count($rightAnsware);
        $pointPerAnsware = $maxpoint / count($rightAnsware);
        //kuldott válaszok
        $sendedAnsware = isset($_POST['options']) ? $_POST['options'] : array();

        $dbAnswerFieldValue = $_POST['options'];

        $res = array_intersect($rightAnsware, $sendedAnsware);
        $totalPoint = $pointPerAnsware * count($res);
print'good - radio';
printR($rightAnsware);
print'sended - radio';
printR($sendedAnsware);
        break;
    ///////////////////////////////////////////////////////////////////
    case 'check':
        //jo valaszok
        foreach ($answareArray as $row)
            $rightAnsware[] = $row['value'];
        //pontszám jó válaszért
        $goodAnsware = count($rightAnsware);
        $pointPerAnsware = $maxpoint / count($rightAnsware);
        //kuldott válaszok
        $sendedAnsware = isset($_POST['chk']) ? $_POST['chk'] : array();//$_POST['chk'];

        $dbAnswerFieldValue = $_POST['chk'];

        //találatok meghatározása
        $res = array_intersect($rightAnsware, $sendedAnsware);
        //ennyit talált el
        $p = count($res);
        //ellenorizzuk, hogy nem lutrizik-e
        $points = (intval($goodAnsware) < intval($p)) ? $p + ($p - $goodAnsware) : $p;
        if ($points < 0) $points = 0;
        //ennyi pontja lett
        $totalPoint = $pointPerAnsware * $points;
//kuldott válaszok
print'good - check';
printR($rightAnsware);
print'sended - check';
printR($sendedAnsware);
        break;
    ///////////////////////////////////////////////////////////////////
    case 'input':
        break;

    case 'sorting':
    case 'pairing':
        //leszedjuk a []-oket
        $tempOrderorder = str_replace('sort[]=', '', $answareArray[0]['order']);
        $rightAnsware = explode('&', $tempOrderorder);
        $pointPerAnsware = $maxpoint / count($rightAnsware);

        //kuldott válaszok
        print'good';
        printR($rightAnsware);
        $tempOrderorder2 = str_replace('sort[]=', '', $_POST['sortSer']);

        $dbAnswerFieldValue = $_POST['sortSer'];

        $sendedAnsware = explode('&', $tempOrderorder2);
print'sended - pairing';
printR($sendedAnsware);
        $res1 = array_diff_assoc($sendedAnsware, $rightAnsware);
        //$res2 = array_intersect( $sendedAnsware , $rightAnsware);
        //$res3 = array_diff_assoc( $res2 , $rightAnsware);
//        printR($res1);
        //printR($res2);
        //printR($res3);
        $p = count($res1) == 0 ? count($rightAnsware) : 0;
        //ellenorizzuk, hogy nem lutrizik-e
        //$points = (intval($rightAnsware) < intval($p)) ? $p + ($p - $rightAnsware) : $p;
        //if($points < 0 ) $points = 0;
        //ennyi pontja lett
        $totalPoint = $pointPerAnsware * $p;
//        print 'totalpont';
//        printR($totalPoint);
        break;
    ///////////////////////////////////////////////////////////////////
    case 'groupping':
        $db = count($answareArray);
        $tempPost = $_POST;
        unset($tempPost['ts']);
        unset($tempPost['u']);
        unset($tempPost['o']);
        unset($tempPost['lft']);
        unset($tempPost['slideshow']);
        unset($tempPost['template']);
        unset($tempPost['badge']);

        $dbAnswerFieldValue = $tempPost;

        for ($i = 0; $i < $db; $i++) {
            //leszedjuk a []-oket
            $tempOrderorder = str_replace('sort[]=', '', $answareArray[$i]['order']);
            $rightAnsware = explode('&', $tempOrderorder);
            $pointPerAnsware = $pointPerAnsware + $maxpoint / count($rightAnsware);
            //print'good';
            //printR($rightAnsware);
            //kuldott válaszok
            $tempSer = '';
            foreach ($tempPost as $key => $val) {
                if ($key == 'sort_' . $answareArray[$i]['group'])
                    $tempSer = $val;
            }
            $tempOrderorder2 = str_replace('sort[]=', '', $tempSer);
            $sendedAnsware = explode('&', $tempOrderorder2);
/**/
print'sended - groupping';
printR($sendedAnsware);

            $res = array_diff_assoc($sendedAnsware, $rightAnsware);
            $res1 = array_diff($sendedAnsware, $rightAnsware);
//            printR($res);
//            printR($res1);
            $p = (count($res) !== 0 && empty($res1)) ? count($rightAnsware) : 0;
            //ellenorizzuk, hogy nem lutrizik-e
            //$points = (intval($rightAnsware) < intval($p)) ? $p + ($p - $rightAnsware) : $p;
            //if($points < 0 ) $points = 0;
            //ennyi pontja lett
            $totalPoint = $pointPerAnsware * $p;
        }
        //printR($totalPoint);
        break;

    case '29p_1':
        $totalPoint = 0;
        foreach ($_POST['sel'] as $k => $v)
            $totalPoint = $totalPoint + $v;

        $dbAnswerFieldValue = $_POST['sel'];

        break;
    case '29p_2':
        foreach ($_POST['sel'] as $k => $v)
            $totalPoint = $totalPoint + $v;

        $dbAnswerFieldValue = $_POST['sel'];
        ////////////////////////////////////////
        //lekerdezem az eddigieket
        ////////////////////////////////////////

        $sqlSelect = "
        SELECT sum(result) AS totalpoint
        FROM training_results
        WHERE training_id = " . (int)$trainingId . " AND slideshow_id = " . (int)$slideShowId . " AND u_id = " . (int)$userId . " AND token = '" . MySQL::filter($token) . "'";

        $result = MySQL::query($sqlSelect, false, false);

        $totalPoint2 = $result[0]['totalpoint'];

        print 'totalpoint from';

        break;
}

///////////////////////////////////////////////////////////////////
//teljes megszerzett pontszam
//tekintettel  a tesztre
///////////////////////////////////////////////////////////////////
$totalPoint3 = $totalPoint + $totalPoint2;

///////////////////////////////////////////////////////////////////
//meghatarozom, mennyi pontot ert el
///////////////////////////////////////////////////////////////////
$db = count($optionArray);
$tempOption = array();
foreach ($optionArray as $row)
    $tempOption[] = $row['score'];
/*
print '$tempOption - meghatarozom, mennyi pontot ert el';
printR( $tempOption );
*/
///////////////////////////////////////////////////////////////////
// megkeresem, hogy az elert pont melyik ertekhez all a legkozelebb
// pl.: 40 a 60-hoz
// pl.: 60< <100
///////////////////////////////////////////////////////////////////
function closest($array, $number)
{
    sort($array);
    foreach ($array as $a) {
        if ($a >= $number) return $a;
    }
    return end($array); // or return NULL;
}

$needles = closest($tempOption, $totalPoint3);

function array_searcher($needles, $array)
{
    $ret = '';
    //foreach ($needles as $needle) {
    foreach ($array as $key => $value) {
        if ($value == $needles) {
            $ret = $key;
        }
    }
    //}
    return $ret;
}

//megkapom az aktuális sort
$ind = array_searcher($needles, $tempOption);

if ($totalPoint3 >= $optionArray[$ind]['score']) {
    $nextSlide = $optionArray[$ind]['next'] == '' ? $_POST['badge'] + 1 : $optionArray[$ind]['next'];
} else {
    $nextSlide = $optionArray[$ind]['prev'] == '' ? $optionArray[$ind - 1]['next'] : $optionArray[$ind]['prev'];
}
/*
    print '$totalPoint3';
    printR($totalPoint3);
    print '$nextSlide';
    printR($nextSlide);
    print '$totalPoint';
    printR($totalPoint);
    //exit;
*/
/////////////////////////////////////////////////////////////////////////////
//elmentjuk az eredmenyet
/////////////////////////////////////////////////////////////////////////////
$settingsArray = array();
$settingsArray['training_id'] = $trainingId;
$settingsArray['slideshow_id'] = $slideShowId;
$settingsArray['slide_id'] = $slideId;
$settingsArray['u_id'] = $userId;
$settingsArray['result'] = $totalPoint;
$settingsArray['testtype'] = "'" . $templateType . "'";
$settingsArray['token'] = "'" . $token . "'";
$settingsArray['office_id'] = $officeId;
$settingsArray['office_nametag'] = "'" . $officeNameTag . "'";
$settingsArray['answer'] = "'" . MySQL::filter(json_encode($dbAnswerFieldValue)) . "'";

$keys = array_keys($settingsArray);
$values = array_values($settingsArray);
$sqlInsert = "
    INSERT INTO training_results
    (" . implode(',', $keys) . ")
    VALUES
    (" . implode(',', $values) . ")
     ON DUPLICATE KEY UPDATE answer='" . MySQL::filter(json_encode($dbAnswerFieldValue)) . "', result=" . (int)$settingsArray['result'];

$result = MySQL::execute($sqlInsert);

//ha vegzett eredmenyek loggolasa
/*
printR($result);
exit;
*/
/////////////////////////////////////////////////////////////////////////////
//lekerdezzuk a slide adatait, ahova irányítjuk
/////////////////////////////////////////////////////////////////////////////
/*
print 'badge';
printR($badge);
print 'next slide';
printR($nextSlide);
print 'trainingSlideshowIsTest';
printR($trainingSlideshowIsTest);
*/
$options = array(
    'table' => 'slide_slides',
    'fields' => 'badge, lft, type, templateType',
    'condition' => array(
        'slideshow_id' => $slideShowId,
        'office_id' => $officeId,
        'office_nametag' => $officeNameTag
    ),
    'conditionExtra' => (($trainingSlideshowIsTest && $nextSlide < $badge)? 'badge > '. $badge : 'badge = ' . $nextSlide),
    'order' => 'badge',
    'limit' => 1
);
$result = MySQL::select($options);

//////////////////////////////////////////////////////////////////////////////
// A 29% teszt tipus esetén a29p_3 templatetype jelenti az utolsó slideot
// és itt összeadhatom a teljes pontszámot
// amit ki is irathatok a következő, elért pontszámnak megfelelő slideon
//////////////////////////////////////////////////////////////////////////////
if ($result[0]['templateType'] == '29p_3') {
    $sqlTest = "
      SELECT SUM(T1.result) AS result
      FROM training_results T1
      INNER JOIN training_results T2
      ON T1.training_id = T2.training_id AND T1.slideshow_id = T2.slideshow_id AND T1.token = T2.token
      WHERE 
          T2.office_nametag = '" . MySQL::filter($officeNameTag) . "' AND
          T2.office_id = " . MySQL::filter($officeId) . " AND
          T2.u_id = " . (int)$userId . " AND T2.testtype LIKE '%29p%' AND
          T2.date = (SELECT MAX(date) 
                  FROM training_results T3
                  WHERE T3.office_nametag = '" . MySQL::filter($officeNameTag) . "' AND T3.office_id = " . MySQL::filter($officeId) . " AND T3.u_id = " . (int)$userId . " AND T3.testtype LIKE '%29p%')";
    $res = MySQL::query($sqlTest, false, false);
    $testPoint = empty($res[0]['result']) ? '0' : $res[0]['result'];
    $_SESSION['29_testPoint'] = $testPoint;
} else
    unset($_SESSION['29_testPoint']);

//////////////////////////////////////////////////////////////////////////////
//megkeressuk a slide adatait (badge is kell)!!!
//osszehasonlit az eredmeny
//ment az eredmeny
//megkeressük a következő badget
//tovabbiranyit a megadott slidera
//printR($result[0]['badge']);
$nextSlideId = $result[0]['badge'] ? $result[0]['badge'] : ((int)$trainingSlideshowIsTest == 1 ? $token : ''); 
//for test
//$nextSlideId = 'id1384943524239';
$url = "http://" . $_SERVER['HTTP_HOST'] . '/'.$trainingType.'/' . $trainingId . '/' . $slideShowId . '/' . $nextSlideId . '/';

//ez itt mi????
if ( ($result[0]['type'] == 'normal') || (strpos($nextSlideId, 'id') !== false ) ) {
    $res = TrainingDetails::addScore($trainingId, $officeId, $slideShowId, $token, $userId);
}

/*
printR($_POST);
printR($url);
printR($res);
exit;
*/
header("Location: $url");
?>