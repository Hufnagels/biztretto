<?php
if (!isset($_SESSION['li'])) { session_start(); }
require_once ($_SERVER['DOCUMENT_ROOT'] .'/../include/config.php');
include ($_SERVER['DOCUMENT_ROOT'] .'/../include/header/_header_header_text.php');
include ($_SERVER['DOCUMENT_ROOT'] .'/../include/header/_header_include_base.php');

$protocol = connectionType();

//$m = new mysql();
$sUserId = MySQL::filter(base64_decode($_GET['u'])); //user
$sOfficeNametag = MySQL::filter(base64_decode($_GET['ont'])); //irodaiazonosito
$tip = 0;//MySQL::filter($_GET['tip']);         //aktiv,inaktiv

$sTable = 'office';
$sIndexColumn = 'office_id';

//a tabla mezeinek neve, ami szerepel a lekerdezesben
$aColumns = array( 'office_id', 'office_nametag', 'office_name_hu', 'office_email', 'office_tel', 'office_postcode', 'office_city', 'office_street', 'contact_name', 'contact_title',  'createdDate' );
//a tabla mezeinek neve, ami szerepel a szuresben
$sColumns = array( 'office_nametag', 'office_name_hu', 'office_email','office_city', 'contact_name' );

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

//$sWhere == '' ? $sWhere = "WHERE " : $sWhere = $sWhere;
//$sWhere .= $officeTip;

// SQL queries Get data to display
$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM ".$sTable." ".$sWhere." ".$sOrder." ".$sLimit;
$rResult = MySQL::query( $sQuery, false, false );

function fatal_error ( $sErrorMessage = '' ){
  header( $_SERVER['SERVER_PROTOCOL'] .' 500 Internal Server Error' );
  die( $sErrorMessage );
}
if ( gettype($rResult) == 'string' )
  fatal_error($rResult);

//printR( $sQuery );
//printR( $rResult );
//exit;

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
      $row[] = $rResult[$i]['office_id'];
      $row[] = $rResult[$i]['office_nametag'];
      
      $row[] = '<div class="hybrid" rel="'.arraySearchFieldName('office_name_hu', $arrFields).'">'.$rResult[$i]['office_name_hu'].'</div>';
      $row[] = '<div class="hybrid" rel="'.arraySearchFieldName('office_email', $arrFields).'">'.$rResult[$i]['office_email'].'</div>';
      $row[] = '<div class="hybrid" rel="'.arraySearchFieldName('office_tel', $arrFields).'">'.$rResult[$i]['office_tel'].'</div>';
      
      //$row[] = '<div class="hybrid" rel="'.arraySearchFieldName('office_postcode', $arrFields).'">'.$rResult[$i]['office_postcode'].'</div>';
      //$row[] = '<div class="hybrid" rel="'.arraySearchFieldName('office_city', $arrFields).'">'.$rResult[$i]['office_city'].'</div>';
      //$row[] = '<div class="hybrid" rel="'.arraySearchFieldName('office_street', $arrFields).'">'.$rResult[$i]['office_street'].'</div>';
      $row[] = '<div>'.$rResult[$i]['office_postcode'].', '.$rResult[$i]['office_city'].' '.$rResult[$i]['office_street'].'</div>';
      
      //$row[] = '<div class="hybrid" rel="'.arraySearchFieldName('office_name_en', $arrFields).'">'.$rResult[$i]['office_name_en'].'</div>';
      $row[] = '<div class="hybrid" rel="'.arraySearchFieldName('contact_name', $arrFields).'">'.$rResult[$i]['contact_name'].'</div>';
      $row[] = '<div class="hybrid" rel="'.arraySearchFieldName('contact_title', $arrFields).'">'.$rResult[$i]['contact_title'].'</div>';
      //$row[] = ($rResult[$i]['activeState'] == 1 ? "Aktív" : "Inaktív");
      
      $row[] = $rResult[$i]['createdDate'];
  $output['aaData'][] = $row;
}
 
echo json_encode( $output );
exit;

?>