<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

preg_match('/([^.]+)\\' . DOMAINTAG_PREGSTRING . '/', $_SERVER['SERVER_NAME'], $matches);
//empty if main domain, else return office_nametag as subdomain

if (isset($matches) && empty($matches)) {
    $rest = str_replace('/', '', array_keys($_GET));
    //printR($rest[0]);
    //rule: / -> index.html
    //all other -> 404
    //case 'send-testrequest':
    //$includeFile = '../phplib/testRegistration.php'; break;
    switch ($rest[0]) {
        case 'send-testrequest':
            $includeFile = '../phplib/testRegistration.php';
            break;
        default :
            //$includeFile = 'pages/test.php';
            $includeFile = 'pages/index.html';
            break;
    }
    if (file_exists($includeFile))
        include $includeFile;
    else
        include ($_SERVER['DOCUMENT_ROOT'] . '/errordocuments/404.php');
    exit;
    /*
    if($_SERVER['SERVER_NAME'] == DOMAINTAG && $_SERVER['QUERY_STRING'] !== '/') {
      $includeFile = 'errordocuments/404.php';
    }*/

} else if (isset($matches[1]) && !empty($matches[1])) {
    $page = '';
    $type = '';
    $subpage = '';
    if (preg_match("/crawl/i", $_SERVER["REQUEST_URI"]) && !preg_match("/.html/i", $_SERVER["REQUEST_URI"])) {
        $request = explode('/', $_SERVER['REQUEST_URI']);

        unset($request[0]);
        unset($request[1]);
        unset($_GET);
        $request = array_values($request);

//echo 'STEP 1 - clean request array';
//printR($request);

        //office_nametag, user_id, entry_id
        $reqCount = count($request);
        $temp = explode('-', $request[$reqCount - 1]);
        unset($request[$reqCount - 1]);

//echo 'STEP 2 - remove userID and officeNametag from request array';
//printR($request);
//printR($temp);

//echo 'STEP 3 - create $_GET from request array';
        //page, type subpage
        $keysArray = array('page', 'type', 'subpage');
        $valuesArray = array('', '', '');
        $reqCount = count($request);
        for ($i = 0; $i < $reqCount; $i++) {
            $valuesArray[$i] = $request[$i];
        }
        $_GET = array_combine($keysArray, $valuesArray);
        $_GET['i'] = isset($temp[0]) ? $temp[0] : '';
        $_GET['u'] = isset($temp[1]) ? $temp[1] : '';
        $_GET['iid'] = isset($temp[2]) ? $temp[2] : '';

    } else { //end crawl
        //printR(  $_SERVER );
        $request = explode('/', $_SERVER['REQUEST_URI']);
        $_GET['page'] = isset($request[1]) ? $request[1] : $request[0];
    }

    if (isset($_GET['page'])) { //&& $_GET['page'] !='') {
        $page = isset($_GET['page']) ? $_GET['page'] : 'home';
        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $subpage = isset($_GET['subpage']) ? $_GET['subpage'] : '';
        $subpage = str_replace('/', '', $subpage);
        //$uid = $_GET['u'];
        //$oid = $_GET['i'];
        switch ($page) {

//elore definialt oldalak
            case 'index.html'     :
                $includeFile = 'pages/index.html';
                break;
            case 'about.html'     :
                $includeFile = 'pages/about.html';
                break;
            case 'method.html'    :
                $includeFile = 'pages/method.html';
                break;
            case 'contact.html'    :
                $includeFile = 'pages/contact.html';
                break;

//check for user activity
            case 'checkSession'   :
                $includeFile = 'keepalive.php';
                break;
            case 'setSession'     :
                $includeFile = 'setalive.php';
                break;

//menustruktura
            case 'admin'         :
                if (Access::checkMenuAccessByIdString('superadmin'))
                    $includeFile = 'pages/superadmin/firm.php';
                break;
            case 'home'           :
                $includeFile = 'pages/home/home.php';
                break;
            case 'myaccount':
                $includeFile = 'pages/myaccount/myaccount.php';
                break;
            case 'usermanagement' :
                if (Access::checkMenuAccessByIdString('usermanager'))
                    $includeFile = 'pages/users/userManagement.php';
                break;
            case 'mymedia'        :
                if (Access::checkMenuAccessByIdString('mymedia'))
                    $includeFile = 'pages/mymedia/mymedia.php';
                break;
            case 'slideeditor'    :
                if (Access::checkMenuAccessByIdString('editor')){
                  $includeFile = 'pages/slideeditor2/editor.php';
                  //$includeFile = 'pages/slideeditor/editor.php';
                }
                    
                break;
            case 'publication'    :
                if (Access::checkMenuAccessByIdString('publication'))
                    $includeFile = 'pages/publication/publication.php';
                break;
            case 'statistic'    :
                if (Access::checkMenuAccessByIdString('statistic'))
                    $includeFile = 'pages/statistic/statistic.php';
                break;

//ajax fileok
            case 'process'        :
                switch ($type) {
                    case 'upload'   :
                        $includeFile = '../phplib/fileupload.php';
                        break;
                    case 'message'  :
                        $includeFile = '../phplib/sendMessage.php';
                        break;
                    case 'avaliable':
                        $includeFile = '../phplib/checkNameAvaliability.php';
                        break;
                    case 'download' :
                        $includeFile = '../phplib/download.php';
                        break;
                    //usermanagemnt
                    case 'users'   :
                        if (Access::checkMenuAccessByIdString('users')) {
                            switch ($subpage) {
                                case 'handelusers'        :
                                    $includeFile = '../phplib/users/handelUsers.php';
                                    break;
                                case 'handelgroups'       :
                                    $includeFile = '../phplib/users/handelGroups.php';
                                    break;
                                case 'uploadusers'        :
                                    $includeFile = '../phplib/users/csvUpload.php';
                                    break;
                                case 'typeahead'          :
                                    $includeFile = '../phplib/users/typeahead.php';
                                    break;
                                case 'handeltraininggroups' :
                                    $includeFile = '../phplib/users/handelTrainingGroups.php';
                                    break;
                            }
                        }
                        break;
                    case 'myaccount':
                        switch ($subpage) {
                            case 'loadmyaccount':
                                $includeFile = '../phplib/myaccount/loadMyAccount.php';
                                break;
                            case 'handelmyaccount':
                                $includeFile = '../phplib/myaccount/handelMyAccount.php';
                                break;
                        }
                        break;
                    //mymedia
                    case 'mymedia':
                        if (Access::checkMenuAccessByIdString('mymedia')) {
                            switch ($subpage) {
                                case 'loadfolder'         :
                                    $includeFile = '../phplib/mymedia/loadDiskAreas.php';
                                    break;
                                case 'handelmediagroups'    :
                                    $includeFile = '../phplib/mymedia/handelMediaGroups.php';
                                    break;
                                case 'handelmediafiles'     :
                                    $includeFile = '../phplib/mymedia/handelMediaFiles.php';
                                    break;

/*
                                case 'loadmediagroups'    :
                                    $includeFile = '../phplib/mymedia/loadMediaGroups.php';
                                    break;
                                case 'loadmediafiles'     :
                                    $includeFile = '../phplib/mymedia/loadMediaFiles.php';
                                    break;
                                case 'savemediafiles'     :
                                    $includeFile = '../phplib/mymedia/saveMediaFiles.php';
                                    break;
                                case 'savemediagroup'     :
                                    $includeFile = '../phplib/mymedia/saveMediaGroup.php';
                                    break;
                                case 'savemediagroupfiles':
                                    $includeFile = '../phplib/mymedia/saveMediaGroupFiles.php';
                                    break;
                                case 'deletemediafiles'   :
                                    $includeFile = '../phplib/mymedia/deleteMediaFiles.php';
                                    break;
*/
                                case 'checkfiles'         :
                                    $includeFile = '../phplib/mymedia/checkBeforeDelete.php';
                                    break;
                            }
                        }
                        break;
                    //slideeditor
                    case 'editor' :
                        if (Access::checkMenuAccessByIdString('editor')) {
                            switch ($subpage) {
                                case 'handelslides'       :
                                    $includeFile = '../phplib/slideeditor2/handelSlides.php';
                                    break;
                                case 'handelslideshow'    :
                                    $includeFile = '../phplib/slideeditor2/handelSlideShow.php';
                                    break;
                                case 'loadslideshows'     :
                                    $includeFile = '../phplib/slideeditor2/loadSlideShowList.php';
                                    break;
                                case 'loadmediafiles'     :
                                    //$includeFile = '../phplib/slideeditor/loadMediaFiles.php';
                                    $includeFile = '../phplib/slideeditor2/loadMediaFiles.php';
                                    break;
                                case 'loadmediagroups'    :
                                    $includeFile = '../phplib/slideeditor2/loadMediaGroups.php';
                                    break;
                                case 'proxy'       :
                                    $includeFile = '../phplib/slideeditor2/proxy.php';
                                    break;/**/
                            }
                        }
                        break;
                    //publication
                    case 'publication' :
                        if (Access::checkMenuAccessByIdString('publication')) {
                            switch ($subpage) {
                                /*case 'uploadcover'        :
                                    $includeFile = '../phplib/publication/uploadCover.php';
                                    break;*/
                                case 'handelslideshow'    :
                                    $includeFile = '../phplib/publication/handelSlideShows.php';
                                    break;
                                case 'handelgroups'       :
                                    $includeFile = '../phplib/publication/handelGroups.php';
                                    break;
                                case 'handeltraining'     :
                                    $includeFile = '../phplib/publication/handelTrainings.php';
                                    break;
                                case 'handeltraininggroups' :
                                    $includeFile = '../phplib/publication/handelTrainingGroups.php';
                                    break;
                                case 'handelinstances' :
                                    $includeFile = '../phplib/publication/handelInstances.php';
                                    break;

                                case 'loadmediafiles'     : $includeFile = '../phplib/publication/loadMediaFiles.php'; break;
                                //case 'loadmediagroups'    : $includeFile = '../phplib/slideeditor/loadMediaGroups.php'; break;
                                //case 'handelattach'       : $includeFile = '../phplib/slideeditor/handelAttach.php'; break;
                            }
                        }
                        break;
                    //statistic
                    case 'statistic' :
                        if (Access::checkMenuAccessByIdString('publication')) {
                            switch ($subpage) {
                                case 'handeltraining'     :
                                    $includeFile = '../phplib/statistic/handelTraining.php';
                                    break;
                                case 'handeltrainingstat' :
                                    $includeFile = '../phplib/statistic/trainingStat.php';
                                    break;
                                case 'handelgroupstat':
                                    $includeFile = '../phplib/statistic/trainingGrpStat.php';
                                    break;
                                case 'handeluserstat':
                                    $includeFile = '../phplib/statistic/userStat.php';
                                    break;
                            }
                        }
                        break;
                    //home
                    case 'home'         :
                        switch ($subpage) {
                            //case 'handelslides'       : $includeFile = '../phplib/slideeditor/handelSlides.php'; break;
                            case 'trainings'            :
                                $includeFile = '../phplib/home/handelTrainings.php';
                                break;
                            case 'trainingdetails'            :
                                $includeFile = '../phplib/home/trainingDetails.php';
                                break;
                            //case 'handelgroups'       : $includeFile = '../phplib/publication/handelGroups.php'; break;
                            //case 'loadslideshows'     : $includeFile = '../phplib/slideeditor/loadSlideShowList.php'; break;
                            //case 'loadmediafiles'     : $includeFile = '../phplib/slideeditor/loadMediaFiles.php'; break;
                            //case 'loadmediagroups'    : $includeFile = '../phplib/slideeditor/loadMediaGroups.php'; break;
                            //case 'handelattach'       : $includeFile = '../phplib/slideeditor/handelAttach.php'; break;
                        }
                        break;

                    case 'avaliable':
                        switch ($subpage) {
                            case 'user'     :
                                $includeFile = 'pages/process-avaluser.php';
                                break;
                            case 'email'    :
                                $includeFile = 'pages/process-avalOfficeEmail.php';
                                break;
                        }
                        break;
                    case 'avatar':
                        switch ($subpage) {
                            case 'rotate'  :
                                $includeFile = 'pages/profile/process-avatar-rotate.php';
                                break;
                        }
                        break;

                    case 'office':
                        switch ($subpage) {
                            case 'users'  :
                                $includeFile = 'pages/users/process-users.php';
                                break;
                            case 'update'  :
                                $includeFile = 'pages/process-swf-sortable-save.php';
                                break;
                        }
                        break;

                }
                break;
            //case '404': $includeFile = 'errordocuments/404.php'; break;
            //default: $includeFile = 'pages/home/home.php'; break;
            default:
                $includeFile = 'errordocuments/404.php';
                break;
        }
    } else {
        $includeFile = 'pages/index.html';
        //
    }
}
//check if file exist
if (file_exists($includeFile)) {
    if (!strstr(basename($includeFile), '.html'))
        require($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');

    include $includeFile;
} else
    include ($_SERVER['DOCUMENT_ROOT'] . '/errordocuments/404.php');
?>