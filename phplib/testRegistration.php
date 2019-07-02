<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest'){
    require_once ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/404.php' );
    exit();
}

SkillMailer::sendTestRegistration(array('email'=>'registration@skillbi.com','name'=>'SKILLBI.COM'),$_POST['html'],'JELENTKEZÉS INGYENES PRÓBA VERZIÓRA');

printSortResult(array('type' => 'success', 'message' => 'Message sent!'));
?>