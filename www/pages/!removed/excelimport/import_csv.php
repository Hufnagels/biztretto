<?php  
require_once ($_SERVER['DOCUMENT_ROOT'].'/include/config.php');
//if (!isset($_SESSION['li'])) { session_start(); }

include ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_header_image.php');
include ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_include_base.php');
//include ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_auth.php');
?>
<link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap.css" />
  <link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/assets/bootstrap/css/bootstrap-responsive.css" />
  <!--<link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/css/fonts.css" />-->
  
  <!--SITE OWN CSS-->
  <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/basecolor.css" />
  <!--<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/color.css" />-->
  
  <link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/css/layout.css" />
<script type="text/javascript" charset="utf-8" src="/js/jquery-1.8.2.min.js"></script>
<script type="text/javascript" src="/assets/ajaxupload.3.5.js"></script>

  <div class="span3">
    <div class="well well-small">
      <div id="upload" class="btn btn-grey">Upload File</div><span id="status" ></span>
    </div>
    <pre id="files" style="display:none;"></pre>
  </div>
  <div class="span7">
    <div class="alert alert-info">
      <strong>FIGYELMEZTETÉS</strong>
      <p>Excel-ből történő exportálás esetén a következő képpen kell eljárni:</p>
      <dd style="list-style-type:decimal">
        <li>Töltse le a következő csv file-t, amely tartalmazza az első sorban a mezőneveket</li>
        <li>Töltse fel adatokkal</li>
        <li>File->Mentés másként->"CSV (pontosveszzővel tagolt) (*.csv)" fájl típust válassza</li>
        <li>A mentés során a program által feltett kérdésekre "IGEN" választ adjon minden esetben</li>
      </dt>
    </div>
  </div>
  <div class="clearfix"></div>
  <div class="row-fluid"><!--ContentEditor-->
  <div class="span10">
    <div id="response" class="alert alert-info">Waiting...</div>
    <div id="result" ></div>
  </div>
  </div>

<script type="text/javascript">
$(function(){
var btnUpload=$('#upload');
	var status=$('#response');
  var result=$('#result');
  $('#files').html();
	new AjaxUpload(btnUpload, {
		action: '/pages/excelimport/process-csv_import.php',
		//Name of the file input box
		name : 'uploadfile',
    data : {'table':'u', 'table2':'0'},
		onSubmit: function(file, ext){
			if (! (ext && /^(csv)$/.test(ext))){ 
				status.text('Only csv files are allowed');
				return false;
			}
			status.text('Uploading...');
		},
		onComplete: function(file, response){
      $('#files').html(file).show();
alert( response );
			
			if(response.length>1){
        status.attr('class', 'alert alert-success').html('ssssss');
        result.html(response);
			} else{
				status.attr('class', 'alert alert-error').html('ssssss');
			}
		}
	});

});
</script>
