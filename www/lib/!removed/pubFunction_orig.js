(function (Publication, $, undefined) {
    var mainElements = {
            form: document.getElementById("trainingsform") || '',
            folderSelect: document.getElementById('themeRow') || '',
            leftContainer: document.getElementById('mySlideshowsContainer') || '',
            rightContainer: document.getElementById('myGroupsContainer') || '',
            iframe: document.getElementById("previewIframe") || '',
            diskAreaId: document.getElementsByName('diskArea_id').value,
            saveAllButton: document.getElementById("saveAll") || ''

        },

        slideshowElements = {
            slideshowList: document.getElementById('slideShowList') || '',
            folderSelector: document.getElementById('daForSlideShow') || ''

        },

        attachmentElements ={
            attachList : document.getElementById('editorsMediaBox') || '',
            mediaList: document.getElementById( "myMediaList" ) || '',
            groupList : document.getElementById('mediaBoxList') || '',
            sortingBar : document.getElementById( "sortingIconBar" ) || ''
        },

        trainingElements = {
            trainingList: document.getElementById('trainingList') || '',
            folderSelector: document.getElementById('daForTrainings') || ''
        },
        trainingStatusElements = {
            trainingStatusSelector: document.getElementById('trainingStatusSelector') || '',
            trainingStatus: document.getElementById('trainingStatus') || ''
        },
        groupElements = {
            groupList: document.getElementById('GroupList') || '',
            row: []
        },
        organizerElements = {
            slideshowList: document.getElementById('myTrainingsList') || '',
            statusChanger: document.getElementById('trainingStatus') || '',
            uploadInput: document.getElementById('upload') || '',
            fileupload: document.getElementById('fileupload') || '',
            uploadCoverButton: document.getElementById('uploadCover') || '',
            clearCoverButton: document.getElementById('clearCover') || '',
            coverImage: document.getElementById('coverImg') || '',
            form: document.getElementById("detailsform") || '',
            detailsElements: {
                cover: document.getElementById('coverImg') || '',
                title: document.getElementById('name') || '',
                authors: document.getElementById('authors') || '',
                description: document.getElementById('description') || '',
				insertCode : document.getElementById('insertCode') || '',
                createNewButton: document.getElementById('createNewTrainng') || '',
                deleteTrainingButton: document.getElementById('deleteTraining') || ''
            },
			insertCodeArray : [
				'<div class="iframeBorderDiv" style="width:870px;height:489px;border:0;overflow:hidden;"><iframe src="',
				'" width="100%" height="100%" style="border:0;" allowfullscreen="" webkitallowfullscreen="" mozallowfullscreen="" oallowfullscreen="" msallowfullscreen=""></iframe></div>'
				],
            slideshowsElements: {
                previewButton: document.getElementById('previewTraining') || '',
                exitPreviewButton: document.getElementById('exitPreview') || '',
                removeButton: document.querySelectorAll('span.removeSlideshow.btn-dark') || '',
                typeChangeButton: document.querySelectorAll('.typeChanger') || '',
                previewSlideshowList : {
                    listholder : document.getElementsByClassName('previewSlideshowList') || '',
                    init : function(){
                        organizerElements.slideshowsElements.previewSlideshowList.createlinks();
                        organizerElements.slideshowsElements.previewSlideshowList.gatherlinks();
                    },
                    destroy : function(){
                        $(organizerElements.slideshowsElements.previewSlideshowList.listholder).html('');
                    },
                    createlinks : function(){
                        organizerElements.slideshowsElements.previewSlideshowList.destroy();
                        $(organizerElements.slideshowList).find('li.userElement').each(function(){
                            var id = $(this).attr('data-id'),
                                title = $(this).find('span.name').text();
                            $(organizerElements.slideshowsElements.previewSlideshowList.listholder).append('<li><a href="#" data-sid="'+id+'">'+title+'</a></li>');
                        })
                    },
                    gatherlinks : function(){
                        $(organizerElements.slideshowsElements.previewSlideshowList.listholder).find('li > a').each(function(){
                            addEventO(this, 'click', function (e) {
                                e.preventDefault();
                                var slideshowId = $(this).attr('data-sid');
                                Trainings.previewTraining(Trainings.selectedId, slideshowId);
                            }, true, _eventHandlers);

                        })
                    }
                },

                init: function () {
                    organizerElements.slideshowsElements.removeButton = document.querySelectorAll('span.removeSlideshow.btn-dark');
                    Trainings.deleteSlideshows(organizerElements.slideshowsElements.removeButton);
                    organizerElements.slideshowsElements.typeChangeButton = document.querySelectorAll('.typeChanger');
                    Trainings.changeSlideshowType(organizerElements.slideshowsElements.typeChangeButton);
                }
            },

            instanceElements: {
                instanceList: document.getElementById('myInstances') || '',
                newInstanceButton: document.getElementById('addNewInstance') || '',
                removeButton: document.querySelectorAll('span.removeInstance.btn-dark') || '',
                droppedusers: document.querySelectorAll('span.droppedusers.badge') || '',
                init: function () {
                    organizerElements.instanceElements.removeButton = document.querySelectorAll('span.removeInstance.btn-dark');
                    Instances.deleteInstance(organizerElements.instanceElements.removeButton);
                    organizerElements.instanceElements.droppedusers = document.querySelectorAll('span.droppedusers.badge');

                }
            }

        },
        authorsString = [],
        iframeUrl = '',
		publicUrl = '',
        instanceElements = {},
        isNewTraining = 0,
        editor = null,
        editorInstance = null,
        values = {},
        settings = {
            url: "",
            data: values,
            responseType: 'json'
        },
        actions = {
            slideshows: {0: 'load', 1: 'delete'},
            trainings: {0: 'load', 1: 'delete', 2: 'update', 3: 'new', 4: 'list', 5: 'toArray', 6: 'updateStatus', 7: 'deletemaster'},
            groups: {0: 'load'},
            instances: {0: 'load', 1: 'delete', 2: 'new', 3: 'changeData'},
            mediafiles : {0:'load', 1:'loadgroup', 2: 'updatefiles'}
        },
        affixElements = {
            0: mainElements.leftContainer,
            1: mainElements.rightContainer
        };

    var emptyImage = 'data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==';
    var maxW = 160;
    var maxH = 224;

    var SlideShows = {

        init: function () {
            //this.viewSlideshows();
            Trainings.addDND();
        },

        viewSlideshows: function () {
            values = {};
            values['action'] = actions.slideshows[0];//'load',
            values['form'] = $(mainElements.form).serializeArray();
            values['folderId'] = $(slideshowElements.folderSelector).attr('data-diskarea-id');
            settings.url = "/crawl?/process/publication/handelslideshow/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data.result)
                return false;

            $(slideshowElements.slideshowList).html(tmpl("tmpl-miniSlides", data));

            Trainings.addDND();
        },

        setSelected: function (entry) {

        }

    };

    var Attachments = {

        init : function(){
            this.filterFiles();
            this.selectFiles();
        },

        selectFiles : function(){
            $(attachmentElements.groupList ).find('li:not(.selected) a.level2').live('click', function(e){

                var _this = $(this);
                e.preventDefault();
                e.stopPropagation();

                _this.parent().siblings().removeClass('selected');
                _this.parent().addClass('selected');
                var folderName = $(this).find('span.name').text();
                folderName = folderName.length > 9 ? folderName.substring(0, 8)+'...' : folderName;
                $(attachmentElements.groupList).prev().find('span.name').text(folderName).attr('title',$(this).find('span.name').text());

                Attachments.viewFiles(_this.attr('data-id'));
                $('.selectMenu').parent().removeClass('open');

            });
        },

        removeFiles : function(entry){

            $("#confirmDiv").confirmModal({
                heading: 'Alert',
                body: 'Delete selected attachment?',
                text:'Delete',
                cancel: true,
                type: 'question',
                callback: function () {
                    entry.remove();
                }
            });

        },

        saveFiles :function(){
            var data = [];
            $(attachmentElements.mediaList).find('li.mediaElement').each(function(i,e){
                data.push( $(e).attr('data-id') );
            });
            return data;
        },

        viewFiles: function (id) {

            values = {};
            values['action'] = id == 'all' ? actions.mediafiles[0] :  actions.mediafiles[1];
            values['groupid'] = id;//actions.mediafiles[0];
            values['form'] = $( mainElements.form ).serializeArray();
            values['diskArea'] = mainElements.diskAreaId;

            settings.url = "/crawl?/process/publication/loadmediafiles/";
            settings.data = values;
            var data = getJsonData( settings );
            if ( !data )
                return false;

            $( attachmentElements.attachList ).html( tmpl( "tmpl-mediaElement", data ) );

            this.addDND();

        },

        filterFiles : function(){
            $(attachmentElements.sortingBar ).find('button' ).bind('click', function(){
                if($(attachmentElements.attachList).find('li').length == 0) return false;
                var classes = $(this).attr('data-class');
                $(attachmentElements.attachList).find('li' ).hide();
                var classArray = classes.split(/\s+/g);
                for (i in classArray)
                    classes == '' ? $(attachmentElements.attachList).find('li' ).show() : $(attachmentElements.attachList).find('li.'+classArray[i] ).show();
            })
        },

        setAction : function() {
            $(attachmentElements.mediaList ).find('.deleteButton').live('click', function () {
                var entry = $(this).closest('li.mediaElement');
                Attachments.removeFiles(entry);
            });
        },

        addDND : function(){
            $(attachmentElements.attachList ).find('li.mediaElement' ).draggable(this.myMediaDragOption);
            //$(attachmentElements.mediaList).droppable(Attachments.attachDropOption);
            $(attachmentElements.mediaList).sortable(Attachments.sortableAttach);
        },

        myMediaDragOption : {
            appendTo:'body',
            handle: 'img',
            revert: 'true',
            helper: 'clone',
            start: function( e,ui){
                $(attachmentElements.mediaList).droppable(Attachments.attachDropOption);
            },
            stop:function(e,ui){
                $(attachmentElements.mediaList).droppable('destroy').sortable(Attachments.sortableAttach);
                Attachments.setAction();
            }
        },

        attachDropOption : {
            tolerance: 'touch',
            activeClass: "ui-state-highlight",
            hoverClass: "ui-state-active",
            greedy: true,
            drop: function(e, ui){
                var element = ui.draggable.clone();
                $(this).append( element );
                $(attachmentElements.mediaList).sortable(Attachments.sortableAttach);
            }
        },

        sortableAttach: {
            receive: function ( event, ui ) {
                //update slidshows attachment data
            },
            stop   : function ( e, ui ) {
                /*
                 if ( ui.position.left < -70 ) {
                 //console.log(ui.position.left);
                 $( ui.item ).remove();
                 var data = [];
                 $(attachmentElements.mediaList).find( 'li' ).each( function ( i, e ) {
                 data.push( $( e ).attr( 'data-id' ) );
                 } );
                 }
                 */
            }
        }

    };

    var Trainings = {
        selectedId: '',

        isSlideshowSaved: false,

        init: function () {

            organizerElements.slideshowsElements.previewSlideshowList.destroy();

            this.setTrainingActions();
            addEventO(organizerElements.uploadInput, 'change', function (event) {
                resizeAndUpload(event, organizerElements.coverImage);
            }, false, _eventHandlers);

            addEventO(organizerElements.uploadCoverButton, 'click', function () {
                $(organizerElements.uploadInput).trigger('click');
                return false;
            }, true, _eventHandlers);

            addEventO(organizerElements.clearCoverButton, 'click', function () {
                organizerElements.coverImage.src = emptyImage;
                $(organizerElements.form).find('input[name="cover"]').val('');
            }, true, _eventHandlers);

            addEventO(organizerElements.detailsElements.createNewButton, 'click', function (e) {
                e.preventDefault();
                Trainings.newTraining();
            }, true, _eventHandlers);

            addEventO(organizerElements.detailsElements.deleteTrainingButton, 'click', function (e) {
                e.preventDefault();
                Trainings.deleteTraining(Trainings.selectedId);
            }, true, _eventHandlers);

            addEventO(mainElements.saveAllButton, 'click', function (e) {
                e.preventDefault();
                Trainings.saveTraining(Trainings.selectedId);
            }, true, _eventHandlers);

            /*
             addEventO(organizerElements.slideshowsElements.previewButton, 'click', function (e) {
             e.preventDefault();
             Trainings.previewTraining(Trainings.selectedId);
             }, true, _eventHandlers);
             */


            addEventO(organizerElements.slideshowsElements.exitPreviewButton, 'click', function (e) {
                e.preventDefault();
                Trainings.closePreview();
            }, true, _eventHandlers);

            $(trainingStatusElements.trainingStatus).find('a').bind('click', function (e) {
                e.stopPropagation();
                if (Trainings.selectedId == '')
                    return false;
                Trainings.setStatus(Trainings.selectedId, $(this));
            });

            addEventO(organizerElements.instanceElements.newInstanceButton, 'click', function (e) {
                e.preventDefault();
                if (Trainings.selectedId == '')
                    return false;
                Instances.newInstance();
            }, true, _eventHandlers);

        },

        upload: function () {
            resizeAndUpload(organizerElements.upload, organizerElements.cover);
            $(organizerElements.form).find('input[name="cover"]').val(organizerElements.detailsElements.cover.src);
        },

        setStatus: function (entry, statusElement) {
            if ($(organizerElements.slideshowList).find('li.userElement').length == 0) {
                sendMessage('alert-error', 'No slideshows in training.');
                return false;
            }
            if(statusElement.attr('data-status') == 'ready')
                $("#confirmDiv").confirmModal({
                    heading: 'Remember',
                    body: 'After setting training status \'go Public\', you can\'t edit slideshow in slideeditor until you set status back to \'Draft\'!',
                    text: 'OK',
                    type: 'question',
                    //cancel: true,
                    callback: function () {
                        return false;
                    }
                });
            values = {};
            values['action'] = actions.trainings[6];
            values['form'] = $(mainElements.form).serializeArray();
            values['status'] = statusElement.attr('data-status');
            settings.url = "/crawl?/process/publication/handeltraining/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data)
                return false;
            statusElement.parent().siblings('li').removeClass('selected').end();
            statusElement.parent().addClass('selected');
            $(trainingStatusElements.trainingStatusSelector).find('span.name').text(statusElement.find('span.name').text());
            $(trainingStatusElements.trainingStatusSelector).find('span.label').attr('class', statusElement.find('span.label').attr('class'));
            $(trainingStatusElements.trainingStatusSelector).parent().removeClass('open');
            var selected = $(trainingElements.trainingList).find('.dataHolder.selected');
            selected.find('.colorTr').attr('class', 'colorTr ' + statusElement.attr('data-status'));
        },

        viewTrainings: function () {
            values = {};
            values['action'] = actions.trainings[4];//'load',
            values['form'] = $(mainElements.form).serializeArray();
            values['folderId'] = $(trainingElements.folderSelector).attr('data-diskarea-id');
            settings.url = "/crawl?/process/publication/handeltraining/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data.result)
                return false;
            $(trainingElements.trainingList).html(tmpl("tmpl-miniTrainings", data));
            this.setTrainingActions();
        },

        setTrainingActions : function() {
            this.gatherTrainings();
            Trainings.addDND();
        },

        loadTraining: function (entry) {
            values = {};
            values['action'] = actions.trainings[0];
            values['form'] = $(mainElements.form).serializeArray();
            values['trainingId'] = entry.id;
            settings.url = "/crawl?/process/publication/handeltraining/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data.main) {
                return false;
            }
            var headData = data.main[0];
            this.selectedId = headData.id;
            $(mainElements.form).find('input[name="id"]').val(headData.id);
            organizerElements.detailsElements.cover.src = headData.cover;
            $(organizerElements.form).find('input[name="cover"]').val(headData.cover);
            organizerElements.detailsElements.title.value = headData.name;
            editorInstance.setValue(headData.description, true);
			//organizerElements.insertCode.value = organizerElements.insertCodeArray[0] + publicUrl + headData.id + '/'+slideshowId+'/';
			
            if (data.result) {
                organizerElements.slideshowList.innerHTML = '';
                $(organizerElements.slideshowList).append(tmpl("tmpl-loadtraining", data));
                $(slideshowElements.slideshowList).find('li').removeClass('disabled');
                $(organizerElements.slideshowList).find('li').each(function () {
                    var $id = $(this).attr('data-id');
                    $(slideshowElements.slideshowList).find('li[id="' + $id + '"]').addClass('disabled').end();
                });
				/**
				 * inserted 2013.11.07
				 * firt slideshow id and training id for public preview
				 */
				var firstSlideshowId = $(organizerElements.slideshowList).find('li:eq(0)').attr('data-id');
				var tokenID = new Date().getTime();
				organizerElements.detailsElements.insertCode.value = organizerElements.insertCodeArray[0] + publicUrl + headData.id + '/'+firstSlideshowId+'/?tokenId='+tokenID+organizerElements.insertCodeArray[1];
				
                $(trainingStatusElements.trainingStatus).find('li').removeClass('selected');
                $(trainingStatusElements.trainingStatus).find('a[data-status="' + headData.activeState + '"]').parent().addClass('selected');
                $(trainingStatusElements.trainingStatusSelector).find('span.label').attr('class', 'label label-' + headData.activeState);
                $(trainingStatusElements.trainingStatusSelector).find('span.name').text($('#trainingStatus li.selected').find('span.name').text());
                $(trainingElements.trainingList).find('li > .dataHolder').removeClass('selected').end();
                $(trainingElements.trainingList).find('li[id="' + headData.id + '"] > .dataHolder').addClass('selected');

                if(data.attach){
                    //console.log('data.attach')

                    var files = [];
                    files['result'] = data.attach;
                    //console.log(files)
                    $(attachmentElements.mediaList).html(tmpl("tmpl-mediaElement", files));
                    Attachments.addDND();

                    Attachments.setAction();
                } else {
                    $( attachmentElements.mediaList ).html('');
                }


                Trainings.addDND();

                organizerElements.slideshowsElements.init();

                organizerElements.slideshowsElements.previewSlideshowList.init();

                Instances.init();

                Trainings.isSlideshowSaved = !!($(organizerElements.slideshowList).find('li.userElement').length > 0);
            }
        },

        saveTraining: function (entry) {
            if (entry == '' && $(organizerElements.detailsElements.title).val().replace(/\s/g, "").length == 0) {
                sendMessage('alert-warning', 'Please give a title!')
                return false;
            }
			
				/**
				 * inserted 2013.11.07
				 * firt slideshow id and training id for public preview
				 */
				var tId = $(mainElements.form).find('input[name="id"]').val();
				if($(organizerElements.slideshowList).find('li:eq(0)').length && tId){
					//console.log( $(organizerElements.slideshowList).find('li:eq(0)') )
					var firstSlideshowId = $(organizerElements.slideshowList).find('li:eq(0)').attr('data-id');
					var tokenID = new Date().getTime();
					organizerElements.detailsElements.insertCode.value = organizerElements.insertCodeArray[0] + publicUrl + tId + '/'+firstSlideshowId+'/?tokenId='+tokenID+organizerElements.insertCodeArray[1];
				} else
					organizerElements.detailsElements.insertCode.value = '';
			
				//return false;
			
            values = {};
            values['action'] = actions.trainings[2];//'update', new if id is empty
            values['form'] = $(mainElements.form).serializeArray();
            //training details
            values['details'] = $(organizerElements.form).serializeArray();
            //slideshows
            var arraied = $(organizerElements.slideshowList).nestedSortable(
                'toArray', {
                    nestedType: 'training',
                    startDepthCount: 0,
                    attribute: 'data-id',
                    slideShowId: $('#trainingsform [name="id"]').val()
                }
            );
            var testArray = [];
            $(organizerElements.slideshowList).find('li.userElement').each(function () {
                var tempArray = [];
                var id = $(this).attr('data-id');
                var $check = $(this).find('.typeChanger:first');
                var $form = $check.closest('form');
                //if($check.val() == 1){
                tempArray = $form.serializeArray();
                tempArray.push({'name': 'id', 'value': id});
                testArray.push(tempArray);
                //}
            });
            values['toArray'] = arraied;
            values['test'] = toObject(testArray);
            //attachments
            values['attachment'] = Attachments.saveFiles();

            settings.url = "/crawl?/process/publication/handeltraining/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data.result[0].id)
                return false;
            this.selectedId = data.result[0].id;
            switch (data.result[0].status){
                case 'new':
                    $(trainingElements.trainingList).prepend(tmpl("tmpl-miniTrainings", data));
                    $(trainingElements.trainingList).find('.dataHolder').removeClass('selected').end();
                    $(trainingElements.trainingList).find('li[id="' + Trainings.selectedId + '"] > .dataHolder').addClass('selected');
                    $(mainElements.form).find('input[name="id"]').val(Trainings.selectedId);
                    break;
                case 'update':
                    var html = tmpl("tmpl-miniTrainings", data);
                    var changeData = $(html).find('.dataHolder').html();
                    $(trainingElements.trainingList).find('li[id="' + Trainings.selectedId + '"] > .dataHolder').html(changeData);
                    break;
            }
        },

        deleteTraining: function (entry) {
            if (entry == '')
                return false;
            var bodytext = 'Do you want to delete master training and all instances?';
            $("#confirmDiv").confirmModal({
                heading: 'Question',
                body: bodytext,
                text: 'Delete training',
                type: 'question',
                cancel: true,
                callback: function () {
                    values = {};
                    values['action'] = actions.trainings[7];
                    values['form'] = $(mainElements.form).serializeArray();
                    settings.url = "/crawl?/process/publication/handeltraining/";
                    settings.data = values;
                    var data = getJsonData(settings);
                    if (!data)
                        return false;
                    Trainings.newTraining();
                    $(trainingElements.trainingList).find('li[id="' + entry + '"]').remove();
                    $(slideshowElements.slideshowList).find('li').removeClass('disabled');
                    organizerElements.coverImage.src = emptyImage;
                    $(organizerElements.form).find('input[name="cover"]').val('');
                    organizerElements.slideshowsElements.previewSlideshowList.destroy();
                    return false;
                }
            });
        },

        newTraining: function () {
            $(mainElements.form).find('input[name="id"]').val('');
            $(trainingStatusElements.trainingStatus).find('li').removeClass('selected');
            $(trainingStatusElements.trainingStatus).find('a[data-status="draft"]').parent().addClass('selected');
            $(trainingStatusElements.trainingStatusSelector).find('span.label').attr('class', 'label label-draft');
            $(trainingStatusElements.trainingStatusSelector).find('span.name').text($('#trainingStatus li.selected').find('span.name').text());
            $(trainingElements.trainingList).find('li > .dataHolder').removeClass('selected').end();
            $(organizerElements.form).find(':input').each(function () {
                switch (this.type) {
                    case 'select-one':
                    case 'text':
                    case 'textarea':
                        $(this).val('');
                        break;
                    case 'checkbox':
                    case 'radio':
                        this.checked = false;
                }
            });
            $(organizerElements.detailsElements.description).data("wysihtml5").editor.clear();
			$(organizerElements.detailsElements.insertCode).val('');
            $(slideshowElements.slideshowList).find('li').removeClass('disabled');
            $(organizerElements.slideshowList).empty();
            $(organizerElements.instanceElements.instanceList).empty();
            this.selectedId = '';
            this.isSlideshowSaved = false;
            organizerElements.coverImage.src = emptyImage;
            $(organizerElements.form).find('input[name="cover"]').val('');
            organizerElements.slideshowsElements.previewSlideshowList.destroy();
            $(attachmentElements.mediaList).empty();
            //destroy
            this.addDND();
        },

        saveSlideshows: function (entry) {
            if (entry == '')
                return false;
            values = {};
            var arraied = $(organizerElements.slideshowList).nestedSortable(
                'toArray', {
                    nestedType: 'training',
                    startDepthCount: 0,
                    attribute: 'data-id',
                    slideShowId: $('#trainingsform [name="id"]').val()
                }
            );
            var testArray = [];
            $(organizerElements.slideshowList).find('li.userElement').each(function () {
                var tempArray = [];
                var id = $(this).attr('data-id');
                var $check = $(this).find('.typeChanger:first');
                var $form = $check.closest('form');
                //if($check.val() == 1){
                tempArray = $form.serializeArray();
                tempArray.push({'name': 'id', 'value': id});
                testArray.push(tempArray);
                //}
            });
            values['toArray'] = arraied;
            values['action'] = actions.trainings[5];
            values['form'] = $(mainElements.form).serializeArray();
            values['test'] = toObject(testArray);
            settings.url = "/crawl?/process/publication/handeltraining/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data)
                return false;
            Trainings.isSlideshowSaved = true;
            organizerElements.slideshowsElements.previewSlideshowList.init();
        },

        deleteSlideshows: function (removeButtons) {
            $(removeButtons).each(function (i, e) {
                addEventO(e, 'click', function () {
                    var $parent = $(e).closest('li.userElement');
                    var parentId = $parent.attr('data-id')
                    var children = $parent.find('li.userElement');
                    var childrenCount = children.length;
                    var slideshowHtml = $(organizerElements.slideshowList).html();
                    var bodytext = '';
                    bodytext = childrenCount > 0 ? childrenCount + ' more slideshow is connected to the selected' : 'Do you really want to delete this slideshow?';
                    $("#confirmDiv").confirmModal({
                        heading: 'Question',
                        body: bodytext,
                        text: 'Delete slidehow' + (childrenCount > 0 ? '\'s' : ''),
                        type: 'question',
                        cancel: true,
                        callback: function () {
                            $parent.remove();
                            var arraied = $(organizerElements.slideshowList).nestedSortable(
                                'toArray', {
                                    nestedType: 'training',
                                    startDepthCount: 0,
                                    attribute: 'data-id',
                                    slideShowId: $('#trainingsform [name="id"]').val()
                                }
                            );
                            values = {};
                            values['toArray'] = arraied;
                            values['action'] = actions.trainings[1];
                            values['form'] = $(mainElements.form).serializeArray();
                            values['sid'] = parentId;
                            settings.url = "/crawl?/process/publication/handeltraining/";
                            settings.data = values;
                            var data = getJsonData(settings);

                            if (!data){
                                $(organizerElements.slideshowList).html(slideshowHtml);
                                return false;
                            }

                            slideshowHtml = '';
                            $(slideshowElements.slideshowList).find('li[id="' + parentId + '"]').removeClass('disabled');
                            //init remove buttons
                            organizerElements.slideshowsElements.init();
                            organizerElements.slideshowsElements.previewSlideshowList.init();
                            Trainings.isSlideshowSaved = !!($(organizerElements.slideshowList).find('li.userElement').length > 0);
                            return false;
                        }
                    });
                }, true, _eventHandlers)
            });
        },

        changeSlideshowType: function (typeChangeButton) {
            $(typeChangeButton).each(function (i, e) {
                addEventO(e, 'click', function () {
                    var $parent = $(e).closest('li.userElement');
                    var parentId = $parent.attr('data-id')
                    var $form = $(e).closest('form');
                    var $hiddenDiv = $form.find('.optionRow');
                    if ($(e).is(':checked')) {
                        $hiddenDiv.removeClass('hidden');
                        $(e).val('1').attr('checked', true);
                        $("#confirmDiv").confirmModal({
                            heading: 'Remember',
                            body: 'In TEST version stepping back between slides are ignored! Only straight forward is allowed!',
                            text: 'OK',
                            type: 'question',
                            //cancel: true,
                            callback: function () {
                                return false;
                            }
                        });
                    } else {
                        $hiddenDiv.addClass('hidden');
                        $(e).val('0').attr('checked', false);
                    }

                }, true, _eventHandlers)
            });
        },

        gatherTrainings: function () {
            $(trainingElements.trainingList).find('li.publicationElement').live('click', function (e) {
                e.preventDefault();
                Trainings.loadTraining(this);
            });
        },

        addDND: function () {
            $(organizerElements.slideshowList).nestedSortable(Trainings.sortableTrainings).droppable(Trainings.dropOnList);//.nestedSortable(Trainings.sortableTrainings)
            $('li.slideshowslide').draggable(Trainings.dragSlideshows)//.droppable(dropOnList);
        },

        previewTraining: function (selectedId, slideshowId) {
            if (this.selectedId == '') {
                sendMessage('alert-warning', 'No training selected!');
                return false;
            }
            if (Trainings.isSlideshowSaved) {
                mainElements.iframe.src = iframeUrl + this.selectedId + '/'+slideshowId+'/';// +diskAreaId+'/'
                $(mainElements.iframe)[0].contentWindow.focus();
            } else {
                sendMessage('alert-warning', 'Save modifications before preview!');
                return false;
            }
            $('div.row.special').slideUp('fast');
            $('div.preview').removeClass('hidden');
            $("html, body").animate({ scrollTop: $(document).height() }, 1000);
        },

        closePreview: function () {
            var type = $(this).attr('data-type');
            $('div.row.special').slideDown('slow');
            $('div.preview').addClass('hidden');
            mainElements.iframe.src = '';
        },

        sortableTrainings: {
            handle: '.span3 > .name',
            items: 'li',
            toleranceElement: '> .trainingdata',
            placeholder: 'placeholder',
            forcePlaceholderSize: true,
            isTree: true,
            maxLevels: 3,
            tabSize: 25,
            update: function (e, ui) {
                $(organizerElements.slideshowList).find('li.userElement').each(function (i, e) {
                    var len = $(e).parents("li").length;
                    $(e).find('.badge.level').text(len + 1);
                    $(e).find('.colorBar').css('border-left-width', (len + 1) * 2 + 'px');
                });
                Trainings.isSlideshowSaved = false;
            },
            stop: function (e, ui) {
                if (ui.position.left < -2000) {
                    var id = ui.item[0].attributes['data-id'].value;
                    $(organizerElements.slideshowList).find('li[data-id="' + id + '"]').remove();
                }
                Trainings.isSlideshowSaved = false;
            }
        },

        dropOnList: {
            tolerance: 'pointer',
            accept: "#slideShowList li:not(.disabled)",
            activeClass: "ui-state-highlight",
            hoverClass: "ui-state-highlight",
            greedy: true,
            drop: function (e, ui) {
                var $selected = $('#draggingContainer3').find('li'),
                    $id = $selected.attr('data-id');
                $(this).append($selected);
                $selected.css('z-index', '1');
                $(this).css('height', ($(this).find('li').length + 1) * 45 + 'px');
                $(slideshowElements.slideshowList).find('li[id="' + $id + '"]').addClass('disabled').end();
                organizerElements.slideshowsElements.init();
                Trainings.isSlideshowSaved = false;
            }
        },

        dragSlideshows: {
            appendTo: 'body',
            handle: ".rightSide",
            revert: true,
            opacity: 0.7,
            dropOnEmpty: true,
            helper: function (event, ui) {
                var selected = $(event.target).closest('li.slideElement'),
                    container,
                    cssRule = {'position': '', 'left': '', 'top': '', 'z-index': '10000'};
                if ($('#draggingContainer3').length == 0)
                    container = $('<ul/>').appendTo('body').attr('id', 'draggingContainer3');
                else {
                    container = $('#draggingContainer3');
                    container.empty();
                }
                var data = {};
                data['result'] = [];
                data['result'].push({
                    'name': selected.attr('data-name'),
                    'id': selected.attr('id'),
                    'type': 0,
                    'repetable': 0,
                    'credit': '',
                    'testtype': 1//,
                    //'folder': $('#daForSlideShow').attr('data-diskarea-id')
                });
                var cl = tmpl("tmpl-trainingslides", data);
                container.addClass('span8').append(cl).css(cssRule);
                $('li', container).css(cssRule);
                return container;
            },
            start: function (event, ui) {
                $('#draggingContainer3').css({'position': 'absolute'});
            }
        }
    };

    var Instances = {

        init: function () {
            this.viewInstances();
        },

        viewInstances: function () {
            values = {};
            values['action'] = actions.instances[0],
                values['form'] = $(mainElements.form).serializeArray();
            settings.url = "/crawl?/process/publication/handelinstances/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data.result)
                return false;
            $(organizerElements.instanceElements.instanceList).html(tmpl("tmpl-traininginstances", data));
            organizerElements.instanceElements.init();
            this.addDND();
            addFormFunctions();
        },

        deleteInstance: function (removeButtons) {
            if (removeButtons == '')
                return false;
            $(removeButtons).each(function (i, e) {
                addEventO(e, 'click', function () {
                    var $parent = $(e).closest('li.userElement');
                    var parentId = $parent.attr('data-id')
                    var bodytext = 'Do you want to delete this instance?';

                    $("#confirmDiv").confirmModal({
                        heading: 'Question',
                        body: bodytext,
                        text: 'Delete instance',
                        type: 'question',
                        cancel: true,
                        callback: function () {
                            values = {};
                            values['action'] = actions.instances[1];
                            values['form'] = $(mainElements.form).serializeArray();
                            values['id'] = parentId;
                            settings.url = "/crawl?/process/publication/handelinstances/";
                            settings.data = values;
                            var data = getJsonData(settings);
                            if (!data)
                                return false;

                            $parent.remove();
                            organizerElements.instanceElements.init();
                            return false;
                        }
                    });
                }, true, _eventHandlers)
            });
        },

        newInstance: function () {
            values = {};
            values['action'] = actions.instances[2];
            values['form'] = $(mainElements.form).serializeArray();
            settings.url = "/crawl?/process/publication/handelinstances/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data.result)
                return false;
            $(organizerElements.instanceElements.instanceList).append(tmpl("tmpl-traininginstances", data));
            organizerElements.instanceElements.init();
            this.addDND();
            addFormFunctions();
        },

        dropBox: function () {
            $('.droppedusers').live('click', function (e) {
                e.stopPropagation();
                e.preventDefault();
                var tempV = $(this).attr('data-traininggroup');
                if (typeof tempV !== 'undefined' && tempV.length > 0) {
                    var tgrp = tempV.split(',');
                    $.each(tgrp, function (i, e) {
                        $(groupElements.groupList).find('li[data-object-id="' + tgrp[i] + '"]').addClass('greenBg');
                    });
                    setTimeout(function(){
                        $(groupElements.groupList).find('li').removeClass('greenBg');
                    },2500)

                }
            });
        },

        addDND: function () {
            $(organizerElements.instanceElements.droppedusers).droppable(this.dropOnGroup).draggable(this.emptyGroup);
            this.dropBox();
        },

        dropOnGroup: {
            tolerance: 'pointer',
            accept: "#GroupList li:not(.disabled)",
            hoverClass: "ui-state-highlight",
            greedy: true,
            drop: function (e, ui) {
                var groupId = $('#draggingContainer2').find('li').attr('data-object-id');
                var origCount = $(this).attr('data-groupcount');
                var origGroups = $(this).attr('data-traininggroup');
                var origText = $(this).text();
                if (origGroups.length > 0 && origGroups.indexOf(groupId) !== -1) {
                    sendMessage('alert-warning', 'This group is already added to this instance');
                    return false;
                }
                var myArray = origGroups.length > 0 ? origGroups.split(',') : [];
                myArray.push(groupId);
                var groupCount = myArray.length;
                $(this).attr('data-groupcount', myArray.length);
                $(this).text(myArray.length + ' group')
                $(this).attr('data-traininggroup', myArray.join(','));

                $('#draggingContainer2').animate({
                    width: ['toggle', 'swing'],
                    height: ['toggle', 'swing'],
                    opacity: 'toggle'
                }, 700, 'linear', function () {
                    $(this).empty();
                });

                values = {};
                values['action'] = actions.instances[3];
                values['form'] = $(mainElements.form).serializeArray();
                values['pk'] = $(this).closest('li.userElement').attr('data-id');
                values['name'] = 'traininggroups';
                values['value'] = $(this).attr('data-traininggroup');
                settings.url = "/crawl?/process/publication/handelinstances/";
                settings.data = values;
                //console.log(values)
                var data = getJsonData(settings);
                if (!data) {
                    $(this).attr('data-groupcount', origCount);
                    $(this).text(origText)
                    $(this).attr('data-traininggroup', origGroups);
                }
                return false;
            }
        },

        emptyGroup: {
            appendTo: 'body',
            handle: "span",
            revert: true,
            opacity: 0.7,
            dropOnEmpty: true,
            helper: 'clone',
            stop: function (event, ui) {
                var group = $(this);
                var bodytext = 'Reset training group list?';
                $("#confirmDiv").confirmModal({
                    heading: 'Question',
                    body: bodytext,
                    text: 'Reset training groups',
                    type: 'question',
                    cancel: true,
                    callback: function () {
                        var origCount = group.attr('data-groupcount');
                        var origGroups = group.attr('data-traininggroup');
                        var origText = group.text();
                        group.text('Drop group here').attr('data-traininggroup', '').attr('data-groupcount', '');
                        //return false;
                        values = {};
                        values['action'] = actions.instances[3];
                        values['form'] = $(mainElements.form).serializeArray();
                        values['pk'] = group.closest('li.userElement').attr('data-id');
                        values['name'] = 'traininggroups';
                        values['value'] = '';
                        settings.url = "/crawl?/process/publication/handelinstances/";
                        settings.data = values;
                        var data = getJsonData(settings);
                        if (!data) {
                            group.attr('data-groupcount', origCount);
                            group.text(origText)
                            group.attr('data-traininggroup', origGroups);
                            return false;
                        }
                        organizerElements.instanceElements.init();
                        return false;
                    }
                });
            }
        }


    };

    var TrainingGroup = {

        init: function () {
            this.gatherGroups();
        },

        viewGroups: function () {
            values = {};
            values['action'] = actions.groups[0],//'load',
                values['form'] = $(mainElements.form).serializeArray();
            settings.url = "/crawl?/process/publication/handeltraininggroups/";
            settings.data = values;
            var data = getJsonData(settings);
            if (!data.result)
                return false;
            $(groupElements.groupList).html(tmpl("tmpl-trainingGroupList", data));
            this.gatherGroups();
        },

        gatherGroups: function () {
            groupElements.row = [];
            $(groupElements.groupList).find('li.mbcholder').each(function () {
                groupElements.row.push({'data-object-id': $(this).attr('data-object-id'), 'data-object-name': $(this).attr('data-object-name')});
            });
            $(groupElements.groupList).find('li.mbcholder').live('click', function () {
                TrainingGroup.addDND();
            });
            this.addDND();
        },

        addDND: function () {
            $(groupElements.groupList).find('li.mediaBox').draggable(TrainingGroup.dragUser);
        },

        dragUser: {
            appendTo: 'body',
            handle: ".name",
            revert: true,
            //greedy: true,
            opacity: 0.7,
            helper: function (event, ui) {
                var selected = $(this),
                    container,
                    cssRule = {'position': '', 'left': '', 'top': '', 'z-index': '10000'};
                if ($('#draggingContainer2').length == 0)
                    container = $('<ul/>').appendTo('body').attr('id', 'draggingContainer2');
                else {
                    container = $('#draggingContainer2');
                    container.empty();
                }
                container.addClass('span2').append(selected.clone().addClass('selected')).css(cssRule);
                $('li', container).css(cssRule);
                return container;
            },
            start: function (event, ui) {
                $('#draggingContainer2').css({'position': 'absolute'});
            }
        }
    };


    Publication.init = function (iframeurl, publicurl, id) {

        $( '#loading' ).show();
        $.fn.editable.defaults.mode = 'inline';
        $.fn.editable.defaults.dataType = 'json';
        $.fn.editable.defaults.emptytext = 'Please, fill this';
        $.fn.editable.defaults.url = '/crawl?/process/publication/handeltraining/';
        $.fn.editable.defaults.params = function (params) {
            params.action = 'changeData';
            return params;
        };

        iframeUrl = iframeurl;
		publicUrl = publicurl;
        mainElements.diskAreaId = id;
        /*
         authorsString = authorstring;
         attachSpecialEditable(authorsString);
         attachEditable();
         */

        editor = $(organizerElements.detailsElements.description).wysihtml5({
            "html": false, //Button which allows you to edit the generated HTML. Default false
            "link": false, //Button to insert a link. Default true
            "image": false, //Button to insert an image. Default true,
            "color": false //Button to change color of font
        });

        editorInstance = editor.data('wysihtml5').editor;

        $(slideshowElements.slideshowList).slimScroll({position: 'left', height: '300px', allowPageScroll: false, width: '190px'});
        $(trainingElements.trainingList).slimScroll({position: 'left', height: '300px', allowPageScroll: false, width: '190px'});
        $(groupElements.groupList).slimScroll({position: 'right', height: '300px', allowPageScroll: false, width: '170px'});
        $(attachmentElements.attachList).slimScroll({position: 'left', height: '300px', allowPageScroll: false, width: '170px'});

        initAffix(affixElements);

        initAccordion();

        Trainings.init();
        SlideShows.init();
        TrainingGroup.init();
        Attachments.init();

        //scrollToWorkingArea($('.row.special'));
        $( '#loading' ).hide();
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
		$('.accordion-toggle:not(.active)').find('i').addClass('icon-plus');
		$('.accordion-toggle.active').find('i').addClass('icon-minus')
    };

    function addFormFunctions() {
        $('.date').datepicker().on('changeDate', function (ev) {
            values = {};
            values['action'] = actions.instances[3];
            values['form'] = $(mainElements.form).serializeArray();
            values['name'] = $(this).find('input').attr('name');
            values['value'] = $(this).find('input').val();
            values['pk'] = $(this).closest('li.userElement').attr('data-id');
            settings.url = "/crawl?/process/publication/handelinstances/";
            settings.data = values;
            var data = getJsonData(settings);
            $(this).datepicker('hide');
        });

        $('.timepicker').timepicker({showSeconds: false, showMeridian: false}).on('hide.timepicker', function (e) {
            values = {};
            values['action'] = actions.instances[3];
            values['form'] = $(mainElements.form).serializeArray();
            values['name'] = $(e.target).attr('name');
            values['value'] = e.time.value;
            values['pk'] = $(e.target).closest('li.userElement').attr('data-id');
            settings.url = "/crawl?/process/publication/handelinstances/";
            settings.data = values;
            var data = getJsonData(settings);
        });
    }

    ////////////////////////////////////
    // upload and resize cover image
    ////////////////////////////////////
    function resizeAndUpload(evt, obj) {
        var file = evt.target.files[0];
        var reader = new FileReader();
        reader.onloadend = function () {

            var tempImg = new Image();
            tempImg.src = reader.result;
            tempImg.onload = function () {
                var dataURL = resizeCrop( this, maxW, maxH, -2 ).toDataURL('image/jpg', 90);
                $(obj).attr('src', dataURL);
                //canvas = null;
                $(organizerElements.form).find('input[name="cover"]').val($(obj).attr('src'));
            }

        }
        reader.readAsDataURL(file);
    }

}(window.Publication = window.Publication || {}, jQuery));