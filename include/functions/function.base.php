<?
//function.base.php
//  ==================================================================================================
//  ==================================================================================================
function Lang( $string) {
 global $lang;
 return isset( $lang[ $string]) ? $lang[ $string] : "--add-- $string";
};
//  ==================================================================================================

function customError($error_level,$error_message,$error_file,$error_line,$error_context) {
  echo "<b>Error:</b> [$error_level] $error_message <br/>";
  echo "<b>Line:</b> [$error_line] $error_file <br/>";
  //include ('/twitt.admin/404.php');
  //echo "<b>Context:</b> [<br/>";
  //print_r($error_context);
  //echo "<br/>]<br/>";
}

function getAllFilesRecursive($dir)
{
    $files = Array();
    $file_tmp= glob($dir.'*',GLOB_MARK | GLOB_NOSORT);

    if (is_array($file_tmp))
        foreach($file_tmp as $item){
            if(substr($item,-1)!=DIRECTORY_SEPARATOR)
                $files[] = $item;
            else
                $files = array_merge($files,getAllFilesRecursive($item));
    }

    return $files;
}

function customErrorMysql($error_level,$error_message,$error_file,$error_line,$error_context) {
    $baseMessage1 = '<script type="javascript">showMessage({text: ';
    $baseMessage2 = ',type: "error"});</script>';
    $connMessage = '"Oops! UNABLE to CONNECT to the DATABASE!"';
    $dbMessage = '"Oops! UNABLE to CONNECT to the DATABASE!"';
    $chMessage = '"Oops! UNABLE to SET database connection ENCODING!"';
    $baseMessage1.$connMessage.$baseMessage2;
}

function customErrorNoMessage($error_level,$error_message,$error_file,$error_line,$error_context) {
  //echo "<b>Error:</b> [$error_level] $error_message <br/>";
  //echo "<b>Line:</b> [$error_line] $error_file <br/>";
  //include ('/twitt.admin/404.php');
  //echo "<b>Context:</b> [<br/>";
  //print_r($error_context);
  //echo "<br/>]<br/>";
}


//  ==================================================================================================
//  COOKIE törlés
//  ==================================================================================================

function DeleteCookie ($name) {
  if (isset($_COOKIE[$name])) {
    setcookie($name,'',time()-3600);
    unset($_COOKIE[$name]);
  }
}

function DeleteAllCookies() {
  if (isset($_SERVER['HTTP_COOKIE'])) {
    $cookies = explode(';', $_SERVER['HTTP_COOKIE']);
    foreach($cookies as $cookie) {
      $parts = explode('=', $cookie);
      $name[] = trim($parts[0]);
      $name1 = trim($parts[0]);
      //setcookie($name1,'',time()-3600);
      //DeleteCookie ($name1);
      unset($_COOKIE[$name1]);
    }
  }
  return $name;
}



//  ==================================================================================================
//  az adott $array-ban megkeresi azokat a mezőket, amelyek a $keys-ben vannak
//  es azokat torli a $array-bol
//  ==================================================================================================
function search($array, $keys){
  foreach($keys as $k) {
    if(isset($array[$k])) {
      unset($array[$k]);
    }
  }
  return $array;
}

//  ==================================================================================================
//  Creates an array by using
//  one array for keys and 
//  another for its values */
//  ==================================================================================================
if (!function_exists('array_combine')) //php5 ben van, de php4 ben nincs
{
  function array_combine($arr1,$arr2) {
     $out = array();
     foreach ($arr1 as $key1 => $value1) {
      $out[$value1] = $arr2[$key1];
     }
     return $out;
  }
}

if(!function_exists('str_split')) {
  function str_split($string, $split_length = 1) {
    $array = explode("\r\n", chunk_split($string, $split_length));
    array_pop($array);
    return $array;
  }
}

//  ==================================================================================================
//  TIME STAMP
//  ==================================================================================================
function time_stamp($session_time) { 
  $time_difference = time() - $session_time ; 
  
  $seconds = $time_difference ; 
  $minutes = round($time_difference / 60 );
  $hours = round($time_difference / 3600 ); 
  $days = round($time_difference / 86400 ); 
  $weeks = round($time_difference / 604800 ); 
  $months = round($time_difference / 2419200 ); 
  $years = round($time_difference / 29030400 ); 
  $result = '';
  // Seconds
  if($seconds <= 60) {
  $result = $seconds.Lang('seconds ago'); 
  } else if($minutes <= 60){
     if($minutes==1){
     $result = Lang('one minute ago'); 
     } else {
      $result = $minutes.Lang('minutes ago'); 
     }
  } else if($hours <= 24){
     if($hours==1) {
     $result = Lang('one hour ago');
    } else {
     $result = $hours.Lang('hours ago');
    }
  } else if($days <= 7){
    if($days==1) {
     $result = Lang('one day ago');
    } else {
     $result = $days.Lang('days ago');
     }
  } else if($weeks <= 4){
     if($weeks==1) {
     $result = Lang('one week ago');
     } else {
     $result = $weeks.Lang('weeks ago');
    }
  } else if($months <= 12){
     if($months==1){
     $result = Lang('one month ago');
     } else {
     $result = $months.Lang('months ago');
     }
  } else {
     if($years==1){
      $result = Lang('one year ago');
     } else {
      $result = $years.Lang('years ago');
     }
  }
  return $result;
}
//  ==================================================================================================
//  FILE SIZE CONVERT
//  ==================================================================================================
function _format_bytes($a_bytes)
{
    if ($a_bytes < 1024) {
        $result = array("size" => $a_bytes, "type" => " B");
    } elseif ($a_bytes < 1048576) {
        $result = array("size" => round($a_bytes / 1024, 2), "type" => " KB");
        //return round($a_bytes / 1024, 2) .' KiB';
    } elseif ($a_bytes < 1073741824) {
        $result = array("size" => round($a_bytes / 1048576, 2), "type" => " MB");
        //return round($a_bytes / 1048576, 2) . ' MiB';
    } elseif ($a_bytes < 1099511627776) {
        $result = array("size" => round($a_bytes / 1073741824, 2), "type" => " GB");
        //return round($a_bytes / 1073741824, 2) . ' GiB';
    } elseif ($a_bytes < 1125899906842624) {
        $result = array("size" => round($a_bytes / 1099511627776, 2), "type" => " TB");
        //return round($a_bytes / 1099511627776, 2) .' TiB';
    } elseif ($a_bytes < 1152921504606846976) {
        $result = array("size" => round($a_bytes / 1125899906842624, 2), "type" => " PB");
        //return round($a_bytes / 1125899906842624, 2) .' PiB';
    } elseif ($a_bytes < 1180591620717411303424) {
        $result = array("size" => round($a_bytes / 1152921504606846976, 2), "type" => " EB");
        //return round($a_bytes / 1152921504606846976, 2) .' EiB';
    } elseif ($a_bytes < 1208925819614629174706176) {
        $result = array("size" => round($a_bytes / 1180591620717411303424, 2), "type" => " ZB");
        //return round($a_bytes / 1180591620717411303424, 2) .' ZiB';
    } else {
        $result = array("size" => round($a_bytes / 1208925819614629174706176, 2), "type" => " YB");
        //return round($a_bytes / 1208925819614629174706176, 2) .' YiB';
    }
    return json_encode($result, true);
}
//  ==================================================================================================
//      stdClass Object to php array
//  ==================================================================================================
function objectToArray($d) {
    if (is_object($d)) {
      // Gets the properties of the given object
      // with get_object_vars function
      $d = get_object_vars($d);
    }
 
    if (is_array($d)) {
      //Return array converted to object
      //Using __FUNCTION__ (Magic constant)
      //for recursive call
      return array_map(__FUNCTION__, $d);
    }
    else {
      // Return array
      return $d;
    }
  }
//  ==================================================================================================
function arraySearchFieldName ($fieldname, $array) {
  $returnValue = '';
  foreach($array as $key => $value) {
    if ($value['fieldname'] == $fieldname) {
//printR($value);
      $returnValue = $value['fieldsize'];
      return $returnValue;
      exit;
    } 
  }
  if ($returnValue == '') return '500';
}

//  ==================================================================================================
//      print_r helyett
//  ==================================================================================================
function printR($array =array(), $exit = false){
  if(DEVMODE === TRUE){
    echo '<pre>';
    print_r($array);
    echo '</pre>';
    if($exit === true)
      exit;
  }
  
}


//  ==================================================================================================
//  ellenorzi, hogy a kapcsolat http vagy https 
//  ==================================================================================================
function connectionType(){
  $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
  return $protocol;
  //$baseurl= connectionType().IMG_SITE_URL.'/';
}

//  ==================================================================================================
//  PHP Garray-t sjon stringge konvertalja
//  ha nincs json encode
//  ==================================================================================================
function array_to_json( $array ){

    if( !is_array( $array ) ){
        return false;
    }

    $associative = count( array_diff( array_keys($array), array_keys( array_keys( $array )) ));
    if( $associative ){

        $construct = array();
        foreach( $array as $key => $value ){

            // We first copy each key/value pair into a staging array,
            // formatting each key and value properly as we go.

            // Format the key:
            if( is_numeric($key) ){
                $key = "key_$key";
            }
            $key = "'".addslashes($key)."'";

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = "'".addslashes($value)."'";
            }

            // Add to staging array:
            $construct[] = "$key: $value";
        }

        // Then we collapse the staging array into the JSON form:
        $result = "{ " . implode( ", ", $construct ) . " }";

    } else { // If the array is a vector (not associative):

        $construct = array();
        foreach( $array as $value ){

            // Format the value:
            if( is_array( $value )){
                $value = array_to_json( $value );
            } else if( !is_numeric( $value ) || is_string( $value ) ){
                $value = "'".addslashes($value)."'";
            }

            // Add to staging array:
            $construct[] = $value;
        }

        // Then we collapse the staging array into the JSON form:
        $result = "[ " . implode( ", ", $construct ) . " ]";
    }

    return $result;
}   
//  ==================================================================================================
function in_object($val, $obj){

    if($val == ""){
        trigger_error("in_object expects parameter 1 must not empty", E_USER_WARNING);
        return false;
    }
    if(!is_object($obj)){
        $obj = (object)$obj;
    }

    foreach($obj as $key => $value){
        if(!is_object($value) && !is_array($value)){
            if($value == $val){
                return true;
            }
        }else{
            return in_object($val, $value);
        }
    }
    return false;
}

//  ==================================================================================================
//  kiuriti a directory tartalmat. akkor hivom meg, amikor minden kepet toroltem  
//  recursive function
//  ==================================================================================================
function SureRemoveDir($dir, $DeleteMe) {
  if (!is_dir($dir)) return false;
  if (!$dh = @opendir($dir)) return false;
  while (false !== ($obj = readdir($dh))) {
    if($obj=='.' || $obj=='..') continue;
    if (!@unlink($dir.'/'.$obj)) SureRemoveDir($dir.'/'.$obj, true);
    //echo $obj;
  }
  closedir($dh);
  if ($DeleteMe){
    @rmdir($dir);
  }
}
//  ==================================================================================================
function rrmdir($path){
  return is_file($path) ? @unlink($path) : false;//array_map('rrmdir',glob($path.'/*'))==@rmdir($path);
}

//  ==================================================================================================
//  create password random 
//  and hash it
//  ==================================================================================================
function HashPassword($input){
  $salt = bin2hex(mcrypt_create_iv(32, MCRYPT_DEV_URANDOM)); 
  $hash = hash("sha256", $salt . $input); 
  $final = $salt . $hash; 
  return $final;
}

function rand_string( $length ) {
  $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ23456789";
  $pass = substr(str_shuffle($chars),0,$length); 
  return $pass;//HashPassword($pass);
}

function sanitize($data){
    $data=trim($data);
    $data=MySQL::filter($data);
    return $data;
}

function getRequest($id,$default=null)
{
    return isset($_REQUEST[$id])?$_REQUEST[$id]:$default;
}

/**
 * @param $string
 * @param bool $stripTags
 * @return Purified|string
 */
function purifyString($string,$stripTags=false)
{
    $htmlPurifier = new HTMLPurifier($c=HTMLPurifier_Config::createDefault());
    $c = $htmlPurifier->purify($string);
    return $stripTags?strip_tags($c):$c;
}

function htmlSpecialCharsHelper($string)
{
    return htmlspecialchars($string,ENT_QUOTES,'utf-8');
}

/**
 * @param string $postId
 * @return array
 */
function createArrayFromPostNV($postId='form')
{
    $formArray = array();
    if (isset($_POST[$postId]))
    {
        for($i=0;$i<count($_POST[$postId]);$i++){
            $formArray[ $_POST[$postId][$i]['name'] ] = MySQL::filter($_POST[$postId][$i]['value']);
        }
    }
    return $formArray;
}


/**
 * @param [$variable1,$variable2...] mixed
 */
function logToFile()
{
    if (DEVMODE !== true) return;

    $args=array();
    foreach (func_get_args() as $id=>$value)
        $args[]='{ ARGS '.$id.' } = '.print_r($value,true)."\r\n\r\DUMP:".serialize($value)."\r\n\n";

    file_put_contents($_SERVER['DOCUMENT_ROOT'].'/../log/phplog/debug.txt',"---------------\r\n".date('Y-m-d H:i:s')."\r\n".implode("\r\n",$args)."\r\n\r\n",FILE_APPEND);
}

?>