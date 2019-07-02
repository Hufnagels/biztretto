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

$subdomain = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG;

///////////////////////////////////////////////////////////
// PAGE CHECK
///////////////////////////////////////////////////////////
if (isset($_GET['pageN']) && $_GET['pageN'] == 'search.html') {
    $request[1] = 'search.html';
} else {
    $request = explode('/', $_SERVER['REQUEST_URI']);
    if (preg_match('/q=/i',$request[1])) $request[1] = 'search.html';//unset($request[1]);
//echo 'URI: '.$request[1];
}
$_GET['pageName'] = $request[1];


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
    <!--<link href='http://fonts.googleapis.com/css?family=Oswald:400,700,300&subset=latin-ext,latin' rel='stylesheet' type='text/css'>
    <link href="http://fonts.googleapis.com/css?family=Roboto+Condensed:400,700|Oswald:400,700&subset=latin-ext,latin" rel="stylesheet" type="text/css">
    -->

    <link rel="stylesheet" href="/css/jquery.bxslider.css" media="screen">


    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/jqueryui/jquery-ui-1.9.1.custom.min.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap-responsive.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/css/fonts.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/basecolor.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/login.css"/>
    <?
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/_jqueryLoad.php');
    //require_once ($_SERVER['DOCUMENT_ROOT'] . '/_bootstrapLoad.php');
    ?>
    <!-- HTML5 Shiv + detect touch events -->
    <script src="/js/modernizr.custom.js" style=""></script>

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

<div class="navbar ">
    <div class="navbar-inner">
        <div class="container-fluid">
                <ul class="nav pull-right">
                    <li class="divider-vertical"></li>
                    <li class="dropdown">
                        <a class="dropdown-toggle" href="#" data-toggle="dropdown">Sign In <strong class="caret"></strong></a>
                        <div class="dropdown-menu" style="padding: 15px; padding-bottom: 0px;">
                            <form method="post" action="/login" accept-charset="UTF-8">
                                <input style="margin-bottom: 15px;" type="text" name="user" placeholder="Type your e-mail…" value=""/>
                                <input style="margin-bottom: 15px;" type="password" name="pass" placeholder="Type your password…" value=""/>
                                <input type="submit" class="btn btn-r btn-dark" id="doLogin" name="doLogin" value="Sign in"/>
                            </form>
                        </div>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</div>

<header id="header" class="container clearfix ">
    <div class="CorporateBrand">
        <img src="<?= $protocol . IMG_SITE_URL; ?>/logo.png" width="40" height="40" class="img-circle"/>
        <div class="CorporateName"><a href="javascript:void(0);"><?=$pageTitle;?></a><span>sharE-learning</span></div>
    </div>

    <nav id="main-nav">
        <ul>
            <li class=""><a href="/index.html" >Home</a></li>
            <li class=""><a href="/free.html">Free trainings</a></li>
            <!--<li class=""><a href="/method.html">Methodes</a></li>-->
            <li class=""><a href="/price.html" >Pricing</a></li>
            <li class=""><a href="/about.html" >About us</a></li>
            <li><a href="/contact.html" >Contact</a></li>
        </ul>
    </nav>
</header>


<section id="content" class="container clearfix" style="min-height: 418px;">
<?
    if (isset($_GET['pageName']) && $_GET['pageName'] !='' ){ //&& isset($_GET['type']) && $_GET['type'] !='' && isset($_GET['subpage']) && $_GET['subpage'] !='') {
        $page = $_GET['pageName'];
        $type = $_GET['type'];
        $subpage = $_GET['subpage'];


        switch ($page) {
            case 'index.html'       : $filename = ($_SERVER['DOCUMENT_ROOT'] .'/pages/index.html'); break;
            case 'about.html'       : $filename = ($_SERVER['DOCUMENT_ROOT'] .'/pages/about.html'); break;
            case 'free.html'        : $filename = ($_SERVER['DOCUMENT_ROOT'] .'/pages/free.html'); break;
            case 'method.html'      : $filename = ($_SERVER['DOCUMENT_ROOT'] .'/pages/method.html'); break;
            case 'price.html'       : $filename = ($_SERVER['DOCUMENT_ROOT'] .'/pages/price.html'); break;
            case 'blog.html'        : $filename = ($_SERVER['DOCUMENT_ROOT'] .'/pages/blog.html'); break;
            case 'contact.html'     : $filename = ($_SERVER['DOCUMENT_ROOT'] .'/pages/contact.html'); break;
            case 'search.html'      : $filename = ($_SERVER['DOCUMENT_ROOT'] .'/pages/search.html'); break;
            //default : include ('404.php'); break;
        }
        if (file_exists($filename)) {
            include $filename;
        } else {
            include ($_SERVER['DOCUMENT_ROOT'] .'/errordocuments/404.php');
            echo 'ilyen oldal nincs';//include ('404.php');
        }
    } else {
        //echo 'ez a foldal';
        $filename = ($_SERVER['DOCUMENT_ROOT'] .'/pages/index.html');
        include $filename;
        //echo $request[1];
    }

?>
</section><!-- end #content -->


<footer id="footer" class="clearfix">
    <div class="container">
        <div class="three-fourth">
            <nav id="footer-nav" class="clearfix">
                <ul>
                    <li class=""><a href="/index.html" >Home</a></li>
                    <li class=""><a href="/free.html" >Free trainings</a></li>
                    <!--<li class=""><a href="/method.html" >Methods</a></li>-->
                    <li class=""><a href="/price.html" >Pricing</a></li>
                    <li class=""><a href="/about.html" >About us</a></li>
                    <li class=""><a href="/contact.html" >Contact</a></li>
                </ul>
            </nav>
            <!-- end #footer-nav -->

            <ul class="contact-info">
                <li class="address">hungary, budapest, dália utca 6.</li>
                <li class="phone">(123) 456-7890</li>
                <li class="email"><a href="mailto:contact@skillbi.com">contact@skillbi.com</a></li>
            </ul>
            <!-- end .contact-info -->

        </div>
        <!-- end .three-fourth -->

        <div class="one-fourth last">

            <span class="title">Stay connected</span>

            <ul class="social-links">
                <li class="twitter"><a href="#">Twitter</a></li>
                <li class="facebook"><a href="#">Facebook</a></li>
                <li class="digg"><a href="#">LinkedIn</a></li>
                <li class="youtube"><a href="#">YouTube</a></li>
                <li class="skype"><a href="#">Skype</a></li>
            </ul>
            <!-- end .social-links -->

        </div>
        <!-- end .one-fourth.last -->

    </div>
    <!-- end .container -->

</footer>
<!-- end #footer -->

<footer id="footer-bottom" class="clearfix">

    <div class="container">

        <ul>
            <li>SILLBI.COM © 2013</li>
            <li><a href="#">Legal Notice</a></li>
            <li><a href="#">Terms</a></li>
        </ul>

    </div>
    <!-- end .container -->

</footer>
<!-- end #footer-bottom -->




<script src="js/jquery.flexslider-min.js"></script>
<script src="js/jquery.isotope.min.js"></script>
<script src="js/jquery.easing-1.3.min.js"></script>
<script src="js/jquery.jcarousel.min.js"></script>
<!--
<script src="js/respond.min.js"></script>

<script src="js/jquery.fancybox.pack.js"></script>
<script src="js/jquery.jcarousel.min.js"></script>
<script src="js/jquery.cycle.all.min.js"></script>

<script src="js/audioplayerv1.min.js"></script>
<script src="js/jquery.gmap.min.js"></script>
-->


<script src="js/jquery.touchSwipe.min.js"></script>
<script src="js/login.js"></script>
<!--<![endif]-->

<a href="#" id="back-to-top" title="Back to Top">Back to Top</a>

<style>
    .rslides {
        position: relative;
        list-style: none;
        overflow: hidden;
        width: 100%;
        padding: 0;
        margin: 0;
    }

    .rslides li {
        -webkit-backface-visibility: hidden;
        position: absolute;
        display: none;
        width: 100%;
        left: 0;
        top: 0;
        margin-left:0;
    }

    .rslides li:first-child {
        position: relative;
        display: block;
        float: left;
    }

    .rslides img {
        display: block;
        height: auto;
        float: left;
        width: 100%;
        border: 0;
    }
    .flexslider li {margin-left: 0px;position:relative}
    .flex-direction-nav {display: none;}
    #slider1 li {border-top:3px solid transparent;}
    #slider1 li.flex-active-slide {border-top:3px solid #F03C02;}
    .caption,
    .flex-caption {
        background: #000;
        height: 50px;
        position: absolute;
        bottom: -10px;
        width: 100%;
        opacity: .7;
        color: #FFF;
        font-size: 12px;
        text-align: center;
        line-height: 45px;
    }
    div.bxcaption {
        text-align: left;
        font-style: italic;
        font-weight: 400;
        margin-left: 40px;
    }
    span.categories {
        text-align: center;
        font-style: italic;
        font-weight: 400;
        margin-left: 35px;
    }
    .projects-carousel h5 {text-align: center;}
    .post-carousel .bxheader,
    .post-carousel p {
        text-align: left;
        font-weight: 800;
        font-size: 14px;
        margin-bottom: 0px;
    }
    #slider1 li {
        float: left;
        list-style-type: none;
        width: 33.3333%;
        margin-left: 0;
        margin-right: 10px;
    }
    #slider1 li h5 {height:36px;}
    .flexslider a:hover {text-decoration: none;}
    #slider1 p {
        margin-left: 40px;
    }
    .navbar .nav li.dropdown > .dropdown-toggle .caret {
        border-top-color: #F03C02;
        border-bottom-color: #F03C02;
    }
    .navbar .nav > li > a {
        color: #F03C02;
    }
    form {
        margin-bottom: 15px;
    }
    .projects-carousel li:hover {
        background: #F8F8F8;
        border-bottom: 1px solid #F03C02;
    }
</style>
</body>
</html>
