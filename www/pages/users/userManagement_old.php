<?php
if(!isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest'){
  require_once ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/404.php' );
  //return "papo2";
  exit();
}
require( $_SERVER['DOCUMENT_ROOT'].'/../include/authenticate.php' );
if (!$_SESSION['logged_in']){
  require_once ( $_SERVER['DOCUMENT_ROOT'].'/errordocuments/403forbidden.php' );
  //return "papo2";
  exit();
}
if (!$_SESSION['logged_in'])
  session_start();
  
require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/class/mobileDetect.php');
$detect = new Mobile_Detect;
if(!$_SESSION['isMobile']){
    $_SESSION['isMobile'] = $detect->isMobile();
}
if(!$_SESSION['isTablet']){
    $_SESSION['isTablet'] = $detect->isTablet();
}


$_SESSION['url'] = $_SERVER['REQUEST_URI'];
//printR( $_SESSION );
require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_include_base.php');
//include ($_SERVER['DOCUMENT_ROOT'] .'/../include/header/_header_auth.php');
$protocol = connectionType();
//clearstatcache();
//set_error_handler("customError");


//SureRemoveDir(IMGPATH.'/'.$_SESSION['office_nametag'].'/', false);
//SureRemoveDir(IMGPATH.'/'.$_SESSION['office_nametag'].'/thumbnail', false);

//printR($_GET);
//name convention

$span2Header = 'Company';
$span2Headerright = 'options';
$span2Headerright2 = 'fast message';
$newDepName = 'create department';
$newTrainingName = ' create group';
$newMBFormInputPlaceholder = ' sales ...';
$newTGFormInputPlaceholder = ' english interm...';
$viewAll = 'Company all';
$notInList = 'Unorganized';

$span8Header = 'users';
$span8Header2 = 'add';
$formdata['label']    = array('Előtag', 'Vezetéknév', 'Keresztnév','Neme', 'Email', 'Csoport','Pozíció', 'Született', 'Nyelv', 'Iskolák', 'Skills');
$formdata['pholder']  = array('DR.', 'Vezetéknév', 'Keresztnév','Neme', 'name@domain.tld', 'Csoport','Pozíció', '1955.01.01', 'magyar, angol', 'főiskola', 'skills');
$formdata['id']  = array('elotag', 'vezeteknev', 'keresztnev','gender', 'email', 'department','position', 'birthDate', 'language', 'schools', 'skills');

//require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/class/class.encrypt.php');

?>
<!---->

<!--<script type="text/javascript" src="/assets/ajaxupload.3.5.js"></script>-->

<form id="usersform" action="#" method="POST" class="hidden">
  <!--<input type="hidden" name="id" value="" />
  <input type="hidden" name="name" value="" />-->
  <input type="hidden" name="office_id" value="<?=$_SESSION['office_id']?>" />
  <input type="hidden" name="office_nametag" value="<?=$_SESSION['office_nametag']?>" />
  <input type="hidden" name="owner" value="<?=$_SESSION['u_id']?>" />
</form>
<!--<div id="upload" class="btn btn-grey" style="margin-left:-10000px;display:none;">Upload File</div>-->

<input id="upload" type="file" class="hidden" />
<form id="fileupload" action="" method="POST" enctype="multipart/form-data" style="margin-left:-10000px;display:none;">
  <span class="btn btn-success fileinput-button"><span>Add files</span><input type="file" name="files[]" multiple></span>
  <button type="submit" class="btn btn-primary start"><span>Start upload</span></button>
</form>

  <div class="row special">
    <!-- LEFT SIDE -->
    <div class="span2">
    
      <div class="affix-top" id="myusersGroupsContainer"><!-- myusersGroupsContainer -->
        <div class=" colHeader"><h3 class="orangeT "><?=$span2Header;?></h3></div>
        <div class="clearfix"></div>

        <!-- usergroups -->
        <div class="createNew well well-small">
          <div class="dropdown">
            <h3 class="functionHeader middlegreyT2">
              <span></span>
              <button class="dropdown-toggle btn btn-dark btn-r" data-toggle="dropdown" role="button"  data-href="#" id="newMB"><?=$newDepName;?></button>
              <ul class="dropdown-menu newusersGroup">
                <li class="newForm">
                  <form class="form-vertical" method="post" action="#">
                    <div class="input-append">
                      <input type="text" id="newMBName" class="input-newMB" placeholder="<?=$newMBFormInputPlaceholder;?>" maxlength="15">
                      <span class="add-on"><button type="button" class="btn btn-dark" id="createNew"><i class="icon-plus"></i></button></span>
                    </div>
                  </form>
                </li>
              </ul>
            </h3>
          </div>
        </div>
        <div class="clearfix"></div>
        <div class="sideBar span2">
          <ul class="mediaBoxList span2" id="usersGroupList"></ul>
        </div>
        <!-- /usergroups -->
        <div class="clearfix"></div>
        
        <div class="colHeader">
          <h3 class="orangeT"><?=$span2Headerright;?></h3>
        </div>
        <div class="clearfix"></div>
        <div class="Options well well-small">
          <span class="functionHeader middlegreyT2">
            <span id="" class="selectedCount pull-left">0</span><span class="pull-left">&nbsp; selected user</span>
            <div class="clear"></div>
            <div class="optionButtons">
            <button type="button" class="btn btn-dark btn-l disabled" data-option-value="active"><span class="success">Activate</span></button>
            <button type="button" class="btn btn-dark btn-l disabled"  data-option-value="inactive"><span class="danger">Deactivate</span></button>
            <button type="button" class="btn btn-dark btn-l disabled" data-option-value="delete"><span class="delete">Delete</span></button>
            </div>
          </span>
        </div>
        
      </div>
    </div>
    <!-- /LEFT SIDE -->
    
    <!-- MIDDLE SIDE -->
    <div class="span8">
      <div id="myUsers">
        <div class=" colHeader">
        <h3 class="orangeT ">
          <span class="pull-left"><?=$span8Header;?></span>
          <span class="pull-left" id="bc"></span>
          
          <span class="pull-right functionHeader">
            <span class="dropdown">
              <!--<button class="dropdown-toggle btn btn-dark btn-r" data-dropdown="#diskAreas" data-toggle="dropdown"  id="selectdiskArea" data-horizontal-offset="-20"  data-vertical-offset="0">Default<span class="caret"></span></button>-->
              <span class="dropdown-toggle btn-r" role="button" data-toggle="dropdown" id="addUserButton" data-href="#"><?=$span8Header2;?><span class="caret"></span></span>
              <ul class="dropdown-menu" id="addUser">
                <li class=""><a href="#" onclick="addIndividualUser()">One user</a><li>
                <li class=""><a href="#" onclick="uploadUsers()">Users from csv file</a><li>
                <li class="divider"></li>
                <li class=""><a href="<?=$protocol.DOMAINTAG;?>/users.csv">CSV file to use by upload</a></li>
              </ul>
            </span>
          </span>
        </h3>
        </div>
        <div class="clearfix"></div>
        
        <!-- ISOTOPE -->
          <div class="well well-small" id="sortingBar">
            <!-- filters -->
            <!-- sortBy -->
            <div class="pull-left">
              <h3 class="functionHeader middlegreyT2">
                <span class="pull-left" id="tsort">sort:
                  <div id="sortBy" class="btn-group" data-toggle="buttons-radio" data-option-key="attr">
                    <button type="button" class="btn btn-dark active" data-option-value="name" data-tsort="include"><span>name</span></button>
                    <button type="button" class="btn btn-dark " data-option-value="id" data-tsort="include"><span>registered</span></button>
                    <button type="button" class="btn btn-dark " data-option-value="skills" data-tsort="include"><span>skills</span></button>
                  </div>
                  <div id="filterByIA" class="btn-group" data-toggle="">
                    <button type="button" class="btn btn-danger active" data-option-value="inactive" data-tsort="exclude"><span>in</span></button>
                    <button type="button" class="btn btn-success active" data-option-value="active" data-tsort="exclude"><span>active</span></button>
                  </div>
                  <div id="sortOrder" class="btn-group" data-toggle="buttons-radio" data-option-key="order">
                    <button type="button" class="btn btn-dark active" data-option-value="asc" data-tsort="include"><span><i class="icon-arrow-up"></i></span></button>
                    <button type="button" class="btn btn-dark " data-option-value="desc" data-tsort="include"><span><i class="icon-arrow-down"></i></span></button>
                  </div>
                </span>
                <span class="pull-left" style="margin-left: 10px;"><span>word:</span>
                  <div id="wordfilter">
                    <div class="btn-group">
                      <input type="text" class="input-small" id="filterWord" />
                    </div>
                  </div>
                </span>
              </h3>
            </div>
            <? if(!$_SESSION['isTablet'] && !$_SESSION['isMobile']) { ?>
            <!-- select all -->
            <div class="pull-right">
              <h3 class="">
                <div class="btn-group dropdown">
                  <span type="button" class="btn btn-dark" id="selectAll"><span>Select all</span></span>
                  <span class="btn btn-dark dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></span>
                  <ul class="dropdown-menu" id="selectionMenu">
                  <li class=""><a href="#" onclick="um_invertSelection();">Invert selection</a><li>
                  <li class=""><a href="#" onclick="um_deselectAll();">Deselect all</a><li>
                  </ul>
                </div>
              </h3>
            </div>
            <!-- /select all -->
            <? } ?>
          </div>
          <!-- /ISOTOPE -->
          
          <!-- myUsersListContainer -->
          <div class="myUsersListContainer">
            <ul id="myUsersListHeader"  class="myUsersList"><li class="userElement span8 middlegreyB2"><div class="userdata"><span class="name span2">Name</span><span class="department span2">Group</span><span class="registered span1">Registered</span><span class="skills span2">Skills</span><span class="action span1">Select</span></div></li></ul>
            <div class="clear"></div>
            <ul id="myUsersList" class="myUsersList"></ul>
            <div class="clear"></div>
          </div>
          <!-- myUsersListContainer -->
          <div id="x1"></div>
      </div><!-- /myMediaFiles -->
    </div>
    <!-- /MIDDLE SIDE -->
    <!-- RIGHT SIDE -->
    <div class="span2">
      <div class="affix-top" id="myOptionsContainer"><!-- myUsersContainer -->
        <div class="colHeader">
          <h3 class="orangeT">training groups</h3>
        </div>
        <div class="clearfix"></div>
        <!-- tarininggroups -->
        <div class="createNew well well-small">
          
          <div class="dropdown">
            <h3 class="functionHeader middlegreyT2">
              <span></span>
              <button class="dropdown-toggle btn btn-dark btn-r" data-toggle="dropdown" role="button"  data-href="#" id="newMB"><?=$newTrainingName;?></button>
              <ul class="dropdown-menu newusersGroup">
                <li class="newForm">
                  <form class="form-vertical" method="post" action="#">
                    <div class="input-append">
                      <input type="text" id="newTGName" class="input-newMB" placeholder="<?=$newTGFormInputPlaceholder;?>" maxlength="15">
                      <span class="add-on"><button type="button" class="btn btn-dark" id="createNewTG"><i class="icon-plus"></i></button></span>
                    </div>
                  </form>
                </li>
              </ul>
            </h3>
          </div>
          
        </div>
        
        <div class="clearfix"></div>
        <div class="sideBar training span2">
          <ul class="mediaBoxList span2" id="trainingGroupList" ></ul>
        </div>
        <!-- /tarininggroups -->
        <div class="clearfix"></div>
        <div class="colHeader">
          <h3 class="orangeT"><?=$span2Headerright2;?></h3>
        </div>
        <div class="clearfix"></div>
        <div class="Options well well-small">
          <span class="functionHeader middlegreyT2">
            <!--<span id="" class="selectedCount pull-left">0</span><span class="pull-left">&nbsp; selected user</span>-->
            <div class="clear"></div>
            <textarea class="fastMessage"></textarea>
            <button type="button" class="btn btn-dark whiteT disabled" id="sendFastMessage"><span>send</span></button>
          </span>
        </div>
        
        <div class="clearfix"></div>

      </div>
    </div>
    <!-- /RIGHT SIDE -->
  </div>
<!--<script type="text/javascript" charset="utf-8" src="/lib/umFunction_new.js"></script>-->
<script type="text/javascript" charset="utf-8" src="/lib/umFunction.js"></script>
<script type="text/javascript">
//userManager.init();
/**/
$(function() {

  //init
  $('#loading').show();
    initUsers();
/*
    $('#'+which+' button').removeClass('active');
    $(this).addClass('active');
    var att = $('#sortBy').find('.btn.active').attr('data-option-value');
    var order = $('#sortOrder').find('.btn.active').attr('data-option-value');
    $('#myUsersList > li:not(.hiddenClass)').tsort({attr:'data-'+att,order:order});
  });
*/
/**/
  $('#filterByIA button').bind('click', function(e){
console.log( $(this).attr('class') );
  e.preventDefault();
    $(this).hasClass('active') ? $(this).removeClass('active') : $(this).addClass('active');
    var type = '.colorBar.'+$(this).attr('data-option-value');
    var elements = $myusers.find('li').find('div'+type).parent();
    elements.toggleClass('hiddenClass2');
  });
  
  //filter users by text in name field
  $('#filterWord').bind('keyup',function(){
    if($(this).val().length < 2) {
      $myusers.find('li').removeClass('hiddenClass');
      return false;
    }
    var searchText = $(this).val().toLowerCase();
    $myusers.find('li').filter(function(){ return $(this).attr('data-name').toLowerCase().indexOf(searchText) == -1; }).addClass('hiddenClass');
  });
  
  //select user by click
  $('.userElement .action .icon-ok').live('click', function(){
    $(this).toggleClass('icon-white');
    $(this).closest('li').toggleClass('selected');
    um_findSelected();
    um_removeUI();
    um_addUI();
  });
  
  //open user detailed data
  var openedClass = 'icon-eye-close opener';
  var closedClass = 'icon-eye-open opener'; 
  $('.userElement .action i').live('click', function(){
    $(this).toggleClass(openedClass+' '+closedClass);
    var $additional = $(this).closest('li').find('.additional');
    $additional.toggleClass('opened');
    if($additional.hasClass('opened')){
      $form = $additional.find('form');
      $form.find(':button[type="button"]').prop('disabled', true);
      $form.find(':input').bind('keyup',function() {
        var disable = false;
        if (($.trim(this.value) === ''))
          disable = true;
        else
          disable = false;
        if($form.find(':button[type="button"]').attr('disabled'))
          $form.find(':button[type="button"]').prop('disabled', disable);
      });
      $form.find(':button[type="button"]').bind('click',function(e){
//console.log( 'submit button clicked' );
        //e.preventDefault();
//console.log( $form.attr('id') );
        $('form[id="'+$form.attr('id')+'"]').validate(updateuserValidate);
        $('form[id="'+$form.attr('id')+'"]').submit();//updateuserValidate
      });
    }
  });
  
  // select/deselect all user
  $('#selectAll').bind('click', function(){
    var elements = $('#myUsersList li').filter(function(){ return ($(this).hasClass("hiddenClass") == false) });
    var textElement = $(this).children('span');
    $(this).toggleClass('active');
    (textElement.text() == 'Select all') ? elements.addClass('selected') : elements.removeClass('selected');
    (textElement.text() == 'Select all') ? elements.find('.icon-ok').removeClass('icon-white') : elements.find('.icon-ok').addClass('icon-white');
    (textElement.text() == 'Select all') ? textElement.text('Deselect all') : textElement.text('Select all');
    var count = $myusers.find('li.selected').length;
    $('.selectedCount').each(function(i,e){
      $(e).text(count); 
    });
    count == 0 ? $('#sendFastMessage').addClass('disabled') : $('#sendFastMessage').removeClass('disabled');
    count == 0 ? $('.optionButtons').find('button').addClass('disabled') : $('.optionButtons').find('button').removeClass('disabled');
    um_removeUI();
    um_addUI();
  });
  
  //options: acitivate/deactivate/delete users
  $('.optionButtons button').bind('click', function(i,e){
    if($(this).hasClass('disabled')) return false;
    var option = $(this).attr('data-option-value');
    var $selected = $myusers.find('li.selected');
    var data = [];
    $selected.each(function(i,e){
      data.push($(e).attr('data-id'));
    });
    var response = um_handelUsers(option, data);
    if(response.type == 'error')
      sendMessage('alert-'+response.type, response.message);
    else {
      sendMessage('alert-'+response.type, response.message);
      if(response.type2 !== 'delete') {
        $selected.each(function(i,e){
          $(e).find('.colorBar ').attr('class','colorBar '+option);
        });
      }
      if(response.type2 == 'delete') {
        $selected.each(function(i,e){
          $(e).remove();
        });
        um_loadUsersGroup();
      }
    }
  });
  
  $('#sendFastMessage').bind('click', function(i,e){
    if($(this).hasClass('disabled')) return false;
    var $selected = $myusers.find('li.selected');
    var data = []; //data['users'] = []; 
    $selected.each(function(i,e){
      data.push({'name':$(e).find('span.name').text(),'email':$(e).find(':input[name="email"]').val()});
    });
console.log( data );
    var response = sendEmail('fastmessage',$('textarea.fastMessage').val(), $('#usersform').serializeArray(), data);
    sendMessage('alert-'+response.message.type, response.message.message);
    if(response.message.type == 'success') $('textarea.fastMessage').val('');
  });
  
  $('#loading').hide();
  
  //upload csv file
  $('#fileupload').fileupload({
    url: '/crawl?/process/users/uploadusers/',
    acceptFileTypes: /(\.|\/)(csv)$/i,
    singleFileUploads: true,
    dataType: 'json',
    uploadTemplateId: null,
    downloadTemplateId: null,
    start: function(e, data){
      $('#loading').show();
    },
    done: function (e, data) {
      var response = data.result;
      sendMessage('alert-'+response.type,response.message);
      if(response.result && response.result.length > 0){
        var data = [];
        data['result'] = [];
        data['result'].push(response.users);
        $myusers.prepend( tmpl("tmpl-users", response) );
        sendMessage('alert-success',response.message);
        um_loadUsersGroup();
        $('#loading').hide();
      }
    }
  });
  
  //var jqXHR;
  $('#upload').bind('change', function (e) {
    if( /\.csv$/gi.test($(this).val()) == false )
      sendMessage('alert-warning', 'Only csv allowed');
    else {
      $('#fileupload').fileupload('send', {
        files: e.target.files || [{name: this.value}],
        fileInput: $(this),
        formData: {form:$('#usersform').serialize()}
      });
    }
    $(this).val('');
  });
  
  $('#createNewTG').bind('click',function(){
    var newName = $('#newTGName').val();
    if ( newName.length == 0 ) return false;
    var boxid = um_handelTrainingGroups('save', newName);
    var data = [];
    data['result'] = [];
    data['result'].push({
      'id':boxid.resultId, 
      'name':newName, 
      'doname': convertDoname(newName),
      'badge' : 0
    });
    var resHtml = tmpl("tmpl-newDepartment", data, true);
    $trainingGroupList.append(resHtml);
    $trainingGroupList.find('li.mediaBox').tsort({attr:'data-object-name'});
    um_removeUI();
    um_addUI();
    $('#newTGName').val('');
    //$userGroupList.find('li.mediaBox').draggable(usergroupDelete);
  });
/**/
/*
  $('#myUsersList').selectable({
    filter: 'LI',
    //distance: '1',
    selecting: function(event, ui){            
        $(ui.selecting).addClass('ui-selected').addClass('selected');
        $(ui.selecting).find('.icon-ok').removeClass('icon-white');

      um_addUI();
    },
    selected: function(event, ui){            
        $(ui.selected).addClass('ui-selected').addClass('selected');
        $(ui.selected).find('.icon-ok').removeClass('icon-white');
        um_addUI();
    },
    unselecting: function(event, ui){
        $(ui.unselecting).removeClass('ui-selected').removeClass('selected');
        $(ui.unselecting).find('.icon-ok').addClass('icon-white');
                um_removeUI();
    },
    unselected: function(event, ui){
        $(ui.unselected).removeClass('ui-selected').removeClass('selected');
        $(ui.unselected).find('.icon-ok').addClass('icon-white');
                um_removeUI();
    }
  });
*/
/**/
  //select usergroups
  $('.mbHeader > div').die('click');
  $('.mbHeader > div').live('click', function(){
    //if ( $myusers.length == 0 ) return false;
    var myusersGroup = $(this).parent().parent();
    if (myusersGroup.hasClass('selected')) return false;
    myusersGroup.siblings().removeClass('selected');
    myusersGroup.addClass('selected');
    var ugName = $(this).text();
    usersGroupSelect(myusersGroup, ugName);
  });
/**/
/*
  //var status=$('#response');
  //var result=$('#result');
  //$('#files').html();
  new AjaxUpload(btnUpload, {
    action: '/pages/users/csvUpload.php',//'/crawl?/process/users/uploadusers/',
    //Name of the file input box
    name : 'uploadfile',
    data : {'form':$('#usersform').serialize()},
    dataType: 'json',
    onSubmit: function(file, ext){
      if (!(ext && /^(csv)$/.test(ext))){ 
        sendMessage('alert-warning','Only csv files are allowed');
        return false;
      }
      $('#loading').show();
    },
    onComplete: function(file, response){
      var result = $.parseJSON(response);
      var data = [];
      data['result'] = [];
      data['result'].push(result.users);
      if(result.message)
        sendMessage('alert-warning',result.message[0].name + ' ' + result.message[0].message);
      if(result.users.length > 0){
        $myusers.append( tmpl("tmpl-users", data) );
        um_handelUserGroups('load');
      }
      $('#loading').hide();
    }
  });
*/
/**/
});

</script>
<style>
  .ui-selected,
  .ui-selecting {background: #CBCBCB;}
</style>
<!-- template: add individual User -->
<script type="text/x-tmpl" id="tmpl-individuser">

<form id="newuser" action="" methode="post" class="form-horizontal">
  <table border="0" width="100%" cellpadding="0" cellspacing="0">
  <tr>
  <!--<td rowspan="" style="width: 80px;vertical-align: top;"><button class="btn btn-dark btn-l" id="addUserPic">Upload picture</button><div class="thumbnail" style="width:50px;height:50px"><img src="" /></div></td>-->
  <td>
  <div class="control-group"><label class="control-label" for="<?=$formdata['id'][0];?>"><?=$formdata['label'][0];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][0];?>" placeholder="<?=$formdata['pholder'][0];?>" value="" class="input-small"></div></div>
  
  <div class="control-group"><label class="control-label" for="<?=$formdata['id'][1];?>"><?=$formdata['label'][1];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][1];?>" placeholder="<?=$formdata['pholder'][1];?>" value=""></div></div>
  
  <div class="control-group"><label class="control-label" for="<?=$formdata['id'][2];?>"><?=$formdata['label'][2];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][2];?>" placeholder="<?=$formdata['pholder'][2];?>" value=""></div></div>
  
  <div class="control-group"><label class="control-label" for="<?=$formdata['id'][3];?>"><?=$formdata['label'][3];?></label><div class="controls"><select name="<?=$formdata['id'][3];?>" class="input-small"><option value="male">Férfi</option><option value="female">Nő</option></select></div></div>
  
  <div class="control-group"><label class="control-label" for="<?=$formdata['id'][4];?>"><?=$formdata['label'][4];?></label><div class="controls"><input type="text" class="required" name="<?=$formdata['id'][4];?>" placeholder="<?=$formdata['pholder'][4];?>" value=""></div></div>
  
  <div class="divider"><span class="badge">1/3</span></div>
      
  <div class="control-group"><label class="control-label" for="<?=$formdata['id'][5];?>"><?=$formdata['label'][5];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][5];?>" placeholder="<?=$formdata['pholder'][5];?>" value="" class="typeahead" data-provide="typeahead">
  <ul class="typeahead dropdown-menu" style="top: 69px; left: 19px; display: none;"></ul>
  </div></div>
  
  <div class="control-group"><label class="control-label" for="<?=$formdata['id'][6];?>"><?=$formdata['label'][6];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][6];?>" placeholder="<?=$formdata['pholder'][6];?>" value=""></div></div>
  
  <div class="divider"><span class="badge">2/3</span></div>
  
  <div class="control-group"><label class="control-label" for="<?=$formdata['id'][7];?>"><?=$formdata['label'][7];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][7];?>" placeholder="<?=$formdata['pholder'][7];?>" value=""></div></div>
  
  <div class="control-group"><label class="control-label" for="<?=$formdata['id'][8];?>"><?=$formdata['label'][8];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][8];?>" placeholder="<?=$formdata['pholder'][8];?>" value=""></div></div>
  
  <div class="control-group"><label class="control-label" for="<?=$formdata['id'][9];?>"><?=$formdata['label'][9];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][9];?>" placeholder="<?=$formdata['pholder'][9];?>" value=""></div></div>

  <div class="control-group"><label class="control-label" for="<?=$formdata['id'][10];?>"><?=$formdata['label'][10];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][10];?>" placeholder="<?=$formdata['pholder'][10];?>" value=""></div></div>
  <div class="divider"><span class="badge">3/3</span></div>

  </td></tr>
</table>
</form>
{% for (var i=0, file; file=o.result[i]; i++) { %}{% } %}
</script>

<!-- template: display myUsers -->
<script type="text/x-tmpl" id="tmpl-users">
{% for (var i=0, file; file=o.result[i]; i++) { %}
  <li class="userElement span2 lightgreyB"
     data-category="{%=file.doname%}"
     data-name="{%=file.fullname%}"
     data-registered="{%=file.registered%}"
     id="{%=file.id%}">
     <div class="colorBar {%=file.active%}"></div>
      <div class="thumbnail span2" style="width:50px;height:50px"><a href="#" rel="popover"><img src="{%=file
      .img%}" alt=""></a></div>
     <div class="userdata">
      <span class="name span2">{%=file.fullname%}</span>
      <span class="department span2">{%=file.department%}</span>
     </div>
  </li>
{% } %}
</script>
<!--
<span class="registered span1">{%=file.registered%}</span>
      <span class="skills span2">
        <div class="progress progress-success"><div class="bar" style="width: 40%"></div></div>
        <span class="pull-left skillpoints">1352 point</span> <span class="pull-left delegatedTrainings">7/10 trainings</span><span class="pull-left inprogress">1  in progress</span>
      </span>
      </div>
      <span class="action span1">
        <span class="btn-dark"><span class="icon-white icon-ok"></span></span>
        <i class="icon-eye-open opener"></i>
      </span>
      <div class="additional middlegreyB">
        <div class="pointer-bottom1"></div>
        <div class="pointer-bottom2"></div>
        <div class="additionalData"></div>
        <div class="userDataRow">
          <form id="user_{%=file.id%}" action="#" methode="post" class="form-horizontal">
            <input type="hidden" name="id" value="{%=file.id%}"/>
          <table border="0" width="100%" cellpadding="0" cellspacing="0">
  <tr>
    <td rowspan="" style="width: 60px;vertical-align: middle;">
      <div class="thumbnail" style="width:50px;height:50px"><a href="#" rel="popover"><img src="" alt=""></a></div>
    </td>
    <td>
      <div class="control-group"><label class="control-label" for="<?=$formdata['id'][4];?>"><?=$formdata['label'][4];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][4];?>" placeholder="<?=$formdata['pholder'][4];?>" value="{%=file.email%}" class="input-medium"></div></div>
      <div class="control-group"><label class="control-label" for="<?=$formdata['id'][7];?>"><?=$formdata['label'][7];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][7];?>" placeholder="<?=$formdata['pholder'][7];?>" value="{%=file.birth%}" class="dateHU input-small"></div></div>
      <div class="control-group"><label class="control-label" for="<?=$formdata['id'][3];?>"><?=$formdata['label'][3];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][3];?>" placeholder="<?=$formdata['pholder'][3];?>" value="{%=file.gender%}" class="input-small"></div></div>
      <div class="control-group"><label class="control-label" for="<?=$formdata['id'][8];?>"><?=$formdata['label'][8];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][8];?>" placeholder="<?=$formdata['pholder'][8];?>" value="{%=file.languages%}" class="input-small"></div></div>
    </td>

    <td style="vertical-align: top;">
      <div class="control-group"><label class="control-label" for="<?=$formdata['id'][6];?>"><?=$formdata['label'][6];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][6];?>" placeholder="<?=$formdata['pholder'][6];?>" value="{%=file.position%}" class="input-small"></div></div>
      <div class="control-group"><label class="control-label" for="<?=$formdata['id'][9];?>"><?=$formdata['label'][9];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][9];?>" placeholder="<?=$formdata['pholder'][9];?>" value="{%=file.schools%}" class="input-small"></div></div>
      <div class="control-group"><label class="control-label" for="<?=$formdata['id'][10];?>"><?=$formdata['label'][10];?></label><div class="controls"><input type="text" name="<?=$formdata['id'][10];?>" placeholder="<?=$formdata['pholder'][10];?>" value="{%=file.skills%}" class="input-small"></div></div>

    </td>
    <td style="vertical-align: bottom;"><button type="button" class="btn btn-dark btn-r whiteT">modify</button></td>
  </tr>

</table>
</form>
        </div>
      </div>
-->
<!-- template:usergroups -->
<script type="text/x-tmpl" id="tmpl-userGroupList">
<li class="viewAll mbcholder span2 selected"  data-object-id="" data-object-name="viewAll"><div class="mbHeader"><div class="name"><?=$viewAll;?></div><span class="pull-right badge">{%=o.result[0].badge%}</span><div class="pointer-right"></div></div></li>
{% if (o.result[1]) { %}
<li class="viewAll mbcholder span2" data-object-id="" data-object-name="notInList"><div class="mbHeader"><div class="name"><?=$notInList;?></div><span class="pull-right badge">{%=o.result[1].badge%}</span><div class="pointer-right"></div></div></li>
{% } %}
{% for (var i=2, boxes; boxes=o.result[i]; i++) { %}
<li class="mediaBox mbcholder span2"
  data-object-id="{%=boxes.id%}"
  data-object-name="{%=boxes.doname%}">
  <div class="mbHeader">
    <div class="name">{%=boxes.name%}</div>
    <span class="pull-right badge">{%=boxes.badge%}</span>
    <div class="pointer-right"></div>
    <div class="pointer-bottom1"></div>
    <div class="pointer-bottom2"></div>
  </div>
</li>
{% } %}
</script>

<!-- template:usergroups -->
<script type="text/x-tmpl" id="tmpl-trainingGroupList">
{% for (var i=0, boxes; boxes=o.result[i]; i++) { %}
<li class="mediaBox mbcholder span2"
  data-object-id="{%=boxes.id%}"
  data-object-name="{%=boxes.doname%}"
  data-member-list="{%=boxes.members%}">
  <div class="mbHeader">
    <div class="name">{%=boxes.name%}</div>
    <span class="pull-right badge">{%=boxes.badge%}</span>
    <div class="pointer-left"></div>
    <div class="pointer-bottom1"></div>
    <div class="pointer-bottom2"></div>
  </div>
</li>
{% } %}
</script>

<!-- template: display newly created mediaBox -->
<script type="text/x-tmpl" id="tmpl-newDepartment">
{% for (var i=0, boxes; boxes=o.result[i]; i++) { %}
<li class="mediaBox mbcholder span2"
  data-object-id="{%=boxes.id%}"
  data-object-name="{%=boxes.doname%}">
  <div class="mbHeader">
    <div class="name">{%=boxes.name%}</div>
    <span class="pull-right badge">{%=boxes.badge%}</span>
    <div class="pointer-right"></div>
    <div class="pointer-bottom1"></div>
    <div class="pointer-bottom2"></div>
  </div>
</li>
{% } %}
</script>

<!-- template: display newly created diskArea -->
<script type="text/x-tmpl" id="tmpl-newDA">
{% for (var i=0, file; file=o.result[i]; i++) { %}
  <li class="area"><a data-href="{%=file.doname%}" data-sortname="{%=file.doname%}" data-id="{%=file.id%}">{%=file.name%}</a></li>
{% } %}
</script>