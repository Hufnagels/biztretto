var $slidesShows = $('#slideShowList'),
    $userGroupList = $('#GroupList'),
    $slidesScroller = $('.slidesList'),
    $trainingOrganizer = $('#myTrainingsList'),
    $trainings = $('#trainingList');

//scrollcontainer size    
var scrollContainerHeight = 345;
var scrollContainerInnerHeight = 270;
var slideScoller;
var viewportWidth = w.innerWidth || e.clientWidth || g.clientWidth;
var viewportHeight = w.innerHeight || e.clientHeight || g.clientHeight;

var trainingSaved = false;
//drop slideshow into main container
var dropOnList = {
    tolerance: 'pointer',
    accept: "#slideShowList li:not(.disabled)",
    activeClass: "ui-state-highlight",
    hoverClass: "ui-state-highlight",
    greedy: true,
    drop: function (e, ui) {
        var $selected = $('#draggingContainer3').find('li'),
            $id = $selected.attr('data-id');
//console.log('bedobott idje');
//console.log( $id );
        $(this).append($selected);
        $(this).css('height', ($(this).find('li').length + 1) * 45 + 'px');
        $slidesShows.find('li[id="' + $id + '"]').addClass('disabled').end();
        addFormFunctions();
        $('.droppedusers.main').live('click', function () {
            var tempV = $(this).attr('data-department');
            if (typeof tempV !== 'undefined' && tempV.length > 0) {
                var deps = tempV.split(',');
                $.each(deps, function (i, e) {
                    $userGroupList.find('li[data-object-id="' + deps[i] + '"][data-object-type="department"]').addClass('greenBg');
                });
            }
            if (tempV == 'all')
                $userGroupList.find('li[data-object-name="viewAll"]').addClass('greenBg');
            var tempV = $(this).attr('data-traininggroup');
            if (typeof tempV !== 'undefined' && tempV.length > 0) {
                var tgrp = tempV.split(',');
                $.each(tgrp, function (i, e) {
                    $userGroupList.find('li[data-object-id="' + tgrp[i] + '"][data-object-type="traininggroup"]').addClass('greenBg');
                });
            }
            var bgColor = $('.greenBg > .mbHeader:first').css('backgroundColor');
            $('.greenBg > .mbHeader').animate({backgroundColor: "#888888"}, 1000).animate({backgroundColor: bgColor}, 1000);
        });
        checkSaveStatus();
    }
};

var dragslideshows = {
    appendTo: 'body',
    handle: ".rightSide",
    revert: true,
    opacity: 0.7,
    dropOnEmpty: true,
    helper: function (event, ui) {
        var selected = $(this),
            container,
            cssRule = {'position': '', 'left': '', 'top': '', 'z-index': '10000'};
        if ($('#draggingContainer3').length == 0)
            container = $('<ul/>').appendTo('body').attr('id', 'draggingContainer3');
        else {
            container = $('#draggingContainer3');
            container.empty();
        }
        var data = [];
        data['result'] = [];
        var slideShowName = $(this).attr('data-name');
        data['result'].push({
            'name': $(this).attr('data-name'),
            'description': $(this).attr('data-description'),
            'cover': $(this).attr('data-cover'),
            'id': $(this).attr('id'),
            'folder': $('#daForSlideShow').attr('data-diskarea-id')
        });
        var cl = tmpl("tmpl-trainingslides", data);
        container.addClass('span8').append(cl).css(cssRule);
        $('li', container).css(cssRule);
        return container;
    },
    start: function (event, ui) {
        $('#draggingContainer3').css({'position': 'absolute'});
    }
};

//sortableTrainings
var sortableTrainings = {
    handle: '.span3 > .name',
    items: 'li',
    toleranceElement: '> .trainingdata',
    placeholder: 'placeholder',
    forcePlaceholderSize: true,
    isTree: true,
    maxLevels: 3,
    tabSize: 25,

    update: function (e, ui) {
        $trainingOrganizer.find('li.userElement').each(function (i, e) {
            var len = $(e).parents("li").length;
            $(e).find('.badge.level').text(len + 1);
            $(e).find('.colorBar').css('border-left-width', (len + 1) * 2 + 'px');
        });
        trainingSaved = false;
        checkSaveStatus();
    },
    stop: function (e, ui) {
        if (ui.position.left < -20) {
            var id = ui.item[0].attributes['data-id'].value;
            $trainingOrganizer.find('li[data-id="' + id + '"]').remove();
            trainingSaved = false;
        }
        //recalculateScrollHeight( $trainingOrganizer );
    }
};

//drop users onto 'drop user here'
var dropOnGroup = {
    tolerance: 'pointer',
    accept: "#GroupList li",
    //activeClass: "ui-state-highlight",
    hoverClass: "ui-state-highlight",
    greedy: true,
    drop: function (e, ui) {
        var group = $(this),
            groupUsers = group.find('div.name').text(),
            groupType = $('#draggingContainer2').find('li').attr('data-object-type'),
            groupId = $('#draggingContainer2').find('li').attr('data-object-id');
        trainingSaved = false;
        checkSaveStatus();
        /*var elements = $myusers.find('li.selected');
         var data = [];
         elements.each( function(i,e){
         data.push({'id':$(e).attr('data-id'),'groupid':group.attr('data-object-id'),'groupName':groupName});
         //$(e).find('span.department').text(groupName);
         });
         */
//console.log('drop fired');
//console.log( data );

//sendMessage('alert-success', response.message.success);
        var count = parseInt($('#draggingContainer2').find('.pull-right.badge').text());
        var origCount = group.attr('data-users');
        origCount = origCount == '' ? 0 : parseInt(origCount);
        group.text((origCount + count) + ' user');
        group.attr('data-users', (origCount + count));
        $('#draggingContainer2').animate({
            width: ['toggle', 'swing'],
            height: ['toggle', 'swing'],
            opacity: 'toggle'
        }, 700, 'linear', function () {
            $(this).empty();
        });
        var viewAll = $('#draggingContainer2').find('li').attr('data-object-name');
        var att = (viewAll == 'viewAll' ? 'data-department' : 'data-' + $('#draggingContainer2').find('li').attr('data-object-type')),
            groupData = group.attr(att);
        group.attr(att, (groupData !== '' ? groupData + ',' + groupId : (viewAll == 'viewAll' ? 'all' : groupId)));
        if (group.hasClass('main')) {
            $trainingOrganizer.find('.droppedusers').each(function () {
                $(this).attr(att, (groupData !== '' ? groupData + ',' + groupId : (viewAll == 'viewAll' ? 'all' : groupId)));
                $(this).text((origCount + count) + ' user');
                $(this).attr('data-users', (origCount + count))
            });
        }
    }
};

var emptyGroup = {
    appendTo: 'body',
    handle: "span",
    revert: true,
    opacity: 0.7,
    dropOnEmpty: true,
    helper: 'clone',
    stop: function (event, ui) {
        $('.droppedusers').text('Drop here').attr('data-departmnet', '').attr('data-traininggroup', '');
        $('.droppedusers.main').text('Drop users here').attr('data-departmnet', '').attr('data-traininggroup', '');
        trainingSaved = false;
        checkSaveStatus();
    }
};

var dragUser = {
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
};
/*
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
 */
var popoveroptions = {
    placement: 'right',
    trigger: "click",
    html: true,
    title: function () {
        return $('#trainingName').val() + ' - cover';
    },
    content: function () {
        return '<img src="' + $('#coverImg').attr('src') + '" />';
    },
    selector: document.body
};
(function ($) {

    pub_pageInit = function () {
        /*
         //getHoverScrollHeight(viewportHeight);
         setHoverScrollHeight();
         */
        $window.resize(function () {
            viewportWidth = w.innerWidth || e.clientWidth || g.clientWidth;
            viewportHeight = w.innerHeight || e.clientHeight || g.clientHeight;
            //setHoverScrollHeight();
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
         */
        //$mediaBoxContainer.hoverscroll({width:170});
        //$slides2.hoverscroll();
        //$userGroupList.hoverscroll();
        //slideScroller = $slidesShows.hoverscroll();
        //$trainings.hoverscroll();
        $userGroupList.slimScroll({position: 'right', height: '400px', width: 'auto', allowPageScroll: false});
        $slidesShows.slimScroll({position: 'left', height: '300px', allowPageScroll: false});
        $trainings.slimScroll({position: 'left', height: '300px', width: '190px', allowPageScroll: false});

        pub_handelSlideShow('load', $('#daForSlideShow').attr('data-diskarea-id'));
        //$trainingOrganizer
        //  .nestedSortable(sortableTrainings)
        //  .droppable(dropOnList);

        //$('.fixed-listcontainer').css('height',scrollContainerInnerHeight+'px');
        pub_loadUserGroups();
        pub_loadTrainingGroups();
        loadTrainings('all');
        addMainFunctions();
    };

    addMainFunctions = function () {
        $trainingOrganizer
            .nestedSortable(sortableTrainings)
            .droppable(dropOnList);

        $slidesShows.find('li:not(.disabled)').draggable(dragslideshows);
    }

    addFormFunctions = function () {
        $('.droppedusers.badge').droppable(dropOnGroup).draggable(emptyGroup);
        $userGroupList.find('li.mbcholder:not(.disabled)').draggable(dragUser);
        $('.date').datepicker();
        $('.timepicker').timepicker({showSeconds: false, showMeridian: false});
    }

    checkSaveStatus = function () {
        var cl = trainingSaved == false ? 'btn btn-danger' : 'btn btn-dark';
        $('#saveTarining').attr('class', cl);
        return trainingSaved == false ? false : true;
    }
    uploadCover = function () {
        $('#upload').trigger('click');
        return false;
    };

    loadTrainings = function (loadType, data2) {
        var values = {};
        values['methode'] = loadType;
        values['id'] = data2;
        values['form'] = $('#trainingsform').serializeArray();
        return $.ajax({
            url: '/crawl?/process/publication/handeltraining/',//"/pages/slideeditor/handelSlides.php",
            data: values,
            async: false,
            type: 'POST',
            dataType: 'json'
        }).done(function (data) {
                if (data.type)
                    sendMessage('alert-' + data.type, data.message);
                if (data.type !== 'error') {
                    newTraining(loadType);
                    switch (loadType) {
                        case 'one':
                            console.log(data);
                            var headData = data.main[0];
                            $('input[name="id"]').val(headData.id);
                            $('#coverImg').attr('src', headData.cover);
                            $('input[name="name"]').val(headData.name);
                            $('input[name="authors"]').val(headData.authors);
                            $('#description').val(headData.description);
                            if (data.result) {
                                $trainingOrganizer.append(tmpl("tmpl-loadtraining", data));
                                $trainingOrganizer.find('li').each(function () {
                                    var $id = $(this).attr('data-id');
                                    $slidesShows.find('li[id="' + $id + '"]').addClass('disabled').end();
                                });
                                //$('#trainingStatus').find('a[data-status="'+headData.state+'"]').trigger('click');

                                $('#trainingStatus').find('li').removeClass('selected');
                                $('#trainingStatus li').find('a[data-status="' + headData.activeState + '"]').parent().addClass('selected');
                                $('#trainingStatusSelector').find('span.label').attr('class', 'label label-' + headData.activeState);
                                $('#trainingStatusSelector').find('span.name').text($('#trainingStatus li.selected').find('span.name').text());

                                addFormFunctions();
                                //$('.droppedusers.badge').droppable(dropOnGroup).draggable(emptyGroup);
                                //$userGroupList.find('li.mbcholder:not(.disabled)').draggable(dragUser);
                            }
                            $('#saveTarining').find('span').text('update');
                            break;
                        case 'all':
                            $trainings.html(tmpl("tmpl-miniTrainings", data, true));
                            $trainings.find('.dataHolder').removeClass('selected');
                            break;
                    }
                    $trainings.find('.rightSide > .name').die('click');
                    $trainings.find('.rightSide > .name').live('click', function (e) {
                        e.stopPropagation();
                        var tElement = $(this).closest('li'),
                            tId = tElement.attr('id'),
                            holder = $(this).closest('.dataHolder');
                        $trainings.find('.dataHolder').removeClass('selected');
                        holder.addClass('selected');
                        $('input[name="id"]').val(tId);
                        loadTrainings('one', tId);
                    });
                    trainingSaved = true;
                    checkSaveStatus();
                }
                $('#loading').hide();
            });
    };

    newTraining = function (type) {
        if (type !== 'one') $trainings.find('.dataHolder.selected').removeClass('selected');
        $('#saveTarining').find('span').text('save');
        $('input[name="id"]').val('');
        $('#coverImg')
            .attr('src', 'data:image/gif;base64,R0lGODlhAQABAIAAAP///////yH5BAEKAAEALAAAAAABAAEAAAICTAEAOw==')
            .attr('src', '');
        $('input[name="name"]').val('');
        $('input[name="authors"]').val('');
        $('#description').val('');
        $trainingOrganizer.empty();
        $slidesShows.find('li.disabled').removeClass('disabled');
        $('#trainingStatus').find('li.selected').removeClass('selected');
        $firstLi = $('#trainingStatus').find('li:first');
        $firstLi.addClass('selected');
        $('#trainingStatusSelector').find('span.name').text($firstLi.find('span.name').text());
        $('#trainingStatusSelector').find('span.label').attr('class', $firstLi.find('span.label').attr('class'));
        $('#trainingStatusSelector').parent().removeClass('open');
        $('.droppedusers.main').attr('data-users', '').attr('data-department', '').attr('data-traininggroup', '').text('drop users here');

        addMainFunctions();
        trainingSaved = true;
        checkSaveStatus();
        //$slidesShows.find('li:not(.disabled)').draggable(dragslideshows);
        //$trainingOrganizer
        //  .nestedSortable(sortableTrainings)
        //  .droppable(dropOnList);
    };

    saveTraining = function (data) {
        if ($trainingOrganizer.find('li').length == 0 || $('#trainingName').val() == '') {
            sendMessage('alert-warning', 'Nothing to save');
            trainingSaved = true;
            return false;
        }
        $('#loading').show();
        var values = {};
        console.log(data);
        values['methode'] = data;
        values['cover'] = $('#coverImg').attr('src'),
            //values['title'] = $('#trainingName').val(),
            //values['description'] = $('#description').val(),
            values['activeState'] = $('#trainingStatus li.selected > a').attr('data-status');
        values['form'] = $('#trainingsform').serializeArray();
        values['slideshows'] = [];
        values['slideshowUsers'] = [];
        $trainingOrganizer.find('li').each(function (i, e) {
            var id = $(this).attr('data-id');
            values['slideshows'][i] = $(this).find('form#slideshow_' + id).serialize();
            values['slideshowUsers'].push({
                'id': $(this).attr('data-id'),
                'departments': $(this).find('.droppedusers').attr('data-department'),
                'traininggroups': $(this).find('.droppedusers').attr('data-traininggroup')
            });
        });
        console.log('saveTraining');
        console.log(values);
        var arraied = $trainingOrganizer.nestedSortable('toArray', {nestedType: 'training', startDepthCount: 0, attribute: 'data-id', slideShowId: $('#trainingsform [name="id"]').val()});
        values['toArray'] = arraied;

        arraied = dump(arraied);
        console.log(arraied);

        return $.ajax({
            url: '/crawl?/process/publication/handeltraining/',//"/pages/slideeditor/handelSlides.php",
            data: values,
            async: false,
            type: 'POST',
            dataType: 'json'
        }).done(function (data) {
                sendMessage('alert-' + data.result.type, data.result.message);
                if (data.result.type !== 'error') {
                    if (data.result.id)
                        $('input[name="id"]').val(data.result.id);
                    $('#saveTarining').find('span').text('update');
                    trainingSaved = true;
                    checkSaveStatus();
                }
                $('#loading').hide();
            });
    };

    updateStatus = function (data) {
        var values = {};
        values['methode'] = 'updateStatus', values['form'] = $('#trainingsform').serializeArray(), values['status'] = data;
        return $.ajax({
            url: '/crawl?/process/publication/handeltraining/',
            data: values,
            type: 'POST',
            dataType: 'json',
            async: false
        }).done(function (data) {
                sendMessage('alert-' + data.result.type, data.result.message);
            });
    };

    pub_clearData = function (type) {
//console.log( 'se_clearData fired' );
        switch (type) {
            case 'all':
                $slidesShows.empty();
                $('#bc').html('');
                $('#myNicPanel').empty();
                $('#trainingsform input[name="id"]').val('');
                $('#trainingsform input[name="name"]').val('');
                trainingSaved = true;
                checkSaveStatus();
                break;
        }
    };

    pub_deselectAll = function () {
        //um_removeUI();
        $('#myTrainingsList li').removeClass('selected').find('.icon-ok').addClass('icon-white');
        ($('#selectAll').hasClass('active') ? $('#selectAll').trigger('click') : '');
        //um_findSelected();
    };
    pub_invertSelection = function () {
        //um_removeUI();
        $('#myTrainingsList li').toggleClass('selected').find('.icon-ok').toggleClass('icon-white');
        ($('#selectAll').hasClass('active') ? $('#selectAll').removeClass('active').find('span').text('Select all') : '');
        //um_addUI();
        //um_findSelected();
    };

    pub_handelSlideShow = function (stype, data) {
//console.log( 'se_loadSlideShowList fired' );
        var values = {};
        values[stype] = data, values['form'] = $('#trainingsform').serializeArray();
        return $.ajax({
            url: "/crawl?/process/publication/handelslideshow/",
            data: values,
            type: 'POST',
            dataType: 'json'
        }).done(function (data) {
//console.log( 'pub_handelSlideShow' );
//console.log( data );
                pub_clearData('all');
                //bind click to listelement
                var resHtml = tmpl("tmpl-miniSlides", data, true);
                $slidesShows.append(resHtml);

                addMainFunctions();
                //$slidesShows.find('li:not(.disabled)').draggable(dragslideshows);
            });
    };

//handel user groups
    pub_handelUserGroups = function (stype, data) {
        var values = {};
        values[stype] = stype, values['form'] = $('#trainingsform').serializeArray(), values['id'] = data;
//console.log( 'um_handelUserGroups values' );
//console.log( values );
        return $.parseJSON($.ajax({
            url: "/crawl?/process/publication/handelgroups/",
            data: values,
            type: 'POST',
            dataType: 'json',
            async: false
        }).responseText);
        /*
         done(function(data){
         console.log( 'um_handelUserGroups response' );
         switch (stype) {
         case 'load': $userGroupList.html(tmpl("tmpl-userGroupList", data)); break;
         case 'new': $userGroupList.append(tmpl("tmpl-userGroupList", data)); break;
         case 'rename': return data.responseText; break;
         }
         });
         */
    };
//loadUsersGroup
    pub_loadUserGroups = function () {
        var response = pub_handelUserGroups('load');
//console.log( 'pub_loadUserGroups' );
//console.log( response );
        $userGroupList.html(tmpl("tmpl-userGroupList", response));
        //$userGroupList.find('li.mediaBox').draggable(usergroupDelete);
    };

//handel training groups
    pub_handelTrainingGroups = function (stype) {
        var values = {};
        values[stype] = stype, values['form'] = $('#trainingsform').serializeArray();
//console.log( 'um_handelTrainingGroups values' );
//console.log( values );
        return $.parseJSON($.ajax({
            url: "/crawl?/process/publication/handeltraininggroups/",
            data: values,
            type: 'POST',
            dataType: 'json',
            async: false
        }).responseText);
    };

//loadTrainingGroup
    pub_loadTrainingGroups = function () {
        var response = pub_handelTrainingGroups('load');
//console.log( 'pub_loadTrainingGroups' );
//console.log( response );
        if (!response.type)
            $userGroupList.append(tmpl("tmpl-trainingGroupList", response));
        //$userGroupList.find('li.mediaBox').draggable(usergroupDelete);
    };
})(jQuery);