<!DOCTYPE html>
<html lang="hu">
<head>
    <title>::iframe test::</title>
	<meta name="apple-mobile-web-app-capable" content="yes">
	<meta name = "viewport" content = "initial-scale = 1, user-scalable = no">
	<?
	echo '<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/assets/bootstrap/css/bootstrap.css" />';
	echo '<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/assets/bootstrap/css/bootstrap-responsive.css" />';
	
	require_once ($_SERVER['DOCUMENT_ROOT'] . '/_jqueryLoad.php');
	?>
	<style>
		
		iframe {
			outline: 2px solid #333;
			
			border:0;
		}
		.frameHolder {

			width: 100%;
			height: 100%;
			margin: 0;
		}
		.iframeBorderDiv {
			width:1024px;height:600px;
			/*width: 100%;
			height: 100%;*/
			border:0;overflow:hidden;
			box-shadow: 1px 0px 15px 1px #999;
-moz-box-shadow: 1px 0px 15px 1px #999;
-webkit-box-shadow: 1px 0px 15px 1px #999;
		}
		
	</style>
</head>
<body style="width:100%;height: 100%;overflow: hidden;">
	<div class="frameHolder">
		<div class="iframeBorderDiv" style="" id="ifr">
			<iframe src="http://ecosparkle.biztretto.com/public2/1/3/?tokenId=id1384033005140" width="100%" height="100%" style="border:0;" allowfullscreen="" webkitallowfullscreen="" mozallowfullscreen="" oallowfullscreen="" msallowfullscreen=""></iframe>
			<!--
			http://ecosparkle.biztretto.com/public/1/3/?tokenId=id1384033005140
			http://lab.hakim.se/reveal-js/
			-->
			
		</div>
	</div>
</body>
</html>