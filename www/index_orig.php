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
} elseif(isset($matches[1]) && !empty($matches[1]) && $matches[1] == $_SESSION['office_nametag']){

    header('Location: ' . $protocol . $_SESSION['office_nametag'].'.' . DOMAINTAG . '/home/');
    exit;
} elseif (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == TRUE && isset($_SESSION['office_nametag'])){
    header('Location: ' . $protocol . $_SESSION['office_nametag'].'.' . DOMAINTAG . '/home/');
    exit;
}


require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

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
}
if (isset($matches[1]) && !empty($matches[1]) && $matches[1] == $_SESSION['office_nametag'])
{
    if ($_SESSION['logged_in'] and $_SERVER['REQUEST_URI'] == '/')
    {
        header('Location: ' . $protocol . $_SESSION['office_nametag'].'.'.DOMAINTAG.'/home/');
        exit;
    }
}


$protocol = connectionType();
$pageTitle = 'SKILLBI.COM';

if (isset($_SESSION['error']) && !empty($_SESSION['error']))
{
    $errorArray = $_SESSION['error'];
    unset($_SESSION['error']);
}

require($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/class/browser.php');

/** @var $browser TYPE_NAME */
$browser = new Browser();
$browserArray = array(
    'name'     => $browser->getBrowser(),
    'version'  => $browser->getVersion(),
    'platform' => $browser->getPlatform(),
    'isTablet' => $detect->isTablet() ? $detect->version($browser->getBrowser()) : ''
);

if (!$_SESSION['isTablet'] && !$_SESSION['isMobile'])
{
    if ($browser->getBrowser() == Browser::BROWSER_IE)
    {
        $bVersion = $browser->getVersion();
        $bv       = (int)$bVersion;
        switch ($bv)
        {
            case 7 : header('X-UA-Compatible: IE=IE7'); break;
            case 8 : header('X-UA-Compatible: IE=IE8'); break;
            default: header('X-UA-Compatible: IE=EDGE');break;
        }
    }
}

///////////////////////////////////////////////////////////
// DEFAULT LANGUAGE CHECK
///////////////////////////////////////////////////////////
$pageLanguage = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
$langArray = array('hu', 'en');
if (!in_array($pageLanguage, $langArray))
{
    $pageLanguage = 'en';
}
if (isset($_POST['lang_']) && in_array($_POST['lang_'], $langArray))
{
    $pageLanguage         = $_POST['lang_'];
    $_SESSION['language'] = $_POST['lang_'];
    setcookie("defLang", $pageLanguage, time() + 60 * 60 * 24 * 365, '/');
    unset($_POST['lang_']);
};
if (isset($_SESSION['language']) && in_array($_SESSION['language'], $langArray))
{
    $pageLanguage = $_SESSION['language'];
    setcookie("defLang", $pageLanguage, time() + 60 * 60 * 24 * 365, '/', "." . DOMAINTAG);
};
if (!isset($_COOKIE["defLang"]))
{
    setcookie("defLang", $pageLanguage, time() + 60 * 60 * 24 * 365, '/', "." . DOMAINTAG);
};

header("Content-language: $pageLanguage");

///////////////////////////////////////////////////////////
// INCLUDE SECTION AND MAIN DEFINITIONS
///////////////////////////////////////////////////////////
include ($_SERVER['DOCUMENT_ROOT'] . '/../include/lang/en_' . $pageLanguage . '.php');

echo '<!DOCTYPE html>';
//<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">

$pageTitle = 'SKILLBI.COM';
$protocol = connectionType();
$subdomain = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG;

///////////////////////////////////////////////////////////
// LOGIN CHECK
///////////////////////////////////////////////////////////
$loggedInUser = 0;

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
    <!-- <meta http-equiv="Pragma" content="cache" />-->
    <meta http-equiv="Expires" content="-1"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">

    <?
    if (!$_SESSION['isTablet'] && !$_SESSION['isMobile'])
    {
        $browser = new Browser();
        if ($browser->getBrowser() == Browser::BROWSER_IE)
        {
            $bVersion = $browser->getVersion();
            $bv       = (int)$bVersion;
            switch ($bv)
            {
                case 7 : print '<meta http-equiv="X-UA-Compatible" content="IE=7" />'; break;
                case 8 : print '<meta http-equiv="X-UA-Compatible" content="IE=8" />'; break;
                default: print '<meta http-equiv="X-UA-Compatible" content="IE=EDGE, chrome=1" />'; break;
            }
        }
    }
    ?>
    <!--APPLE TOUCH ICONS-->
    <link rel="apple-touch-icon" href="/images/icons/apple-touch-icon-16x16.png">
    <link rel="apple-touch-icon" sizes="72x72" href="/images/icons/apple-touch-icon-72x72.png">
    <link rel="apple-touch-icon" sizes="114x114" href="/images/icons/apple-touch-icon-114x114.png">
    <!--INDIVIDUAL GOOGLE FONT
    <link href='http://fonts.googleapis.com/css?family=Yanone+Kaffeesatz:400,200,700,300&subset=latin,latin-ext' rel='stylesheet' type='text/css'>
    <link href='http://fonts.googleapis.com/css?family=Oswald:400,700,300|Inconsolata:400,700|Open+Sans+Condensed:300,300italic,700&subset=latin-ext,latin' rel='stylesheet' type='text/css'>-->
    <!--<link href='http://fonts.googleapis.com/css?family=Oswald:400,700,300&subset=latin-ext,latin' rel='stylesheet' type='text/css'>-->
    <link href="http://fonts.googleapis.com/css?family=Roboto+Condensed:400,700|Oswald:400,700&subset=latin-ext,latin" rel="stylesheet" type="text/css">
    <!--NEEDED CSS-->

    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/jqueryui/jquery-ui-1.9.1.custom.min.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap-responsive.css"/>
    <!---->
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/css/fonts.css"/>

    <!--SITE OWN CSS-->
    <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/basecolor.css"/>
    <!--<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/color.css" />-->

    <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/layout.css"/>
    <!--BASE JS FILES-->

    <?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/_jqueryLoad.php');
    ?>

    <script type="text/javascript" charset="utf-8" src="/assets/bootstrap/alert.min.js"></script>

    <!--[if lt IE 9]><link rel="stylesheet" type="text/css" href="/assets/960/1200.css"/><![endif]-->
    <!--[if IE 8]><link rel="stylesheet" type="text/css" href="/css/ie8.css"/><![endif]-->
    <!--[if IE 7]><link rel="stylesheet" type="text/css" href="/css/ie7.css"/><![endif]-->
    <!-- Shim to make HTML5 elements usable in older Internet Explorer versions -->
    <!--[if lt IE 10]><script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script><![endif]-->
    <!--<script type="text/javascript" charset="utf-8" src="/css/pie/PIE.js" ></script>-->
</head>

<body class="guest<? if (isset($errorArray) && !empty($errorArray)){echo " error";} ?>">
<?php
if (!$_SESSION['isTablet'] && !$_SESSION['isMobile'])
{
    if ($browser->getBrowser() == Browser::BROWSER_IE)
    {
        $bVersion = $browser->getVersion();
        if ($bVersion < 9)
            echo '<div class="alert" style="color:black"><!--<button type="button" class="close" data-dismiss="alert">×</button>--><strong>Please note that SKILLBI supports Internet Explorer versions 7 or 8 in minimal level.</strong><br>We recommend upgrading to Internet Explorer 9, or use of following Google Chrome, or Firefox to reach best user experience and full functionality</div><div class="clearfix"></div>';
    }
}
?>
<noscript>Engedélyezd a javascriptet</noscript>
<div class="container">
    <div class="row">
        <!-- LOGO & SITENAME -->
        <div class="CorporateBrand">
            <img src="<?= $protocol . IMG_SITE_URL; ?>/logo.png" width="40" height="40" class="img-circle"/>

            <div class="CorporateName">
                <a href="javascript:void(0);"><?=$pageTitle;?></a>
                <span>sharE-learning</span>
            </div>
        </div>
<?
if (isset($errorArray) && !empty($errorArray)) {
    echo '</div></div>';
    echo'<div class="container"><div class="row"><div class="span6 error" style="width:auto;margin: 30px 0 0 30px;"><div class="alert alert-' . $errorArray['type'] . '" style="padding: 5px 5px 5px 10px;">';
    foreach ($errorArray['messages'] as $error) {
        echo '<p style="margin:5px;">' . $error . '</p>';
    }
    echo '</div></div>';
    if ($errorArray['type'] == 'error' or $errorArray['type'] == 'warning') {
        echo '<div class="span12" style="margin: 30px 0 0 30px;">
          <p class="pull-left">Térjen vissza a bejelentkező oldalra</p>
          <div class="clearfix"></div>
          <p class="pull-left"><a href="/" >Vissza</p>';
        echo '</div>';
        exit;
    }
    echo '</div></div>';
    echo '<div class="clearfix"></div>';
}
?>
        <div class="pull-right">
            <div class="clearfix"></div>
            <form method="post" name="logForm" id="logForm" action="/login" class="form-inline" accept-charset="UTF-8" style="">
                <!--<h4 class="orangeT">Give your login details</h4>-->
                <div class="clearfix">
                    <div class="pull-left">
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-envelope"></i></span><input type="text" name="user" placeholder="Type your e-mail…" value=""/>
                        </div>
                        <div class="input-prepend">
                            <span class="add-on"><i class="icon-key"></i></span><input type="password" name="pass" placeholder="Type your password…" value=""/>
                        </div>
                    </div>
                    <div class="pull-left">
                        <input type="submit" class="btn btn-r btn-dark" id="doLogin" name="doLogin" value="Sign in"/>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="clearfix"></div>
    <div id="outerwrapperDiv" class="row" style="">
        <div id="innerwrapperDiv"></div>
        <div id="upperDiv" class="fade in">
            <div class="thumbnail" style="height:348px;">
                <img src="<?= $protocol . IMG_SITE_URL; ?>/landing-top.jpg" width="100%" height="100%"/>
            </div>
            <div class="preselect" style="height:140px;padding:20px 0;">
                <div class="pull-left functionHeader">
                    <h3>Kivel osztaná meg tudását környezetében?</h3>

                    <p>(válasszon az ingyenes próbaverzióhoz)</p>
                </div>
                <div class="pull-right">
                    <button class="circle circle-a"><span class="text">vásárlóim</span></button>
                    <button class="circle circle-b"><span class="text">partnereim</span></button>
                    <button class="circle circle-c"><span class="text">kollégáim</span></button>
                </div>
            </div>
        </div>
        <div id="lowerDiv" class="fade in">
            <div class="assessment lightgreyB" style="">
                <div class="offset1 span5">
                    <div class="colHeader">Jelentkezés ingyenes próba verzióra</div>
                    <div class="clearfix"></div>
                    <form id="assessmentForm" action="#" method="post" class="form-inline" accept-charset="UTF-8">
                        <input type="hidden" name="question" value="promo">

                        <table width="437" border="0" cellpadding="0" cellspacing="20" class="assessmentTable">
                            <tr>
                                <td colspan="2"><p>Milyen képzéseket szeretne indítani?</p>
                                    <label class="checkbox"><span>Vásárlói</span><br/>
                                        <input type="checkbox" name="chk[]" rel="circle-a" value="Vásárlói">
                                    </label>
                                    <label class="checkbox"><span>Partneri</span><br/>
                                        <input type="checkbox" name="chk[]" rel="circle-b" value="Partneri">
                                    </label>
                                    <label class="checkbox"><span>Vállalati</span><br/>
                                        <input type="checkbox" name="chk[]" rel="circle-c" value="Vállalati">
                                    </label>
                                    <label class="checkbox"><span>Más</span><br/>
                                        <input type="checkbox" name="chk[]" value="mas">
                                    </label>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="2"><p>Mennyi képzést szeretne indítani évente?</p>
                                    <input type="number" name="proyear" min="0" class="input-small" value=""/>
                                </td>
                            </tr>
                            <tr>
                                <td>&nbsp;</td>
                                <td>&nbsp;</td>
                            </tr>
                            <tr>
                                <td width="155"><label>Név</label>
                                    <input type="text" name="name" placeholder="" required value="">
                                </td>
                                <td width="222" rowspan="3" align="left" valign="top" nowrap="nowrap">
                                    <label style="width:130px">Kérdezzen bátran</label>
                                    <textarea rows="4" name="message" placeholder="" style="height:150px;resize: none;"></textarea>
                                </td>
                            </tr>
                            <tr>
                                <td><label>Vállalat</label>
                                    <input type="text" name="firm" placeholder="" required value="">
                                </td>
                            </tr>
                            <tr>
                                <td><label>Telefon</label>
                                    <input type="tel" name="phone" placeholder="" required value="">
                                </td>
                            </tr>
                            <tr>
                                <td><label>E-mail</label>
                                    <input name="email" placeholder="" type="email" required value="">
                                </td>
                                <td align="right" valign="bottom">
                                    <div class="removable">
                                        <p class="slider-text">Húzza el az elküldéshez</p>

                                        <div id="slider-captcha"></div>
                                        <div class="clearfix" style="height: 1px;"></div>
                                        <button class="btn btn-dark" id="submit">Elküld</button>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </form>
                </div>
                <div class="span2">
                    <p>A Skillbi.com segít a tananyagok megtervezésében, és megtanítjuk az online képzés
                        módszertanára, hogy hatékony képzéseket tudjon létrehozni!</p>

                    <div class="clearfix"></div>
                    <fieldset class="errorcontainer" style="display:block;">
                        <span class="error" id="name_error">Adja meg nevét!</span>
                        <span class="error" id="firm_error">Adja meg a cég nevét!</span>
                        <span class="error" id="phone_error">Adje meg elérhetőségét!</span>
                        <span class="error" id="email_error">Adja meg e-mail címét!</span>
                    </fieldset>
                </div>
            </div>
        </div>

    </div>
</div>
<style>
    #outerwrapperDiv {
        /**/
        overflow: hidden;
        position: relative;
        height: 540px;

    }

    #upperDiv {
        /*position:absolute;left:0;top:0px;*/
        height: 540px;
    }

    #lowerDiv {
        /*position:absolute;left:0;*/
        height: 540px;
        width: 100%;
    }

    .assessment {
        /*position:relative;*/
        height: 520px;
        width: 100%;
    }
</style>
<script type="text/javascript" charset="utf-8" src="/lib/landingpage.min.js"></script>
</body>
</html>