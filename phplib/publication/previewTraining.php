<?php
require($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');
if (!$_SESSION['logged_in']) {
    include ($_SERVER['DOCUMENT_ROOT'] . '/errordocuments/403forbidden.php');
    exit();
}
@session_start();
$_SESSION['LAST_ACTIVITY'] = time();

require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

$protocol = connectionType();
$cssURL = $protocol . DOMAINTAG;

include ($_SERVER['DOCUMENT_ROOT'] . '/../include/class/browser.php');
$browser = new Browser();


$trainingId = MySQL::filter($_GET['trainingId']);
if (isset($_GET['showId']) && !empty($_GET['showId']))
    $showId = MySQL::filter($_GET['showId']);

//$lft = (isset($_GET['lft']) && !empty($_GET['lft']) ) ? MySQL::filter($_GET['lft']) : 0;
$badge = (isset($_GET['badge']) && !empty($_GET['badge'])) ? MySQL::filter($_GET['badge']) : 0;

//$diskArea_id = MySQL::filter($_GET['diskArea_id']);
//ellenorizni, hogy leteznek-e
//ha nem, akkor hiba oldal

///////////////////////////////////////////////////////////////
//Megkeresem az elso slideshowt
///////////////////////////////////////////////////////////////
$formArray0['training_id'] = $trainingId;
$formArray0['slideshow_id'] = $showId;
$formArray0['office_id'] = $_SESSION['office_id'];
$formArray0['office_nametag'] = $_SESSION['office_nametag'];
$options = array(
    'table' => 'training_slideshow',
    'fields' => '*',
    'condition' => $formArray0,
    //'conditionExtra' => 'lft = 2',

    'limit' => 1
);
$result = MySQL::select($options);

if ($result == true){
    $showId = MySQL::filter($result[0]['slideshow_id']);
} else {
    include ($_SERVER['DOCUMENT_ROOT'] . '/errordocuments/404.php');
    exit;
}
$sql = "
    SELECT title, full_name AS name
    FROM training_training tt
    LEFT JOIN user_u u
    ON tt.authors = u.u_id
    WHERE tt.office_id = ".$_SESSION['office_id']." AND tt.office_nametag ='".$_SESSION['office_nametag']."' AND tt.training_id = ".$trainingId;
$result1 = MySQL::query($sql, false,false);

$title = MySQL::filter($result1[0]['title']);
$author = MySQL::filter($result1[0]['name']);
//////////////////////////////////////////////////////////////

$formArray['slideshow_id'] = $showId;
$formArray['office_id'] = $_SESSION['office_id'];
$formArray['office_nametag'] = $_SESSION['office_nametag'];
//$formArray['office_id'] = MySQL::filter($_POST['load']);
//print_r($formArray);

//////////////////////////////////////////////////////////////
// check for attachment
//////////////////////////////////////////////////////////////
/*
$sql = "
  SELECT *
  FROM media_mymedia
  WHERE
  FIND_IN_SET (mymedia_id, ( SELECT attachment FROM slide_slideshow WHERE slideshow_id = ".$showId." ) )";
$attRes = MySQL::query($sql, false, false);
$attHtml = '';
$db = count($attRes);
if($db > 0){
  $imageURL = connectionType().$_SESSION['office_nametag'].'.'.DOMAINTAG.'/media/';
  $downloadUrl = connectionType().$_SESSION['office_nametag'].'.'.DOMAINTAG.'/download/';
  $attHtml = createAttachList($attRes, $imageURL, $downloadUrl);
}
*/
//if(!empty($mediaFilesArray))
//printR($attHtml);
//exit;
//////////////////////////////////////////////////////////////
//query slideshow slides
//////////////////////////////////////////////////////////////
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
            //'html'            => htmlspecialchars_decode($row['html']),//stripslashes($row['html']),//base64_decode($row['html']),
            'html2' => htmlspecialchars_decode($row['htmlForSlideshow']), //stripslashes($row['html']),//base64_decode($row['html']),
            'tag' => $row['tag'],
            'badge' => $row['badge'],
            'lft' => $row['lft'],
            //'error'           => $row['missingContent'],
            //'slideLevel'      => $row['slideLevel'],
            'description' => htmlspecialchars_decode($row['description']),
            'templateOption' => ($row['templateOption'] == '' ? '' : json_decode($row['templateOption'], true))
        );
    }
    ;

} else {
    //$returnData = array('error' => 'Slideshow can\'t be loaded!');
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
    <meta name="viewport" content="width=1024"/>
    <title>::SKILLBI::SLIDESHOW PREVIEW</title>
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
    <!--
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/jqueryui/jquery-ui-1.9.1.custom.min.css" />
    -->
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen"
          href="/assets/bootstrap/css/bootstrap-responsive.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/css/fonts.css"/>
    <!---->
    <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/basecolor.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/slideeditor.css"/>
    <!--
    <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/layout.css" />
    -->
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="<?= $cssURL; ?>/css/slideshow.css"/>
    <!---->
    <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/video.css"/>


    <?
    $includeFile = $_SERVER['DOCUMENT_ROOT'] . '/../media/' . $_SESSION['office_nametag'] . '/corporate/' . $_SESSION['office_nametag'] . '.css';
    $cssFile = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/corporate/' . $_SESSION['office_nametag'] . '.css';
    if (file_exists($includeFile)) {
        echo '<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="' . $cssFile . '">';
    }
    ?>
    <!--[if lt IE 9]>
    <link rel="stylesheet" type="text/css" href="/assets/960/1200.css"/><![endif]-->
    <!--[if IE 8]>
    <link rel="stylesheet" type="text/css" href="/css/ie8.css"/><![endif]-->
    <!--[if IE 7]>
    <link rel="stylesheet" type="text/css" href="/css/ie7.css"/><![endif]-->
    <!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
    <!--[if lt IE 10]>
    <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <!--<script type="text/javascript" charset="utf-8" src="/css/pie/PIE.js" ></script>-->

    <?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/_jqueryLoad.php');
    ?>

</head>
<body class="firstTime <?= $_SESSION['office_nametag']; ?>">
<div id="corporateHeader" class="corporateHeader row">
    <?
    $includeFile = $_SERVER['DOCUMENT_ROOT'] . '/../media/' . $_SESSION['office_nametag'] . '/corporate/' . $_SESSION['office_nametag'] . 'SlideshowHeader.php';
    if (file_exists($includeFile))
        include($includeFile);
    ?>
</div>

<div id="impress">

    <?
    $db = count($returnData);
    $x = array();$y = array();$z = array();$rotate = array();
    //x,y,z -6000 +6000 ->12000/$db
    //rotate 0 - 360    ->360/$db
    for ($i = 0; $i < $db; $i++) {
        $x[] = -6000 + (12000 / $db) * $i;
        $rotate[] = 0 + (360 / $db) * $i;
    }
    echo '<div id="overview" class="step slide" data-x="0" data-y="0" data-scale="10">
            <h1>'.$title.'</h1>
            <h3>'.$author.'</h3>
          </div>';
    foreach ($returnData as $row) {
        echo '<div
        class="slide step"
        id="slide_' . $row['badge'] . '"
        data-template-type="' . $row['templateType'] . '"
        data-slide-tag="' . $row['tag'] . '"
        data-x="' . rand(-3000,3000) . '"
        data-y="'.rand(-3000,3000).'"
        data-z="0"
        data-scale="0.5"
        data-rotate-x="0"
        data-rotate-y="0"
        data-rotate-z="0"
        >';
//$x[$row['badge'] - 1]

        echo '<div class="takaro">';
        if ($row['templateType'] !== 'normal') {
            echo '<form ' . createFormHeader2('slideForm', '/submit/', 'post', '', time(), $_SESSION['u_id'], $_SESSION['office_nametag'], $row['lft'], $showId, $row['templateType'], $row['badge']);
            echo '<input type="hidden" name="training" value="' . $trainingId . '" />';
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
            echo $row['html2']; //. '<div class="buttonClass" style="left: 87.13333333333333%; top: 91.25925925925927%; width: 12%; height: auto; position: absolute;"><button type="submit" id="submitForm" class="btn btn-dark btn-r">send</button></div>';
            echo '</form>';
        } else { /**/
            echo $row['html2'];
        }
        //echo '<video controls="" preload="auto" width="100%" height="100%" poster="http://trillala.skill.madein.hu/media/default/thumbnail/roczko.jpg"><source src="http://trillala.skill.madein.hu/streamvideo.php" type="video/mp4"></video>';
        echo '</div>';
        echo '</div>';
        //if ($row['templateType'] !== 'normal') break;
    }
    ?>
    <!---->


</div>

<!-- Fallback message-->
<div class="fallback-message">
    <p>Your browser <b>doesn't support the features required</b> by impress.js,
        so you are presented with a simplified version of this presentation.</p>

    <p>For the best experience please use the latest <b>Chrome</b>,
        <b>Safari</b> or <b>Firefox</b> browser.</p>
</div>
<!-- Hint for handling-->
<div class="hint"><p>Use a spacebar or arrow keys to navigate</p></div>
<!--<div class="progressbar"><div></div></div>-->
<div class="progress badge"></div>
<div id="corporateFooter">
    <div class="userBrand"><span class="pull-left slogen"></span><span class="pull-left sup"></span></div>
</div>
<div class="pointer-right1ss"></div>
<div class="pointer-right2ss"></div>
<div class="pointer-left1ss"></div>
<div class="pointer-left2ss"></div>
<!---->
<?
if ($browser->getBrowser() == Browser::BROWSER_IE) {
    echo '<script type="text/javascript" charset="utf-8" src="/assets/jmpress.js"></script>';
} else {
    echo '<script type="text/javascript" charset="utf-8" src="/assets/impress.plugins.js"></script>';
    echo '<script type="text/javascript" charset="utf-8" src="/assets/impress.js"></script>';
}
?>

<!--

<script type="text/javascript" charset="utf-8" src="/assets/video.js"></script>
<script>
/*
document.addEventListener("impress:substepenter", function (event) {
    var substep=event.target.getAttribute("data-active-substep");
    document.getElementById('events_debugger').innerHTML = 'Entering substep '+substep+'<br />'+document.getElementById('events_debugger').innerHTML;
});
document.addEventListener("impress:substepleave", function (event) {
    var substep=event.target.getAttribute("data-active-substep");
    document.getElementById('events_debugger').innerHTML = 'Leaving substep '+substep+'<br />'+document.getElementById('events_debugger').innerHTML;
});
document.addEventListener("impress:stepenter", function (event) {
    var step = event.target.id;
    document.getElementById('events_debugger').innerHTML = 'Entering step '+step+'<br />'+document.getElementById('events_debugger').innerHTML;
});
document.addEventListener("impress:stepleave", function (event) {
    var step = event.target.id;
    document.getElementById('events_debugger').innerHTML = 'Leaving step '+step+'<br />'+document.getElementById('events_debugger').innerHTML;
});
*/
</script>
<div id="events_debugger"></div>
<!---->

<script type="text/javascript">
    if ("ontouchstart" in document.documentElement) {
        document.querySelector(".hint").innerHTML = "<p>Tap on the left or right to navigate</p>";
    }

    $(function () {
        //ell ie eseten jmpress
        //minden mas impress
        /*
         //impress().init(), impress().showMenu()
         //$('#impress').jmpress()
         $videos = $('#impress').find('video');
         $.each($videos, function(i, e){
         });
         */
        $.when(
                <?
                if ($browser->getBrowser() == Browser::BROWSER_IE) {
                  echo "$('#impress').jmpress()";
                } else {
                  echo 'impress().init(), impress().showMenu()';
                }
                ?>
            ).done(function (resp) {
                setTimeout(function () {
                    $('body').removeClass('firstTime');
                }, 2000);
                $('body').removeClass('firstTime');
                <? if ($browser->getBrowser() !== Browser::BROWSER_IE) { ?>
                $('div[class^="pointer-right"]').bind('click', function () {
                    impress().next();
                });
                $('div[class^="pointer-left"]').bind('click', function () {
                    impress().prev();
                });
                <? } ?>
                /*
                $videos = $('#impress').find('video');
                var data = [];
                $.each($videos, function (i, e) {
                    //alert( e + '  ' + i );
                    //$(this).attr('poster', ''); //ios
                    $(this).attr('class', 'video-js vjs-default-skin');
                    $(this).attr('id', 'video' + i).css({'width': '100%', 'height': '90%'});

                    //touchon kell
                    _V_('video' + i, { 'width': '100%', 'height': '100%'});
                    //$(this).load();
                    //$('.video-js').css({'width': '100%', 'height': '100%'});
                });
                */
            });

        <?
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
        ?>

    });
</script>
</body>
</html>