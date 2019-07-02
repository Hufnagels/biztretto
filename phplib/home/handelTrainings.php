<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once ( $_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_auth.php' );

require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_include_base.php');


$homePageList = Statistics::getTrainingListHomePage($_SESSION['u_id']);
foreach ($homePageList['present'] as $id=>$row)
{
    $trainingsArray[]=array(
                    'id'=>$id,
                    'name'=>$row['data']['meta']['title'],
                    'cover'=>$row['data']['meta']['cover'],
                    'description'=>$row['data']['meta']['description'],
                    'pieces'=>count($row['slideshows']),
                    'visited'=>$row['data']['folyamatban'],
                    'solved'=>$row['data']['elvegzett'],
                    'authors'=>$row['data']['meta']['authors']
    );
}

if ($trainingsArray) {
    $returnData['result'] = $trainingsArray;
    $returnData['extra']['result'] = $trainingsArray;

} else {
    $returnData = array(
      'type' => 'info',
      'message' => 'No trainings to solve'
  );
}
printSortResult($returnData);


?>