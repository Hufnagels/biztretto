
(function (slideEditor, $, undefined) {

    var mainElements = {
            leftContainer : document.getElementById( "mySlideshowsContainer" ) || '',
            mainContainer : document.getElementById( "slideEditor" ) || '',
            previewContainer : document.querySelector( "div.preview" ) || '',
            previewIframe :  document.getElementById( "previewIframe" ) || '',
            form : document.getElementById( "saveslidesform" ) || '',

            slideshowContainer : document.getElementById( "slidesList" ) || '',
            allslideshowContainer : document.getElementById( "slidesList2" ) || '',

            mymediaContainer : document.getElementById( "editorsMediaBox" ) || '',
            testsContainer : document.getElementById( "testSlides" ) || '',
            tempDiv: document.getElementById( "tempDiv" ) || '',
            matrixHolder : '<span id="play" style=""><span class="rotate">rotate</span>		<span class="scale">scale</span><span class="skewx">skew</span><span class="skewy">skew</span><span class="move">move</span></span>' || '',
            matrixWrapper : '<span id="play" style=""></span>' || '',
            matrixWrapperButtons : '<span class="rotate">rotate</span>		<span class="scale">scale</span><span class="skewx">skew</span><span class="skewy">skew</span><span class="move">move</span>' || ''
        },

        slideshowElements = {

            loadSlideshowContainer: document.getElementById('loadSlideshow') || '',
            loadSlideshowButton: document.getElementById('loadSlideShowButton') || '',

            init:function(){
                this.viewList();
            },

            selectFolder : function(){
                //folder select
                $( '#themeList a' ).bind( 'click', function ( e ) {
                    //e.stopPropagation();
                    if ( $( this ).parent().hasClass( 'selected' ) ) return false;
                    var _daMG = $( this ).closest( '#themeList' ).prev( '.selectMenu' );
                    _daMG.find( 'span.name' ).text( $( this ).text() );
                    var folderName = $( this ).text();
                    folderName = folderName.length > 12 ? folderName.substring( 0, 11 ) + '...' : folderName;
                    _daMG.find( 'span.name' ).text( folderName ).attr( 'title', $( this ).text() );
                    //$(this).closest('#themeList').prev('.selectMenu').
                    _daMG.attr( 'data-diskarea-id', $( this ).attr( 'data-diskarea-id' ) );
                    $( this ).closest( '#themeList' ).find( 'li' ).removeClass( 'selected' ).end();
                    $( this ).parent().addClass( 'selected' );
                    if ( $( this ).closest( '#themeList' ).prev( '.selectMenu' ).attr( 'id' ) == 'daForSlideShow' ) {
                        $( '#saveslidesform input[name="diskArea"]' ).val( $( this ).attr( 'data-diskarea-id' ) );
                        Editor.helperFunctions.se_clearData( 'all' );
//loadSlideShowList();
                    } else
//loadMediaGroups();//sample data
                        $( this ).closest( '.pull-right' ).removeClass( 'open' );
                    return false;
                } );
            },

            viewList:function(){
                defaults.loadedSlideshows = this.helperFunctions.handelSlideshow('',1);
                if(!defaults.loadedSlideshows)
                    return false;
                $( slideshowElements.loadSlideshowContainer ).empty().append( tmpl( "tmpl-lsslist", defaults.loadedSlideshows ) );
                $( slideshowElements.loadSlideshowButton ).find( 'span.name' ).text( 'select slideshow' );
                defaults.disabledSlideshows = $.map( $('li.disabled a', slideshowElements.loadSlideshowContainer), function (element) { return $(element).attr('data-id') });

                allslideshowsElements.viewList();

                this.setSlideshowAction();
            },

            setDisabled : function(entry){
                //slideshow list2 ellenőrzése

                var parentId = $( allslideshowsElements.loadSlideshowContainer2 ).find( 'li.selected a' ).attr( 'data-id' );
                if ( entry.attr( 'data-id' ) == parentId ) {
                    $( allslideshowsElements.loadSlideshowContainer2 ).empty();
                    $( allslideshowsElements.loadSlideshowButton2 ).find( 'span.name' ).text( 'select slideshow' ).attr( 'title', '' );
                }
                $( allslideshowsElements.loadSlideshowContainer2 ).find('a' ).each(function(){
                    $(this).removeClass( 'disabled' );
                });
                $( allslideshowsElements.loadSlideshowContainer2 ).find( 'a[data-id="' + entry.attr( 'data-id' ) + '"]' ).addClass( 'disabled' );
            },

            loadSelectedSlideshow:function(entry){
                defaults.selectedSlideshowId = entry.attr( 'data-id' );
                values = {};
                values['action'] = actions.slideshows[0];
                values['id'] = defaults.selectedSlideshowId;
                values['form'] = $(mainElements.form).serializeArray();
                settings.url = "/crawl?/process/editor/handelslideshow/";
                settings.data = values;
                var data = getJsonData(settings);
                if(!data)
                    return false;

                this.printSlide(entry, data);

            },

            printSlide : function(entry, data){
                var title = entry.find( 'span.sname' ).text(),
                    sName = title.length > 18 ? title.substring( 0, 15 ) + '...' : title,
                    id = entry.attr( 'data-id' );
                slideshowElements.helperFunctions.setFormData({
                    'id': id,
                    'name':title
                });

                $(slideshowElements.loadSlideshowButton).find( 'span.name' ).text( sName ).attr( 'title', title );
                entry.parent().siblings().removeClass( 'selected' );
                entry.parent().addClass( 'selected' );

                var resHtml = tmpl( "tmpl-miniSlides", data, true );
                $(mainElements.slideshowContainer).empty().append( resHtml );
                this.initSortable();
//recalculateScrollHeight( $('#slidesList') );
//                slidesHelperfunctions.storeslideIDs();
                defaults.slideSaved = true;

//                defaults.slideShowSaved = Editor.helperFunctions.checkFormState( 'saveslidesform' );

                entry.closest( '.open' ).removeClass( 'open' );
                //2013.01.19
                //Editor.helperFunctions.addUIevents();
                defaults.prevOffset = false;

                Editor.toolbarElements.initColorpicker();
                this.setSlideAction();
                //this.setTrafficAction();

                slideshowElements.setDisabled(entry);

                slideshowElements.handelEditable( entry );

            },

            handelEditable : function(entry){
                $('#bc').html('<span class="pull-left"><a class="editable" id="mbName_'+entry.attr( 'data-id' )+'" data-type="text" data-pk="'+entry.attr( 'data-id' )+'">' +entry.find( 'span.sname' ).text()+'</a></span>'+
                    '<span class="btn btn-empty btn-dark" id="deleteGrp" data-id="' + entry.attr( 'data-id' ) + '"><i class="icon-trash"></i></span>');

                var id = entry.attr( 'data-id' );
                $.fn.editable.defaults.url = '/crawl?/process/editor/handelslideshow/';
                $.fn.editable.defaults.params = function (params) {
                    params.action = 'rename';
                    params.form = $(mainElements.form).serializeArray()
                    return params;
                };

                this.removeGroup(document.getElementById('deleteGrp'));

                $('#mbName_'+id).editable({
                    success: function (response, newValue) {
                        if (newValue.match(/^\s+$/) === null && newValue.length === 0) {
                            sendMessage('alert-error', 'Give a name');
                            return false;
                        }
                        //entry.attr('data-object-name', convertDoname(newValue));
                        entry.find('span.name').text(newValue);
                        //allslideshowban is atirni
                        $(slideshowElements.loadSlideshowButton ).find('span.name' ).text(newValue);
                        $(slideshowElements.loadSlideshowContainer ).find('li.selected .sname' ).text(newValue);
                        slideshowElements.setDisabled(entry);
                        $(allslideshowsElements.loadSlideshowContainer2 )
                            .find('a[data-id="'+entry.attr( 'data-id' )+'"] > .sname').text(newValue);
                        //sendMessage('alert-' + response.type, response.message);
                    }
                });
            },

            removeGroup: function (entry) {
                addEventO(entry, 'click', function () {
                    $("#confirmDiv").confirmModal({
                        heading: 'Question',
                        body: 'Do you really want to delete this slideshow?',
                        type: 'question',
                        text: 'Delete',
                        cancel: true,
                        callback: function () {
                            values = {};
                            values['action'] = actions.slideshows[4],
                                values['form'] = $(mainElements.form).serializeArray();
                            values['id'] = $(entry).attr('data-id');
                            settings.url = "/crawl?/process/editor/handelslideshow/";
                            settings.data = values;
                            var data = getJsonData(settings);

                            if (data) {
                                /*
                                 td
                                 remove from allslideshows to
                                 */
                                slideshowElements.helperFunctions.deleteSlideshow(entry);
                            }
                            return false;
                        }
                    });

                }, false, _eventHandlers)

            },

            setSlideshowAction : function(){
                //bind click to listelement

                //$( slideshowElements.loadSlideshowContainer).find('a' ).each(function(){
                //$(this).bind( 'click', function ( e ) {
                $( slideshowElements.loadSlideshowContainer).on('click','a', function(e){
                    e.preventDefault();
                    e.stopPropagation();
                    var _this = $( this );

                    if (_this.attr('data-id') == '') return false;
                    if ( _this.parent().hasClass( 'selected' ) ) return false;
                    /*
                     td
                     check if area saved
                     */
                    if(!defaults.slideShowSaved){

                        slideshowElements.createNewSlideshow();
                        defaults.triggerSave = true;
                        return false;
                    }

                    if(!defaults.slideSaved){
                        var response = slideshowElements.helperFunctions.getEditorHtml();
                        slideshowElements.helperFunctions.updateSlide(response);
                        //$(Editor.toolbarElements.saveSlideButton).trigger('click')
                        defaults.slideSaved = true;
                        return false;
                    }
                    //defaults.slideShowSaved = Editor.helperFunctions.checkFormState( 'saveslidesform' );

                    Editor.helperFunctions.se_clearData( 'all' );
                    slideshowElements.loadSelectedSlideshow(_this);
                    //try to load slides

                    //});
                });
            },

            setSlideAction : function(){
                //select slide from list and load content to editor


                var slides = $(mainElements.slideshowContainer ).find('.slideElement');
                //slides.live( 'click', function ( e ) {

                //var slides =
                $(mainElements.slideshowContainer ).on("click", '.slideElement .rightSide', function ( e ) {

//Editor.helperFunctions.removeUIevents();
                    e.stopPropagation();
                    if ( $( this ).closest( '.dataHolder' ).hasClass( 'selected' ) ) return false;

                    /*
                     td
                     check if editorarea element is not empty -> save button has btn-danger class
                     */
                    if(!Editor.helperFunctions.checkSlideState()){
                        $(Editor.toolbarElements.saveSlideButton).trigger('click');
                    }

                    defaults.selectedSlideId = $(this ).closest('li').attr( 'id' );
                    defaults.slideSaved = true;
                    Editor.helperFunctions.checkSlideState();
                    slides.parent().removeClass( 'selected' );
                    var $dataHolder = $( this ).closest( 'li' );

                    slideshowElements.helperFunctions.setSelectedSlide($dataHolder);

                } );

            },

            setTrafficAction : function(){
                //open trafficData
                //var traffic = $(mainElements.slideshowContainer ).find('.slideElement .rightSide i');
                //traffic.live( 'click', function () {
                $(mainElements.slideshowContainer ).on("click", '.slideElement .rightSide i', function ( e ) {
                    TOlog('pap2','setraffic')
                    $( this ).toggleClass( defaults.openedClass + ' ' + defaults.closedClass );
                    $( this ).closest('li.slideElement').find( '.trafficData' ).toggleClass( 'opened' );
                } );

            },

            createNewSlideshow : function(){
                var data = [],
                    body = '',
                    response = [];
                data['result'] = [];
                data['result'].push( {
                    'input': '',
                    'desc': '',
                    'folders': folderString
                } );

                body = tmpl( 'tmpl-saveslideshow', data );
                $( "#confirmDiv" ).confirmModal( {
                    heading : 'Give a name of your slideshow',
                    body    : body,
                    text    : 'Save',
                    type    : 'question',
                    cancel : true,
                    callback: function () {
                        if ( $( '#slideshowName' ).val() == '' ) {
                            sendMessage( 'alert-error', 'Empty name is forbidden' );
                            return false;
                        }

                        var mgData = [];
                        mgData.push( {
                            'name': $( '#slideshowName' ).val(),
                            'description': $( '#slideshowDescription' ).val(),
                            'diskArea' : defaults.diskAreaId
                        } );

                        response = slideshowElements.helperFunctions.handelSlideshow( mgData, 2 );

                        if ( response.type ) {
                            //sendMessage( 'alert-'+response.type, response.result.error );
                            return false;
                        }

                        $(mainElements.slideshowContainer ).empty();

                        var formdata = {'id':response.result[0].id.toString(),'name':response.result[0].name};


                        slideshowElements.helperFunctions.setFormData(formdata);

                        $(slideshowElements.loadSlideshowContainer).prepend( tmpl( "tmpl-lsslist", response, true ) );

                        $( slideshowElements.loadSlideshowButton ).find( 'span.name' ).text( response.result[0].name );

                        $(allslideshowsElements.loadSlideshowContainer2).prepend( tmpl( "tmpl-lsslist", response, true ) );

                        var selectedEntry = $(slideshowElements.loadSlideshowContainer ).find('a[data-id="' + response.result[0].id + '"]' );
                        selectedEntry.parent().addClass('selected');

                        slideshowElements.setDisabled(selectedEntry);
                        defaults.slideShowSaved = true;
                        defaults.slideSaved = false;

                        slideshowElements.handelEditable( selectedEntry );

                        if ( defaults.triggerSave ) {
                            //triggerSave = false;
                            response = slideshowElements.helperFunctions.getEditorHtml();

                            slideshowElements.helperFunctions.addNewSlide(response);
                        } else {
                            Editor.helperFunctions.se_clearData('all');
                            slideshowElements.handelEditable( selectedEntry );
                        }

                        slideshowElements.setSlideAction();
                    }
                } );
            },

            duplicateSlideshow : function(entry){},

            createPreview:function(entry){},

            initSortable:function(){
                $(mainElements.slideshowContainer).nestedSortable( slideshowElements.sortableSlides );
            },

            helperFunctions : {

                loadSlideshowList : function(){
                    values = {};
                    values['action'] = actions.slideshows[1];
                    values['form'] = $(mainElements.form).serializeArray();
                    settings.url = "/crawl?/process/editor/handelslideshow/";
                    settings.data = values;
                    var data = getJsonData(settings);
                    return data;
                },

                deleteSlideshow : function(entry){
                    $(slideshowElements.loadSlideshowContainer ).find('li.selected').remove();
                    $(mainElements.slideshowContainer ).empty();
                    Editor.helperFunctions.se_clearData('editor');
                    $(slideshowElements.loadSlideshowButton ).find('.name').text('select slideshow');
                    $(allslideshowsElements.loadSlideshowContainer2 )
                        .find('a[data-id="'+$(entry).attr( 'data-id' )+'"]' ).closest('li' ).remove();
                },

                handelSlideshow : function(entry, type){
                    values = {};
                    values['data'] = entry;
                    values['action'] = actions.slideshows[type];
                    values['form'] = $(mainElements.form).serializeArray();
                    settings.url = "/crawl?/process/editor/handelslideshow/";
                    settings.data = values;
                    var data = getJsonData(settings);
                    return data;
                },

                handelSlides : function(entry, type){
                    values = {};
                    values['data'] = entry.result;
                    values['toArray'] = entry.arraied;
                    values['action'] = actions.slide[type];
                    values['form'] = $(mainElements.form).serializeArray();
                    settings.url = "/crawl?/process/editor/handelslides/";
                    settings.data = values;
                    var data = getJsonData(settings);
                    return data ? data : false;

                },

                getEditorHtml : function(){
                    $(mainElements.tempDiv).length == 0 ? $( 'body' ).append( '<div id="tempDiv" />' ) : $(mainElements.tempDiv).html( '' );
                    $(mainElements.tempDiv).css( {
                        'width': defaults.editingAreaWidth,
                        'height': defaults.editingAreaHeight
                    } );

                    Editor.helperFunctions.removeUIevents();
                    Editor.helperFunctions.removeToolsEvent();

                    if ( $(Editor.editorAreaElememts.editorArea).find( '.buttonClass' ).length == 0 && $(Editor.editorAreaElememts.editorArea).attr( 'data-slide-type' ) !== 'normal' ){
                        $(Editor.editorAreaElememts.editorArea).append( '<div class="buttonClass disabled" style="left: 87.13333333333333%; top: 91.25925925925927%; width: 12%; height: auto; position: absolute;"><button type="button" id="submitForm" class="btn btn-dark btn-r">send</button></div>' );
                        defaults.templateSlide = true;
                    } else if ( $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type' ) == 'normal' ) {
                        $(Editor.editorAreaElememts.editorArea).find( '.buttonClass' ).remove();
                        defaults.templateSlide = false;
                    }

                    //append all editor element to prepering area
                    var html = $(Editor.editorAreaElememts.editorArea).html();
                    $(mainElements.tempDiv).append( html );

                    var returnData = slideshowElements.helperFunctions.gatherSlideData(0);

                    var arraied = $(mainElements.slideshowContainer ).find('li' ).length > 0 ? $(mainElements.slideshowContainer).nestedSortable( 'toArray', {startDepthCount: 0, slideShowId: $( '#saveslidesform [name="id"]' ).val()} ) : [];

                    return [html, returnData, arraied];

                },

                gatherSlideData : function(isDuplicate){
                    //remove unwanted elements, classes
                    //update contenteditable id
                    var textElements = $(mainElements.tempDiv).find( 'div[id^="myInstance_"]' ),
                        iId;
                    for ( var i = 0; i < textElements.length; i++ ) {
                        iId = isDuplicate ? (new Date().getTime())+i : textElements[i].getAttribute( 'id' );
                        textElements[i].setAttribute( 'data-temp-id', iId );
                        textElements[i].setAttribute( 'id', 'myInstance_' + iId );
                    }
                    var returnData = [];
                    //remove empty or unicode non-printing span's
                    var travers = document.getElementById( 'tempDiv' );
                    $.when( textEditingHelperfunctions.recurseDomChildren( travers, true ) ).done( function ( a ) {
                        returnData = Editor.helperFunctions.serializeForm(
                            $(mainElements.tempDiv),
                            $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type' )
                        );
                    });
                    return returnData;
                },

                setFormData : function(entry){
                    document.getElementById('slideshowID').value = entry.id;
                    document.getElementById('slideshowNAME').value = entry.name;
                    $(mainElements.form ).find('input[name="diskArea"]' ).val(defaults.diskAreaId);
                },

                setSlidesCount : function(){
                    var selectedId = $(mainElements.slideshowContainer ).find('.dataHolder.selected' ).parent().attr('id');
                    var slidesCount = $(mainElements.slideshowContainer ).find('li.slideElement' ).length;
                    $(slideshowElements.loadSlideshowContainer ).find('a[data-id="'+selectedId+'"]' ).children('.badge' ).text(slidesCount);
                    $(allslideshowsElements.loadSlideshowContainer2 ).find('a[data-id="'+selectedId+'"]' ).children('.badge' ).text(slidesCount);
                },

                setSelectedSlide : function(entry){

                    Editor.helperFunctions.checkSlideState();

                    //check slide type
                    defaults.templateSlide = (entry.attr( 'data-slide-type' ) == 'template') ? true : false;
                    defaults.templateSlide == true ? $( Editor.bottombarElements.testEditorContainer ).show() : $( Editor.bottombarElements.testEditorContainer ).hide();
                    //$('.testSlideEditorContainer').hide();
                    $( '.trafficDataHolder, .tools' ).html( '' );
                    defaults.templateSlide ? $( Editor.bottombarElements.bottomBar ).find('.level' ).show() : $( Editor.bottombarElements.bottomBar ).find('.level' ).hide();
                    $(mainElements.allslideshowContainer).find('.dataHolder').removeClass('selected').end();
                    $(mainElements.slideshowContainer).find('.dataHolder').removeClass('selected').end();
                    entry.find( '.dataHolder' ).addClass( 'selected' );

                    $(Editor.editorAreaElememts.editorArea).attr( 'data-slide-type', (defaults.templateSlide == true ? 'template' : 'normal') );
                    $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type', (defaults.templateSlide == true ? entry.attr('data-template-type') : '') );

                    //remove any instances
                    Editor.helperFunctions.ckedit.clearEditorInstances();

                    //update content, attach events
                    var html = entry.find( '.leftSide' ).html(),
                        desription = entry.find( '.descriptionTag' ).html(),
                        tag = entry.find( '.rightSide .name' ).text();
                    Editor.toolbarElements.setColorPicker(entry.attr('data-bg'));
                    $(Editor.editorAreaElememts.editorArea).empty().html( html );
                    $(Editor.bottombarElements.slidetagInput).val( tag );

                    if ( defaults.templateSlide ) {
                        //var textElements = $(Editor.editorAreaElememts.editorArea).find( 'div[id^="myInstance_"]' );
                        //update trafficmanager data
                        var data = slidesHelperfunctions.getTrafficData( entry ),
                            html = tmpl( "tmpl-trafficdata", data );
                        $( html ).appendTo( $( '.trafficDataHolder' ) );
                        /*
                         * td nincs hasznalatban az uj design miatt
                         check/option id replace
                         */
                        $(Editor.editorAreaElememts.editorArea).find( 'option' ).live( 'click', function () {
                            //$(Editor.editorAreaElememts.editorArea).on( 'click', 'option', function () {
                            $( this ).siblings().attr( 'selected', '' );
                            $( this ).attr( 'selected', 'selected' );
                        } );

                        $( '[name="levelCount"] option' ).filter(function () {
                            return $( this ).val() == entry.attr( 'data-slidelevel' );
                        } ).attr( 'selected', true );

                        var groups = $(Editor.editorAreaElememts.editorArea).find( '.sortableForm:not([id])' );
                        if ( groups.length > 0 )
                            $.each( groups, function () {
                                $( this ).attr( 'id', 'group_' + createUID() );
                            } );
                        //slideshowElements.setTrafficAction();
                        /*
                         td
                         html torles helyett mas
                         */
                        switch ($(Editor.editorAreaElememts.editorArea).attr('data-template-type')){
                            case 'radio':
                                var radios = $(Editor.editorAreaElememts.editorArea).find( 'input[type="radio"]' );
                                $.each( radios, function () {
                                    $( this ).attr( 'id', 'r' + createUID() );
                                    $(this).next('label').attr('for', $( this ).attr( 'id') );
                                } );
                                break;
                            case 'check':
                                var radios = $(Editor.editorAreaElememts.editorArea).find( 'input[type="checkbox"]' );
                                $.each( radios, function () {
                                    $( this ).attr( 'id', 'c' + createUID() );
                                    $(this).next('label').attr('for', $( this ).attr( 'id') );
                                } );
                                break;
                        }
                        /*
                         if(entry.parent().attr('id') !== 'testSlides')
                         entry.find('.leftSide' ).html('');
                         */
                        defaults.slideSaved = true;
                        Editor.helperFunctions.checkSlideState();
                    }

                    var textElements = $(Editor.editorAreaElememts.editorArea).find( 'div[id^="myInstance_"]' );

                    //check duplicate id's
                    var textObjects = [];
                    textElements.each(function ( i,e ) {
                        //$( this ).attr( 'onpaste', 'handlepaste(this, event)' );
                        var id = $(this ).attr('id' ).replace(/myInstance_/g,'' );
                        $(this ).attr('id','myInstance_'+id )
                        textObjects.push(id);
                    } );

                    textObjects = textObjects.sort();

                    var results = [];
                    results = textObjects.filter(function(elem, pos, self) {
                        return self.indexOf(elem) == pos;
                    })

                    //if(textObjects.length !== results.length){
                    results = [];
                    for(var i=0;i<textObjects.length;i++)
                        $(textElements[i]).attr('id','myInstance_'+createUID2() ).attr('class','textDiv');
                    //}
                    //end of check

                    textElements.each( function ( i,e ) {
                        Editor.helperFunctions.ckedit.createEditorInstance($(e ).attr('id'), true);
                    } );

                    Editor.helperFunctions.addUIevents();
//TOlog('setSelectedSlide',defaults.templateSlide)
                    if ( defaults.templateSlide ) {
                        Editor.helperFunctions.addToolsEvent( entry.attr( 'data-template-type' ) )
                        $(Editor.editorAreaElememts.editorArea).find('.sortableForm')
                            .droppable( testslidesElements.formDrop )
                            .sortable( testslidesElements.formSortable );

                        //$( '#editorArea .sortableForm' ).droppable( testslidesElements.formDrop );
                        //$( '#editorArea .sortableForm' ).sortable( testslidesElements.formSortable );

                        $(Editor.editorAreaElememts.editorArea).find('.holder .textDiv')
                            .droppable(testslidesElements.templateDropOption);//templateDropOption);
                        slideshowElements.setTrafficAction();
                    }

                    //if(entry.attr('data-bg') !=='')
                    Editor.toolbarElements.resetColorPicker();
                    defaults.selectedSlideBg = entry.attr('data-bg') == '' ? 'rgba(0, 0, 0, 0)' : entry.attr('data-bg');
                    $(Editor.editorAreaElememts.editorArea ).find('.elementSelected' ).each(function(){
                        $(this ).removeClass('.elementSelected');
                    });
                    //Editor.toolbarElements.setColor(defaults.selectedSlideBg);

                    //Editor.editorAreaElememts.editorArea.style.backgroundColor = entry.attr('data-bg');

                },

                addNewSlide : function(returnData){
                    //save as brand new slide
                    //temporary slide id
                    //it must be corrected after save
                    var timeStamp = new Date().getTime(),
                        html = returnData[0],//$(mainElements.tempDiv).html(),
                        html2 = returnData[1].html2,
                        answare = returnData[1].answare,
                        tag = $( '#slideTag' ).val(),
                        description = $( '.description' ).val(),
                        badge = $( mainElements.slideshowContainer ).find( 'li.slideElement' ).length + 1,
                        stype = (defaults.templateSlide == true) ? 'template' : 'normal',
                        templateType = $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type' ),
                        data = {};
                    data['result'] = [];

                    data['result'].push( {
                        'id'         : timeStamp,
                        'html'       : html,
                        'html2'      : html2,
                        'tag'        : tag,
                        'badge'      : badge,
                        'type'       : stype,
                        'templateType' : clearNULL(templateType),
                        'description': description,
                        'background' : Editor.editorAreaElememts.editorArea.style.backgroundColor
                    } );
                    if ( defaults.templateSlide ) {
                        //data['result'] = [];
                        //data['result'].templateType = templateType;
                        var obj = slidesHelperfunctions.setTrafficData();
                        data['result'][0].templateOption = obj;
                        data['result'][0].templateType = templateType;
                        //data['result'][0].templateOption = obj;
                        data['result'][0].slideLevel = $( '[name="levelCount"]' ).val();
                        Editor.helperFunctions.addToolsEvent( templateType );
                    }

                    var response = slideshowElements.helperFunctions.handelSlides(data,1);
                    $(mainElements.slideshowContainer ).append(tmpl( "tmpl-miniSlides", response, true ))

                    defaults.slideSaved = true;
                    $( mainElements.slideshowContainer ).find('.dataHolder.selected' ).removeClass('selected');
                    var $dataHolder = $( mainElements.slideshowContainer ).find('li[id="'+response.result[0].id+'"]');
                    slideshowElements.helperFunctions.setSelectedSlide($dataHolder);

                    //return response;
                },

                updateSlide : function(returnData){

                    var html = returnData[0],//$(mainElements.tempDiv).html(),
                        html2 = returnData[1].html2,
                        answare = returnData[1].answare,
                        tag = $( '#slideTag' ).val(),
                        description = $( '.description' ).val(),
                        thisSlide = $(mainElements.slideshowContainer).find( '.dataHolder.selected' ).parent(),
                        data = {};
                    data['result'] = [];

                    thisSlide.find( '.leftSide' ).html( '' ).html( html );
                    thisSlide.find( '.rightSide' ).find( '.name' ).text( tag );

                    data['result'].push( {
                        'id'          : thisSlide.attr( 'id' ),
                        'type'        : thisSlide.attr( 'data-slide-type' ),
                        'templateType': clearNULL(thisSlide.attr( 'data-template-type' )),
                        'badge'       : thisSlide.find( 'span.badge.nr' ).text(),
                        'tag'         : tag,
                        'html'        : html,
                        'html2'       : html2,
                        'answare'     : answare,
                        'description' : description,
                        'background' : Editor.editorAreaElememts.editorArea.style.backgroundColor
                    } );

                    thisSlide.find( '.descriptionTag' ).html( description );

                    if ( defaults.templateSlide ) {
                        var dataRows = $( '.trafficDataHolder' ).find( 'div.control-group' );
                        var obj = [];
                        $.each( dataRows, function ( i, e ) {
                            var prevId = slidesHelperfunctions.getslidesIDforTrafficData( $( e ).find( '#slidePrev' ).val() );
                            var nextId = slidesHelperfunctions.getslidesIDforTrafficData( $( e ).find( '#slideNext' ).val() );
                            obj.push( {
                                'prev'  : $( e ).find( '#slidePrev' ).val(),
                                'prevID': (prevId == 'undefined' ? 0 : prevId),
                                'score' : $( e ).find( '#slideScore' ).val(),
                                'next'  : $( e ).find( '#slideNext' ).val(),
                                'nextID': (nextId == 'undefined' ? 0 : nextId)
                            } );
                        } );
                        data['result'][0].templateOption = obj;
                        data['result'][0].slideLevel = $( '[name="levelCount"] option:selected' ).val();
                        var tdata = {};
                        tdata['result'] = obj;
                        var result = tmpl( 'tmpl-slidetrafficdata', tdata );
                        thisSlide.find( '.trafficData' ).find( '.whiteLine' ).remove().addClass( 'opened' );
                        thisSlide.find( '.trafficData' ).append( $( result ) );
                    }

                    var response = slideshowElements.helperFunctions.handelSlides(data,3);

                    if(response){

                        defaults.slideSaved = true;
                        Editor.helperFunctions.checkSlideState();
                    }
                    $(Editor.toolbarElements.saveSlideButton).removeClass('active');
                    //return data;
                }

            },

            sortableSlides: {
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
                        slidesHelperfunctions.updateSlidesIDarray( $( e ).attr( 'id' ), (i + 1), 'new' );
                        slidesHelperfunctions.refreshslidesIDinTrafficData( $( e ).attr( 'id' ) );
                    } );
                    var IstemplateSlide = $( '#slidesList li[data-slide-type="template"]' ).find( 'div.dataHolder.selected' );

                    if ( IstemplateSlide.length == 1 ) {
                        var data = slidesHelperfunctions.getTrafficData( IstemplateSlide.parent() );
                        var html = tmpl( "tmpl-trafficdata", data );
                        $( html ).appendTo( $( '.trafficDataHolder' ) );
                    }
                    slidesHelperfunctions.storeslideIDs();

                    //update slides parent, left, right data
                    var entry = [];
                    entry.result = '';
                    entry.arraied = $(mainElements.slideshowContainer).nestedSortable( 'toArray', {nestedType: 'slide', startDepthCount: 0, slideShowId: $( '#saveslidesform [name="id"]' ).val()} );

                    var response = slideshowElements.helperFunctions.handelSlides(entry,4);

                },
                stop                : function ( e, ui ) {
                    if ( ui.position.left < -70 ) {
                        var bodytext, isInTraffic = false;
                        var slideId = $( ui.item ).attr( 'id' );
                        isInTraffic = slidesHelperfunctions.searchslidesIDinTrafficData( slideId );
                        isInTraffic == false ? bodytext = 'Do you really want to delete this Slide?' : bodytext = 'Do you really want to delete this Slide?<br />Template slide is pointing to it!';


                        $( "#confirmDiv" ).confirmModal( {
                            heading : 'Alert',
                            body    : bodytext,
                            text    : 'Remove',
                            type    : 'question',
                            cancel  : true,
                            callback: function () {
                                //var arraied = $(mainElements.slideshowContainer).nestedSortable( 'toArray', {nestedType: 'slide', startDepthCount: 0, slideShowId: $( '#saveslidesform [name="id"]' ).val()} );
                                var entry = [];
                                entry.result = slideId;
                                entry.arraied = $(mainElements.slideshowContainer).nestedSortable( 'toArray', {nestedType: 'slide', startDepthCount: 0, slideShowId: $( '#saveslidesform [name="id"]' ).val()} );

                                var response = slideshowElements.helperFunctions.handelSlides(entry,2);
                                //se_handelSlide( slideId, $( Editor.editorAreaElememts.editorForm ).serializeArray(), 'delete', arraied );
                                if ( response.type !== 'error' ) {

                                    if ( $( ui.item ).find( '.dataHolder.selected' ).length == 1 )
                                        Editor.helperFunctions.se_clearData( $( ui.item ).attr( 'data-slide-type' ) );

                                    $( ui.item ).remove();
                                    //sendMessage( 'alert-success', response.message ); //response.message

                                    slidesHelperfunctions.deleteslidesIDinTrafficData( slideId );
                                    slidesHelperfunctions.storeslideIDs();

                                    $( '#slidesList > li.slideElement' ).each( function ( i, e ) {
                                        $( e ).find( '.badge.nr' ).text( i + 1 );
                                        slidesHelperfunctions.updateSlidesIDarray( $( e ).attr( 'id' ), (i + 1), 'new' );
                                        slidesHelperfunctions.refreshslidesIDinTrafficData( $( e ).attr( 'id' ) );
                                    } );
                                    //var arraied = $(mainElements.slideshowContainer).nestedSortable( 'toArray', {nestedType: 'slide', startDepthCount: 0, slideShowId: $( '#saveslidesform [name="id"]' ).val()} );
                                    entry.arraied = $(mainElements.slideshowContainer).nestedSortable( 'toArray', {nestedType: 'slide', startDepthCount: 0, slideShowId: $( '#saveslidesform [name="id"]' ).val()} );
                                    var response = slideshowElements.helperFunctions.handelSlides(entry,4);
                                    //var response = se_handelSlide( '', $( Editor.editorAreaElememts.editorForm ).serializeArray(), 'sort', arraied );
                                    /*
                                     if ( !response )
                                     sendMessage( 'alert-success', response.message ); //response.message
                                     else
                                     sendMessage( 'alert-error', response.error ); //response.message
                                     */
                                } //else {
                                //sendMessage( 'alert-'+, response.error ); //response.message
                                //}
                                $(mainElements.slideshowContainer).nestedSortable( slideshowElements.sortableSlides );
                                slideshowElements.helperFunctions.setSlidesCount();
                            }
                        } );

                    }

                }
            }

        },

        mymediaElements = {
            sortingBar : document.getElementById( "sortingIconBar" ) || '',
            groupList : document.getElementById( "mediaBoxList2" ) || '',

            init : function(){
                this.selectBox();
                this.filterFiles();
            },

            filterFiles : function(){
                $(mymediaElements.sortingBar ).find('button' ).bind('click', function(){
                    if($(mainElements.mymediaContainer).find('li').length == 0) return false;
                    var classes = $(this).attr('data-class');
                    $(mainElements.mymediaContainer).find('li' ).hide();
                    var classArray = classes.split(/\s+/g);
                    for (i in classArray)
                        classes == '' ? $(mainElements.mymediaContainer).find('li' ).show() : $(mainElements.mymediaContainer).find('li.'+classArray[i] ).show();
                })
            },

            selectBox : function(){
                //$(mymediaElements.groupList ).find('li:not(.selected) a.level2').live('click', function(e){
                $(mymediaElements.groupList).on('click','li:not(.selected) a.level2',function(e){
                    var _this = $(this);
                    e.preventDefault();
                    e.stopPropagation();
                    _this.parent().siblings().removeClass('selected');
                    _this.parent().addClass('selected');
                    var folderName = $(this).find('span.name').text();
                    folderName = folderName.length > 20 ? folderName.substring(0, 20)+'...' : folderName;
                    $(mymediaElements.groupList).prev().find('span.name').text(folderName).attr('title',$(this).find('span.name').text());
                    mymediaElements.viewBoxFiles(_this.attr('data-id'));
                    $('.selectMenu').parent().removeClass('open');
                });
            },

            viewBoxFiles: function (id){
                values = {};
                values['action'] = (id == 'all' ? actions.mediafiles[0] : actions.mediafiles[1]);
                values['group'] = id;
                values['form'] = $( mainElements.form ).serializeArray();
                values['diskArea'] = defaults.diskAreaId;

                settings.url = "/crawl?/process/editor/loadmediafiles/";
                settings.data = values;
                var data = getJsonData( settings );
                if ( !data )
                    return false;

                $( mainElements.mymediaContainer ).html( tmpl( "tmpl-mediaElement", data ) );
                mymediaElements.addDND();
            },

            addDND:function(){
                $( mainElements.mymediaContainer).find('li')
                    .draggable( mymediaElements.myMediaDragOption );
            },

            myMediaDragOption : {
                appendTo: 'body',
                handle  : 'img',
                revert  : 'true',
                helper  : 'clone',
                start   : function ( e, ui ) {

                    $(Editor.editorAreaElememts.editorArea).find( '.holder .textDiv' )
                        .droppable( testslidesElements.templateDropOption );
                    Editor.helperFunctions.removeToolsEvent();
                    //$('#editorArea .sortableForm').droppable(formDrop);
//Editor.helperFunctions.removeToolsEvent();

                    $(Editor.editorAreaElememts.editorArea)
                        .droppable( Editor.helperFunctions.ui.editorDropOption );
//2012.12.11
                    //$attach.droppable( attachDropOption );

                    defaults.prevOffset = true;
                },
                stop    : function ( e, ui ) {
                    $(Editor.editorAreaElememts.editorArea)
                        .droppable( 'destroy' );
                    if ( $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type' ) !== 'normal' )
                        Editor.helperFunctions.addToolsEvent( $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type' ) );
                }
            }
        },

        testslidesElements = {

            init:function(){
                //var testslides = $(mainElements.testsContainer ).find('.slideElement .rightSide');
                //testslides.live( 'click', function ( e ) {
                $(mainElements.testsContainer ).on('click','.slideElement .rightSide',function(e){
//Editor.helperFunctions.removeUIevents();
                    e.stopPropagation();
                    if ( $( this ).closest( '.dataHolder' ).hasClass( 'selected' ) ) return false;
                    /*
                     td
                     check if editorarea element is not empty -> save button has btn-danger class
                     */
                    defaults.selectedSlideId = $(this ).closest('li').attr( 'id' );
                    defaults.slideSaved = false;
                    Editor.helperFunctions.checkSlideState();
                    $(mainElements.slideshowContainer ).find('.dataHolder.selected' ).removeClass('selected');
                    $(this).parent().removeClass( 'selected' );
                    var $dataHolder = $( this ).closest( 'li' );

                    slideshowElements.helperFunctions.setSelectedSlide($dataHolder);

                } );
            },

            toolsDrag: {
                appendTo: 'body',
                handle  : 'li',
                revert  : 'true',
                helper  : 'clone',
                start   : function ( e, ui ) {
                    $(Editor.editorAreaElememts.editorArea).find('.sortableForm')
                        .droppable( testslidesElements.formDrop )
                        .sortable( testslidesElements.formSortable );
//$( '#editorArea .sortableForm' ).droppable( testslidesElements.formDrop ).sortable( testslidesElements.formSortable );
                    $( ui.helper ).find( '#addOptionRow' ).remove();
                },
                stop    : function ( e, ui ) {
                    $(Editor.editorAreaElememts.editorArea).find('.sortableForm').droppable( 'destroy' );
//$( '#editorArea .sortableForm' ).droppable( 'destroy' );
                    //.sortable('destroy');
                    //defaults.slideSaved = false;
                    //defaults.slideShowSaved = Editor.helperFunctions.checkFormState('saveslidesform');
                }
            },

            toolsDrag2: {
                appendTo: 'body',
                handle  : 'li',
                revert  : 'true',
                helper  : 'clone',
                start   : function ( e, ui ) {

                    defaults.prevOffset = true;
                    $(Editor.editorAreaElememts.editorArea).droppable( Editor.helperFunctions.ui.editorDropOption );
                },
                stop    : function ( e, ui ) {

                    $(Editor.editorAreaElememts.editorArea).droppable( 'destroy' );
                    Editor.helperFunctions.addUIevents();
                    Editor.helperFunctions.addToolsEvent( 'groupping' );
                },
                drag    : function ( e, ui ) {

                    var element = ui.helper;

                }
            },

            formDrop: {
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
                    var templateType = $( Editor.editorAreaElememts.editorArea ).attr( 'data-template-type' );

                    switch ( templateType ) {
                        case 'radio':
                        case 'check':
                            $( this ).append( element );
                            var inputElements = $( this ).find( ':input' );
                            var timeStamp = new Date().getTime(),
                                iId = 'myInstance_' + timeStamp;
                            element.find( '.textDiv' ).attr( 'id', iId );

                            //editor.addInstance(iId).floatingPanel();
                            Editor.helperFunctions.ckedit.createEditorInstance( iId, true );

                            for ( var i = 0, e; e = inputElements[i]; i++ ){
                                e.value = i;
                            }
                            $(Editor.editorAreaElememts.editorArea).find('.sortableForm').sortable( testslidesElements.formSortable );
//$( '#editorArea .sortableForm' ).sortable( testslidesElements.formSortable );
                            break;
                        case 'input':
                            var inpElement = element;
                            element.find( 'span.btn-dark' ).remove();

                            if ( !textEditingHelperfunctions.elementContainsSelection( document.getElementById( $(Editor.editorAreaElememts.editorArea).find('.sortableForm').attr( 'id' ) ) ) ) return false;
//don't remove

                            textEditingHelperfunctions.pasteHtmlAtCaret( inpElement.html() );

                            var inputElements = $(Editor.editorAreaElememts.editorArea).find('.sortableForm input' );

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


                            element.find( '.textDiv' ).attr( 'id', iId );
                            $( this ).append( element );

                            Editor.helperFunctions.ckedit.createEditorInstance(iId,true);//.addInstance( iId ).floatingPanel();

                            $(Editor.editorAreaElememts.editorArea).find('.sortableForm').sortable( testslidesElements.formSortable );

//$( '#editorArea .sortableForm' ).sortable( testslidesElements.formSortable );
                            break;

                    }
                    defaults.slideSaved = false;
                    defaults.slideShowSaved = Editor.helperFunctions.checkFormState( 'saveslidesform' );
                    Editor.helperFunctions.checkSlideState();
                }
            },

            formSortable: {
                cancel              : ':input,button,.contenteditable',
                placeholder         : "ui-state-highlight",
                handle              : "span.btn-dark",
                forcePlaceholderSize: true,
                connectWidth        : '#editorArea .sortableForm',
                start               : function ( e, ui ) {
                    Editor.helperFunctions.removeUIevents();
                    Editor.helperFunctions.removeToolsEvent();
                },
                stop                : function ( e, ui ) {
                    defaults.slideSaved = false;
                    defaults.slideShowSaved = Editor.helperFunctions.checkFormState( 'saveslidesform' );
                    Editor.helperFunctions.checkSlideState();
                    if ( $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type' ) !== 'normal' )
                        Editor.helperFunctions.addToolsEvent( $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type' ) );
                    Editor.helperFunctions.addUIevents();
                },
                out                 : function ( e, ui ) {
                    if ( ui.position.left < -70 ) {
////console.log(ui.position.left);
                        $( ui.item ).remove();
                        //$('#editorArea .sortableForm').sortable(formSortable);
                        $(Editor.editorAreaElememts.editorArea).find('.sortableForm li')
                            .sortable( testslidesElements.formSortable );
//$( '#editorArea .sortableForm li' ).sortable( testslidesElements.formSortable );
                        defaults.slideSaved = false;
                    }
                    Editor.helperFunctions.checkSlideState();
                }
            },

//drop image,video, audio elements

            templateDropOption: {
                tolerance  : 'pointer',
                accept     : ".image, .video, .audio",
                activeClass: "ui-state-highlight",
                hoverClass : "ui-state-active",
                //greedy: true,
                //iframeFix: true,
                drop       : function ( e, ui ) {

                    var parentOffset = $( this ).offset();
                    //or $(this).offset(); if you really just want the current element's offset
                    var posX = e.pageX - parentOffset.left;
                    var posY = e.pageY - parentOffset.top;

                    posX = parseInt( posX / defaults.editingAreaWidth * 100 );
                    posY = parseInt( posY / defaults.editingAreaHeight * 100 );

                    //var $elementHolder;
                    var eW, eH, nW, nH;
                    var results = [];
                    results['result'] = [];
                    switch ( ui.draggable.children().attr( 'class' ) ) {
                        case 'colorBar image':
                            eW = 160;
                            eH = 120;
                            nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                            nH = parseInt( eH / defaults.editingAreaHeight * 100 );
                            results['result'].push( {
                                'mediaurl': ui.draggable.attr( 'data-mediaurl' ),
                                'left'    : '0%',
                                'top'     : '0%',
                                'width'   : '100%',
                                'height'  : 'auto'//'100%'
                            } );
                            //var result = tmpl("tmpl-eaImage", results);
                            var element = $( '<div style="left:0%;top:0%; width:20%; height:2%"><img src="' + ui.draggable.clone().attr( 'data-mediaurl' ) + '" /></div>' );
                            if ( !textEditingHelperfunctions.elementContainsSelection( document.getElementById( $( this ).attr( 'id' ) ) ) ) return false;
//don't remove

                            textEditingHelperfunctions.pasteHtmlAtCaret( element.html() );

                            $( this ).append( $( result ) );
                            defaults.prevOffset = false;
                            break;
                        case 'colorBar audio':
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
                            //$(Editor.editorAreaElememts.editorArea).append( $(result) );
                            break;
                        case 'colorBar video':
                            eW = 320;
                            eH = 240;
                            nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                            nH = parseInt( eH / defaults.editingAreaHeight * 100 );
                            var tmplName, mediaurl, poster;
                            switch ( ui.draggable.attr( 'data-mediatype' ) ) {
                                case 'local':
                                    mediaurl = ui.draggable.attr( 'data-mediaurl' ).split( '.mp4' )[0];
                                    poster = ui.draggable.find( 'img' ).attr( 'src' );//attr('data-poster');
                                    tmplName = 'tmpl-eaLVideo';
                                    break;
                                case 'remote':
                                    var video = parseVideoURL( ui.draggable.attr( 'data-mediaurl' ) );
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

                            var result = tmpl( tmplName, results );
                            //$(Editor.editorAreaElememts.editorArea).append( $(result) );
                            break;
                    }
                    //$('#editorsMediaBox li').draggable('destroy');
                    //Editor.helperFunctions.addUIevents();
                    //$(Editor.editorAreaElememts.editorArea).droppable('destroy');
                    defaults.slideSaved = false;
                    defaults.slideShowSaved = Editor.helperFunctions.checkFormState( 'saveslidesform' );
                    Editor.helperFunctions.checkSlideState();

                },
                stop       : function ( e, ui ) {
                    var posX = parseInt( $( this ).offset().left / defaults.editingAreaWidth * 100 );
                    var posY = parseInt( $( this ).offset().top / defaults.editingAreaWidth * 100 );
                    var eW = $( this ).width(), eH = $( this ).height();
                    var nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                    var nH = parseInt( eH / defaults.editingAreaHeight * 100 );
                    if ( $( this ).hasClass( defaults.textClass ) )
                    //$(this).css('height','auto');
                        $( this ).css( {'left': posX + '%', 'top': posY + '%', 'width': nW + '%', 'height': nH + '%'} );
                },
                out        : function ( event, ui ) {
                }


            }
        },

        allslideshowsElements = {
            loadSlideshowContainer2: document.getElementById('loadSlideshow2') || '',
            loadSlideshowButton2: document.getElementById('loadSlideShow2Button') || '',
            /*
             init:function(){},
             */
            viewList : function(){
                $( this.loadSlideshowContainer2 ).empty().append( tmpl( "tmpl-lsslist", defaults.loadedSlideshows ) );
                $( this.loadSlideshowButton2 ).find( 'span.name' ).text( 'select slideshow' );
                this.setNoneditable(defaults.disabledSlideshows);

                this.setSlideshowAction()
            },

            loadSelectedSlideshow2:function(entry){
                values = {};
                values['action'] = actions.slideshows[0];
                values['id'] = entry.attr( 'data-id' );
                values['form'] = $(mainElements.form).serializeArray();
                settings.url = "/crawl?/process/editor/handelslideshow/";
                settings.data = values;
                var data = getJsonData(settings);
                if(!data)
                    return false;

                allslideshowsElements.setDisabled(entry);

                var resHtml = tmpl( "tmpl-miniSlides", data, true );
                $(mainElements.allslideshowContainer).empty().append( resHtml );
//recalculateScrollHeight( $('#slidesList') );
                //slidesHelperfunctions.storeslideIDs();
                //defaults.slideSaved = true;
                //defaults.slideShowSaved = Editor.helperFunctions.checkFormState( 'saveslidesform' );

                entry.closest( '.open' ).removeClass( 'open' );
                var title = entry.find( 'span.sname' ).text();
                var sName = title.length > 18 ? title.substring( 0, 15 ) + '...' : title;
                $(allslideshowsElements.loadSlideshowButton2).find( 'span.name' ).text( sName ).attr( 'title', title );
                //2013.01.19
//Editor.helperFunctions.addUIevents();
                defaults.prevOffset = false;

                this.setSlideAction();

            },

            setSlideshowAction : function(){
                //bind click to listelement
                $( allslideshowsElements.loadSlideshowContainer2 ).on('click','a',function(e){
                    //$( allslideshowsElements.loadSlideshowContainer2 ).find('a' ).each(function(){
                    //$(this).bind( 'click', function ( e ) {
                    e.preventDefault();
                    e.stopPropagation();
                    var _this = $( this );
                    if (_this.attr('data-id') == '') return false;
                    if ( _this.parent().hasClass( 'selected' ) ) return false;
//Editor.helperFunctions.se_clearData( 'all' );
                    allslideshowsElements.loadSelectedSlideshow2(_this);
                    $(this).parent('li').siblings().removeClass('selected').end();
                    $(this).parent('li').addClass('selected')
                    //});
                });
            },

            setSlideAction : function(){
                //select slide from list and load content to editor
                //this.addDND();
                var slides = $(mainElements.allslideshowContainer ).find('.slideElement');

                slides.live( 'click', function ( e ) {

//Editor.helperFunctions.removeUIevents();
                    e.preventDefault();
                    if ( $( this ).closest( '.dataHolder' ).hasClass( 'selected' ) ) return false;
                    /*
                     td
                     check if editorarea element is not empty -> save button has btn-danger class
                     */
                    $.each(slides, function(){
                        $(this).find('.dataHolder').removeClass('selected');
                    })
                    //defaults.selectedSlideId = $(this ).closest('li').attr( 'id' );
                    defaults.slideSaved = true;
                    Editor.helperFunctions.checkSlideState();
                    //slides.parent().removeClass( 'selected' );
                    var $dataHolder = $( this ).closest( 'li' );

                    slideshowElements.helperFunctions.setSelectedSlide($dataHolder);

                } );

            },

            setDisabled : function(entry){
                //slideshow list ellenőrzése
                $( slideshowElements.loadSlideshowContainer ).find( 'a').each(function(){
                    if($(this).attr('data-id')!==''){
                        $(this).removeClass('disabled');
                    }
                })
                $( slideshowElements.loadSlideshowContainer ).find( 'a[data-id="'+entry.attr( 'data-id' )+'"]' ).addClass( 'disabled' );
            },

            setNoneditable: function(data){
                $('li', allslideshowsElements.loadSlideshowContainer2 ).filter( function (i,element) {
                    if( $(element).find('a').attr('data-id' ).indexOf(data) == 1){
                        $(element ).parent().addClass('disabled');
                    }
                });
            }
        },

        actions = {
            slide     : {0: 'load', 1: 'new', 2: 'delete', 3: 'update', 4: 'sort'},
            slideshows: {0: 'load', 1: 'list', 2: 'new', 3: 'rename', 4: 'delete', 5: 'duplicate'},
            mediafiles: {0: 'load', 1: 'loadgroup'}
        },

        affixElements = {
            0: mainElements.leftContainer
        },

        preventObject = {

        },
        values = {},
        settings = {
            url: "",
            data: values,
            responseType: 'json'
        },

        defaults = {
            diskAreaId                : $( 'input[name="diskArea"]' ).val(),
            currentSlideShowId        : null,
            disabledSlideshows        : $.map( $('li.disabled a', slideshowElements.loadSlideshowContainer), function (element) { return $(element).attr('data-id') }),
            loadedSlideshows          : [],
            templateSlide             : false,
            slideSaved                : true,
            slideShowSaved            : true,
            prevOffset                : false,
            selectedSlideshowId       : '',
            selectedSlideHTML         : '',
            selectedSlideId           : '',
            selectedSlideBg           : 'rgba(0, 0, 0, 0)',
            slideIDs                  : [],
            editingAreaWidth          : 0,
            editingAreaHeight         : 0,
            textClass                 : 'textClass',
            resizableClass            : 'ResizableClass',
            nonResizableClass         : 'nonResizableClass',
            isSelected                : 'isSelected',
            elementSelected           : 'elementSelected',
            triggerSave               : false,
            scrollContainerHeight     : 345,
            scrollContainerInnerHeight: 270,
            openedClass               : 'icon-eye-close',
            closedClass               : 'icon-eye-open',
            subDomain : ''
        };


    var Editor = {

        editorAreaElememts: {
            editorArea: document.getElementById( "editorArea" ) || '',
            editorForm: document.getElementById( 'editorAreaForm' ) || '',

            init     : function () {
                $( '.deleteBox' ).live( 'click', function () {
                    if ( $( this ).parent().hasClass( defaults.textClass ) ) {
                        Editor.helperFunctions.ckedit.removeEditorInstance( $( this ).parent().find( 'div[id^="myInstance_"]' ).attr( 'id' ) )
                        Editor.helperFunctions.ckedit.listEditorInstances();
                    }

                    $( this ).parent().remove();
                    defaults.slideSaved = false;
                    Editor.helperFunctions.checkSlideState();
                } );
            }/*,

             addDND   : function () {
             },
             removeDND: function () {
             }
             */
        },

        toolbarElements : {
            gridButton : document.getElementById( "removeBG" ) || '',
            insertTextButton : document.getElementById( "addText" ) || '',
            clearAreaButton : document.getElementById( "clearArea" ) || '',
            newSlideButton : document.getElementById( "newSlide" ) || '',
            duplicateSlideButton : document.getElementById( "duplicateSlide" ) || '',
            saveSlideButton : document.getElementById( "saveSlide" ) || '',
            newSlideshowButton : document.getElementById( "createNewSlideShow" ) || '',
            previewSlideshowButton : document.getElementById( "previewSlideShow" ) || '',
            exitPreviewButton : document.getElementById( "exitPreview" ) || '',
            colorpicker : document.getElementById( "colorPicker" ) || '',
            toCanvas : document.getElementById('toCanvas') || '',


            init : function(){

                addEventO(Editor.editorAreaElememts.editorArea, 'click', function(event){
                    var objectType; //div, img, audio, video, txt
                    var sampleText = '';//'Sample text...';
                    var _this = $( event.target );

                    $('.'+defaults.elementSelected).find('#play').remove();
                    $('.'+defaults.elementSelected).removeClass(defaults.elementSelected)

                    //insert textnode, if "T" is active and clicked on editorArea
                    if ( event.target.id == 'editorArea' && $(Editor.toolbarElements.insertTextButton).hasClass( 'active' )  ) {
                        var parentOffset = _this.offset();
                        var posX = (event.pageX - parentOffset.left),
                            posY = (event.pageY - parentOffset.top),
                            newX = posX / defaults.editingAreaWidth * 100,
                            newY = posY / defaults.editingAreaHeight * 100;

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
                        $(Editor.editorAreaElememts.editorArea).append( $( result ) );
                        defaults.slideSaved = false;
                        Editor.helperFunctions.checkSlideState();
                        defaults.slideShowSaved = Editor.helperFunctions.checkFormState( 'saveslidesform' );
                        /*
                         var html = '<div class="'+textClass+' '+defaults.resizableClass+' isSelected" style="left:'+newX+'%;top:'+newY+'%;width:'+nW+'%;height:auto;position:absolute;">' + 
                         '<div class="movingBox"><i class="icon-move"></i></div>'+
                         '<div class="deleteBox"><i class="icon-remove"></i></div>'+
                         '<div id="myInstance_'+randomnumber+'" class="textDiv" contenteditable="true" style="position: relative;">'+sampleText+'</div>'+
                         //'<textarea id="myInstance_'+randomnumber+'"placeholder="Sample text..." contenteditable=true ></textarea>'+
                         '</div>';
                         */
                        $(Editor.toolbarElements.insertTextButton).toggleClass( 'active' );
//_this.append( $(html) );
//Editor.addDND();
//Editor.helperFunctions.addUIevents();
                        var iId = 'myInstance_' + timeStamp;
                        Editor.helperFunctions.ckedit.createEditorInstance( iId, true );
                        $(Editor.editorAreaElememts.editorArea).trigger('input');
                        defaults.slideSaved = false;
                        return false;

                    }
                    if ( event.target.nodeName == 'INPUT' ) {

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

                    if ( event.target.getAttribute('class') == 'movingBox' || event.target.nodeName == 'I') {
                        //toggle isSelected class

                        $(event.target ).closest('.slideItem' ).addClass(defaults.elementSelected);
                        /*
                         td
                         */
                        //$(event.target ).closest('.slideItem' ).wrap(mainElements.matrixWrapper);
                        //$('#play').css($(event.target ).closest('.slideItem' ).css()).prepend(mainElements.matrixWrapperButtons);
                        //$(event.target ).closest('.slideItem' ).append(mainElements.matrixHolder);
                        //var matrix = new Play();
                        //$(event.target.parentNode).toggleClass(defaults.isSelected);
                    }
                    $(this ).removeClass('active');
                }, true, _eventHandlers);

                addEventO(Editor.editorAreaElememts.editorArea, "input", function () {
                    Editor.helperFunctions.checkSlideState();
                }, false,_eventHandlers );

                addEventO(Editor.toolbarElements.gridButton,'click', function(){
                    $( Editor.editorAreaElememts.editorArea ).toggleClass( 'pure' );

                }, true,_eventHandlers);

                addEventO(this.toCanvas,'click', function(){
                    if($(mainElements.slideshowContainer ).find('.selected' ).parent('li').index())
                        $("#confirmDiv").confirmModal({
                            heading: 'Alert',
                            body: 'Only from the first slide can be snapshot taken',
                            text:'Ok',
                            //cancel: true,
                            type: 'question',
                            callback: function () {
                                return false;
                            }
                        });
                    else{
                        Editor.toolbarElements.tocanvas();
                    }
                    $(this ).removeClass('active');
                }, true,_eventHandlers);

                addEventO(this.clearAreaButton,'click', function(e){
                    e.stopPropagation();
                    Editor.helperFunctions.se_clearData( 'normal' );
                    $(Editor.editorAreaElememts.editorArea).empty();
                    if ( !defaults.templateSlide ) {
                        $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type', 'normal' );
                    }

                    if($(mainElements.slideshowContainer).find('li' ).length == 0)
                        $(Editor.toolbarElements.saveSlideButton ).removeClass('btn-danger' ).addClass('btn-dark');
                    $( this ).removeClass( 'active' );
                }, true,_eventHandlers);

                addEventO(this.newSlideButton,'click', function(){
                    if ( !Editor.helperFunctions.checkFormState( 'saveslidesform' ) ) {
                        defaults.slideSaved = false;
                        defaults.slideShowSaved = false;
                        $( "#confirmDiv" ).confirmModal( {
                            heading : 'Alert',
                            body    : 'You must create slideshow first',
                            text    : 'OK',
                            type    : 'question',
                            callback: function () {
                                $(this ).removeClass('active');
                            }
                        });
                        return false;
                    }
                    $(mainElements.slideshowContainer).find( '.dataHolder.selected' ).removeClass( 'selected' );
                    $( '.description' ).val( '' );
                    Editor.helperFunctions.se_clearData( 'template' );
                    $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type', 'normal' );

                    var data = [];
                    data.push('');
                    data.push({'html2':'', 'answare':''});
                    slideshowElements.helperFunctions.addNewSlide(data);
                    slideshowElements.helperFunctions.setSlidesCount();
                    $(this ).removeClass('active');
                    return false;
                }, true,_eventHandlers);

                addEventO(this.duplicateSlideButton,'click', function(){
                    if($( mainElements.slideshowContainer ).find('.dataHolder.selected' ).length == 0) return false;
                    var response = slideshowElements.helperFunctions.getEditorHtml();
                    slideshowElements.helperFunctions.addNewSlide(response);

                    $(this ).removeClass('active');
                }, true,_eventHandlers);

                addEventO(this.saveSlideButton,'click', function(){

                    /*
                     check, if slideshow saved or not
                     if not call slideshow save function (f:addNewSlideshow)
                     after set new slideshow selected
                     and save editorarea content (f:addNewSlide)
                     */
                    if ( !Editor.helperFunctions.checkFormState( 'saveslidesform' ) ) {
                        defaults.slideSaved = false;
                        defaults.triggerSave = true;
                        //$( '#saveSlideshow' ).trigger( 'click' );
                        slideshowElements.createNewSlideshow();
                        return false;
                    }

                    /*
                     create temp div, and clone editors data to it
                     and purge from wreck and return clean data as an array
                     f:gatherSlideData
                     */
                    var response = slideshowElements.helperFunctions.getEditorHtml();

//return false;
                    if ($(mainElements.slideshowContainer).find( '.dataHolder.selected' ).length == 1 ) {
                        //update
                        slideshowElements.helperFunctions.updateSlide(response);
                        Editor.helperFunctions.addUIevents();
                        Editor.helperFunctions.addToolsEvent();
                    }else{
                        //save new
                        slideshowElements.helperFunctions.addNewSlide(response);

                    }
                    //slidesHelperfunctions.handeslideAfterEvents();
                    $(Editor.toolbarElements.saveSlideButton ).attr('class', 'btn btn-dark');
                }, true,_eventHandlers);

                addEventO(this.newSlideshowButton,'click', function(e){
                    e.stopPropagation();
                    e.preventDefault();
                    defaults.triggerSave = false;
                    slideshowElements.createNewSlideshow();
                }, true,_eventHandlers);

                addEventO(this.previewSlideshowButton,'click', function(e){
                    e.stopPropagation();
                    e.preventDefault();
                    var type = $( this ).attr( 'data-type' );

                    var slideshowId = $( '#saveslidesform [name="id"]' ).val();
                    if ( slideshowId !== '' ) {
                        if ( type == 'preview' ) {
                            var iframe = $( "#previewIframe" );
                            defaults.diskAreaId = $( '#saveslidesform [name="diskArea"]' ).val();

                            iframe.attr( 'src', defaults.subDomain + slideshowId + '/' );// +defaults.diskAreaId+'/'

                            iframe[0].contentWindow.focus();
                        }
                    } else {
                        sendMessage( 'alert-warning', 'No slideshow selected!' );
                        return false;
                    }
                    $( 'section.special' ).slideUp( 'fast' );//.toggleClass('hidden');
                    $( '.' + type ).toggleClass( 'hidden' );
                    $( "html, body" ).animate( { scrollTop: $( '#exitPreview' ).offset().top }, 1000 );
                    $(this ).closest('.dropdown.open' ).removeClass('open');

                }, false,_eventHandlers);

                addEventO(this.exitPreviewButton, 'click', function(e){
                    var type = $( this ).attr( 'data-type' );
                    $( 'section.special' ).slideDown( 'slow' );//.toggleClass('hidden');
                    $( '.' + type ).toggleClass( 'hidden' );
                    if ( type == 'preview' ) {
                        var iframe = $( "#previewIframe" );
                        iframe.attr( 'src', '' );
                    }

                    $( "html, body" ).animate( { scrollTop: $( '#inner-headline' ).offset().top }, 1000 );

                    //defaults.editingAreaWidth = $(Editor.editorAreaElememts.editorArea).width();
                    defaults.editingAreaHeight = defaults.editingAreaWidth * 9 / 16;
                    $(Editor.editorAreaElememts.editorArea).css('display','block');
                }, false, _eventHandlers);

                this.initColorpicker();
            },

            tocanvas : function(){
                //var canvas = document.createElement('canvas');
                //var thiscreen = $('#editorArea');
                //thiscreen = thiscreen[0];
                $( '#loading' ).show();
                $( Editor.toolbarElements.gridButton ).trigger( 'click' );
                setTimeout( function () {
                    html2canvas( $(Editor.editorAreaElememts.editorArea) , {
                        proxy     : '/proxy',
                        onrendered: function ( canvas ) {
                            var canvas2 = document.createElement( "canvas" );
                            canvas2.width = 320;
                            canvas2.height = 180;
                            canvas2.getContext( "2d" ).drawImage( canvas, 0, 0, canvas2.width, canvas2.height );
                            var myImage = canvas2.toDataURL( "image/png" );
                            $( '#render' ).attr( 'src', myImage );
                            /*
                             td
                             save to slideshow cover
                             */
                            $( '#loading' ).hide();
                            $( Editor.toolbarElements.gridButton ).trigger( 'click' );
                            scrollToWorkingArea( $( '.row.special' ) );
                            $( '#canvas' ).remove();
                        }
                    } );
                }, 1000 )
            },

            initColorpicker : function(){
                $(Editor.toolbarElements.colorpicker).spectrum({
                    color: "#ECC",
                    showInput: true,
                    //className: "full-spectrum",
                    showInitial: true,
                    showSelectionPalette: true,
                    showAlpha: true,
                    maxPaletteSize: 10,
                    preferredFormat: "rgb",
                    localStorageKey: "skill.color",
                    showButtons: false,
                    clickoutFiresChange: true,
                    showPalette: true,
                    move: function (color) {
                        Editor.toolbarElements.setColor(color);
                    },
                    change: function (color) {
                        Editor.toolbarElements.setColor(color);
                    },
                    hide: function (color) {
                        Editor.toolbarElements.setColor(color);

                        //Editor.toolbarElements.setColor(defaults.selectedSlideBg);
                    },
                    palette: [
                        ["rgb(0, 0, 0)", "rgb(67, 67, 67)", "rgb(102, 102, 102)", /*"rgb(153, 153, 153)","rgb(183, 183, 183)",*/
                            "rgb(204, 204, 204)", "rgb(217, 217, 217)", /*"rgb(239, 239, 239)", "rgb(243, 243, 243)",*/ "rgb(255, 255, 255)"],
                        ["rgb(152, 0, 0)", "rgb(255, 0, 0)", "rgb(255, 153, 0)", "rgb(255, 255, 0)", "rgb(0, 255, 0)",
                            "rgb(0, 255, 255)", "rgb(74, 134, 232)", "rgb(0, 0, 255)", "rgb(153, 0, 255)", "rgb(255, 0, 255)"],
                        ["rgb(230, 184, 175)", "rgb(244, 204, 204)", "rgb(252, 229, 205)", "rgb(255, 242, 204)", "rgb(217, 234, 211)",
                            "rgb(208, 224, 227)", "rgb(201, 218, 248)", "rgb(207, 226, 243)", "rgb(217, 210, 233)", "rgb(234, 209, 220)",
                            "rgb(221, 126, 107)", "rgb(234, 153, 153)", "rgb(249, 203, 156)", "rgb(255, 229, 153)", "rgb(182, 215, 168)",
                            "rgb(162, 196, 201)", "rgb(164, 194, 244)", "rgb(159, 197, 232)", "rgb(180, 167, 214)", "rgb(213, 166, 189)",
                            "rgb(204, 65, 37)", "rgb(224, 102, 102)", "rgb(246, 178, 107)", "rgb(255, 217, 102)", "rgb(147, 196, 125)",
                            "rgb(118, 165, 175)", "rgb(109, 158, 235)", "rgb(111, 168, 220)", "rgb(142, 124, 195)", "rgb(194, 123, 160)",
                            "rgb(166, 28, 0)", "rgb(204, 0, 0)", "rgb(230, 145, 56)", "rgb(241, 194, 50)", "rgb(106, 168, 79)",
                            "rgb(69, 129, 142)", "rgb(60, 120, 216)", "rgb(61, 133, 198)", "rgb(103, 78, 167)", "rgb(166, 77, 121)",
                            "rgb(133, 32, 12)", "rgb(153, 0, 0)", "rgb(180, 95, 6)", "rgb(191, 144, 0)", "rgb(56, 118, 29)",
                            "rgb(19, 79, 92)", "rgb(17, 85, 204)", "rgb(11, 83, 148)", "rgb(53, 28, 117)", "rgb(116, 27, 71)",
                            "rgb(91, 15, 0)", "rgb(102, 0, 0)", "rgb(120, 63, 4)", "rgb(127, 96, 0)", "rgb(39, 78, 19)",
                            "rgb(12, 52, 61)", "rgb(28, 69, 135)", "rgb(7, 55, 99)", "rgb(32, 18, 77)", "rgb(76, 17, 48)","transparent"]
                    ]
                });

                /*
                 $(Editor.toolbarElements.colorpicker).colorpicker();
                 var editorStyle='';
                 var buttonStyle = $(Editor.toolbarElements.colorpicker ).find('i')[0].style;
                 $(Editor.toolbarElements.colorpicker).colorpicker().on('changeColor', function(ev) {
                 var selected = $(Editor.editorAreaElememts.editorArea ).find('.'+defaults.elementSelected );
                 TOlog('colorpicker is selected?',selected.length)
                 if(selected.length == 1){
                 editorStyle = selected[0].style;
                 } else {
                 editorStyle = $(Editor.editorAreaElememts.editorArea)[0].style;
                 }
                 buttonStyle.backgroundColor = ev.color.toRGB();
                 editorStyle.backgroundColor = ev.color.toRGB();
                 $(mainElements.slideshowContainer).find('li[id="'+defaults.selectedSlideId+'"]' ).attr('data-bg',ev.color.toRGB());
                 });
                 */
            },

            resetColorPicker : function(){
                var editorStyle = $(Editor.editorAreaElememts.editorArea)[0].style;
                var buttonStyle = $(Editor.toolbarElements.colorpicker ).find('i')[0].style;
                buttonStyle.backgroundColor = 'rgba(255,255,255,1)';
                editorStyle.backgroundColor = 'none';
            },

            setColorPicker : function(color){
                if( color == '' ) color = 'rgba(255,255,255,1)';
                var editorStyle = $(Editor.editorAreaElememts.editorArea)[0].style;
                var buttonStyle = $(Editor.toolbarElements.colorpicker ).find('i')[0].style;
                buttonStyle.backgroundColor = color;
                editorStyle.backgroundColor = color;
            },

            setColor : function(color){
                var selected = $(Editor.editorAreaElememts.editorArea ).find('.'+defaults.elementSelected );
                var buttonStyle = $(Editor.toolbarElements.colorpicker ).find('i')[0].style;
                var editorStyle = $(Editor.editorAreaElememts.editorArea)[0].style;
                var db = selected.length;
                if(db){
                    editorStyle = selected[0].style;
                } else {
                    editorStyle = $(Editor.editorAreaElememts.editorArea)[0].style;
                    selected = $(Editor.editorAreaElememts.editorArea );
                }

                var bgcolor = color == 'rgba(0, 0, 0, 0)' ? '' : color.toRgbString();
                buttonStyle.backgroundColor = bgcolor;
                editorStyle.backgroundColor = bgcolor;

                if(!db){
                    $(mainElements.slideshowContainer).find('li[id="'+defaults.selectedSlideId+'"]' ).attr('data-bg',bgcolor);
                    $(mainElements.slideshowContainer).find('li[id="'+defaults.selectedSlideId+'"] > .dataHolder > .leftSide' ).css('background-color',bgcolor == '' ? 'white' : bgcolor);
                    $(Editor.editorAreaElememts.editorArea).attr('data-bg',bgcolor);
                }

                defaults.slideSaved = false;
                Editor.helperFunctions.checkSlideState();
            }

        },

        bottombarElements : {
            bottomBar : document.getElementById('editorBottomBar') || '',
            slidetagInput : document.getElementById('slideTag') || '',
            description : document.getElementById('descArea') || '',
            levelCount : document.getElementsByName('levelCount') || '',
            testEditorContainer : document.getElementsByClassName('testSlideEditorContainer') || '',

            init : function(){
                addEventO(Editor.bottombarElements.slidetagInput,'keypress',function(){
                    if($(mainElements.slideshowContainer).find('.dataHolder.selected').length == 1 ) {
                        defaults.slideSaved = false;
                        Editor.helperFunctions.checkSlideState();
                    }
                }, true, _eventHandlers)
            }
        },

        init : function(){
            Editor.toolbarElements.init();
            Editor.bottombarElements.init();
            Editor.editorAreaElememts.init();

        },

        addDND : function(){
            $(Editor.editorAreaElememts.editorArea).droppable( Editor.helperFunctions.ui.editorDropOption );
            $(Editor.editorAreaElememts.editorArea).find( '.'+defaults.nonResizableClass ).draggable( Editor.helperFunctions.ui.editorElementdraggable );
            $(Editor.editorAreaElememts.editorArea).find( '.'+defaults.resizableClass ).draggable( Editor.helperFunctions.ui.editorElementdraggable )

            $(Editor.editorAreaElememts.editorArea).find( '.'+defaults.resizableClass ).each(function () {
                //$(this).on('resize')

                if ( $( this ).hasClass( 'video' ) ) {
                    $( this ).resizable( Editor.helperFunctions.ui.resizableVideoOptions );
                } else {
                    $( this ).resizable( Editor.helperFunctions.ui.resizableOptions );
                }
            } );

            $(Editor.editorAreaElememts.editorArea).find( '.'+defaults.resizableClass ).each(function () {
                if ( $( this ).hasClass( 'video' ) ) {
                    $( this ).resizable( Editor.helperFunctions.ui.resizableVideoOptions );
                } else {
                    $( this ).resizable( Editor.helperFunctions.ui.resizableOptions );
                }
            } );

            $(Editor.editorAreaElememts.editorArea).find( '.ui-resizable-handle' ).attr( 'style', 'z-index:1000;' );



            /*
             $(Editor.editorAreaElememts.editorArea).find('.textDiv').addEventListener("input", function() {
             Editor.helperFunctions.checkSlideState();
             }, false);
             */

            $( '[contenteditable]' ).live( 'focus',function () {
                var $this = $( this );

                $this.data( 'before', $this.html() );
                return $this;
            } ).live( 'blur keyup paste', function () {
                    var $this = $( this );
                    if ( $this.data( 'before' ) !== $this.html() ) {
                        $this.data( 'before', $this.html() );
                        $this.trigger( 'change' );
                        defaults.slideSaved = false;
//Editor.helperFunctions.checkSlideState();

                    }
                    return $this;
                } );
        },

        removeDND : function(){
            if ($(Editor.editorAreaElememts.editorArea).data('draggable')) {
            }
        },

        helperFunctions : {

            ckedit : {
                clearEditorInstances: function () {
                    for (name in CKEDITOR.instances) {
                        CKEDITOR.instances[name].destroy()
                    }
                },

                createEditorInstance: function (iId, inline) {
                    //inline = false;
                    if (!document.getElementById(iId)) return false;
                    var inlineInstance = (!!(inline == true));
////console.log( 'instance id: '+inlineInstance );
                    var myinstances = [];
                    var editor = CKEDITOR.inline(iId);
//this is the foreach loop
                    return false;

                    if (inlineInstance)
                        var editor = CKEDITOR.inline(iId);
                    if (!inlineInstance) {
                        CKEDITOR.inline( iId, {
                            extraPlugins: 'sharedspace',
                            removePlugins: 'floatingspace,resize',
                            sharedSpaces: {
                                top: 'myNicPanel',
                                bottom: 'bottom'
                            }
                        });
                        /*
                         CKEDITOR.replace(iId, {
                         sharedSpaces: {
                         top: 'myNicpanel',
                         bottom: 'editorBottomBar'
                         }
                         });
                         */
                    }
                    return false;
                },

                removeEditorInstance : function(id){
                    CKEDITOR.instances[id].destroy()
                },

                listEditorInstances : function(){
                    for (var i in CKEDITOR.instances) {
                    }
                }
            },

            ui : {
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
//console.log( 'editordropoption DROP fired (pervoffset check) ' +  defaults.prevOffset);
                        if ( !defaults.prevOffset &&
                            ($(Editor.editorAreaElememts.editorArea).attr( 'data-template-type' ) !== 'normal' &&
                                $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type' ) !== 'puzzle') ) {
                            //defaults.prevOffset=true;
                            //console.log( 'kukazva az elem' );
                            //2013.02.12
                            //return false;
                        }
                        var isPuzzle = false;
                        if ( $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type' ) == 'puzzle' )
                            isPuzzle = true;

//console.log( 'editordropoption DROP defaults.prevOffset: ' +  defaults.prevOffset);
//console.log( 'editordropoption DROP defaults.prevOffset: ' +  ui.draggable.children().attr('class') );
                        if ( !defaults.prevOffset ) return false;
                        //var $elementHolder;
                        var eW, eH, nW, nH;
                        var results = [];
                        results['result'] = [];
                        switch ( ui.draggable.children().attr( 'class' ) ) {
                            case 'colorBar image':
                                //2013.02.12
                                if ( isPuzzle && $(Editor.editorAreaElememts.editorArea).find( 'img' ).length > 0 ) return false;

                                eW = 160;
                                //eH = 120;
                                nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                                //nH = parseInt( eH / defaults.editingAreaHeight * 100 );

                                results['result'].push( {
                                    'mediaurl': ui.draggable.attr( 'data-mediaurl' ),
                                    'left'    : posX + '%',
                                    'top'     : posY + '%',
                                    'width'   : nW + '%',
                                    'height'  : 'auto'//nH + '%'//
                                } );
                                var result = tmpl( "tmpl-eaImage", results );
//console.log( 'image dropped to editor');
                                $( this ).append( $( result ) );
                                //$(Editor.editorAreaElememts.editorArea).append( $(result) );
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
                                $(Editor.editorAreaElememts.editorArea).append( $( result ) );
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
                                    'height'  : nH + '%',
                                    'w':nW,
                                    'h':nH
                                } );
//console.log( results );
                                var result = tmpl( tmplName, results );
                                $(Editor.editorAreaElememts.editorArea).append( $( result ) );
                                break;
                            case 'btn btn-dark':
                                //groupping template
                                if ( isPuzzle ) return false;
                                var element, group = ui.helper.clone();
                                var parentOffset = $(Editor.editorAreaElememts.editorArea).offset(),
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
                                $(Editor.editorAreaElememts.editorArea).append( element );
                                Editor.helperFunctions.ckedit.createEditorInstance(iId)
                                //editor.addInstance( iId ).floatingPanel();

                                break;
                        }
                        //$(Editor.editorAreaElememts.editorArea).find('video').skVideo();
                        //$('#editorsMediaBox li').draggable('destroy');
                        Editor.helperFunctions.addUIevents();
                        //$(Editor.editorAreaElememts.editorArea).droppable('destroy');
                        defaults.slideSaved = false;
                        defaults.slideShowSaved = Editor.helperFunctions.checkFormState( 'saveslidesform' );
                        Editor.helperFunctions.checkSlideState();

                    },
                    stop       : function ( e, ui ) {
                        var posX = parseInt( $( this ).offset().left / defaults.editingAreaWidth * 100 );
                        var posY = parseInt( $( this ).offset().top / defaults.editingAreaWidth * 100 );
                        var eW = $( this ).width(), eH = $( this ).height();
                        var nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                        var nH = parseInt( eH / defaults.editingAreaHeight * 100 );
                        if ( $( this ).hasClass( defaults.textClass ) )
                            $( this ).css( 'height', 'auto' );
                        //$( this ).css( {'left': posX + '%', 'top': posY + '%', 'width': nW + '%'/*, 'height':nH+'%'*/} );
                        this.style.left =posX + '%';
                        this.style.top =posY + '%';
                        this.style.width =nW + '%';
                        //this.style.height =nH + '%';


                    }/*,
                     out: function(event, ui){
                     //console.log( 'drop failde' );
                     }*/
                },

                editorElementdraggable : {
                    handle     : 'div.movingBox',
                    containment: "#editorArea",
                    drag       : function ( e, ui ) {
                        Editor.helperFunctions.removeToolsEvent();
                        //var posX = parseInt($(this).position().left/defaults.editingAreaWidth*100);
                        //var posY = parseInt($(this).position().top/defaults.editingAreaWidth*100);
                        //var eW = $(this).width(), eH=$(this).height();
                        //var nW=parseInt(eW/defaults.editingAreaWidth*100);
                        //var nH=parseInt(eH/defaults.editingAreaHeight*100);
                        //$(this).css({'left':posX+'%', 'top':posY+'%', 'width':nW+'%', 'height':nH+'%'});
                        ////console.log( 'positionLeft: '+$(this).position().left+' defaults.editingAreaWidth: '+defaults.editingAreaWidth+' percent: '+$(this).position().left/(defaults.editingAreaWidth-20)*100);
                    },
                    stop       : function ( e, ui ) {
                        ////console.log( $(this).position().left/$(this).parent().outerWidth()*100 );
                        var posX = $( this ).position().left / defaults.editingAreaWidth * 100;//parseInt();
                        var posY = $( this ).position().top / defaults.editingAreaHeight * 100;//parseInt();
                        var eW = $( this ).outerWidth(), eH = $( this ).outerHeight();
                        //var nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                        //var nH = parseInt( eH / defaults.editingAreaHeight * 100 );
                        //$( this ).css( {'left': posX + '%', 'top': posY + '%', 'width': nW + '%'/*, 'height':nH+'%'*/} );
                        this.style.left =posX + '%';
                        this.style.top =posY + '%';
                        //this.style.width =nW + '%';

                        //console.log('editor element drag stopped');
                        defaults.slideSaved = false;
                        defaults.slideShowSaved = Editor.helperFunctions.checkFormState( 'saveslidesform' );
                        Editor.helperFunctions.checkSlideState();
                        Editor.helperFunctions.addToolsEvent( $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type' ) );
                    }
                },

                resizableOptions : {
                    helper: "ui-resizable-helper",
                    ghost : false,
                    //"aspectRatio": true,
                    stop  : function ( event, ui ) {
                        var posX = $( this ).position().left / defaults.editingAreaWidth * 100;//parseInt();
                        var posY = $( this ).position().top / defaults.editingAreaHeight * 100;//parseInt();
                        var eW = $( this ).outerWidth(), eH = $( this ).outerHeight();
                        var nW = parseInt( eW / defaults.editingAreaWidth * 100 );
                        var nH = parseInt( eH / defaults.editingAreaHeight * 100 );
                        //$( this ).css( {'left': posX + '%', 'top': posY + '%', 'width': nW + '%', 'height': nH + '%'} );
                        this.style.left =posX + '%';
                        this.style.top =posY + '%';
                        this.style.width =nW + '%';
                        this.style.height =nH + '%';

                        if($(this ).parent().find('.sortableForm' ).length = 0)
                            $(this ).find('.textDiv' ).css( 'height', '100%');

                        //if ( $( this ).hasClass( defaults.textClass ) )
                        //    $( this ).css( 'height', 'auto' );//
                        defaults.slideSaved = false;
                        defaults.slideShowSaved = Editor.helperFunctions.checkFormState( 'saveslidesform' );
                        Editor.helperFunctions.checkSlideState();
                        var iframe = $( this ).find( 'iframe' );
                        iframe.css( 'pointer-events', 'none' );
                    },
                    start : function ( event, ui ) {
                        var iframe = $( this ).find( 'iframe' );
                        iframe.css( 'pointer-events', 'none' );
                    },
                    resize: function ( e, ui ) {
                        /*
                         var ed = document.getElementById( 'editorArea' ),
                         ce = document.getElementById( 'ContentEditor' ),
                         eWidth = ed.offsetWidth,
                         eHeight = ed.offsetHeight,
                         uiposLeft = ui.position.left - ce.offsetLeft - ed.offsetLeft,
                         uiposTop = ui.position.top - ce.offsetTop - ed.offsetTop,
                         maxHeight = eHeight - uiposTop - 1,
                         maxWidth = eWidth - uiposLeft - 1;
                         $( '#editorArea .' + defaults.resizableClass ).resizable( "option", "maxWidth", maxWidth );
                         $( '#editorArea .' + defaults.resizableClass ).resizable( "option", "maxHeight", maxHeight );
                         */
                        //$('#editorArea .'+defaults.resizableClass).css('height','auto');

                    }
                },

                resizableVideoOptions: {
                    helper       : "ui-resizable-helper",
                    ghost        : false,
                    aspectRatio  : true,
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
                        //$( this ).css( {'left': posX + '%', 'top': posY + '%', 'width': nW + '%', 'height': nH + '%'} );
                        this.style.left =posX + '%';
                        this.style.top =posY + '%';
                        this.style.width =nW + '%';
                        this.style.height =nH + '%';
                        var iframe = $( this ).find( 'iframe' );
                        iframe.attr( 'src', iframe.attr( 'src' ).replace( /w=(.*?)&h=/g, 'w=' + nW + '&h=' ) );
                        iframe.css( 'pointer-events', 'auto' );
                        defaults.slideSaved = false;
                        defaults.slideShowSaved = Editor.helperFunctions.checkFormState( 'saveslidesform' );
                        Editor.helperFunctions.checkSlideState();
                        if ( $( this ).hasClass( defaults.textClass ) ) {
                            $( this ).css( 'height', 'auto' );
                        }
                    },

                    resize: function ( e, ui ) {
                        /*
                         var ed = Editor.editorAreaElememts.editorArea,
                         ce = document.getElementById( 'ContentEditor' ),
                         eWidth = ed.offsetWidth,
                         eHeight = ed.offsetHeight,
                         uiposLeft = ui.position.left - ce.offsetLeft - ed.offsetLeft,
                         uiposTop = ui.position.top - ce.offsetTop - ed.offsetTop,
                         maxHeight = eHeight  - 1,
                         maxWidth = eWidth  - 1;

                         $(Editor.editorAreaElememts.editorArea).find('.'+defaults.resizableClass)
                         .resizable( "option", "maxWidth", maxWidth )
                         .resizable( "option", "maxHeight", maxHeight )
                         */
                        //$( '#editorArea .' + defaults.resizableClass ).resizable( "option", "maxWidth", maxWidth );
                        //$( '#editorArea .' + defaults.resizableClass ).resizable( "option", "maxHeight", maxHeight );

                        //$('#editorArea .'+defaults.resizableClass).css('height','auto');

                    }
                }

            },

            addUIevents: function () {
//console.log( 'addUIevents fired' );
                //$( '#editorArea .' + defaults.nonResizableClass )
                $(Editor.editorAreaElememts.editorArea).find('.'+defaults.nonResizableClass)
                    .draggable( Editor.helperFunctions.ui.editorElementdraggable );
                //$( '#editorArea .' + defaults.resizableClass )
                $(Editor.editorAreaElememts.editorArea).find('.'+defaults.resizableClass)
                    .draggable( Editor.helperFunctions.ui.editorElementdraggable )
                //$.each( $( '#editorArea .' + defaults.resizableClass ), function () {
                $(Editor.editorAreaElememts.editorArea).find('.'+defaults.resizableClass).each(function(){
                    if ( $( this ).hasClass( 'video' ) ) {
                        $( this ).resizable( Editor.helperFunctions.ui.resizableVideoOptions );
                    } else {
                        $( this ).resizable( Editor.helperFunctions.ui.resizableOptions );
                    }
                } );

                $(Editor.editorAreaElememts.editorArea).find( '.ui-resizable-handle' ).attr( 'style', 'z-index:1000;' );
                $(mainElements.slideshowContainer).nestedSortable( slideshowElements.sortableSlides );
                /*
                 $(Editor.editorAreaElememts.editorArea).find('.textDiv').addEventListener("input", function() {
                 Editor.helperFunctions.checkSlideState();
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
                            defaults.slideSaved = false;
                            Editor.helperFunctions.checkSlideState();
//console.log( 'blur keyup paste event fired' );
                        }
                        return $this;
                    } );
            },

            removeUIevents: function () {
//console.log( 'removeUIevents fired' );
                var $elem = $( '#editorArea .' + defaults.nonResizableClass + ':data(draggable)' );
                if ( $elem.length )
                    $( '#editorArea .' + defaults.nonResizableClass ).draggable( 'destroy' );
                var $elem = $( '#editorArea .' + defaults.resizableClass + ':data(resizable)' );
                if ( $elem.length )
                    $( '#editorArea .' + defaults.resizableClass ).resizable( 'destroy' );
                var $elem = $( '#editorArea .' + defaults.resizableClass + ':data(draggable)' );
                if ( $elem.length )
                    $( '#editorArea .' + defaults.resizableClass ).draggable( 'destroy' );
            },

            serializeForm: function ( container, templatetype ) {
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
                        returnData.answare = JSON.stringify( $( Editor.editorAreaElememts.editorForm ).serializeArray() );
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
                var html2 = textEditingHelperfunctions.purgeHtml( container, templatetype );
                returnData.html2 = html2;
                return returnData;
            },

            addToolsEvent: function ( templateType ) {
//console.log( 'addToolsEvent fired' );
////console.log( 'toolsevent: '+ $('[data-slide-type="template"] > .selected').length );
                //if($('#slidesList [data-slide-type="template"] .selected').length == 0) $('.tools').html('');

                var $elem = $( '#editorArea .sortableForm li:data(sortable)' );

//console.log( 'van sortable? : '+$elem.length );
                var uId = createUID2();
                switch ( templateType ) {
                    case 'radio':

                        $( '.tools' ).html( '<li class="holder"><span class="btn btn-dark"><span class="icon-move"></span></span><input type="radio" name="options[]" value="0" class="regular-radio big-radio" id="' + uId + '"><label for="' + uId + '"></label><div class="textDiv" contenteditable="true">Option one is this and that</div></li>' );
                        $( '#editorArea .sortableForm' ).droppable( testslidesElements.formDrop );
                        $( '#editorArea .sortableForm li' ).sortable( testslidesElements.formSortable );
                        $( '.tools li' ).draggable( testslidesElements.toolsDrag );
                        break;
                    case 'check':
                        $( '.tools' ).html( '<li class="holder"><span class="btn btn-dark"><span class="icon-move"></span></span><input type="checkbox" name="chk[]" value="0" class="regular-checkbox big-checkbox" id="' + uId + '"><label for="' + uId + '"></label><div class="textDiv" contenteditable="true">Option one is this and that</div></li>' );
                        $( '#editorArea .sortableForm' ).droppable( testslidesElements.formDrop );
                        $( '#editorArea .sortableForm li' ).sortable( testslidesElements.formSortable );
                        $( '.tools li' ).draggable( testslidesElements.toolsDrag );
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
                        $( '#editorArea .sortableForm' ).droppable( testslidesElements.formDrop );
                        $( '.tools .holder' ).draggable( testslidesElements.toolsDrag );
                        $( '#editorArea .textDiv' ).focus();
                        /**/
                        break;
                    case 'sorting' : //sorting answers
                        $( '.tools' ).html( '<li class="holder"><span class="btn btn-dark"><span class="icon-move"></span></span><div class="textDiv" contenteditable="true">sortable element</div></li>' );
                        $( '#editorArea .sortableForm' ).droppable( testslidesElements.formDrop );
                        $( '#editorArea .sortableForm li' ).sortable( testslidesElements.formSortable );
                        $( '.tools li' ).draggable( testslidesElements.toolsDrag );
                        break;
                    case 'groupping': //groups and memebers n sortable list with connectTo
                        $( '.tools' ).html( '<li class="holder group"><span class="btn btn-dark"><span class="icon-move"></span></span>' +
                            '<div class="ResizableClass textClass isSelected" style=""><div class="textDiv header">New group</div><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><ul class="sortableForm ui-sortable ui-droppable"></ul></div>' +
                            '</li>' );
                    case 'pairing':
                        $( '.tools' ).append( '<li class="holder"><span class="btn btn-dark"><span class="icon-move"></span></span><div class="textDiv" contenteditable="true">sortable element</div></li>' );

                        $( '#editorArea .sortableForm' ).droppable( testslidesElements.formDrop );
                        $( '#editorArea .sortableForm li' ).sortable( testslidesElements.formSortable );
                        $( '.tools li:not(.holder.group)' ).draggable( testslidesElements.toolsDrag );
                        $( '.tools li.holder.group' ).draggable( testslidesElements.toolsDrag2 );
                        $( '#editorArea .sortableHolder' ).draggable( Editor.helperFunctions.ui.editorElementdraggable );
                        break;
                    case 'puzzle':
                        //$('#editorsMediaBox li').draggable(myMediaDragOption);
//console.log( 'puzzle' );

                        break;
                    case '29p_1':
                    case '29p_2':
                        var inputElements = $(Editor.editorAreaElememts.editorArea).find( ':input' );
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
                            data['result'].push( {'prev': '', 'prevID': '', 'score': 99, 'next': '', 'nextID': ''} );
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

            },

            removeToolsEvent : function () {
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
            },

            ////////////////////////////////////////////
            // functions for test slides
            ////////////////////////////////////////////
            se_clearData: function ( type ) {
//console.log( 'se_clearData fired' );
                ////////////////////////////////////////////
                // clear data/pointers/dataholders
                // even new normal/test slides is created/loaded
                ////////////////////////////////////////////
                switch ( type ) {
                    case 'editor':

                        $( '#saveslidesform input[name="id"]' ).val( '' );
                        $( '#saveslidesform input[name="name"]' ).val( '' );
                        defaults.templateSlide = false;
                    case 'all':
                        $(Editor.editorAreaElememts.editorArea).attr( 'data-template-type', 'normal' );
                        $(Editor.editorAreaElememts.editorArea).empty();

                        Editor.helperFunctions.ckedit.clearEditorInstances();
                        $(mainElements.slideshowContainer).empty();
                        $( '#bc' ).html( '' );
                        $( '#myNicPanel' ).empty();
                        slidesHelperfunctions.storeslideIDs();
                    case 'template':
                        $( '.testSlideEditorContainer' ).hide();
                        $( '.trafficDataHolder, .tools' ).html( '' );
                        defaults.templateSlide = false;
                    case 'normal':
                        //$(Editor.editorAreaElememts.editorArea).html('');
                        $( '#testSlides .slideElement .rightSide' ).parent().removeClass( 'selected' );
                        //nicEditors.editors= [];
                        $( '#editorBottomBar .level' ).hide();
                        $( '#slideTag' ).val( '' );
                        defaults.triggerSave = false;
                        defaults.slideShowSaved = true;
                        defaults.slideSaved = true;
                        $(Editor.editorAreaElememts.editorArea).empty();
                        break;
                }
            },

            checkFormState : function ( formID ) {
                var state = ($( '#' + formID + ' input[name="id"]' ).val() != '');
                (state === false) ? $( '#saveSlide' ).removeClass( 'btn-dark' ).addClass( 'btn-danger' ) : $( '#saveSlide' ).addClass( 'btn-dark' ).removeClass( 'btn-danger' );
                return state;
                //sendMessage('alert-warning', 'Your slidshow is saved. U can rename it!');
            },

            checkSlideState: function () {
                var state = defaults.slideSaved;// ? true : false;
                (!state) ? $(Editor.toolbarElements.saveSlideButton).removeClass( 'btn-dark' ).addClass( 'btn-danger' ) : $(Editor.toolbarElements.saveSlideButton).addClass( 'btn-dark' ).removeClass( 'btn-danger' );
                return state;
                //sendMessage('alert-warning', 'Your slidshow is saved. U can rename it!');
            }

        }
    };

    var slidesHelperfunctions = {

        handeslideAfterEvents: function () {
            //2013.01.19
            Editor.helperFunctions.addUIevents();
            //recalculateScrollHeight( $('#slidesList') );
            $(mainElements.slideshowContainer).nestedSortable( sortableSlides );
            slidesHelperfunctions.storeslideIDs();
            defaults.slideSaved = true;
        },

        setTrafficData: function () {
            $(mainElements.testsContainer ).find('.dataHolder' ).each(function(){
                $(this ).removeClass('selected');
            });

            var prevS,
                scoreS,
                nextS,
                obj = [];
            $('.trafficmanagerContainer .control-group').each(function (i, e) {
                prevS = $(this).find('#slidePrev').val() ,
                    scoreS = $(this).find('#slideScore').val(),
                    nextS = $(this).find('#slideNext').val();

                var prevID = slidesHelperfunctions.searchTrafficData(prevS),
                    nextID = slidesHelperfunctions.searchTrafficData(nextS);

                obj.push({/*'id':i,'name':'radio', 'good':1,*/
                    'prev': prevS,
                    'prevID': (typeof(prevID) == 'undefined' ? 0 : prevID),
                    'score': scoreS,
                    'next': nextS,
                    'nextID': (typeof(nextID) == 'undefined' ? 0 : nextID)
                });
            });
            return obj;
        },

        //get traffic managers data from slide
        getTrafficData: function (slideelement) {
            var $datalines = slideelement.find('.whiteLine');
            var data = [];
            data['result'] = [];

            $.each($datalines, function (i, e) {

                data['result'].push({
                    'prev': $(e).find('span.badge.prev').text(),
                    'prevID': $(e).find('span.badge.prev').attr('data-slide-id'),
                    'score': $(e).find('span.badge.score').text(),
                    'next': $(e).find('span.badge.next').text(),
                    'nextID': $(e).find('span.badge.next').attr('data-slide-id')
                });
            });
            $('.trafficDataHolder').empty();
            return data;
        },

        //store slides ID-s in an array
        storeslideIDs: function () {
            slideIDs = [];
            $.each($('#slidesList li.slideElement'), function () {
                var key = $(this).attr('id'),
                    value = parseInt($(this).find('span.badge.nr').text());
                slideIDs[key] = value;
            });
        },

        //return slidelement id for a given badge(nr)
        searchTrafficData: function (value) {
            for (var key in slideIDs) {
                if (slideIDs[key] == value)
                    return key;

            }
        },

        //after reordering or removing or inserting slideelement
        //must the ID holder array updated
        updateSlidesIDarray: function (ts, i, option) {
            switch (option) {
                case 'new'   :
                    slideIDs[ts] = i;
                    break;
                case 'delete':
                    delete slideIDs[ts];
                    break;
                case 'update':

                    break;
            }
        },

        //get slides ID, when the traffic data is known
        getslidesIDforTrafficData: function (badge) {
            for (i in slideIDs) {
                if (slideIDs[i] == badge) return i;
            }
        },

        //after reordering slideelement list must correct traffic data pointers
        refreshslidesIDinTrafficData: function (ts) {
            var i = slideIDs[ts];
            var trafficElement = $('#slidesList .trafficData').filter(function () {
                return $(this).find('span.badge.nr').attr('data-slide-id') == ts
            });
            var trafficElement = $('#slidesList .trafficData').find('[data-slide-id="' + ts + '"]');
            for (var j = 0; j < trafficElement.length; j++)
                $(trafficElement[j]).text(slideIDs[ts]);
        },

        //search for id in traffic data if user want to delete slide
        searchslidesIDinTrafficData: function (ts) {
            var i = slideIDs[ts];
            var trafficElement = $('#slidesList .trafficData').filter(function () {
                return $(this).find('span.badge.nr').attr('data-slide-id') == ts
            });
            var trafficElement = $('#slidesList .trafficData').find('[data-slide-id="' + ts + '"]');
            return trafficElement.length == 0 ? false : true;
            //for(var j=0;j<trafficElement.length;j++)
            //$(trafficElement[j]).text(slideIDs[ts]);
        },

        //when remove a slide and template slide pointing to it, set number to 0
        deleteslidesIDinTrafficData: function (ts) {
            var i = slideIDs[ts];
            var trafficElement = $('#slidesList .trafficData').filter(function () {
                return $(this).find('span.badge.nr').attr('data-slide-id') == ts
            });
            var trafficElement = $('#slidesList .trafficData').find('[data-slide-id="' + ts + '"]');
            for (var j = 0; j < trafficElement.length; j++)
                $(trafficElement[j]).text(0).attr('data-slide-id', '0');
        }

    };

    var textEditingHelperfunctions = {
        handlepaste: function (elem, e) {
            var savedcontent = elem.innerHTML;
            var clipboardData;
            if (e && e.clipboardData && e.clipboardData.getData) {// Webkit - get data from clipboard, put into editdiv, cleanup, then cancel event
                if (/text\/html/.test(e.clipboardData.types)) {
                    clipboardData = e.clipboardData.getData('text/html');
                } else if (/text\/plain/.test(e.clipboardData.types)) {
                    clipboardData = e.clipboardData.getData('text/plain');
                } else {
                    clipboardData = "";
                }

                waitforpastedata(elem, clipboardData);
                if (e.preventDefault) {
                    e.stopPropagation();
                    e.preventDefault();
                }
                return false;
            }
            else {// Everything else - empty editdiv and allow browser to paste content into it, then cleanup

                //elem.innerHTML = "";
                textEditingHelperfunctions.waitforpastedata(elem, clipboardData);
                return true;
            }
        },

        waitforpastedata: function (elem, savedcontent) {

            if (elem.childNodes && elem.childNodes.length > 0) {
                textEditingHelperfunctions.processpaste(elem, savedcontent);
            }
            else {
                that = {
                    e: elem,
                    s: savedcontent
                }
                that.callself = function () {
                    textEditingHelperfunctions.waitforpastedata(that.e, that.s)
                }
                setTimeout(that.callself, 20);
            }
        },

        processpaste: function (elem, savedcontent) {
            pasteddata = elem.innerHTML;
            //^^Alternatively loop through dom (elem.childNodes or elem.getElementsByTagName) here
            // Do whatever with gathered data;
            return textEditingHelperfunctions.pasteHtmlAtCaret(textEditingHelperfunctions.cleanHTML(savedcontent));

        },

        //////////////////////////////////////////////
        //insert form element in a contenteditable div
        // to the cursor position
        //////////////////////////////////////////////
        pasteHtmlAtCaret: function (html) {
            var sel, range;
            if (window.getSelection) {
                // IE9 and non-IE
                sel = window.getSelection();
                if (sel.getRangeAt && sel.rangeCount) {
                    range = sel.getRangeAt(0);
                    range.deleteContents();
                    // Range.createContextualFragment() would be useful here but is
                    // non-standard and not supported in all browsers (IE9, for one)
                    var el = document.createElement("div");
                    el.innerHTML = html;
                    var frag = document.createDocumentFragment(), node, lastNode;
                    while ((node = el.firstChild)) {
                        lastNode = frag.appendChild(node);
                    }
                    range.insertNode(frag);
                    // Preserve the selection
                    if (lastNode) {
                        range = range.cloneRange();
                        range.setStartAfter(lastNode);
                        range.collapse(true);
                        sel.removeAllRanges();
                        sel.addRange(range);
                    }
                }
            } else if (document.selection && document.selection.type != "Control") {
                // IE < 9
                document.selection.createRange().pasteHTML(html);
            }
            defaults.prevOffset = false;
        },

//check if getselection is in a contenteditables div by id

        isOrContains: function (node, container) {
            while (node) {
                if (node === container) {
                    return true;
                }
                node = node.parentNode;
            }
            return false;
        },

        elementContainsSelection: function (el) {
            var sel;
            if (window.getSelection) {
                sel = window.getSelection();
                if (sel.rangeCount > 0) {
                    for (var i = 0; i < sel.rangeCount; ++i) {
                        if (!isOrContains(sel.getRangeAt(i).commonAncestorContainer, el)) {
                            return false;
                        }
                    }
                    return true;
                }
            } else if ((sel = document.selection) && sel.type != "Control") {
                return isOrContains(sel.createRange().parentElement(), el);
            }
            return false;
        },

        ///////////////////////////////
        // paste word html to contenteditable
        // and clean it
        ///////////////////////////////
        cleanHTML: function (in_word_text) {
            var tmp = document.createElement("span");
            tmp.innerHTML = in_word_text;
            var newString = tmp.textContent || tmp.innerText;
            // this next piece converts line breaks into break tags
            // and removes the seemingly endless crap code
            newString = newString.replace(/\n\n/g, "").replace(/<!--[\s\S]*?-->/g, "").replace(/<(.|\n)*?>/g, " ");
            newString = newString.replace(/<(.|\n)*?>/g, " ");
            newString = textEditingHelperfunctions.replaceNbsps(newString);
            // this next piece removes any break tags (up to 10) at beginning
            for (i = 0; i < 100; i++) {
                if (newString.substr(0, 6) == "<br />") {
                    newString = newString.replace("<br />", "");
                }
            }
            return newString;
        },

        //////////////////////////////////
        // copy paste from clipboard to contenteditable
        //////////////////////////////////
        getInputSelection: function (el) {

//return false;
            var start = 0, end = 0, normalizedValue, range,
                textInputRange, len, endRange;

            if (typeof el.selectionStart == "number" && typeof el.selectionEnd == "number") {
                start = el.selectionStart;
                end = el.selectionEnd;
            } else {
                range = getSelectedText();//document.selection.createRange();

                if (range) {//&& range.parentElement() == el) {
                    len = el.innerHTML.length;
                    normalizedValue = el.innerHTML.replace(/\r\n/g, "").replace(/\n\n/g, "").replace(/<!--[\s\S]*?-->/g, "").replace(/<(.|\n)*?>/, "");
                    normalizedValue = textEditingHelperfunctions.replaceNbsps(normalizedValue);


                    // Create a working TextRange that lives only in the input
                    textInputRange = el.createTextRange();
                    textInputRange.moveToBookmark(range.getBookmark());

                    // Check if the start and end of the selection are at the very end
                    // of the input, since moveStart/moveEnd doesn't return what we want
                    // in those cases
                    endRange = el.createTextRange();
                    endRange.collapse(false);

                    if (textInputRange.compareEndPoints("StartToEnd", endRange) > -1) {
                        start = end = len;
                    } else {
                        start = -textInputRange.moveStart("character", -len);
                        start += normalizedValue.slice(0, start).split("\n").length - 1;

                        if (textInputRange.compareEndPoints("EndToEnd", endRange) > -1) {
                            end = len;
                        } else {
                            end = -textInputRange.moveEnd("character", -len);
                            end += normalizedValue.slice(0, end).split("\n").length - 1;
                        }
                    }
                }
            }

            return {
                start: start,
                end: end
            };
        },

        replaceNbsps: function (str) {
            var re = new RegExp(String.fromCharCode(160), "g");
            return str.replace(re, " ");
        },

        //contenteditable true and get selected range
        getSelectedText: function () {
            if (window.getSelection) {
                return window.getSelection();
            }
            else {
                if (document.selection) {
                    return document.selection.createRange();//.text;
                }
            }
            return '';
        },

        ///////////////////////////////////
        //purgeHtml for slideshow
        //remove move/delete/resize elements
        // unwrap myinstance div around text
        ///////////////////////////////////
        purgeHtml: function (container, templatetype) {
            var purged = '';
            //step2
            //case group template move all element to holder
            if (templatetype == 'groupping') {
                var groups = container.find('.sortableForm:not([id="group1"])');
                $.each(groups, function () {
                    var ht = $(this).html();
                    $('#sortableHolder', container).append(ht);
                    $(this).html('');
                });
            }
            $(container).find('.slideItem').each(function () {
                $(this).find('.movingBox').remove();
                $(this).find('.deleteBox').remove();
                $(this).find('[contenteditable="true"]').each(function () {
                    $(this).contents().unwrap();
                });
                $(this)
                    .removeClass(defaults.textClass)
                    .removeClass('ResizableClass')
                    .removeClass(defaults.isSelected)
                    .removeClass(defaults.nonResizableClass)
                    .removeAttr('data-item-type');
                $(this).find(':input[type="text"]').each(function () {
                    $(this).val('');
                });
                $(this).find(':input[type="radio"], :input[type="checkbox"]').removeAttr('checked');
                $(this).find('option').each(function () {
                    $(this).removeAttr('selected');
                });
                $(this).find('.btn').each(function () {
                    $(this).attr('class', 'btn-dark');
                });

            });
            $(container).find('.buttonClass').remove();
            return container.html();
        },

        ///////////////////////////////////
        // purgeHtml of contenteditable before save to db
        // purge html data from empty elements like: <span></span>
        // nodeType
        // Element  1
        // Attr  2
        // Text  3
        ///////////////////////////////////
        recurseDomChildren: function (start, output) {
            var nodes;
            if (start.childNodes) {
                nodes = start.childNodes;
                textEditingHelperfunctions.loopNodeChildren(nodes, output);
            }
        },

        loopNodeChildren: function (nodes, output) {
            var node;
            for (var i = 0; i < nodes.length; i++) {
                node = nodes[i];
                switch (node.tagName) {
                    case 'IMG':
                    case 'AUDIO':
                    case 'VIDEO':
                        var sClass = node.parentNode.getAttribute('class');

                        if (sClass != null)
                            if (sClass.match(/ResizableClass/g)) {
                                node.parentNode.style.position = 'absolute';

                                //node.setAttribute('style', node.parentNode.getAttribute('style') );
                            }
                        continue;
                        break;
                    case 'DIV':
                        if (node.parentNode.getAttribute('class') == 'textClass ResizableClass') {

                            node.parentNode.style.position = 'absolute';
                            //node.setAttribute('style', node.parentNode.getAttribute('style') );
                            //conso
                        }
                }
                if (output) {
                    textEditingHelperfunctions.outputNode(node);
                }
                if (node.childNodes) {
                    textEditingHelperfunctions.recurseDomChildren(node, output);
                }
            }
        },

        outputNode: function (node) {
            var whitespace;
            whitespace = /^\s+$/g;
            if (node.nodeType === 1) {

                for (var x = 0; x < node.attributes.length; x++) {

                }
            } else if (node.nodeType === 3) {
                //clear whitespace text nodes
                node.data = node.data.replace(whitespace, "");
                if (node.data && node.data.length == 1) {
                    //clear unicode text nodes
                    node.data = node.data.replace(/[\u0080-\uFFFF]+/g, "");
                    if (node.parentNode.tagName == 'SPAN' && node.data.length == 0) {
                        node.parentNode.parentNode.removeChild(node.parentNode);
                    }
                }
            }
        }
    };

    slideEditor.init = function(subd){
        defaults.subDomain = subd;

        $( '#loading' ).show();
        initAccordion();
        //scrollToWorkingArea($('.row.special'));
        initScroller();
        initAffix(affixElements);

        for ( var key in preventObject ) {
            $( preventObject[key] ).preventEnter();
        };

        Editor.init();
        slideshowElements.init();
        mymediaElements.init();
        testslidesElements.init();
        /*allslideshowsElements.init();*/

        defaults.editingAreaWidth = Editor.editorAreaElememts.editorArea.offsetWidth;
        defaults.editingAreaHeight = Editor.editorAreaElememts.editorArea.offsetHeight;

        $window.resize(function(){
            defaults.editingAreaWidth = Editor.editorAreaElememts.editorArea.offsetWidth;
            defaults.editingAreaHeight = Editor.editorAreaElememts.editorArea.offsetHeight;
        });

        $('.dropdown-toggle').dropdown();

        $( '#loading' ).hide();

        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.dataType = 'json';
        $.fn.editable.defaults.emptytext = 'Please, fill this';
        $.fn.editable.defaults.url = '';

    };

    this.initScroller = function(){
        var $window = $(window);
        var w = window, d = document, e = d.documentElement, g = d.getElementsByTagName('body')[0];
        var viewportWidth, viewportHeight;
        viewportWidth = w.innerWidth || e.clientWidth || g.clientWidth;
        viewportHeight = w.innerHeight || e.clientHeight || g.clientHeight;

        if (viewportWidth < 1200 ) {
            $( slideshowElements.loadSlideshowContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '140px'});
            $( mainElements.slideshowContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '160px' });
            $( mainElements.mymediaContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '140px' });
            $( mainElements.testsContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '160px' });
            $( allslideshowsElements.loadSlideshowContainer2 ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '140px' });
            $( mainElements.allslideshowContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '160px' });
        } else {
            $( slideshowElements.loadSlideshowContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '190px'});
            $( mainElements.slideshowContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '190px' });
            $( mainElements.mymediaContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '170px' });
            $( mainElements.testsContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '190px' });
            $( allslideshowsElements.loadSlideshowContainer2 ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '190px' });
            $( mainElements.allslideshowContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '190px' });
        }

        $(window).resize(function(){
            viewportWidth=w.innerWidth||e.clientWidth||g.clientWidth;
            viewportHeight=w.innerHeight||e.clientHeight||g.clientHeight;
            if (viewportWidth < 1170 ) {
                $( slideshowElements.loadSlideshowContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '140px'});
                $( mainElements.slideshowContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '160px' });
                $( mainElements.mymediaContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '140px' });
                $( mainElements.testsContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '160px' });
                $( allslideshowsElements.loadSlideshowContainer2 ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '140px' });
                $( mainElements.allslideshowContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '160px' });
            } else {
                $( slideshowElements.loadSlideshowContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '190px'});
                $( mainElements.slideshowContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '190px' });
                $( mainElements.mymediaContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '170px' });
                $( mainElements.testsContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '190px' });
                $( allslideshowsElements.loadSlideshowContainer2 ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '190px' });
                $( mainElements.allslideshowContainer ).slimScroll({ position: 'left', height: '400px', allowPageScroll: false, width: '190px' });
            }
        });
        if(window.outerWidth < 1200) {

        }


    };

    this.initAccordion = function() {
        $('.accordion').on('show', function (e) {
            $(e.target).prev('.accordion-heading').find('.accordion-toggle').addClass('active');
            $(e.target).prev('.accordion-heading').find('.accordion-toggle i').removeClass('icon-plus');
            $(e.target).prev('.accordion-heading').find('.accordion-toggle i').addClass('icon-minus');
        });

        $('.accordion').on('hide', function (e) {
            $(this).find('.accordion-toggle').not($(e.target)).removeClass('active');
            $(this).find('.accordion-toggle i').not($(e.target)).removeClass('icon-minus');
            $(this).find('.accordion-toggle i').not($(e.target)).addClass('icon-plus');
        });
    }

}(window.slideEditor = window.slideEditor || {}, jQuery));