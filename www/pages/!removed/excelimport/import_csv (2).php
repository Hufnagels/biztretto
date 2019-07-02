<?php  
require_once ($_SERVER['DOCUMENT_ROOT'].'/include/config.php');

//if (!isset($_SESSION['li'])) { session_start(); }
include ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_header_image.php');
include ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_include_base.php');
?>
<form action="" method="post" enctype="multipart/form-data" name="form1" id="form1"> 
  Choose your file: <br /> 
  <input name="csv" type="file" id="csv" />
  
</form> 
<input type="button" name="submit" id="submit" value="Submit" class="btn" /> 
<pre id="result"></pre>

<script type="text/javascript">
$(function(){
  $('#submit').click(function(){
    alert( $('#csv').val() );
    $.post('/include/excelimport/process-csv_import.php', $('form#form1').serialize(), function(data){
      $('#result').html(data);
    });
    
  });
});
</script>