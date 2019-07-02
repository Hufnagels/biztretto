<?php
if (!SkillGlobalConfig::$settings['auth.disablecheckxhttprequestheader'])
{
    if((!isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')){
      require_once ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/404.php' );
      //return "papo2";
      exit();
    }
}
@session_start();
require_once( $_SERVER['DOCUMENT_ROOT'].'/../include/authenticate.php' );
if (!$_SESSION['logged_in']){
  include_once ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/403forbidden.php' );
  //return "papo2";
  exit();
}
$_SESSION['LAST_ACTIVITY'] = time();
$_SESSION['url'] = $_SERVER['REQUEST_URI'];
?>