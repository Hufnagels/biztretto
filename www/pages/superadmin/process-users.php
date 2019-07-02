<?php
if (!isset($_SESSION['li'])) { session_start(); }
require_once ($_SERVER['DOCUMENT_ROOT'] .'/../include/config.php');
include ($_SERVER['DOCUMENT_ROOT'] .'/../include/header/_header_header_text.php');
include ($_SERVER['DOCUMENT_ROOT'] .'/../include/header/_header_include_base.php');

$protocol = connectionType();

$sUserId = MySQL::filter(base64_decode($_GET['u'])); //user
$sOfficeNametag = MySQL::filter(base64_decode($_GET['ont'])); //irodaiazonosito
$tip = 0;//MySQL::filter($_GET['tip']);         //aktiv,inaktiv

$sTable = 'user_u';
$sIndexColumn = 'u_id';

//a tabla mezeinek neve, ami szerepel a lekerdezesben
$aColumns = array( 'u_id', 'office_nametag', 'full_name', 'userlevel', 'user_name', 'user_email', 'user_tel', 'activeState', 'ctime', 'profilePicture', 'approved',  'banned' );
//a tabla mezeinek neve, ami szerepel a szuresben
$sColumns = array( 'full_name', 'user_name', 'user_email','user_tel' );

//a valtozok filterezese
foreach ($_GET as $key => $value){
  $_GET[$key] = MySQL::filter($value);
}

//printR( $_GET );

// Paging
$sLimit = "";
if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' ){
  $sLimit = "LIMIT ".MySQL::filter( $_GET['iDisplayStart'] ).", ". MySQL::filter( $_GET['iDisplayLength'] );
}
 
// Ordering
$sOrder = "";
if ( isset( $_GET['iSortCol_0'] ) ){
  $sOrder = "ORDER BY  ";
  for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ ){
    if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" ){
      $sOrder .= $aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".MySQL::filter( $_GET['sSortDir_'.$i] ) .", ";
    }
  }
 
  $sOrder = substr_replace( $sOrder, "", -2 );
  if ( $sOrder == "ORDER BY" ){
    $sOrder = "";
  }
}

// Filtering
$sWhere = "";
if ( isset($_GET['sSearch']) && $_GET['sSearch'] != "" ){
  $sWhere = "WHERE (";
  for ( $i=0 ; $i<count($sColumns) ; $i++ ){
    if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" ){
      $sWhere .= $sColumns[$i]." LIKE '%".MySQL::filter( $_GET['sSearch'] )."%' OR ";
    }
  }
  $sWhere = substr_replace( $sWhere, "", -3 );
  $sWhere .= ')';
}
 
// Individual column filtering
for ( $i=0 ; $i<count($aColumns) ; $i++ ){
  if ( isset($_GET['bSearchable_'.$i]) && $_GET['bSearchable_'.$i] == "true" && $_GET['sSearch_'.$i] != '' ){
    if ( $sWhere == "" ){
      $sWhere = "WHERE ";
    } else {
      $sWhere .= " AND ";
    }
    $sWhere .= $sColumns[$i]." LIKE '%".MySQL::filter($_GET['sSearch_'.$i])."%' ";
  }
}

//SPECIALIS szukites
$officeTip= "";//" office_nametag='$sOfficeNametag'";

$sWhere == '' ? $sWhere = "WHERE " : $sWhere = $sWhere;
$sWhere .= $officeTip;

// SQL queries Get data to display
$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM ".$sTable." ".$sWhere." ".$sOrder." ".$sLimit;
$rResult = MySQL::query( $sQuery, false, false );

function fatal_error ( $sErrorMessage = '' ){
  header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
  die( $sErrorMessage );
}
if ( gettype($rResult) == 'string' )
  fatal_error($rResult);

printR( $sQuery );
printR( $rResult );
exit;

// Data set length after filtering
$sQuery = "SELECT FOUND_ROWS()";
$rResultFilterTotal = MySQL::query( $sQuery, false, false );
$iFilteredTotal = array_values($rResultFilterTotal[0]);

// Total data set length
$sQuery = "SELECT COUNT(".$sIndexColumn.") FROM ".$sTable;
$rResultTotal = MySQL::query( $sQuery, false, false);
$iTotal = array_values($rResultTotal[0]);

// Output
$output = array(
  "sEcho" => intval($_GET['sEcho']),
  "iTotalRecords" => $iTotal,
  "iTotalDisplayRecords" => $iFilteredTotal,
  "aaData" => array()
);

$row = array();
$db = count( $rResult );

//exit;
$arrFields = MySQL::mysqlFetch($sTable);

for ( $i=0;$i<$db;$i++ ){
  $row = array();
  
      //0 hirdetes azonosito hidden
      $row[] = $rResult[$i]['u_id'];
      //1 cim 
      $row[] = $rResult[$i]['office_nametag'];
      //2 szerzo
      $row[] = '<div class="hybrid" rel="'.arraySearchFieldName('full_name', $arrFields).'">'.$rResult[$i]['full_name'].'</div>';
      $szint = '';
      switch ($rResult[$i]['userlevel']) {
        case '7': $szint = 'admin'; break;
        case '5': $szint = 'iroda vezető'; break;
        case '3': $szint = 'szerkesztő'; break;        
      }
      //3 tipus 
      $row[] = $szint;
      //4 intezmeny  
      $row[] = '<div class="hybrid" rel="'.arraySearchFieldName('user_name', $arrFields).'">'.$rResult[$i]['user_name'].'</div>';
      //4 intezmeny  
      $row[] = '<div class="hybrid" rel="'.arraySearchFieldName('user_email', $arrFields).'">'.$rResult[$i]['user_email'].'</div>';
      //4 intezmeny  
      $row[] = '<div class="hybrid" rel="'.arraySearchFieldName('user_tel', $arrFields).'">'.$rResult[$i]['user_tel'].'</div>';
      //5 keszult 
      $row[] = ($rResult[$i]['activeState'] == 1 ? "Aktív" : "Inaktív");
      
      $row[] = date("Y-m-d G:j:s", intval($rResult[$i]['ctime']));

      //7 icons
        $sOutput = '';
        if ($rResult[$i]['profilePicture'] != '') {
          $sOutput = '<img src="data:image/jpg;base64,'.$rResult[$i]['profilePicture'].'" width="50" height="50">';
        } else {      
          $sOutput = '';
        }
      $row[] =  $sOutput;
      
        $sOutput = '';
        //<span class=\"sprites view\"></span>
        $sOutput .= '<div class="tableAction"><i class="icon-edit"></i></div>';
        if ($rResult[$i]['u_id'] != $sUserId) {
          $sOutput .= '<input type="checkbox" id="chk'.$rResult[$i]['u_id'].'" name="chk'.$rResult[$i]['u_id'].'" value="'.$rResult[$i]['u_id'].'" class="DTInputs" />';
        }
      $row[] = $sOutput;
/*
  for ( $i=0 ; $i<count($aColumns) ; $i++ ){
    $row[] = $rResult[$j][ $aColumns[$i] ];
    //$row['DT_RowClass'] = 'alert alert-error';
  }
*/

  $output['aaData'][] = $row;
}
 
echo json_encode( $output );
exit;

?>