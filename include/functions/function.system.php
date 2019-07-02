<?
//function.system.php
require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/config.php');
//  ===========================================================================================
//  felhasznalo szintjenek ellenorzese
//  ===========================================================================================
function checkPrivate() {
  if(isset($_SESSION['user_type']) && $_SESSION['user_type'] == 'private') {
    return true;
  } else { 
    return false ;
  }
}
function checkAdmin() {
  if(isset($_SESSION['userlevel']) && $_SESSION['userlevel'] == SITE_ADMIN_LEVEL) {
    return true;
  } else { 
    return false ;
  }
}
function checkIrodaAdmin() {
  if(isset($_SESSION['userlevel']) && $_SESSION['userlevel'] == OFFICE_ADMIN_LEVEL) {
    return true;
  } else { 
    return false ;
  }
}
function checkGroupAdmin() {
  if(isset($_SESSION['userlevel']) && $_SESSION['userlevel'] == EDITOR_LEVEL) {
    return true;
  } else { 
    return false ;
  }
}

function checkPwd($x,$y) {
  if(empty($x) || empty($y) ) { return false; }
  if (strlen($x) < 4 || strlen($y) < 4) { return false; }
  if (strcmp($x,$y) != 0) {
    return false;
  } 
  return true;
}
function PwdHash($pwd, $salt = null)
{
    if ($salt === null)     {
        $salt = substr(md5(uniqid(rand(), true)), 0, SALT_LENGTH);
    }
    else     {
        $salt = substr($salt, 0, SALT_LENGTH);
    }
    return $salt . sha1($pwd . $salt);
}
/**/


//  ==================================================================================================
//  AZONOSITASHOZ KODGENERALAS FUGGVENYEI
//  ==================================================================================================

function genToken( $len = 32, $md5 = true ) {
     
        # Seed random number generator
        # Only needed for PHP versions prior to 4.2
        mt_srand( (double)microtime()*1000000 );
     
        # Array of characters, adjust as desired
        $chars = array(
            'Q', '@', '8', 'y', '%', '^', '5', 'Z', '(', 'G', '_', 'O', '`',
            'S', '-', 'N', '<', 'D', '{', '}', '[', ']', 'h', ';', 'W', '.',
            '/', '|', ':', '1', 'E', 'L', '4', '&', '6', '7', '#', '9', 'a',
            'A', 'b', 'B', '~', 'C', 'd', '>', 'e', '2', 'f', 'P', 'g', ')',
            '?', 'H', 'i', 'X', 'U', 'J', 'k', 'r', 'l', '3', 't', 'M', 'n',
            '=', 'o', '+', 'p', 'F', 'q', '!', 'K', 'R', 's', 'c', 'm', 'T',
            'v', 'j', 'u', 'V', 'w', ',', 'x', 'I', '$', 'Y', 'z', '*'
        );
     
        # Array indice friendly number of chars; empty token string
        $numChars = count($chars) - 1; $token = '';
     
        # Create random token at the specified length
        for ( $i=0; $i < $len; $i++ )
            $token .= $chars[ mt_rand(0, $numChars) ];
     
        # Should token be run through md5?
        if ( $md5 ) {
     
            # Number of 32 char chunks
            $chunks = ceil( strlen($token) / 32 ); $md5token = '';
     
            # Run each chunk through md5
            for ( $i=1; $i<=$chunks; $i++ )
                $md5token .= md5( substr($token, $i * 32 - 32, 32) );
     
            # Trim the token
            $token = substr($md5token, 0, $len);
     
        } return $token;
    }
    
function build_token($sessiontoken) {
  $newST = ''; $newTime ='';
  $newST = str_split($sessiontoken,7);
  $newTime = str_split(time(),2);
  $count = count($newST);
  $newtoken = '';
  for ($x = 0; $x < $count; $x++) {
    $newtoken .= $newST[$x].$newTime[$x];
  }
  $newtoken = stringEnc(base64_encode($newtoken));
  return $newtoken;
}

function rebuild_token($newtoken) {
  $tmptoken = base64_decode(stringDec($newtoken));
  $tmp2token = str_split($tmptoken,9);
  //print_r( $tmp2token);
  $count = count($tmp2token);
  //echo $count;
  $orig_token = '';
  $orig_Time = '';
  $result = array();
  unset($result);
  for ($x = 0; $x < $count-1; $x++) {
    $tmp = str_split($tmp2token[$x],7);
    $orig_token .= $tmp[0];
     //echo $orig_token.'<br/>';
     $orig_Time .= $tmp[1];
     //echo $orig_Time.'<br/>';
  }
  $tmp1 = str_split($tmp2token[$count-1],4);
       $orig_token .= $tmp1[0];
       //echo $orig_token.'<br/>';
       $orig_Time .= $tmp1[1];
       //echo $orig_Time.'<br/>';

  $result[] = $orig_token;
  $result[] = $orig_Time;
  return $result;
}
// az idolimit ellenorzesehez
function time_diff($s){ 
    $m=0;$hr=0;$d=0;$td="0 az elteres"; 
    
    if($s>59) { 
        $m = (int)($s/60); 
        $s = $s-($m*60); // sec left over 
        $td = "$m min $s sec"; 
    } 
    if($m>59){ 
        $hr = (int)($m/60); 
        $m = $m-($hr*60); // min left over 
        $td = "$hr hr"; if($hr>1) $td .= "s"; 
        if($m>0) $td .= ", $m min"; 
    } 
    if($hr>23){ 
        $d = (int)($hr/24); 
        $hr = $hr-($d*24); // hr left over 
        $td = "$d day"; if($d>1) $td .= "s"; 
        if($d<3){ 
            if($hr>0) $td .= ", $hr hr"; if($hr>1) $td .= "s"; 
        } 
    } 
    return $td; 
} 
//  ==================================================================================================
//  code - decode 
//  ==================================================================================================

function stringEnc($string) {
  $res = encode($string,'kiSsKAac72;ab');
  return $res;
}
function stringDec($string) {
  $res = decode($string,'kiSsKAac72;ab');
  return $res;
}


function encode($string,$key) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
  $j = 0;
  $hash = '';
    for ($i = 0; $i < $strLen; $i++) {
        $ordStr = ord(substr($string,$i,1));
        if ($j == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= strrev(base_convert(dechex($ordStr + $ordKey),16,36));
    }
    return $hash;
}

function decode($string,$key) {
    $key = sha1($key);
    $strLen = strlen($string);
    $keyLen = strlen($key);
  $j = 0;
  $hash = '';
    for ($i = 0; $i < $strLen; $i+=2) {
        $ordStr = hexdec(base_convert(strrev(substr($string,$i,2)),36,16));
        if ($j == $keyLen) { $j = 0; }
        $ordKey = ord(substr($key,$j,1));
        $j++;
        $hash .= chr($ordStr - $ordKey);
    }
    return $hash;
}
//  ==================================================================================================
function get_valid_ip( $ip ) {
    return preg_match("/^([1-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])" .
            "(\.([0-9]|[1-9][0-9]|1[0-9][0-9]|2[0-4][0-9]|25[0-5])){3}$/", $ip );
}
//$ip = "192.168.1.1";
//if ( get_valid_ip( $ip ) ) {
//    echo $ip . " is valid.";
//}

//  ================================================================================================== 
//  Form header
//  ================================================================================================== 
function createFormHeader($formName, $formAction, $formMethod, $autocomplete = 'off', $token, $userType, $irodaiazonosito, $tableName, $tableData, $tip, $ac) {
  
  echo '<form name="'.$formName.'" id="'.$formName.'" method="'.$formMethod.'" action="'.$formAction.'" autocomplete="'.$autocomplete.'">';
  echo '<input type="hidden" name="ts" value="'.$token.'" />';
    $tipus = '';
    ($userType == 'office') ? $tipus = 'office' : $tipus = 'private';
  echo '<input type="hidden" name="utip" value="'.$tipus.'" />';
  echo '<input type="hidden" name="ia" value="'.$irodaiazonosito.'" />';
  $tableCode = '';
  //$tableCode = tabledecode($tableName);
  echo '<input type="hidden" name="tn" value="'.$tableName.'" />';
  echo '<input type="hidden" name="tnd" value="'.$tableData.'" />';
  echo '<input type="hidden" name="tip" value="'.$tip.'" />';
  echo '<input type="hidden" name="ac" value="'.$ac.'" />';
}

//  ================================================================================================== 
//  Form header 2.
//  ================================================================================================== 
function createFormHeader2($formName, $formAction, $formMethod, $autocomplete = 'off', $token, $userid, $irodaiazonosito, $lft, $slideshow, $template, $badge) {
  $html = '';
  $html .= ' name="'.$formName.'" id="'.$formName.'" method="'.$formMethod.'" action="'.$formAction.'" autocomplete="'.$autocomplete.'">';
  $html .= '<input type="hidden" name="ts" value="'.$token.'" />';
  $html .= '<input type="hidden" name="u" value="'.$userid.'" />';
  $html .= '<input type="hidden" name="o" value="'.$irodaiazonosito.'" />';
  $html .= '<input type="hidden" name="lft" value="'.$lft.'" />';
  $html .= '<input type="hidden" name="slideshow" value="'.$slideshow.'" />';
  $html .= '<input type="hidden" name="template" value="'.$template.'" />';
  $html .= '<input type="hidden" name="badge" value="'.$badge.'" />';
  //$tableCode = '';
  //$tableCode = tabledecode($tableName);
  //echo '<input type="hidden" name="tn" value="'.$tableName.'" />';
  //echo '<input type="hidden" name="tnd" value="'.$tableData.'" />';
  //echo '<input type="hidden" name="tip" value="'.$tip.'" />';
  //echo '<input type="hidden" name="ac" value="'.$ac.'" />';
  return $html;
}


//  ==================================================================================================
//  kilistazza a directory tartalmat es
//  osszehasonlitja $_SESSION valtozoban levo kepeket
//  a direktoriban talaltakkal
//  process-swf-sortable-save.php, form_ingadata_swf.php
//  ==================================================================================================
function checkDeletablePictures($basePath,$sessionPath,$sessionArray, $sType) {

//echo "Path: ".$basePath.$sessionPath."<br/>";
  
  $dirResult = array();
  $dirCount = 0;
  $diffArray = array();
  $diffCount = 0;
  $diffCountInside = 0;
  $sessionCount = 0;
  $filesArray = array();
  $returnMessage = '';
  $returnMessageType = 'warning';
  
  //Hany kep van a konyvtarban?
  if ($handle = opendir($basePath.$sessionPath)) {
      while (false !== ($entry = readdir($handle))) {
          if ($entry != "." && $entry != "..") {
            $dirResult[] = $entry;
          }
      }
      closedir($handle);
  }
  $dirCount = count($dirResult);
//echo "<br/>dircount: ".$dirCount."<br/>";

  //ellenorzom a sessionben tarolt adatokat, 
  $sessionCount = count($sessionArray);

  //osszehasonlitashos seged valtozo
  for ($i = 0; $i < $sessionCount; $i++) {
    $filesArray[] = $sessionArray[$i]['filename'];
  }

  //ELLENORZESI RESZ
  /*
    Esetek:
      1. sess es dir darabszam es tartalom ugyan az
      2. sess es dir darabszam eltero
      2.1. dir tobb, mint sess
      2.2. sess tobb, mint dir
    Vizsgalni kell, hogy van-e egyezes a sess es dir kozott
    Alapavetes: a session adatai meghatarozok
  */
  
  //tobb van a directoryban, mint az adatbazisban
  // es az adatbazisban van kep
  if ( $dirCount > $sessionCount && $sessionCount > 0) {
    for ($i = 0; $i < $sessionCount; $i++) {
      $diffArray[] = array_diff($dirResult,$filesArray);
    }
    $diffCount = count($diffArray[0]);

    foreach ( $diffArray[0] as $k => $v) {
      $str = $basePath.$sessionPath.$v;
      if (is_file($str)){
        unlink($str);
      }
    }
    $returnMessage = "Képadatok frissítve";
  }
  
  //van a directoryban, az adatbazisban nincs
  if ( $dirCount > $sessionCount && $sessionCount == 0) {
    SureRemoveDir($basePath.$sessionPath, false);
  }
  
  //megegyezik a darabszam. vajon a tartalom is?
  if ( $dirCount == $sessionCount) {
    for ($i = 0; $i < $sessionCount; $i++) {
      $diffArray[] = array_diff($dirResult,$filesArray);
    }
    $diffCount = count($diffArray[0]);
    if ($diffCount != 0 ) {
      foreach ( $diffArray[0] as $k => $v) {
        $str = $basePath.$sessionPath.$v;
        if (is_file($str)){
          unlink($str);
        }
      }
      unset($_SESSION[$sType]['files_array']);
      $returnMessage = "darabszam azonos, de nem egyezik a tartalom";
    } else {
      $returnMessage = "Mindenhol egyezes";
    }
  }
  
  //kevesebb van a directoryban, mint az adatbazisban
  if ( $dirCount < $sessionCount) {
//echo "<br/>forditva: <br/>";
    $diffArray = array();
    for ($i = 0; $i < $sessionCount; $i++) {
      $diffArray[] = array_diff($filesArray,$dirResult);
    }
    $db = count($diffArray);
    for ($i = 0; $i < $db; $i++) {
      //unset($_SESSION[$sType]['files_array'][$i]);
    }
//print_r($diffArray[0]);
//print_r($_SESSION[$sType]['files_array']);
    //$returnMessage = "A ".implode(',',array_keys($diffArray[0]))."\n képek hiányoznak";
    //$returnMessageType = "error";
    $returnMessage = "A session uritve";
  }
  /*if ($returnMessage != "")
  echo "<script type=\"text/javascript\" charset=\"utf-8\">
        showNotification({
          message: '".$returnMessage."',
          type: '".$returnMessageType."',
          autoClose: false
        });
        </script>
      ";
  */
  return '';//$returnMessage;
}

//  ==================================================================================================
//  Return message in json formatted string 
//  ==================================================================================================
function jsonMessage($jName,$jAction,$jType, $jResult, $msg1, $msg2) {
  //echo $jResult;
  $message = '{';
  $message .= '"sName":"'.$jName.'",';
  $message .= '"sAction":"'.$jAction.'",';
  $message .= '"sType":"'.$jType.'",';
  $message .= '"sResult":'.$jResult.',';
  if (!is_array($msg1) && $msg1 == "") {
    $message .= '"sReturnInfo": ""';
  } else {
    $message .= '"sReturnInfo": [';
    if (is_array($msg2)) {
      $max = count($msg2);
      $vl = ',';
      for ($i = 0; $i < $max; $i++) {
        if ($i == $max-1) { $vl = ''; } 
        $message .= '{"'.$msg1[$i].'":"'.$msg2[$i].'"}'.$vl;
      }
    } else {
      $message .= '{"'.$msg1.'":"'.$msg2.'"}';
    }
    $message .= ']';
  }
  $message .= '}';
  return $message;
}
//  ==================================================================================================
function json_message($header,$Data1,$Data2) {
  if (!is_Array($Data2)) {
    $msg = $Data1.' - '.$Data2;
    if ( empty($data1) ) { $msg = $Data2; }
    $message = '{"id": "'.$header.'", "message": "'.$msg.'"}';
    } else {
      foreach ($Data2 as $key => $value) {
        $values2[]=$value;
        //$keys[]=$key;
      }
      $data_count = (int)count(array_keys($values2));
      if (is_Array($Data1)) {
        foreach ($Data1 as $key => $value) {
          $values1[]=$value;
        }
        } else { 
          for ($k=0; $k < $data_count; $k++) {
            $values1[]=$Data1;
          }
      }
      $data_count = (int)count(array_keys($values2));
      $kieg = ','; $msg = '';
      $message = '{"id": "'.$header.'", "message": [';
      for ($k=0; $k < $data_count; $k++) {           // az elso hat elem az form alapadatai
        if ( $k >= $data_count-1 ) { $kieg = ''; }
        $msg .= '{"id": "'. $values1[$k] .'", "message": "'. $values2[$k] .'"}' . $kieg;
      }
      $message .= $msg . ']}';
  }
  return $message;
}

//  ==================================================================================================
//  ellenorzi, hogy letezik-e az adott konytar 
//  pl.: hirdetes/projekt 
//  Ha nem, letrehozza
//  ==================================================================================================
function checkDir($basePath, $sessionPath) {
  $dirName = $basePath.$sessionPath;//rendben 2012.01.15.

  if (!file_exists( $dirName )) {
    //$oldumask = umask(0);
    mkdir($dirName, 0777, true); // or even 01777 so you get the sticky bit set
    //umask($oldumask);
    if (!file_exists($dirName)) {
      echo "<script type=\"text/javascript\" charset=\"utf-8\">
        showMessage({
          text: 'Rendszerhiba lépett fel. Nem tud képeket feltölteni!".$dirName."',
          type: 'error'
        });
        </script>
      ";
    }
  }
}

//  ==================================================================================================

//  ==================================================================================================
//  Nested tree //query for tree line: "SELECT * FROM category WHERE mainParent = 3 ORDER BY lft ASC"
//  ==================================================================================================
function toHierarchy($collection)
{
        // Trees mapped
        $trees = array();
        $l = 0;

        if (count($collection) > 0) {
                // Node Stack. Used to help building the hierarchy
                $stack = array();

                foreach ($collection as $node) {
                        $item = $node;
                        $item['children']['result'] = array();

                        // Number of stack items
                        $l = count($stack);

                        // Check if we're dealing with different levels
                        while($l > 0 && $stack[$l - 1]['depth'] >= $item['depth']) {
                                array_pop($stack);
                                $l--;
                        }

                        // Stack is empty (we are inspecting the root)
                        if ($l == 0) {
                                // Assigning the root node
                                $i = count($trees);
                                $trees[$i] = $item;
                                if(empty($trees[$i]['children']['result']))
                                  unset($trees[$i]['children']);
                                $stack[] = & $trees[$i];
                        } else {
                                // Add node to parent
                                $i = count($stack[$l - 1]['children']['result']);
                                $stack[$l - 1]['children']['result'][$i] = $item;
                                $stack[] = & $stack[$l - 1]['children']['result'][$i];
                                if(empty($stack[$l - 1]['children']['result']))
                                  unset($trees[$i]['children']);
                        }
                  
                }
        }

        return $trees;
}

//  ==================================================================================================

//  ==================================================================================================
//  RETURN JSON FORMATTED RESULT WITH HEADER content type application/json
//  ==================================================================================================
function printSortResult( $returnArray = array('error' => 'Misspelled data sent to server!') ){
  $_SESSION['LAST_ACTIVITY'] = time();
  header('Pragma: no-cache');
  header('Cache-Control: no-store, no-cache, must-revalidate');
  header('Content-Disposition: inline; filename="files.json"');
  // Prevent Internet Explorer from MIME-sniffing the content-type:
  header('X-Content-Type-Options: nosniff');
  header('Access-Control-Allow-Credentials:false');
  header('Access-Control-Allow-Headers:Content-Type, Content-Range, Content-Disposition, Content-Description');
  header('Access-Control-Allow-Origin:*');
  header('Content-type: application/json');
  header('Expires:Thu, 19 Nov 1981 08:52:00 GMT');
  header('Keep-Alive:timeout=15, max=100');
  header('Pragma:no-cache');
  $json = json_encode($returnArray, true);
  echo $json;
  exit;
}

function printResult( $returnArray = array('error' => 'Misspelled data sent to server!') ){
  $_SESSION['LAST_ACTIVITY'] = time();
  header('Pragma: no-cache');
  header('Cache-Control: no-store, no-cache, must-revalidate');
  header('Content-Disposition: inline; filename="files.json"');
  // Prevent Internet Explorer from MIME-sniffing the content-type:
  header('X-Content-Type-Options: nosniff');
  header('Access-Control-Allow-Credentials:false');
  header('Access-Control-Allow-Headers:Content-Type, Content-Range, Content-Disposition, Content-Description');
  header('Access-Control-Allow-Origin:*');
  header('Content-type: application/json');
  header('Expires:Thu, 19 Nov 1981 08:52:00 GMT');
  header('Keep-Alive:timeout=15, max=100');
  header('Pragma:no-cache');
  $resF['result'] = $returnArray;
  $json = json_encode($resF, true);
  echo $json;
  exit;
}

//  ==================================================================================================
//  extract subdomain(s) from doman ex: $_SERVER['HTTP_HOST']
//  ==================================================================================================
function extract_domain($domain){
    if(preg_match("/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/i", $domain, $matches))
    {
        return $matches['domain'];
    } else {
        return $domain;
    }
}

function extract_subdomains($domain){
    $subdomains = $domain;
    $domain = extract_domain($subdomains);

    $subdomains = rtrim(strstr($subdomains, $domain, true), '.');

    return $subdomains;
}
//  ==================================================================================================
//  array group by key and reindex
//  ==================================================================================================
/* //sample
<pre>Array
(
    [0] => Array
        (
            [groupid] => 3
            [name] => Angol kezdő
            [doname] => angolkezdo
            [u_id] => 388
        )

    [1] => Array
        (
            [groupid] => 3
            [name] => Angol kezdő
            [doname] => angolkezdo
            [u_id] => 316
        )

    
    [4] => Array
        (
            [groupid] => 1
            [name] => Angol kezdő2
            [doname] => angolkezdo2
            [u_id] => 386
        )

    [5] => Array
        (
            [groupid] => 1
            [name] => Angol kezdő2
            [doname] => angolkezdo2
            [u_id] => 384
        )

    
)
*/

function groupByAndReindex($array, $value){
  $level_arr=array();
  $newArray=array();
  foreach ($array as $key => &$entry) {
    $level_arr[$entry[$value]][$key] = $entry;
  }
  $level_arr = array_values($level_arr);
  foreach($level_arr as $tmparray){
    $newArray[] = array_values($tmparray);
  }
  return $newArray;
}
//  ==================================================================================================
//  array group by key and reindex
//  ==================================================================================================
function createAttachList( $attRes, $imageURL, $downloadUrl ) {

    $mediaFilesArray = array();
    $html = '';
    $db = count($attRes);
    for ($i=0;$i<$db;$i++){
      switch ($attRes[$i]['type']) {
        case 'audio': $su = "audio-grey.png"; break;
        //case 'video': $su = "http://img.skillbi.local/160x120.gif"; break;
        case 'pdf':   $su = "pdf-grey.png"; break;
        case 'excel': $su = "excel-grey.png"; break;
        case 'word':  $su = "doc-grey.png"; break;
        case 'powerpoint': $su = "ppt-grey.png"; break;
        default: $su = $attRes[$i]['thumbnail_url']; break;
      }/*
      $mediaFilesArray[$i] = array(
          "id" => $attRes[$i]['id'],
          "name" => $attRes[$i]['name'],
          "type" => $attRes[$i]['type'],
          "mediatype" => $attRes[$i]['mediatype'],
          "mediaurl" => $attRes[$i]['mediaurl'],
          "thumbnail_url" => $su//,
          //"uploaded" => $attRes[$i]['uploaded'],//date( "Y.m.d." , $query[$i]['uploaded']),
          //"uploaded_ts" => $attRes[$i]['uploaded_ts']
          );*/
      $html .='<li class="mediaElement span2 lightgreyB '.$attRes[$i]['type'].'">';
      $html .='<a href="'.$downloadUrl.$attRes[$i]['mediaurl'].'"><div class="colorBar '.$attRes[$i]['type'].'"></div><div class="thumbnail">';
        $html .='<img src="';
        if ($attRes[$i]['mediatype'] == 'remote') {
          $html .=$su;
        } else { 
          $html .=$imageURL.$su.'" alt="" />';
        }     
         $html .='</div><div class="caption"><p><span class="name">'.substr($attRes[$i]['name'],0,21).'</span></p></div></a></li>';

    }
return $html;
}
function createAttachLinks( $attRes, $imageURL, $downloadUrl ) {

    $mediaFilesArray = array();
    $html = '';
    $db = count($attRes);
    for ($i=0;$i<$db;$i++){
        switch ($attRes[$i]['type']) {
            case 'audio': $su = "audio-grey.png"; break;
            //case 'video': $su = "http://img.skillbi.local/160x120.gif"; break;
            case 'pdf':   $su = "pdf-grey.png"; break;
            case 'excel': $su = "excel-grey.png"; break;
            case 'word':  $su = "doc-grey.png"; break;
            case 'powerpoint': $su = "ppt-grey.png"; break;
            default: $su = $attRes[$i]['thumbnail_url']; break;
        }
        $html .='<li><a href="'.$downloadUrl.$attRes[$i]['mediaurl'].'"><span class="name">'.substr($attRes[$i]['name'],0,51).'</span></a></li>';

    }
    return $html;
}
?>
