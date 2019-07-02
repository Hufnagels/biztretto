<?php
    /*********** Specify user levels *************/
    define ("SUPER_ADMIN_LEVEL", 9);
    define ("SITE_ADMIN_LEVEL", 8);
    define ("OFFICE_ADMIN_LEVEL", 7);
    define ("EDITOR_LEVEL", 5);
    define ("USER_LEVEL", 3);
    define ("TEST_USER_LEVEL", 2);
    define ("DEMO_USER_LEVEL", 1);

    define ("ACTIVE", 1);
    define ("INACTIVE", 2);
    define ("DELETED", 3);
    define ("ACCENTED", 4);

    /*********** COOKIE params *************/
    define("COOKIE_TIME_OUT", 1); //specify cookie timeout in days (default is 10 days)
    define('SALT_LENGTH', 9); // salt for password

    /*********** SITE SPECIFIC DIRECTORY's *************/
    define ("IMGPATH", "/var/www/varsoft.hu/biztretto/media/"); // ha nem htaccessesn keresztul megy
    define ("SITEPATH", "/var/www/varsoft.hu/biztretto/www/"); // ha nem htaccessesn keresztul megy
    define ("includePath", "/var/www/varsoft.hu/biztretto/include/");

    /*********** SITE SPECIFIC URL's *************/
    define ("IMG_SITE_URL", "media.biztretto.local");
    define ("SITE_URL", "http://biztretto.local/");

    define ("DOMAINTAG", "biztretto.local");
    define ("DOMAINTAG_PREGSTRING", ".biztretto\.local");

    define ("SITENAME", "BIZTRETTO.COM");
    define ("WATERMARK_TEXT", "BIZTRETTO.local");
    define ("UPLOAD_LIMIT", 200);

    define ("DEVMODE", TRUE);
    define ("ACTIONLOGGER_GZIP", FALSE);
    define ("SKILLMAILER_SENDMAIL", FALSE); //csak devmode=false, es skillmailer_sendmail=true eseten megy ki level, egyebkent csak a log mappaba kerul!

    /*********** USED REGEX's  *************/
    define ("URL_REGEX", "(?i)\b((?:[a-z][\w-]+:(?:/{1,3}|[a-z0-9%])|www\d{0,3}[.]|[a-z0-9.\-]+[.][a-z]{2,4}/)(?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>]+|(\([^\s()<>]+\)))*\)|[^\s`!()\[\]{};:'\".,<>?«»“”‘’]))");

    define ('EMAIL_REGEX', '/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU');

    define ("SITEADMIN_EMAIL", "youremail@varsoft.hu");
    define ("CUSTOMER_SUPPORT_EMAIL", "youremail@varsoft.hu");
    define ("HELP_DESK_EMAIL", "youremail@varsoft.hu");


    /*********** USED FOR AUTHENTICATE.PHP  *************/
    $domain  = SITE_URL;
    $email   = 'youremail@'.DOMAINTAG;
    $subject = 'New user registration notification';
    $from    = 'From: '.DOMAINTAG;

//Define Recaptcha parameters
    $privatekey = "Your Recaptcha private key";
    $publickey  = "Your Recaptcha public key";

//Define length of salt,minimum=10, maximum=35
    $length_salt = 15;

//Define the maximum number of failed attempts to ban brute force attackers
//minimum is 5
    $maxfailedattempt = 5;

//Define session timeout in seconds
//minimum 60 (for one minute)
    $sessiontimeout = 60 * 25;

////////////////////////////////////
//END OF USER CONFIGURATION/////////
////////////////////////////////////


    $loginpage_url = $domain . '';
    $forbidden_url = $domain . 'errordocuments/403forbidden.php';


    define ('BROWSERARRAY', null);

    if (defined('PHP_WINDOWS_VERSION_MAJOR')) setlocale(LC_CTYPE, 'C');

    define ("DB_HOST", "localhost"); // set database host
    define ("DB_USER", "youremail"); // set database user
    define ("DB_PASS", "youremail"); // set database password
    define ("DB_NAME", "vs_biztretto_default"); // set database name
    define ("DB_PREFIX", "vs_biztretto_"); // set database name

    ini_set("session.cookie_domain", "." . DOMAINTAG);
    ini_set("post_max_size", '100M');

    class SkillGlobalConfig
    {
        public static $settings = array('auth.disablecheckxhttprequestheader' => FALSE);
    }

    session_start();

    if (DEVMODE === TRUE)
    {
        error_reporting(E_WARNING | E_ERROR);
        ini_set('display_errors', TRUE);
    }
    require_once ('class/mobileDetect.php');

    $detect = new Mobile_Detect;
    if (!$_SESSION['isMobile']) $_SESSION['isMobile'] = $detect->isMobile();
    if (!$_SESSION['isTablet']) $_SESSION['isTablet'] = $detect->isTablet();

    require_once('class/autoload.php');

?>
