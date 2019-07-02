<?php
include ($_SERVER['DOCUMENT_ROOT'] .'/../include/header/_header_header_text.php');
echo '<!DOCTYPE html>';
?>
<!-- ell browser window size -->
<html style="height: 100%;">
  <head>
  <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no">
  <meta name="apple-mobile-web-app-capable" content="yes">
  <meta name="apple-mobile-web-app-status-bar-style" content="black">
  
  <title>::SKILLBI::SLIDESHOW PREVIEW</title>
  <script type="text/javascript" charset="utf-8" src="/js/jquery-1.8.2.min.js"></script>
  <style>
body, html {background:#EFEFEF;}
.mainslide{	position: relative;	width: 1000px;height: 600px;border-radius: 50px;background:white;z-index:100;}
label, input {float:left;}
.past > * {opacity:.5;z-index:0;}
.past {z-index:0;}
.present > div {opacity:1;z-index:100;}
.past, .present {}
#end.present {background:none;}

video, audio {-webkit-transform: translateZ(1px);-moz-transform: translateZ(1px);}
.impress-not-supported #impress {display:none;}
.fallback-message {font-family: sans-serif;line-height: 1.3;width: 780px;padding: 10px 10px 0;margin: 20px auto;border: 1px solid #E4C652;border-radius: 10px;background: #EEDC94;}
.fallback-message p {margin-bottom: 10px;}
.impress-supported .fallback-message {display: none;}

.step ul {
   list-style-type: square;
   font-size: 1.4em;
   text-align: left;
   line-height: 1.5em;
   list-style-position: inside;
   transform: translateX(-300px);
   -moz-transform: translateX(-300px);
   -ms-transform: translateX(-300px);
   -o-transform: translateX(-300px);
   -webkit-transform: translateX(-300px);
   transition: all 1s ease-in 0s ;
   -moz-transition: all 1s ease-in 0s ;
   -ms-transition: all 1s ease-in 0s ;
   -o-transition: all 1s ease-in 0s ;
   -webkit-transition: all 1s ease-in 0s ;
}
.step.present ul  li{
   transform: translateX(0px);
   -moz-transform: translateX(0px);
   -ms-transform: translateX(0px);
   -o-transform: translateX(0px);
   -webkit-transform: translateX(0px);
 }

 
.progressbar {
	position:absolute;
	right:118px;
	bottom:10px;
	left:118px;
	border-radius:7px;
	border:2px solid rgba(100, 100, 100, 0.2);
}

.progressbar DIV {
	width:0;
	height:10px;
	border-radius:5px;
	background:rgba(75, 75, 75, 0.4);
	-webkit-transition:width 1s linear;
	-moz-transition:width 1s linear;
	-ms-transition:width 1s linear;
	-o-transition:width 1s linear;
	transition:width 1s linear;
}
.progress-off {z-index:2999;}
.progress {position:absolute;right:50px;bottom:10px;text-align: right;}
  </style>
  <meta content="width=device-width, minimum-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
</head>
<body class="impress-on-____slide____1" style="height: 100%; overflow: hidden;">
  <!-- impress -->
  <?
  //print_r($_SERVER['HTTP_USER_AGENT']);
  ?>
  <div id="impress">
  
      <div class="mainslide step" id="____slide____1" data-x="-6562" data-y="-3980"  data-z="-3703" data-scale="1" data-rotate="10"  style="">
      
        <div class="textClass ResizableClass isSelected ui-draggable ui-resizable" style="left: 0%; top: 0.4740740740740741%; position: absolute; width: 100%; height: 9%;"><div class="textDiv"  style="position: relative;"><h1 style="text-align: center;">Sample text...</h1></div></div>
        
        <div class="ResizableClass image isSelected ui-draggable ui-resizable" style="left: 71.6%; top: 59.25925925925925%; position: absolute; width: 24%; height: 29%;"><img style="width: 100%; height: 100%;" src="http://img.varsoft.hu/160x120.gif"></div>
        
        <div class="textClass ResizableClass isSelected ui-draggable ui-resizable" style="left: 71.33333333333334%; top: 26.074074074074073%; position: absolute; width: 16%; height: 26%;"><div class="textDiv " style="position: relative;" ><ul><li>alma</li><li>ata</li><li>kiskacsa</li><li>fekete tó</li><li>beszarábia</li></ul></div></div>
        
        <div class="nonResizableClass audio isSelected ui-draggable" style="left: 56.266666666666666%; top: 15.407407407407408%; position: absolute; width: 20%; height: 7%; border:1px solid black;">
        
        <audio controls="" preload="auto" width="100%" height="100%" style="display: block;width:100%;height:100%" data-x="-6562" data-y="-3980"  data-z="-3703" data-scale="1" data-rotate="10">
          <source src="http://img.skillbi.local/trillala/horse.mp3" type="audio/mpeg">
          <source src="http://img.skillbi.local/trillala/horse.wav" type="audio/wav">
        </audio>

        <!--
        <video controls src="http://img.skillbi.local/trillala/movie.mp4" width="100%" height="100%"></video>
        -->
        </div>
        
        <div class="ResizableClass video isSelected ui-draggable ui-resizable" style="left: 10.933333333333334%; top: 25.833087384259258%; position: absolute; width: 29%; height: 49%;">
        
        <video controls preload="auto" width="100%" height="100%" poster="http://img.skillbi.local/trillala/thumbnail/movie.jpg">
          <source src="http://img.skillbi.local/trillala/movie.mp4" type="video/mp4" />
          <source src="http://img.skillbi.local/trillala/movie.ogg" type="video/ogg" />
          
        </video>
        <!--
        <iframe width="100%" height="100%" src="http://www.youtube.com/embed/XRxTucrU4Q4?fs=1&feature=oembed" frameborder="0" allowfullscreen  ></iframe>
        -->
        <!--
        <object width="100%" height="100%" style="z-index:1002;" id="teszt2"><param name="movie" value="http://www.youtube.com/v/XRxTucrU4Q4&amp;hl=en&amp;fs=1"><param name="allowFullScreen" value="true"><embed src="http://www.youtube.com/v/XRxTucrU4Q4&amp;hl=en&amp;fs=1" type="application/x-shockwave-flash" allowfullscreen="true" width="100%" height="100%" wmode="opaque"></object>
        -->
        </div>
        
      </div>
     
      <div class="mainslide step" id="____slide____2" data-x="-3190" data-y="-2606" data-z="-3449" data-scale=".5" data-rotate="-150" style="">
        
        <div style="left: 55.333333333333336%; top: 44.08888888888889%; width: 41%; height: auto; position: absolute;" class="textClass ResizableClass"><div style="position: relative;" class="textDiv" ><p style=""><span style="font-size:300%;">itt meg valami szöbeg</span></p></div></div>
        <div class="textClass ResizableClass isSelected ui-draggable ui-resizable" style="left: 27.6%; top: 5.684939236111111%; width: 48%; height: 9%; position: absolute;"><div class="textDiv" style="position: relative;" ><h1>Ez teszt Levinek</h1></div></div>
        <div class="ResizableClass image isSelected ui-draggable ui-resizable" style="left: 8.799999999999999%; top: 30.099754050925924%; width: 42%; height: 56%; position: absolute;"><img src="http://img.varsoft.hu/160x120.gif" style="width: 100%; height: 100%;"></div>
      </div>
      
      <div class="mainslide step" id="____slide____3" data-x="-2190" data-y="-1606" data-z="-3449" data-scale="1" data-rotate="-100" style="">
        <div class="textDiv"  style="position: relative;"><h1 style="text-align: center;">Attachment list</h1></div>
      </div>
      
      
      <div id="mainslide step" id="____slide____4" data-x="0" data-y="500" data-z="-3449" data-scale="10" data-rotate="0">
        <span class="author" style="position: relative;bottom: -200px;font-size: 700%;padding: 5%;"><i>by</i><br><span class="name">SKILLBI</span></span>
      </div>
    
  </div>
  <!-- /impress-->
  
  <div class="fallback-message">
    <p>Your browser <b>doesn't support the features required</b> by impress.js,
    so you are presented with a simplified version of this presentation.</p>
    <p>For the best experience please use the latest <b>Chrome</b>,
    <b>Safari</b> or <b>Firefox</b> browser.</p>
</div>

<div class="progressbar"><div></div></div>
<div class="progress"></div>
<!---->
<script type="text/javascript" charset="utf-8" src="/lib/impress.progress.js"></script>
<script type="text/javascript" charset="utf-8" src="/lib/impress.js"></script>
<!--<script type="text/javascript" charset="utf-8" src="/pages/slideeditor/impress.console.js"></script>-->
<script type="text/javascript">
    var iframes = document.getElementsByTagName('iframe');
    var audios = document.getElementsByTagName('audio');
    //alert(audios);
    //$(audios).css('position','relative');
    //alert(iframes.length);
$(function() {
   impress().init();
    //$('#impress').jmpress();
      
    //.on(function(){});
    /*console().init();
    console().open(); // If you want them to open automatically
    */
});
</script>

</body>
</html>