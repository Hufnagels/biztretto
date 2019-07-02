<?php
require_once ( $_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_auth.php' );
//$_SESSION['url'] = $_SERVER['REQUEST_URI'];
//printR( $_SESSION );
//include ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_header_text.php');
include ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_include_base.php');
//include ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_auth.php');
$protocol = connectionType();
//print_r( $_POST );
//exit;

$returnData = array();
//$m = new mysql();
if ( isset($_POST['form']) && !empty($_POST['form']) ){
  $formArray = array();
  for($i=0;$i<count($_POST['form']);$i++){
    $formArray[ $_POST['form'][$i]['name'] ] = $_POST['form'][$i]['value'];
  }
  
  if ( isset($_POST['query']) && $_POST['query'] !== '' ){
    if (isset($formArray['id'])) unset($formArray['id']);
    if (isset($formArray['name'])) unset($formArray['name']);
    if (isset($formArray['owner'])) unset($formArray['owner']);
    $options = array (
      'table' => 'user_u',
      'fields' => 'department AS name',
      'condition' => $formArray,
      'conditionExtra' => "department LIKE '%".$_POST['query']."%'",
      'order' => 'department',
      'limit' => 100
    );
    $result = MySQL::select($options);
    /*
    //for bootstrap-typeahead from  https://gist.github.com/1866577
    foreach($result as $row)
      $returnData[] = $row;
    */
    
    //for normal bootstrap
    foreach($result as $row)
      $returnData[] = $row['name'];
  }
} else {
  $returnData = array('');
}
//exit;
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
$resF['result'] = $returnData;
$json = json_encode($returnData, true);
echo $json;    
?>