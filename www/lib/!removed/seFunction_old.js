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


$.extend( $.ui.resizable.prototype, (function ( orig ) {
    return {
        _mouseStart: function ( event ) {
            this._aspectRatio = !!(this.options.aspectRatio);
            return(orig.call( this, event ));
        }
    };
})( $.ui.resizable.prototype["_mouseStart"] ) );




//drop image,video, audio elements
var prevOffset = true,
    curOff = false;

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
                    $( '#bc' ).html( '<span class="divider">>&nbsp;</span><span class="editable" id="ssName" data-type="text">' + _this.find( 'span.sname' ).text() + '</span>' );
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

////////////////////////////////////////////
// create text on editor area at mouse position
// attach wordprocessor to it
////////////////////////////////////////////


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