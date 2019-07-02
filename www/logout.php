<?php

require_once( $_SERVER['DOCUMENT_ROOT'].'/../include/config.php' );

require ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_include_base.php');

  //register logout time
  $domain = User::globalSkillDatabaseNameByEmail($_SESSION['user_email']);
  if (MySQL::isExistsInstance($domain)) 
        MySQL::changeDB($db = DB_PREFIX . $domain);
		
  $array_of_conditions = array(
    'ctime' => $_SESSION['logged_in_time'],
    'u_id' => $_SESSION['u_id']
  );
  $datetime = date('Y-m-d H:i:s', time());
  $whatToSet = array('logoutDate' => $datetime );

  $result = MySQL::update('stat_login',$whatToSet, $array_of_conditions);

ActionLogger::addToActionLog('user.logout',$u=new User($_SESSION['u_id']),$u->getDBField('user_email'),array());

$_SESSION['logged_in'] = FALSE;
session_destroy();   
session_unset();
header(sprintf("Location: ".SITE_URL));
exit;
?>
