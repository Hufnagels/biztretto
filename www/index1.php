<?php
require($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');

preg_match('/([^.]+)\\' . DOMAINTAG_PREGSTRING . '/', $_SERVER['SERVER_NAME'], $matches);

$protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

if (isset($matches[1]) && !empty($matches[1]) && $matches[1] !== $_SESSION['office_nametag']) {

    if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == TRUE) {
        $subdomain = $_SESSION['office_nametag'] . '.';
        $temp = explode('/', $_SERVER['REDIRECT_SCRIPT_URL']);
        $redirectTag = $_SERVER['REDIRECT_SCRIPT_URL'];
        header('Location: ' . $protocol . $subdomain . DOMAINTAG . $redirectTag);
    } else {
        header('Location: ' . $protocol . DOMAINTAG);
    }
    exit;
} else {
	//meg tesztelni kell:
	//ha a user be van lepve, de ujranyitja a lapot a fooldalrol, akkor ne hibasan toltodjon be az ajaxos resz, hanem iranyitsa at a fooldalt aldomainjere, majd home oldalra.
    if ($_SESSION['logged_in'] and $_SESSION['office_nametag'] and $_SERVER['SERVER_NAME'] != $_SESSION['office_nametag'].'.'.DOMAINTAG)
    {
        header('Location: ' . $protocol . $_SESSION['office_nametag'].'.'.DOMAINTAG.'/home/');
        exit;
    }
}

if (isset($_SESSION['error']) && !empty($_SESSION['error']))
    $errorArray = $_SESSION['error'];

unset($_SESSION['error']);

/**/

require($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');
///////////////////////////////////////////////////////////
// LOGIN CHECK
///////////////////////////////////////////////////////////
$loggedInUser = 0;
if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == TRUE) {
    $loggedInUser = 1;
};

if(!$loggedInUser){
    header('Location: /');
}


require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/class/browser.php');

$browser = new Browser();
$browserArray = array(
    'name' => $browser->getBrowser(),
    'version' => $browser->getVersion(),
    'platform' => $browser->getPlatform(),
    'isTablet' => $detect->isTablet() ? $detect->version($browser->getBrowser()) : ''
);

if (!$_SESSION['isTablet'] && !$_SESSION['isMobile']) {
    if ($browser->getBrowser() == Browser::BROWSER_IE) {
        $bVersion = $browser->getVersion();
        $bv = (int)$bVersion;
        switch ($bv) {
            case 7 :
                header('X-UA-Compatible: IE=IE7');
                break;
            case 8 :
                header('X-UA-Compatible: IE=IE8');
                break;
            default:
                header('X-UA-Compatible: IE=EDGE');
                break;
        }
    }
}

///////////////////////////////////////////////////////////
// DEFAULT LANGUAGE CHECK
///////////////////////////////////////////////////////////
$pageLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$langArray = array('hu', 'en');
if (!in_array($pageLanguage, $langArray)) {
    $pageLanguage = 'en';
}
if (isset($_POST['lang_']) && in_array($_POST['lang_'], $langArray)) {
    $pageLanguage = $_POST['lang_'];
    $_SESSION['language'] = $_POST['lang_'];
    setcookie("defLang", $pageLanguage, time() + 60 * 60 * 24 * 365, '/');
    unset($_POST['lang_']);
};
if (isset($_SESSION['language']) && in_array($_SESSION['language'], $langArray)) {
    $pageLanguage = $_SESSION['language'];
    setcookie("defLang", $pageLanguage, time() + 60 * 60 * 24 * 365, '/', "." . DOMAINTAG);
};
if (!isset($_COOKIE["defLang"])) {
    setcookie("defLang", $pageLanguage, time() + 60 * 60 * 24 * 365, '/', "." . DOMAINTAG);
};

header("Content-language: $pageLanguage");

///////////////////////////////////////////////////////////
// INCLUDE SECTION AND MAIN DEFINITIONS
///////////////////////////////////////////////////////////
include ($_SERVER['DOCUMENT_ROOT'] . '/../include/lang/en_' . $pageLanguage . '.php');

echo '<!DOCTYPE html>';
//<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

$pageTitle = SITENAME;
$protocol = connectionType();
$subdomain = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG;

?>
<!--[if lt IE 7 ]><html lang="<?=$pageLanguage;?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]><html lang="<?=$pageLanguage;?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]><html lang="<?=$pageLanguage;?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]><html lang="<?=$pageLanguage;?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--><html lang="<?= $pageLanguage; ?>"> <!--<![endif]-->
<head>
    <title>::<?=$pageTitle;?>::</title>
    <meta name="og:title" content="::<?= $pageTitle; ?>::">
    <meta name="title" content="::<?= $pageTitle; ?>::">

    <!-- ez valtozhat a tartalom függvényében -->
    <meta name="description" content="">
    <meta name="og:description" content="">
    <meta name="og:image" content="">
    <!--<meta name="keywords" content="<? /*include ($_SERVER['DOCUMENT_ROOT'].'/tagsFile.txt'); */?>" />  -->

    <link rel="shortcut icon" href="/favicon.ico"/>
    <link rel="icon" href="/favicon.ico" type="image/x-icon"/>
    <meta name="robots" content="index, follow"/>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <!-- <meta http-equiv="Pragma" content="cache" />
    <meta http-equiv="Expires" content="-1"/>-->
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <?
    if (!$_SESSION['isTablet'] && !$_SESSION['isMobile']) {
        $browser = new Browser();
        if ($browser->getBrowser() == Browser::BROWSER_IE) {
            $bVersion = $browser->getVersion();
            $bv = (int)$bVersion;
            switch ($bv) {
                case 7 :  print '<meta http-equiv="X-UA-Compatible" content="IE=7" />';  break;
                case 8 :  print '<meta http-equiv="X-UA-Compatible" content="IE=8" />';  break;
                default:  print '<meta http-equiv="X-UA-Compatible" content="IE=EDGE, chrome=1" />'; break;
            }
        }

        if ($browser->getBrowser() == Browser::BROWSER_IE && $browser->getVersion() > 9) {
            //print '<script src="http://maps.google.com/maps?file=api&v=2&sensor=false&key=ABQIAAAA8mq1BKT0A47vjS5nelRZMhRwHI1-Kn-Hal2JDBHB9Czk3_1DchR0EcAYsQx9JUEumFtoGgTrfitaEQ" type="text/javascript"></script>';
        } else {
            //print '<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>';
        }
    }
    ?>
    <!--APPLE TOUCH ICONS-->
    <link rel="apple-touch-icon" href="/images/icons/apple-touch-icon-16x16.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/images/icons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/images/icons/apple-touch-icon-114x114.png">
    <?
    /*<!--INDIVIDUAL GOOGLE FONT
    <link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,200,700,300&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Oswald:400,700,300|Inconsolata:400,700|Open+Sans+Condensed:300,300italic,700&subset=latin-ext,latin' rel='stylesheet' type='text/css'>-->
    <!--<link href='http://fonts.googleapis.com/css?family=Oswald:400,700,300&subset=latin-ext,latin' rel='stylesheet' type='text/css'>
    <link href="http://fonts.googleapis.com/css?family=Roboto+Condensed:400,700|Oswald:400,700&subset=latin-ext,latin"
          rel="stylesheet" type="text/css">-->
    <!--NEEDED CSS-->
    <!--
        <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/jqueryui/jquery-ui-1.9.1.custom.min.css"/>
        <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap.css"/>
        <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap-responsive.css"/>

        <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/css/fonts.css"/>

        <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/basecolor.css"/>

        <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/layout.css"/>
    -->
    */


        echo '<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/site.css" />';


        //login style
        //echo '<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/login/overwrite.css"/>';
        //echo '<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/login/style.css"/>';
        //echo '<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/login/default.css"/>';

        //echo '<link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/css/_dark.css" />';
        //echo '<link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/css/_flat.css" />';
    if ($loggedInUser == true) {
        $includeFile = $_SERVER['DOCUMENT_ROOT'] . '/../media/' . $_SESSION['office_nametag'] . '/corporate/' . $_SESSION['office_nametag'] . '.css';
        $cssFile = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/corporate/' . $_SESSION['office_nametag'] . '.css';
        if (file_exists($includeFile)) {
            echo '<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="' . $cssFile . '">';
        }
    }

    require_once ($_SERVER['DOCUMENT_ROOT'] . '/_jqueryLoad.php');
    ?>

    <script type="text/javascript" charset="utf-8" src="/assets/bootstrap/alert.min.js"></script>

    <!--[if lt IE 9]><link rel="stylesheet" type="text/css" href="/assets/960/1200.css"/><![endif]-->
    <!--[if gt IE 8]><link rel="stylesheet" type="text/css" href="/css/ie8.css"/><![endif]-->
    <!--[if IE 7]><link rel="stylesheet" type="text/css" href="/css/ie7.css"/><![endif]-->
    <!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
    <!--[if lt IE 10]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <!--<script type="text/javascript" charset="utf-8" src="/css/pie/PIE.js" ></script>-->
</head>

<body class="<? if ($loggedInUser == false) {
    echo "guest";
}  elseif (isset($errorArray) && !empty($errorArray)) {
    echo " error";
} else {
    echo 'loggedin';
}?>" > <!--OnKeyPress="return disableKeyPress(event)"  onresize="window.location=window.location" -->
<?php
//echo $loggedInUser;
//printR( $_SESSION );
if (!$_SESSION['isTablet'] && !$_SESSION['isMobile']) {
    if ($browser->getBrowser() == Browser::BROWSER_IE) {
        $bVersion = $browser->getVersion();
        if ($bVersion < 9)
            echo '<div class="alert" style="color:black"><!--<button type="button" class="close" data-dismiss="alert">×</button>--><strong>Please note that BIZTRETTO supports Internet Explorer versions 7 or 8 in minimal level.</strong><br>We recommend upgrading to Internet Explorer 9, or use of following Google Chrome, or Firefox to reach best user experience and full functionality</div><div class="clearfix"></div>';
    }
}
?>
<noscript>Engedélyezd a javascriptet</noscript>
<?
if ($loggedInUser == true) {
    echo '<div id="idletimeout" class="fade"><div class="alert alert-block"><div class="container"><div class="row"><div class="span12"><h4 style="font-family: \'Roboto Condensed\', sans-serif;">Warning!</h4><p>You were 20 minutes inactive. System will automatically log you out in<span class="badge" id="counter"></span>&nbsp;sec. <a id="idletimeout-resume" href="javascript:void(0);">I want resume work</a></p></div></div></div></div></div>';
    echo '<div id="messageHolder"></div>';
    //rintR($_SESSION);
}

?>
<form method="post" id="sign_out_form" action="/logout" style="display:none;">
    <? $token = isset($_SESSION['SESSION_TOKEN']) ? build_token($_SESSION['SESSION_TOKEN']) : ''; ?>
    <input name="authenticity_token" value="<?= $token; ?>" type="hidden"/>
    <input name="session_token" id="session_token" value="0" type="hidden"/>
</form>
<div id="wrapper">

<header class="hidden-top toggle-link-open">
    <div class="container">

        <div class="row nomargin">
            <div class="span12">
                <div class="headnav">
                    <ul>
                        <?
                        echo '<li class="loggedInName">Welcome ' . $_SESSION['keresztnev'] . '!</li>';
                        echo '<li><a tabindex="-1" href="javascript:void(0);" onclick="$(\'form#sign_out_form\').submit()" class="noClick">Sign out</a></li>';
                        ?>
                    </ul>
                </div>
            </div>
        </div>

        <div class="row nomargin">
            <div class="span3 nomargin">
                <a href="/"><div class="logo">
                        <div class="pull-left ">
                            <img src="<?= $protocol . IMG_SITE_URL; ?>/logo.png" />
                        </div>
                    </div>
                </a>
            </div>
            <div class="span9 pull-right">
                <div class="navbar navbar-static-top">

                    <div class="navigation">
                        <nav>
                            <ul class="nav topnav bold">

                                <?
                                if ($loggedInUser == true) {
                                    switch ($_SESSION['userlevel']) {
                                        case 9:
                                            echo '<li><a tabindex="-1" href="/admin/" data-crumb="">Admin</a></li>';
                                            echo '<li><a tabindex="-1" href="/config/" data-crumb="">Setup office</a></li>';
                                            break;
                                    }
                                    echo '<li><a tabindex="-1" href="/home/" data-crumb="My trainings">Home</a></li>';
                                    echo '<li><a tabindex="-1" href="/myaccount/" data-crumb="My Account">My Account</a></li>';
                                    switch ($_SESSION['userlevel']) {
                                        case 9:
                                            //admin
                                        case 7:
                                            //slideshow creator
                                            echo '<li><a tabindex="-1" href="/usermanagement/" data-crumb="Usermanager">UserManager</a></li>';
                                        case 5:
                                            echo '<li><a tabindex="-1" href="/mymedia/" data-crumb="Mymedia">MyMedia</a></li>';
                                            echo '<li><a tabindex="-1" href="/slideeditor/" data-crumb="Slideeditor">SlideEditor</a></li>';
                                            echo '<li><a tabindex="-1" href="/publication/" data-crumb="Training publication">Publication</a></li>';
                                            echo '<li><a tabindex="-1" href="/statistic/" data-crumb="Statistic">Statistic</a></li>';
                                            break;
                                        //user
                                        case 3:

                                            break;
                                    }
                                }
                                ?>
                            </ul>
                        </nav>
                    </div><!-- end navigation -->

                </div>
            </div>
        </div>
    </div>
</header>

    <div class="clearfix"></div>

    <?
    //error messages
    if (isset($errorArray) && !empty($errorArray)) {
        echo'<div class="container"><div class="row"><div class="span6 error" style="width:auto;margin: 30px 0 0 30px;"><div class="alert alert-' . $errorArray['type'] . '" style="padding: 5px 5px 5px 10px;">';
        foreach ($errorArray['messages'] as $error) {
            echo '<p style="margin:5px;">' . $error . '</p>';
        }
        echo '</div></div>';
        if ($errorArray['type'] == 'error' or $errorArray['type'] == 'warning') {
            echo '<div class="span12" style="margin: 30px 0 0 30px;">
          <p class="pull-left">Térjen vissza a bejelentkező oldalra</p>
          <div class="clearfix"></div>
          <p class="pull-left"><a href="/logout" >Vissza</p>';
            echo '</div>';
            exit;
        }
        echo '</div></div>';
        echo '<div class="clearfix"></div>';
    }


    if (!$loggedInUser) {
        echo '<div class="container"><div class="row">';
        echo '<div class="span12"><div class="userBrand"><span class="pull-left slogen"></span><span class="pull-left sup"></span><span class="pull-right companylogo"></span></div></div>';
        //echo '<div class="span12"><div id="messageHolder"></div></div>';
        //echo '<span class="pull-left span12 orangeB" id="xxx"></span>';
        echo '</div></div>';

    } ?>
    <!--CONTENT-->

    <div id="ContentEditor" ><!--ContentEditor-->
        <div id="main_content"></div>
    </div>


    <!--/CONTENT-->
    <div class="clearfix"></div>
    <!--FOOTER-->
    <div class="clear"></div>
    <!--FOOTER-->

</div>


<div id="loading" style="display: block;" class="well well small">
    <div id="loading-overlay"></div>
    <div id="main-loading" class=""><img src="/images/ajax-loader.gif"></div>
    <span class="loadingMessage">Loading in progress...</span>
</div>

<div id="confirmDiv"></div>

<div class="modal styled fade" id="myModal1">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4>Alert</h4>
    </div>
    <div class="modal-body">
        <p>Sites function are unreachabele under resolution 980px wide</p>
        <p>Please, change resuolution up to minimum 980px wide form</p>
    </div>

</div>

<div class="modal styled fade" id="myModal2">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
        <h4></h4>
    </div>
    <div class="modal-body"></div>
    <div class="modal-footer">
        <!--<button class="btn" data-dismiss="modal">Close</button>-->
        <button class="btn btn-dark calltoaction" id="">Save changes</button>
    </div>
</div>

<!-- details Modal -->
<div id="myDetails" class="modal styled hide fade" tabindex="-1" role="dialog" aria-labelledby="myDetailsModalLabel" aria-hidden="true">
    <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
        <h4 id="myDetailsLabel">Reset your <strong>password</strong></h4>
    </div>
    <div class="modal-body">

    </div>
</div>
<!-- end reset modal -->

<!--/FOOTER-->
<?
//printR(trim($_SERVER["REQUEST_URI"],"/"));
if (trim($_SERVER["REQUEST_URI"],"/") == 'home'){
?>
<div id="trainingFrame" class="hidden fullscreen_hide">
	<div class="colHeader">
		<div class="orangeT">
			<span class="btn btn-dark exit" id="exitPreview" data-type="preview">Exit</span>
			<span class="btn btn-dark exit" id="gotoFullscreen" data-type="fullscreen">fullscreen</span>
			<span class="slideshowName"></span>
			<input type="hidden" id="auth_token" name="auth_token" value="" />
			
			<div class="progress badge btn-dark pull-right" style="color: white;padding: 10px;line-height: 5px;margin-right: 10px;margin-top: 7px;"></div>
			<!--
			<div class="btn-group pull-right">
			  <button type="button" class="btn btn-dark dropdown-toggle" data-toggle="dropdown"><span class="progress imp"></span><span class="caret"></span></button>
			  <ul class="dropdown-menu" role="menu" id="menuList">
			  </ul>
			</div>-->
		</div>
	</div>
	<div class="clearfix"></div>
    <div class="iframeHolder">
		<style>
		.progress.imp {
-webkit-border-radius: 2px;
-moz-border-radius: 2px;
border-radius: 2px;
padding: 8px 5px;
line-height: 10px;
color: #eee;
background: transparent;
border: 0;
}
</style>
	<?
    $includeFile = $_SERVER['DOCUMENT_ROOT'] . '/../media/' . $_SESSION['office_nametag'] . '/corporate/' . $_SESSION['office_nametag'] . '.css';
    $cssFile = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/corporate/' . $_SESSION['office_nametag'] . '.css';
    if (file_exists($includeFile)) {
        echo '<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="' . $cssFile . '">';
    }
    
    $includeFile = $_SERVER['DOCUMENT_ROOT'] . '/../media/' . $_SESSION['office_nametag'] . '/corporate/' . $_SESSION['office_nametag'] . 'SlideshowHeader.php';
    if (file_exists($includeFile)){
        echo '<div id="corporateHeader" class="corporateHeader row">';
		include($includeFile);
		echo '</div>';
		}

		$includeFile = $_SERVER['DOCUMENT_ROOT'] . '/../media/' . $_SESSION['office_nametag'] . '/corporate/' . $_SESSION['office_nametag'] . 'SlideshowFooter.php';
	if (file_exists($includeFile)){
		echo '<div id="corporateFooter" class="userBrand row">';
		include($includeFile);
		echo '</div>';
	}
    ?>
		<iframe id="previewIframe" scrolling="no" frameborder="0" border="0" src="" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>
	</div>
</div>
<? } ?>
<?
if ($loggedInUser == true) {

    //<!--<div id="hiddenDiv"><span id="play"><span class="rotate">rotate</span><span class="skewx">skew</span><span class="skewy">skew</span><span class="scale">scale</span></span></div>-->
    echo '<div id="tempDivOuter"><div id="tempDiv"></div></div>';


    echo '<script type="text/javascript" charset="utf-8" src="/js/base.js"></script>';
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/_bootstrapLoad.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/_fileuploadLoad.php');
	echo '<script type="text/javascript" charset="utf-8" src="/assets/fileupload/tmpl.js"></script>';
    //echo '<script type="text/javascript" charset="utf-8" src="/assets/isotope/jquery.isotope.js"></script>';
    echo '<script type="text/javascript" charset="utf-8" src="/assets/jquery.tinysort.min.js"></script>';
    echo '<script type="text/javascript" charset="utf-8" src="/assets/scroll.min.js"></script>';

} else {
    ?>

<?
}
?>
<script type="text/javascript" charset="utf-8" src="/js/jquery.validate.js"></script>
<script type="text/javascript" charset="utf-8" src="/lib/functions.js"></script>


<script type="text/javascript">
    var $window = $(window);
    var w = window, d = document, e = d.documentElement, g = d.getElementsByTagName('body')[0];
    var viewportWidth, viewportHeight;
    viewportWidth = w.innerWidth || e.clientWidth || g.clientWidth;
    viewportHeight = w.innerHeight || e.clientHeight || g.clientHeight;
    var editedobject = document.getElementById('header'),
        $editedobject = $(editedobject);
    var _globallogging = true;
<? if ( $loggedInUser == true ) { ?>
    var slideShowSaved = true;
    var slideSaved = true;
    var editor;
<? } ?>

    $(function () {
        <? if ( $loggedInUser == true ) { ?>
        viewportWidth=w.innerWidth||e.clientWidth||g.clientWidth;
        viewportHeight=w.innerHeight||e.clientHeight||g.clientHeight;
        if (viewportWidth < 990 ) {
            $('#wrapper').addClass('hidden');
            $('#myModal1').modal('show');
        } else {
            $('#wrapper').removeClass('hidden');
            $('.modal').modal('hide');
        }

        $(window).resize(function(){
            viewportWidth=w.innerWidth||e.clientWidth||g.clientWidth;
            viewportHeight=w.innerHeight||e.clientHeight||g.clientHeight;
            if (viewportWidth < 990 ) {
                $('#wrapper').addClass('hidden');
                $('#myModal1').modal('show');
            } else {
                $('#wrapper').removeClass('hidden');
                $('.modal').modal('hide');
            }
        });

        $("body:not(dropdown)").on('click', function () {
            $('.dropdown input, .dropdown label').click(function (e) {
                e.stopPropagation();
            });
            $('[data-toggle="dropdown"]').each(function () {
                $(this).parent().removeClass('open');
            });
        });

        $('[href^=#]').live('click', function (e) {
            e.preventDefault();
        });

        $('.menu a').on('click', function (e) {
            e.preventDefault;
            e.stopPropagation();
            var href = $(this).attr('href');
            if (!slideShowSaved || !slideSaved) {
                e.preventDefault();
                e.stopPropagation();
                $("#confirmDiv").confirmModal({
                    heading: "Your slideshow isn't saved yet",
                    body: 'When you dismiss this message, Your last action will be lost!',
                    text: 'DISCARD',
                    cancel: true,
                    canceltext: 'STAY',
                    'type':'question',
                    callback: function () {
                        location.pathname = href;
                        return false;
                    }
                });
            }
        });

        $.idleTimeout('#idletimeout', '#idletimeout a', {
            idleAfter: 60 * 20,
            pollingInterval: 50 * 4,
            keepAliveURL: '/crawl?/checkSession/',
            onTimeout: function () {
                $('form#sign_out_form').submit();
            },
            onIdle: function () {
                $('#idletimeout').toggleClass('in');
            },
            onCountdown: function (counter) {
                $(this).find("span.badge").html(counter);
            },
            onResume: function () {
                $.ajax({url: "/crawl?/setSession/"}).done(function (data) {
                    $('#idletimeout').removeClass('in');
                });
            }
        });

        <? } ?>
        
        if (!$.browser.webkit) {
            //FF,MSIE
            window.onload = function (event) {

                $('#loading').show();
                $('ul.nav li').removeClass('active');
                var link = $('ul.nav a[href="' + location.pathname + '"]');
                link.parent().addClass('active');
                $('#linkName' ).text(link.text());
                $('#ContentEditor').load('/crawl', location.pathname);
                $('#loading').hide();
                return false;
            };
        };


        //chrome
        window.onpopstate = function (event) {
            $('#loading').show();
            $('#ContentEditor').load('/crawl', location.pathname);
            $('ul.nav li').removeClass('active');
            var link = $('ul.nav a[href="' + location.pathname + '"]');
            link.parent().addClass('active');
            $('#linkName' ).text(link.text());
            $('#loading').hide();
            return false;
        };

        $(".alert").alert();
       /**/
        $('.toggle-link').each(function() {
            $(this).click(function() {
                var state = 'open'; 
                var target = $(this).attr('data-target');
                var targetState = $(this).attr('data-target-state');
                
                if (typeof targetState !== 'undefined' && targetState !== false) {
                    state = targetState;
                }

                if (state == 'undefined') {
                    state = 'open';
                }
                $(target).toggleClass('toggle-link-'+ state);
                $(this).toggleClass(state);
            });
        });

		
    });

</script>
<? if ($loggedInUser == true) {


    //echo '<script type="text/javascript" charset="utf-8" src="/lib/matrices.js"></script>';
} ?>
</body>
</html>