<!DOCTYPE html>
<html lang="hu">
<head>
    <title>::iframe test::</title>
	<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/assets/bootstrap/css/bootstrap.css" />
	<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/assets/bootstrap/css/bootstrap-responsive.css" />
	<style>
		.frame{
			padding-top:20px;
		}
		iframe {
			outline: 2px solid #333;
			
			border:0;
		}
		.frameHolder {
			box-shadow: 1px 0px 15px 1px #999;
			-moz-box-shadow: 1px 0px 15px 1px #999;
			-webkit-box-shadow : 1px 0px 15px 1px #999;
		}
	</style>
</head>
<?php

if ( !function_exists( 'hex2bin' ) ) {
    function hex2bin( $str ) {
        $sbin = "";
        $len = strlen( $str );
        for ( $i = 0; $i < $len; $i += 2 ) {
            $sbin .= pack( "H*", substr( $str, $i, 2 ) );
        }

        return $sbin;
    }
}
?>
<body>
	<div class="container">
		<div class="row frame">
			<div class="span9 frameHolder">
				<div class="iframeBorderDiv" style="width:870px;height:489px;border:0;overflow:hidden;"><iframe src="http://ecosparkle.biztretto.com/public/1/3/?tokenId=id1384033005140" width="100%" height="100%" style="border:0;" allowfullscreen="" webkitallowfullscreen="" mozallowfullscreen="" oallowfullscreen="" msallowfullscreen=""></iframe></div>
			</div>
			
			<div class="span12">
			<?
			function objectToArray($d) {
				if (is_object($d)) {
					// Gets the properties of the given object
					// with get_object_vars function
					$d = get_object_vars($d);
				}
		 
				if (is_array($d)) {
					/*
					* Return array converted to object
					* Using __FUNCTION__ (Magic constant)
					* for recursive call
					*/
					return array_map(__FUNCTION__, $d);
				}
				else {
					// Return array
					return $d;
				}
			}
			$request = '{"\/process\/editor\/handelslides\/":"","data":[{"id":"4","type":"normal","templateType":"","badge":"2","tag":"","html":"<div class=\"textClass ResizableClass isSelected slideItem\" style=\"left: 5.670103092783505%; top: 8.623853211009175%; width: 82%; height: 77%; position: absolute;\" data-item-type=\"Text\"><div class=\"movingBox\"><i class=\"icon-move\"><\/i><\/div><div class=\"deleteBox\"><i class=\"icon-remove\"><\/i><\/div><div id=\"myInstance_11f3fefa534c\" class=\"textDiv cke_editable cke_editable_inline cke_contents_ltr cke_show_borders cke_focus\" contenteditable=\"true\" onpaste=\"handlepaste(this, event)\" style=\"position: relative;\" tabindex=\"0\" spellcheck=\"true\" role=\"textbox\" aria-label=\"Rich Text Editor, myInstance_11f3fefa534c\" title=\"Rich Text Editor, myInstance_11f3fefa534c\" aria-describedby=\"cke_523\"><p><strong>Kiegeszito szolgaltataskent rendelheto feladatpeldak:<\/strong><\/p><p><span style=\"color:#000000;\"><span style=\"font-size:20px;\"><strong>\u200b<\/strong><\/span><\/span><\/p><ul><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">falak tisztitasa<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">ablakok tisztitasa belulrol\/kivulrol (evszakfuggo)<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">fugatisztitas goztechnikaval<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">csillarok tisztitasa (technikaja az ugyfellel megbeszeltek alapjan)<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">butorok tisztitasa belulrol<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">egyeb lakasdekoraciok, szemelyes targyak, hang \u2013 es videoeszkozok portalanitasa<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">futotestek, azok hata es azok mogotti falresz tisztitasa<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">butorok es butorelemek kivulrol valo tisztitasa az azokon levo targyak el-es visszapakolasaval<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">kilincsek attorlese<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">lampaernyok porszivozasa, attorlese<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">helyisegenkenti sajatossagok,pl. mosogatas, agynemuhuzas, vasalas, mosas,stb.<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">huto takaritasa belulrol<\/span><\/span><\/li><\/ul><\/div><\/div>","html2":"<div class=\"slideItem\" style=\"left: 5.670103092783505%; top: 8.623853211009175%; width: 82%; height: 77%; position: absolute;\"><p><strong>Kiegeszito szolgaltataskent rendelheto feladatpeldak:<\/strong><\/p><p><span style=\"color:#000000;\"><span style=\"font-size:20px;\"><strong><\/strong><\/span><\/span><\/p><ul><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">falak tisztitasa<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">ablakok tisztitasa belulrol\/kivulrol (evszakfuggo)<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">fugatisztitas goztechnikaval<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">csillarok tisztitasa (technikaja az ugyfellel megbeszeltek alapjan)<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">butorok tisztitasa belulrol<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">egyeb lakasdekoraciok, szemelyes targyak, hang \u2013 es videoeszkozok portalanitasa<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">futotestek, azok hata es azok mogotti falresz tisztitasa<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">butorok es butorelemek kivulrol valo tisztitasa az azokon levo targyak el-es visszapakolasaval<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">kilincsek attorlese<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">lampaernyok porszivozasa, attorlese<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">helyisegenkenti sajatossagok,pl. mosogatas, agynemuhuzas, vasalas, mosas,stb.<\/span><\/span><\/li><li><span style=\"color:#000000;\"><span style=\"font-size:20px;\">huto takaritasa belulrol<\/span><\/span><\/li><\/ul><\/div>","description":"","background":"rgb(255, 255, 255)"}],"action":"update","form":[{"name":"id","value":"1"},{"name":"name","value":"Company standard"},{"name":"diskArea","value":"1"},{"name":"office_id","value":"1"},{"name":"office_nametag","value":"ecosparkl"},{"name":"owner","value":"1"}]}';
			$uncompressed = json_decode($request);
			
			$uncompress = objectToArray($uncompressed);
			
			//print_r( $uncompress['data'][0]['html2'] );
			?>
			</div>
		</div>
	</div>
</body>
</html>