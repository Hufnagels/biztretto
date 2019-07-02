/*
$(function() {  
// from version 1.9

  $.extend( $.fn.dataTable.defaults, {
    "bProcessing": true,
    
    "bStateSave": true,
    "sPaginationType": "full_numbers",
    "oLanguage": {
      "sProcessing":   "Feldolgozás...",
      "sLengthMenu":   "_MENU_ találat oldalanként",
      "sZeroRecords":  "Nincs a keresésnek megfelel\u0151 találat",
      "sInfo":         "Találatok: _START_ - _END_ Összesen: _TOTAL_",
      "sInfoEmpty":    "Nulla találat",
      "sInfoFiltered": "", // (_MAX_ összes rekord közül sz\u0171rve)
      "sInfoPostFix":  "",
      "sSearch":       "Keresés:",
      "sUrl":          "",
      "oPaginate": {
        "sFirst":    "Első",
        "sPrevious": "Előző",
        "sNext":     "Következő",
        "sLast":     "Utolsó"
      }
    }
  });
});
*/

(function($, window, document) {
// API method to get paging information
$.fn.dataTableExt.oApi.fnPagingInfo = function ( oSettings )
{
    return {
        "iStart":         oSettings._iDisplayStart,
        "iEnd":           oSettings.fnDisplayEnd(),
        "iLength":        oSettings._iDisplayLength,
        "iTotal":         oSettings.fnRecordsTotal(),
        "iFilteredTotal": oSettings.fnRecordsDisplay(),
        "iPage":          Math.ceil( oSettings._iDisplayStart / oSettings._iDisplayLength ),
        "iTotalPages":    Math.ceil( oSettings.fnRecordsDisplay() / oSettings._iDisplayLength )
    };
}
 
// Bootstrap style pagination control 
$.extend( $.fn.dataTableExt.oPagination, {
    "bootstrap": {
        "fnInit": function( oSettings, nPaging, fnDraw ) {
            var oLang = oSettings.oLanguage.oPaginate;
            var fnClickHandler = function ( e ) {
                e.preventDefault();
                if ( oSettings.oApi._fnPageChange(oSettings, e.data.action) ) {
                    fnDraw( oSettings );
                }
            };
 
            $(nPaging).addClass('pagination').append(
                '<ul>'+
                    '<li class="prev disabled"><a href="#">&larr; '+oLang.sPrevious+'</a></li>'+
                    '<li class="next disabled"><a href="#">'+oLang.sNext+' &rarr; </a></li>'+
                '</ul>'
            );
            var els = $('a', nPaging);
            $(els[0]).bind( 'click.DT', { action: "previous" }, fnClickHandler );
            $(els[1]).bind( 'click.DT', { action: "next" }, fnClickHandler );
        },
 
        "fnUpdate": function ( oSettings, fnDraw ) {
            var iListLength = 5;
            var oPaging = oSettings.oInstance.fnPagingInfo();
            var an = oSettings.aanFeatures.p;
            var i, j, sClass, iStart, iEnd, iHalf=Math.floor(iListLength/2);
 
            if ( oPaging.iTotalPages < iListLength) {
                iStart = 1;
                iEnd = oPaging.iTotalPages;
            }
            else if ( oPaging.iPage <= iHalf ) {
                iStart = 1;
                iEnd = iListLength;
            } else if ( oPaging.iPage >= (oPaging.iTotalPages-iHalf) ) {
                iStart = oPaging.iTotalPages - iListLength + 1;
                iEnd = oPaging.iTotalPages;
            } else {
                iStart = oPaging.iPage - iHalf + 1;
                iEnd = iStart + iListLength - 1;
            }
 
            for ( i=0, iLen=an.length ; i<iLen ; i++ ) {
                // Remove the middle elements
                $('li:gt(0)', an[i]).filter(':not(:last)').remove();
 
                // Add the new list items and their event handlers
                for ( j=iStart ; j<=iEnd ; j++ ) {
                    sClass = (j==oPaging.iPage+1) ? 'class="active"' : '';
                    $('<li '+sClass+'><a href="#">'+j+'</a></li>')
                        .insertBefore( $('li:last', an[i])[0] )
                        .bind('click', function (e) {
                            e.preventDefault();
                            oSettings._iDisplayStart = (parseInt($('a', this).text(),10)-1) * oPaging.iLength;
                            fnDraw( oSettings );
                        } );
                }
 
                // Add / remove disabled classes from the static elements
                if ( oPaging.iPage === 0 ) {
                    $('li:first', an[i]).addClass('disabled');
                } else {
                    $('li:first', an[i]).removeClass('disabled');
                }
 
                if ( oPaging.iPage === oPaging.iTotalPages-1 || oPaging.iTotalPages === 0 ) {
                    $('li:last', an[i]).addClass('disabled');
                } else {
                    $('li:last', an[i]).removeClass('disabled');
                }
            }
        }
    }
} );

// $ DataTables Library addONs
$.fn.dataTableExt.oApi.fnReloadAjax = function ( oSettings, sNewSource, fnCallback, bStandingRedraw ){
  if ( typeof sNewSource != 'undefined' && sNewSource != null ) {
    oSettings.sAjaxSource = sNewSource;
  }
  this.oApi._fnProcessingDisplay( oSettings, true );
  var that = this;
  var iStart = oSettings._iDisplayStart;
  
  oSettings.fnServerData( oSettings.sAjaxSource, null, function(json) {
    // Clear the old information from the table 
    that.oApi._fnClearTable( oSettings );
    // Got the data - add it to the table 
    for ( var i=0 ; i<json.aaData.length ; i++ ) {
      that.oApi._fnAddData( oSettings, json.aaData[i] );
    };
    oSettings.aiDisplay = oSettings.aiDisplayMaster.slice();
    that.fnDraw( that );
    if ( typeof bStandingRedraw != 'undefined' && bStandingRedraw === true ) {
      oSettings._iDisplayStart = iStart;
      that.fnDraw( false );
    };
    that.oApi._fnProcessingDisplay( oSettings, false );
    // Callback user function - for event handlers etc 
    if ( typeof fnCallback == 'function' && fnCallback != null ) {
      fnCallback( oSettings );
    };
  });
};

var DT_PagingControl = function ( oDTSettings ) {
  	oDTSettings.aoDrawCallback.push({
			"fn": function () {
			  var bShow = oDTSettings.oInstance.fnPagingInfo().iTotalPages > 1;
			  for ( var i=0, iLen=oDTSettings.aanFeatures.p.length ; i<iLen ; i++ ) {
			    oDTSettings.aanFeatures.p[i].style.display = bShow ? "block" : "none";
		    };
			},
			"sName": "PagingControl"
		});
}

if ( typeof $.fn.dataTable == "function" &&
     typeof $.fn.dataTableExt.fnVersionCheck == "function" &&
     $.fn.dataTableExt.fnVersionCheck('1.8.0') ) {
	$.fn.dataTableExt.aoFeatures.push({
		"fnInit": function( oDTSettings ) {
		  new DT_PagingControl( oDTSettings );
		},
		"cFeature": "P",
		"sFeature": "PagingControl"
	});
} else {
	alert( "Warning: PagingControl requires DataTables 1.8.0 or greater - www.datatables.net/download");
};
})(jQuery, window, document);




$.editable.addInputType('autogrow', {
    element : function(settings, original) {
        var textarea = $('<textarea />');
        if (settings.rows) {
            textarea.attr('rows', settings.rows);
        } else {
            textarea.height(settings.height);
        };
        if (settings.cols) {
            textarea.attr('cols', settings.cols);
        } else {
            textarea.width(settings.width);
        };
        $(this).append(textarea);
        return(textarea);
    },
    plugin : function(settings, original) {
        $('textarea', this).autogrow(settings.autogrow);
    }
});

$.editable.addInputType('charcounter', {
    element : function(settings, original) {
        var textarea = $('<textarea />');
        if (settings.rows) {
            textarea.attr('rows', settings.rows);
        } else {
            textarea.height(settings.height);
        };
        if (settings.cols) {
            textarea.attr('cols', settings.cols);
        } else {
            textarea.width(settings.width);
        };
        $(this).append(textarea);
        return(textarea);
    },
    plugin : function(settings, original) {
        $('textarea', this).charCounter(settings.charcounter.characters, settings.charcounter);
    }
});

$.editable.addInputType('hybrid', {
        element : function(settings, original) {
            var textarea = $('<textarea />');
            if (settings.rows) {
                textarea.attr('rows', settings.rows);
            } else {
                textarea.height(settings.height);
            };
            if (settings.cols) {
                textarea.attr('cols', settings.cols);
            } else {
                textarea.width(settings.width);
            };
            $(this).append(textarea);
            return(textarea);
        },
        plugin : function(settings, original) {
            $('textarea', this).charCounter(settings.charcounter.characters, settings.charcounter);
            $('textarea', this).autogrow(settings.autogrow);
        }
    });
    
function inlineEditHybrid (oTable, table, url) {
  $('.hybrid', oTable.fnGetNodes()).editable( url, { 
    type      : "hybrid",
    event     : "click",
    submit    : 'OK',
    cancel    : 'cancel',
    style     : "inherit",
    onblur    : "ignore",
    charcounter : {
      characters : 100
    },
    autogrow : {
      lineHeight : 16,
      minHeight  : 32
    },
    intercept: function (jsondata) {
      var ssData = $.parseJSON(jsondata);
      showMessage({
          text: ssData.sResult,
          type: ssData.sType
      });
      // do something with obj.status and obj.other
      //$('#officeCurrentBallace').html(ssData.sName);
      return(ssData.sAction);
    },
    callback  : function( sValue, y ) {
      var aPos = oTable.fnGetPosition( this.parentNode );
      //oTable.fnUpdate( sValue, aPos[0], aPos[1] );
      oTable.fnDraw();
    },
    "submitdata": function ( value, settings ) {
      var aPos = oTable.fnGetPosition(this.parentNode);
      //alert(aPos);
      var aData = oTable.fnGetData(aPos[0]);
      return {
        "id": aData[0],
        "ref": aPos
        //keel az eldó lakas
      };
      
    }
  });
};

function inlineEditCC (oTable, table, url) {
  $('.editableCC', oTable.fnGetNodes()).editable( url, { 
    type      : "charcounter",
    event     : "click",
    submit    : 'OK',
    cancel    : 'cancel',
    style     : "inherit",
    onblur    : "ignore",
    charcounter : {
      characters : 100
    },
    intercept: function (jsondata) {
      var ssData = $.parseJSON(jsondata);
      showMessage({
          text: ssData.sResult,
          type: ssData.sType
      });
      // do something with obj.status and obj.other
      //$('#officeCurrentBallace').html(ssData.sName);
      return(ssData.sAction);
    },
    callback  : function( sValue, y ) {
      var aPos = oTable.fnGetPosition( this.parentNode );
      //oTable.fnUpdate( sValue, aPos[0], aPos[1] );
      oTable.fnDraw();
    },
    "submitdata": function ( value, settings ) {
      var aPos = oTable.fnGetPosition(this.parentNode);
      var aData = oTable.fnGetData(aPos[0]);
      return {
        "id": aData[0],
        "ref": aPos
        //keel az eldó lakas
      };
      
    }
  });
};

function inlineEdit(oTable, table, url) {
  $('.editable', oTable.fnGetNodes()).editable( url, { 
    indicator : "<img src='../images/ajax-loader-min.gif' width='10' height='10'>",
    tooltip   : "Click to edit...",
    event     : "click",
    style     : "inherit",
    height    : "14px", 
    width     : "90%",
    intercept: function (jsondata) {
      var ssData = $.parseJSON(jsondata);
      showMessage({
          text: ssData.sResult,
          type: ssData.sType
      });
      // do something with obj.status and obj.other
      return(ssData.sAction);
    },
    callback  : function( sValue, y ) {
      var aPos = oTable.fnGetPosition( this.parentNode );
      //oTable.fnUpdate( sValue, aPos[0], aPos[1] );
      oTable.fnDraw();
    },
    "submitdata": function ( value, settings ) {
      var aPos = oTable.fnGetPosition(this.parentNode);
      var aData = oTable.fnGetData(aPos[0]);
      return {
        "id": aData[0],
        "ref": aPos
        //keel az eldó lakas
      };
      
    }/*
    callback  : function( sValue, y ) {
      var aPos = oTable.fnGetPosition( this );

      //oTable.fnUpdate( sValue, aPos[0], aPos[1] );
      //open_popup('Info',y);
      },
    submitdata: function ( sValue, y ) {
      var aPos = oTable.fnGetPosition(this);
      var aData = oTable.fnGetData(aPos[0]);
  //    alert(aData[0]);
      return {
        "id": aData[0],
        "ref": aData[1],
        "value": sValue //aPos[2]
      };
    },*/
  });
};

function inlineEditAgrow(oTable, table, url) {
  $('.autogrow', oTable.fnGetNodes()).editable( url, { 
    indicator : "<img src='../images/ajax-loader-min.gif' width='10' height='10'>",
    type      : "autogrow",
    tooltip   : "Click to edit...",
    event     : "click",
    submit    : 'OK',
    cancel    : 'cancel',
    style     : "inherit",
    onblur    : "ignore",
    autogrow  : {
             lineHeight : 16,
             minHeight  : 32
          },
    intercept: function (jsondata) {
      var ssData = $.parseJSON(jsondata);
      showMessage({
          text: ssData.sResult,
          type: ssData.sType
      });
      // do something with obj.status and obj.other
      return(ssData.sAction);
    },
    callback  : function( sValue, y ) {
      var aPos = oTable.fnGetPosition( this.parentNode );
      //oTable.fnUpdate( sValue, aPos[0], aPos[1] );
      oTable.fnDraw();
    },
    "submitdata": function ( value, settings ) {
      var aPos = oTable.fnGetPosition(this.parentNode);
      var aData = oTable.fnGetData(aPos[0]);
      return {
        "id": aData[0],
        "ref": aPos
        //keel az eldó lakas
      };
      
    }
    /*
    callback  : function( sValue, y ) {
      var aPos = oTable.fnGetPosition( this );

      //oTable.fnUpdate( sValue, aPos[0], aPos[1] );
      //open_popup('Info',y);
      },
    submitdata: function ( sValue, y ) {
      var aPos = oTable.fnGetPosition(this);
      var aData = oTable.fnGetData(aPos[0]);
  //    alert(aData[0]);
      return {
        "id": aData[0],
        "ref": aData[1],
        "value": sValue //aPos[2]
      };
    },*/
  });
};

function serializeTable(formname) {
  var str = $('form#'+formname).serialize();
  var sData = $('input', oTable.fnGetNodes()).serialize();
  if (sData == '') {
    showMessage({
	      text: "<? echo Lang('none_selected'); ?>",
	      type: 'warning'
	    });
    return 'q';
  } else {
    str = str+'&'+sData;
    return str;
  };
};

function serializeDatatables(formname){
  var str = $('form#'+formname).serialize();
  var sData = $('input', oTable.fnGetNodes()).serialize();
  if (sData == '') {
    return '';
    } else {
      str = str + '&' + sData;
      return str;
  };
};

function reloadDataTable(table){
  table.fnDraw();
  //table.fnReloadAjax();
};

function fixTableWidth(tableId) {
  var tableWrapper = $('#' + tableId + '_wrapper');
  //remove widths that shouldn't be there on the datatable
  $('table.dataTable').css('width', '');
  tableWrapper.css('width', '');
  tableWrapper.find('.dataTables_scrollHead').css('width', '');
  tableWrapper.find('.dataTables_scrollHeadInner').css('width', '');
  tableWrapper.find('.dataTables_scrollHeadInner').find('table').css('width', '');
  $.each($('table.dataTable > thead > tr > th'), function(i,e) {
    $(this).css('width', '');
    //alert(e);
  });
  var tableDataContent = tableWrapper.find('.dataTables_scrollBody')[0];
  var tableHasScrollBar = true;//(tableDataContent.scrollHeight > tableDataContent.clientHeight);
 
  //fixes header aligment issues in all major browsers
  if (tableHasScrollBar)
    tableWrapper.find('.dataTables_scrollHead').css('margin-right', '15px');
 
  //fixes table data stretchyness in <IE8
  if (navigator.appVersion.indexOf("MSIE 6") != -1 || navigator.appVersion.indexOf("MSIE 7") != -1) {
    $('#' + tableId).css('width', '');
    tableWrapper.find('.dataTables_scrollBody').css('overflow-x', 'hidden');
  };
  tableWrapper.find('.dataTables_scrollBody').css('width', $('#tableBox div.content').width() + 'px');
};