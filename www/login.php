<?php

require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/functions/function.base.php');
/**
 * HA JOTT DOMAIN PARAMETER, ELLENORIZZUK, ES IDEIGLENES ADATBAZIST VALTUNK
 */

//$_POST['user'] = $_POST['username'];
//$_POST['pass'] = $_POST['password'];
//print_r($_POST);
//print_r($_SESSION['logged_in']);
//exit;
/**/
if ( (isset($_POST["pass"])) && (isset($_POST["user"])) && $_SESSION['logged_in'] == FALSE ) { //  !isset

    $domain = User::globalSkillDatabaseNameByEmail($_POST['user']);

    if (MySQL::isExistsInstance($domain)) {
        MySQL::changeDB($db = DB_PREFIX . $domain);
    } else {
        session_regenerate_id(true);

        $_SESSION['error'] = array(
            'type' => 'error',
            'messages' => array(
                'You are not registered1!'
            )
        );

//hiba eseten hibauzenet kiirasa
        header(sprintf("Location: /")); //forbidden_url
        exit;
    }
}

//print '$domain:'.$db;
//exit;

/**
 * BRUTE FORCE VEDELEM ELLENORZESE
 */

//Pre-define validation
$referer = '';
if (isset($_SESSION['REFERER']) && $_SESSION['REFERER'] != '')
    $referer = $_SESSION['REFERER'];

$validationresults = TRUE;
$registered = TRUE;
$recaptchavalidation = TRUE;
$userCol = ""; //"user_email";
$userCond = ""; //"user_email='$user'";
//Trapped brute force attackers and give them more hard work by providing a captcha-protected page

$iptocheck = $_SERVER['REMOTE_ADDR'];
$iptocheck = MySQL::filter($iptocheck); //mysql_real_escape_string($iptocheck);

$fetchdefault = array(
    'table' => 'ipcheck',
    'fields' => 'loggedip',
    'condition' => "loggedip = '" . $iptocheck . "'"
);
/*
if ($fetch = MySQL::row($fetchdefault)) {
    $resultx = MySQL::query("SELECT `failedattempts` FROM `ipcheck` WHERE `loggedip`='$iptocheck'", false, false);
    $loginattempts_total = $resultx[0]['failedattempts'];

    if ($loginattempts_total > $maxfailedattempt) {

//too many failed attempts allowed, redirect and give 403 forbidden.
        $_SESSION['error'] = array(
            'type' => 'warning',
            'messages' => array(
                'You attempted to login 4 times without succes!',
                'The system has You temporarly disabled for security reason!',
                'Please, contact your registrar'
            )
        );
//to many loginattempt
        header(sprintf("Location: /")); //forbidden_url

//header(sprintf("Location: %s", $redirectback));	//forbidden_url
        exit;
    }
}
*/
/**
 * LOGGED_IN VALTOZO DEKLARALASA HA SZUKSEGES
 */
//Check if a user has logged-in
if (!isset($_SESSION['logged_in'])) {
    $_SESSION['logged_in'] = FALSE;
}


/**
 * HA A USER LOGIN OLDALROL JOTT, AKKOR MEGPROBALOM BEJELENTKEZTETNI
 */
//Check if the form is submitted
if ((isset($_POST["pass"])) && (isset($_POST["user"])) && ($_SESSION['LAST_ACTIVITY'] == FALSE)) {

//Username and password has been submitted by the user
//Receive and sanitize the submitted information
    unset($_SESSION['error']);

    $user = sanitize($_POST["user"]);
    $pass = sanitize($_POST["pass"]);

    function isEmail($email)
    {
        return preg_match(EMAIL_REGEX, $email) ? TRUE : FALSE;
    }

    function isUserID($username)
    {
        return preg_match('/^[a-z\d_]{5,20}$/i', $username) ? TRUE : FALSE;
    }

//username or user email
    $userCol = "user_email";
    $userCond = "user_email='$user'";


//validate username
    $fetchdefault = array(
        'table' => 'user_u',
        'fields' => $userCol,
        'condition' => $userCond.' AND deleted = 0'
    );

    if (!($fetch = MySQL::row($fetchdefault))) {
//no records of username in database
//user is not yet registered
        $registered = FALSE;
    }

    if ($registered == TRUE) {

//Grab login attempts from MySQL database for a corresponding username
        $result1 = MySQL::query("SELECT `loginattempt` FROM `user_u` WHERE " . $userCond, false, false);
//$row = mysql_fetch_array($result1);
        $loginattempts_username = $result1[0]['loginattempt'];

    }

    $fetchdefault = array(
        'table' => 'user_u',
        'fields' => $userCol,
        'condition' => $userCond . ' AND approved = 1'
    );
//ellenorizzuk, hogy aktivalt-e a user
    if (($registered == TRUE) && !($fetch = MySQL::row($fetchdefault))) {

        $_SESSION['error'] = array(
            'type' => 'warning',
            'messages' => array(
                'You are not activated, therefore You can\'t use the system.',
                'Find your registration email, and follow the instructions!',
                'Otherwise please contact your administrator, who has registered You!'
            )
        );
//to many loginattempt
        header(sprintf("Location: /")); //forbidden_url
        exit;
    }


    if (($loginattempts_username > 3) || ($loginattempts_total > 3)) {
//Require those user with login attempts failed records to
//submit captcha and validate recaptcha
        $_SESSION['error'] = array(
            'type' => 'error',
            'messages' => array(
                'You attempted to login 4 times without success!',
                'The system has You temporarly disabled for security reason!',
                'Please contact your registrar.'
            )
        );
//to many loginattempt
        header(sprintf("Location: ")); //forbidden_url
        exit;
    }

//Nincs regisztralva
    if ($registered == FALSE) {
        $_SESSION['error'] = array(
            'type' => 'error',
            'messages' => array(
                'You are not registered!'
            )
        );

        header(sprintf("Location: /")); //forbidden_url
        exit;
    }

//Get correct hashed password based on given username stored in MySQL database
    if ($registered == TRUE) {
//username is registered in database, now get the hashed password
        $result = MySQL::query("SELECT `pwd` FROM `user_u` WHERE " . $userCond, false, false);
//$row = mysql_fetch_array($result);
        $correctpassword = $result[0]['pwd'];
        $salt = substr($correctpassword, 0, 64);
        $correcthash = substr($correctpassword, 64, 64);
        $userhash = hash("sha256", $salt . $pass);
    }

    if ((!($userhash == $correcthash)) || ($registered == FALSE)) {
//|| ($recaptchavalidation==FALSE)
//($correctpassword == $userhash ) {

//user login validation fails

        $validationresults = FALSE;

        
//log login failed attempts to database

        if ($registered == TRUE) {
            $loginattempts_username = $loginattempts_username + 1;
            $loginattempts_username = intval($loginattempts_username);

//update login attempt records

            MySQL::execute("UPDATE `user_u` SET `loginattempt` = '$loginattempts_username' WHERE " . $userCond);

//Possible brute force attacker is targeting registered usernames
//check if has some IP address records
            $fetchdefault = array(
                'table' => 'ipcheck',
                'fields' => 'loggedip',
                'condition' => "loggedip='" . $iptocheck . "'"
            );

            if (!($fetch = MySQL::row($fetchdefault))) {

//no records
//insert failed attempts

                $loginattempts_total = 1;
                $loginattempts_total = intval($loginattempts_total);
                MySQL::execute("INSERT INTO `ipcheck` (`loggedip`, `failedattempts`, `user_email`) VALUES ('$iptocheck', '$loginattempts_total', '$user')");
            } else {

//has some records, increment attempts

                $loginattempts_total = $loginattempts_total + 1;
                MySQL::execute("UPDATE `ipcheck` SET `failedattempts` = '$loginattempts_total' WHERE `loggedip` = '$iptocheck' AND `user_email` = '$user'");
            }
            
            
            /*
            check , if password is mismach
            
            */
            if (!($userhash == $correcthash)) 
            {
              $_SESSION['error'] = array(
                  'type' => 'error',
                  'messages' => array(
                      'Your login data is incorrect!'
                  )
              );
              
              header(sprintf("Location: /")); //forbidden_url
              exit;
            }
        }

//Possible brute force attacker is targeting randomly

        if ($registered == FALSE) {
            $fetchdefault = array(
                'table' => 'ipcheck',
                'fields' => 'loggedip',
                'condition' => "loggedip='" . $iptocheck . "'"
            );
            if (!($fetch = MySQL::row($fetchdefault))) {

//no records
//insert failed attempts

                $loginattempts_total = 1;
                $loginattempts_total = intval($loginattempts_total);
                MySQL::execute("INSERT INTO `ipcheck` (`loggedip`, `failedattempts`) VALUES ('$iptocheck', '$loginattempts_total')");
            } else {

//has some records, increment attempts

                $loginattempts_total = $loginattempts_total + 1;
                MySQL::execute("UPDATE `ipcheck` SET `failedattempts` = '$loginattempts_total' WHERE `loggedip` = '$iptocheck'");
            }
        }

        
    } else {
//user successfully authenticates with the provided username and password
        function GenKey($length = 11)
        {
            $password = "";
            $possible = "0123456789bcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
            $i = 0;
            while ($i < $length) {
                $char = substr($possible, mt_rand(0, strlen($possible) - 1), 1);
                if (!strstr($password, $char)) {
                    $password .= $char;
                    $i++;
                }
            }
            return $password;
        }

//Reset login attempts for a specific username to 0 as well as the ip address
        $loginattempts_username = 0;
        $loginattempts_total = 0;
        $loginattempts_username = intval($loginattempts_username);
        $loginattempts_total = intval($loginattempts_total);
        $stamp = time();
        $ckey = GenKey();
        MySQL::execute("UPDATE `user_u` SET `ctime`='$stamp', `ckey` = '$ckey', `loginattempt` = '$loginattempts_username' WHERE " . $userCond);
        MySQL::execute("UPDATE `ipcheck` SET `failedattempts` = '$loginattempts_total' WHERE `loggedip` = '$iptocheck'");

//Generate unique signature of the user based on IP address
//and the browser then append it to session
//This will be used to authenticate the user session
//To make sure it belongs to an authorized user and not to anyone else.
//generate random salt
        function genRandomString()
        {
//credits: http://bit.ly/a9rDYd
            $string = '';
            $length = 50;
            $characters = "0123456789abcdef";
            for ($p = 0; $p < $length; $p++) {
                $string .= $characters[mt_rand(0, strlen($characters))];
            }

            return $string;
        }

        $random = genRandomString();
        $salt_ip = substr($random, 0, $length_salt);

//hash the ip address, user-agent and the salt
        $useragent = $_SERVER["HTTP_USER_AGENT"];
        $hash_user = sha1($salt_ip . $iptocheck . $useragent);

//concatenate the salt and the hash to form a signature
        $signature = $salt_ip . $hash_user;

//Regenerate session id prior to setting any session variable
//to mitigate session fixation attacks

        session_regenerate_id();

//Finally store user unique signature in the session
//and set logged_in to TRUE as well as start activity time
        $result = MySQL::query("SELECT u_id, office_id, office_nametag, keresztnev, full_name, userlevel FROM `user_u` WHERE " . $userCond . " LIMIT 1", false, false);
        foreach ($result as $row) {
            $tempRow[] = $row;
        }

        $uid = $tempRow[0]['u_id'];
        $officeid = $tempRow[0]['office_id'];
        $datetime = date('Y-m-d H:i:s', $stamp);
        MySQL::execute("INSERT INTO `stat_login` (`u_id`, `office_id`, `ctime`, `loginDate`) VALUES ('$uid', '$officeid', '$stamp', '$datetime')");

        /**
         * SESSION VALTOZOK BEALLITASA
         */
        foreach ($tempRow[0] as $k => $v) {
            $_SESSION[$k] = $v;
        }

        $result = MySQL::query("SELECT office_type FROM `office` WHERE office_id=" . $officeid . " LIMIT 1", false, false);
        $_SESSION['officeType'] = $result[0]['office_type'];

        $_SESSION['signature'] = $signature;
        $_SESSION['logged_in'] = TRUE;
        $_SESSION['database'] = $db;
        $_SESSION['logged_in_time'] = $stamp;
        $_SESSION['LAST_ACTIVITY'] = time();

//if office member logged in, redirect to "office".domain.tld
//preg_match('/([^.]+)\.skill\.madein\.hu/', $_SERVER['SERVER_NAME'], $matches);
//$currentCookieParams = session_get_cookie_params();
        $lifetime = 30 * 60;
        $currentCookieParams["lifetime"] = time() + $lifetime;
        $currentCookieParams["path"] = '/';
        $rootDomain = '.' . DOMAINTAG;
        $currentCookieParams["secure"] = 'no';
        $currentCookieParams["httponly"] = '';
        session_set_cookie_params(
            $currentCookieParams["lifetime"],
            $currentCookieParams["path"],
            $rootDomain,
            $currentCookieParams["secure"],
            $currentCookieParams["httponly"]
        );

        ActionLogger::addToActionLog('user.login',$u=new User($_SESSION['u_id']),$u->getDBField('user_email'),array());

        unset($_POST);

        /**
         * REDIRECT
         */
        header('Location: ' . connectionType() . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/home/');

        exit();
    }
} else {
    /**
     * REDIRECT
     */
    header('Location: ' . connectionType() . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/home/');

    exit();
}

if (!$_SESSION['logged_in']) {
    unset($_SESSION['signature']);
    unset($_SESSION['logged_in']);
    unset($_SESSION['logged_in_time']);
    unset($_SESSION['LAST_ACTIVITY']);
    header('Location: /');
    return "papo2";
    exit();
}
