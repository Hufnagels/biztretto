<?php

$_autoLoadDir = $_SERVER['DOCUMENT_ROOT'].'/../include/';

require_once($_autoLoadDir.'htmlpurifier/HTMLPurifier.auto.php'); //roszz kodok kiszurese
require_once($_autoLoadDir.'functions/function.base.php');        //alap fuggvenyek
require_once($_autoLoadDir.'class/class.mysql.php');              //db kezeles
require_once($_autoLoadDir.'class/class.access.php');             //mihez van jogosultsagod
require_once($_autoLoadDir.'class/class.actionlogger.php');       //logolja az admin user esemenyeit
require_once($_autoLoadDir.'class/class.trainingdetails.php');    //training details adatok lekerdezese
require_once($_autoLoadDir.'class/class.statistics.php');         //statisztikai adatok lekerdezese
require_once($_autoLoadDir.'class/class.user.php');               //user kezeles
require_once($_autoLoadDir.'class/class.skillmailer.php');        //levelezes
require_once($_autoLoadDir.'class/class.office.php');             //office tabla kezelese
require_once($_autoLoadDir.'class/class.mediamediabox.php');      //media_mediabox tabla kezelese
require_once($_autoLoadDir.'class/class.slideslides.php');        //slideshow tabla kezelese
require_once($_autoLoadDir.'class/class.slideslideshow.php');     //slideslideshow tabla kezelese
require_once($_autoLoadDir.'class/class.trainingtraining.php');   //trainingtraining tabla kezelese
require_once($_autoLoadDir.'class/class.mediamediaboxfiles.php'); //
require_once($_autoLoadDir.'class/class.trainingslideshow.php'); //
require_once($_autoLoadDir.'class/class.usertraininggroup.php'); //
require_once($_autoLoadDir.'class/class.userusergroup.php'); //
require_once($_autoLoadDir.'class/class.mediadiskarea.php'); //

unset($_autoLoadDir);
?>