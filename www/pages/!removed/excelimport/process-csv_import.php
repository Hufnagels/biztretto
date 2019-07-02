<?php
require_once ($_SERVER['DOCUMENT_ROOT'].'/include/config.php');
ob_start();

if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest'){
	require_once ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/404.php' );
  exit();
}

require( $_SERVER['DOCUMENT_ROOT'].'/include/authenticate.php' );
if (!$_SESSION['logged_in']){
  include ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/403forbidden.php' );
  exit();
}

require_once ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_include_base.php');

clearstatcache();

$sm = new mysql();
if (array_key_exists('u', $_GET)) { $uid = $sm->filter(base64_decode($_GET['u'])); }
if (array_key_exists('i', $_GET)) { $ont = $sm->filter(base64_decode($_GET['i'])); }

$_SESSION['LAST_ACTIVITY'] = time(); 

//a kliens oldalon a data valtozoban kuldott adatok
//a $_POST valtozoban vannak
if (!isset($_FILES["uploadfile"]) || !is_uploaded_file($_FILES["uploadfile"]["tmp_name"]) || $_FILES["uploadfile"]["error"] != 0) {
	echo "ERROR:invalid upload";
	exit(0);
}
//exit;

$dirName = $_SERVER['DOCUMENT_ROOT'].'/../media/_zip/';
$acceptedFiles = array("application/vnd.ms-excel");
ob_end_clean();
if ($_FILES['uploadfile']['size'] > 0) { 
  $result = '';
    //get the csv file 
    //$file = $_FILES[csv][tmp_name]; 
    $file =  $dirName.$_FILES['uploadfile']['name'];
printR( $file );
    if (move_uploaded_file($_FILES['uploadfile']['tmp_name'], $file)) { 
//echo "success"; 
//echo $file;
      //$encoding=(isset($_GET['encoding']) && $_GET['encoding']=='ISO-8859-2')?'UTF-8':'ISO-8859-2';
      header("Cache-Control: no-store, no-cache, must-revalidate");
      header("Cache-Control: post-check=0, pre-check=0", false);
      header("Pragma: no-cache");
      //header("Content-Type: text/csv; charset=$encoding");
      //header("Content-Disposition: inline; filename=$name");

      $handle = fopen($file,"r"); 
      $header = fgetcsv($handle,0,";","'");
      //check if header presented?
      
      
      $columns = implode(',',$header);

      //loop through the csv file and insert into database 
      do { 
          if ( $data[0] ) {
            if ( is_numeric($data[0]) )
              $data[0] = '';
            for ($i=0;$i<count($data);$i++){
//echo mb_detect_encoding($data[$i]);
              if (mb_detect_encoding($data[$i], 'UTF-8', true) === FALSE)
                $data[$i] = iconv('ISO 8859-2', 'UTF-8', $data[$i]);
              $values[] = "'". $data[$i]."'";
            }
            //printR( $data );
            //mysql_query(
            $result[]="INSERT INTO contacts (".$columns.") VALUES (".implode(',', $values).")";
            //);
            //$result[] = $values;
            $values = array();
          } 
      } while ($data = fgetcsv($handle,0,";","'")); 
    } else {
      $result = "error";
    }
    unlink($file);
}


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
$resF['result'] = $result;
$json = json_encode($resF, true);
echo $json;
?>