var $trainingGroupList = $( "#trainingGroupList" ),  //draggable
    $trainers = $('#trainers'),
    $userGroupList = $('#usersGroupList'),
    $userBoxElement = $('.sortable'),//sortable and draggable
    $myusers = $('#myUsersList'), //draggable
    $userBoxScroller = $('#usersGroupList'),
    allUsers = [],
    allUsersId = [];

var openedClass = 'icon-eye-close opener';
var closedClass = 'icon-eye-open opener';      
var scrollContainerHeight = 425;
var scrollContainerInnerHeight = 350;

var selectedClass = 'selectedElement';
var xpos; var ypos;

//ISOTOPE
//var $isoObject = $('#myMediaList');
var $optionSet = $('#sortBy');
var ascOrder = true;
var currentFilter, sortValue;

var btnUpload=$('#upload');

var newuserValidate = {
    rules:{
        keresztnev:"required",
        vezeteknev:"required",
        email:{required:true,email: true},
        neme:"required"
    },
    messages:{
        keresztnev:"Enter your first and last name",
        vezeteknev:"Enter your first and last name",
        email:{
            required:"Enter your email address",
            email:"Enter valid email address"
        },
        neme:"Select Gender"
    },
    errorClass: "help-inline",
    errorElement: "span",
    highlight:function(element, errorClass, validClass){
        $(element).parents('.control-group').addClass('error');
    },
    unhighlight: function(element, errorClass, validClass){
        $(element).parents('.control-group').removeClass('error');
        $(element).parents('.control-group').addClass('success');
    },
    submitHandler: function(form) {
        var response = um_handelUsers( 'save', $('#newuser').serializeArray() );
        if (response.message.type == 'error'){
            sendMessage('alert-'+response.message.type, response.message.message);
            return false;
        } else if (response.message.type == 'success'){
            $('#confirmDiv').modal('hide');
            sendMessage('alert-'+response.message.type, response.message.message);
            $myusers.prepend(tmpl("tmpl-users", response.users));
            if(response.group.id){
                switch (response.group.type){
                    case 'new':
                        var resp = um_handelUserGroups('load');
                        $userGroupList.html(tmpl("tmpl-userGroupList", resp));
                        break;
                    case 'exist':
                        var badge = $userGroupList.find('[data-object-id="'+response.group.id+'"]').find('span.badge');
                        badge.text(parseInt(badge.text()) + 1);
                        break;
                }
            } else {
                var badge = $userGroupList.find('[data-object-name="notInList"]').find('span.badge');
                badge.text(parseInt(badge.text()) + 1);
                var badge = $userGroupList.find('[data-object-name="viewAll"]').find('span.badge');
                badge.text(parseInt(badge.text()) + 1);
            }
        }
        return false;
        //$(form).submit();
    }
};

var updateuserValidate = {
  rules:{
    email:{required:true,email: true},
    birthDate:{required: true,date: true}
  },
  messages:{
    email:{
      required:"Enter your email address",
      email:"Enter valid email address"
    },
  birthDate:""
  },
  errorClass: "help-inline",
  errorElement: "span",
  highlight:function(element, errorClass, validClass){
    $(element).parents('.control-group').addClass('error');
  },
  unhighlight: function(element, errorClass, validClass){
    $(element).parents('.control-group').removeClass('error');
    $(element).parents('.control-group').addClass('success');
  },
  submitHandler: function(form) {
//console.log( 'form validation before response' );
    var response = um_handelUsers( 'update', $(form).serializeArray() );
//console.log( 'form validation response' );
    sendMessage('alert-'+response.type, response.message);
 }
};

var dropOnGroup = {
  tolerance: 'pointer',
  accept: "#myUsersList li",
  //activeClass: "ui-state-highlight",
  hoverClass: "ui-state-highlight",
  greedy: true,
  drop: function(e, ui){
    var group = $(this),
        groupId = group.attr('data-object-name'),
        groupName = group.find('div.name').text(),
        groupHolder = group.parent().attr('id');
    var elements = $myusers.find('li.selected');

    var data = [];
    elements.each( function(i,e){
      data.push({'id':$(e).attr('data-id'),'groupid':group.attr('data-object-id'),'groupName':groupName});
      //$(e).find('span.department').text(groupName);
    });

    var response;
    switch (groupHolder){
      case 'usersGroupList': response = um_handelUsers('groupChange', data); break;
      case 'trainingGroupList':  
        var groupId = group.attr('data-object-id');
        response = um_handelTrainingGroups('add', data);
        elements = null;
        break;
      case 'trainers':
console.log( 'trainers' );
console.log( data );
        break;
    }
//
    if(response.type == 'error')
      sendMessage('alert-'+response.type, response.message);
    else {
      sendMessage('alert-'+response.type, response.message);
      $('#draggingContainer2').animate({
        width: ['toggle', 'swing'],
        height: ['toggle', 'swing'],
        opacity: 'toggle'
      }, 700, 'linear', function() {
        if (elements)
        elements.each( function(i,e){
          $(e).find('span.department').text(groupName);
          $(e).attr('data-category', convertDoname(groupName));
        });
        $('#draggingContainer2').remove();
        $.when( um_loadUsersGroup(), um_loadTrainingGroup()).done(function(a,b){
          um_addUI();
console.log( groupHolder + ' ' + groupId);
          //$('#'+groupHolder).find('.mbcholder[data-object-name="'+groupId+'"]').addClass('selected');
          $('.userElement.selected .action .icon-ok').trigger('click');
        });
      });
      
    }
  }
};

var dragUser = {
  appendTo:'body',
  handle: ".colorBar",
  revert:true,
  //greedy: true,
  opacity: 0.7,
  helper: function(event, ui){
    var selected = $('.selected:not(.hiddenClass, .hiddenClass2)', '#myUsersList');
//console.log( selected );
    if (selected.length === 0 || selected.length === 1) {
     selected = $(this);
    }
    selected.addClass('selected');
    if( $('#draggingContainer2').length == 0 )
      var container = $('<ul/>').appendTo('body').attr('id', 'draggingContainer2');
    else {
      container = $('#draggingContainer2');
      container.empty();
    }
    container.addClass('span8').append( selected.clone() ).css({'position':'', 'left':'', 'top':'','z-index':'10000'});
    $('li', container).css({'position':'', 'left':'', 'top':'','z-index':'10000'});
    return container; 
  },
  start: function(event,ui){
    $('#draggingContainer2').css({'position':'absolute'});
  },
  stop: function(event, ui){
    var pos = ui.position;
    pos = Math.abs(pos.left);
//console.log( pos );
    if ($('.mediaBox.selected').length == 1 && pos < 50 ){
      alert('trash');
      //ide jon departmentbol torles
      //ide jon a training groupbol torles
      var selectedHolder = $('.mediaBox.selected'),
          selectedHolderId = selectedHolder.attr('data-object-name'),
          groupHolderId = selectedHolder.parent().attr('id'),
          response, delIds, data = [],
          selectedElements = $('#draggingContainer2').find('li');
      $.each(selectedElements, function(){
        data.push($(this).attr('data-id'));
      });
      data.type = groupHolderId;
//console.log( data );
      response = um_handelUsers( 'remove', data, groupHolderId);
      if(response.type == 'success'){
        selectedHolder.find('.badge').text(parseInt(selectedHolder.find('.badge').text()) - selectedElements.length);
        switch (groupHolderId){
          case 'usersGroupList': 
            um_loadUsersGroup();
            $.each(selectedElements, function(){
              $(this).find('.department').text('');
            });
          /*$.when(um_loadUsersGroup()).done(function(a){$('.mbcholder.selected').removeClass('selected').end();});*/ break;
          case 'trainingGroupList': break;
        }
        $('.mbcholder.selected').removeClass('selected').end();
        $('#'+groupHolderId).find('.mbcholder[data-object-id="'+selectedHolderId+'"]').addClass('selected');
        $myusers.find('li.selected:not(.hiddenClass, .hiddenClass2)').toggleClass('selected hiddenClass');
        um_addUI();
      }
      sendMessage('alert-'+response.type, response.message);
    }
  }
};

var usergroupDelete = {
    //snap: "#mediboxList",
    //snapMode: "inner",
    distance: 15,
    axis: "x",
    revert: 'invalid',
    handel: '.name',
    stop: function(event, ui) {
        var element2delete = $(this);
        if($(this).hasClass('selected')) {
        } else {
            var pos = ui.position;
            pos = Math.abs(pos.left);
            if (pos > 30 ) {
                switch (element2delete.parent().attr('id')){
                    case 'usersGroupList':
                        var users = $myusers.find('li[data-category="'+element2delete.attr('data-object-name')+'"]');
                        var bodytext = '<p>Do you really want to delete Department <strong>'+element2delete.find('.name').text()+'</strong> ?</p>';
                        if(users.length > 0)
                            bodytext += '<p>All user will set to UNORGANIZED.</p>';
                        $("#confirmDiv").confirmModal({
                            heading: 'Alert',
                            body: bodytext,
                            type: 'question',
                            text:'Delete',
                            cancel:true,
                            callback: function () {
                                var data = {}; data['users'] = [];
                                data.groupid=(element2delete.attr('data-object-id'));
                                users.each(function(i,e){
                                    data['users'].push({'id':$(e).attr('data-id')});
                                });
                                var response = um_handelUserGroups('delete', data);
                                //console.log( 'mbdelete '+element2delete.attr('data-object-id') );
                                if(response.type == 'success') {
                                    //var response = um_handelUserGroups('load');
                                    //$userGroupList.html(tmpl("tmpl-userGroupList", response));
                                    var unorg = $userGroupList.find('li[data-object-name="notInList"]'),
                                        count = unorg.find('span.badge').text();
                                    //console.log('count: '+count + 'new: '+ parseInt(count)+users.length);
                                    unorg.find('span.badge').text(parseInt(count)+users.length);
                                    element2delete.remove();
                                    users.each(function(i,e){
                                        $(e).attr('data-category','').find('span.department').text('');
                                    });
                                }
                                sendMessage('alert-'+response.type, response.message);
                            }
                        });
                        break;
                    case 'trainingGroupList':
                        var data = [];
                        data.push({'id':element2delete.attr('data-object-id')});
                        var response = um_handelTrainingGroups('delete', data);
                        if(response.type == 'success')
                            element2delete.remove();
                        sendMessage('alert-'+response.type, response.message);
//console.log( response );
                        break;
                }
            };
        };
    }
};
(function($){

initUsers = function(){
  //working hoverscroll
  //getHoverScrollHeight(viewportHeight);
  
  //window resize
  $(window).smartresize(function(){
    viewportWidth=w.innerWidth||e.clientWidth||g.clientWidth;
    viewportHeight=w.innerHeight||e.clientHeight||g.clientHeight;
    if (viewportWidth < 980 ) {
    
    }
/*
    getHoverScrollHeight(viewportHeight);
    $('.fixed-listcontainer').css('height',scrollContainerInnerHeight+'px');
    $('.hoverscroll').css('height',scrollContainerHeight+'px');

    var $elem = $('#myMediaList:data(isotope)');
    if ( $mymedia != null && $elem.length )
      $mymedia.isotope('reLayout');  
*/
  });
/*  
  //working hoverscroll
  $.fn.hoverscroll.params = $.extend($.fn.hoverscroll.params, {
    vertical : true,
    width: 190,
    height: scrollContainerHeight,
    flheight:scrollContainerInnerHeight,
    arrows: true,
    fixedArrows: true
  });
  
  //$userBoxScroller.hoverscroll();
*/
  $userBoxScroller.slimScroll({position: 'left',height: '300px',allowPageScroll: false,width:'auto'});
  $trainingGroupList.slimScroll({position: 'right',height: '300px',allowPageScroll: false,width:'190px'});
  
  //$('.fixed-listcontainer').css('height','380px');
  
  um_loadUsers();
  um_loadTrainingGroup();
};

um_addUI = function(){
  $userGroupList.find('li.mediaBox')
    .droppable(dropOnGroup)
    .draggable(usergroupDelete);
  $trainingGroupList.find('li.mediaBox')
    .droppable(dropOnGroup)
    .draggable(usergroupDelete);
  $myusers.find('li.selected').draggable(dragUser);
  $trainers.find('li.mediaBox')
    .droppable(dropOnGroup);
};
um_removeUI = function(){
  var $elem = $myusers.find('li:data(draggable)');
  if ($elem.length > 0)
    $elem.draggable( 'destroy' );
};

um_findSelected = function(){
  var count = $myusers.find('li.selected').length;
  $('.selectedCount').each(function(i,e){
    $(e).text(count); 
  });
  count == 0 ? $('#sendFastMessage').addClass('disabled') : $('#sendFastMessage').removeClass('disabled');
  count == 0 ? $('.optionButtons').find('button').addClass('disabled') : $('.optionButtons').find('button').removeClass('disabled');
};

um_deselectAll = function(){
  um_removeUI();
  $('#myUsersList li').removeClass('selected').find('.icon-ok').addClass('icon-white');
  ($('#selectAll').hasClass('active') ? $('#selectAll').trigger('click'):'');
  um_findSelected();
};
um_invertSelection = function(){
  um_removeUI();
  $('#myUsersList li').toggleClass('selected').find('.icon-ok').toggleClass('icon-white');
  ($('#selectAll').hasClass('active') ? $('#selectAll').removeClass('active').find('span').text('Select all'):'');
  um_addUI();
  um_findSelected();
};

//handel user groups
um_handelUserGroups  = function(stype, data){
  var values = {};
  values[stype] = stype, values['form'] = $('#usersform').serializeArray(), values['id'] = data;
console.log( 'um_handelUserGroups values' );
console.log( values );
  return $.parseJSON($.ajax({
    url: "/crawl?/process/users/handelgroups/",
    data : values,
    type:'POST',
    dataType: 'json',
    async:false
  }).responseText);
};

//loadUsersGroup
um_loadUsersGroup = function(){
  var response = um_handelUserGroups('load');
  $userGroupList.html(tmpl("tmpl-userGroupList", response));
  $userGroupList.find('li.mediaBox').draggable(usergroupDelete);
};

//handel users
um_handelUsers  = function(stype, data, extra){
  var values = {};
console.log( typeof extra );
  (typeof extra == 'undefined' || extra == '') ? values[stype] = stype : values[stype] = extra;
  /*values[stype] = stype, */values['form'] = $('#usersform').serializeArray(), values['id'] = data;
console.log( 'um_handelUsers values' );
console.log( values );
  return $.parseJSON($.ajax({
    url: "/crawl?/process/users/handelusers/",
    data : values,
    type:'POST',
    dataType: 'json',
    async:false
  }).responseText);
};

//load users
um_loadUsers = function(){
  var values = {};
  values['load'] = 'load', values['form'] = $('#usersform').serializeArray();
  return $.ajax({
    url: "/crawl?/process/users/handelusers/",
    data : values,
    type:'POST',
    dataType: 'json'
  }).done(function(data){
    setmyUsersArray(data);
    $myusers.append( tmpl("tmpl-users", data) );
    var response = um_handelUserGroups('load');
    $userGroupList.html(tmpl("tmpl-userGroupList", response));
    $userGroupList.find('li.mediaBox').draggable(usergroupDelete);
  });
};

setmyUsersArray = function(data){
  allUsers = [];
  allUsers = data;
  allUsersId = [];
  for(var i=0; i< allUsers.result.length; i++){
    allUsersId.push( parseInt(allUsers.result[i].id) );
  }
};

//save individual user
addIndividualUser = function(){
//console.log(' addIndividualUser ' );
  var data = [];
  data['result'] = [];
  data['result'].push();
  var bodytext = tmpl("tmpl-individuser", data);
  $("#confirmDiv").confirmModal({
    heading: 'Add individual user',
    body: bodytext,
    text:'Save user',
    page: 'users',
    cancel:true,
    callback: function () {
      $("#newuser").validate(newuserValidate);
      $("#newuser").submit();
      $("#newuser").submit( {
        url:'',
        onSubmit: function(){
          return $(this).validate(newuserValidate);  
        },
        success:function(){
        
        }
      });
      return false;
    }
  });
};

uploadUsers = function(){
  $('#upload').trigger('click');
  return false;
};

//handel usersGroup select
usersGroupSelect = function(usersGroup, ugName){

  var ugDoname = usersGroup.attr('data-object-name');
  var ugId = usersGroup.attr('data-object-id');
  var bcHtml = (ugDoname == 'viewAll' ) ? '' : ((ugDoname == 'notInList' ) ? '<span class="divider">>&nbsp;</span><span>' +ugName+'</span>' : '<span class="divider">>&nbsp;</span><span class="editable" id="ugName_'+ugId+'" data-type="text" data-pk="1">' +ugName+'</span>' );
  $('#bc').html(bcHtml);
  $('#ugName_'+ugId).editable({
    title: 'Rename selected group',
    placement: 'bottom',
    send:'never'
  });
  switch (ugDoname){
    case 'viewAll': $myusers.find('li').removeClass('hiddenClass'); break;
    case 'notInList': 
      $myusers.find('li').removeClass('hiddenClass').filter(function(){ return $(this).attr('data-category') !== ''; }).addClass('hiddenClass'); break;
    default:
      $myusers.find('li').removeClass('hiddenClass').filter(function(){ return $(this).attr('data-category') !== ugDoname; }).addClass('hiddenClass'); break;
  }
  $('#ugName_'+ugId).on('update', function(e, editable) {
    var ugData = [];
    ugData.push({'newname':editable.value,'id':ugId, 'oldname':ugName});
    var response = um_handelUserGroups('rename', ugData);
    if(!response.newname){
      sendMessage('alert-error', response.error);
      $('#bc').find('.editable').text(ugName);
      return false;
    }
    usersGroup.attr('data-object-name',response.sortname);
    usersGroup.find('div.name').text(response.newname);
    //data-category, span.department change too
    $myusers.find('[data-category="'+ugDoname+'"]').attr('data-category',response.sortname).find('span.department').text(response.newname)
    sendMessage('alert-success', 'Succesfully renamed');
  });
};

//handel training groups
um_handelTrainingGroups  = function(stype, data){
  var values = {};
  values[stype] = stype, values['form'] = $('#usersform').serializeArray(), values['id'] = data;
console.log( 'um_handelTrainingGroups values' );
console.log( values );
  return $.parseJSON($.ajax({
    url: "/crawl?/process/users/handeltraininggroups/",
    data : values,
    type:'POST',
    dataType: 'json',
    async:false
  }).responseText);
};

//handel trainingGroup select
trainingGroupSelect = function(trainingGroup, trName){
  var trDoname = trainingGroup.attr('data-object-name');
  var trId = trainingGroup.attr('data-object-id');
  var members = trainingGroup.attr('data-member-list');
  var bcHtml = '<span class="divider">>&nbsp;</span><span class="editable" id="trName_'+trId+'" data-type="text" data-pk="1">' +trName+'</span>';
  $('#bc').html(bcHtml);
  $('#trName_'+trId).editable({
    title: 'Rename selected group',
    placement: 'bottom',
    send:'never'
  });
  var data = members.split(',');
  $myusers.find('li').removeClass('hiddenClass').filter(function(){ return data.indexOf($(this).attr('data-id')) == -1; }).addClass('hiddenClass');
  
  $('#trName_'+trId).on('update', function(e, editable) {
    var ugData = [];
    ugData.push({'newname':editable.value,'id':trId, 'oldname':trName});
    var response = um_handelTrainingGroups('rename', ugData);
    if(!response.newname){
      sendMessage('alert-error', response.error);
      $('#bc').find('.editable').text(trName);
      return false;
    }
    trainingGroup.attr('data-object-name',response.sortname);
    trainingGroup.find('div.name').text(response.newname);
    //data-category, span.department change too
    sendMessage('alert-success', 'Succesfully renamed');
  });
};

//load training Groups
um_loadTrainingGroup = function(){
  var response = um_handelTrainingGroups('load');
  if( response.type !== 'error' ){
  $trainingGroupList.html(tmpl("tmpl-trainingGroupList", response));
  $trainingGroupList.find('li.mediaBox').draggable(usergroupDelete);
  }
};

//load trainers
//handel training groups
um_handelTrainers  = function(stype, data){
  var values = {};
  values[stype] = stype, values['form'] = $('#usersform').serializeArray(), values['id'] = data;
console.log( 'um_handelTrainers values' );
console.log( values );
  return $.parseJSON($.ajax({
    url: "/crawl?/process/users/handeltrainers/",
    data : values,
    type:'POST',
    dataType: 'json',
    async:false
  }).responseText);
};

um_loadTrainers = function(){
  var response = um_handelTrainers('load');
  if( response.type !== 'error' ){
    $trainers.html(tmpl("tmpl-trainers", response));
    //$trainingGroupList.find('li.mediaBox').draggable(usergroupDelete);
  }
};
})(jQuery);