<?php

    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
    SkillGlobalConfig::$settings['auth.disablecheckxhttprequestheader'] = true;
    //require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
	//require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
    

	preg_match('/([^.]+)\\' . DOMAINTAG_PREGSTRING . '/', $_SERVER['SERVER_NAME'], $matches);
//empty if main domain, else return office_nametag as subdomain

if (isset($matches) && empty($matches)) {
    //we are on main domain
    //we can only login
    //rule: / -> index.html
    //all other -> 404
//echo 'STEP 0 - matches';
    $includeFile = 'pages/index.html';

    if ($_SERVER['SERVER_NAME'] == DOMAINTAG && $_SERVER['QUERY_STRING'] !== '/') {
        $includeFile = 'errordocuments/404.php';
    }
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

        //office_nametag, user_id, entry_id
        $reqCount = count($request);
        $temp = explode('-', $request[$reqCount - 1]);
        unset($request[$reqCount - 1]);

//echo 'STEP 2 - remove userID and officeNametag from request array';

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

        switch ($page) {
            case 'slideshow'      :
			
			require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
            require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');
                if (Access::getAccessLevel()>=5)
                    $includeFile = '../phplib/slideeditor2/showSlideshow.php';
                break;
            case 'training'       :
                if (Access::checkAccessTrainingSlideShow($_REQUEST['trainingId'],$_REQUEST['showId']))
                    $includeFile = '../phplib/home/showTraining.php';
                break;

            case 'public'       :

                //if (Access::checkAccessTrainingSlideShow($_REQUEST['trainingId'],$_REQUEST['showId']))
                    $includeFile = $_SERVER['DOCUMENT_ROOT'] . '/../phplib/home/showPublicTraining.php';

                break;
			
			case 'public2'       :

                //if (Access::checkAccessTrainingSlideShow($_REQUEST['trainingId'],$_REQUEST['showId']))
                    $includeFile = $_SERVER['DOCUMENT_ROOT'] . '/../phplib/home/showPublicTrainingR.php';

                break;

            case 'preview'       :
                if (Access::getAccessLevel()>=5)
                    $includeFile = '../phplib/publication/previewTraining.php';
                break;
            case 'review'       :
                //@todo security
                $includeFile = '../phplib/home/reviewTraining.php';
                break;

            default:
                $includeFile = 'errordocuments/404.php';
                break;
        }
    } else {
        $includeFile = 'pages/index.html';
    }

}

//check if file exist
if (file_exists($includeFile)) {
    SkillGlobalConfig::$settings['auth.disablecheckxhttprequestheader'] = true;
    if (!strstr(basename($includeFile), '.html')  && !(isset($page) && $page == 'public') && !(isset($page) && $page == 'public2') ){
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
	    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');
	}
	
    include $includeFile;
} else {
    include ('errordocuments/404.php');
}
?>