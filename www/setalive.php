<?php
require_once( $_SERVER['DOCUMENT_ROOT'].'/../include/config.php' );

require( $_SERVER['DOCUMENT_ROOT'].'/../include/authenticate.php' );

require( $_SERVER['DOCUMENT_ROOT'] .'/../include/header/_header_auth.php' );


$_SESSION['LAST_ACTIVITY'] = time();
?>