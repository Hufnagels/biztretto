<?php
require($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');

require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
echo '<!DOCTYPE html>';

$pageTitle = 'SKILLBI.COM';
$protocol = connectionType();
?>
<!--[if lt IE 7 ]>
<html lang="<?=$pageLanguage;?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>
<html lang="<?=$pageLanguage;?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>
<html lang="<?=$pageLanguage;?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>
<html lang="<?=$pageLanguage;?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!-->
<html lang="<?= $pageLanguage; ?>"> <!--<![endif]-->
<head>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/jqueryui/jquery-ui-1.9.1.custom.min.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap-responsive.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/css/fonts.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/login/overwrite.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/login/style.css"/>
    <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/login/default.css"/>
</head>
<body class="guest">
<div id="wrapper">
    <header>
        <div class="container">
            <!-- hidden top area toggle link -->
            <div id="header-hidden-link">
                <a href="#" class="toggle-link" title="Click me you'll get a surprise" data-target=".hidden-top"><i></i>Open</a>
            </div>
            <!-- end toggle link -->

            <div class="row nomargin">
                <div class="span12">
                    <div class="headnav">
                        <ul>

                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="span4">
                    <a href="index.html">
                        <div class="logo">
                            <div class="pull-left ">
                                <img src="<?= $protocol . IMG_SITE_URL; ?>/logo.png" width="40" height="40" class="logo img-circle"/>
                            </div>
                            <div class="pull-left" style="margin-left: 10px;"><h1><?=$pageTitle;?></h1>
                                <h6>sharE-learning</h6></div>
                        </div>
                    </a>
                </div>

            </div>
        </div>
    </header>

    <section id="inner-headline">
        <div class="container">
            <div class="row">
                <div class="span12">
                    <div class="inner-heading">
                        <h2 id="linkData">Activate your account </h2>
                    </div>
                </div>

            </div>
    </section>

    <section id="content">
        <div class="container">
            <div class="row special">
                <div class="span12">
                    <?
                    if (!isset($_POST['doAuth'])) {
                        ?>
                        <form method="post" name="authForm" id="authForm" action="/auth.php" class="form-inline" accept-charset="UTF-8" autocomplete="off">
                            <h6>Please, give your email and password</h6>

                            <div class="control-group">
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="icon-envelope"></i></span><input type="text" name="user" placeholder="Type your e-mail…" value=""/>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="icon-key"></i></span><input type="password" name="pass" placeholder="Type your password…" value=""/>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <div class="controls">
                                    <div class="input-prepend">
                                        <span class="add-on"><i class="icon-wrench"></i></span><input type="text" name="auth" placeholder="Your key" value="<?= $_GET['key']; ?>"/>
                                    </div>
                                </div>
                            </div>
                            <div class="clearfix" style="margin-top:10px;"></div>
                            <div class="control-group">
                                <div class="controls">
                                    <!--<button type="button" class="btn btn-dark" data-toggle="button" id="rememberMe">I remember you</button>-->
                                    <input type="submit" class="btn btn-r btn-dark" id="doAuth" name="doAuth" value="Activate">
                                </div>
                            </div>
                        </form>
                    <?
                    } else {
                        require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/class/class.encrypt.php');
                        //$m = new mysql();

                        $email = MySQL::filter($_POST['user']);
                        $pwd = MySQL::filter($_POST['pass']);
                        $encrypted = MySQL::filter($_POST['auth']);

                        $domain = User::globalSkillDatabaseNameByEmail($email);

                        if (MySQL::isExistsInstance(DB_PREFIX . $domain)) MySQL::changeDB($domain);

                        function isEmail($email)
                        {
                            return preg_match('/^\S+@[\w\d.-]{2,}\.[\w]{2,6}$/iU', $email) ? TRUE : FALSE;
                        }

                        $userCol = "user_email";
                        $userCond = "user_email='$email'";

                        //get data from db
                        $sql = 'SELECT user_email, pwd, cryptedText, activation_code, activeState, approved FROM user_u WHERE user_email="'.MySQL::filter($email).'" LIMIT 1';

                        $res = MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc);

                        $registered = $crypted = false; $errorArray = array();

                        if ((count($res) == 1) && $res[0]['activeState'] == 1 && $res[0]['approved'] == 1) {
                            $errorArray['error'] = array(
                                'type' => 'warning',
                                'messages' => array(
                                    'Youre account already activated!',
                                    'You\'ll be redirected in 10 seconds to login page!',
                                    '1'
                                )
                            );
                        } else {
                            if ((count($res) == 1) && $email !== '' && !is_numeric($email)) {
                                $registered = TRUE;

                                //ell, hogy a pwd stimmel-e
                                $correctpassword = $res[0]['pwd'];
                                $salt = substr($correctpassword, 0, 64);
                                $correcthash = substr($correctpassword, 64, 64);
                                $userhash = hash("sha256", $salt . $pwd);
                                if ((!($userhash == $correcthash))) {
                                    $passmatch = FALSE;
                                } else {
                                    $passmatch = TRUE;
                                }
                                //ell, hogy az cryptedText stimmel-e
                                $enc = new Encryption();
                                $enc->addKey($email);
                                $decrypted = $enc->decode($res[0]['cryptedText']);
                                $activation_code = $res[0]['activation_code'];

                                if ((!($activation_code == $decrypted))) {
                                    $crypted = FALSE;
                                } else {
                                    $crypted = TRUE;
                                }

                                if ($registered && $passmatch && $crypted) {
                                    //activate user
                                    $tmpUserObject = User::getUserObjectByEmail($email);
                                    $tmpUserObject->setDBField('approved',1);
                                    $tmpUserObject->setDBField('activation_time',date('Y-m-d H:i:s', time()));
                                    $tmpUserObject->setDBField('activeState',1);

                                    ActionLogger::addToActionLog('user.auth',$tmpUserObject,'user activation success',array());

                                    if (!$tmpUserObject->save()) {
                                        $errorArray['error'] = array(
                                            'type' => 'error',
                                            'messages' => array(
                                                'Data can\'t updated in db!'
                                            )
                                        );
                                    }
                                } else {
                                    //auth adatok hibasak
                                    $errorArray['error'] = array(
                                        'type' => 'error',
                                        'messages' => array(
                                            'You are unathorized!'
                                        )
                                    );
                                }
                            } else {
                                $errorArray['error'] = array(
                                    'type' => 'error',
                                    'messages' => array(
                                        'Youre data doesn\'t exist!'
                                    )
                                );
                            }
                        }
                        if (isset($errorArray) && !empty($errorArray)) {
                            echo'<div class="container"><div class="row"><div class="span6 error" style="width:auto;margin: 30px 0 0 30px;"><div class="alert alert-' . $errorArray['error']['type'] . '" style="padding: 5px 5px 5px 10px;">';

                            foreach ($errorArray['error']['messages'] as $key => $error) {
                                if ($error !== '1') echo '<p style="margin:5px;">' . $error . '</p>';
                                if ($error == '1') header("refresh:10;url=/");
                            }
                            echo '</div></div></div></div>';
                        } else {
                            echo'<div class="container"><div class="row"><div class="span6 error" style="width:auto;margin: 30px 0 0 30px;"><div class="alert alert-success" style="padding: 5px 5px 5px 10px;">';
                            echo '<p style="margin:5px;">You have successfully activated!</p>';
                            echo '<p style="margin:5px;">You\'ll be redirected in 10 seconds to login page!</p>';
                            echo '</div></div></div></div>';
                            header("refresh:10;url=/");
                        }
                    }
                    ?>
                </div>

            </div>
        </div>
    </section>

    <footer>
        <div class="container">
            <div class="row">
                <div class="span3">
                    <div class="widget">
                        <h5 class="widgetheading">Browse pages</h5>
                        <ul class="link-list">
                            <li class=""><a href="/index.html">Home</a></li>
                            <!--<li class=""><a href="/free.html">Free trainings</a></li>
                            <li class=""><a href="/method.html">Methodes</a></li>-->
                            <li class=""><a href="/price.html">Pricing</a></li>
                            <li class=""><a href="/about.html">About us</a></li>
                            <li><a href="/contact.html">Contact</a></li>
                        </ul>
                    </div>
                </div>
                <div class="span3">
                    <div class="widget">
                        <h5 class="widgetheading">Important stuff</h5>
                        <ul class="link-list">

                            <li><a href="/terms.html">Terms and conditions</a></li>
                            <li><a href="/privacy.html">Privacy policy</a></li>

                        </ul>

                    </div>
                </div>
                <div class="span3">
                    <div class="widget">
                        <h5 class="widgetheading"></h5>

                        <div class="flickr_badge">

                        </div>
                        <div class="clear"></div>
                    </div>
                </div>
                <div class="span3">
                    <div class="widget">
                        <h5 class="widgetheading">Get in touch with us</h5>
                        <address>
                            <strong>SILLBI.COM LLP.</strong>
                        </address>
                        <p>
                            <i class="icon-envelope-alt"></i> <a href="mailto:contact@skillbi.com">contact@skillbi.com</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div id="sub-footer">
            <div class="container">
                <div class="row">
                    <div class="span6">
                        <div class="copyright">
                            <p><span>SILLBI.COM © 2013 All right reserved. </span></p>
                        </div>
                    </div>

                    <div class="span6">
                        <ul class="social-network hidden">
                            <li>
                                <a href="#" data-placement="bottom" title="" data-original-title="Facebook"><i class="icon-facebook icon-square"></i></a>
                            </li>
                            <li>
                                <a href="#" data-placement="bottom" title="" data-original-title="Twitter"><i class="icon-twitter icon-square"></i></a>
                            </li>
                            <li>
                                <a href="#" data-placement="bottom" title="" data-original-title="Linkedin"><i class="icon-linkedin icon-square"></i></a>
                            </li>

                            <li>
                                <a href="#" data-placement="bottom" title="" data-original-title="Google plus"><i class="icon-google-plus icon-square"></i></a>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </footer>
</div>
<div class="clearfix"></div>

</body>
</html>