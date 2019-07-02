<?php
//require user configuration and database connection parameters
require_once($_SERVER['DOCUMENT_ROOT'].'/../include/config.php');
require_once($_SERVER['DOCUMENT_ROOT'].'/../include/functions/function.base.php');

/*
 * HA A USER MAR BE VAN LEPVE, ELLENORIZZUK A SESSION SIGNATURET, HIBA ESETEN REDIRECT
 *
 */
if (($_SESSION['logged_in'])==TRUE)
{

    //valid user has logged-in to the website
    //Check for unauthorized use of user sessions
    $iprecreate= $_SERVER['REMOTE_ADDR'];
    $useragentrecreate=$_SERVER["HTTP_USER_AGENT"];
    $signaturerecreate=$_SESSION['signature'];
    //Extract original salt from authorized signature
    $saltrecreate = substr($signaturerecreate, 0, $length_salt);
    //Extract original hash from authorized signature
    $originalhash = substr($signaturerecreate, $length_salt, 40);
    //Re-create the hash based on the user IP and user agent
    //then check if it is authorized or not
    $hashrecreate= sha1($saltrecreate.$iprecreate.$useragentrecreate);

    if (!($hashrecreate==$originalhash)) {

        //Signature submitted by the user does not matched with the
        //authorized signature
        //This is unauthorized access
        //Block it

        $_SESSION['error'] = array(
            'type' => 'error',
            'messages' => array(
                'Unathorized access to the system.'
            )
        );
        //to many loginattempt
        //header(sprintf("Location: /"));	//forbidden_url
        return 'papo';
        exit;
    }
    //Session Lifetime control for inactivity
    $_SESSION['LAST_ACTIVITY'] = time();

}

?>