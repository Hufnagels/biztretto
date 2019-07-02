<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
    SkillGlobalConfig::$settings['auth.disablecheckxhttprequestheader'] = true;
//print_r($_GET);
//print_r($_SESSION);
//exit;
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');

    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/class/class.download.php');
    $folder   = MySQL::filter($_GET['folder']);
    $filename = MySQL::filter($_GET['filename']);

    $_SESSION['LAST_ACTIVITY'] = time();
    $download_path             = $_SERVER['DOCUMENT_ROOT'] . '/../media/' . $_SESSION['office_nametag'] . '/' . $folder . '/';

    $args = array(
        'download_path'   => $download_path,
        'file'            => $filename,
        'extension_check' => TRUE,
        'referrer_check'  => FALSE,
        'referrer'        => NULL,
    );
    printR($download_path);
    $download      = new chip_download($args);
    $download_hook = $download->get_download_hook();
    if ($download_hook['download'] == TRUE)
    {

        /* You can write your logic before proceeding to download */

        /* Let's download file */
        $download->get_download();

    }

?>