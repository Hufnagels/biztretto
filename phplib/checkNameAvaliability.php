<?php
require( $_SERVER['DOCUMENT_ROOT'].'/../include/authenticate.php' );

if (!$_SESSION['logged_in']){
  include ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/403forbidden.php' );
  //return "papo2";
  exit();
}

$_SESSION['LAST_ACTIVITY'] = time();

//printR( $_SESSION );
include ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_header_text.php');
include ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_include_base.php');

//printR(json_encode($_POST['request'][0]));
//printR($_POST['form']);

//$m = new mysql();
if ( isset($_POST['form']) && !empty($_POST['form']) ){
  
  //create new diskarea
  if ( isset($_POST['diskarea']) && !empty($_POST['diskarea']) ){
    //print_r($_POST['remote'][0]);
    $formArray = array();
    for($i=0;$i<count($_POST['form']);$i++){
      $formArray[ $_POST['form'][$i]['name'] ] = $_POST['form'][$i]['value'];
    }
    if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);
    if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);
    $sortname = normalize_special_characters( strtolower( MySQL::filter( $_POST['diskarea'] ) ) );
    $sortname = str_replace( ' ', '', $sortname );
    $newDiskareaName = array( 'name' => MySQL::filter( $_POST['diskarea'] ), 'sortname' => $sortname);
    //check if exist in database

    $options = array (
      'table' => 'media_diskarea',
      'fields' => 'diskArea_id',
      'condition' => $formArray,
      'conditionExtra' => "name = '".$newDiskareaName['name']."'",
      'order' => 'diskArea_id',
      'limit' => 1
    );
//print_r( $options );
    $result = MySQL::select($options);
//print_r( $result );
    if(!empty($result)){
      $slidesArray = array('error' => 'This name is exist. Try another one!');
    } else {
      $formArray['createdDate'] = date( "Y-m-d H:i:s" , time() );
      $array_of_values = array_merge( $formArray, $newDiskareaName );
      $insertID = MySQL::insert('media_diskarea',$array_of_values);
      
      $sortname = normalize_special_characters( strtolower( $newDiskareaName['name'] ) );
      $sortname = str_replace( ' ', '', $sortname );
      $slidesArray = array(
        'success' => '', 
        'name' => $newDiskareaName['name'], 
        'sortname' => $sortname, 
        'id' => $insertID, 
        'message' => 'Successfully created the new diskArea. Remeber, the uploadlimit is 200!');
    }
  }
  
  //create new mediabox
  if ( isset($_POST['mediabox']) && !empty($_POST['mediabox']) ){
    //print_r($_POST['remote'][0]);
    $formArray = array();
    for($i=0;$i<count($_POST['form']);$i++){
      $formArray[ $_POST['form'][$i]['name'] ] = $_POST['form'][$i]['value'];
    }
    if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);
    if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);

    $newDiskareaName = array( 'name' => MySQL::filter( $_POST['mediabox'] ) );
    //check if exist in database

    $options = array (
      'table' => 'media_mediabox',
      'fields' => 'mediabox_id',
      'condition' => $formArray,
      'conditionExtra' => "name LIKE '%".$newDiskareaName['name']."%'",
      'order' => 'mediabox_id',
      'limit' => 1
    );
//print_r( $options );
    $result = MySQL::select($options);
print_r( $result );
    if(!empty($result)){
      $slidesArray = array('error' => 'This name is exist. Try another one!');
    } else {
      $formArray['createdDate'] = date( "Y-m-d H:i:s" , time() );
      $array_of_values = array_merge( $formArray, $newDiskareaName );
      //$insertID = MySQL::insert('media_diskarea',$array_of_values);
      
      $sortname = normalize_special_characters( strtolower( $newDiskareaName['name'] ) );
      $sortname = str_replace( ' ', '', $sortname );
      $slidesArray = array('success' => '', 'name' => $newDiskareaName['name'], 'doname' => $sortname, 'id' => $insertID, 'message' => 'Successfully created the new diskArea. \r\n Remeber, the uploadlimit is 200!');
    }
  }
//print_r($_POST);

} else {
  $slidesArray = array('error' => '', 'message' => 'Misspelled data sent to server!');
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
$resF['result'] = $slidesArray;
$json = json_encode($slidesArray, true);
echo $json;
//print $html;            
?>