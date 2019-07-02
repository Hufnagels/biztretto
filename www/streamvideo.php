<?php


    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
    $protocol = connectionType();
	
	
	if(isset($_SESSION['office_nametag'])){
		$formArray0 = array();
		$formArray0['office_nametag'] = $_SESSION['office_nametag'];
		SkillGlobalConfig::$settings['auth.disablecheckxhttprequestheader'] = true;
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');
		
	} else {
		preg_match('/([^.]+)\\' . DOMAINTAG_PREGSTRING . '/', $_SERVER['SERVER_NAME'], $matches);
		$formArray0 = array();
		$formArray0['database'] = DB_PREFIX.$matches[1];
		$formArray0['office_id'] = 1;
		$formArray0['office_nametag'] = $matches[1];
		//connect to db
		MySQL::connect(DB_HOST, DB_USER, DB_PASS, ((isset($formArray0['database'])) and $formArray0['database']) ? $formArray0['database'] : null);

	}
	
    $imageURL = $formArray0['office_nametag'] . '.' . DOMAINTAG . '/media/';
    //get subdomain
    $subdomain = isset($formArray0['office_nametag']) ? str_replace('.'.DOMAINTAG,'',$_SERVER['HTTP_HOST']).'/default/' : '';
    //get subdomain
    //$subdomain = str_replace('.'.DOMAINTAG,'',$_SERVER['HTTP_HOST']).'/default/';
    //echo 'subdomain streamvideo:'.$subdomain; exit;

    //SkillGlobalConfig::$settings['auth.disablecheckxhttprequestheader'] = true;
    //require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
    //require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');


//$imageURL = 'trillala.skillbi.local/media/';


//get filename + file
$filename = filter_input(INPUT_GET, 'filename', FILTER_SANITIZE_STRING);
$file = $_SERVER['DOCUMENT_ROOT'] .'/../media/'.$subdomain.$filename;
//print_r($file);
//exit;

$filesize = filesize($file);
$ctype = mime_content_type($file);

$offset = 0;
$length = $filesize;

header('Content-Type: ' . $ctype);
if ( empty($_SERVER['HTTP_RANGE']) ){
    header("Content-Length: $filesize");
    $fh = fopen($file, "rb") or die("Could not open file: " .$file);
    # output file
    while(!feof($fh)){
         # output file without bandwidth limiting
        echo fread($fh, $filesize);
    }
    fclose($fh);
} else {   
//violes rfc2616, which requires ignoring  the header if it's invalid
  rangeDownload($file);
}


//////////////////////////////////////////////
function rangeDownload($file) {
  $_SESSION['LAST_ACTIVITY'] = time();
	$fp = @fopen($file, 'rb');
 
	$size   = filesize($file); // File size
	$length = $size;           // Content length
	$start  = 0;               // Start byte
	$end    = $size - 1;       // End byte
	// Now that we've gotten so far without errors we send the accept range header
	/* At the moment we only support single ranges.
	 * Multiple ranges requires some more work to ensure it works correctly
	 * and comply with the spesifications: http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
	 *
	 * Multirange support annouces itself with:
	 * header('Accept-Ranges: bytes');
	 *
	 * Multirange content must be sent with multipart/byteranges mediatype,
	 * (mediatype = mimetype)
	 * as well as a boundry header to indicate the various chunks of data.
	 */
	header("Accept-Ranges: 0-$length");
	// header('Accept-Ranges: bytes');
	// multipart/byteranges
	// http://www.w3.org/Protocols/rfc2616/rfc2616-sec19.html#sec19.2
	if (isset($_SERVER['HTTP_RANGE'])) {
 
		$c_start = $start;
		$c_end   = $end;
		// Extract the range string
		list(, $range) = explode('=', $_SERVER['HTTP_RANGE'], 2);
		// Make sure the client hasn't sent us a multibyte range
		if (strpos($range, ',') !== false) {
 
			// (?) Shoud this be issued here, or should the first
			// range be used? Or should the header be ignored and
			// we output the whole content?
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			// (?) Echo some info to the client?
			exit;
		}
		// If the range starts with an '-' we start from the beginning
		// If not, we forward the file pointer
		// And make sure to get the end byte if spesified
		if ($range == '-') {
 
			// The n-number of the last bytes is requested
			$c_start = $size - substr($range, 1);
		}
		else {
 
			$range  = explode('-', $range);
			$c_start = $range[0];
			$c_end   = (isset($range[1]) && is_numeric($range[1])) ? $range[1] : $size;
		}
		/* Check the range and make sure it's treated according to the specs.
		 * http://www.w3.org/Protocols/rfc2616/rfc2616-sec14.html
		 */
		// End bytes can not be larger than $end.
		$c_end = ($c_end > $end) ? $end : $c_end;
		// Validate the requested range and return an error if it's not correct.
		if ($c_start > $c_end || $c_start > $size - 1 || $c_end >= $size) {
 
			header('HTTP/1.1 416 Requested Range Not Satisfiable');
			header("Content-Range: bytes $start-$end/$size");
			// (?) Echo some info to the client?
			exit;
		}
		$start  = $c_start;
		$end    = $c_end;
		$length = $end - $start + 1; // Calculate new content length
		fseek($fp, $start);
    $_SESSION['LAST_ACTIVITY'] = time();
		header('HTTP/1.1 206 Partial Content');
	}
	// Notify the client the byte range we'll be outputting
	header("Content-Range: bytes $start-$end/$size");
	header("Content-Length: $length");
 
	// Start buffered download
	$buffer = 1024 * 8;
	while(!feof($fp) && ($p = ftell($fp)) <= $end) {
 
		if ($p + $buffer > $end) {
 
			// In case we're only outputtin a chunk, make sure we don't
			// read past the length
			$buffer = $end - $p + 1;
		}
		set_time_limit(0); // Reset time limit for big files
		echo fread($fp, $buffer);
		flush(); // Free up memory. Otherwise large files will trigger PHP's memory limit.
    $_SESSION['LAST_ACTIVITY'] = time();
	}
 
	fclose($fp);
 
}
?>