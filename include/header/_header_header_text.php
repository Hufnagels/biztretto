<?
header('Content-type: text/html; charset=utf-8');
/**/
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: 0");
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past 
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: private",false); // required for certain browsers 
header("Pragma: no-cache");

?>