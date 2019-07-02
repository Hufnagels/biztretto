var templateSlide = false;
var $mymedia = null;
var $slides = $( '#slidesList' ),
    $slides2 = $( '#slidesList2' ),
    $slidesScroller = $( '.slidesList' ),
    $mediaBoxContainer = $( "#editorsMediaBox" ),
    $editor = $( '#editorArea' ),
    $training = $( '#trainings' ),
    $attach = $( '#attachementList' ),
    $trainings = $( '#trainingSlides' ),
    $tests = $( '#testSlides' );

var editor;

var slideIDs = [];
//editor, editor elements
var editingAreaWidth, editingAreaHeight;
var textClass = 'textClass';
var resizableClass = 'ResizableClass';
var nonResizableClass = 'nonResizableClass';
var isSelected = 'isSelected';

var triggerSave = false;

//scrollcontainer size    
var scrollContainerHeight = 345;
var scrollContainerInnerHeight = 270;
var slideScoller;
/**/
//var isCaretOn 
//mediaBox elements drag
var myMediaDragOption = {
    appendTo: 'body',
    handle  : 'img',
    revert  : 'true',
    helper  : 'clone',
    start   : function ( e, ui ) {
//console.log( 'myMediaDragOption START fired: templateDropOption' );
//console.log( 'prevOffset: ' + prevOffset );
        //2013.01.19
        $editor.find( '.holder .textDiv' ).droppable( templateDropOption );//templateDropOption);
        removeToolsEvent();
        //$('#editorArea .sortableForm').droppable(formDrop);
//removeToolsEvent();
//console.log( 'myMediaDragOption START fired: editorDropOption' );
        $editor.droppable( editorDropOption );
//2012.12.11
//console.log( 'myMediaDragOption START fired: attachDropOption' );
        $attach.droppable( attachDropOption );

        prevOffset = true;
    },
    stop    : function ( e, ui ) {
        //2013.01.19

        $editor.droppable( 'destroy' );
        //2012.12.11
        $attach.droppable( 'destroy' ).sortable( sortableAttach );
        if ( $editor.attr( 'data-template-type' ) !== 'normal' ) addToolsEvent( $editor.attr( 'data-template-type' ) );
    }
};

//drop image,video, audio elements
var editorDropOption = {
    tolerance  : 'pointer',
    accept     : ".image, .video, .audio, li",
    activeClass: "ui-state-highlight",
    hoverClass : "ui-state-active",
    //greedy: true,
    //iframeFix: true,
    drop       : function ( e, ui ) {

        //check puzzle template type
        //check if img dropped too
//console.log( 'hany img van a szerkeszton? '+$(this).find('img').length );

        var parentOffset = $( this ).offset();
        //or $(this).offset(); if you really just want the current element's offset
        var posX = e.pageX - parentOffset.left;
        var posY = e.pageY - parentOffset.top;
        posX = parseInt( posX / editingAreaWidth * 100 );
        posY = parseInt( posY / editingAreaHeight * 100 );

        //2013.01.19
//console.log( 'editordropoption DROP fired (pervoffset check) ' +  prevOffset);
        if ( !prevOffset && ($editor.attr( 'data-template-type' ) !== 'normal' && $editor.attr( 'data-template-type' ) !== 'puzzle') ) {
            //prevOffset=true;
            //console.log( 'kukazva az elem' );
            //2013.02.12
            //return false;
        }
        var isPuzzle = false;
        if ( $editor.attr( 'data-template-type' ) == 'puzzle' )
            isPuzzle = true;

//console.log( 'editordropoption DROP prevOffset: ' +  prevOffset);
//console.log( 'editordropoption DROP prevOffset: ' +  ui.draggable.children().attr('class') );
        if ( !prevOffset ) return false;
        //var $elementHolder;
        var eW, eH, nW, nH;
        var results = [];
        results['result'] = [];
        switch ( ui.draggable.children().attr( 'class' ) ) {
            case 'colorBar image':
                //2013.02.12
                if ( isPuzzle && $editor.find( 'img' ).length > 0 ) return false;
                eW = 160;
                eH = 120;
                nW = parseInt( eW / editingAreaWidth * 100 );
                nH = parseInt( eH / editingAreaHeight * 100 );
//console.log( ui.draggable.attr('data-mediaurl') );
                results['result'].push( {
                    'mediaurl': ui.draggable.attr( 'data-mediaurl' ),
                    'left'    : posX + '%',
                    'top'     : posY + '%',
                    'width'   : nW + '%',
                    'height'  : nH + '%'//
                } );
                var result = tmpl( "tmpl-eaImage", results );
//console.log( 'image dropped to editor');
                $( this ).append( $( result ) );
                //$editor.append( $(result) );
                break;
            case 'colorBar audio':
                if ( isPuzzle ) return false;
                eW = 250;
                eH = 30;
                nW = parseInt( eW / editingAreaWidth * 100 );
                nH = parseInt( eH / editingAreaHeight * 100 );
                results['result'].push( {
                    'mediaurl': ui.draggable.attr( 'data-mediaurl' ).split( '.mp3' )[0],
                    'left'    : posX + '%',
                    'top'     : posY + '%',
                    'width'   : nW + '%',
                    'height'  : nH + '%'
                } );
                var result = tmpl( "tmpl-eaLAudio", results );
                $editor.append( $( result ) );
                break;
            case 'colorBar video':
                if ( isPuzzle ) return false;
                eW = 200;
                eH = 112;
                nW = parseInt( eW / editingAreaWidth * 100 );
                nH = parseInt( eH / editingAreaHeight * 100 );
                var tmplName, mediaurl, poster, fname, daid, oname;
                switch ( ui.draggable.attr( 'data-mediatype' ) ) {
                    case 'local':
                        eW = ui.draggable.attr( 'data-video-width' );
                        eH = ui.draggable.attr( 'data-video-height' );
                        nW = parseInt( eW / editingAreaWidth * 100 );
                        nH = parseInt( eH / editingAreaHeight * 100 );
                        mediaurl = ui.draggable.attr( 'data-mediaurl' ).split( '.mp4' )[0];
                        poster = ui.draggable.find( 'img' ).attr( 'src' );//attr('data-poster');
                        tmplName = 'tmpl-eaLVideo';
                        fname = ui.draggable.attr( 'isotope-data-name' ).split( '.mp4' )[0];
                        daid = $( '#themeList li.selected' ).attr( 'data-diskarea-id' );
                        oname = $( '#saveslidesform input[name="office_nametag"]' ).val();
                        break;
                    case 'remote':
                        var video = testUrlForMedia( ui.draggable.attr( 'data-mediaurl' ) );
                        mediaurl = 'http://www.youtube.com/embed/' + video.id + '?fs=1&feature=oembed&enablejsapi=1&html5=1';
                        poster = 'http://img.youtube.com/vi/' + video.id + 'hqdefault.jpg';
                        //<img alt="" src="http://img.youtube.com/vi/UBIgwohOddk/hqdefault.jpg">
                        tmplName = 'tmpl-eaRVideo';
                        break;
                }
                results['result'].push( {
                    'mediaurl': mediaurl,
                    'fname'   : fname,
                    'poster'  : poster,
                    'left'    : posX + '%',
                    'top'     : posY + '%',
                    'width'   : nW + '%',
                    'height'  : nH + '%'
                } );
//console.log( results );
                var result = tmpl( tmplName, results );
                $editor.append( $( result ) );
                break;
            case 'btn btn-dark':
                //groupping template
                if ( isPuzzle ) return false;
                var element, group = ui.helper.clone();
                var parentOffset = $editor.offset(),
                    posX = e.pageX - parentOffset.left,
                    posY = e.pageY - parentOffset.top;
                var eW, eH, nW, nH;
                posX = parseInt( posX / editingAreaWidth * 100 );
                posY = parseInt( posY / editingAreaHeight * 100 );
                eW = 200;
                eH = 120;
                if ( group.hasClass( 'group' ) ) {
                    element = group.find( 'div.ResizableClass' );
                }
                if ( element.hasClass( 'ResizableClass' ) ) {
                }
                ////console.log( 'formdrop groupping element' );
                ////console.log( element.html() );
                nW = parseInt( eW / editingAreaWidth * 100 );
                nH = parseInt( eH / editingAreaHeight * 100 );
                element.css( {
                    'position': 'absolute',
                    'left'    : posX + '%',
                    'top'     : posY + '%',
                    'width'   : nW + '%',
                    'height'  : 'auto'
                } );
                var timeStamp = new Date().getTime(),
                    iId = 'myInstance_' + timeStamp;
                element.find( '.sortableForm' ).attr( 'id', 'group_' + createUID() );
                element.find( '.textDiv' ).attr( 'id', iId );
                $editor.append( element );
                editor.addInstance( iId ).floatingPanel();

                break;
        }
        //$editor.find('video').skVideo();
        //$('#editorsMediaBox li').draggable('destroy');
        addUIevents();
        //$editor.droppable('destroy');
        slideSaved = false;
        slideShowSaved = checkFormState( 'saveslidesform' );
        checkSlideState();

    },
    stop       : function ( e, ui ) {
        var posX = parseInt( $( this ).offset().left / editingAreaWidth * 100 );
        var posY = parseInt( $( this ).offset().top / editingAreaWidth * 100 );
        var eW = $( this ).width(), eH = $( this ).height();
        var nW = parseInt( eW / editingAreaWidth * 100 );
        var nH = parseInt( eH / editingAreaHeight * 100 );
        if ( $( this ).hasClass( 'textClass' ) )
            $( this ).css( 'height', 'auto' );
        $( this ).css( {'left': posX + '%', 'top': posY + '%', 'width': nW + '%'} );


    }
};

//daraggable editor elements
var editorElementdraggable = {
    handle     : 'div.movingBox',
    containment: "#editorArea",
    drag       : function ( e, ui ) {
        removeToolsEvent();
        //var posX = parseInt($(this).position().left/editingAreaWidth*100);
        //var posY = parseInt($(this).position().top/editingAreaWidth*100);
        //var eW = $(this).width(), eH=$(this).height();
        //var nW=parseInt(eW/editingAreaWidth*100);
        //var nH=parseInt(eH/editingAreaHeight*100);
        //$(this).css({'left':posX+'%', 'top':posY+'%', 'width':nW+'%', 'height':nH+'%'});
////console.log( 'positionLeft: '+$(this).position().left+' editingAreaWidth: '+editingAreaWidth+' percent: '+$(this).position().left/(editingAreaWidth-20)*100);
    },
    stop       : function ( e, ui ) {
////console.log( $(this).position().left/$(this).parent().outerWidth()*100 );
        var posX = $( this ).position().left / editingAreaWidth * 100;//parseInt();
        var posY = $( this ).position().top / editingAreaHeight * 100;//parseInt();
        var eW = $( this ).outerWidth(), eH = $( this ).outerHeight();
        var nW = parseInt( eW / editingAreaWidth * 100 );
        var nH = parseInt( eH / editingAreaHeight * 100 );
        $( this ).css( {'left': posX + '%', 'top': posY + '%', 'width': nW + '%'} );
//console.log('editor element drag stopped');
        slideSaved = false;
        slideShowSaved = checkFormState( 'saveslidesform' );
        checkSlideState();
        addToolsEvent( $editor.attr( 'data-template-type' ) );
    }
};
/**/
//drop item to attachment list
var attachDropOption = {
    tolerance  : 'touch',
    //accept: ".word, .excel, .pdf",
    activeClass: "ui-state-highlight",
    hoverClass : "ui-state-active",
    greedy     : true,
    drop       : function ( e, ui ) {
        var element = ui.draggable.clone();
        if ( !checkFormState( 'saveslidesform' ) ) {
            slideSaved = false;
//console.log( 'triggered' );
            triggerSave = true;
            $( '#saveSlideshow' ).trigger( 'click' );
            return false;
        }
        $( this ).append( element );
        $attach.sortable( sortableAttach );
        var data = [];
        $attach.find( 'li' ).each( function ( i, e ) {
            data.push( $( e ).attr( 'data-id' ) );
//console.log( data );
        } );
        var response = se_handelAttach( data, $( '#saveslidesform' ).serializeArray(), 'save' );
        if ( !response.error ) {
            sendMessage( 'alert-success', response.message ); //response.message
        } else {
            sendMessage( 'alert-error', response.error ); //response.message
            element.remove();
        }
    }
};

$.extend( $.ui.resizable.prototype, (function ( orig ) {
    return {
        _mouseStart: function ( event ) {
            this._aspectRatio = !!(this.options.aspectRatio);
            return(orig.call( this, event ));
        }
    };
})( $.ui.resizable.prototype["_mouseStart"] ) );

//resizable
var resizableOptions = {
    helper: "ui-resizable-helper",
    ghost : true,
    //"aspectRatio": true,
    stop  : function ( event, ui ) {
        var posX = $( this ).position().left / editingAreaWidth * 100;//parseInt();
        var posY = $( this ).position().top / editingAreaHeight * 100;//parseInt();
        var eW = $( this ).outerWidth(), eH = $( this ).outerHeight();
        var nW = parseInt( eW / editingAreaWidth * 100 );
        var nH = parseInt( eH / editingAreaHeight * 100 );
        $( this ).css( {'left': posX + '%', 'top': posY + '%', 'width': nW + '%', 'height': nH + '%'} );
        if ( $( this ).hasClass( 'textClass' ) )
            $( this ).css( 'height', 'auto' );
        slideSaved = false;
        slideShowSaved = checkFormState( 'saveslidesform' );
        var iframe = $( this ).find( 'iframe' );
        iframe.css( 'pointer-events', 'none' );
    },
    start : function ( event, ui ) {
        var iframe = $( this ).find( 'iframe' );
        iframe.css( 'pointer-events', 'none' );
    },
    resize: function ( e, ui ) {

        var ed = document.getElementById( 'editorArea' ),
            ce = document.getElementById( 'ContentEditor' ),
            eWidth = ed.offsetWidth,
            eHeight = ed.offsetHeight,
            uiposLeft = ui.position.left - ce.offsetLeft - ed.offsetLeft,
            uiposTop = ui.position.top - ce.offsetTop - ed.offsetTop,
            maxHeight = eHeight - uiposTop - 1,
            maxWidth = eWidth - uiposLeft - 1;
        $( '#editorArea .' + resizableClass ).resizable( "option", "maxWidth", maxWidth );
        $( '#editorArea .' + resizableClass ).resizable( "option", "maxHeight", maxHeight );

        //$('#editorArea .'+resizableClass).css('height','auto');
        slideSaved = false;
        checkSlideState();
    }
};


var resizableVideoOptions = {
    helper       : "ui-resizable-helper",
    ghost        : true,
    "aspectRatio": true,
    start        : function ( event, ui ) {
        var iframe = $( this ).find( 'iframe' );
        iframe.css( 'pointer-events', 'none' );
    },
    stop         : function ( event, ui ) {
        var posX = $( this ).position().left / editingAreaWidth * 100;//parseInt();
        var posY = $( this ).position().top / editingAreaHeight * 100;//parseInt();
        var eW = $( this ).outerWidth(), eH = $( this ).outerHeight();
        var nW = parseInt( eW / editingAreaWidth * 100 );
        var nH = parseInt( eH / editingAreaHeight * 100 );
        $( this ).css( {'left': posX + '%', 'top': posY + '%', 'width': nW + '%', 'height': nH + '%'} );
        var iframe = $( this ).find( 'iframe' );
        iframe.attr( 'src', iframe.attr( 'src' ).replace( /w=(.*?)&h=/g, 'w=' + nW + '&h=' ) );
        iframe.css( 'pointer-events', '' );
        console.log( iframe.attr( 'src' ).replace( /w=(.*?)&h=/g, 'w=' + nW + '&h=' ) )
        slideSaved = false;
        slideShowSaved = checkFormState( 'saveslidesform' );
        if ( $( this ).hasClass( 'textClass' ) ) {
            $( this ).css( 'height', 'auto' );
        }
    },

    resize: function ( e, ui ) {

        var ed = document.getElementById( 'editorArea' ),
            ce = document.getElementById( 'ContentEditor' ),
            eWidth = ed.offsetWidth,
            eHeight = ed.offsetHeight,
            uiposLeft = ui.position.left - ce.offsetLeft - ed.offsetLeft,
            uiposTop = ui.position.top - ce.offsetTop - ed.offsetTop,
            maxHeight = eHeight - uiposTop - 1,
            maxWidth = eWidth - uiposLeft - 1;
        $( '#editorArea .' + resizableClass ).resizable( "option", "maxWidth", maxWidth );
        $( '#editorArea .' + resizableClass ).resizable( "option", "maxHeight", maxHeight );

        //$('#editorArea .'+resizableClass).css('height','auto');
        slideSaved = false;
        checkSlideState();
    }
};


//sortableSlides
var sortableSlides = {
    handle              : '.rightSide',
    items               : 'li.slideElement',
    toleranceElement    : '> div',
    placeholder         : 'sortablePlaceholder',
    forcePlaceholderSize: true,
    isTree              : true,
    maxLevels           : 1,
    //grid: [ 20, 10 ],
    update              : function ( e, ui ) {
        $( '#slidesList > li.slideElement' ).each( function ( i, e ) {
            $( e ).find( '.badge.nr' ).text( i + 1 );
            updateSlidesIDarray( $( e ).attr( 'id' ), (i + 1), 'new' );
            refreshslidesIDinTrafficData( $( e ).attr( 'id' ) );
        } );
        var templateSlide = $( '#slidesList li[data-slide-type="template"]' ).find( 'div.dataHolder.selected' );

        if ( templateSlide.length == 1 ) {
            var data = getTrafficData( templateSlide.parent() );
            var html = tmpl( "tmpl-trafficdata", data );
            $( html ).appendTo( $( '.trafficDataHolder' ) );
        }
        storeslideIDs();

        //update slides parent, left, right data
        var arraied = $slides.nestedSortable( 'toArray', {nestedType: 'slide', startDepthCount: 0, slideShowId: $( '#saveslidesform [name="id"]' ).val()} );
        console.log( arraied );
        var response = se_handelSlide( '', $( '#saveslidesform' ).serializeArray(), 'sort', arraied );
        if ( !response.error )
            sendMessage( 'alert-success', response.message ); //response.message
        else
            sendMessage( 'alert-error', response.error ); //response.message
    },
    stop                : function ( e, ui ) {
        if ( ui.position.left < -70 ) {
            var bodytext, isInTraffic = false;
            var slideId = $( ui.item ).attr( 'id' );
            isInTraffic = searchslidesIDinTrafficData( slideId );
            isInTraffic == false ? bodytext = 'Do you really want to delete this Slide?' : bodytext = 'Do you really want to delete this Slide?<br />Template slide is pointing to it!';
////console.log( 'torles ' + searchslidesIDinTrafficData( slideId ) );

            $( "#confirmDiv" ).confirmModal( {
                heading : 'Alert',
                body    : bodytext,
                text    : 'Remove',
                type    : 'question',
                callback: function () {
                    var arraied = $slides.nestedSortable( 'toArray', {nestedType: 'slide', startDepthCount: 0, slideShowId: $( '#saveslidesform [name="id"]' ).val()} );
                    var response = se_handelSlide( slideId, $( '#saveslidesform' ).serializeArray(), 'delete', arraied );
                    if ( !response.error ) {
////console.log( $(ui.item).find('.dataHolder.selected').length );
                        if ( $( ui.item ).find( '.dataHolder.selected' ).length == 1 )
                            se_clearData( $( ui.item ).attr( 'data-slide-type' ) );
////console.log('ui slidetype');
////console.log( $(ui.item).attr('data-slide-type') );
                        $( ui.item ).remove();
                        sendMessage( 'alert-success', response.message ); //response.message

                        deleteslidesIDinTrafficData( slideId );
                        storeslideIDs();

                        $( '#slidesList > li.slideElement' ).each( function ( i, e ) {
                            $( e ).find( '.badge.nr' ).text( i + 1 );
                            updateSlidesIDarray( $( e ).attr( 'id' ), (i + 1), 'new' );
                            refreshslidesIDinTrafficData( $( e ).attr( 'id' ) );
                        } );
                        var arraied = $slides.nestedSortable( 'toArray', {nestedType: 'slide', startDepthCount: 0, slideShowId: $( '#saveslidesform [name="id"]' ).val()} );
                        var response = se_handelSlide( '', $( '#saveslidesform' ).serializeArray(), 'sort', arraied );
                        if ( !response.error )
                            sendMessage( 'alert-success', response.message ); //response.message
                        else
                            sendMessage( 'alert-error', response.error ); //response.message
                    } else {
                        sendMessage( 'alert-error', response.error ); //response.message
                    }
                    $slides.nestedSortable( sortableSlides );
                }
            } );
////console.log( 'sorting out pozicio ell volt a torleshez' );
        }

//recalculateScrollHeight( $('#slidesList') );
////console.log( 'sorting out' );
////console.log( slideIDs );
        //save slides parent, left, right data

    }
};

//2012.12.11.
//sortable attach
var sortableAttach = {
    receive: function ( event, ui ) {
        //update slidshows attachment data

    },
    stop   : function ( e, ui ) {
        if ( ui.position.left < -70 ) {
//console.log(ui.position.left);
            $( ui.item ).remove();
            var data = [];
            $attach.find( 'li' ).each( function ( i, e ) {
                data.push( $( e ).attr( 'data-id' ) );
//console.log( data );
            } );
            var response = se_handelAttach( data, $( '#saveslidesform' ).serializeArray(), 'save' );
            if ( !response.error ) {
                sendMessage( 'alert-success', response.message ); //response.message
            } else {
                sendMessage( 'alert-error', response.error ); //response.message
            }
        }

        //update slideshows attachment data
    }
};

var accordionOption = {
    header     : "span.pull-left",
    heightStyle: "content",
    collapsible: true,
    icons      : null
};
////////////////////////////////////////////////////////////////////////////////
//template editing section
//1. DnD form element and delete from editorarea with drop
var toolsDrag = {
    appendTo: 'body',
    handle  : 'li',
    revert  : 'true',
    helper  : 'clone',
    start   : function ( e, ui ) {
        $( '#editorArea .sortableForm' )
            .droppable( formDrop )
            .sortable( formSortable );
        $( ui.helper ).find( '#addOptionRow' ).remove();
    },
    stop    : function ( e, ui ) {
        $( '#editorArea .sortableForm' )
            .droppable( 'destroy' );
        //.sortable('destroy');
        //slideSaved = false;
        //slideShowSaved = checkFormState('saveslidesform');
    }
};
var toolsDrag2 = {
    appendTo: 'body',
    handle  : 'li',
    revert  : 'true',
    helper  : 'clone',
    start   : function ( e, ui ) {
        console.log( 'toolsDrag2 fired' );
        prevOffset = true;
        $editor.droppable( editorDropOption );
    },
    stop    : function ( e, ui ) {
        /*
         var element, group = ui.helper.clone();
         var parentOffset = $editor.offset(),
         posX = e.pageX - parentOffset.left,
         posY = e.pageY - parentOffset.top;
         var eW, eH, nW, nH;
         posX = parseInt(posX / editingAreaWidth*100);
         posY = parseInt(posY / editingAreaHeight*100);
         eW=200; eH=120;
         if ($(this).hasClass('group') ){
         element = group.find('div.ResizableClass');
         }
         if (element.hasClass('ResizableClass')){}
         ////console.log( 'formdrop groupping element' );
         ////console.log( element.html() );
         nW=parseInt(eW/editingAreaWidth*100);
         nH=parseInt(eH/editingAreaHeight*100);
         element.css({
         'position':'absolute',
         'left': posX+'%',
         'top':posY+'%',
         'width':nW+'%',
         'height':'auto'
         });
         var timeStamp = new Date().getTime(),
         iId = 'myInstance_'+timeStamp;
         element.find('.textDiv').attr('id', iId);
         $editor.append( element );
         editor.addInstance(iId).floatingPanel();
         */
//console.log( 'toolsDrag2 ended' );
        $editor.droppable( 'destroy' );
        addUIevents();
        addToolsEvent( 'groupping' );
    },
    drag    : function ( e, ui ) {
        console.log( 'toolsDrag2' );
        var element = ui.helper;
        console.log( element.offset().top );
    }
};

var formDrop = {
    tolerance  : 'touch',
    //accept: ".word, .excel, .pdf",
    activeClass: "ui-state-highlight",
    hoverClass : "ui-state-active",
    greedy     : true,
    drop       : function ( e, ui ) {

        var element = ui.draggable.clone();
        var uId = createUID2();
        element.attr( 'class', 'holder' );
        element.find( '.btn-dark' ).removeClass( 'btn' );
        element.find(':input').attr('id', uId);
        element.find( 'label' ).attr('for', uId);
        var templateType = $( '#editorArea' ).attr( 'data-template-type' );
        alert(templateType)
        
        switch ( templateType ) {
            case 'radio':
            case 'check':
            var uId = 
                $( this ).append( element );
                var inputElements = $( this ).find( ':input' );
                
                var timeStamp = new Date().getTime(),
                    iId = 'myInstance_' + timeStamp;
                element.find( '.textDiv' ).attr( 'id', iId );

                //editor.addInstance(iId).floatingPanel();
                createEditorInstance( iId, true );

                for ( var i = 0, e; e = inputElements[i]; i++ )
                    e.value = i;
                $( '#editorArea .sortableForm' ).sortable( formSortable );
                break;
            case 'input':
                var inpElement = element;
                element.find( 'span.btn-dark' ).remove();
//console.log('input template ');
//console.log( $('#editorArea .sortableForm').attr('id') );
                if ( !elementContainsSelection( document.getElementById( $( '#editorArea .sortableForm' ).attr( 'id' ) ) ) ) return false;
//don't remove
//console.log( 'added to form ' + inpElement.find('span').remove().html() );
                pasteHtmlAtCaret( inpElement.html() );

                var inputElements = $( '#editorArea .sortableForm' ).find( ':input' );
//console.log('inputok szama: '+ inputElements.length);
                for ( var i = 0, e; e = inputElements[i]; i++ ) {
                    if ( e.nodeName == 'INPUT' )
                        e.name = 'inp' + i;
                    else if ( e.nodeName == 'SELECT' )
                        e.name = 'sel' + i;
                }
                break;
            case 'sorting':
            case 'groupping':
            case 'pairing':
                var timeStamp = new Date().getTime(),
                    iId = 'myInstance_' + timeStamp;
                element.attr( 'id', 'sort_' + createUID() );
                console.log( element );
                element.find( '.textDiv' ).attr( 'id', iId );
                $( this ).append( element );
                editor.addInstance( iId ).floatingPanel();
                $( '#editorArea .sortableForm' ).sortable( formSortable );
                break;

        }
        slideSaved = false;
        slideShowSaved = checkFormState( 'saveslidesform' );
        checkSlideState();
    }
};

var formSortable = {
    cancel              : ':input,button,.contenteditable',
    placeholder         : "ui-state-highlight",
    handle              : "span.btn-dark",
    forcePlaceholderSize: true,
    connectWidth        : '#editorArea .sortableForm',
    start               : function ( e, ui ) {
        removeUIevents();
        removeToolsEvent();
    },
    stop                : function ( e, ui ) {
        slideSaved = false;
        slideShowSaved = checkFormState( 'saveslidesform' );
        checkSlideState();
        if ( $editor.attr( 'data-template-type' ) !== 'normal' ) addToolsEvent( $editor.attr( 'data-template-type' ) );
        addUIevents();
    },
    out                 : function ( e, ui ) {
        if ( ui.position.left < -70 ) {
////console.log(ui.position.left);
            $( ui.item ).remove();
            //$('#editorArea .sortableForm').sortable(formSortable);
            $( '#editorArea .sortableForm li' ).sortable( formSortable );
            slideSaved = false;
        }
        checkSlideState();
    }
};

//drop image,video, audio elements
var prevOffset = true,
    curOff = false;

var templateDropOption = {
    tolerance  : 'pointer',
    accept     : ".image, .video, .audio",
    activeClass: "ui-state-highlight",
    hoverClass : "ui-state-active",
    //greedy: true,
    //iframeFix: true,
    drop       : function ( e, ui ) {
//console.log( 'templateDropOption fired' );
        var parentOffset = $( this ).offset();
        //or $(this).offset(); if you really just want the current element's offset
        var posX = e.pageX - parentOffset.left;
        var posY = e.pageY - parentOffset.top;
//console.log( $(this).attr('class') );
        posX = parseInt( posX / editingAreaWidth * 100 );
        posY = parseInt( posY / editingAreaHeight * 100 );
////console.log( 'dropped' );
        //var $elementHolder;
        var eW, eH, nW, nH;
        var results = [];
        results['result'] = [];
        switch ( ui.draggable.children().attr( 'class' ) ) {
            case 'colorBar image':
                eW = 160;
                eH = 120;
                nW = parseInt( eW / editingAreaWidth * 100 );
                nH = parseInt( eH / editingAreaHeight * 100 );
                results['result'].push( {
                    'mediaurl': ui.draggable.attr( 'data-mediaurl' ),
                    'left'    : '0%',
                    'top'     : '0%',
                    'width'   : '100%',
                    'height'  : 'auto'//'100%'
                } );
                //var result = tmpl("tmpl-eaImage", results);
                var element = $( '<div style="left:0%;top:0%; width:20%; height:2%"><img src="' + ui.draggable.clone().attr( 'data-mediaurl' ) + '" /></div>' );
                if ( !elementContainsSelection( document.getElementById( $( this ).attr( 'id' ) ) ) ) return false;
//don't remove
//console.log( 'added to form ' + $(this).attr('id') );
                pasteHtmlAtCaret( element.html() );

//console.log('textDiv');
                $( this ).append( $( result ) );
                prevOffset = false;
                break;
            case 'colorBar audio':
                eW = 250;
                eH = 30;
                nW = parseInt( eW / editingAreaWidth * 100 );
                nH = parseInt( eH / editingAreaHeight * 100 );
                results['result'].push( {
                    'mediaurl': ui.draggable.attr( 'data-mediaurl' ).split( '.mp3' )[0],
                    'left'    : posX + '%',
                    'top'     : posY + '%',
                    'width'   : nW + '%',
                    'height'  : nH + '%'
                } );
                var result = tmpl( "tmpl-eaLAudio", results );
                //$editor.append( $(result) );
                break;
            case 'colorBar video':
                eW = 320;
                eH = 240;
                nW = parseInt( eW / editingAreaWidth * 100 );
                nH = parseInt( eH / editingAreaHeight * 100 );
                var tmplName, mediaurl, poster;
                switch ( ui.draggable.attr( 'data-mediatype' ) ) {
                    case 'local':
                        mediaurl = ui.draggable.attr( 'data-mediaurl' ).split( '.mp4' )[0];
                        poster = ui.draggable.find( 'img' ).attr( 'src' );//attr('data-poster');
                        tmplName = 'tmpl-eaLVideo';
                        break;
                    case 'remote':
                        var video = testUrlForMedia( ui.draggable.attr( 'data-mediaurl' ) );
                        mediaurl = 'http://www.youtube.com/embed/' + video.id + '/?fs=1&feature=oembed';
                        tmplName = 'tmpl-eaRVideo';
                        break;
                }
                results['result'].push( {
                    'mediaurl': mediaurl,
                    'poster'  : poster,
                    'left'    : posX + '%',
                    'top'     : posY + '%',
                    'width'   : nW + '%',
                    'height'  : nH + '%'
                } );
//console.log( results );
                var result = tmpl( tmplName, results );
                //$editor.append( $(result) );
                break;
        }
        //$('#editorsMediaBox li').draggable('destroy');
        //addUIevents();
        //$editor.droppable('destroy');
        slideSaved = false;
        slideShowSaved = checkFormState( 'saveslidesform' );
        checkSlideState();

    },
    stop       : function ( e, ui ) {
        var posX = parseInt( $( this ).offset().left / editingAreaWidth * 100 );
        var posY = parseInt( $( this ).offset().top / editingAreaWidth * 100 );
        var eW = $( this ).width(), eH = $( this ).height();
        var nW = parseInt( eW / editingAreaWidth * 100 );
        var nH = parseInt( eH / editingAreaHeight * 100 );
        if ( $( this ).hasClass( 'textClass' ) )
        //$(this).css('height','auto');
            $( this ).css( {'left': posX + '%', 'top': posY + '%', 'width': nW + '%', 'height': nH + '%'} );
    },
    out        : function ( event, ui ) {
//console.log( 'drop failde' );
    }


};
var drop;
var dragItems;
////////////////////////////////////////////
(function ( $ ) {

////////////////////////////////////////////
    serializeForm = function ( container, templatetype ) {
        console.log( 'serializeForm templatetype' );
        console.log( templatetype );
        var returnData = [];
        returnData['answare'] = [];
        returnData['html2'] = [];
        //step1 serilaize form
        switch ( templatetype ) {
            case 'normal':
                returnData.answare = '';
                break;
            case 'radio' :
            case 'check' :
            case 'input' :
                returnData.answare = JSON.stringify( $( '#editorAreaForm' ).serializeArray() );
                break;
            case 'sorting':
            case 'groupping':
            case 'pairing' :
                sortableForms = container.find( 'ul.sortableForm:not([id="group1"])' );
                var array = [];
                $.each( sortableForms, function () {
                    array.push( {
                        'group': $( this ).attr( 'id' ),
                        'order': $( this ).sortable().sortable( 'serialize' )
                    } );
                } );
                returnData['answare'] = JSON.stringify( array );
                break;
        }
        var groups = container.find( '.sortableForm:not([id="group1"])' );
//console.log('groups');
//console.log( groups.length );

        //step2 sort order
        $.each( groups, function () {
            var groupId = $( this ).attr( 'id' );
            //originalSort = JSON.stringify(originalLiElements = $('#'+groupId+' li', container));

            $( '#' + groupId + ' li', container ).tsort( { order: 'rand' } );

            /*
             //csak ha min.2 elem van kevergetunk
             if (originalLiElements && originalLiElements.length>1)
             {
             //addig keverjuk, amig ugynazt dobja ki
             do
             {
             randomSort = JSON.stringify($('#'+groupId+' li', container).tsort({ order:'rand' }));
             }
             while(randomSort == originalSort);
             }
             */
        } );

        //step3 purge html
        var html2 = purgeHtml( container, templatetype );
        returnData.html2 = html2;
        return returnData;
    };
////////////////////////////////////////////
// functions for test slides
////////////////////////////////////////////
    addToolsEvent = function ( templateType ) {
//console.log( 'addToolsEvent fired' );
////console.log( 'toolsevent: '+ $('[data-slide-type="template"] > .selected').length );
        //if($('#slidesList [data-slide-type="template"] .selected').length == 0) $('.tools').html('');

        var $elem = $( '#editorArea .sortableForm li:data(sortable)' );

//console.log( 'van sortable? : '+$elem.length );
        var uId = createUID2();
        switch ( templateType ) {
            case 'radio':
                
                $( '.tools' ).html( '<li class="holder"><span class="btn btn-dark"><span class="icon-move"></span></span><input type="radio" name="options[]" value="0" class="regular-radio big-radio" id="'+uId+'"><label for="'+uId+'"></label><div class="textDiv" contenteditable="true">Option one is this and that</div></li>' );
                $( '#editorArea .sortableForm' ).droppable( formDrop );
                $( '#editorArea .sortableForm li' ).sortable( formSortable );
                $( '.tools li' ).draggable( toolsDrag );
                break;
            case 'check':
                $( '.tools' ).html( '<li class="holder"><span class="btn btn-dark"><span class="icon-move"></span></span><input type="checkbox" name="chk[]" value="0" class="regular-checkbox big-checkbox" id="'+uId+'"><label for="'+uId+'"></label><div class="textDiv" contenteditable="true">Option one is this and that</div></li>' );
                $( '#editorArea .sortableForm' ).droppable( formDrop );
                $( '#editorArea .sortableForm li' ).sortable( formSortable );
                $( '.tools li' ).draggable( toolsDrag );
                break;
            case 'input':
                $( '.tools' ).html(
                    '<div class="holder" draggable="true" style="display: inline-block;" contenteditable="false" id="tmpP" >' +
                        '<span class="btn btn-dark"><span class="icon-move"></span></span>' +
                        '<input type="text" name="inp" value="" class="input-small"/>' +
                        '</div>' +
                        '<div class="holder" draggable="true" style="display: inline-block;" contenteditable="false" id="tmpS">' +
                        '<span class="btn btn-dark"><span class="icon-move"></span></span>' +
                        '<select name="sel"></select>' +
                        '</div>' +
                        '<span class="btn btn-dark pull-left" id="addOptionRow"><span class="icon-plus"></span></span>' +
                        '<span class="btn btn-dark pull-left" id="refreshOption"><span class="icon-refresh"></span></span>' +
                        '<div class="pull-left optionrowArea">' +
                        //'<div class="optionrow">'+
                        //  '<input type="text" class="input-small" /><span class="btn btn-dark"><span class="icon-minus"></span></span>'+
                        //'</div>'+
                        '</div>' );
                $( '#addOptionRow' ).bind( 'click', function () {
                    $( '.optionrowArea' ).append( '<div class="optionrow">' +
                        '<input type="text" class="input-small" /><span class="btn btn-dark remove"><span class="icon-minus"></span></span>' +
                        '</div>' );
                } );
                $( '#refreshOption' ).bind( 'click', function () {
                    $( '.toolsContainerElements select' )
                        .find( 'option' )
                        .remove()
                        .end()
                        .append( '<option value="">--Select--</option>' );
                    $( '.optionrowArea input' ).each( function ( i ) {
//console.log( $(this).val() );
                        if ( $( this ).val() != '' ) {
                            $( '.toolsContainerElements select' )
                                .append( '<option value="' + i + '">' + $( this ).val() + '</option>' );
                        }
                    } )
                } );
                $( '.optionrowArea .remove' ).live( 'click', function () {
                    $( this ).parent().remove();
                } );

                $( '#editorArea input' ).live( 'keypress', function ( e ) {
//console.log( 'key pressed' );
                    var code = (e.keyCode ? e.keyCode : e.which);
                    if ( code == 13 ) return false;
//console.log($(this).val());
                    $( this ).attr( 'value', $( this ).val() );
                } );
                $( '#editorArea select' ).live( 'keyup', function () {

                } );

                $( '#editorArea select' ).live( 'change', function () {
                    // $('option:selected', 'select').removeAttr('selected');
                    //$(this).find('option').removeAttr('selected');
                    $( this ).find( 'option:selected' ).attr( 'selected', 'selected' ).siblings().removeAttr( 'selected' );
                    //console.log( $(this).val() );

                } );
                //<span class="input-small"></span>
                $( '#editorArea .sortableForm' ).droppable( formDrop );
                $( '.tools .holder' ).draggable( toolsDrag );
                $( '#editorArea .textDiv' ).focus();
                /**/
                break;
            case 'sorting' : //sorting answers
                $( '.tools' ).html( '<li class="holder"><span class="btn btn-dark"><span class="icon-move"></span></span><div class="textDiv" contenteditable="true">sortable element</div></li>' );
                $( '#editorArea .sortableForm' ).droppable( formDrop );
                $( '#editorArea .sortableForm li' ).sortable( formSortable );
                $( '.tools li' ).draggable( toolsDrag );
                break;
            case 'groupping': //groups and memebers n sortable list with connectTo
                $( '.tools' ).html( '<li class="holder group"><span class="btn btn-dark"><span class="icon-move"></span></span>' +
                    '<div class="ResizableClass textClass isSelected" style=""><div class="textDiv header">New group</div><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><ul class="sortableForm ui-sortable ui-droppable"></ul></div>' +
                    '</li>' );
            case 'pairing':
                $( '.tools' ).append( '<li class="holder"><span class="btn btn-dark"><span class="icon-move"></span></span><div class="textDiv" contenteditable="true">sortable element</div></li>' );

                $( '#editorArea .sortableForm' ).droppable( formDrop );
                $( '#editorArea .sortableForm li' ).sortable( formSortable );
                $( '.tools li:not(.holder.group)' ).draggable( toolsDrag );
                $( '.tools li.holder.group' ).draggable( toolsDrag2 );
                $( '#editorArea .sortableHolder' ).draggable( editorElementdraggable );
                break;
            case 'puzzle':
                //$('#editorsMediaBox li').draggable(myMediaDragOption);
//console.log( 'puzzle' );

                break;
            case '29p_1':
            case '29p_2':
                var inputElements = $editor.find( ':input' );
//console.log('inputok szama: '+ inputElements.length);
                for ( var i = 0, e; e = inputElements[i]; i++ ) {
                    if ( e.nodeName == 'INPUT' )
                        e.name = 'inp' + i;
                    else if ( e.nodeName == 'SELECT' )
                        e.name = 'sel[]';//+i;
                }
                break;
        }

        if ( $( '.trafficDataHolder .control-group' ).length == 0 ) {
            var data = [];
            data['result'] = [];
            switch ( templateType ) {
                case '29p_1':
                    data['result'].push( {'prev': '', 'prevID': '', 'score': 4, 'next': '', 'nextID': ''} );
                    break;
                case '29p_2':
                    data['result'].push( {'prev': '', 'prevID': '', 'score': 129, 'next': '', 'nextID': ''} );
                    data['result'].push( {'prev': '', 'prevID': '', 'score': 155, 'next': '', 'nextID': ''} );
                    data['result'].push( {'prev': '', 'prevID': '', 'score': 181, 'next': '', 'nextID': ''} );
                    data['result'].push( {'prev': '', 'prevID': '', 'score': 207, 'next': '', 'nextID': ''} );
                    data['result'].push( {'prev': '', 'prevID': '', 'score': 233, 'next': '', 'nextID': ''} );
                    data['result'].push( {'prev': '', 'prevID': '', 'score': 259, 'next': '', 'nextID': ''} );
                    break;
                case '29p_3':
                    data['result'].push( {'prev': '', 'prevID': '', 'score': 0, 'next': '', 'nextID': ''} );
                    break;
                case 'sorting':
                    data['result'].push( {'prev': '', 'prevID': '', 'score': 60, 'next': '', 'nextID': ''} );
                case 'radio':
                case 'check':
                case 'input':
                case 'groupping':
                case 'pairing':
                case 'puzzle':
                    data['result'].push( {'prev': '', 'prevID': '', 'score': 100, 'next': '', 'nextID': ''} );
                    break;
            }
            var html = tmpl( "tmpl-trafficdata", data );
            $( html ).appendTo( $( '.trafficDataHolder' ) );

            /*    var data = [],
             arr =['sorting'];
             data['result'] = [];
             data['result'].push({'prev': '','prevID': '','score': 100,'next': '','nextID': ''});
             //templateType !=='radio'
             if($.inArray(templateType, arr) !== -1) 
             data['result'].push({'prev': '','prevID': '','score': 60,'next': '','nextID': ''});
             var html = tmpl("tmpl-trafficdata", data);
             $(html).appendTo( $('.trafficDataHolder') );*/
        }

    };

////////////////////////////////////////////
    removeToolsEvent = function () {
//console.log( 'removeToolsEvent fired' );
        var $elem = $( '#editorArea .sortableForm:data(droppable)' );
        if ( $elem.length )
            $( '#editorArea .sortableForm' ).droppable( 'destroy' );
        var $elem = $( '#editorArea .sortableForm li:data(sortable)' );
        if ( $elem.length )
        //$('#editorArea .sortableForm li').sortable('destroy');
            var $elem = $( '.tools li:data(draggable)' );
        if ( $elem.length )
            $( '.tools li' ).draggable( 'destroy' );
    };

////////////////////////////////////////////
// check slideshow saved state
// check on slide is anithyng modified
// set state unsaved
////////////////////////////////////////////

    checkFormState = function ( formID ) {
//console.log( 'checkFormState fired' );
        var state = ($( '#' + formID + ' input[name="id"]' ).val() == '') ? false : true;
        //var sl = ($slides.find('li').length == 0) ? true : false;
        //if (state == sl) return true;
////console.log( 'checkFormState' );
////console.log( state );
        (state === false) ? $( '#saveSlideshow' ).removeClass( 'btn-dark' ).addClass( 'btn-danger' ) : $( '#saveSlideshow' ).addClass( 'btn-dark' ).removeClass( 'btn-danger' );
        return state;
        //sendMessage('alert-warning', 'Your slidshow is saved. U can rename it!');
    }

    checkSlideState = function () {
        var state = (slideSaved) ? true : false;
        //var sl = ($slides.find('li').length == 0) ? true : false;
        //if (state == sl) return true;
//console.log( 'checkSlideState fired' );
//console.log( state );

        (!state) ? $( '#saveSlide' ).removeClass( 'btn-dark' ).addClass( 'btn-danger' ) : $( '#saveSlide' ).addClass( 'btn-dark' ).removeClass( 'btn-danger' );
        return state;
        //sendMessage('alert-warning', 'Your slidshow is saved. U can rename it!');
    }

////////////////////////////////////////////
// clear data/pointers/dataholders
// even new normal/test slides is created/loaded
////////////////////////////////////////////
    se_clearData = function ( type ) {
//console.log( 'se_clearData fired' );
        switch ( type ) {
            case 'editor':
                $editor.attr( 'data-template-type', 'normal' );
                $editor.empty();

                editor;
                $( '#slidesList' ).empty();
                $( '#bc' ).html( '' );
                $( '#myNicPanel' ).empty();
                $( '#saveslidesform input[name="id"]' ).val( '' );
                $( '#saveslidesform input[name="name"]' ).val( '' );
                templateSlide = false;
            case 'all':
                storeslideIDs();
            case 'template':
                $( '.testSlideEditorContainer' ).hide();
                $( '.trafficDataHolder, .tools' ).html( '' );
                templateSlide = false;
            case 'normal':
                //$editor.html('');
                $( '#testSlides .slideElement .rightSide' ).parent().removeClass( 'selected' );
                //nicEditors.editors= [];
                $( '#editorBottomBar .level' ).hide();
                $( '#slideTag' ).val( '' );
                triggerSave = false;
                slideShowSaved = true;
                slideSaved = true;
                $editor.empty();
                break;
        }
    };

////////////////////////////////////////////
// page init function
////////////////////////////////////////////
    se_pageInit = function () {
//console.log( 'se_pageInit fired' );
        slideShowSaved = true;
        slideSaved = true;
        triggerSave = false;
        storeslideIDs();
        //setHoverScrollHeight();
        //window resize
        editingAreaWidth = $( '#editorAreaContainer' ).width();
        editingAreaHeight = editingAreaWidth * 9 / 16;
        //$editor.css({'height':editingAreaHeight, 'width': editingAreaWidth});

        $window.resize( function () {
            viewportWidth = w.innerWidth || e.clientWidth || g.clientWidth;
            viewportHeight = w.innerHeight || e.clientHeight || g.clientHeight;
            //setHoverScrollHeight();
            setToolbarPosition();
        } );
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
         */
        //$mediaBoxContainer.hoverscroll({width:170});
        //$slides2.hoverscroll();
        //$tests.hoverscroll();
        //slideScroller = $slides.hoverscroll();
        //$attach.hoverscroll();
        $mediaBoxContainer.slimScroll( {position: 'left', height: '300px', allowPageScroll: false} );
        $slides2.slimScroll( {position: 'left', height: '300px', allowPageScroll: false} );
        //$tests.slimScroll({position: 'right',height: '350px',width:'190px',allowPageScroll: false});
        $slides.slimScroll( {position: 'left', height: '300px', allowPageScroll: false} );
        $attach.slimScroll( {position: 'left', height: '300px', allowPageScroll: false} );
        $.when( $( '#loadSlideshow' ).slimScroll( {position: 'left', height: '300px', width: '300px', allowPageScroll: false} ) )
            .done( function ( a ) {
                $( '#loadSlideShowButton > .slimScrollDiv' ).css( {'position': 'absolute', 'top': '100%', 'left': '0'} );
            } );
        /*  $slides.hover(function() {
         $("body").css("overflow","hidden");
         }, function() {
         $("body").css("overflow","");
         });*/

        //setup EditorArea size
        editingAreaWidth = $editor.width();
        editingAreaHeight = editingAreaWidth * 9 / 16;
        //$editor.css('height',editingAreaHeight);

        //setup slides sortable
        $slides.nestedSortable( sortableSlides );

        se_loadSlideShowList();
        se_loadMediaGroups();
        //se_loadMediaFiles(14);//sample data
        se_loadSlideShowList2();
        //load mediabox content
        /*
         $.when( loadMediaBox() ).done(function( resp1 ){
         //alert( resp2 );
         setTimeout(function() {
         //$editor.droppable(editorDropOption);
         }, 50);

         });
         */
        $attach.sortable().disableSelection();
        //working hoverscroll
        //$('.fixed-listcontainer').css('height',scrollContainerInnerHeight+'px');
    };

////////////////////////////////////////////
// jquery ui events on elements
////////////////////////////////////////////
    addUIevents = function () {
//console.log( 'addUIevents fired' );
        $( '#editorArea .' + nonResizableClass )
            .draggable( editorElementdraggable );
        $( '#editorArea .' + resizableClass )
            .draggable( editorElementdraggable )
        $.each( $( '#editorArea .' + resizableClass ), function () {
            if ( $( this ).hasClass( 'video' ) ) {
                $( this ).resizable( resizableVideoOptions );
            } else {
                $( this ).resizable( resizableOptions );
            }
        } );

        $editor.find( '.ui-resizable-handle' ).attr( 'style', 'z-index:1000;' );
        $slides.nestedSortable( sortableSlides );
        /*
         $editor.find('.textDiv').addEventListener("input", function() {
         checkSlideState();
         }, false);
         */

        $( '[contenteditable]' ).live( 'focus',function () {
            var $this = $( this );
//console.log( 'focus event fired' );
            $this.data( 'before', $this.html() );
            return $this;
        } ).live( 'blur keyup paste', function () {
                var $this = $( this );
                if ( $this.data( 'before' ) !== $this.html() ) {
                    $this.data( 'before', $this.html() );
                    $this.trigger( 'change' );
                    slideSaved = false;
                    checkSlideState();
//console.log( 'blur keyup paste event fired' );
                }
                return $this;
            } );
    };

    removeUIevents = function () {
//console.log( 'removeUIevents fired' );
        var $elem = $( '#editorArea .' + nonResizableClass + ':data(draggable)' );
        if ( $elem.length )
            $( '#editorArea .' + nonResizableClass ).draggable( 'destroy' );
        var $elem = $( '#editorArea .' + resizableClass + ':data(resizable)' );
        if ( $elem.length )
            $( '#editorArea .' + resizableClass ).resizable( 'destroy' );
        var $elem = $( '#editorArea .' + resizableClass + ':data(draggable)' );
        if ( $elem.length )
            $( '#editorArea .' + resizableClass ).draggable( 'destroy' );
    }

////////////////////////////////////////////
//cke floating toolbar position
////////////////////////////////////////////
    setToolbarPosition = function () {
//console.log( 'setToolbarPosition fired' );
        //getHoverScrollHeight(scrollContainerHeight);
        $( '.fixed-listcontainer' ).css( 'height', scrollContainerInnerHeight + 'px' );
        $( '.hoverscroll' ).css( 'height', scrollContainerHeight + 'px' );

        //positioning floating toolbar
        var etb = document.getElementById( 'editorTopBar' );
        var cea = document.getElementById( 'ContentEditor' );
        var newL = cea.offsetLeft + etb.offsetLeft + 100;
        var newT = cea.offsetTop + 55;

        $( '.cke.cke_float' ).css( {'left': newL + 'px', 'top': newT + 'px'} );
        $( '#xxx' ).html( 'width:' + viewportWidth + ' Height:' + viewportHeight + '  editorbar left: ' + etb.offsetLeft + '  contenteditor left: ' + cea.offsetLeft + ' editorbarTop: ' + newT );
        //

        //setupEditor
        editingAreaWidth = $( '#editorAreaContainer' ).width();
        editingAreaHeight = editingAreaWidth * 9 / 16;
        //$editor.css({'height':editingAreaHeight, 'width': editingAreaWidth});
    };

    /*
     for(key in data){
     // for-in loop goes over all properties including inherited properties
     // let's use only our own properties
     if(data.hasOwnProperty(key)){
     //console.log("key = " + key + ", value = " + data[key]);
     }
     }
     */

////////////////////////////////////////////
// functions for handel data
// load/save slideshow
// load/save/update slide
// load/save/update attachment
////////////////////////////////////////////
//load media groups
//eltavolitva, nem hasznalt fuggveny
    se_loadMediaGroups = function () {
        console.log( 'se_loadMediaGroups fired' );
        console.log( $( '#daForMediaGroups' ).attr( 'data-diskarea-id' ) );
        return $.ajax( {
            url     : "/crawl?/process/editor/loadmediagroups/",
            data    : {diskArea: $( '#daForMediaGroups' ).attr( 'data-diskarea-id' )},
            type    : 'POST',
            dataType: 'json'
        } ).done( function ( data ) {
////console.log( data );
                //tmpl-diskareas2 load only default diskAreas mediaboxes
////console.log(data);
                $( '#editorsMediaBox' ).html( '' );
                $( '#mediaBoxList' ).html( tmpl( "tmpl-mblist", data ) ); //tmpl-mblist - tmpl-diskareas
                $( '#mediaBoxList' ).prev().find( 'span.name' ).text( 'select' );

                $( '#mediaBoxList li:not(.selected) a.level2' ).bind( 'click', function ( e ) {
//console.log('level2 clicked');
                    var _this = $( this );
                    e.preventDefault();
                    e.stopPropagation();

                    _this.parent().siblings().removeClass( 'selected' );
                    _this.parent().addClass( 'selected' );
                    var folderName = $( this ).find( 'span.name' ).text();
                    folderName = folderName.length > 9 ? folderName.substring( 0, 8 ) + '...' : folderName;
                    $( '#mediaBoxList' ).prev().find( 'span.name' ).text( folderName ).attr( 'title', $( this ).find( 'span.name' ).text() );

                    //2013.01.19
                    se_loadMediaFiles( _this.attr( 'data-id' ) );
                    /*
                     $.when( se_loadMediaFiles( _this.attr('data-id') ) ).done(function( resp1 ){
                     setTimeout(function() { 
                     //$('#editorsMediaBox li').draggable(myMediaDragOption);
                     }, 50);
                     });
                     */

                    $( '.selectMenu' ).parent().removeClass( 'open' );

                } );

            } );
    };

//load mediafiles
    se_loadMediaFiles = function ( mdeiaGroupId ) {
//console.log( 'se_loadMediaFiles fired' );
        return $.ajax( {
            url     : "/crawl?/process/editor/loadmediafiles/",
            data    : {diskArea: $( '#daForMediaGroups' ).attr( 'data-diskarea-id' ), mediaGroup: mdeiaGroupId},
            type    : 'POST',
            dataType: 'json'
        } ).done( function ( data ) {
////console.log('selected mediabox files loaded');
////console.log(data);
                $mediaBoxContainer.empty();
                $mediaBoxContainer.append( tmpl( "tmpl-mediaElement", data ) );
                //ellenorizni, hogy template-e
                $( '#editorsMediaBox li' ).draggable( myMediaDragOption );

                $( '#mediaBoxList a.level2' ).bind( 'click', function ( e ) {
//console.log('level2 clicked');
                    var _this = $( this );
                    e.preventDefault();
                    e.stopPropagation();

                    _this.parent().siblings().removeClass( 'selected' );
                    _this.parent().addClass( 'selected' );
                    //2013.01.19
                    se_loadMediaFiles( _this.attr( 'data-id' ) )
                    /*
                     $.when( se_loadMediaFiles( _this.attr('data-id') ) ).done(function( resp1 ){
                     setTimeout(function() { 
                     //$('#editorsMediaBox li').draggable(myMediaDragOption);
                     }, 50);
                     });
                     */
                    $( this ).closest( '.open' ).removeClass( 'open' );
                } );
            } );
    };

//rename slideshow inline
    se_handelEditable = function () {
//console.log( 'se_handelEditable fired' );
        /*$('#ssName').editable({
         title: 'Rename slideshow',
         placement: 'bottom',
         send:'never'
         });
         $('#ssName').on('update', function(e, editable) {
         ////console.log('editable id: '+ mgId );editable.value
         var mgData = [];
         mgData.push({'name':editable.value});
         var response = handelSlideShow(mgData, $('#saveslidesform').serializeArray(), 'rename');
         if(!response.name){
         sendMessage('alert-error', response.error);
         } else {
         sendMessage('alert-success', response.message);
         $('#saveslidesform input[name="name"]').val(response.name);
         $('#loadSlideshow .selected').find('span.sname').text(editable.value);
         }
         });*/
    };

// load all slideshows from all diskareas
    se_loadSlideShowList2 = function () {
//console.log( 'se_loadSlideShowList2 fired' );
        return $.ajax( {
            url     : "/crawl?/process/editor/loadslideshows/",
            //data : {diskArea : $('#saveslidesform').find('input[name="diskArea_id"]').val()},
            type    : 'POST',
            dataType: 'json'
        } ).done( function ( data ) {
////console.log( data );
                $( '#loadSlideshow2' ).empty().append( tmpl( "tmpl-lsslist", data ) );
                //bind click to listelement
                $( '#loadSlideshow2 a' ).bind( 'click', function ( e ) {
                    if ( !checkFormState( 'saveslidesform' ) ) {
                        slideSaved = false;
                        triggerSave = true;
                        $( '#saveSlideshow' ).trigger( 'click' );
                        return false;
                    }

                    //se_clearData('all');
                    var _this = $( this );
                    e.preventDefault();
                    e.stopPropagation();
                    //if(_this.parent().hasClass('selected')) return false;
                    if ( $( this ).parent().hasClass( 'selected' ) || _this.hasClass( 'disabled' ) ) return false;
                    //try to load slides
                    var resultSet = handelSlideShow( _this.attr( 'data-id' ), $( '#saveslidesform' ).serializeArray(), 'load' );
                    if ( resultSet.error ) {
                        sendMessage( 'alert-error', resultSet.error );
                        return false;
                    }
                    var title = _this.find( 'span.sname' ).text();
                    var sName = title.length > 9 ? title.substring( 0, 8 ) + '...' : title;
                    $( '#loadSlideShow2Button' ).find( 'span.name' ).text( sName ).attr( 'title', title );
                    /*
                     $attach.empty();
                     $('#bc').html('<span class="divider">>&nbsp;</span><span class="editable" id="ssName" data-type="text">' +_this.find('span.sname').text()+'</span>');
                     //this.firstChild.data
                     $('#saveslidesform input[name="id"]').val( _this.attr('data-id') );
                     $('#saveslidesform input[name="name"]').val(this.firstChild.data);
                     se_handelEditable();
                     */
                    _this.parent().siblings().removeClass( 'selected' );
                    _this.parent().addClass( 'selected' );
//console.log( 'se_loadSlideShowList loaded data' );
//console.log( resultSet );
                    $slides2.empty();
                    var resHtml = tmpl( "tmpl-miniSlides2", resultSet, true );
                    $slides2.append( resHtml );
//recalculateScrollHeight( $('#slidesList2') );    
                    /*
                     storeslideIDs();
                     slideSaved = true;
                     slideShowSaved = checkFormState('saveslidesform');
                     //console.log('se_loadSlideShowList liselement clicked');
                     $(this).closest('.open').removeClass('open');
                     //2013.01.19
                     addUIevents();
                     */
                    $( this ).closest( '.open' ).removeClass( 'open' );
                } );//bind click to listelement
                /*
                 if ($("[rel=tooltip]").length)
                 $("[rel=tooltip]").tooltip();
                 */
            } );
    };

//load slideshow from selected diskarea
    se_loadSlideShowList = function () {
//console.log( 'se_loadSlideShowList fired' );
        return $.ajax( {
            url     : "/crawl?/process/editor/loadslideshows/",
            data    : {diskArea: $( '#saveslidesform' ).find( 'input[name="diskArea_id"]' ).val()},
            type    : 'POST',
            dataType: 'json'
        } ).done( function ( data ) {
//console.log( data );
                $( '#loadSlideshow' ).empty().append( tmpl( "tmpl-lsslist", data ) );
                $( '#loadSlideShowButton' ).find( 'span.name' ).text( 'select slideshow' );
                se_clearData( 'all' );
                //bind click to listelement
                $( '#loadSlideshow a' ).bind( 'click', function ( e ) {
                    if ( $( this ).parent().hasClass( 'selected' ) ) return false;
                    se_clearData( 'all' );
                    var _this = $( this );
                    //slideshow list2 ellenőrzése
                    var parentId = $( '#loadSlideshow2' ).find( 'li.selected a' ).attr( 'data-id' );
                    if ( _this.attr( 'data-id' ) == parentId ) {
                        $( '#slidesList2' ).empty();
                        $( '#loadSlideShow2Button' ).find( 'span.name' ).text( 'select slideshow' ).attr( 'title', '' );
                    }
                    $( '#loadSlideshow2 a' ).removeClass( 'disabled' );
                    $( '#loadSlideshow2' ).find( 'a[data-id="' + _this.attr( 'data-id' ) + '"]' ).addClass( 'disabled' );

                    e.preventDefault();
                    e.stopPropagation();
                    //if(_this.parent().hasClass('selected')) return false;

                    //try to load slides
                    var resultSet = handelSlideShow( _this.attr( 'data-id' ), $( '#saveslidesform' ).serializeArray(), 'load' );
                    if ( resultSet.error ) {
                        sendMessage( 'alert-error', resultSet.error );
                        return false;
                    }
                    $attach.empty();
//2013.01.25
                    $( '#bc' ).html( '<span class="editable" id="ssName" data-type="text">' + _this.find( 'span.sname' ).text() + '</span>' );
                    //this.firstChild.data
                    se_handelEditable();
                    var title = _this.find( 'span.sname' ).text();
                    var sName = title.length > 18 ? title.substring( 0, 15 ) + '...' : title;
                    $( '#saveslidesform input[name="id"]' ).val( _this.attr( 'data-id' ) );
                    $( '#saveslidesform input[name="name"]' ).val( title );
                    $( '#loadSlideShowButton' ).find( 'span.name' ).text( sName ).attr( 'title', title );
//2013.01.25
                    _this.parent().siblings().removeClass( 'selected' );
                    _this.parent().addClass( 'selected' );
//console.log( 'se_loadSlideShowList loaded data' );
//console.log( resultSet );
                    $slides.empty();
                    var resHtml = tmpl( "tmpl-miniSlides", resultSet, true );
                    $slides.append( resHtml );
//recalculateScrollHeight( $('#slidesList') );    
                    storeslideIDs();
                    slideSaved = true;
                    slideShowSaved = checkFormState( 'saveslidesform' );
//console.log('se_loadSlideShowList listelement clicked');
                    $( this ).closest( '.open' ).removeClass( 'open' );
                    //2013.01.19
                    addUIevents();
                    prevOffset = false;
                    var attaches = se_handelAttach( '', $( '#saveslidesform' ).serializeArray(), 'load' );
//console.log(attaches);
                    if ( attaches.error ) {
                        sendMessage( 'alert-error', attaches.error );
                    } else {
                        $attach.append( tmpl( "tmpl-mediaElement", attaches ) );
                        $attach.sortable( sortableAttach );
                    }

                    /*
                     //2013.02.04
                     for(var i=0;i<resultSet.result.length;i++){
                     var html = resultSet.result[i].html;
                     $editor.find('.impressSlidesHolder').append('<div class="mainslide step" data-x="'+(i*1000)+'" data-y="0" data-z="0" data-scale="1" data-rotate="180">'+html+'<div>');
                     }
                     impress().init();
                     //2013.02.04
                     */

                } );//bind click to listelement
                /*
                 if ($("[rel=tooltip]").length)
                 $("[rel=tooltip]").tooltip();
                 */
            } );
    };

// save/update
    handelSlideShow = function ( name, data2, stype ) {
//console.log( 'handelSlideShow fired' );
        var values = {};
        values[stype] = name, values['form'] = data2;
////console.log( 'handelSlideShow sended data' );
////console.log( values );
        return $.parseJSON( $.ajax( {
            url     : "/crawl?/process/editor/handelslideshow/",
            data    : values,
            async   : false,
            type    : 'POST',
            dataType: 'json'
        } ).responseText );
    };

//load/save/update/delete slides
    se_handelSlide = function ( slideid, data, stype, arraied ) {
////console.log( 'se_handelSlide fired' );
//sendMessage('alert-info', 'data-sent to server');
        var values = {};
        values[stype] = slideid, values['form'] = data, values['toArray'] = arraied;
//console.log( 'se_handelSlide values' );
//console.log( values );
        return $.parseJSON( $.ajax( {
            url     : '/crawl?/process/editor/handelslides/',//"/pages/slideeditor/handelSlides.php",
            data    : values,
            async   : false,
            type    : 'POST',
            dataType: 'json'
        } ).responseText );
    };

// events after slide is saved/updated
    se_handeslideAfterEvents = function () {
//console.log( 'se_handeslideAfterEvents fired' );
        //2013.01.19
        addUIevents();
        //recalculateScrollHeight( $('#slidesList') );
        $slides.nestedSortable( sortableSlides );
        storeslideIDs();
        slideSaved = true;
    };

// attachment
    se_handelAttach = function ( data, data2, stype ) {
//console.log( 'se_handelAttach fired' );
        var values = {};
        values[stype] = data, values['form'] = data2;
////console.log( 'handelAttach sended data' );
////console.log( values );
        return $.parseJSON( $.ajax( {
            url     : "/crawl?/process/editor/handelattach/",
            data    : values,
            async   : false,
            type    : 'POST',
            dataType: 'json'
        } ).responseText );
    };

    /*
     loadMediaBox = function(){
     $.ajax({
     url: "/pages/slideeditor/loadMediaFiles.php",
     async: true,
     dataType: 'json'
     }).done(function(data){
     var result = tmpl("tmpl-mediaBox", data);
     $mediaBoxContainer.append(result);
     });
     };
     */

    /*
     for(key in data){
     // for-in loop goes over all properties including inherited properties
     // let's use only our own properties
     if(data.hasOwnProperty(key)){
     //console.log("key = " + key + ", value = " + data[key]);
     }
     }
     //return false;
     /**/
    
    clearEditorInstances = function(){
        for(name in CKEDITOR.instances)
        {
            console.log(name);
            CKEDITOR.instances[name].destroy()
        }

    }

    createEditorInstance = function ( iId, inline ) {
        if ( !document.getElementById( iId ) ) return false;
        var inlineInstance = (inline == true ? true : false);
////console.log( 'instance id: '+inlineInstance );
        var myinstances = [];
        var editor = CKEDITOR.inline( iId );
//this is the foreach loop
        for(var i in CKEDITOR.instances) {

            // this  returns each instance as object try it with alert(CKEDITOR.instances[i])
            console.log(CKEDITOR.instances[i].name);

        }

        return false;

            if ( inlineInstance )
            var editor = CKEDITOR.inline( iId );
        if ( !inlineInstance ) {
            CKEDITOR.replace( iId, {
                sharedSpaces: {
                    top   : 'editorTopBar',
                    bottom: 'editorBottomBar'
                }
            } );
        }
        return false;
    };
    /**/
////////////////////////////////////////////
// create text on editor area at mouse position
// attach wordprocessor to it
////////////////////////////////////////////
//slide editor functions
    editObject = function ( event ) {

        var objectType; //div, img, audio, video, txt
        var sampleText = '';//'Sample text...';
        var _this = $( event.target );
//console.log('clicked on editorArea node: ' + event.target.nodeName);

        //insert textnode, if "T" is active and clicked on editorArea
        if ( event.target.id == 'editorArea' && document.getElementById( 'addText' ).getAttribute( 'class' ) == 'btn btn-dark active' ) {
            var parentOffset = _this.offset();
            var posX = (event.pageX - parentOffset.left),
                posY = (event.pageY - parentOffset.top),
                newX = posX / editingAreaWidth * 100,
                newY = posY / editingAreaHeight * 100;

            var timeStamp = new Date().getTime();
            var eW = 160, eH = 30;
            var nW = parseInt( eW / editingAreaWidth * 100 );
            var nH = parseInt( eH / editingAreaHeight * 100 );

            var results = [];
            results['result'] = [];
            results['result'].push( {
                'instanceID': timeStamp,
                'left'      : newX + '%',
                'top'       : newY + '%',
                'width'     : nW + '%'
            } );
            var result = tmpl( "tmpl-eatext", results );
            $editor.append( $( result ) );
            /*
             $('#myInstance_'+timeStamp).on("paste", function(event,i){
             handlepaste(this,event);
             });
             */
            /*
             var html = '<div class="'+textClass+' '+resizableClass+' isSelected" style="left:'+newX+'%;top:'+newY+'%;width:'+nW+'%;height:auto;position:absolute;">' + 
             '<div class="movingBox"><i class="icon-move"></i></div>'+
             '<div class="deleteBox"><i class="icon-remove"></i></div>'+
             '<div id="myInstance_'+randomnumber+'" class="textDiv" contenteditable="true" style="position: relative;">'+sampleText+'</div>'+
             //'<textarea id="myInstance_'+randomnumber+'"placeholder="Sample text..." contenteditable=true ></textarea>'+
             '</div>';
             */
            $( '#addText' ).toggleClass( 'active' );
//_this.append( $(html) );
            addUIevents();
            var iId = 'myInstance_' + timeStamp;
            createEditorInstance( iId, true );
//console.log( 'nicEditors.editors' );
//console.log( nicEditors.editors );
            /*
             if(nicEditors.editors.length == 0)

             editor = new nicEditor();
             editor.addInstance(iId).floatingPanel();
             */
            //nicEditors.editors.push( new nicEditor().setPanel('myNicPanel').addInstance(iId) );
            document.getElementById( 'editorArea' ).addEventListener( "input", function () {
                checkSlideState();
            }, false );
            return false;

        }
        if ( event.target.nodeName == 'INPUT' ) {
//console.log( _this.attr('type') );
//console.log( _this.attr('checked') );
            switch ( _this.attr( 'type' ) ) {
                case 'radio':
                    $( '#editorArea :input' ).removeAttr( 'checked' );
                    _this.attr( 'checked', 'checked' );
                    break;
                case 'checkbox' :
                    !_this.attr( 'checked' ) ? _this.removeAttr( 'checked' ) : _this.attr( 'checked', 'checked' );
                    break;
            }
        }
        /*else if ( event.target.id == 'editorArea' && event.target.tagName == 'SECTION') {
         //or remove isSelected class from every childNode
         var elements = event.target.childNodes;
         var chNodes = elements.length;
         var tagNames = [];
         for (var i = 0; i < elements.length; ++i) {tagNames.push(elements[i].tagName);}
         for(var i=0;i<elements.length;i++){
         if (typeof tagNames[i] === 'string') {
         var classes = elements[i].getAttribute('class');
         elements[i].setAttribute('class', classes.replace(/\bisSelected\b/,'') );
         ////console.log( i+' : '+ elements[i].getAttribute('class'));
         }
         }
         } */

        /*else if ( event.target.getAttribute('class') =='movingBox' ) {
         //toggle isSelected class
         //console.log( event.target.getAttribute('class') );
         hasClass(event.target.parentNode, 'isSelected') == null ? addClass(event.target.parentNode,'isSelected') : removeClass(event.target.parentNode,'isSelected');
         //$(event.target.parentNode).toggleClass('isSelected');
         }*/


    };
/**/
/////////////////////////////////
// check input on contenteditable
// ex: copy paste from word
/////////////////////////////////
//handel copy+paste
    handlepaste = function ( elem, e ) {
        var savedcontent = elem.innerHTML;
        var clipboardData;
        if ( e && e.clipboardData && e.clipboardData.getData ) {// Webkit - get data from clipboard, put into editdiv, cleanup, then cancel event
            if ( /text\/html/.test( e.clipboardData.types ) ) {
                clipboardData = e.clipboardData.getData( 'text/html' );
            } else if ( /text\/plain/.test( e.clipboardData.types ) ) {
                clipboardData = e.clipboardData.getData( 'text/plain' );
            } else {
                clipboardData = "";
            }
            console.log( clipboardData );
            waitforpastedata( elem, clipboardData );
            if ( e.preventDefault ) {
                e.stopPropagation();
                e.preventDefault();
            }
            return false;
        }
        else {// Everything else - empty editdiv and allow browser to paste content into it, then cleanup
            console.log( 'handlepaste everything else fired' );
            //elem.innerHTML = "";
            waitforpastedata( elem, clipboardData );
            return true;
        }
    }

    waitforpastedata = function ( elem, savedcontent ) {
        console.log( 'pastedata fired' );
        if ( elem.childNodes && elem.childNodes.length > 0 ) {
            processpaste( elem, savedcontent );
        }
        else {
            that = {
                e: elem,
                s: savedcontent
            }
            that.callself = function () {
                waitforpastedata( that.e, that.s )
            }
            setTimeout( that.callself, 20 );
        }
    }

    processpaste = function ( elem, savedcontent ) {
        pasteddata = elem.innerHTML;
        //^^Alternatively loop through dom (elem.childNodes or elem.getElementsByTagName) here

        //elem.innerHTML = savedcontent;

        // Do whatever with gathered data;
        return pasteHtmlAtCaret( cleanHTML( savedcontent ) );
//console.log( cleanHTML(pasteddata) );
    }

//////////////////////////////////////////////
//insert form element in a contenteditable div
// to the cursor position
//////////////////////////////////////////////
    pasteHtmlAtCaret = function ( html ) {
        var sel, range;
        if ( window.getSelection ) {
            // IE9 and non-IE
            sel = window.getSelection();
            if ( sel.getRangeAt && sel.rangeCount ) {
                range = sel.getRangeAt( 0 );
                range.deleteContents();
                // Range.createContextualFragment() would be useful here but is
                // non-standard and not supported in all browsers (IE9, for one)
                var el = document.createElement( "div" );
                el.innerHTML = html;
                var frag = document.createDocumentFragment(), node, lastNode;
                while ( (node = el.firstChild) ) {
                    lastNode = frag.appendChild( node );
                }
                range.insertNode( frag );
                // Preserve the selection
                if ( lastNode ) {
                    range = range.cloneRange();
                    range.setStartAfter( lastNode );
                    range.collapse( true );
                    sel.removeAllRanges();
                    sel.addRange( range );
                }
            }
        } else if ( document.selection && document.selection.type != "Control" ) {
            // IE < 9
            document.selection.createRange().pasteHTML( html );
        }
        console.log( ' formDrop pasteHtmlAtCaret' );
        prevOffset = false;
    };

//check if getselection is in a contenteditables div by id
    isOrContains = function ( node, container ) {
        while ( node ) {
            if ( node === container ) {
//console.log( true );
                return true;
            }
            node = node.parentNode;
        }
//console.log( false );
        return false;
    }

    elementContainsSelection = function ( el ) {
        var sel;
        if ( window.getSelection ) {
            sel = window.getSelection();
            if ( sel.rangeCount > 0 ) {
                for ( var i = 0; i < sel.rangeCount; ++i ) {
                    if ( !isOrContains( sel.getRangeAt( i ).commonAncestorContainer, el ) ) {
                        return false;
                    }
                }
                return true;
            }
        } else if ( (sel = document.selection) && sel.type != "Control" ) {
            return isOrContains( sel.createRange().parentElement(), el );
        }
        return false;
    };

///////////////////////////////
// paste word html to contenteditable
// and clean it
///////////////////////////////
    cleanHTML = function ( in_word_text ) {
        var tmp = document.createElement( "span" );
        tmp.innerHTML = in_word_text;
        var newString = tmp.textContent || tmp.innerText;
        // this next piece converts line breaks into break tags
        // and removes the seemingly endless crap code
        newString = newString.replace( /\n\n/g, "" ).replace( /<!--[\s\S]*?-->/g, "" ).replace( /<(.|\n)*?>/g, " " );
        newString = newString.replace( /<(.|\n)*?>/g, " " );
//console.log( newString );
        newString = replaceNbsps( newString );
        // this next piece removes any break tags (up to 10) at beginning
        for ( i = 0; i < 100; i++ ) {
            if ( newString.substr( 0, 6 ) == "<br />" ) {
                newString = newString.replace( "<br />", "" );
            }
        }
        return newString;
    }

//////////////////////////////////
// copy paste from clipboard to contenteditable
//////////////////////////////////
    getInputSelection = function ( el ) {
        console.log( el.innerHTML );
//return false;
        var start = 0, end = 0, normalizedValue, range,
            textInputRange, len, endRange;

        if ( typeof el.selectionStart == "number" && typeof el.selectionEnd == "number" ) {
            start = el.selectionStart;
            end = el.selectionEnd;
        } else {
            range = getSelectedText();//document.selection.createRange();

            if ( range ) {//&& range.parentElement() == el) {
                len = el.innerHTML.length;
                normalizedValue = el.innerHTML.replace( /\r\n/g, "" ).replace( /\n\n/g, "" ).replace( /<!--[\s\S]*?-->/g, "" ).replace( /<(.|\n)*?>/, "" );
                normalizedValue = replaceNbsps( normalizedValue );
                ;

                // Create a working TextRange that lives only in the input
                textInputRange = el.createTextRange();
                textInputRange.moveToBookmark( range.getBookmark() );

                // Check if the start and end of the selection are at the very end
                // of the input, since moveStart/moveEnd doesn't return what we want
                // in those cases
                endRange = el.createTextRange();
                endRange.collapse( false );

                if ( textInputRange.compareEndPoints( "StartToEnd", endRange ) > -1 ) {
                    start = end = len;
                } else {
                    start = -textInputRange.moveStart( "character", -len );
                    start += normalizedValue.slice( 0, start ).split( "\n" ).length - 1;

                    if ( textInputRange.compareEndPoints( "EndToEnd", endRange ) > -1 ) {
                        end = len;
                    } else {
                        end = -textInputRange.moveEnd( "character", -len );
                        end += normalizedValue.slice( 0, end ).split( "\n" ).length - 1;
                    }
                }
            }
        }

        return {
            start: start,
            end  : end
        };
    }

////////////////////////////////
    replaceNbsps = function ( str ) {
        var re = new RegExp( String.fromCharCode( 160 ), "g" );
        return str.replace( re, " " );
    }


//contenteditable true and get selected range
    getSelectedText = function () {
        if ( window.getSelection ) {
            return window.getSelection();
        }
        else {
            if ( document.selection ) {
                return document.selection.createRange();//.text;
            }
        }
        return '';
    };


///////////////////////////////////
//purgeHtml for slideshow
//remove move/delete/resize elements
// unwrap myinstance div around text
///////////////////////////////////
    purgeHtml = function ( container, templatetype ) {
        var purged = '';
        //step2
        //case group template move all element to holder
        if ( templatetype == 'groupping' ) {
            var groups = container.find( '.sortableForm:not([id="group1"])' );
            $.each( groups, function () {
                var ht = $( this ).html();
                $( '#sortableHolder', container ).append( ht );
                $( this ).html( '' );
            } );
        }
        $( container ).find( '.slideItem' ).each( function () {
            $( this ).find( '.movingBox' ).remove();
            $( this ).find( '.deleteBox' ).remove();
            $( this ).find( '[contenteditable="true"]' ).each( function () {
                $( this ).contents().unwrap();
            } );
            $( this )
                .removeClass( 'textClass' )
                .removeClass( 'ResizableClass' )
                .removeClass( 'isSelected' )
                .removeClass( 'nonResizableClass' )
                .removeAttr( 'data-item-type' );
            $( this ).find( ':input[type="text"]' ).each( function () {
                $( this ).val( '' );
            } );
            $( this ).find( ':input[type="radio"], :input[type="checkbox"]' ).removeAttr( 'checked' );
            $( this ).find( 'option' ).each( function () {
                $( this ).removeAttr( 'selected' );
            } );
            $( this ).find( '.btn' ).each( function () {
                $( this ).attr( 'class', 'btn-dark' );
            } );

        } );
        $( container ).find( '.buttonClass' ).remove();
        return container.html();
    };

///////////////////////////////////
// purgeHtml of contenteditable before save to db
// purge html data from empty elements like: <span></span>
// nodeType
// Element  1
// Attr  2
// Text  3
///////////////////////////////////

    recurseDomChildren = function ( start, output ) {
        var nodes;
        if ( start.childNodes ) {
            nodes = start.childNodes;
            loopNodeChildren( nodes, output );
        }
    };

    loopNodeChildren = function ( nodes, output ) {
        var node;
        for ( var i = 0; i < nodes.length; i++ ) {
            node = nodes[i];
            switch ( node.tagName ) {
                case 'IMG':
                case 'AUDIO':
                case 'VIDEO':
                    var sClass = node.parentNode.getAttribute( 'class' );
//console.log('parent nod style: '+  node.parentNode.getAttribute('style') );
                    if ( sClass != null )
                        if ( sClass.match( /ResizableClass/g ) ) {
                            node.parentNode.style.position = 'absolute';
////console.log('parent nod style: '+  node.parentNode.getAttribute('style') );
                            //node.setAttribute('style', node.parentNode.getAttribute('style') );
                        }
                    continue;
                    break;
                case 'DIV':
                    if ( node.parentNode.getAttribute( 'class' ) == 'textClass ResizableClass' ) {
////console.log('parent nod class: '+ node.parentNode.getAttribute('class') );
                        node.parentNode.style.position = 'absolute';
                        //node.setAttribute('style', node.parentNode.getAttribute('style') );
                        //conso
                    }
            }
            if ( output ) {
                outputNode( node );
            }
            if ( node.childNodes ) {
                recurseDomChildren( node, output );
            }
        }
    };

    outputNode = function ( node ) {
        var whitespace;
        whitespace = /^\s+$/g;
        if ( node.nodeType === 1 ) {
////console.log("element: " + node.tagName);
            for ( var x = 0; x < node.attributes.length; x++ ) {
////console.log("attr: " + node.attributes[x].nodeValue); 
            }
        } else if ( node.nodeType === 3 ) {
            //clear whitespace text nodes
            node.data = node.data.replace( whitespace, "" );
            if ( node.data && node.data.length == 1 ) {
                //clear unicode text nodes
                node.data = node.data.replace( /[\u0080-\uFFFF]+/g, "" );
                if ( node.parentNode.tagName == 'SPAN' && node.data.length == 0 ) {
////console.log('ures node '+node.parentNode.parentNode.tagName ); 
                    node.parentNode.parentNode.removeChild( node.parentNode );
                }
            }
        }
    };

////////////////////////////////////////////
//recalculate scroller haight to prevent slide delete
//bug, if slide is outside of scroller
//can't be deleted with drag to left
////////////////////////////////////////////
    recalculateScrollHeight = function ( scroller ) {
        var items = scroller.find( 'li' );
////console.log( items.length );
        var totalHeight = 0;
        $.each( items, function () {
////console.log( $(this).outerHeight() );
            totalHeight += $( this ).outerHeight();
        } );
////console.log( totalHeight );
        //scroller.parent().css('height', totalHeight+'px');
        scroller.css( 'height', totalHeight + 'px' );
    };

////////////////////////////////////////////
//set traffic managers data on slide
    setTrafficData = function () {
        $( '#testSlides .dataHolder' ).removeClass( 'selected' );
        var prevS, scoreS, nextS, obj = [];
        $( '.trafficmanagerContainer .control-group' ).each( function ( i, e ) {
            prevS = $( this ).find( '#slidePrev' ).val() ,
                scoreS = $( this ).find( '#slideScore' ).val(),
                nextS = $( this ).find( '#slideNext' ).val();
////console.log(i +' : '+prevS+'-'+scoreS+'-'+nextS);
            var prevID = searchTrafficData( prevS ),
                nextID = searchTrafficData( nextS );
//console.log( 'pervID: '+prevID );
            obj.push( {
                'prev'  : prevS,
                'prevID': (typeof(prevID) == 'undefined' ? 0 : prevID),
                'score' : scoreS,
                'next'  : nextS,
                'nextID': (typeof(nextID) == 'undefined' ? 0 : nextID)
            } );
        } );
        return obj;
    };

//get traffic managers data from slide
    getTrafficData = function ( slideelement ) {
        var $datalines = slideelement.find( '.whiteLine' );
        var data = [];
        data['result'] = [];
////console.log( 'getTrafficData' );
////console.log( $datalines );
        $.each( $datalines, function ( i, e ) {
////console.log( 'sor: '+i );
            data['result'].push( {
                'prev'  : $( e ).find( 'span.badge.prev' ).text(),
                'prevID': $( e ).find( 'span.badge.prev' ).attr( 'data-slide-id' ),
                'score' : $( e ).find( 'span.badge.score' ).text(),
                'next'  : $( e ).find( 'span.badge.next' ).text(),
                'nextID': $( e ).find( 'span.badge.next' ).attr( 'data-slide-id' )
            } );
        } );
        $( '.trafficDataHolder' ).empty();
        return data;
    };

//store slides ID-s in an array
    storeslideIDs = function () {
        slideIDs = [];
        $.each( $( '#slidesList li.slideElement' ), function () {
            var key = $( this ).attr( 'id' ),
                value = parseInt( $( this ).find( 'span.badge.nr' ).text() );
            slideIDs[key] = value;
        } );
    }
//return slidelement id for a given badge(nr)
    searchTrafficData = function ( value ) {
        for ( var key in slideIDs ) {
            if ( slideIDs[key] == value )
                return key;
//console.log( 'key: '+key+' value: '+slideIDs[key] );
        }
    };

//after reordering or removing or inserting slideelement 
//must the ID holder array updated
    updateSlidesIDarray = function ( ts, i, option ) {
        switch ( option ) {
            case 'new'   :
                slideIDs[ts] = i;
                break;
            case 'delete':
                delete slideIDs[ts];
                break;
            case 'update':

                break;
        }
    };

//get slides ID, when the traffic data is known
    getslidesIDforTrafficData = function ( badge ) {
        for ( i in slideIDs ) {
            if ( slideIDs[i] == badge ) return i;
        }
    };

//after reordering slideelement list must correct traffic data pointers
    refreshslidesIDinTrafficData = function ( ts ) {
        var i = slideIDs[ts];
        var trafficElement = $( '#slidesList .trafficData' ).filter( function () {
            return $( this ).find( 'span.badge.nr' ).attr( 'data-slide-id' ) == ts
        } );
        var trafficElement = $( '#slidesList .trafficData' ).find( '[data-slide-id="' + ts + '"]' );
        for ( var j = 0; j < trafficElement.length; j++ )
            $( trafficElement[j] ).text( slideIDs[ts] );
    };

//search for id in traffic data if user want to delete slide
    searchslidesIDinTrafficData = function ( ts ) {
        var i = slideIDs[ts];
        var trafficElement = $( '#slidesList .trafficData' ).filter( function () {
            return $( this ).find( 'span.badge.nr' ).attr( 'data-slide-id' ) == ts
        } );
        var trafficElement = $( '#slidesList .trafficData' ).find( '[data-slide-id="' + ts + '"]' );
        return trafficElement.length == 0 ? false : true;
        //for(var j=0;j<trafficElement.length;j++)
        //$(trafficElement[j]).text(slideIDs[ts]);
    };

//when remove a slide and template slide pointing to it, set number to 0
    deleteslidesIDinTrafficData = function ( ts ) {
        var i = slideIDs[ts];
        var trafficElement = $( '#slidesList .trafficData' ).filter( function () {
            return $( this ).find( 'span.badge.nr' ).attr( 'data-slide-id' ) == ts
        } );
        var trafficElement = $( '#slidesList .trafficData' ).find( '[data-slide-id="' + ts + '"]' );
        for ( var j = 0; j < trafficElement.length; j++ )
            $( trafficElement[j] ).text( 0 ).attr( 'data-slide-id', '0' );
    };
/**/
    setSlidesOnOrch = function () {
        var $elements = $slides.find( 'li.slideElement' ),
            results = [];

        $elements.each( function ( el, i ) {
            results['result'].push( {

            } );
            var result = tmpl( "tmpl-eaImage", results );
        } );
    }
})( jQuery );