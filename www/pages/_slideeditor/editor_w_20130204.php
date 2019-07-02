<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest'){
	require_once ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/404.php' );
  //return "papo2";
  exit();
}
require( $_SERVER['DOCUMENT_ROOT'] .'/../include/authenticate.php' );
if (!$_SESSION['logged_in']){
  include ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/403forbidden.php' );
  //return "papo2";
  exit();
}
 
session_start();

$_SESSION['url'] = $_SERVER['REQUEST_URI'];
//printR( $_SESSION );
require_once ($_SERVER['DOCUMENT_ROOT'] .'/../include/config.php');
include ($_SERVER['DOCUMENT_ROOT'] .'/../include/header/_header_header_text.php');
include ($_SERVER['DOCUMENT_ROOT'] .'/../include/header/_header_include_base.php');
//include ($_SERVER['DOCUMENT_ROOT'] .'/../include/header/_header_auth.php');
$protocol = connectionType();
$imageURL = $_SESSION['office_nametag'].'.'.DOMAINTAG.'/media/';

//clearstatcache();
//set_error_handler("customError");
$m = new mysql();
$uid = $m->filter( base64_decode($_GET['u']) ); //user
$ont = $m->filter( base64_decode($_GET['i']) ); //irodaiazonosito
//$sql = "SELECT diskArea_id, name FROM media_diskarea WHERE office_nametag = '".$_SESSION['office_nametag']."' u_id = ".$_SESSION['u_id'];
$selectArray = array (
      'table' => 'media_diskarea',
      'fields' => 'diskArea_id, name',
      'condition' => array('office_nametag' => $_SESSION['office_nametag']),
      'conditionExtra' => " NOT name = 'corporate'",
      'order' => '1',
      'limit' => 50
    );
$result = $m->select($selectArray);
//printR( $result );
$diskAreaJson = json_encode( $result, true );
//SureRemoveDir(IMGPATH.'/'.$_SESSION['office_nametag'].'/', false);
//SureRemoveDir(IMGPATH.'/'.$_SESSION['office_nametag'].'/thumbnail', false);

//printR($_GET);
//name convention

$span2Header = 'slideshow';
$loadslideshow_Button = 'slideshow';
$saveslideshow_Button = 'save';
$saveasslideshow_Button = 'save as';
$span10Header = 'slide designer';
$createtext_Button = 'create text';

$addnewslide_Button = 'new slide';
$saveslide_Button = 'save slide';

$newFormInputPlaceholder = ' Slide...';

$accordion0_Header = 'mediabox';
$accordion0_addmedia_Button = 'add media';
$accordion1_Header = 'slideshows';
$accordion1_select_Button = 'slideshow';
$accordion2_Header = "tests";

?>
<script type="text/javascript" charset="utf-8" src="/lib/impress.js"></script>
<!--
<script src="http://html5demos.com/h5utils.js"></script>

<script type="text/javascript" charset="utf-8" >CKEDITOR_BASEPATH = '/assets/ckeditor/';</script>
<script type="text/javascript" charset="utf-8" src="/assets/ckeditor/ckeditor.cust.js"></script>
  


<script type="text/javascript" charset="utf-8" >

  if (CKEDITOR) {
    for(name in CKEDITOR.instances){
      CKEDITOR.instances[name].destroy()
    }
  }

  CKEDITOR.editorConfig = function( config ){
    config.language = 'hu';
    config.toolbar = 'Extra';
  };
  CKEDITOR.disableAutoInline = true;
/*  
  var editor;
  
  
  CKEDITOR.on( 'dialogDefinition', function( event ) {
      var dialogDefinition = event.data.definition,
      genericOnShow = dialogDefinition.onShow;
      dialogDefinition.onShow = function() {
        genericOnShow.apply( this );
        this.move( 10, 10 );
        // ...or anything you want ;)
      }
    });
*/ 
</script>
-->

<form id="saveslidesform" action="#" method="POST" class="hidden1">
  <input type="hidden" name="id" value="" />
  <input type="hidden" name="name" value="" />
  <input type="hidden" name="diskArea_id" value="<?=$result[0]['diskArea_id'];?>" />
  <input type="hidden" name="office_id" value="<?=$_SESSION['office_id']?>" />
  <input type="hidden" name="office_nametag" value="<?=$_SESSION['office_nametag']?>" />
  <input type="hidden" name="owner" value="<?=$_SESSION['u_id']?>" />
</form>

  <div class="row special">
    <!-- LEFT SIDE -->
    <div class="span2">
      <div id="accordionLeft">
        <div class="colHeader">
          <h3 class="orangeT"><span class="pull-left"><?=$span2Header;?></span><span class="pull-right functionHeader" id="createNewSlideShow">New</span></h3>
        </div>
          <!--accordion content 0 -->
          <div id="accordionContent">
            <!-- createNewslide -->
            <div class="clearfix"></div>
            <div class="createNew well well-small">
              <div class="dropdown">
                <h3 class="functionHeader middlegreyT2" id="myslidesContainer">
                  <span><?=$loadslideshow_Button;?>:</span>
                  <div class="pull-right">  
                    <span class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#" id="loadSlideShowButton"><span class="name">select</span><b class="caret"></b></span>
                    <ul class="dropdown-menu" id="loadSlideshow"></ul>  
                  </div>
                </h3>
              </div>
            </div>

            <div class="clearfix"></div>
            <!-- /createNewslide -->
            
            <!-- slideshowHolder -->
            <div class="slidesHolder">
              <ul class="slidesList" id="slidesList" data-object-type="slidesList" style="">
              </ul>
            </div>
            <div class="clearfix"></div>
            <div class="createNew well well-small">
              <div class="dropdown">
                <h3 class="functionHeader middlegreyT2" id="myslidesContainer">
                  <div class="clearfix"></div>
                  <div class="dropdown pull-left functionHeader">
                    <button class="btn btn-dark btn-l" role="button" id="saveSlideshow"><?=$saveslideshow_Button;?></button>
                  </div>
                  <div class="dropdown pull-right functionHeader">
                    <button class="btn btn-dark btn-r" role="button" id="saveSlideshow"><?=$saveasslideshow_Button;?></button>
                  </div>
                </h3>
              </div>
            </div>
            <!-- /slideshowHolder -->
          </div>
        <div class="colHeader"><h3 class="orangeT "><span class="pull-left">attachment</span></h3></div>
          <!--accordion content 1 -->
          <div id="accordionContent">
            <!-- attachementContainer -->
            <div class="clearfix"></div>
            <!-- attachement -->
            <ul class="sub mediaBox span2" id="attachementList" >
              
            </ul>
            <!-- /attachement -->
          </div>
            <!-- /attachementContainer -->
          <div class="clearfix"></div>
      </div>


    </div>
    <!-- /LEFT SIDE -->
    
    <!-- MIDDLE SIDE -->
    <div class="span8">
      <!-- slideEditor -->
      <div id="slideEditor" class="affix-top whiteB">
        <div class=" colHeader"><h3 class="orangeT "><span class="pull-left"><?=$span10Header;?></span><span class="pull-left" id="bc"></span></h3></div>
        <div class="clearfix"></div>
        <!-- editorTopBar -->
        <div class="createNew well well-small" id="editorTopBar">
          <div class="pull-left">
            <h3 class="functionHeader middlegreyT2">
              <div class="btn-toolbar">
                <div class="btn-group" data-toggle="button">
                  <button type="button" class="btn btn-dark" id="addText"><?=$createtext_Button;?></button>
                  
                  <!--<div id="myNicPanel" style="width: 525px;float:left;"></div>-->
                </div>
                <div class="btn-group">
                  <!--<button type="button" class="btn btn-dark" id="addSample">add sample</button>-->
                  <button type="button" class="btn btn-dark" id="clearArea">clear area</button>
                </div>
              </div>
             </h3>  
          </div>

          <div class="pull-right">
            <button class="btn btn-dark" id="newSlide"><?=$addnewslide_Button;?></button>
            <div class="btn-group" data-toggle="button">
              <button type="button" class="btn btn-dark active" id="removeBG"><i class="icon-th"></i></button>
            </div>
          </div>          
<div class="clearfix"></div>
<div id="myNicPanel"></div>          
        </div>
        <!-- /editorTopBar -->
        <div class="clearfix"></div>
        <div class="well well-small middlegreyB" id="editorAreaContainer">
          <!-- editorArea -->
          <form action="#" id="editorAreaForm">
          
          
          <div class="editorArea" id="editorArea" data-template-type="normal">
<div id="impress" style="-webkit-transform-origin: 0% 0%;-webkit-transition: -webkit-transform 1s ease-in-out 0s,-webkit-perspective, opacity;-webkit-transform-style: preserve-3d;position: absolute;top: 50%;left: 50%;-webkit-perspective: 1000px;-webkit-transform: translate(-50%, -50%) scaleX(1) scaleY(1) scaleZ(1);width: 500px;height: 300px;overflow: hidden;">
  <div class="impressSlidesHolder" style="position: absolute;-webkit-transform-origin: 0% 0%;-webkit-transition: all 500ms ease-in-out 0ms;-webkit-transform-style: preserve-3d;-webkit-transform: rotateZ(-180deg) rotateY(0deg) rotateX(0deg)translate3d(-48000px, 0px, 0px);">

  </div>
</div>
          </div>
          
          
          </form>
          <!-- /editorArea -->
          <div class="clearfix"></div>
          <!-- editorBottomBar -->
          <div id="editorBottomBar">
            <div class="pull-left">
              <form class="form-inline">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-tag"></i></span>
                  <input type="text" name="slideTag" id="slideTag" maxlength="16" class="input-large" placeholder="New tag">
                </div>
              </form>
            </div>
            
            <div class="pull-left level">
              <form class="form-inline">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-adjust"></i> Level </span>
                  <select name="levelCount" class="input-small">
                    <option value="1">1</option>
                    <option value="2">2</option>
                    <option value="3">3</option>
                  </select>
                </div>
              </form>
            </div>
            <div class="pull-left level">
              <button class="btn btn-dark" onclick="//console.log( 'submitform' );//console.log( $('#editorAreaForm').serialize() )">submit form</button>
            </div>
            <div class="pull-right">

              <button id="saveSlide" class="btn btn-dark" ><?=$saveslide_Button;?></button>
            </div>
          
            <div class="clearfix"></div>
            <div class="interactiveHelp">
              <span id="showHelp" class="icon-exclamation-sign"></span>
              <div id="content" class="transitions middlegreyB closed " >
            <div class="inner"></div></div>
            </div>
            <div class="clearfix"></div>
            <div class="testSlideEditorContainer">
              <div class="trafficmanagerContainer pull-left">
                <h3 class="functionHeader darkgreyT">Traffic data</h3>
                <div class="pull-left trafficDataHolder">
                  <!--<form style="display: block;" class="form-inline" id="trafficForm" action="" method=""></form>
                    <div class="control-group"><input type="text" placeholder="goto" class="square" id="slidePrev"><i class="icon-chevron-left"></i><input type="text" placeholder="score" class="square" id="slideScore"><i class="icon-chevron-left"></i><input type="text" placeholder="goto" class="square" id="slideNext"></div>-->
                </div>
<!--
                <div class="pull-left">
                  <button class="btn btn-dark" id="addTrafficRow"><i class="icon-plus"></i></button>
                </div>
-->
              </div>
              <div class="toolsContainer span4 pull-right">
                <h3 class="functionHeader darkgreyT">Test elements</h3>
                <div class="toolsContainerElements pull-left">
                  <input type="hidden" id="tmpType" value="" />
                  <div class="control-group">
                    <ul class="tools"></ul>
                  </div>
                </div>
              </div>
            </div>
            <div class="clearfix"></div>
            <div class="description">
            <h3 class="functionHeader darkgreyT">Description</h3>
              <textarea id="descArea" style="width:100%;height:100px"></textarea>
            </div>
          </div>
          <!-- /editorBottomBar -->


        </div>
      </div>
      <!-- /slideEditor -->
    </div>
    <!-- MIDDLE SIDE -->
    
    <!-- RIGHT SIDE -->
    <div class="span2">
      <div id="accordionRight">
        <div class=" colHeader"><h3 class="orangeT "><span class="pull-left"><?=$accordion0_Header;?></span><span class="pull-right functionHeader" id="addFilesEditor"><?=$accordion0_addmedia_Button;?></span></h3></div>
          <!--accordion content 1 -->
          <div id="accordionContent" style="dispaly:block;">
            <div class="createNew well well-small" id="themeRow" style="display:block;">
              <div class="dropdown">
                <h3 class="functionHeader middlegreyT2" id="mbArea"><span>folder:</span>
                  <!--<button class="btn btn-dark btn-r" data-dropdown="#mediaBoxList" data-horizontal-offset="-20" data-vertical-offset="50" id="selectMediaBox">mediabox<span class="caret"></span></button>-->
                  <div class="pull-right">
                  <?
                  if(count($result) > 1) {
                    echo '<span class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#" data-diskarea-id="'.$result[0]['diskArea_id'].'"><span class="name">'.$result[0]['name'].'</span><b class="caret"></b></span><ul class="dropdown-menu" id="themeList">';
                        for($i=0;$i<count($result);$i++){
                          echo '<li class="'.($i == 0 ? 'selected"':'').'"><a href="javascript:void(0)" class="level1" data-diskarea-id="'.$result[$i]['diskArea_id'].'">'.$result[$i]['name'].'</a></li>';
                        }
                        echo '</ul>';
                  } else {
                    echo '<span class="selectMenu btn-dark btn-r" role="button" data-diskarea-id="'.$result[0]['diskArea_id'].'"><span class="name">'.$result[0]['name'].'</span></span>';
                  }
                  ?>
                    </div>  
                </h3>
              </div>
            </div>
            <div class="createNew well well-small">
              <div class="dropdown">
                <h3 class="functionHeader middlegreyT2" id="mbArea"><span>mediabox:</span>
                  <!--<button class="btn btn-dark btn-r" data-dropdown="#mediaBoxList" data-horizontal-offset="-20" data-vertical-offset="50" id="selectMediaBox">mediabox<span class="caret"></span></button>-->
                  <div class="pull-right">  
                        <span class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#" ><span class="name">select</span><b class="caret"></b></span>
                        <ul class="dropdown-menu" id="mediaBoxList"></ul>  
                    </div>  
                </h3>
              </div>
            </div>
            <!-- sortingBar -->
            <div class="createNew well well-small" id="sortingIconBar">
              <div class="btn-group" data-toggle="buttons-radio">
                <button type="button" class="btn btn-dark active" data-class=""><i class="icon-eye-open"></i></button>
                <button type="button" class="btn btn-dark" data-class="image"><i class="icon-camera"></i></button>
                <button type="button" class="btn btn-dark" data-class="video"><i class="icon-film"></i></button>
                <button type="button" class="btn btn-dark" data-class="audio"><i class="icon-music"></i></button>
                <button type="button" class="btn btn-dark" data-class="word excel pdf"><i class="icon-file"></i></button>
              </div>
            </div>
            <!-- /sortingBar -->
            <!-- selected mediaBox -->
            <div class="sideBar span2">
              <ul class="mediaBox span2 sub greyB" id="editorsMediaBox">

              </ul>
            </div>
            <!-- selected mediaBox -->
          </div>
          <!--accordion content 1 -->

        <div class=" colHeader"><h3 class="orangeT "><span class="pull-left"><?=$accordion2_Header;?></span></h3></div>
          <!--accordion content 2 -->
          <div id="accordionContent" style="dispaly:none;">
            <div id="templateContainer">
              <ul class="slidesList span2" id="testSlides" data-object-type="slidesList">
                <? include($_SERVER['DOCUMENT_ROOT'].'/pages/slideeditor/templates.php' ); ?>
              </ul>
            </div>
          </div>
          <!--/accordion content 2 -->
          
          <div class=" colHeader"><h3 class="orangeT "><span class="pull-left"><?=$accordion1_Header;?></span></h3></div>
          <!--accordion content 2 -->
          <div id="accordionContent" style="dispaly:none;">
            <div class="createNew well well-small">
              <div class="dropdown">
                <h3 class="functionHeader middlegreyT2"><span><?=$accordion1_select_Button;?>:</span>
                    
                    
                  <div class="pull-right">
                    <span class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#" id="loadSlideShow2Button"><span class="name">select</span><b class="caret"></b></span>
                    <ul class="dropdown-menu" id="loadSlideshow2"></ul>  
                  </div>
                  
                </h3>
              </div>
            </div>
            <div id="slidesList2Container">
              <ul class="slidesList" id="slidesList2" data-object-type="slideList"></ul>
            </div>
          </div>
          <!--/accordion content 2 -->
      </div>
      
<div id="mediaBoxList" class="dropdown-menu has-tip anchor-right">
  <ul>
  </ul>  
</div>
    </div>
    <!-- /RIGHT SIDE -->
  </div>
<div class="clearfix"></div>

<input type="button" class="btn" id="toArray" />
<pre id="toArrayOutput" class="row"></pre>

<script type="text/javascript" charset="utf-8" src="/lib/nested.sortable.js"></script>
<script src="/assets/BeNicedit/nicEdit.js" type="text/javascript"></script>
<script type="text/javascript" charset="utf-8" src="/lib/seFunction.js"></script>
<script type="text/javascript" charset="utf-8" >

$(function() {
var folderString = $.parseJSON( '<?=$diskAreaJson;?>' );
//console.log( folderString );
//teszt
$('#toArray').click(function(e){
  arraied = $slides.nestedSortable('toArray', {startDepthCount: 0, slideShowId: $('#saveslidesform [name="id"]').val()});
//console.log(arraied);
  arraied = dump(arraied);
  (typeof($('#toArrayOutput')[0].textContent) != 'undefined') ? $('#toArrayOutput')[0].textContent = arraied : $('#toArrayOutput')[0].innerText = arraied;
});

//teszt
  //Set default open/close settings
  $('#accordionLeft #accordionContent').hide();
  $('#accordionLeft #accordionContent:first').show();

  $('#accordionLeft .colHeader .pull-left').bind('click', function(){
  //console.log('accordion header clicked');
    if( $(this).parent().parent().next().is(':hidden') ) { //If immediate next container is closed...
      $('#accordionLeft #accordionContent').slideUp(); 
      //Remove all .acc_trigger classes and slide up the immediate next container
      $(this).parent().parent().next().slideDown(); 
      //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    } else {
      $(this).parent().parent().next().slideUp(); 
    }
    return false; //Prevent the browser jump to the link anchor
  });

  $('#accordionRight #accordionContent').hide(); //Hide/close all containers
  $('#accordionRight #accordionContent:first').show();

  $('#accordionRight .colHeader .pull-left').bind('click', function(){
  //console.log('accordion header clicked');
    if( $(this).parent().parent().next().is(':hidden') ) { //If immediate next container is closed...
      $('#accordionRight #accordionContent').slideUp(); 
      //Remove all .acc_trigger classes and slide up the immediate next container
      $(this).parent().parent().next().slideDown(); 
      //Add .acc_trigger class to clicked trigger and slide down the immediate next container
    } else {
      $(this).parent().parent().next().slideUp(); 
    }
    return false; //Prevent the browser jump to the link anchor
  });


/*
  $( "#accordionLeft, #accordionRight" ).accordion(accordionOption);
  $('#accordionRight').accordion({active: 1});

  $().button('toggle');

  $('.dropdown-toggle').dropdown();

  $('.dropdown input, .dropdown label').click(function(e) {
    e.stopPropagation();
    return false;
  });
  */
/*  
  //affix main panel
  $('#slideEditor').affix({
    offset: {
      top: function () { return $window.width() <= 1200 ? 100 : 45 },
      bottom: 0
    }
  });
*/
  //INIT
  se_pageInit();
  editor = new nicEditor();
  editor.panelInstance('descArea');
  

      
/*-------------------------------------------------------------------*/
//  for testing
//new nicEditor().setPanel('myNicPanel');
//$('#editorArea video').video();
//$('#editorsMediaBox li').draggable(myMediaDragOption);
/*  
  //add sample
  $('#addSample').bind('click', function(){
    $editor.html('').append(exampleSlide);
    //new nicEditor().setPanel('myNicPanel').addInstance('myInstance_16');

   editor = new nicEditor();
   editor.addInstance('myInstance_63').floatingPanel();
   editor.addInstance('myInstance_16').floatingPanel();
   //new nicEditor().floatingPanel().addInstance('myInstance_16')

//console.log( nicEditors.editors );
    
    //CKEDITOR.inline( 'myInstance_16' );
    //CKEDITOR.inline( 'myInstance_63' );
    
    //$('#editorsMediaBox li').draggable('destroy');
    addUIevents();
    $(this).removeClass('active');
  });
*/

//  /for testing
/*-------------------------------------------------------------------*/

/*
  //set selected state of elements on editorArea
  $('.movingBox').live('click', function(){
    //$(this).parent().toggleClass('isSelected');
  });
*/
  
  
//left side slideshow
  
  //load slidshow
  $('#createNewSlideShow').bind('click', function(){
    se_clearData('all');
//console.log( 'new slideshow triggered' );
    $('#saveSlideshow').trigger('click');
    if(!slideShowSaved) return false;
  });
  
  //save slideshow
  $('#saveSlideshow').bind('click',function(){
    if( checkFormState('saveslidesform')  ){
      sendMessage('alert-warning', 'Try to use save as!');
      return false;
    }
    var data = [];
    data['result'] = [];
    data['result'].push({'input':'', 'desc':'', 'folders':folderString});
//console.log( data );
    var body = tmpl('tmpl-saveslideshow', data) ;
    $("#confirmDiv").confirmModal({
      heading: 'Give a name of your slideshow',
      body: body,
      text:'Save',
      callback: function () {
        if($('#slideshowName').val() == ''){
          sendMessage('alert-error', 'Empty name is forbidden');
          return false;
        }
        slideShowSaved = true;
        slideSaved = true;
        var mgData = [];
        mgData.push({'name':$('#slideshowName').val(),'description':$('#slideshowDescription').val()});
        var response = handelSlideShow(mgData, $('#saveslidesform').serializeArray(), 'save');
//console.log( 'saved new slideshow?' );
//console.log( response );
        if(!response.result.name){
          sendMessage('alert-error', response.result.error);
          return false;
        }
        $('#saveslidesform input[name="id"]').val(response.result.id);
        $('#saveslidesform input[name="name"]').val(response.result.name);
        
        $('#bc').html('<span class="divider">>&nbsp;</span><span class="editable" id="ssName" data-type="text">' +response.result.name+'</span>');

        $.when(se_loadSlideShowList()).done(function(a){
        $('#loadSlideshow a[data-id="'+response.result.id+'"]').parent().addClass('selected');
        });
//console.log( slideShowSaved );
<? if($_SESSION['userlevel'] > 5 ) { echo 'se_handelEditable();'; }; ?>
        if(triggerSave) {
          triggerSave = false;
          $('#saveSlide').trigger('click');
        }
        sendMessage('alert-success', 'Your new '+response.result.name+' slidshow is successfully saved');
      }
    });
  });
  
  //select slide from list and load content to editor
  $('.slideElement .rightSide .name').live('click', function(e){
/*
//2013.02.04
  impress().goto( parseInt($(this).next().text()) );
  return false;
//2013.02.04
*/
//console.log( '.slideElement .rightSide .name CLICK fired' );
removeUIevents();
    e.stopPropagation();
/*    $('#myModal').modal({backdrop:false});
    $('#myModal').modal('toggle');*/
    if( $(this).parent().parent().hasClass('selected') )
      return false;
    slideSaved = true;
    checkSlideState();
    $('.slideElement .rightSide').parent().removeClass('selected');
    var $dataHolder = $(this).closest('li');
    //check slide type
    templateSlide = ($dataHolder.attr('data-slide-type') == 'template') ? true : false;
    templateSlide == true ? $('.testSlideEditorContainer').show() : $('.testSlideEditorContainer').hide();
    //$('.testSlideEditorContainer').hide();
    $('.trafficDataHolder, .tools').html('');
    templateSlide ? $('#editorBottomBar .level').show(): $('#editorBottomBar .level').hide();
    
    $dataHolder.find('.dataHolder').toggleClass('selected');

    $('#editorArea').attr('data-template-type','normal');
    //remove any instances
    //if (CKEDITOR) {
    //  for(name in CKEDITOR.instances){
    //    CKEDITOR.instances[name].destroy();
    //  }
    //}
    //update content, attach events
    var html = $dataHolder.find('.leftSide').html(),
        desription = $dataHolder.find('.descriptionTag').html(),
        tag = $dataHolder.find('.rightSide .name').text();
    $editor.empty().html(html);
    $('#slideTag').val(tag);
    var textElements = $editor.find( 'div[id^="myInstance_"]' );
    //CKEDITOR.disableAutoInline = true;
    if (templateSlide) {
//console.log( '.slideElement .rightSide CLICK template slide' );
      //var textElements = $editor.find( 'div[id^="myInstance_"]' );
      //update trafficmanager data
      var data = getTrafficData( $dataHolder ),
          html = tmpl("tmpl-trafficdata", data);
      $(html).appendTo( $('.trafficDataHolder') );
      //find slide element template type
      var templateType = $dataHolder.attr('data-template-type');
      $('#editorArea').attr('data-template-type',templateType);
      //$('#tmpType').val(templateType);
////console.log( templateType );
      $('#editorBottomBar .level').show();
      $('.testSlideEditorContainer').show();
///////////////
      $editor.find('option').live('click', function(){
        $(this).siblings().attr('selected','');
        $(this).attr('selected','selected');
      });
///////////////
      $('[name="levelCount"] option').filter(function() {
        //may want to use $.trim in here
        return $(this).val() == $dataHolder.attr('data-slidelevel'); 
      }).attr('selected', true);
    }

    editor = new nicEditor();
////console.log( editor.panelInstance );
////console.log( nicEditors.editors  );
    for (var i=0; i<textElements.length; i++) {
      var timeStamp = new Date().getTime();
      var iId = 'myInstance_'+timeStamp;
      $(textElements[i]).attr('id', iId);
      editor.addInstance(iId).floatingPanel();
      //var editor = CKEDITOR.inline(iId);
    }
//console.log( '.slideElement .rightSide .name CLICK fired: add editors for text instances' );
//2013.01.19
    addUIevents();
    if(templateSlide) {
      addToolsEvent(templateType)
      $('#editorArea .sortableForm').droppable(formDrop);
      $('#editorArea .sortableForm').sortable(formSortable);

      //2013.01.19
      //removeUIevents();
////console.log('slide right click templateDropOption fired');
      //$editor.find('.holder .textDiv').droppable(templateDropOption);//templateDropOption);
      
    }
////console.log( 'slide betoltese a szerkesztobe' );
////console.log( slideIDs );
    //nicEditors.allTextAreas();
    
    //if(!nicEditors.findEditor('descArea'))
      //editor.removeInstance('descArea');
//    editor.addInstance('descArea').floatingPanel();
    //editor.panelInstance('descArea');
    $('.nicEdit-main').html(desription);
  });

  //open trafficData
  var openedClass = 'icon-eye-close opener';
  var closedClass = 'icon-eye-open opener'; 
  $('.slideElement .rightSide i').live('click', function(){
    $(this).toggleClass(openedClass+' '+closedClass);
    $(this).parent().parent().parent().next('.trafficData').toggleClass('opened');
    recalculateScrollHeight( $('#slidesList') );
    slideScroller[0].startMoving(1, 1000);
  });
  
  //editorArea topBar
  
  //clear area
  $('#clearArea').bind('click', function(){
    $(this).removeClass('active');
    if (!templateSlide) {
      se_clearData('normal');
    }
  });
  
  //add new slide
  $('#newSlide').bind('click',function(){
    $slides.find('.dataHolder.selected').removeClass('selected');
    $('.description').find('.nicEdit-main').html('');
    se_clearData('template');
    $editor.attr('data-template-type','normal');
    $('#saveSlide').trigger('click');
    return false;
  });

  //change background
  $('#removeBG').bind('click', function(){
    $('#editorArea').toggleClass('pure');
  });
  
  //editor Area 
  //click events
  $editor.bind('click', function(e){
    editObject(e);
  });
  
  //delete elements on editorArea
  $('.deleteBox').live('click', function(){
    if ( $(this).parent().hasClass('textClass') ){
      var textElement = $(this).parent().find( 'div[id^="myInstance_"]' );
      var iId = textElement.attr('id');
      //CKEDITOR.instances[iId].destroy();
      editor.removeInstance(iId);
    }
    $(this).parent().remove();
    /*
    if( checkFormState('saveslidesform') ){
      slideSaved = false;
      checkSlideState();
      return false;
    }
    */
    slideSaved = false;
    checkSlideState();
  });

  //editorArea bottomBar
/*
  $('[name="levelCount"]').live('change', function(){
    $(this).attr('data-selected', $('[name="levelCount"] option:selected').val());
  });
*/
  //tagname change
  $('#slideTag').live('keypress',function(){
    if($slides.find('.dataHolder.selected').length == 1){
      slideSaved = false;
      checkSlideState();
    }
  });
  //slide: update selected or save as brand new
  $('#saveSlide').on('click', function(){
    if( !checkFormState('saveslidesform') ){
      slideSaved = false;
      triggerSave = true;
      $('#saveSlideshow').trigger('click');
      return false;
    }
    $('#tempDiv').length == 0 ? $('body').append('<div id="tempDiv" />') : $('#tempDiv').html('');
    $('#tempDiv').css({'width':editingAreaWidth,'height':editingAreaHeight});
    removeUIevents();
    removeToolsEvent();
    //append all editor element to prepering area
    $('#tempDiv').append( $editor.html() );
    //remove unwanted elements, classes
    //update contenteditable id
    var textElements = $('#tempDiv').find( 'div[id^="myInstance_"]' );
    for (var i=0; i<textElements.length; i++) {
      var iId = textElements[i].getAttribute('id');
      textElements[i].setAttribute('data-temp-id', iId);
      textElements[i].setAttribute('id', 'myInstance_'+iId);
    }
    //remove empty or unicode non-printing span's
    var travers = document.getElementById('tempDiv');
    $.when(recurseDomChildren(travers, true))
      .done(function(apple){
        var html = $('#tempDiv').html(),
            tag = $('#slideTag').val(),
            data = {};
        data['result'] = [];
        //check if update
        if ( $slides.find('.dataHolder.selected').length == 1 ){
          $slides.find('.dataHolder.selected').children('.leftSide').html('').html(html);
          $slides.find('.dataHolder.selected').children('.rightSide').find('.name').text(tag);
          var thisSlide = $slides.find('.dataHolder.selected').parent();
          data['result'].push({
            'id': thisSlide.attr('id'),
            'type': thisSlide.attr('data-slide-type'),
            'templateType': thisSlide.attr('data-template-type'),
            'badge':thisSlide.find('span.badge.nr').text(),
            'tag': tag,
            'html': html,
            'description':$('.description').find('.nicEdit-main').html()
            });
//console.log( 'save slide update' );
////console.log( html );

          var desription = $('.description').find('.nicEdit-main').html();
          thisSlide.find('.descriptionTag').html(desription);
          if (templateSlide) {
            var dataRows = $('.trafficDataHolder').find('div.control-group');
            var obj = [];
            $.each(dataRows, function(i,e){
              var prevId = getslidesIDforTrafficData( $(e).find('#slidePrev').val() );
              var nextId = getslidesIDforTrafficData( $(e).find('#slideNext').val() );
              obj.push({
                'prev': $(e).find('#slidePrev').val(),
                'prevID': (prevId == 'undefined' ? 0 : prevId),
                'score': $(e).find('#slideScore').val(),
                'next': $(e).find('#slideNext').val(),
                'nextID': (nextId == 'undefined' ? 0 : nextId)
              });
            });
            data['result'][0].templateOption = obj;
            data['result'][0].slideLevel = $('[name="levelCount"] option:selected').val();
            var tdata = {}; tdata['result'] = obj;
            var result = tmpl('tmpl-slidetrafficdata', tdata);
            thisSlide.find('.trafficData').addClass('opened').find('.whiteLine').remove();
            thisSlide.find('.trafficData').append( $(result) );
          }
          //update slide data
          var arraied = $slides.nestedSortable('toArray', {startDepthCount: 0, slideShowId: $('#saveslidesform [name="id"]').val()});
          var response = se_handelSlide(data, $('#saveslidesform').serializeArray(), 'update', arraied);
          if(!response.error){
            sendMessage('alert-success', response.message); //response.message
            slideSaved = true;
            checkSlideState();
          }else{
            sendMessage('alert-error', response.error); //response.message
          }
          //end update

        } else {
          //save as brand new slide
          //temporary slide id
          //it must be corrected after save
          var timeStamp = new Date().getTime();
          var tag = $('#slideTag').val();
          var badge = $('#slidesList li.slideElement').length + 1;
          var stype = (templateSlide == true) ? 'template':'normal';
          data['result'].push({'id':timeStamp, 'html':html,'tag':tag,'badge':badge,'type': stype,'description':$('.description').find('.nicEdit-main').html()});
          var desription = $('.description').find('.nicEdit-main').html();
          
          if (templateSlide) {
            //data['result'] = [];
            var templateType = $('#testSlides li .dataHolder.selected').parent().attr('data-template-type');
            //data['result'].templateType = templateType;
            var obj = setTrafficData();
            data['result'][0].templateOption=obj;
            data['result'][0].templateType=templateType;
            data['result'][0].templateOption=obj;
            data['result'][0].slideLevel = $('[name="levelCount"]').val();
            addToolsEvent(templateType);
          }
          //data['result'].push(basedata);
          //save slide
          var arraied = $slides.nestedSortable('toArray', {startDepthCount: 0, slideShowId: $('#saveslidesform [name="id"]').val()});
          var response = se_handelSlide(data, $('#saveslidesform').serializeArray(), 'new', arraied);
          if(!response.error) {
            var resHtml = tmpl("tmpl-miniSlides", data, true);
            $slides.append(resHtml);
            $slides.find('.badge:contains('+badge+')').closest('.dataHolder').addClass('selected');
            if (templateSlide) {
              $tests.find('.dataHolder.selected').removeClass('selected');
              $slides2.find('.dataHolder.selected').removeClass('selected');
              $slides.find('.dataHolder.selected').parent().find('.trafficData').addClass('opened');
            }
            slideScroller[0].startMoving(1, 1000);
            $slides.find('.dataHolder.selected').parent().attr('id', response.id);
            $slides2.find('.dataHolder.selected').removeClass('selected');
            updateSlidesIDarray(response.id, badge, 'new');
            sendMessage('alert-success', response.message); //response.message
            slideSaved = true;
            checkSlideState();
          } else {
            sendMessage('alert-error', response.error); //response.message
          }
        } //end new
    });
    se_handeslideAfterEvents();
  });

  //trafficManagerContainer
  $('#trafficmanagerButton').bind('click', function(){
    $(this).toggleClass('active');
    $('.trafficmanagerContainer').toggleClass('opened');
  });

  /*
  $('#addTrafficRow').live('click', function(e){
    e.stopPropagation();
    var data = [];
    data['result'] = [];
    data['result'].push({'prev': '', 'score':'', 'next':''}); //, 'new':1
////console.log( data['result'] );
    var html = tmpl("tmpl-trafficdata", data);
    $(html).appendTo( $('.trafficDataHolder') );
  });
  
  $('.removeTrafficRow').live('click', function(event){
    event.stopPropagation();
    $(this).parent().remove();
  });
*/
  
  //right side mediabox
  //folder select
  $('#themeList a').bind('click', function(e){
    //e.stopPropagation();
    if($(this).parent().hasClass('selected')) return false;
    var _this = $('#themeList').prev('.selectMenu');
    _this.find('span.name').text($(this).text());
    var folderName = $(this).text();
    folderName = folderName.length > 12 ? folderName.substring(0, 11)+'...' : folderName;
    _this.find('span.name').text(folderName).attr('title',$(this).text());
    $('#saveslidesform').find('input[name="diskArea_id"]').val($(this).attr('data-diskarea-id'));
    $('#themeList li').removeClass('selected').end();
    $(this).parent().addClass('selected');
    se_loadMediaGroups( );//sample data
    $('.selectMenu').parent().removeClass('open');
    return false;
  });

  //filter mediabox
  $('#sortingIconBar button').bind('click', function(){
    if($( '#editorsMediaBox li').length == 0 ) return false;
    var classes = $(this).attr('data-class');
    $( '#editorsMediaBox li').hide();
    var classArray = classes.split(/\s+/g);
    for (i in classArray)
      classes == '' ? $( '#editorsMediaBox li').show() : $( '#editorsMediaBox li.'+classArray[i]).show();
  });
  
  // open/close uploadcontainer
  var content = $('#content');
  content.inner = $('#content .inner');
  content.on('transitionEnd webkitTransitionEnd transitionend oTransitionEnd msTransitionEnd', function(e){
    if(content.hasClass('open')){
      content.css('max-height', '160px');
    }
  });

  $('#showHelp').on('click', function(e){
    content.toggleClass('open closed');
    content.contentHeight = content.outerHeight();
    if (content.hasClass('closed')){
        content.removeClass('transitions').css('max-height', content.contentHeight);
        setTimeout(function(){
            content.addClass('transitions').css({'max-height': 0,'opacity': 0});
        }, 10); 
    }else if (content.hasClass('open')){
        content.contentHeight += content.inner.outerHeight();
        content.css({'max-height': content.contentHeight,'opacity': 1});
    }
  });
  
  
});
</script>
<!-- load/save slideshows and display slides -->

<!-- template: Load SlideShows  -->
<script type="text/x-tmpl" id="tmpl-lsslist">
{% for (var i=0, lsslist; lsslist=o.result[i]; i++) { %}
  <li>
    <a href="#" data-id="{%=lsslist.id%}" rel="tooltip" title="{%=lsslist.description%}">
      <span class="sname">{%=lsslist.name%}</span>
      <span class="pull-right badge">{%=lsslist.count%}</span>
    </a>
  </li>
{% } %}
</script>

<!-- template: Load SlideShows  -->
<script type="text/x-tmpl" id="tmpl-lsslist1">
<div class="listHolder">
<table class="table table-striped table-condensed">
  <caption>Files to delete</caption>
  <thead><tr><th>Name</th><th>Description</th><th>Slides</th></tr></thead>
  <tbody>
    {% for (var i=0, lsslist; lsslist=o.result[i]; i++) { %}
    <tr>
      <td>{%=lsslist.name%}</td>
      <td>{%=lsslist.description%}</td>
      <td><span class="pull-right badge">{%=lsslist.count%}</span></td>
    </tr>
    {% } %}
  </tbody>
</table>
</div>
</script>

<!-- template: save slideshow -->
<script type="text/x-tmpl" id="tmpl-saveslideshow">
{% for (var i=0, file; file=o.result[i]; i++) { %}
<form class="form-inline">
  <div class="control-group">
    <div class="controls">
      <input type="text" id="slideshowName" class="input-xlarge" placeholder="Name...." value="{%=file.input%}"/>
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <select name="folder">
      {% for (var j=0, folder; folder=file.folders[j]; j++) { %}
        <option value="{%=folder.diskArea_id%}">{%=folder.name%}</option>
      {% } %}
      </select>
    </div>
  </div>
  <div class="control-group">
    <div class="controls">
      <textarea id="slideshowDescription" placeholder="Description..." style="width:97%">{%=file.desc%}</textarea>
    </div>
  </div>
</form>
{% } %}
</script>

<!-- template mini slides and traffic datas-->
<script type="text/x-tmpl" id="tmpl-miniSlides">
{% for (var i=0, slides; slide=o.result[i]; i++) { %}
  <li id="{%=slide.id%}" class="slideElement {% if (slide.templateType) { %} templateSlide {% } %}" data-slide-type="{%=slide.type%}" {% if (slide.templateType) { %} data-template-type="{%=slide.templateType%}" data-slideLevel="{%=slide.slideLevel%}" {% } %}>
    <div class="dataHolder">
      <!--<div class="nyil"><div class="arrow-right"></div></div>-->
      <div class="pointer-right"></div>
      <div class="leftSide">
        {%=slide.html%}
      </div>
      <div class="descriptionTag">{%=slide.description%}</div>
      <div class="rightSide {% if (slide.error) { %}redB{% } %}" {% if (slide.error) { %}title="{%=slide.error%}"{% } %}>
        <span class="name">{%=slide.tag%}</span>
        <span class="badge nr">{%=slide.badge%}</span>
        {% if (slide.templateOption) { %}
        <div class="trafficDetail"><i class="icon-eye-close opener"></i></div>
        {% } %}
      </div>
    </div>
    {% if (slide.templateOption) { %}
    <div class="trafficData span2">
      <div class="pointer-bottom2"></div>
      <div class="pointer-bottom1"></div>
      {% for (var j=0, slides2; tmpO=slide.templateOption[j]; j++) { %}
      <div class="whiteLine">
        <span class="badge prev" data-slide-id="{%=tmpO.prevID%}">{%=tmpO.prev%}</span>
        <i class="icon-chevron-left"></i>
        <span class="badge score">{%=tmpO.score%}</span>
        <i class="icon-chevron-left"></i>
        <span class="badge next" data-slide-id="{%=tmpO.nextID%}">{%=tmpO.next%}</span>
      </div>
      {% } %}
    </div>
    {% } %}
  </li>
{% } %}
</script>
<!-- template mini slides and traffic datas-->
<script type="text/x-tmpl" id="tmpl-miniSlides2">
{% for (var i=0, slides; slide=o.result[i]; i++) { %}
  <li id="{%=slide.id%}" class="slideElement {% if (slide.templateType) { %} templateSlide {% } %}" data-slide-type="{%=slide.type%}" {% if (slide.templateType) { %} data-template-type="{%=slide.templateType%}" data-slideLevel="{%=slide.slideLevel%}" {% } %}>
    <div class="dataHolder">
      <!--<div class="nyil"><div class="arrow-right"></div></div>-->
      <div class="pointer-left"></div>
      <div class="leftSide">
        {%=slide.html%}
      </div>
      <div class="descriptionTag">{%=slide.description%}</div>
      <div class="rightSide">
        <span class="name">{%=slide.tag%}</span>
        <span class="badge nr">{%=slide.badge%}</span>
        {% if (slide.templateOption) { %}
        <div class="trafficDetail"><i class="icon-eye-close opener"></i></div>
        {% } %}
      </div>
    </div>
    {% if (slide.templateOption) { %}
    <div class="trafficData span2">
      <div class="pointer-bottom2"></div>
      <div class="pointer-bottom1"></div>
      {% for (var j=0, slides2; tmpO=slide.templateOption[j]; j++) { %}
      <div class="whiteLine">
        <span class="badge prev" data-slide-id="{%=tmpO.prevID%}">{%=tmpO.prev%}</span>
        <i class="icon-chevron-left"></i>
        <span class="badge score">{%=tmpO.score%}</span>
        <i class="icon-chevron-left"></i>
        <span class="badge next" data-slide-id="{%=tmpO.nextID%}">{%=tmpO.next%}</span>
      </div>
      {% } %}
    </div>
    {% } %}
  </li>
{% } %}
</script>
<!-- template: display traffic data on bottombar -->
<script type="text/x-tmpl" id="tmpl-trafficdata">
{% for (var i=0, file; file=o.result[i]; i++) { %}
<div class="control-group">
  <input type="text" placeholder="goto" class="square" id="slidePrev" value="{%=file.prev%}"><i class="icon-chevron-left"></i>
  <input type="text" placeholder="score" class="square" id="slideScore"  value="{%=file.score%}"><i class="icon-chevron-left"></i>
  <input type="text" placeholder="goto" class="square" id="slideNext" value="{%=file.next%}">
</div>
{% } %}
</script>
<!--
{% if ((i>0) || (file.new == 1)) { %}{% } %}
    <button class="btn btn-dark removeTrafficRow"><i class="icon-minus"></i></button>
-->
<!-- template: display update traffic data on slider -->
<script type="text/x-tmpl" id="tmpl-slidetrafficdata">
{% for (var i=0, file; file=o.result[i]; i++) { %}
<div class="whiteLine">
  <span data-slide-id="{%=file.previd%}" class="badge prev">{%=file.prev%}</span><i class="icon-chevron-left"></i>
  <span class="badge score">{%=file.score%}</span><i class="icon-chevron-left"></i>
  <span data-slide-id="{%=file.nextid%}" class="badge next">{%=file.next%}</span>
</div>
{% } %}
</script>


<!-- load mediaboxes w/o diskarea groupped -->
<!--<li > <a href="javascript:void(0)" class="level2" data-id="all"><i>All files</i></a></li>-->
<!-- template: list madiaboxes only -->
<script type="text/x-tmpl" id="tmpl-mblist">
  
{% for (var i=0, mblist; mblist=o.result[i]; i++) { %}
  <li> <a href="javascript:void(0)" class="level2" data-id="{%=mblist.id%}">
  <span class="name">{%=mblist.name%}</span><span class="pull-right badge">{%=mblist.badge%}</span>
  </a></li>
{% } %}
</script>

<!-- template: diskareas and mediaboxes -->
<script type="text/x-tmpl" id="tmpl-diskareas">
{% for (var i=1, disk; disk=o.result[i]; i++) { %}
  <li><a href="javascript:void(0)" class="level1" data-diskarea-id="{%=disk.id%}"><b>{%=disk.name%}</b></a>
    <ul class="sub" id="{%=disk.id%}">
      <li > <a href="javascript:void(0)" class="level2" data-id="all"><i>All files</i></a></li>
    {% for (var j=0, box; box=disk.boxes[j]; j++) { %}
      <li> <a href="javascript:void(0)" class="level2" data-id="{%=box.id%}">{%=box.name%}</a></li>
    {% } %}
    </ul>
  </li>
{% } %}
</script>

<!-- template: diskareas and mediaboxes II. (default only) -->
<script type="text/x-tmpl" id="tmpl-diskareas2">
{% for (var i=1, disk; disk=o.result[i]; i++) { %}
  <li class="subs"> <a href="#" data-id="all"><b>All files</b></a></li>
  {% for (var j=0, box; box=disk.boxes[j]; j++) { %}
  <li class="subs"> <a href="#" data-id="{%=box.id%}">{%=box.name%}</a></li>
  {% } %}
{% } %}
</script>

<!-- load mediabox mediaelements -->

<!-- template: display myMediaFiles -->
<script type="text/x-tmpl" id="tmpl-mediaElement">
{% for (var i=0, file; file=o.result[i]; i++) { %}
  <li class="mediaElement span2 lightgreyB {%=file.type%} isotope-item"  
     isotope-data-category="{%=file.type%}"
     isotope-data-name="{%=file.name%}"
     isotop-data-uploaded="{%=file.uploaded_ts%}"
     data-mediaurl="{% if (file.mediatype == 'remote') { %}{%=file.mediaurl%}{% } else { %}<?=connectionType().$imageURL;?>{%=file.mediaurl%}{% } %}"
     data-mediatype="{%=file.mediatype%}"
     {% if(file.type == 'video') { %}
     data-poster="{% if (file.mediatype == 'remote') { %}{%=file.thumbnail_url%}{% } else { %}<?=connectionType().$imageURL;?>{%=file.thumbnail_url%}{% } %}"
     {% } %}
     data-id="{%=file.id%}">
     <div class="colorBar {%=file.type%}"></div>
       <div class="thumbnail">
         {% if (file.type == 'image' || file.type == 'audio' || file.type == 'video') { %}<a href="#" rel="popover" >{% } %}
           <img src="{% if (file.mediatype == 'remote') { %}{%=file.thumbnail_url%}{% } else { %}<?=connectionType().$imageURL;?>{%=file.thumbnail_url%}{% } %}" alt=""  />
         {% if (file.type == 'image' || file.type == 'audio' || file.type == 'video') { %}</a>{% } %}
         <div class="caption">
           <p><span class="name">{%=file.name.substring(0,22)%}</span></p>
           <p><span class="uploaded">{%=file.uploaded%}</span></p>
           <p><span class="type">{%=file.type%}</span>
           {% if (file.duration) { %}<span class="additional">{%=file.duration%}</span>{% } %}
           {% if (file.filesize) { %}<span class="additional">{%=file.filesize%}</span>{% } %}
           </p>
         </div>
       </div>
  </li>
{% } %}
</script>

<!-- templates for dropped mediaelements -->

<!-- template: display image on ea -->
<script type="text/x-tmpl" id="tmpl-eaImage">
{% for (var i=0, file; file=o.result[i]; i++) { %}
<div class="ResizableClass image isSelected slideItem" style="left: {%=file.left%}; top: {%=file.top%}; width: {%=file.width%}; height: {%=file.height%}; position: absolute;" data-item-type="Image"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><img src="{%=file.mediaurl%}" style="width: 100%; height: 100%;"></div>
{% } %}
</script>

<!-- template: display local video on ea -->
<script type="text/x-tmpl" id="tmpl-eaLVideo">
{% for (var i=0, file; file=o.result[i]; i++) { %}
<div class="ResizableClass video isSelected slideItem" style="left: {%=file.left%}; top: {%=file.top%}; width: {%=file.width%}; height: {%=file.height%}; position: absolute;" data-item-type="lVideo"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><video style="width:100%;height:100%;" controls="controls" poster="{%=file.poster%}"><source src="{%=file.mediaurl%}{%=file.mediaurl%}.mp4" type="video/mp4"><source src="{%=file.mediaurl%}.ogg" type="video/ogg"></video></div>
{% } %}
</script>

<!-- template: display remote video on ea -->
<script type="text/x-tmpl" id="tmpl-eaRVideo">
{% for (var i=0, file; file=o.result[i]; i++) { %}
<div class="ResizableClass video isSelected slideItem" style="left: {%=file.left%}; top: {%=file.top%}; width: {%=file.width%}; height: {%=file.height%}; position: absolute;" data-item-type="rVideo"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><iframe width="100%" height="100%" src="{%=file.mediaurl%}" frameborder="0" allowfullscreen=""></iframe></div>
{% } %}
</script>

<!-- template: display local audio on ea -->
<script type="text/x-tmpl" id="tmpl-eaLAudio">
{% for (var i=0, file; file=o.result[i]; i++) { %}
<div class="nonResizableClass audio isSelected slideItem" style="left: {%=file.left%}; top: {%=file.top%}; width: {%=file.width%}; height: {%=file.height%}; position: absolute;" data-item-type="Audio"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><audio controls="controls"><source src="{%=file.mediaurl%}.mp3" type="audio/mpeg"><source src="{%=file.mediaurl%}{%=file.mediaurl%}.wav" type="audio/wave"></audio></div>
{% } %}
</script>

<!-- template: display text on ea -->
<script type="text/x-tmpl" id="tmpl-eatext">
{% for (var i=0, file; file=o.result[i]; i++) { %}
<div class="textClass ResizableClass isSelected slideItem" style="left: {%=file.left%}; top: {%=file.top%}; width: {%=file.width%}; height:auto;position:absolute;" data-item-type="Text"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_{%=file.instanceID%}" class="textDiv" contenteditable="true" style="position: relative;">{% if (file.text) { %}{%=file.text%}{% } %}</div></div>
{% } %}
</script>
