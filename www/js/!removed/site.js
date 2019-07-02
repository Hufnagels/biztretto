(function($){
var savedBefore = false;

loadAjaxPage = function(object,url) {
  var error = false;
  var objectType = '#'; //'.'
  if (document.getElementById(object) == null) {
    var l = 0;//document.getElementsByClassName(object).length;
    ( l == 1 ) ? objectType = '.' : error = true ;
  };
  if (url == '') error = true ;
  if (error) {
     showMessage({
        text: 'Rendszerhiba. Nincs hova tölteni az információt!!',
        type: 'error'
      });
    return false;
  } else {
    if ($('#loader').length > 0 ) $('#loader').remove();
    var timestamp = new Date().getTime();
    $(objectType+object).html('');
    $('body').append('<div id="loader" class="ui-widget ui-widget-content ui-corner-tl ui-corner-tr ui-corner-bl ui-corner-br" style="z-index:9999999; width:51px; height:50px; background:#eeeeee;"><img src="/images/ajax-loader.gif" style="position:absolute; left:0; top:100px;right:0"/></div>');
    $('#loader').html('<img src="/images/ajax-loader.gif" style="margin: 10px"/>').center(''); 
    $('#'+object).css('display','block');
    if (object == 'editWrapper') {
          $('#bgOverlay').css('display','block');
          $('#editWrapper').append('<span class="closeEdit"></span>');
        };
    $('#'+object).load('/crawl',url);
    $('#loader').remove();
    //$('#editWrapper').append('<span class="closeEdit"></span>');
    return false;
  };
};

collectedData = function(formNames) {
  var myString = new String(formNames); 
  var myArray = myString.split(',');
  var str = '';
  var sData = ''; var ssData = '';
  var max = myArray.length;
  for (var i = 0; i < max; i++){
    var formName = myArray[i];
    str = '';
    ssData = $('form#'+formName).serialize();
    $('#'+formName+' input[disabled]').each( function() {
      str += '&' + this.id + '=' + $(this).val(); 
    });
    $('#'+formName+' input[type=checkbox]').each(function(){
      if ($(this).is(':checked')) { 
        name = $(this).attr('name');
        str += '&' + name + '=1';
      } else {
        name = $(this).attr('name');
        str += '&' + name + '=0';
      };
    });
    $('#'+formName+' input[type=radio]:checked').each(function(){
      if ($(this).is(':checked')) { 
        name = $(this).attr('name');
        str += '&' + name + '=' + $(this).val();
      };
    });
    sData += ssData + str; 
    if ( i < max-1) sData += '&';
  };
  sData = $.base64.encode(sData);
  return sData
};

ajaxSaveData = function(formName,sData){
  var url = $('#'+formName).attr('action');
  if (url == '') url = '/save-data';
  var methode = $('#'+formName).attr('method');
//alert('url:'+url+' methode:'+methode+' sData:'+sData);
  var returnData = '';
  $.ajax({
    type       : methode,
    url        : url,//'pages/save-settings.php?v='+ Number(new Date()), //
    data       : {'post' : sData},
    dataType   : 'html',
    async      : false,
    beforeSend : function() {},
    success    : function(sbData) {returnData = sbData;}, //$.parseJSON(sbData)
    error      : function(response, status, xhr){returnData = response;}
  });
  return returnData;
};

ajaxSaveHtmlData = function(formName,sData){
  var url = $('#'+formName).attr('action');
  if (url == '') url = '/save-data';
  var methode = $('#'+formName).attr('method');
  //alert('url:'+url+' methode:'+methode+' sData:'+sData);
  var returnData = '';
  $.ajax({
    type       : methode,
    url        : url+'?v='+ Number(new Date()), //'pages/save-settings.php?v='+ Number(new Date()),
    data       : sData,
    dataType   : 'html',
    async      : false,
    beforeSend : function() {},
    success    : function(sbData) {returnData = $.parseJSON(sbData);},
    error      : function(response, status, xhr){returnData = response;}
  });
  return returnData;
};

jsonMessage2 = function(jsonData, returnHtml) {
  $.each(jsonData, function (Jkey, Jval) {
    if (Jval && typeof Jval == "object") {
      $.each(Jval, function (Jkey2, Jval2) {
        returnHtml += Jkey2 + ' - ' + Jval2 + '<br>';
      });
    } else {
      if (Jval != '') {
        returnHtml += Jkey + ' - ' + Jval + '<br>';
        num = num + 1;
      }
    }
  })
  return returnHtml;
};

loadNested = function() {
  $.post("nstree.php", function(nested_data){
    $("#treeBlock").html("");
    $("#treeBlock").html(nested_data);
  });
}

/*
$.datepicker.regional['hu'] = {
    closeText: 'bezárás',
    prevText: '&laquo;&nbsp;vissza',
    nextText: 'elĹ?re&nbsp;&raquo;',
    currentText: 'ma',
    monthNames: ['Január', 'Február', 'Március', 'Április', 'Május', 'Június',
    'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December'],
    monthNamesShort: ['Jan', 'Feb', 'Már', 'Ápr', 'Máj', 'Jún',
    'Júl', 'Aug', 'Szep', 'Okt', 'Nov', 'Dec'],
    dayNames: ['Vasárnap', 'Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat'],
    dayNamesShort: ['Vas', 'Hét', 'Ked', 'Sze', 'Csü', 'Pén', 'Szo'],
    dayNamesMin: ['V', 'H', 'K', 'Sze', 'Cs', 'P', 'Szo'],
    weekHeader: 'Hé',
    dateFormat: 'yy-mm-dd',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: true,
    yearSuffix: ''};
  $.datepicker.regional['en'] = {
    closeText: 'Done',
    prevText: 'Prev',
    nextText: 'Next',
    currentText: 'Today',
    monthNames: ['January','February','March','April','May','June',
    'July','August','September','October','November','December'],
    monthNamesShort: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun',
    'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
    dayNames: ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
    dayNamesShort: ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'],
    dayNamesMin: ['Su','Mo','Tu','We','Th','Fr','Sa'],
    weekHeader: 'Wk',
    dateFormat: 'dd/mm/yy',
    firstDay: 1,
    isRTL: false,
    showMonthAfterYear: false,
    yearSuffix: ''};
  //$.datepicker.setDefaults($.datepicker.regional['en']);
  $.datepicker.setDefaults($.datepicker.regional['hu']);
*/
})(jQuery);


(function($){

	$.fn.jCircle = function(options) {
		var defaults = {
			size: 40
		},
			settings = $.extend({}, defaults, options);

		settings.size = parseInt(settings.size) || 40;
		settings.radius = (settings.size/2) + 'px';

		return this.each(function(){
			var $this = $(this);

			$this.css({
				display: 'block',
				width: settings.size + 'px',
				height: settings.size + 'px'
			});

			$this.css('-moz-border-radius', settings.radius);
			$this.css('-webkit-border-radius', settings.radius);
      $this.css('border-radius', settings.radius);
		});
	}

    $.fn.extend({ 
        tMenu: function(options) {
            var defaults = {
                tabcontainer : '#tabs',
                activeTab : 0
            };

            var options = $.extend(defaults, options);

            return this.each(function() {
                  var o = options;
                  var obj = $(this);
                  $(o.tabcontainer + ' > div').hide();
                  //$(o.tabcontainer + ' > div:not(:first)').hide();
                  var liItems = $('li',obj);
                  var divItems = $(o.tabcontainer + ' > div');
                  
                  var aItems = $('li a',obj);
                  //$('li:first',obj)
                  $(liItems[o.activeTab],obj).addClass('active');
                  //$(o.tabcontainer ,' div:first').show();
                  $(divItems[o.activeTab],o.tabcontainer).show();
                  aItems.click(function(){
                    liItems.removeClass('active');
                    $(this).parent().addClass('active');
                    var currentTab = this.hash;
                    var regexp = /#/gi;
                    var href = $(this).attr("rel");
                    $(o.tabcontainer + ' > div').hide();
                    $(currentTab).show().css('display','block');
                    if (href)
                      loadAjaxPage(currentTab.replace(regexp, ''), href);
                    return false;
                  }); 
            });
        }
    });

    $.fn.extend({
        tAccord: function(options) {
            var defaults = {
                accordionContainer : 'ul#accordion'
            };

            var options = $.extend(defaults, options);

            return this.each(function() {
                  var o = options;
                  var obj = $(this);
                  $(o.accordionContainer + ' .firstLine').hide();
                  var headItems = $('a.heading',obj);
                  var aItems = $('li a',obj);
                  $('li:first',obj).addClass('active');
                  //$(o.accordionContainer ,' div:first').show();
                  headItems.click(function(){
                    $(this).css('outline','none');
                    if($(this).parent().hasClass('current')) {
                      $(this).siblings('ul.firstLine').slideUp('fast',function() {
                        $(this).parent().removeClass('current');
                        //$.scrollTo('#accordion',1000);
                      });
                    } else {
                      $(o.accordionContainer + ' li.current ul.firstLine').slideUp('fast',function() {
                        $(this).parent().removeClass('current');
                      });
                      $(this).siblings('ul.firstLine').slideToggle('fast',function() {
                        $(this).parent().toggleClass('current');
                      });
                      //$.scrollTo('#accordion',1000);
                    }
                    return false;
                  }); 
            });
        }
    });
})(jQuery);
/*
function showNotification(params){
    // options array
    var options = { 
        'showAfter'         : 0, // number of sec to wait after page loads
        'duration'          : 3, // display duration
        'autoClose'         : false, // flag to autoClose notification message
        'type'              : 'error', // type of info message error/success/info/warning
        'message'           : 'Return to System Administrator', // message to dispaly
        'link_message'      : '', // link flag to show extra description
        'link_notification' : 'Részletek', // link flag to show extra description
        'description'       : '', // link to desciption to display on clicking link message,
        'func'              : function() {}, // run after close box 
        'fade'              : false 
    }; 
    // Extending array from params
    $.extend(true, options, params);
    
    var msgclass = 'succ_bg'; // default success message will shown
    if(options['type'] == 'error'){
        msgclass = 'error_bg'; // over write the message to error message
    } else if(options['type'] == 'information'){
        msgclass = 'info_bg'; // over write the message to information message
    } else if(options['type'] == 'warning'){
        msgclass = 'warn_bg'; // over write the message to warning message
    } 
    
    // Parent Div container
    var container = '<div id="info_message" class="' + msgclass + ' container_12">';
    container +=      '<div class="center_auto">';
    container +=        '<div class="info_message_text message_area">';
    container +=           options['message'];
    container +=        '</div>';
    if (!options['autoClose'])
      container +=        '<div class="info_close_btn button_area" onclick="return closeNotification()"></div>';
    container +=        '<div class="clearboth"></div>';
    if (options['description'] != '') {
	    container +=        '<div class="link_notification">' + options['link_notification'] + '</div>';
	    container +=      '</div>';
	    container +=      '<div class="info_more_descrption container_12">' + jsonMessage2(options['description'], '') + '</div>';
	    container +=    '</div>';
    } else {
      container +=    '</div></div>';
    }
    
    $notification = $(container);
    
    // Appeding notification to Body
    if(options['fade']){
	    $('body').append('<div id="fade" class="fade ui-widget-overlay"></div>');
      $('#fade').css({'display':'block','width': '100%', 'height': '100%', 'position': 'fixed','left': '0','top': '0'}); 
    }
    $('body').append($notification);
    
    var divHeight = $('div#info_message').height();
    // see CSS top to minus of div height
    $('div#info_message').css({
        top : '-'+divHeight+'px'
    });
    
    // showing notification message, default it will be hidden
    $('div#info_message').show();
    
    // Slide Down notification message after startAfter seconds
    slideDownNotification(options['showAfter'], options['autoClose'],options['duration'],options['func']);
    
    $('.link_notification').live('click', function(){
      $('.info_more_descrption').html(jsonMessage2(options['description'], '')).slideDown('fast');
    });
    
}
// function to close notification message
// slideUp the message
function closeNotification(duration, func){
    var divHeight = $('div#info_message').height();
    setTimeout(function(){
        $('div#info_message').animate({
            top: '-'+divHeight
        }); 
        // removing the notification from body
        setTimeout(function(){
            $('div#info_message').remove();
              removeFade();
            //if (func !='')
            // eval(func);
            if (typeof func == "function") func.call(this);
        },200);
    }, parseInt(duration * 1000));   
    
}

// sliding down the notification
function slideDownNotification(startAfter, autoClose, duration, func){    
    setTimeout(function(){
        $('div#info_message').animate({
            top: 0
        }); 
        if(autoClose){
            setTimeout(function(){
                closeNotification(duration, func);
            }, duration);
        }
    }, parseInt(startAfter * 1000));
}

function removeFade() {
	if ($('#fade').length > 0) {
  	$('#fade').remove();
  }
}
*/
;(function(h){var m=h.scrollTo=function(b,c,g){h(window).scrollTo(b,c,g)};m.defaults={axis:'y',duration:1};m.window=function(b){return h(window).scrollable()};h.fn.scrollable=function(){return this.map(function(){var b=this.parentWindow||this.defaultView,c=this.nodeName=='#document'?b.frameElement||b:this,g=c.contentDocument||(c.contentWindow||c).document,i=c.setInterval;return c.nodeName=='IFRAME'||i&&h.browser.safari?g.body:i?g.documentElement:this})};h.fn.scrollTo=function(r,j,a){if(typeof j=='object'){a=j;j=0}if(typeof a=='function')a={onAfter:a};a=h.extend({},m.defaults,a);j=j||a.speed||a.duration;a.queue=a.queue&&a.axis.length>1;if(a.queue)j/=2;a.offset=n(a.offset);a.over=n(a.over);return this.scrollable().each(function(){var k=this,o=h(k),d=r,l,e={},p=o.is('html,body');switch(typeof d){case'number':case'string':if(/^([+-]=)?\d+(px)?$/.test(d)){d=n(d);break}d=h(d,this);case'object':if(d.is||d.style)l=(d=h(d)).offset()}h.each(a.axis.split(''),function(b,c){var g=c=='x'?'Left':'Top',i=g.toLowerCase(),f='scroll'+g,s=k[f],t=c=='x'?'Width':'Height',v=t.toLowerCase();if(l){e[f]=l[i]+(p?0:s-o.offset()[i]);if(a.margin){e[f]-=parseInt(d.css('margin'+g))||0;e[f]-=parseInt(d.css('border'+g+'Width'))||0}e[f]+=a.offset[i]||0;if(a.over[i])e[f]+=d[v]()*a.over[i]}else e[f]=d[i];if(/^\d+$/.test(e[f]))e[f]=e[f]<=0?0:Math.min(e[f],u(t));if(!b&&a.queue){if(s!=e[f])q(a.onAfterFirst);delete e[f]}});q(a.onAfter);function q(b){o.animate(e,j,a.easing,b&&function(){b.call(this,r,a)})};function u(b){var c='scroll'+b,g=k.ownerDocument;return p?Math.max(g.documentElement[c],g.body[c]):k[c]}}).end()};function n(b){return typeof b=='object'?b:{top:b,left:b}}})(jQuery);


/*
form_serialize = function(formname, extra) {
  var sData, extra, str;
  sData = $('form#'+formname).serialize();
  if ( typeof extra !== "undefined" && extra) {
    if ( typeof sData == "undefined" || sData.length == 0) { 
      sData = extra.slice(1,extra.length); 
      } else {
    sData += extra;
    }
  }
  //alert ('site.js: ' + sData);
  str = $.base64.encode(sData);
  return str;
}

collectData = function(formName) {
  var str = '';
  $('#'+formName+' input[disabled]').each( function() {
    str += '&' + this.id + '=' + $(this).val(); 
  });
  $('#'+formName+' input[type=checkbox]').each(function(){
    if ($(this).is(':checked')) { 
      name = $(this).attr('name');
      str += '&'+name+'=1';
    } else {
      name = $(this).attr('name');
      str += '&'+name+'=0';
    }
  });
  
  var sData = form_serialize(formName, str);
  return sData
}

ajax_save_data = function(sData){
  var returnData = '';
  $('#xca').val('');
  $('#xcb').val('');
  $.ajax({
    type       : 'post',
    url        : 'pages/save-settings.php?v='+ Number(new Date()),
    data       : {'post' : sData},
    dataType   : 'json',
    async      : false,
    beforeSend : function() {
        //open_popup('Info','Mentés folymatban....');
        $('#message-box .close').addClass('invisible');
    },
    success    : function(sbData) {
      returnData = sbData;
    },
    error      : function(response, status, xhr){
      returnData = response;
    }
  });
  //alert(returnData);
  return returnData;
}

json_message = function(jsonData) {
  var jsonObj = $.parseJSON(jsonData);
  if (jsonObj.id !== '') {
    txt += '<p><b>' + jsonObj.id + '</b>';
  }
  if ($.isArray(jsonObj.message)) {
    txt += '<p><ul style="list-style:none; margin-left:10px; padding-left:10px;">';
    $.each(jsonObj.message, function (i, item) {
      txt += '<li>';// + jsonObj.message[i].id;
      txt += '<p style="margin-left:10px;">' + jsonObj.message[i].message + '</li>';
    });
    txt += '</ul>';
  } else {
    txt += '<p>' + jsonObj.message;
  }
  return txt;
}

open_popup = function(header, message) {
  if ($('#fade').length < 1) {
    $('body').append('<div id="fade" class="fade ui-widget-overlay"></div>');
    $('#fade').css({'display':'block','width': '100%', 'height': '100%', 'position': 'fixed','left': '0','top': '0'}); 
    $('body').append('<div id="message-box" class="ui-widget-content ui-corner-tl ui-corner-tr" style="cursor:move;"><b><h2 class="ui-widget-header ui-corner-tl ui-corner-tr">' + header + '</h2></b><div id="message" class="ui-widget-content ui-corner-bl ui-corner-br"></div></div>');
    //$("#message-box").draggable();
    $('#message-box').css({'position':'fixed'}).center().fadeIn('fast').prepend('<div class="message_close_tab"><span class="closeText">[Close]</span><span class="ui-icon ui-icon-close"></span></div>');
    $('#message').addClass('ui-widget-content').html( message);
  }
}

sendMessage = function(header, message, contentdiv) {
  if( message != '') {
    $('body').append('<div id="message-box" class="ui-widget-content ui-corner-tl ui-corner-tr ui-corner-bl ui-corner-br"><h2 class="ui-widget-header ui-corner-tl ui-corner-tr">' + header + '</h2><div id="message" class="ui-widget-content ui-corner-bl ui-corner-br"></div></div>');
    $('#message').html( message);
    $('#message-box').center('wrapper').fadeIn('fast').fadeOut(9000, function() {
      $(this).remove();
    });
  }
}
*/
// referens oldal
/*
processRefData = function(data, messages) {
  //if (data.message !='No action given') {
  var arr = data.ref;
          var txt='<table>';
          for (var i=0; i < arr.length; i++) {
            var has_child = 'false';
            var a=0;
            var delable =messages[0];
            is_del='';
            var o = arr[i];
            var ref_id = o.id;
            var ref_parent = o.parent;
            var ref_name = o.val;
            var ref_adv = o.adv_count;
            var ref_cr = o.credit
            // check if has child 
            a=i+1;
            if ( (a < arr.length) && (arr[a].parent == arr[i].id) ) {
               delable =messages[0];
               has_child = 'true';
              }
            // check if has advertise
            if (ref_adv == 0 && has_child == 'false' && ref_cr == 0) { 
              delable = messages[1]; 
              is_del='<p name="del_ref_'+ref_id+'" id="del_ref_'+ref_id+'" value="X" class="del_ref ui-icon ui-icon-closethick" style="float:right; text-align=right; display:inline; cursor:pointer;" onclick="del_referent(this.id);" title="'+ messages[2] +'">';
              }
            txt +='<tr><td width="62">Referens: </td><td width="224">' + ref_name + '</td><td width="87" rowspan="2">'+ delable  +'</td><td width="50" rowspan="2" id="td_'+ref_id+'">' + is_del + '</td></tr><tr><td width="62">&nbsp;</td><td>' + ref_adv + messages[3] + ',' + messages[4] + ': ' + ref_cr + '</td></tr>';
            };
            txt +='</table>';
  return txt;          
}
*/
/*
save_nested = function(nestedId) {
  open_popup('Info','<? echo Lang(in_progress); ?>');
  $('#refs input[name=ac]').val('sns');
  arraied = $('ol.sortable').nestedSortable('toArray', {startDepthCount: 0});
  //var sData = $('form#refs').serialize();
  var sData = form_serialize('form#refs',arraied);
  str = sData;//$.base64_encode(sData);
  
  $('#refs input[name=ac]').val('');
  $.ajax({
    type: 'POST',
    url: 'pages/save_settings.php',
    data: {'post': str, 'toArray': arraied},
    dataType: 'json',
    success: function(sData){
      sbData = $.toJSON(sData);
      
      $('#message').html('').html(json_message(sbData));
      
      //$('#AddReferent').html('');
      load_nested();
      loadAjaxPage('content', 'pages/form_add_referens.php?i=' + nestedId);
      $('#message-box').center('');
    },
    error  : function(jqXHR, textStatus, errorThrown){
      $('#message').html(textStatus);
       $('#message-box .close').removeClass('invisible').addClass('visible');
    }
  });
}


del_referent = function(obj) {
  $('#refs input[name=ac]').val('dr');
  var sData = $('form#refs').serialize();
  var i = obj.split('del_ref_');
  var string = sData + '&ref=' + i[1];
  var str = $.base64_encode(string);
  $('#refs input[name=ac]').val('');
  $.ajax({
    type: 'POST',
    url: 'pages/save_settings.php',
    data: {'post': str},
    dataType: 'json',
    success: function(data){
      var object = data.message;
      var ref=object.split(' - ');
      if (ref[0] != 0) {
        $('#del_ref_'+i[1]).remove();
        $('#td_'+i[1]).append('<b> - '+ data.message +'</b>')
        $('#ref_'+i[1]).remove();
        load_nested();
        } else { 
          $('#message').html('').html('<? echo Lang(error_error); ?>');//data.message);
      }
    }            
  });
}
*/