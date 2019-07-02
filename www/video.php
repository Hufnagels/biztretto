<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
$protocol = connectionType();


if(isset($_SESSION['office_nametag'])){
	$imageURL = $protocol.(isset($_SESSION['office_nametag']) ? $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/default/thumbnail/' : 'media.'.DOMAINTAG . '/');
	$videodomain = $protocol.(isset($_SESSION['office_nametag']) ? $_SESSION['office_nametag'].'.'.DOMAINTAG.'/streamvideo/' : DOMAINTAG.'/streamvideo/');
	//$imageURL = 'trillala.skillbi.local/media/';
	//get subdomain
	$subdomain = isset($_SESSION['office_nametag']) ? str_replace('.'.DOMAINTAG,'',$_SERVER['HTTP_HOST']).'/default/' : '';

    SkillGlobalConfig::$settings['auth.disablecheckxhttprequestheader'] = true;
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');
	
	$formArray0 = array();
	$formArray0['database'] = DB_PREFIX.$matches[1];
	$formArray0['office_id'] = 1;
	$formArray0['office_nametag'] = $_SESSION['office_nametag'];
	
} else {
	preg_match('/([^.]+)\\' . DOMAINTAG_PREGSTRING . '/', $_SERVER['SERVER_NAME'], $matches);
	
	
	///////////////////////////////////////////////////////////////
	//Megkeresem az elso slideshowt
	///////////////////////////////////////////////////////////////
	$formArray0 = array();
	$formArray0['database'] = DB_PREFIX.$matches[1];
	$formArray0['office_id'] = 1;
	$formArray0['office_nametag'] = $matches[1];
	//connect to db
	MySQL::connect(DB_HOST, DB_USER, DB_PASS, ((isset($formArray0['database'])) and $formArray0['database']) ? $formArray0['database'] : null);
	$imageURL = $protocol.(isset($formArray0['office_nametag']) ? $formArray0['office_nametag'] . '.' . DOMAINTAG . '/media/default/thumbnail/' : 'media.'.DOMAINTAG . '/');
	$videodomain = $protocol.(isset($formArray0['office_nametag']) ? $formArray0['office_nametag'].'.'.DOMAINTAG.'/streamvideo/' : DOMAINTAG.'/streamvideo/');
	$subdomain = isset($formArray0['office_nametag']) ? str_replace('.'.DOMAINTAG,'',$_SERVER['HTTP_HOST']).'/default/' : '';

}

//print_r($_GET);
//printR($formArray0);
//exit;

//get filename + file
$filename = filter_input(INPUT_GET, 'file', FILTER_SANITIZE_STRING);
$file = $_SERVER['DOCUMENT_ROOT'] .'/../media/'.$subdomain.$filename;
//echo '$file video.php:'.$file; exit;
//get size
$w = filter_input(INPUT_GET, 'w', FILTER_SANITIZE_NUMBER_FLOAT);
$h = filter_input(INPUT_GET, 'h', FILTER_SANITIZE_NUMBER_FLOAT);
$name = pathinfo($file,PATHINFO_FILENAME);
$extension = pathinfo($file,PATHINFO_EXTENSION);


exec("ffprobe -v quiet -print_format json -show_format -show_streams " . $file, $response);
$ffprobeResponse = json_decode(implode(null, $response));
//printR($response);
//exit;
switch ($extension) {
    case 'wmv':
        $videoWidth      = $ffprobeResponse->streams[1]->width;
        $videoHeight     = $ffprobeResponse->streams[1]->height;
        $dur             = gmdate("H:i:s", (int)$ffprobeResponse->streams[1]->duration);
        break;
    default:
        $videoWidth      = $ffprobeResponse->streams[0]->width;
        $videoHeight     = $ffprobeResponse->streams[0]->height;
        $dur             = gmdate("H:i:s", (int)$ffprobeResponse->streams[0]->duration);
        break;
}



$slideWidth = isset($formArray0['office_nametag']) ? 1024 : 100;
$scale =$videoHeight/$videoWidth;

$newWidth = $videoWidth > 1024 ? 1000 : $videoWidth;//isset($formArray0['office_nametag']) ? $slideWidth* $w/100*1 : $videoWidth;
$newHeight = $videoHeight > 500 ? 500 : $videoHeight;// isset($formArray0['office_nametag']) ? $newWidth * $scale : $videoHeight;



echo '<html style="height: 100%;">';
    echo '<head>';
    echo '<meta name="viewport" content="width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=no">';
    echo '<meta name="apple-mobile-web-app-capable" content="yes">';
    echo '<meta name="apple-mobile-web-app-status-bar-style" content="black">';
    echo '<meta name="viewport" content="width=1024">';
    echo '<title>SLIDESHOW PREVIEW</title>';
    //echo '<!--<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/video.css">-->';
    
    echo '<script type="text/javascript" charset="utf-8" src="/js/jquery-1.8.3.min.js"></script>';
    //echo '<script type="text/javascript" charset="utf-8" src="/assets/me/mediaelement-and-player.min.js"></script>';
    //echo '<link rel="stylesheet" href="/assets/me/mediaelementplayer.css" />';

echo '</head>';
echo '<body class="'.(isset($formArray0['office_nametag']) ? $formArray0['office_nametag'] : '').'" style="overflow:hidden;margin:0;padding:0;">'; // onunload="MediaElementPlayer.globalUnbind()"
  echo'<video id="vid1" preload="none" controls width="'.$newWidth.'px" height="'.$newHeight.'px" class="" poster="'.$imageURL.$name.'.jpg">';
      echo'<source src="'.$videodomain.$name.'.mp4" type="video/mp4">';
      echo'<source src="'.$videodomain.$name.'.ogg" type="video/ogg">';
      echo'<p>Video Playback Not Supported</p>';
  echo'</video>';
echo'</body>';
?>
<script type="text/javascript">
<?
/*
     MediaElementPlayer.prototype.enterFullScreen_org = MediaElementPlayer.prototype.enterFullScreen;
    MediaElementPlayer.prototype.enterFullScreen = function () {
        // code goes here
        this.enterFullScreen_org();
    }

    MediaElementPlayer.prototype.exitFullScreen_org = MediaElementPlayer.prototype.exitFullScreen;
    MediaElementPlayer.prototype.exitFullScreen = function () {
        // code goes here
        this.exitFullScreen_org();
    }

 */
?>



    var $window = $(window);
    var w = window, d = document, e = d.documentElement, g = d.getElementsByTagName('body')[0];
    var viewportWidth, viewportHeight;
    viewportWidth = w.innerWidth || e.clientWidth || g.clientWidth;
    viewportHeight = w.innerHeight || e.clientHeight || g.clientHeight;
    var _eventHandlers = {};
	
    //register/unregister attached events to nodes
    function addEventO(node, event, handler, capture, _eventHandlers) {
        if (node == '')
            return false;
        if (!(node in _eventHandlers))
            _eventHandlers[node] = {};
        if (!(event in _eventHandlers[node]))
            _eventHandlers[node][event] = [];
        _eventHandlers[node][event].push([node, handler, capture]);
        if (node.addEventListener)
            node.addEventListener(event, handler, capture);
        else
            node.attachEvent('on' + event, handler, capture);
    };

    $(function () {

		var video = document.getElementById('vid1');
		video.volume = 0.3;
		video.ended = function(e) {
			alert('video ended');
		  //parent.postMessage('VideoMessage', '*');
		}
		
		
/*		
        $(video).attr('width',viewportWidth+'px');
        $(video).attr('height',viewportHeight+'px');
        //$('video,audio').mediaelementplayer();

        

        $(window).resize(function () {
            viewportWidth = w.innerWidth || e.clientWidth || g.clientWidth;
            viewportHeight = w.innerHeight || e.clientHeight || g.clientHeight;
            $(video).attr('width',viewportWidth+'px');
            $(video).attr('height',viewportHeight+'px');
        });
*/
    });
</script>
</html>