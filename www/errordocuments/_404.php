<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/config.php');
/*
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers
header("Pragma: no-cache");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
header('Content-type: text/html; charset=utf-8');
*/
require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_include_base.php');

$headers = $_SERVER;
$errorCode = $_SERVER['REDIRECT_STATUS'];
$errorText = '';

switch ($errorCode)
{
  case 403    : $errorText = '403'; break;
  case 404    : $errorText = '404'; break;
  default     : $errorText = 'Something else'; break;
}
foreach ($headers as $header => $value) {
    //echo "$header: $value <br />\n";
}
echo '
  <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap.css" />
  <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap-responsive.css" />
  <!--<link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/css/fonts.css" />-->
  <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/basecolor.css" />
  <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/layout.css" />
  <link href=\'http://fonts.googleapis.com/css?family=Oswald:400,700,300&subset=latin-ext,latin\' rel=\'stylesheet\' type=\'text/css\'>
  ';
$errorCode = '404';
?>
<style>
    .triang {
        width: 0;
        height: 0;
        position: absolute;
        left: 0;
        top: 0;
        border-style: solid;
        border-width: 0 100px 178px 100px;
        border-color: rgba(0, 0, 0, 0) rgba(0, 0, 0, 0) #FFB310 rgba(0, 0, 0, 0);
        filter: progid:DXImageTransform.Microsoft.Chroma(color='#000000');
    }
</style>
<div class="row">
  <div class="container">
    <div class="span6 offset3 errormessage">
      <h1><span class="triang"><span class="sign">!</span></span><span class="number"><?=$errorCode;?></span></h1>
    </div>
    <div class="clear"></div>
  </div>
</div>
<div class="clear"></div>
        