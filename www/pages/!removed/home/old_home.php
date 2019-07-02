<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest'){
	require_once ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/404.php' );
  //return "papo2";
  exit();
}

require( $_SERVER['DOCUMENT_ROOT'].'/../include/authenticate.php' );


if (!$_SESSION['logged_in']){
  require_once ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/403forbidden.php' );
  //return "papo2";
  exit();
}
if (!$_SESSION['logged_in'])
  session_start();
  
require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/class/mobileDetect.php');
$detect = new Mobile_Detect;
if(!$_SESSION['isMobile']){
    $_SESSION['isMobile'] = $detect->isMobile();
}
if(!$_SESSION['isTablet']){
    $_SESSION['isTablet'] = $detect->isTablet();
}


$_SESSION['url'] = $_SERVER['REQUEST_URI'];
//printR( $_SESSION );
require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/db.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_include_base.php');
//include ($_SERVER['DOCUMENT_ROOT'] .'/../include/header/_header_auth.php');
$protocol = connectionType();
//clearstatcache();
//set_error_handler("customError");
$sm = new mysql();
$uid = $sm->filter( base64_decode($_GET['u']) ); //user
$ont = $sm->filter( base64_decode($_GET['i']) ); //irodaiazonosito

//printR($_SERVER);
?>

<div class="row">
  <div class="span12" style="position:relative;height: 600px;">
  {home}
  
  
  
    <span id="save"><a href="#1,0,0,1,0,0" class="reset">oh noes!</a></span>
    <span id="play" style="position: absolute;width: 400px;height: 400px;left: 200px;top: 100px;">
        <span class="rotate">rotate</span>
        
        <span class="skewx">skew</span>
        <span class="skewy">skew</span>
        <span class="move">move</span>
        <!---->
        <span class="scale">scale</span>
      
      </span>
    <div class="ResizableClass video " id="teszt" style="position: absolute;width: 400px;height: 400px;left: 200px;top: 100px;">
      <div class="movingBox"><i class="icon-move"></i></div>
      
      
      <div id="teszt2" style="">
      <!--
        <object width="100%" height="100%" style="z-index:1002";>
          <param name="movie" value="http://www.youtube.com/v/XRxTucrU4Q4&amp;hl=en&amp;fs=1" name='wmode' value='opaque'>
          <param name="allowFullScreen" value="true" name='wmode' value='opaque' >
          <embed src="http://www.youtube.com/v/XRxTucrU4Q4&amp;hl=en&amp;fs=1" type="application/x-shockwave-flash" allowfullscreen="true" width="100%" height="100%" wmode="opaque">
        </object>
      -->
      </div>
      
    </div>
  
  
      
      
      
  </div>
</div>

<script type="text/javascript">
var editedobject = document.getElementById('teszt2');
var $editedobject = $(editedobject);
</script>
