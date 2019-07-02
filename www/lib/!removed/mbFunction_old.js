var $mediabox = $( ".mediaBox" ),  //draggable
    $mediaBoxList = $('#mediaBoxList'),
    $mediaBoxElement = $('.sortable'),//sortable and draggable
    $mymedia = $('#myMediaList'), //draggable
    //$trash = $( "#trash" ), //droppable
    //$trashContainer = $( "#trashContainer" ),
    $medaiBoxScroller = $('#mediaBoxList'),
    allMediaFiles = [],
    allMediaFileId = [];

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
  
////legujjabb proba 2012.11.11 10.30
/**/
var myMediaDragOption = {
  appendTo:'body',
  distance: 10,
  handle: ".caption",
  greedy: true,
  revert:true,
  helper: function(event, ui){
    $('.isotope').css('overflow','visible');
    var selected = $('.'+selectedClass, '#myMediaList');
    if (selected.length === 0 || selected.length === 1) {
     selected = $(this);
    }
    selected.addClass(selectedClass);
    //if( !$('#draggingContainer') )
    var container = $('<ul/>').appendTo('body').attr('id', 'draggingContainer');
    //else 
      //container = $('#draggingContainer');
    var cssRule = {'position':'', 'left':'', 'top':'','z-index':'10000'};
      container.append('<li style="height: 202px;"><h1>'+selected.length+' selected</h1></li>').css(cssRule);
      //container.addClass('span6').append( selected.clone() ).css(cssRule);
    $('li', container).css(cssRule);
    return container; 
  },
  start: function(event,ui){
    $('#draggingContainer').css({'position':'absolute'});
    //$trashContainer.css({'height':'100px'});
//$trashContainer.trigger('mouseenter');
  },
  stop: function(event,ui){
    var selected = $('.'+selectedClass, '#myMediaList');
    if (selected.length === 0) {selected = $(this);}
    selected.css('position','absolute');
    var pos = ui.position;
    pos = Math.abs(pos.left);
//console.log( pos );
    if (pos > 100 && pos < 125 ){
//console.log('trash'); 
      deleteTrash(selected);
    } else {
      var $elem = $('#myMediaList:data(isotope)');
      if ($elem.length)
        $mymedia.isotope( 'reLayout' );
    //$trashContainer.css({'height':'40px'});
    }
  },
  drag:function(event, ui){
    var pos = ui.position;
    pos = Math.abs(pos.left);
    var elements = ui.helper.children();
      /**/
//console.log( pos );
    if (pos > 100 && pos < 125 ){
//console.log('trash'); 
      //$.each(elements, function(){ $(this).addClass('orangeB').removeClass('lightgreyB'); });
    } else {
      $.each(elements, function(){ $(this).removeClass('orangeB').addClass('lightgreyB'); });
    }

//console.log(elements.attr('class'));
  }
};
    
var mediaBoxDropOption = {
  tolerance: 'pointer',
  accept: "#myMediaList li",
  //activeClass: "ui-state-highlight",
  hoverClass: "ui-state-highlight",
  greedy: true,
  drop: function(e, ui){
////console.log( $('#mediaBoxList').find('i.icon-eye-close.opener').length );
    //if( $('#mediaBoxList').find('i.icon-eye-close.opener').length == 0 ) return false;
    //var elements = ui.helper.children();
    //$(this).find('.sub').append(elements);

    var selected = $('.'+selectedClass, '#myMediaList');
    if (selected.length === 0) {selected = $(this);}
    selected.css('position','absolute');
    $('.ui-draggable',$(this)).attr('data-object-parent-id',$(this).attr('id'));//.css('z-index','');
    $('.ui-draggable',$(this)).attr('data-object-type','mediaBoxElement');
    //$('.ui-draggable',$(this)).attr('data-parent-object-type','mediaBox');
    $('.'+selectedClass,'.sub, #myMediaList').removeClass(selectedClass);
    //$('li', $(this))
    //elements.removeClass('ui-draggable').removeClass('ui-draggable-dragging');
    //$('.sub li')
    //elements.removeClass('isotope-item').removeClass('isotope-hidden').css('opacity','');
    var $elem = $('#myMediaList:data(isotope)');
    if ($elem.length)
      $mymedia.isotope( 'reLayout' );
    //elements.draggable(mediaBoxElementDelete);
    var data = [];
    data['parent'] = [], data['result'] = [];
    data.id = $(this).attr('data-object-id');
    $(selected).each(function(i,e){
      data['result'].push({'id':$(this).attr('data-id')});
    });
    var result = handelMediaGroupFiles(data, $('#fileupload').serializeArray(), 'save');
    //hozzaadni a subhoz
    $(this).find('.sub').append(selected.clone());
//console.log( data );
  }
};

var mediaBoxDelete = {
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
        var bodytext = 'Do you really want to delete this Mediabox?';
        $("#confirmDiv").confirmModal({
          heading: 'Alert',
          body: bodytext,
          type:'question',
          cancel:true,
          text:'Delete',
          callback: function () {
            var result = handelMediaGroup(element2delete.attr('data-object-id'), $('#fileupload').serializeArray(), 'delete');
//console.log( 'mbdelete '+element2delete.attr('data-object-id') );
            (result.result == true) ? element2delete.remove() : sendMessage('alert-error', 'Error on delete');
          }
        });
      }
    }
  }
};

var mediaBoxElementDelete = {
  //snap: "#mediboxList", 
  //snapMode: "inner",  
  distance: 15,
  axis: "x",
  revert: 'invalid',
  stop: function(event, ui) {
    if($(this).hasClass('selected')) {
    } else {
      var pos = ui.position;
      pos = Math.abs(pos.left);
      if (pos > 30 ) {
        var data = [];
        data['parent'] = [], data['result'] = [];
        data.id = $(this).parent().attr('id');
        data['result'].push({'id':$(this).attr('data-id')});
////console.log( data );
        var result = handelMediaGroupFiles(data, $('#fileupload').serializeArray(), 'delete');
////console.log( result );
        if(result.result)
          $(this).remove();
      }
    }
  }
};

/*
var sortableOption = {
  //appendTo: 'body',
  connectWith:'.sortable',
  placeholder: "ui-state-highlight2",
  //axis: "y",
  forceHelperSize: true,
  forcePlaceholderSize: true,
  cursor: "move", 
  items: "li:not(.ui-state-disabled)",
  tolerance: "pointer",
  revert: false,
  receive:function(e,ui){
    $('#trash .mediaBox > ul.sub').css('display','');
  },
  out: function(e, ui){
    //alert( 'remove' );
    //$('.ui-sortable-helper', this).remove();
  }
};
*/
/*
var trashOption = {
  tolerance: 'touch',
  accept: "#myMediaList li",
  activeClass: "ui-state-highlight",
  hoverClass: "ui-state-highlight",
  //greedy: true,
  drop: function(e, ui){
    $('#myMediaList li').css('position','absolute');
    var $removable = $mymedia.find('.'+ selectedClass );
    if ($removable.length === 0) {$removable = $(this);}
    $mymedia.isotope( 'remove', $removable );
    var deletedElements = ui.helper.children(); 
    $trash.prepend(deletedElements);
    
    $('.'+selectedClass,'.sub, #trash').removeClass(selectedClass);
    //$('#allElementContainer').empty().append( $('#myMediaList li').clone() );
    //alert($('#myMediaList li').length+' ' +$('#allElementContainer li').length);
  }
};
*/
/*
(function($, undefined) {
	$.extend($.infinitescroll.prototype,{
		_callback_masonry: function infscr_callback_masonry (newElements) {
			$(this).masonry('appended',$(newElements));
		}
	});
})(jQuery);
*/

//popover setup
var popoverContent = function(data){
  var mediaUrl = data.attr('data-mediaurl')
      mediaType = data.attr('data-mediatype'),
      type = data.attr('isotope-data-category');
  var html = '', res = [];
  res['result'] = [];
  switch (type){
    case 'image': html = '<img src="'+mediaUrl+'" />'; break;
    case 'video':
      if (mediaType == 'remote') {
        var video = testUrlForMedia(mediaUrl);
        html = '<iframe width="292px" height="150px" src="http://www.youtube.com/embed/'+video.id+'?fs=1&feature=oembed" frameborder="0" allowfullscreen></iframe>';
      } else if (mediaType == 'local') {
        var str = mediaUrl.substring(0, mediaUrl.lastIndexOf("."));//split('.mp3');
        res['result'].push({ 'name': str, 'poster': data.find('img').attr('src') });
        html = tmpl("tmpl-video", res);
      }
      break;
    case 'audio': 
      var str = mediaUrl.substring(0, mediaUrl.lastIndexOf("."));//split('.mp3');
      res['result'].push({ 'name': str });
      html = tmpl("tmpl-audio", res);
      break;
  }
  return html;
};

var popoveroptions = {
  placement: function (context, source) {
    var position = $(source).offset();
    if (position.left < 300) {
      return "right";
    } else if (position.left > 900 ) {
      return "left";
    }
    if (position.top < 300){
      return "bottom";
    }
    return "top";
  }, 
  trigger: "click",
  html : true,
  title: function () { return $(this).closest('li.mediaElement').find('span.name').text(); },
  content: function () { return popoverContent($(this).closest('li.mediaElement')); },
  selector : document.body
};
var sortTriggered = false;
var totalFiles, uploadLimit, maxFiles = 200;

(function (MyMedia, $, undefined) {
var optionElements = {
    buttonGroup: document.getElementsByClassName('optionButtons') || '',
    selectedCount: document.getElementById('selectedCount') || '',
    $optionButton: $('.optionButtons').find('button') || '',
    init: function () {
        this.$optionButton.bind('click', function () {
            if ($(this).hasClass('disabled')) return false;
            var action = $(this).attr('data-option-value');
            var ids = []
            $mymedia.find('li.selectedElement:not(.isotope-hidden)').each(function () {
                ids.push($(this).attr('data-id'))
            })

            var selected = $mymedia.find('li.selectedElement:not(.isotope-hidden, .hiddenClass)');
            //$('li.selectedElement:not(.isotope-hidden)', '#myMediaList');
            optionElements.setAction(action, selected);

            //$myusers.find('li.rootClass.selected').removeClass('selected').find('.icon-ok').removeClass('icon-white');
            MyMedia.countSelected();
        })
    },
    setAction: function(action, elements){
        switch (action){
            case 'delete':
                deleteTrash(elements);
                break;
            case 'removefrom':
                var data = [];
                data['parent'] = [], data['result'] = [];
                data.id = $('.mediaBox.selected').attr('data-object-id');
                elements.each(function(){
                    data['result'].push({'id':$(this).attr('data-id')});
                })
//console.log(data)
//return false;
                var result = handelMediaGroupFiles(data, $('#fileupload').serializeArray(), 'delete');
                if(result.result){
                    //add hidden
                    elements.each(function(){
                        $(this).addClass('isotope-hidden');
                        $mymedia.isotope('reLayout');
                        $mediaBoxList.find('selected').find('.sub').find('li[data-id="'+$(this).attr('data-id')+'"]')
                    })
                }

                break;
        }
    }
}
    MyMedia.countSelected = function() {
        var count = $mymedia.find('li.selectedElement:not(.isotope-hidden, .hiddenClass)').length;
        $(optionElements.selectedCount).text(count);
        count == 0 ? optionElements.$optionButton.addClass('disabled') : optionElements.$optionButton.removeClass('disabled');
        if($mediaBoxList.find('.viewAll.selected').length){
            $(optionElements.buttonGroup).find('button[data-option-value="removefrom"]').addClass('disabled');
        } else {
            $(optionElements.buttonGroup).find('button[data-option-value="delete"]').addClass('disabled');
        }
    }

    MyMedia.init = function(){
        optionElements.init();
        this.countSelected();
    }

}(window.MyMedia = window.MyMedia || {}, jQuery));

(function($){
/*
myMediaElement = function(type, mediatype, name, uploaded_ts, uploaded, thumbnail_url, madiaurl, duration, size){
  this.type = type;
  this.mediatype = mediatype;
  this.name = name;
  this.uploaded_ts = uploaded_ts;
  this.uploaded = uploaded;
  this.thumbnail_url = thumbnail_url;
  this.mediaurl = mediaurl;
  if(duration !== '')
    this.duration = duration;
  if(size !== '')
    this.size = size;
};
*/
mm_initPageLoad = function() {
    destroyIso();
    $mediabox.empty();
    $mediaBoxList.find('.mediaBox').remove();
    
    $mymedia.empty();
    //$trash = $( "#trash" ), //droppable
    //$trashContainer = $( "#trashContainer" ),
    allMediaFiles = [],
    allMediaFileId = [];
    
  $.when( mm_loadMediaGroups(), mm_loadMediaFiles() )
  .done(function( resp1, resp2 ){
    //resp2[1] == 'success' ? setmyMediaArray(resp2[0]) : sendMessage('alert-error', 'mymedia hiba');
    //resp1[1] == 'success' ? setmyMediaArray(resp1[0]) : sendMessage('alert-error', 'mymedia hiba');
////console.log('loading phase');
////console.log( resp1[0] );
    setTimeout(function() {

      initMyMedia();

      //mymedia elemek a kukaba torlessel
      //$trash.droppable(trashOption);
      
      initIso();

      //popover
      $('[rel="popover"]').clickover(popoveroptions);

    }, 100);
  });
};

initMyMedia = function(){
//working hoverscroll
      $('.fixed-listcontainer').css('height',scrollContainerInnerHeight+'px');

      //mymedia elemek: tobbszoros kijelolese
      $('#myMediaList li .caption').die('click');
      $('#myMediaList li .caption').live('click', function(){
        $(this).parents('li.span2').toggleClass(selectedClass);
        MyMedia.countSelected();
      });

      //mymedia elemek mediaboxon belulre dobasa 
      $('#myMediaList li').draggable(myMediaDragOption);

      //mediaboxon beluli lista, ami fogadja mymedia elemeket
      $('.mediaBox').droppable(mediaBoxDropOption);
      $('.sub li').draggable(mediaBoxElementDelete);
      $('.mediaBoxList .mediaBox').draggable(mediaBoxDelete);
      //$(".mediaBoxList").sortable({connectWith:'#trash'});
      totalFiles = $('#myMediaList li').length;
      uploadLimit = (maxFiles - totalFiles) > 0 ? maxFiles - totalFiles : 0;
      $('#fileupload').fileupload('option', 'maxNumberOfFiles', uploadLimit);
      //mymedia elemek a kukaba torlessel
      //$('#allElementContainer').empty().append( $('#myMediaList li').clone() );
//$('#trash').droppable(trashOption);
  //load diskarea names
  mm_diskarea();
};

//ISOTOPE
initIso = function(){
  //$mymedia.imagesLoaded( function(){
  $mymedia.isotope({
    itemSelector: '.span2:not(.hiddenClass)',
    layoutMode: 'masonry',
    /*masonry : {
      columnWidth: 170,
      gutterWidth: 30
    },*/
    animationEngine: 'jquery',
    animationOptions: {
      duration: 250,
      easing: 'linear',
      queue: false
    },
    getSortData : {
      /*name : function( $elem ) {
        var name = $elem.attr('isotope-data-name').toLowerCase().replace(' ','_');
        return name;//$elem.attr('isotope-data-name').toLowerCase();
      },*/
      number : function( $elem ) {
        return parseInt($elem.attr('data-id'));
      }
    },
    sortBy : 'original-order',
    sortAscending : ascOrder
  });

  //filter
  currentFilter = '*';
  $('#filters button').bind('click',function(){
    currentFilter = $(this).attr('isotope-data-filter');
    currentFilter = (currentFilter == 'all') ? '*' : currentFilter;
    switch (currentFilter) {
      case '*':
      case '.selectedElement':
        $('#filters button').removeClass('active');
        $(this).addClass('active');
        break;
      default:
        var fruits = new Array();
        $(this).toggleClass('active');
        $('#filters button[isotope-data-filter="all"], #filters button[isotope-data-filter=".selectedElement"]').removeClass('active');
        if( $('#filters button.active').length == 0 )
          $('#filters').find('button[isotope-data-filter="all"]').trigger('click');

        $('#filters button.active').each(function(){
          var filterVal = $(this).attr('isotope-data-filter');
          filterVal = (filterVal == 'all') ? '*' : filterVal;
          fruits.push(filterVal);
        });
        currentFilter = my_implode_js(', ',fruits);
        break;
    }
    sortValue = $optionSet.find('.active').attr('isotope-data-option-value');
    $mymedia.isotope({ 
      filter: currentFilter,
      sortBy : sortValue,
      sortAscending : ascOrder
    });
      MyMedia.countSelected();
    return false;
    //$mymedia.isotope( 'reLayout' );
    //$(this).trigger('click');
  });
      
  //sortBy
  $('#sortBy button').bind('click',function(){
    var $this = $(this);
    // don't proceed if already selected
    if ( $this.hasClass('active') )
      return false;
    $optionSet.find('.active').removeClass('active');
    $this.addClass('active');
    sortValue = $this.attr('isotope-data-option-value');
////console.log( 'sortby' );
////console.log( currentFilter );
////console.log( sortValue );
////console.log( ascOrder );
    //alert( ascOrder );
    $mymedia.isotope({
      filter: currentFilter,
      sortBy : sortValue,
      sortAscending : ascOrder
    });
    return false;
    //$mymedia.isotope( 'reLayout' );
    //$(this).trigger('click');
  });
        
  //sorting order: asc desc
  
  $('#sortOrder button').bind('click',function(e){
    e.preventDefault();
    $('#sortOrder button').removeClass('active');
    $(this).addClass('active');
    ascOrder = ($(this).attr('isotope-data-option-value') == 'true') ? true : false;
    var fruits = new Array();
    $('#filters button.active').each(function(){
      var filterVal = $(this).attr('isotope-data-filter');
      filterVal = (filterVal == 'all') ? '*' : filterVal;
      fruits.push(filterVal);
      //fruits.push($(this).attr('isotope-data-filter'));
    });
    currentFilter = my_implode_js(', ',fruits);
    sortValue = $optionSet.find('.active').attr('isotope-data-option-value');
////console.log( 'sortOrder' );
////console.log( currentFilter );
////console.log( sortValue );
////console.log( ascOrder );
    $mymedia.isotope({
      //filter: currentFilter,
      //sortBy : sortValue,
      sortAscending : ascOrder 
    });
    $mymedia.on(function(){$(this).isotope( 'reLayout' ) });
    //$mymedia.isotope( 'reLayout' );

    return false;
  });

  
  //});
  //$mymedia.css('height','');//
  //$mymedia.isotope('reLayout');
  $('#loading').hide();
};

destroyIso = function() {
  var $elem = $('#myMediaList:data(isotope)');
  if ($elem.length)
    $mymedia.isotope( 'destroy' );
  $('#filters button').unbind('click');
  $('#sortBy button').unbind('click');
  $('#sortOrder button').unbind('click');
};  

//load diskarea names
mm_diskarea = function(){
  var values = {};
  values['type'] = 'load', values['form'] = $('#fileupload').serializeArray();
  return $.ajax({
    url: "/crawl?/process/mymedia/loadfolder/",
    data : values,
    type:'POST',
    dataType: 'json'
  }).done(function(data){
    $('.selectediskArea li.area').remove().end();
    var html = tmpl("tmpl-newDA", data);
    var divider = $('.selectediskArea li.divider');//.find('.divider');
    $(html).insertBefore( divider );
    $('.selectediskArea [data-id="'+$('#fileupload').find('input[name="diskArea_id"]').val()+'"]').parent().addClass('selected');
  });
};

//load media groups
mm_loadMediaGroups = function(){
  return $.ajax({
    url: "/crawl?/process/mymedia/loadmediagroups/",
    data : {diskArea : $('#fileupload').find('input[name="diskArea_id"]').val()},
    type:'POST',
    dataType: 'json'
  }).done(function(data){
    $mediaBoxList.append(tmpl("tmpl-mediaBoxList", data));
  });
};

//load mediafiles
mm_loadMediaFiles = function(){
  return $.ajax({
    url: "/crawl?/process/mymedia/loadmediafiles/",
    data : {diskArea : $('#fileupload').find('input[name="diskArea_id"]').val()},
    type:'POST',
    dataType: 'json'
  }).done(function(data){
    setmyMediaArray(data);
//console.log(data);
    $mymedia.append( tmpl("tmpl-mediaElement", data) );
  });
  
};

setmyMediaArray = function(data){
  allMediaFiles = [];
  allMediaFiles = data;
  allMediaFileId = [];
  for(var i=0; i< allMediaFiles.result.length; i++){
    allMediaFileId.push( parseInt(allMediaFiles.result[i].id) );
////console.log( allMediaFiles.result[i].id );
  }
};
collectDownload = function(hostUrl){
  var selected = $mymedia
    .find('li.selectedElement:not(.isotope-hidden)')
    .filter(function(){ return $(this).attr('data-mediatype') !== 'remote'});
  var data = [];
  $.each(selected, function(i,e){
    data.push({
      'name':$(e).attr('isotope-data-name'),
      'type':$(e).attr('isotope-data-category')
    });
  });
  if(data.length == 0){
    sendMessage('alert-error','No files selected');
    return false;
  }
  var body = tmpl('tmpl-download', data) ;
////console.log( body );
//return false;
  $("#confirmDiv").confirmModal({
    heading: 'Selected local files to download',
    body: body,
    text:'Download',
    callback: function () {
      var result = downloadMediaFiles(data, $('#fileupload').serializeArray());
      if(result.download)
        window.location = hostUrl+result.download
    }
  });
};

downloadMediaFiles = function(data, data2){
  var values = {};
  values['files'] = data, values['form'] = data2;
////console.log( 'download files' );
////console.log( values );
  return $.parseJSON($.ajax({
    url: "/crawl?/process/download/",
    data : values,
    type:'POST',
    async: false,
    dataType: 'json' 
  }).responseText);
};

saveMediaFiles = function(data, data2, stype){
  var values = {};
  values[stype] = data['result'], values['form'] = data2;
  var results = [];
  results['result'] = [];
    $('#loading').show();
  $.ajax({
    url: "/crawl?/process/mymedia/savemediafiles/",
    data : values,
    type:'POST',
    dataType: 'json' 
  }).done(function(data){
    if( stype == 'remote' ){
      $.each(data, function(key, val) {
        results[key]=val;
      });
    } else if( stype == 'local' ){
      $('ul.files1').empty();
      $.each(data, function(key, val) {
        var results = [];
        results['result'] = [];
        results['result'].push(val);
        setmyMediaArray(results);
        var result = tmpl("tmpl-mediaElement", results);
        $mymedia.isotope( 'insert', $(result) );
        $('[rel="popover"]').clickover(popoveroptions);
        $('#myMediaList li').draggable(myMediaDragOption);
      });
      
    }
    $('#loading').hide();
  });
  return results;
};

//add mediagroup or remove mediagroup and linked files 
handelMediaGroup = function(name, data2, stype){
  var values = {};
  values[stype] = name, values['form'] = data2;
//console.log( 'handelmediagroup' );
//console.log( values );
  return $.parseJSON($.ajax({
    url: "/crawl?/process/mymedia/savemediagroup/",
    data : values,
    type:'POST',
    async: false,
    dataType: 'json' 
  }).responseText);
};

//add mediagroupfiles or remove mediagroupfiles from list 
handelMediaGroupFiles = function(data,data2, stype){
  var values = {};
  values['selected'] = data.id, 
  values['form'] = data2, 
  values['result'] = data['result'], 
  values['type'] = stype;
////console.log( 'handelMediaGroupFiles' );
////console.log( values );
  return $.parseJSON($.ajax({
    url: "/crawl?/process/mymedia/savemediagroupfiles/",
    data : values,
    type:'POST',
    async: false,
    dataType: 'html' 
  }).responseText);
};

deletMediaFiles = function(data, data2, data3){//, stype){
  var values = {};
  values['selected'] = data, 
  values['form'] = data2,
  values['slides'] = data3;//, 
  //values['type'] = stype;
////console.log( values );
  return $.parseJSON($.ajax({
    url: "/crawl?/process/mymedia/deletemediafiles/",
    data : values,
    type:'POST',
    async: false,
    dataType: 'json' 
  }).responseText);
};

checkBeforeDelete = function(data, data2){//, stype){
  var values = {};
  values['check'] = data, 
  values['form'] = data2;
  return $.parseJSON($.ajax({
    url: "/crawl?/process/mymedia/checkfiles/",
    data : values,
    type:'POST',
    async: false,
    dataType: 'json' 
  }).responseText);
};

deleteTrash = function(trashElements){
  if (trashElements.length == 0) return false;
  var mediagroups = $mediaBoxList.find('li.mediaBox');
  var bodytext,
      trashE = [],
      trashEId = [],
      mediaG = [],
      mediaGE = [],
      findedElements = [];
  findedElements['result'] = []
    //collect trash elements data
  $.each(trashElements, function(i,e){
    trashE.push({
      'name': $(e).attr('isotope-data-name'), 
      'id':$(e).attr('data-id'), 
      'type':$(e).attr('isotope-data-category') ,
      'mediatype':$(e).attr('data-mediatype') 
    });
    trashEId.push(  parseInt($(e).attr('data-id')) );
  });
  
  //loop through mediagroups and find match
  $.each(mediagroups, function(i,e){
    var mediaGEl = $(e).find('li.mediaElement');//.attr('data-id')
    $.each(mediaGEl, function(i,e){
      mediaG.push( parseInt($(e).attr('data-id')) );
      mediaGE.push({ 
        'name': $(e).parent().parent().find('div.name').text(), 
        'id': parseInt($(e).attr('data-id')) 
      });
    });
  });
      //if match record it to array
////console.log( 'trash length' );
////console.log( trashE.length );
  for(var i=0;i< trashE.length;i++){
    findedElements['result'].push({
      'mediaelement':trashE[i].name,
      'id': trashE[i].id,
      'textid':trashE[i].name.toLowerCase().latinize().substring(0,4),
      'mediatype':trashE[i].mediatype,
      'type' : trashE[i].type,
      'groupname': (mediaG.indexOf( parseInt(trashE[i].id) ) != -1) ? mediaGE[mediaG.indexOf( parseInt(trashE[i].id) )].name : '',
      'groupid': (mediaG.indexOf( parseInt(trashE[i].id) ) != -1) ? mediaGE[mediaG.indexOf( parseInt(trashE[i].id) )].id : '',
      'dname' : $('#fileupload [name="diskArea_name"]').val(),
      'did':$('#fileupload [name="diskArea_id"]').val()
    });
  }
  mediaG = [];
  mediaGE = [];
  //sort result by name
  findedElements['result'].sort(dynamicSort('textid'));
  bodytext = tmpl("tmpl-emptytrash", findedElements);
  var checkResult = checkBeforeDelete( findedElements['result'], $('#fileupload').serializeArray() );
  
  
  
  
  if(!checkResult.result.error)
    bodytext += tmpl("tmpl-emptytrash2", checkResult);
  else
    bodytext += tmpl("tmpl-emptytrashMessage", checkResult);
////console.log( 'checkResult[result]' )
////console.log( checkResult['result'] )

////console.log( 'findedElements[result]' )
////console.log( findedElements['result'] )
  $("#confirmDiv").confirmModal({
    heading: 'Delete selected media',
    body: bodytext,
    text:'Delete',
    callback: function () {
      
      var response = deletMediaFiles( findedElements['result'], $('#fileupload').serializeArray(), checkResult.result );
      if (!response.result){
        sendMessage('alert-error', response.error);
      } else {
        for(i in allMediaFiles['result'])
          ( $.inArray( parseInt(allMediaFiles['result'][i].id), trashEId) !== -1 ) ? allMediaFiles['result'].splice(i,1) : '';
        $mymedia.isotope('remove', $(trashElements) );
        sendMessage('alert-success', response.message);
        totalFiles = $('#myMediaList li').length;
        uploadLimit = (maxFiles - totalFiles) > 0 ? maxFiles - totalFiles : 0;
        $('#fileupload').fileupload('option', 'maxNumberOfFiles', uploadLimit);
      }
//console.log( allMediaFiles)
//console.log(trashEId);
/*
////console.log( allMediaFiles)
////console.log(trashEId);

      for(i in allMediaFiles['result']){
        ( $.inArray( parseInt(allMediaFiles['result'][i].id), trashEId) !== -1 ) ? allMediaFiles['result'].splice(i,1) : '';
////console.log( i);
      }
////console.log( allMediaFiles)
//return false;
      $mymedia.isotope('remove', $(trashElements) );
//$trash.empty();
////console.log( allMediaFiles );
*/
    }
  });
};

//handel mediabox select
mediaBoxSelect = function(mediaboxGroup, mgName){
  var mgId = mediaboxGroup.attr('data-object-id');
    (mgId == 'viewAll') ? $('#bc').html('') : $('#bc').html('<span class="divider">>&nbsp;</span><span class="editable" id="mbName_'+mgId+'" data-type="text" data-pk="1">' +mgName+'</span>');
    if (mgId == 'viewAll') {
      $mymedia.find('li.mediaElement').removeClass('hiddenClass');
    }else{
      var ids = [];
      mediaboxGroup.children().next('.sub').find('li.mediaElement').each( function(){
        ids.push( parseInt($(this).attr('data-id')) );
      });
////console.log( ids );
      var arr2 = $.map(allMediaFileId, function (value, key) { return value; });
////console.log( arr2 );
      for(var i=0;i<ids.length;i++) 
        removeItem(arr2, ids[i]);
////console.log( arr2 );
      for (i in arr2) 
        $mymedia.find('li[data-id="'+arr2[i]+'"]').addClass('hiddenClass');
      for (i in ids) 
        $mymedia.find('li[data-id="'+ids[i]+'"]').removeClass('hiddenClass');
    }
    setTimeout(function() {
      $('#myMediaList li').draggable(myMediaDragOption);
      initIso();
    }, 100);
};

//get yutube video info
getYouTubeInfo = function(video_id) {
  var isRecevied = false;
  var media = {};
  $.ajaxSetup({async: false});
  $.getJSON('http://gdata.youtube.com/feeds/api/videos/'+video_id+'?v=2&alt=jsonc', function(data){
    media.title = data.data.title;
    media.desc = data.data.description;
    media.author = data.data.uploader;
    media.updated = data.data.updated;
    media.duration = data.data.duration;
    parseresults(data);
  });
  return media;
};

parseresults = function(data) {
  $('#ytTitle').val(data.data.title);
  $('#ytDesc').val(data.data.description);
  $('#ytAuthor').val(data.data.uploader);
  $('#ytUploaded').val(data.data.updated);
};

/*
addVideoItem = function(id){
  var poster = 'http://img.youtube.com/vi/'+id+'/2.jpg';/hqdefault.jpg
  var $newItems = $('<div class="item" /><div class="item" /><div class="item" />');
$('#container').append( $newItems ).isotope( 'insert', $newItems );
};
*/
})(jQuery);