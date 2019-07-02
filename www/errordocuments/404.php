<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
/*
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header('Content-type: text/html; charset=utf-8');
*/
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

$headers = $_SERVER;
$errorCode = $_SERVER['REDIRECT_STATUS'];
$errorText = '';

switch ($errorCode)
{
    case 403    :
        $errorText = '403';
        break;
    case 404    :
        $errorText = '404';
        break;
    default     :
        $errorText = 'Something else';
        break;
}
foreach ($headers as $header => $value)
{
    //echo "$header: $value <br />\n";
}
//echo '<link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap.css" /><link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap-responsive.css" /><!--<link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/css/fonts.css" />--><link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/basecolor.css" /><link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/layout.css" /><link href=\'http://fonts.googleapis.com/css?family=Oswald:400,700,300&subset=latin-ext,latin\' rel=\'stylesheet\' type=\'text/css\'>';
$errorCode = '404';
?>
<section id="inner-headline">
    <div class="container">
        <div class="row">
            <div class="span4">
                <div class="inner-heading">
                    <h2>404</h2>
                </div>
            </div>
            <div class="span8">

            </div>
        </div>
    </div>
</section>
<section id="content">
    <div class="container">


        <div class="row">
            <div class="span12">
                <h1 class="aligncenter">404 Error not found</h1>
            </div>

        </div>


    </div>
</section>
        