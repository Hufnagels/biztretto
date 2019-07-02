<?php
ob_start();//Hook output buffer
  require_once( $_SERVER['DOCUMENT_ROOT'].'/../include/class/class.mysql.php');
  require_once( $_SERVER['DOCUMENT_ROOT'].'/../include/functions/function.base.php');
  require_once( $_SERVER['DOCUMENT_ROOT'].'/../include/functions/function.system.php');
  require_once( $_SERVER['DOCUMENT_ROOT'].'/../include/functions/function.text.php');
  //require_once( $_SERVER['DOCUMENT_ROOT'].'/../include/functions/function.images.php');

ob_end_clean();//Clear output buffer
?>