<?php
include ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_header_text.php');
include ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_include_base.php');
session_start();
$protocol = connectionType();
$cssURL = $protocol . DOMAINTAG;

include ($_SERVER['DOCUMENT_ROOT'] . '/../include/class/browser.php');
$browser = new Browser();

$showId=0;
$trainingId = MySQL::filter($_GET['trainingId']);
if (isset($_GET['showId']) && !empty($_GET['showId']))
    $showId = MySQL::filter($_GET['showId']);

$badge = (isset($_GET['badge']) && !empty($_GET['badge'])) ? MySQL::filter($_GET['badge']) : 0;


///////////////////////////////////////////////////////////////
//megkeresem a domain prefixből a tablat
///////////////////////////////////////////////////////////////
preg_match('/([^.]+)\\' . DOMAINTAG_PREGSTRING . '/', $_SERVER['SERVER_NAME'], $matches);
$_SESSION['database'] = DB_PREFIX.$matches[1];
//connect to db
MySQL::connect(DB_HOST, DB_USER, DB_PASS, ((isset($_SESSION['database'])) and $_SESSION['database']) ? $_SESSION['database'] : null);

///////////////////////////////////////////////////////////////
//Megkeresem az elso slideshowt
///////////////////////////////////////////////////////////////
$formArray0 = array();
$formArray0['training_id'] = $trainingId;
$formArray0['office_id'] = 1;
$formArray0['office_nametag'] = $matches[1];
$formArray0['u_id'] = 1;
$_SESSION['u_id'] = 1;

if (!$showId) {
    include ($_SERVER['DOCUMENT_ROOT'] . '/errordocuments/404.php');
    exit;
}

$sql = "
    SELECT title, full_name AS name
    FROM training_training tt
    LEFT JOIN user_u u
    ON tt.authors = u.u_id
    WHERE tt.office_id = ".$formArray0['office_id']." AND tt.office_nametag ='".$formArray0['office_nametag']."' AND tt.training_id = ".$trainingId;
$result1 = MySQL::query($sql, false,false);

$title = MySQL::filter($result1[0]['title']);
$author = MySQL::filter($result1[0]['name']);

$subtitle = ''; // slideshow neve

//HA JOTT TOKENID, AKKOR AZ ELSO SLIDEOT NEZNI A SLIDESHOWBAN
if ((int)$showId > 0) {
    $res = MySQL::executeQuery('SELECT * FROM training_slideshow WHERE training_id=' . (int)$trainingId . ' AND slideshow_id=' . (int)$showId);
    $row = MySQL::fetchRecord($res, MySQL::fmAssoc);

    //elso slide
    if ($_GET['tokenId']) {
        //kitoltom a score tablaba is a test testtype mezoket, ezert lekerem a training_slideshow tablabol oket!
        $typeResult = TrainingDetails::getTraningSlideShowFields($trainingId,$showId);

        switch ($row['type']) {
            //NEM TESZT ESETEN A REGI VISITED,ES SCORE ERTEKEK ARCHIVALASA, ES UJ VISITED REKORD BESZURASA
            //AZTAN UJ SCORE ERTEK BESZURASA -> MEGNEZTED EZERT SUCCESS, ES RATE=5
            case 0:
                //arhivalas
                MySQL::runCommand('UPDATE training_slideshow_score SET archive=1 WHERE training_id=' . (int)$trainingId . ' AND slideshow_id=' . (int)$showId . ' AND office_id=' . (int)$formArray0['office_id']);

                //visited rekord beszurasa
                TrainingDetails::addVisited($trainingId,$showId,$formArray0['office_id'],1,$_REQUEST['tokenId']);

                //eredmeny beszurasa success = 1
                MySQL::execute('INSERT IGNORE INTO training_slideshow_score (training_id,slideshow_id,office_id,u_id,token_id,created,type,testtype,success,archive,visited,rate)
                        VALUES("' . (int)$trainingId . '","' . (int)$showId . '","' . (int)$formArray0['office_id'] . '","1","' . MySQL::filter($_REQUEST['tokenId']) . '",NOW(),'.$typeResult['type'].','.$typeResult['testtype'].',1,0,0,0)
                    ');
                break;
            //TESZT ESETEN A REGI VISITED ERTEKEK ARCHIVALASA, ES UJ VISITED REKORD BESZURASA
            case 1:
                //archivalas
                MySQL::runCommand('UPDATE training_slideshow_score SET archive=1 WHERE visited=1 AND training_id=' . (int)$trainingId . ' AND slideshow_id=' . (int)$showId . ' AND office_id=' . (int)$formArray0['office_id']);

                //visited rekord beszurasa

                TrainingDetails::addVisited($trainingId,$showId,$formArray0['office_id'],$formArray0,$_REQUEST['tokenId']);
                break;
        }
    }
}
//////////////////////////////////////////////////////////////

//////////////////////////////////////////////////////////////

$formArray['slideshow_id'] = (int)$showId;
$formArray['office_id'] = $formArray0['office_id'];
$formArray['office_nametag'] = $formArray0['office_nametag'];

////////////////////////////////////////////////////////////
//query slideshow slides
////////////////////////////////////////////////////////////
$options = array(
    'table' => 'slide_slides',
    'fields' => '*',
    'condition' => $formArray,
    'conditionExtra' => 'badge >= ' . $badge, //"name LIKE '%".$newDiskareaName['name']."%'",
    'order' => 'badge',
    'limit' => 100
);
$result = MySQL::select($options);

//print_r($result);
if ($result == true) {

    foreach ($result as $row) {

        $returnData[] = array(
            'id' => $row['slides_id'],
            'type' => $row['type'],
            'templateType' => $row['templateType'] == '' ? 'normal' : $row['templateType'],
            'html2' => htmlspecialchars_decode($row['htmlForSlideshow']), //stripslashes($row['html']),//base64_decode($row['html']),
            'tag' => $row['tag'],
            'badge' => $row['badge'],
            'lft' => $row['lft'],
            'description' => htmlspecialchars_decode($row['description']),
            'templateOption' => ($row['templateOption'] == '' ? '' : json_decode($row['templateOption'], true)),
            'background' => $row['background'] == NULL ? 'transparent' : $row['background']
        );
        //rgba(255,255,255,1)
    };

} else {
    include ($_SERVER['DOCUMENT_ROOT'] . '/errordocuments/404.php');
	echo 'hmm';
    exit;
}
$tempateType = 'normal';
unset($_POST);
echo '<!DOCTYPE html>';
?>

<html style="height: 100%;">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black">
    
    <title>::BIZTRETTO::SLIDESHOW PREVIEW</title>
    <?
    if ($browser->getBrowser() == Browser::BROWSER_IE) {
        $bVersion = $browser->getVersion();
        $bv = (int)$bVersion;
        switch ($bv) {
            case 7 : print '<meta http-equiv="X-UA-Compatible" content="IE=7" />'; break;
            case 8 : print '<meta http-equiv="X-UA-Compatible" content="IE=8" />'; break;
            default: print '<meta http-equiv="X-UA-Compatible" content="IE=EDGE, chrome=1" />'; break;
        }
    }
    ?>

    <!---->
	<link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap-responsive.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/css/fonts.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/basecolor.css"/>
    <!--<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/slideeditor.css"/>-->
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/css/public_slideshow.css"/>
	
	<?
    $includeFile = $_SERVER['DOCUMENT_ROOT'] . '/../media/' . $formArray['office_nametag'] . '/corporate/' . $formArray['office_nametag'] . '.css';
    $cssFile = $protocol . $formArray['office_nametag'] . '.' . DOMAINTAG . '/media/corporate/' . $formArray['office_nametag'] . '.css';
    if (file_exists($includeFile)) {
        echo '<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="' . $cssFile . '">';
    }
    ?>
    
	<?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/_jqueryLoad.php');
    ?>
    <!--<script type="text/javascript" charset="utf-8" src="/assets/video.js"></script>-->
</head>

<body class="fade firstTime <?= $formArray0['office_nametag']; ?>" style="position:relative;">


    <?
    $includeFile = $_SERVER['DOCUMENT_ROOT'] . '/../media/' . $formArray['office_nametag'] . '/corporate/' . $formArray['office_nametag'] . 'SlideshowHeader.php';
    if (file_exists($includeFile)){
        echo '<div id="corporateHeader" class="corporateHeader row">';
		include($includeFile);
		echo '</div>';
	}
	$includeFile2 = $_SERVER['DOCUMENT_ROOT'] . '/../media/' . $formArray['office_nametag'] . '/corporate/' . $formArray['office_nametag'] . 'SlideshowFooter.php';
	if (file_exists($includeFile2)){
		echo '<div id="corporateFooter" class="userBrand row">';
		include($includeFile2);
		echo '</div>';
	}
	
    ?>


<div id="footer" class="">
	
	<?
	if($returnData[0]['templateType'] == 'normal') {
	?>
	<div class="progress badge"></div>
	<div class="buttons">
		<span class="btn btn-dark btn-l prev"><i class="icon-chevron-left"></i></span>
		<span class="btn btn-dark btn-l next"><i class="icon-chevron-right"></i></span>
	</div>
	<?
	}
	?>
	<span class="btn btn-dark btn-l fullscreen"><i class="icon-fullscreen"></i></span>
</div>


<div id="impress">

    <?
    $db = count($returnData);
    $x = array();$y = array();$z = array();$rotate = array();
    //x,y,z -6000 +6000 ->12000/$db
    //rotate 0 - 360    ->360/$db
	$leftOrigin = $db * 1200 /2;
    for ($i = 0; $i < $db; $i++) {
        $x[] = -1* $leftOrigin + 1100*$i;//-6000 + (12000 / $db) * $i;
        $rotate[] = 0;// + (360 / $db) * $i;
    }

    /*
     * le kell kérdezni a tréning címét,
     * a slideshow címét
     * a trénert
     * és kell egy overview slide az elejére, ha a badge = 2
     *
     */



       /**/
	   $or = -1* $leftOrigin - 2200;
	if ($db>2)
		//echo '<div id="overview" class="step slide" data-x="'.$or.'" data-y="0" data-scale="1"><h1>'.$title.'</h1><h3>'.$author.'</h3></div>';
        


    //foreach ($returnData as $row) {
        //echo '<div class="slide step" id="slide_' . $row['badge'] . '" data-template-type="' . $row['templateType'] . '" data-slide-tag="' . $row['tag'] . '" data-x="' . $x[$row['badge'] - 1] . '" data-y="0"  data-z="0" data-scale="1" data-rotate="0">'; //'.$rotate[$row['badge']-1].'
    //if(count($returnData)>2 && $returnData[0]['templateType'] == 'normal')
    //echo '<div id="overview" class="step slide" data-x="0" data-y="0" data-scale="10" data-slide-tag="Overview"><h1>'.$title.'</h1><h3>'.$author.'</h3></div>';
    foreach ($returnData as $row) {
        echo '<div
        class="slide step"
        style="background-color:transparent;"
        id="slide_' . $row['badge'] . '"
        data-template-type="' . $row['templateType'] . '"
        data-slide-tag="' . $row['tag'] . '"
        data-x="' . $x[$row['badge'] - 1] /* rand(-3000,3000)    */ . '"
        data-y="'. '0' /*   rand(-3000,3000)  */.'"
        data-z="0"
        data-scale=".8"
        data-rotate-x="0"
        data-rotate-y="0"
        data-rotate-z="0"
        >';
        //echo '<div class="takaro">';
        if ($row['templateType'] !== 'normal') {
            echo '<form ' . createFormHeader2('slideForm', '/submit/', 'post', '', time(), $_SESSION['u_id'], $formArray0['office_nametag'], $row['lft'], $showId, $row['templateType'], $row['badge']);
            echo '<input type="hidden" name="training" value="' . $trainingId . '" />';
            echo '<input type="hidden" name="slide" value="' . $row['id'] . '" />';
            switch ($row['templateType']) {
                case 'sorting':
                case 'pairing':
                    echo '<input type="hidden" id="sortSer" name="sortSer" value="" />';
                    $tempateType = $row['templateType'];
                    break;
                case 'groupping':
                    $tempateType = $row['templateType'];
                    break;
            }
            echo $row['html2'] . '<div class="buttonClass" style="left: 45%; top: 90%; width: 10%; height: auto; position: absolute;"><button type="submit" id="submitForm" class="btn btn-dark btn-r">send</button></div>';
            echo '</form>';
        } else { /**/
            echo $row['html2'];
        }
        //echo '<video controls="" preload="auto" width="100%" height="100%" poster="http://trillala.skill.madein.hu/media/default/thumbnail/roczko.jpg"><source src="http://trillala.skill.madein.hu/streamvideo.php" type="video/mp4"></video>';
        //echo '</div>';
        echo '</div>';
        if ($row['templateType'] !== 'normal') {
            //$tempateType = 'template';
            break;
        }
    }
    ?>

</div>

<!-- Fallback message-->
<div class="fallback-message fade">
    <p>Your browser <b>doesn't support the features required</b> by impress.js, so you are presented with a simplified version of this presentation.</p>
    <p>For the best experience please use the latest <b>Chrome</b>, <b>Safari</b> or <b>Firefox</b> browser.</p>
</div>

<!-- Hint for handling
<div class="hint"><p>Use a spacebar or arrow keys to navigate</p></div>
-->
<?
echo '<script type="text/javascript" charset="utf-8" src="/assets/ieDataset.js"></script>';
/*
if ($browser->getBrowser() == Browser::BROWSER_IE) {
	echo '<script type="text/javascript" charset="utf-8" src="/assets/ieDataset.js"></script>';
    echo '<script type="text/javascript" charset="utf-8" src="/assets/jmpress.js"></script>';
	
} else {}
*/
    echo '<script type="text/javascript" charset="utf-8" src="/assets/impress.pubplugins.js"></script>';
    //echo '<script type="text/javascript" charset="utf-8" src="/assets/impress.js"></script>';
	echo '<script type="text/javascript" charset="utf-8" src="/js/publicImpress.js"></script>';

?>

<script type="text/javascript">

function cancelFullscreen(el) {
    var requestMethod = el.cancelFullScreen || el.webkitCancelFullScreen || el.mozCancelFullScreen || el.exitFullscreen;
    if (requestMethod) { // cancel full screen.
        requestMethod.call(el);
    } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
        /**/
         var wscript = new ActiveXObject("WScript.Shell");
         if (wscript !== null) {
         wscript.SendKeys("{F11}");
         }
         
    }
}

function requestFullscreen(element) {
    // Supports most browsers and their versions.
    var requestMethod = element.requestFullScreen || element.webkitRequestFullScreen || element.mozRequestFullScreen || element.msRequestFullScreen;
//alert(requestMethod)
    if (requestMethod) { // Native full screen.
        requestMethod.call(element);
        //$('#gotoFullscreen').removeClass('hiddenClass').attr('data-type', 'screen').text('Exit fullscreen');
    } else if (typeof window.ActiveXObject !== "undefined") { // Older IE.
        $('#gotoFullscreen').addClass('hiddenClass');
        /**/
         var wscript = new ActiveXObject("WScript.Shell");
         if (wscript !== null) {
         wscript.SendKeys("{F11}");
         } else {
         alert('Csak manuálisan válthat teljes képernyős üzemmódra');
         }
         
    }
}

function toggleFullscreen() {
    var elem = document.body; // Make the body go full screen.
    var isInFullScreen = (document.fullScreenElement && document.fullScreenElement !== null) || (document.mozFullScreen || document.webkitIsFullScreen);
    console.log('inFull? '+ isInFullScreen)
    if (isInFullScreen) {
        cancelFullscreen(document);
        //$('#gotoFullscreen').attr('data-type', 'fullscreen').text('Fullscreen');
    } else {
        requestFullscreen(elem);
        //$('#gotoFullscreen').removeClass('hiddenClass').attr('data-type', 'screen').text('Exit fullscreen');
    }

    return false;
}

    $(function () {

        $.when(
                <?
				/*
                if ($browser->getBrowser() == Browser::BROWSER_IE) {
                  echo "$('#impress').jmpress({ container : '#impress',
    animation: {
        transitionTimingFunction: 'cubic-bezier(.10, .10, .25, .90)'
    },
})";
                } else {}*/
                  echo 'impress().init()'; //, impress().showMenu()
                
                ?>
            ).done(function (resp) {
                setTimeout(function () {
                    $('body').removeClass('firstTime' ).addClass('in');
                }, 1500);
                if ("ontouchstart" in document.documentElement) {
                    document.querySelector(".hint").innerHTML = "<p>Tap on the left or right to navigate</p>";
                }
				/*
				<?
                if ($browser->getBrowser() == Browser::BROWSER_IE) {
                ?>
				$(".btn.prev").bind("click", function(e){
					$('#impress').jmpress("prev");
				});
				  
				$(".btn.next").bind("click", function(e){
					$('#impress').jmpress("next");
				});
				<?
                } else {
                ?>
				$(".btn.prev").bind("click", function(e){
					impress().prev();
				});
				  
				$(".btn.next").bind("click", function(e){
					impress().next();
				});
				<?
                }
                ?>
				*/
				$(".btn.prev").bind("click", function(e){
					impress().prev();
				});
				  
				$(".btn.next").bind("click", function(e){
					impress().next();
				});
				$('.btn.fullscreen').bind('click', function(e){
					toggleFullscreen();
				});

            });

        <?
        //echo "alert('".$tempateType."');";
            switch ($tempateType){
              case 'sorting':
                print "
                $('ul.sortableForm').sortable({
                  update:function(event,ui){
                    $('#sortSer').val(  $('ul.sortableForm').sortable('serialize') );
                  }
                });";
                  print "$('#sortSer').val(  $('ul.sortableForm').sortable('serialize') );";
                break;
              case 'pairing':
                print "
                $('ul.sortable').sortable({
                  update:function(event,ui){
                    $('#sortSer').val(  $('ul.sortable').sortable('serialize') );
                  }
                });";
                  print "$('#sortSer').val(  $('ul.sortable').sortable('serialize') );";
                break;
              case 'groupping':
                print "
                  $( '#sortableHolder' ).sortable({connectWith: 'ul.sortableForm'});";
                print "
                  $('.slide[data-template-type=\"groupping\"]')
                    .find('ul.sortableForm')
                    .each(function(i,el){
                      $('.slide[data-template-type=\"groupping\"] #slideForm').prepend('<input type=\"hidden\" name=\"sort_'+$(this).attr('id')+'\" id=\"sort_'+$(this).attr('id')+'\" value=\"\" />');
                      $(this).sortable(
                        {connectWith: 'ul.sortableForm'},
                        {update:function(event,ui){
                          $('#sort_'+$(this).attr('id')).val( $('#'+$(this).attr('id')).sortable('serialize') );
                        }
                      });
                    });";
                print "
                  $('ul.sortableForm')
                    .each(function(i,el){
                      $('#sort_'+$(this).attr('id')).val( $('#'+$(this).attr('id')).sortable('serialize') );
                    });";
                break;
            }

            if(isset($_SESSION['29_testPoint']) && !empty($_SESSION['29_testPoint'])){
              echo "$('#testPointDiv').val('".$_SESSION['29_testPoint']."');";
              echo "$('div.badge.test', parent.document.body).text('".$_SESSION['29_testPoint']." pont');";
            }

        ?>
    });
</script>
</body>
</html>