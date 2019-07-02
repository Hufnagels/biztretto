(function ( slideEditor, $, undefined ) {

    var mainElements = {
            leftContainer   : document.getElementById( "mySlideshowsContainer" ) || '',
            previewContainer: document.querySelector( "div.preview" ) || '',
            previewIframe   : document.getElementById( "previewIframe" ) || '',
            form            : document.getElementById( "saveslidesform" ) || ''
        },

        actions = {
            slide     : {0: 'load', 1: 'save', 2: 'delete', 3: 'new'},
            slideshows: {0: 'load', 1: 'list', 2: 'new', 3: 'save', 4: 'saveas', 5: 'delete'},
            mediafiles: {0: 'load', 1: 'loadgroup', 2: 'insert'}
        },

        affixElements = {
            0: mainElements.leftContainer
        },

        preventObject = {

        },

        values = {},
        settings = {
            url         : "",
            data        : values,
            responseType: 'json'
        },
        defaults = {
            diskAreaId                : $( 'input[name="diskArea"]' ).val(),
            currentSlideShowId        : null,
            templateSlide             : false,
            slideSaved                : false,
            slideShowSaved            : false,
            prevOffset                : false,
            slideIDs                  : [],
            editingAreaWidth          : 0,
            editingAreaHeight         : 0,
            textClass                 : 'textClass',
            resizableClass            : 'ResizableClass',
            nonResizableClass         : 'nonResizableClass',
            isSelected                : 'isSelected',
            triggerSave               : false,
            scrollContainerHeight     : 345,
            scrollContainerInnerHeight: 270,
            openedClass               : 'icon-eye-close opener',
            closedClass               : 'icon-eye-open opener'
        };

    var Editor = {
        editorArea: document.getElementById( "editorArea" ) || '',
        editorForm: document.getElementById( 'editorAreaForm' ) || '',

        init      : function () {
            $( '.deleteBox' ).live( 'click', function () {
                if ( $( this ).parent().hasClass( 'textClass' ) ) {
                    Editor.helperFunctions.ckeditor.removeEditorInstance( $( this ).parent().find( 'div[id^="myInstance_"]' ).attr( 'id' ) )
                    Editor.helperFunctions.listEditorInstances();
                }

                $( this ).parent().remove();
                defaults.slideSaved = false;
//checkSlideState();
            } );
            this.toolbarElements.init();
            this.bottombarElements.init();
        },

        helperFunctions : {

            clearEditorInstances: function () {
                for (name in CKEDITOR.instances) {
                    console.log(name);
                    CKEDITOR.instances[name].destroy()
                }
            },

            createEditorInstance: function (iId, inline) {

                if (!document.getElementById(iId)) return false;
                var inlineInstance = (inline == true ? true : false);
////console.log( 'instance id: '+inlineInstance );
                var myinstances = [];
                var editor = CKEDITOR.inline(iId);
//this is the foreach loop

                return false;

                if (inlineInstance)
                    var editor = CKEDITOR.inline(iId);
                if (!inlineInstance) {
                    CKEDITOR.replace(iId, {
                        sharedSpaces: {
                            top: 'editorTopBar',
                            bottom: 'editorBottomBar'
                        }
                    });
                }
                return false;
            },

            removeEditorInstance : function(id){
                CKEDITOR.instances[id].destroy()
            },

            listEditorInstances : function(){
                for (var i in CKEDITOR.instances) {
                    console.log(CKEDITOR.instances[i].name);
                }
            }

        },

        toolbarElements: {
            gridButton            : document.getElementById( "removeBG" ) || '',
            insertTextButton      : document.getElementById( "addText" ) || '',
            clearAreaButton       : document.getElementById( "clearArea" ) || '',
            newSlideButton        : document.getElementById( "newSlide" ) || '',
            duplicateSlideButton  : document.getElementById( "duplicateSlide" ) || '',
            saveSlideButton       : document.getElementById( "saveSlide" ) || '',
            newSlideshowButton    : document.getElementById( "createNewSlideShow" ) || '',
            previewSlideshowButton: document.getElementById( "previewSlideShow" ) || '',


            init: function () {

                addEventO( Editor.editorArea, 'click', function ( event ) {
                    var objectType; //div, img, audio, video, txt
                    var sampleText = '';//'Sample text...';
                    var _this = $( event.target );
console.log( 'clicked on editorArea node: ' + event.target.nodeName );
console.log( event )

                    //insert textnode, if "T" is active and clicked on editorArea
                    if ( event.target.id == 'editorArea' && $( Editor.toolbarElements.insertTextButton ).hasClass( 'active' ) ) {
                        var parentOffset = _this.offset();
                        var posX = (event.pageX - parentOffset.left),
                            posY = (event.pageY - parentOffset.top),
                            newX = posX / defaults.editingAreaWidth * 100,
                            newY = posY / defaults.editingAreaHeight * 100;
                        console.log( posX )
                        var timeStamp = new Date().getTime();
                        var eW = 160, eH = 30;
                        var nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                        var nH = parseInt( eH / defaults.editingAreaHeight * 100 );

                        var results = [];
                        results['result'] = [];
                        results['result'].push( {
                            'instanceID': timeStamp,
                            'left'      : newX + '%',
                            'top'       : newY + '%',
                            'width'     : nW + '%'
                        } );
                        var result = tmpl( "tmpl-eatext", results );
                        $( Editor.editorArea ).append( $( result ) );

                        /*
                         var html = '<div class="'+textClass+' '+resizableClass+' isSelected" style="left:'+newX+'%;top:'+newY+'%;width:'+nW+'%;height:auto;position:absolute;">' +
                         '<div class="movingBox"><i class="icon-move"></i></div>'+
                         '<div class="deleteBox"><i class="icon-remove"></i></div>'+
                         '<div id="myInstance_'+randomnumber+'" class="textDiv" contenteditable="true" style="position: relative;">'+sampleText+'</div>'+
                         //'<textarea id="myInstance_'+randomnumber+'"placeholder="Sample text..." contenteditable=true ></textarea>'+
                         '</div>';
                         */
                        $( Editor.toolbarElements.insertTextButton ).toggleClass( 'active' );
//_this.append( $(html) );
                        Editor.addDND();
//addUIevents();
                        var iId = 'myInstance_' + timeStamp;
                        helperFunctions.createEditorInstance( iId, true );
                        $( Editor.editorArea ).trigger( 'input' );
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

                }, true, _eventHandlers );
            }
        },

        bottombarElements: {
            bottomBar          : document.getElementById( 'editorBottomBar' ) || '',
            slidetagInput      : document.getElementById( 'slideTag' ) || '',
            description        : document.getElementById( 'descArea' ) || '',
            levelCount         : document.getElementsByName( 'levelCount' ) || '',
            testEditorContainer: document.getElementsByClassName( 'testSlideEditorContainer' ) || '',

            init: function () {
                addEventO( Editor.bottombarElements.slidetagInput, 'keypress', function () {
                    if ( $( Slideshows.slideshowContainer ).find( '.dataHolder.selected' ).length == 1 ) {
                        slideSaved = false;
                        //checkSlideState();
                    }
                }, true, _eventHandlers )
            }
        },

        options : {

            editorDropOption: {
                tolerance  : 'pointer',
                accept     : ".image, .video, .audio, li",
                activeClass: "ui-state-highlight",
                hoverClass : "ui-state-active",
                drop       : function ( e, ui ) {

                    var parentOffset = $( this ).offset();
                    //or $(this).offset(); if you really just want the current element's offset
                    var posX = e.pageX - parentOffset.left;
                    var posY = e.pageY - parentOffset.top;
                    posX = parseInt( posX / defaults.editingAreaWidth * 100 );
                    posY = parseInt( posY / defaults.editingAreaHeight * 100 );

                    //2013.01.19
//console.log( 'editordropoption DROP fired (pervoffset check) ' +  prevOffset);
                    if ( !prevOffset &&
                        ($(editorAreaElememts.editorArea).attr( 'data-template-type' ) !== 'normal' &&
                            $(editorAreaElememts.editorArea).attr( 'data-template-type' ) !== 'puzzle') ) {
                        //prevOffset=true;
                        //console.log( 'kukazva az elem' );
                        //2013.02.12
                        //return false;
                    }
                    var isPuzzle = false;
                    if ( $(editorAreaElememts.editorArea).attr( 'data-template-type' ) == 'puzzle' )
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
                            if ( isPuzzle && $(editorAreaElememts.editorArea).find( 'img' ).length > 0 ) return false;
                            eW = 160;
                            eH = 120;
                            nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                            nH = parseInt( eH / defaults.editingAreaHeight * 100 );
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
                            //$(editorAreaElememts.editorArea).append( $(result) );
                            break;
                        case 'colorBar audio':
                            if ( isPuzzle ) return false;
                            eW = 250;
                            eH = 30;
                            nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                            nH = parseInt( eH / defaults.editingAreaHeight * 100 );
                            results['result'].push( {
                                'mediaurl': ui.draggable.attr( 'data-mediaurl' ).split( '.mp3' )[0],
                                'left'    : posX + '%',
                                'top'     : posY + '%',
                                'width'   : nW + '%',
                                'height'  : nH + '%'
                            } );
                            var result = tmpl( "tmpl-eaLAudio", results );
                            $(editorAreaElememts.editorArea).append( $( result ) );
                            break;
                        case 'colorBar video':
                            if ( isPuzzle ) return false;
                            eW = 200;
                            eH = 112;
                            nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                            nH = parseInt( eH / defaults.editingAreaHeight * 100 );
                            var tmplName, mediaurl, poster, fname, daid, oname;
                            switch ( ui.draggable.attr( 'data-mediatype' ) ) {
                                case 'local':
                                    eW = ui.draggable.attr( 'data-video-width' );
                                    eH = ui.draggable.attr( 'data-video-height' );
                                    nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                                    nH = parseInt( eH / defaults.editingAreaHeight * 100 );
                                    mediaurl = ui.draggable.attr( 'data-mediaurl' ).split( '.mp4' )[0];
                                    poster = ui.draggable.find( 'img' ).attr( 'src' );//attr('data-poster');
                                    tmplName = 'tmpl-eaLVideo';
                                    fname = ui.draggable.attr( 'isotope-data-name' ).split( '.mp4' )[0];
                                    daid = $( '#themeList li.selected' ).attr( 'data-diskarea-id' );
                                    oname = $( 'input[name="office_nametag"]' ).val();
                                    break;
                                case 'remote':
                                    var video = parseVideoURL( ui.draggable.attr( 'data-mediaurl' ) );
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
                            $(editorAreaElememts.editorArea).append( $( result ) );
                            break;
                        case 'btn btn-dark':
                            //groupping template
                            if ( isPuzzle ) return false;
                            var element, group = ui.helper.clone();
                            var parentOffset = $(editorAreaElememts.editorArea).offset(),
                                posX = e.pageX - parentOffset.left,
                                posY = e.pageY - parentOffset.top;
                            var eW, eH, nW, nH;
                            posX = parseInt( posX / defaults.editingAreaWidth * 100 );
                            posY = parseInt( posY / defaults.editingAreaHeight * 100 );
                            eW = 200;
                            eH = 120;
                            if ( group.hasClass( 'group' ) ) {
                                element = group.find( 'div.ResizableClass' );
                            }
                            if ( element.hasClass( 'ResizableClass' ) ) {
                            }
                            ////console.log( 'formdrop groupping element' );
                            ////console.log( element.html() );
                            nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                            nH = parseInt( eH / defaults.editingAreaHeight * 100 );
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
                            $(editorAreaElememts.editorArea).append( element );
                            Editor.helperFunctions.createEditorInstance(iId)
                            //editor.addInstance( iId ).floatingPanel();

                            break;
                    }
                    //$(editorAreaElememts.editorArea).find('video').skVideo();
                    //$('#editorsMediaBox li').draggable('destroy');
                    addUIevents();
                    //$(editorAreaElememts.editorArea).droppable('destroy');
                    slideSaved = false;
                    slideShowSaved = checkFormState( 'saveslidesform' );
                    checkSlideState();

                },
                stop       : function ( e, ui ) {
                    var posX = parseInt( $( this ).offset().left / defaults.editingAreaWidth * 100 );
                    var posY = parseInt( $( this ).offset().top / defaults.editingAreaWidth * 100 );
                    var eW = $( this ).width(), eH = $( this ).height();
                    var nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                    var nH = parseInt( eH / defaults.editingAreaHeight * 100 );
                    if ( $( this ).hasClass( 'textClass' ) )
                        $( this ).css( 'height', 'auto' );
                    $( this ).css( {'left': posX + '%', 'top': posY + '%', 'width': nW + '%'/*, 'height':nH+'%'*/} );


                }/*,
                 out: function(event, ui){
                 //console.log( 'drop failde' );
                 }*/
            },

            editorElementdraggable : {
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
                    ////console.log( 'positionLeft: '+$(this).position().left+' defaults.editingAreaWidth: '+editingAreaWidth+' percent: '+$(this).position().left/(editingAreaWidth-20)*100);
                },
                stop       : function ( e, ui ) {
                    ////console.log( $(this).position().left/$(this).parent().outerWidth()*100 );
                    var posX = $( this ).position().left / defaults.editingAreaWidth * 100;//parseInt();
                    var posY = $( this ).position().top / defaults.editingAreaHeight * 100;//parseInt();
                    var eW = $( this ).outerWidth(), eH = $( this ).outerHeight();
                    var nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                    var nH = parseInt( eH / defaults.editingAreaHeight * 100 );
                    $( this ).css( {'left': posX + '%', 'top': posY + '%', 'width': nW + '%'/*, 'height':nH+'%'*/} );
                    //console.log('editor element drag stopped');
                    slideSaved = false;
                    slideShowSaved = checkFormState( 'saveslidesform' );
                    checkSlideState();
                    addToolsEvent( $(editorAreaElememts.editorArea).attr( 'data-template-type' ) );
                }
            },

            resizableOptions : {
                helper: "ui-resizable-helper",
                ghost : true,
                //"aspectRatio": true,
                stop  : function ( event, ui ) {
                    var posX = $( this ).position().left / defaults.editingAreaWidth * 100;//parseInt();
                    var posY = $( this ).position().top / defaults.editingAreaHeight * 100;//parseInt();
                    var eW = $( this ).outerWidth(), eH = $( this ).outerHeight();
                    var nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                    var nH = parseInt( eH / defaults.editingAreaHeight * 100 );
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
                    $(Editor.editorArea ).find( resizableClass ).resizable( "option", "maxWidth", maxWidth );
                    $(Editor.editorArea ).find( resizableClass ).resizable( "option", "maxHeight", maxHeight );

                    //$('#editorArea .'+resizableClass).css('height','auto');
                    slideSaved = false;
                    checkSlideState();
                }
            },


            resizableVideoOptions: {
                helper       : "ui-resizable-helper",
                ghost        : true,
                "aspectRatio": true,
                start        : function ( event, ui ) {
                    var iframe = $( this ).find( 'iframe' );
                    iframe.css( 'pointer-events', 'none' );
                },
                stop         : function ( event, ui ) {
                    var posX = $( this ).position().left / defaults.editingAreaWidth * 100;//parseInt();
                    var posY = $( this ).position().top / defaults.editingAreaHeight * 100;//parseInt();
                    var eW = $( this ).outerWidth(), eH = $( this ).outerHeight();
                    var nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                    var nH = parseInt( eH / defaults.editingAreaHeight * 100 );
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
                    $(Editor.editorArea ).find( resizableClass ).resizable( "option", "maxWidth", maxWidth );
                    $(Editor.editorArea ).find( resizableClass ).resizable( "option", "maxHeight", maxHeight );

                    //$('#editorArea .'+resizableClass).css('height','auto');
                    slideSaved = false;
                    checkSlideState();
                }
            }

        }

    };

    var MyMedia = {
        mediaboxList  : document.getElementById( 'mediaBoxList' ) || '',
        mediaContainer: document.getElementById( "editorsMediaBox" ) || '',
        sortingBar    : document.getElementById( "sortingIconBar" ) || '',
        groupList     : document.getElementById( "mediaBoxList" ) || '',

        init: function () {
            this.selectBox();
            this.filterFiles();
        },

        filterFiles: function () {
            $( MyMedia.sortingBar ).find( 'button' ).bind( 'click', function () {
                if ( $( MyMedia.mediaContainer ).find( 'li' ).length == 0 ) return false;
                var classes = $( this ).attr( 'data-class' );
                $( MyMedia.mediaContainer ).find( 'li' ).hide();
                var classArray = classes.split( /\s+/g );
                for ( i in classArray )
                    classes == '' ? $( MyMedia.groupList ).find( 'li' ).show() : $( MyMedia.groupList ).find( 'li.' + classArray[i] ).show();
            } )
        },

        selectBox: function () {
            $( MyMedia.groupList ).find( 'li:not(.selected) a.level2' ).live( 'click', function ( e ) {
                var _this = $( this );
                e.preventDefault();
                e.stopPropagation();
                _this.parent().siblings().removeClass( 'selected' );
                _this.parent().addClass( 'selected' );
                var folderName = $( this ).find( 'span.name' ).text();
                folderName = folderName.length > 9 ? folderName.substring( 0, 8 ) + '...' : folderName;
                $( MyMedia.groupList ).prev().find( 'span.name' ).text( folderName ).attr( 'title', $( this ).find( 'span.name' ).text() );
                MyMedia.viewBoxFiles( _this.attr( 'data-id' ) );
                $( '.selectMenu' ).parent().removeClass( 'open' );
            } );
        },

        viewBoxFiles: function ( id ) {
            values = {};
            values['action'] = actions.mediafiles[0];
            values['form'] = $( mainElements.form ).serializeArray();
            values['diskArea'] = defaults.diskAreaId;

            settings.url = "/crawl?/process/mymedia/handelmediafiles/";
            settings.data = values;
            var data = getJsonData( settings );
            if ( !data )
                return false;

            $( myMediaElements.mediaList ).append( tmpl( "tmpl-mediaElement", data ) );
        },

        addDND: function () {
            $( MyMedia.mediaContainer ).find( 'li' ).draggable( this.dndOptions.myMediaDragOption );
        },

        dndOptions: {
            myMediaDragOption: {
                appendTo: 'body',
                handle  : 'img',
                revert  : 'true',
                helper  : 'clone',
                start   : function ( e, ui ) {
//console.log( 'myMediaDragOption START fired: templateDropOption' );
//console.log( 'prevOffset: ' + prevOffset );
                    //2013.01.19
                    //$(editorAreaElememts.editorArea)1
                    $( editorAreaElememts.editorArea ).find( '.holder .textDiv' ).droppable( templateDropOption );
                    removeToolsEvent();
                    //$('#editorArea .sortableForm').droppable(formDrop);
//removeToolsEvent();
//console.log( 'myMediaDragOption START fired: editorDropOption' );
                    $( editorAreaElememts.editorArea ).droppable( Editor.editorDropOption );
//2012.12.11
//console.log( 'myMediaDragOption START fired: attachDropOption' );
                    //$attach.droppable( attachDropOption );

                    prevOffset = true;
                },
                stop    : function ( e, ui ) {
                    $( editorAreaElememts.editorArea ).droppable( 'destroy' );
                    if ( $( editorAreaElememts.editorArea ).attr( 'data-template-type' ) !== 'normal' )
                        addToolsEvent( $( editorAreaElememts.editorArea ).attr( 'data-template-type' ) );
                }
            }
        }


    };

    var Slideshows = {
        slideshowList      : document.getElementById( "loadSlideshow" ) || '',
        slideshowContainer : document.getElementById( "slidesList" ) || '',
        slideshowList2     : document.getElementById( "slidesList" ) || '',
        slideshowContainer2: document.getElementById( "slidesList2" ) || '',

        init: function () {

        },

        loadSlideshowList : function () {
        },
        loadSlideshow     : function ( id ) {
        },
        renameSlideshow   : function ( id ) {
        },
        deleteSlideshow   : function ( id ) {
        },
        duplicateSlideshow: function ( id ) {
        },
        saveSlideshow     : function ( id ) {
        },
        gatherSlideshow   : function () {

        },
        dndOptions        : {},

        slides: {
            init       : function () {
            },
            selectSlide: function ( id ) {
            },
            deleteSlide: function ( id ) {
            }

        }


    };

    var Tests = {
        testsContainer: document.getElementById( "testSlides" ) || '',
        init          : function () {

        },

        selectTest: function ( id ) {

        }
    };

    var Templates = {

        init: function () {

        }

    };



    slideEditor.init = function () {

        $( '#loading' ).show();
        initAccordion();

        initScroller();
        initAffix( affixElements );


        for ( var key in preventObject ) {
            $( preventObject[key] ).preventEnter();
        };

        Editor.init();
        Slideshows.init();
        MyMedia.init();
        Tests.init();
        Templates.init();

        defaults.editingAreaWidth = Editor.editorArea.offsetWidth;
        defaults.editingAreaHeight = Editor.editorArea.offsetHeight;

        $( '#loading' ).hide();

        $window.resize( function () {
            defaults.editingAreaWidth = Editor.editorArea.offsetWidth;
            defaults.editingAreaHeight = Editor.editorArea.offsetHeight;
        } )
        scrollToWorkingArea( $( '.row.special' ) );
    }

    this.initScroller = function () {
        $( Slideshows.slideshowList ).slimScroll( { position: 'left', height: '400px', allowPageScroll: false} );
        $( Slideshows.slideshowContainer ).slimScroll( { position: 'left', height: '400px', allowPageScroll: false, width: '190px' } );
        $( MyMedia.mediaContainer ).slimScroll( { position: 'left', height: '400px', allowPageScroll: false, width: '170px' } );
        $( Tests.testsContainer ).slimScroll( { position: 'left', height: '400px', allowPageScroll: false, width: '190px' } );
        $( Slideshows.slideshowContainer2 ).slimScroll( { position: 'left', height: '400px', allowPageScroll: false, width: '190px' } );
    }

    this.initAccordion = function () {
        $( '#accordionLeft #accordionContent' ).hide();
        $( '#accordionLeft #accordionContent:first' ).show();

        $( '#accordionLeft .colHeader .pull-left' ).bind( 'click', function () {
            if ( $( this ).parent().parent().next().is( ':hidden' ) ) {
                $( '#accordionLeft #accordionContent' ).slideUp();
                $( this ).parent().parent().next().slideDown();
            } else {
                $( this ).parent().parent().next().slideUp();
            }
            return false;
        } );
    }

}( window.slideEditor = window.slideEditor || {}, jQuery ));