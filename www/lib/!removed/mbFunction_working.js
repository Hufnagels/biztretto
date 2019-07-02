var openedClass = 'icon-eye-close opener';
var closedClass = 'icon-eye-open opener';
var scrollContainerHeight = 425;
var scrollContainerInnerHeight = 350;

var selectedClass = 'selected';
var xpos;
var ypos;

var sortTriggered = false;
var totalFiles, uploadLimit, maxFiles = 200;


var popoverContent = function ( data ) {
    var mediaUrl = data.attr( 'data-mediaurl' )
    mediaType = data.attr( 'data-mediatype' ),
        type = data.attr( 'data-category' );
    var html = '', res = [];
    res['result'] = [];
//console.log(data)
    switch ( type ) {
        case 'image':
            var img = new Image();
            img.src = mediaUrl;
            img.onload = function() {

                //alert(this.width + 'x' + this.height);
                //html = '<img src="' + mediaUrl + '"  style="width:'+this.width+'px; height:'+this.height+'px"/>';
                //return html;
            }
            html = '<img src="' + mediaUrl + '"  />';

            //return false;
            break;
        case 'video':
            if ( mediaType == 'remote' ) {
                var video = parseVideoURL( mediaUrl );
                html = '<iframe width="292px" height="150px" src="http://www.youtube.com/embed/' + video.id + '?fs=1&feature=oembed" frameborder="0" allowfullscreen></iframe>';
            } else if ( mediaType == 'local' ) {
                var str = mediaUrl.substring( 0, mediaUrl.lastIndexOf( "." ) );//split('.mp3');
                res['result'].push( {
                    'name': data.attr( 'data-name' ),
                    'poster': data.find( 'img' ).attr( 'src' ),
                    'videoWidth': data.attr( 'data-video-width' ),
                    'videoHeight': data.attr( 'data-video-height' )
                } );
                html = tmpl( "tmpl-video", res );
            }
            break;
        case 'audio':
            var str = mediaUrl.substring( 0, mediaUrl.lastIndexOf( "." ) );//split('.mp3');
            res['result'].push( { 'name': str } );
            html = tmpl( "tmpl-audio", res );
            break;
    }
    return html;
};

var popoveroptions = {
    placement: function ( context, source ) {
//console.log( $( source ).offset() )
        var position = $( source ).offset();
        if ( position.left < 300 ) {
            return "right";
        } else if ( position.left > 900 ) {
            return "left";
        }
        if ( position.top < 300 ) {
            return "bottom";
        }
        return "top";
    },
    trigger  : "click",
    html     : true,
    title    : function () {
        return $( this ).closest( 'li.mediaElement' ).attr( 'data-name' );
    },
    content  : function () {
        return popoverContent( $( this ).closest( 'li.mediaElement' ) );
    },
    selector : document.body
};


(function ( MyMedia, $, undefined ) {

    var myMediaElements = {
            form     : document.getElementById( "fileupload" ) || '',
            mediaList: document.getElementById( "myMediaList" ) || ''
        },

        mediaGroupElements = {
            input    : document.getElementById( 'newMBName' ) || '',
            addButton: document.getElementById( 'createNew' ) || '',
            groupList: document.getElementById( 'mediaBoxList' ),
            container: document.getElementById( 'myMediaBoxesContainer' ) || '',
            groups   : document.getElementsByClassName( 'mbcholder' ) || ''
        },

        diskAreaElements = {
            diskAreaButton: document.getElementById( 'diskAreaButton' ) || ''

        },

        sortingElements = {
            $sortbyButtons   : $( '#sortBy > button' ) || '',
            $sortby          : $( '#sortBy' ) || '',
            $wordFilterButton: $( '#filterWord' ) || '',
            $orderButtons    : $( '#sortOrder > button' ) || '',
            $order           : $( '#sortOrder' ) || '',
            $filterButtons   : $( '#filters > button' ) || '',
            order            : 'asc',
            init             : function () {
                this.$wordFilterButton.bind( 'keyup', function () {
                    if ( $( this ).val().length < 2 ) {
                        $( myMediaElements.mediaList ).find( 'li' ).removeClass( 'hiddenClass' );
                        return false;
                    }
                    var searchText = $( this ).val().toLowerCase();
                    $( myMediaElements.mediaList ).find( 'li' ).filter(function () {
                        return $( this ).attr( 'data-name' ).toLowerCase().indexOf( searchText ) == -1;
                    } ).addClass( 'hiddenClass' );
                    sortingElements.sort();
                } );

                this.$filterButtons.bind( 'click', function ( e ) {
                    e.preventDefault();
                    $( this ).hasClass( 'active' ) ? $( this ).removeClass( 'active' ) : $( this ).addClass( 'active' );
                    $( myMediaElements.mediaList ).find( 'li' ).removeClass( 'hiddenClass' )
                    var type = $( this ).attr( 'isotope-data-filter' ).replace( /\./g, '' );
//console.log( type )
                    if ( type !== 'all' ) {
                        var elements = $( myMediaElements.mediaList ).find( 'li' ).filter( function () {
                            return type.indexOf( $( this ).attr( 'data-category' ) ) == -1
                        } )
                        elements.toggleClass( 'hiddenClass' );
                    }
                    sortingElements.sort();
                } );

                this.$sortbyButtons.bind( 'click', function ( e ) {
                    e.preventDefault();
                    $( sortingElements.$sortby ).find( 'button' ).removeClass( 'active' );
                    $( this ).addClass( 'active' );
                    sortingElements.sort();
                } )

                this.$orderButtons.bind( 'click', function ( e ) {
                    e.preventDefault();
                    sortingElements.$order.find( 'button' ).removeClass( 'active' );
                    $( this ).addClass( 'active' );
                    sortingElements.sort();
                } )
                sortingElements.sort();
            },
            sort             : function () {

                //$('#detailRow').remove();
                var att = $( '#sortBy' ).find( '.btn.active' ).attr( 'data-option-value' ); //name/date
                var order = $( '#sortOrder' ).find( '.btn.active' ).attr( 'data-option-value' ); //asc/desc

                var filterArray = [];
                sortingElements.$filterButtons.filter(function () {
                    return !$( this ).hasClass( 'active' )
                } ).each( function () {
                        filterArray.push( 'colorBar ' + $( this ).attr( 'data-option-value' ) )
                    } );
                $( myMediaElements.mediaList ).find( 'li > .colorBar' ).filter(function () {
                    return filterArray.indexOf( this.className ) !== -1;
                } ).parent().addClass( 'hiddenClass' );
//console.log($(myMediaElements.mediaList).find('li.mediaElement > .colorBar').filter(function(){ return filterArray.indexOf(this.className);}).attr('class'))
                $( myMediaElements.mediaList )
                    .find( 'li:not(.hiddenClass, .hidden)' )
                    .tsort( {attr: 'data-' + att, order: order} );
                $( myMediaElements.mediaList )
                    .find( 'li.hiddenClass, li.mediaElement.hidden' )
                    .appendTo( myMediaElements.mediaList );
            }
        },

        uploadElements = {
            uploadWidget      : document.getElementById( "fileupload" ) || '',
            container         : document.getElementById( "uploadContainer" ) || '',
            addButton         : document.getElementById( "addFiles" ) || '',
            uploadFinishButton: document.getElementById( "uploadFinish" ) || '',
            addVideoLinkButton: document.getElementById( 'addVideoLink' ) || '',
            filesLeft: document.getElementById( 'filesLeft' ) || '',
            $content          : $( "#content" ) || '',
            messages          : {
                0: 'Upload & conversation in progress...',
                1: 'Loading in progress...'
            },

            init: function () {
                this.initUploadWidget();
                this.initAddFilesButton();
                this.setUploadLimit();
                this.$content.inner = $( this.$content ).find( '.inner' );
                $( this.$content ).on( 'transitionEnd webkitTransitionEnd transitionend oTransitionEnd msTransitionEnd', function ( e ) {
                    if ( uploadElements.$content.hasClass( 'open' ) ) {
                        uploadElements.$content.css( 'max-height', '360px' );
                    }
                } );
            },

            initAddFilesButton: function () {

                addEventO( this.addButton, 'click', function ( e ) {
                    uploadElements.$content.toggleClass( 'open closed' );
                    uploadElements.$content.contentHeight = uploadElements.$content.outerHeight();
                    if ( uploadElements.$content.hasClass( 'closed' ) ) {
                        uploadElements.$content.removeClass( 'transitions' ).css( 'max-height', uploadElements.$content.contentHeight );
                        setTimeout( function () {
                            uploadElements.$content.addClass( 'transitions' ).css( {'max-height': 0, 'opacity': 0} );
                        }, 10 );
                    } else if ( uploadElements.$content.hasClass( 'open' ) ) {
                        uploadElements.$content.contentHeight += uploadElements.$content.inner.outerHeight();
                        uploadElements.$content.css( {'max-height': uploadElements.$content.contentHeight, 'opacity': 1} );
                    }
                    $( '#loading' ).hide().find( '.loadingMessage' ).text( uploadElements.messages[1] );
                }, true, _eventHandlers );

                addEventO( this.uploadFinishButton, 'click', function ( e ) {
                    if ( $( uploadElements.container ).find( 'li.template-download' ).length == 0 ) {
                        $( uploadElements.addButton ).trigger( 'click' );
                        return false;
                    }

                    $( '#loading' ).hide().find( '.loadingMessage' ).text( uploadElements.messages[1] );

                    var data = [];

                    $( uploadElements.container ).find( 'li.template-download' ).each( function ( i, e ) {
                        $( this ).filter( function () {
                            return !$( this ).hasClass( 'error' ) ? data.push( {
                                'type'         : $( this ).attr( 'data-category' ),
                                'mediatype'    : $( this ).attr( 'data-mediatype' ),
                                'name'         : $( this ).attr( 'data-name' ),
                                'uploaded_ts'  : $( this ).attr( 'data-uploaded' ),
                                'uploaded'     : $( this ).find( 'span.uploaded' ).text(),
                                'thumbnail_url': $( this ).find( 'img' ).attr( 'src' ),
                                'mediaurl'     : $( this ).attr( 'data-mediaurl' ),
                                'additional'   : $( this ).find( 'span.additional' ).text(),
                                'size'         : $( this ).attr( 'data-video-width' ) + ',' + $( this ).attr( 'data-video-height' )
                            } ) : '';
                        } );

                    } );

                    if($( uploadElements.container ).find( 'li.template-download').length > 0){
                        uploadElements.saveMediaFiles(data, 'local');
                        $( uploadElements.container ).empty();
                    }

                    uploadElements.setUploadLimit();

                    $( uploadElements.addButton ).trigger( 'click' );

                }, true, _eventHandlers );

                addEventO( this.addVideoLinkButton, 'click', function ( e ) {
                    if ( $( this ).hasClass( 'disabled' ) ) return false;
                    var isInserted = false;
                    var video = null;
                    var videoResult = {};
                    $( '#youtube' ).live( "blur", function () {//paste keyup attach
                        var url = $( this ).val();

                        video = parseVideoURL( url ); //parseVideoURL(url);
                        if(!video) return false;
                        if ( isInserted == false && video ) {
                            $( '#video' )
                                .html( '' )
                                .append( '<iframe width="100%" height="100%" src="'+video.urlType+'www.youtube.com/embed/' + video.id + '?fs=1&feature=oembed" frameborder="0" allowfullscreen></iframe>' );
                            videoResult = uploadElements.getYouTubeInfo( video.id );
                            isInserted = true;
                        }
                    } );

                    var data = [];
                    data['result'] = [];
                    data['result'].push( {'provider': '', 'id': '', 'title': '', 'desc': ''} );
                    var bodytext = tmpl( "tmpl-videolink", data, false );

                    $( "#confirmDiv" ).confirmModal( {
                        heading : 'Add video link',
                        body    : bodytext,
                        text    : 'Add video',
                        type    : 'question',
                        cancel  : true,
                        callback: function () {
                            var data = [], ts = Math.round( new Date().getTime() / 1000 ), date = new Date, uploaded;
                            uploaded = date.customFormat( "#YYYY#.#MM#.#DD#." );
                            data.push( {
                                'type'         : 'video',
                                'mediatype'    : 'remote',
                                'name'         : $( '#ytTitle' ).val(),//videoResult.title,
                                'uploaded_ts'  : ts,
                                'uploaded'     : uploaded,
                                'thumbnail_url': video.urlType+'img.youtube.com/vi/' + video.id + '/hqdefault.jpg',
                                'mediaurl'     : video.urlType+'www.youtube.com/watch?v=' + video.id + '/',
                                'duration'     : secondsToTime( videoResult.duration )
                            } );

                            uploadElements.saveMediaFiles(data, 'remote');
                            return false;
                        }
                    } );

                }, true, _eventHandlers );
            },

            saveMediaFiles : function(files, type){

                values = {};
                values['action'] = actions.mediafiles[3];
                values['type'] = type;
                values['form'] = $( myMediaElements.form ).serializeArray();
                values['diskArea'] = diskArea.diskAreaId;
                values['files'] = files;
                settings.url = "/crawl?/process/mymedia/handelmediafiles/";
                settings.data = values;

                var data = getJsonData( settings );
                if ( !data )
                    return false;

                $(myMediaElements.mediaList).append(tmpl("tmpl-mediaElement", data));
                sortingElements.sort();
                $(myMediaElements.mediaList).find('.detailButton').clickover(popoveroptions);
                this.setUploadLimit();

            },

            setUploadLimit : function(){
                totalFiles = $(myMediaElements.mediaList ).find('li.mediaElement').length;
                uploadLimit = (maxFiles - totalFiles) > 0 ? maxFiles - totalFiles : 0;
                $( uploadElements.uploadWidget ).fileupload('option', 'maxNumberOfFiles', uploadLimit);
                this.filesLeft.innerHTML = uploadLimit;
            },

            destroyUploadWidget: function () {
                $( uploadElements.uploadWidget ).fileupload( 'destroy' );
            },

            initUploadWidget: function () {
                $( uploadElements.uploadWidget ).fileupload( {
                    url           : '/crawl?/process/upload/',
                    filesContainer: $( uploadElements.container )
                } );
                $( uploadElements.uploadWidget ).fileupload( 'option', {
                    maxNumberOfFiles: 1,
                    url             : '/crawl?/process/upload/',
                    maxFileSize     : 1200000000,
                    // The maximum width of the preview images:
                    previewMaxWidth : 150,
                    // The maximum height of the preview images:
                    previewMaxHeight: 100,
                    acceptFileTypes : /(\.|\/)(gif|jpe?g|png|pdf|xls?|doc?|swf|ogg|flv|mp4|wmv|mp3|ppt?)$/i,
                    process         : [
                        {
                            action     : 'load',
                            fileTypes  : /^image\/(gif|jpeg|png)$/,
                            maxFileSize: 20000000 // 20MB
                        },
                        {
                            action   : 'resize',
                            maxWidth : 1920,
                            maxHeight: 1200,
                            minWidth : 800,
                            minHeight: 600
                        },
                        { action: 'save' }
                    ]
                } ).bind( 'fileuploadadd',function ( e, data ) {
                        //$(this).fileupload('option', 'maxNumberOfFiles')

                    } ).bind( 'fileuploadchange',function ( e, data ) {
                        $.each( data.files, function ( index, file ) {
                            var matches = file.type.match( /(word|excel|powerpoint|pdf)/gi );
                            data.files[index].typeShort = matches;
                        } );
                        calculateFreeSpace( data.files, 1338 );
                    } ).bind( 'fileuploadsubmit',function ( e, data ) {
                        $( '#loading' ).show().find( '.loadingMessage' ).text( uploadElements.messages[0] )
                    } ).bind( 'fileuploaddone',function ( e, data ) {
                        $( '#loading' ).hide().find( '.loadingMessage' ).text( uploadElements.messages[1] );
                    } ).bind( 'fileuploadstop',function ( e ) {
                        $( '#loading' ).hide().find( '.loadingMessage' ).text( uploadElements.messages[1] );
                    } ).bind( 'fileuploadfail', function ( e, data ) {
                        $( '#loading' ).hide().find( '.loadingMessage' ).text( uploadElements.messages[1] );
                    } );

            },

            getYouTubeInfo: function ( video_id ) {
                var isRecevied = false;
                var media = {};
                $.ajaxSetup( {async: false} );
                $.getJSON( 'http://gdata.youtube.com/feeds/api/videos/' + video_id + '?v=2&alt=jsonc', function ( data ) {
                    media.title = data.data.title;
                    media.desc = data.data.description;
                    media.author = data.data.uploader;
                    media.updated = data.data.updated;
                    media.duration = data.data.duration;
                    uploadElements.parseresults( data );
                } );
                return media;
            },
            parseresults  : function ( data ) {
                $( '#ytTitle' ).val( data.data.title );
                $( '#ytDesc' ).val( data.data.description );
                $( '#ytAuthor' ).val( data.data.uploader );
                $( '#ytUploaded' ).val( data.data.updated );
            }
        },

        optionElements = {
            buttonGroup  : document.getElementsByClassName( 'optionButtons' ) || '',
            selectedCount: document.getElementById( 'selectedCount' ) || '',
            $optionButton: $( '.optionButtons' ).find( 'button' ) || '',
            init         : function () {
                this.$optionButton.bind( 'click', function () {
                    if ( $( this ).hasClass( 'disabled' ) ) return false;
                    var action = $( this ).attr( 'data-option-value' );
                    var selectedData = []
                    $( myMediaElements.mediaList ).find( 'li.selected:not(.hidden, hiddenClass)' ).each( function () {
                        selectedData.push( {'id':$( this ).attr( 'data-id' ), 'name':$( this ).attr( 'data-name' )} )
                    });

                    var selected = $( myMediaElements.mediaList ).find( 'li.selected:not(.hidden, .hiddenClass)' );
                    optionElements.setAction( action, selected, selectedData );

                    //$myusers.find('li.mediaElement.selected').removeClass('selected').find('.icon-ok').removeClass('icon-white');
                    mediaFiles.countSelected();
                } )
            },

            setAction    : function ( action, elements ,selectedData) {
                switch ( action ) {
                    case 'delete':
                        mediaFiles.deleteFiles(elements, selectedData)
                        //deleteTrash( elements );
                        break;
                    case 'removefrom':
                        mediaFiles.removeFromGroup(elements, selectedData)
                        break;
                }
            }
        },

        selectionMenuElements = {
            selectionMenu: document.getElementById('selectionMenu') || '',
            select: function (entry) {
                switch (entry.attr('class')) {
                    case 'selectall' :
                        $(myMediaElements.mediaList).find('li.mediaElement:not(.hiddenClass, .hidden)').addClass('selected').find('.icon-ok').addClass('icon-white');
                        break;
                    case 'deselectall' :
                        $(myMediaElements.mediaList).find('li.mediaElement:not(.hiddenClass, .hidden)').removeClass('selected').find('.icon-ok').removeClass('icon-white');
                        break;
                    case 'invertselection' :
                        $(myMediaElements.mediaList).find('li.mediaElement:not(.hiddenClass, .hidden)').toggleClass('selected').find('.icon-ok').toggleClass('icon-white');
                        break;
                }
                mediaFiles.countSelected();
                //mediaFiles.addDND();
            }

        },

        values = {},

        settings = {
            url         : "",
            data        : values,
            responseType: 'json'
        },

        actions = {
            mediafiles: {0: 'load', 1: 'delete', 2: 'rename', 3: 'add', 4: 'addto', 5: 'removefrom'},
            groups    : {0: 'load', 1: 'delete', 2: 'rename', 3: 'add'},
            diskArea  : {0: 'load', 1: 'delete', 2: 'rename', 3: 'new'}
        },

        preventObject = {
            0: mediaGroupElements.input,
            1: mediaGroupElements.input
        },

        affixElements = {
            0: mediaGroupElements.container
        },
        totalFiles = 0,
        maxFiles = 200,
        uploadLimit = 200;

    var mediaFiles = {

        init: function () {
            this.viewFiles();
            this.addDND();
        },

        viewFiles: function () {
            values = {};
            values['action'] = actions.mediafiles[0];
            values['form'] = $( myMediaElements.form ).serializeArray();
            values['diskArea'] = diskArea.diskAreaId;

            settings.url = "/crawl?/process/mymedia/handelmediafiles/";
            settings.data = values;
            var data = getJsonData( settings );
            if ( !data )
                return false;

            $( myMediaElements.mediaList ).append( tmpl( "tmpl-mediaElement", data ) );
            uploadElements.setUploadLimit();

            $(myMediaElements.mediaList).find('.selectButton').live('click', function () {
                var entry = $(this).closest('li.mediaElement');
                mediaFiles.selectFiles(entry);
            });

            $(myMediaElements.mediaList).find('.detailButton').clickover(popoveroptions);
        },

        selectFiles: function (entry) {
            entry.find('.icon-ok').toggleClass('icon-white');
            entry.toggleClass('selected');
            this.countSelected();
            this.addDND();
        },

        countSelected: function () {
            var count = $(myMediaElements.mediaList).find('li.selected').length;
            $(optionElements.selectedCount).text(count);

            if(!count){
                optionElements.$optionButton.addClass('disabled');
                $(mediaGroupElements.groupList ).find('li.selected' ).attr('data-object-id')=="viewAll" ?
                    $(optionElements.buttonGroup ).find('button[data-option-value="removefrom"]' ).addClass('disabled') :
                    $(optionElements.buttonGroup ).find('button[data-option-value="removefrom"]' ).removeClass('disabled');
            }else{
                optionElements.$optionButton.removeClass('disabled');
                $(mediaGroupElements.groupList ).find('li.selected' ).attr('data-object-id')=="viewAll" ?
                    $(optionElements.buttonGroup ).find('button[data-option-value="removefrom"]' ).addClass('disabled') :
                    $(optionElements.buttonGroup ).find('button[data-option-value="removefrom"]' ).removeClass('disabled');
            }

        },

        deleteFiles: function (trashElements, selectedData) {
            var bodytext,
                trashE = [],
                findedElements = [];
            findedElements['result'] = []
            //collect trash elements data
            $.each(trashElements, function(i,e){
                trashE.push({
                    'name': $(e).attr('data-name'),
                    'id':$(e).attr('data-id'),
                    'type':$(e).attr('data-category') ,
                    'mediatype':$(e).attr('data-mediatype'),
                    'mediabox':$(e ).attr('data-mediabox')
                });
            });

            for(var i=0;i< trashE.length;i++){
                findedElements['result'].push({
                    'mediaelement':trashE[i].name,
                    'id': trashE[i].id,
                    'textid':trashE[i].name.toLowerCase().latinize().substring(0,4),
                    'mediatype':trashE[i].mediatype,
                    'type' : trashE[i].type,
                    'groupname': clearNULL($(mediaGroupElements.groupList).find('li[data-object-id="'+trashE[i].mediabox+'"]' ).attr('data-object-name')),
                    'did': diskArea.diskAreaId
                });
            }

            findedElements['result'].sort(dynamicSort('textid'));

            var checkResult = mediaFiles.checkBeforeDelete( findedElements['result']);

            if(!checkResult)
                bodytext = tmpl("tmpl-emptytrash", findedElements);
            //bodytext = tmpl("tmpl-emptytrash2", checkResult);
            else{
                bodytext = tmpl("tmpl-emptytrash", findedElements);
                bodytext += tmpl("tmpl-emptytrash2", checkResult);
            }

            $("#confirmDiv").confirmModal({
                heading: 'Delete selected media',
                body: bodytext,
                text:'Delete',
                cancel: true,
                callback: function () {

                    values = {};
                    values['action'] = actions.mediafiles[1];
                    values['form'] = $( myMediaElements.form ).serializeArray();
                    values['files'] = findedElements['result'];
                    values['slides'] = checkResult['result'];
                    settings.url = "/crawl?/process/mymedia/handelmediafiles/";
                    settings.data = values;
                    var data = getJsonData( settings );
                    //if ( !data )
                    //    return false;

                    for (var i=0, file; file=findedElements['result'][i]; i++) {
                        $(myMediaElements.mediaList ).find('li[data-id="'+file.id+'"]' ).remove();
                    }
                    sortingElements.sort();
                    mediaFiles.countSelected();
                    uploadElements.setUploadLimit();

                }
            });

        },

        checkBeforeDelete : function(elements){
            values = {};
            values['check'] = elements;
            values['form'] = $( myMediaElements.form ).serializeArray();
            settings.url = "/crawl?/process/mymedia/checkfiles/";
            settings.data = values;
            var data = getJsonData( settings );
            return data;
        },

        addToGroup : function(entry, elements, selectedData){
            values = {};
            values['action'] = actions.mediafiles[4];
            values['form'] = $( myMediaElements.form ).serializeArray();
            values['files'] = selectedData;
            values['groupid'] = entry;
            settings.url = "/crawl?/process/mymedia/handelmediafiles/";
            settings.data = values;
            var data = getJsonData( settings );
            if ( !data )
                return false;

            $.each(elements, function(){
                $(this ).attr('data-mediabox', entry)
            })

        },

        removeFromGroup : function(elements, selectedData){
            values = {};
            values['action'] = actions.mediafiles[5];
            values['form'] = $( myMediaElements.form ).serializeArray();
            values['files'] = selectedData;
            settings.url = "/crawl?/process/mymedia/handelmediafiles/";
            settings.data = values;
            var data = getJsonData( settings );
            if ( !data )
                return false;

            $.each(elements, function(){
                $(this ).attr('data-mediabox', '0')
            })
            var entry = $(mediaGroupElements.groupList ).find('li.mbcholder.selected');
            mediaGroups.listSelectedGroupFiles(entry.attr('data-object-id'));
        },

        addDND: function () {

            $( myMediaElements.mediaList ).find( 'li.mediaElement:not(.hidden, .hiddenClass)' ).draggable( this.myMediaDragOption );
        },

        myMediaDragOption: {
            appendTo: 'body',
            distance: 10,
            handle  : ".thumbnail",
            greedy  : true,
            revert  : true,
            opacity:0.7,
            helper  : function ( event, ui ) {
                $( this ).addClass( 'selected' );
                var selected = $( myMediaElements.mediaList ).find( 'li.selected:not(.hidden, .hiddenClass)' );
                if ( selected.length === 0 || selected.length === 1 ) {

                    selected = $( this );
                }
                mediaFiles.countSelected();
                if ( $( '#draggingContainer2' ).length === 0 )
                    var container = $( '<ul class="span2 thumbnails" />' ).appendTo( 'body' ).attr( 'id', 'draggingContainer2' );
                else {
                    container = $( '#draggingContainer2' );
                    container.empty();
                }
                container.append( '<li class="mediaElement span2 lightgreyB"><div class="thumbnail"><h1>' + selected.length + ' selected</h1></div></li>' ).css( {'position': '', 'left': '', 'top': '', 'z-index': '10000'} );
                //$('li', container).css({'position': '', 'left': '', 'top': '', 'z-index': '10000'});
                return container;
            },

            start   : function ( event, ui ) {
                $( '#draggingContainer2' ).css( {'position': 'absolute'} );
            },

            stop    : function ( event, ui ) {

                var pos = ui.position;
                pos = Math.abs( pos.left );
            }
        }

    };

    var diskArea = {

        messages  : {
            0: 'Fatal error! Main data is missing. Please reload the page'
        },
        diskAreaId: '',

        setDiskAreaId: function ( id ) {
            this.diskAreaId = id;
            //return diskArea.diskAreaId;
        },

        checkDiskAreaId: function () {
            this.setDiskAreaId( $( myMediaElements.form ).find( 'input[name="diskArea_id"]' ).val() );
            if ( this.diskAreaId === '' ) {
                sendMessage( 'alert-error', this.messages[0] );
                MyMedia.destroy();
                return false;
            } else {
                return diskArea.diskAreaId;
            }
        },

        init: function () {
            this.checkDiskAreaId();
            this.load();
            this.changeDiskArea();
        },

        load: function () {
            values = {};
            values['action'] = actions.diskArea[0];
            values['form'] = $( myMediaElements.form ).serializeArray();

            settings.url = "/crawl?/process/mymedia/loadfolder/";
            settings.data = values;
            var data = getJsonData( settings );
            if ( !data )
                return false;
            $( '.selectediskArea li.area' ).remove().end();
            var html = tmpl( "tmpl-newDA", data );
            var divider = $( '.selectediskArea li.divider' );//.find('.divider');
            $( html ).insertBefore( divider );
            $( '.selectediskArea [data-id="' + diskArea.diskAreaId + '"]' ).parent().addClass( 'selected' );
        },

        createNew: function () {

        },

        delete: function () {

        },

        changeDiskArea: function () {
            $( '.selectediskArea a' ).live( 'click', function ( e ) {
                e.preventDefault();
                e.stopPropagation();
                var buttonSortname = $( diskAreaElements.diskAreaButton ).attr( 'data-sortname' );
                var sortname = $( this ).attr( 'data-sortname' );
                if ( sortname == buttonSortname ) return false;
                $( '#bc' ).empty();
                var id = $( this ).attr( 'data-id' );
                diskAreaElements.diskAreaButton.firstChild.nodeValue = $( this ).text();
                $( this ).parent().siblings().removeClass( 'selected' );
                $( this ).parent().toggleClass( 'selected' );
                $( '#diskAreaButton' ).attr( 'data-sortname', sortname );
                $( myMediaElements.form ).find( 'input[name="diskArea_id"]' ).val( id );
                $( myMediaElements.form ).find( 'input[name="diskArea_name"]' ).val( sortname.replace( /#/g, '' ) )

                diskArea.setDiskAreaId( id );
            } );
        }
    };

    var mediaGroups = {

        init: function () {
            this.viewGroups();
            this.addDND();
            addEventO(mediaGroupElements.addButton, 'click', function () {
                var entry = {
                    groupName: mediaGroupElements.input.value || '',
                    post: actions.groups[3]
                };
                mediaGroups.addGroup(entry);
            }, true, _eventHandlers)
        },

        viewGroups: function (entry) {
            values = {};
            values['action'] = actions.groups[0];
            values['form'] = $( myMediaElements.form ).serializeArray();
            values['diskArea_id'] = diskArea.diskAreaId;
            settings.url = "/crawl?/process/mymedia/handelmediagroups/";
            settings.data = values;
            var data = getJsonData( settings );
            if ( !data )
                return false;
            $( mediaGroupElements.groupList ).html( '<li class="viewAll mbcholder span2 selected" data-object-id="viewAll"><div class="mbHeader"><div class="name">view all</div><div class="pointer-right"></div></div></li>' )
            $( mediaGroupElements.groupList ).append( tmpl( "tmpl-mediaBoxList", data ) );

            this.initGroupSelection();
        },

        addGroup : function(entry){
            if (entry.groupName == '') {
                sendMessage('alert-warning', 'Give some text')
                return false;
            }
            values = {};
            values['action'] = entry.post,
                values['form'] = $(myMediaElements.form).serializeArray();
            values['groupname'] = entry.groupName;
            settings.url = "/crawl?/process/mymedia/handelmediagroups/";
            settings.data = values;
            var data = getJsonData(settings);

            if (data) {
                mediaGroupElements.input.value = '';
                var group = [];
                group['result'] = [];
                group['result'].push({
                    'id': data.result.id,
                    'name': entry.groupName,
                    'doname': convertDoname(entry.groupName),
                    'badge': 0
                });

                var resHtml = tmpl("tmpl-newMediaBox", group, true);
                $(mediaGroupElements.groupList).append(resHtml);
                $(mediaGroupElements.groupList).find('li.mediaBox').tsort({attr: 'data-object-name'});
                this.addDND();
                this.initGroupSelection();
            }
        },

        initGroupSelection : function(){
            $(mediaGroupElements.groupList).find('li.mbcholder').die('click');
            $(mediaGroupElements.groupList).find('li.mbcholder').live('click', function(){
                mediaGroups.selectGroup($(this));
            })
        },

        selectGroup : function(entry){
            if(entry.hasClass('selected')) return false;
            entry.siblings().removeClass('selected');
            entry.addClass('selected');
            this.listSelectedGroupFiles(entry.attr('data-object-id'));
            mediaFiles.countSelected();
            this.editGroup(entry);

        },

        editGroup : function(entry){
            $.fn.editable.defaults.url = '/crawl?/process/mymedia/handelmediagroups/';
            $.fn.editable.defaults.params = function (params) {
                params.action = 'rename';
                params.form = $(myMediaElements.form).serializeArray()
                return params;
            };
            var mgId = entry.attr('data-object-id');
            var obj = entry.find('div.name');
            var mgName = obj.text();

            if (mgId == 'viewAll') {
                $('#bc').html('');
                return false;
            }

            $('#bc').html('<span class="divider pull-left">>&nbsp;</span>'+
                '<span class="pull-left"><a class="editable" id="mbName_'+mgId+'" data-type="text" data-pk="'+mgId+'">' +mgName+'</a></span>'+
                '<span class="btn pull-left btn-empty" id="deleteGrp" data-id="' + mgId + '"><i class="icon-trash"></i></span>');

            this.removeGroup(document.getElementById('deleteGrp'));

            $('#mbName_'+mgId).editable({
                success: function (response, newValue) {
                    if (newValue.match(/^\s+$/) === null && newValue.length === 0) {
                        sendMessage('alert-error', 'Give a name');
                        return false;
                    }
                    entry.attr('data-object-name', convertDoname(newValue));
                    entry.find('div.name').text(newValue);
                    sendMessage('alert-' + response.type, response.message);
                }
            });
        },

        removeGroup: function (entry) {
            addEventO(entry, 'click', function () {
                $("#confirmDiv").confirmModal({
                    heading: 'Question',
                    body: 'Do you really want to delete this mediabox?',
                    type: 'question',
                    text: 'Delete',
                    cancel: true,
                    callback: function () {
                        values = {};
                        values['action'] = actions.groups[1],
                            values['form'] = $(myMediaElements.form).serializeArray();
                        values['id'] = $(entry).attr('data-id');
                        settings.url = "/crawl?/process/mymedia/handelmediagroups/";
                        settings.data = values;
                        var data = getJsonData(settings);

                        if (data) {
                            $('#bc').html('');
                            $(myMediaElements.mediaList).find('li.mediaElement').removeClass('hidden');
                            $(mediaGroupElements.groupList).find('li.selected').remove();
                            $(mediaGroupElements.groupList).find('li[data-object-name="viewAll"]').addClass('selected');
                            $(mediaGroupElements.groupList).find('li[data-mediabox="'+$(entry).attr('data-id')+'"]').attr('data-mediabox','0');
                        }

                    }
                });

            }, false, _eventHandlers)

        },

        listSelectedGroupFiles : function(id){
            if(id == 'viewAll'){
                $(myMediaElements.mediaList).find('li.mediaElement').removeClass('hidden');
                sortingElements.sort();
            } else {
                $(myMediaElements.mediaList).find('li.mediaElement').removeClass('hidden').filter(function(){
                    return $(this).attr('data-mediabox') !== id;
                }).addClass('hidden')
            }
            sortingElements.sort();
        },

        addDND    : function () {
            $( mediaGroupElements.groupList ).find('li.mbcholder').droppable( this.mediaBoxDropOption );
        },

        mediaBoxDropOption: {
            tolerance : 'pointer',
            accept    : "#myMediaList li",
            //activeClass: "ui-state-highlight",
            hoverClass: "ui-state-highlight",
            greedy    : true,
            drop      : function ( e, ui ) {
                if($(this).hasClass('selected')) return false;

                var selectedData = []
                $( myMediaElements.mediaList ).find( 'li.selected:not(.hidden, hiddenClass)' ).each( function () {
                    selectedData.push( {'id':$( this ).attr( 'data-id' ), 'name':$( this ).attr( 'data-name' )} )
                });

                var selected = $( myMediaElements.mediaList ).find( 'li.selected:not(.hidden, .hiddenClass)' );

                mediaFiles.addToGroup($(this ).attr('data-object-id'),selected,selectedData);
            }
        }
    };

    MyMedia.init = function () {
        $( '#loading' ).show();

        for ( var key in preventObject ) {
            $( preventObject[key] ).preventEnter();
        };

        initAffix(affixElements);

        diskArea.init();

        uploadElements.init();

        optionElements.init();

        $( uploadElements.container ).slimScroll( { height : '237px', allowPageScroll: false } );

        $( mediaGroupElements.groupList ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '190px' });

        mediaGroups.init();

        mediaFiles.init();

        mediaFiles.countSelected();

        sortingElements.init();

        $(selectionMenuElements.selectionMenu).find('a').bind('click', function (e) {
            e.preventDefault();
            selectionMenuElements.select($(this));
        });

        $( '#loading' ).hide();

        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.dataType = 'json';
        $.fn.editable.defaults.emptytext = 'Please, fill this';
        $.fn.editable.defaults.url = '';

        scrollToWorkingArea($('.row.special'));
    };

    MyMedia.destroy = function () {
        uploadElements.destroyUploadWidget();
        delete window.MyMedia;
    }

}( window.MyMedia = window.MyMedia || {}, jQuery ));