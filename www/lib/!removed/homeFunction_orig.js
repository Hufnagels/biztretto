//var previewContainer='<div id="trainingFrame" class="hidden fullscreen_hide"><div class="colHeader"><h3 class="orangeT"><span class="btn btn-dark exit" id="exitPreview" data-type="preview">Exit Training</span><span class="btn btn-dark exit" id="gotoFullscreen" data-type="fullscreen">fullscreen</span><span class="slideshowName"></span><input type="hidden" id="auth_token" name="auth_token" value="" /></h3></div><div class="clearfix"></div><div class="iframeHolder"><iframe id="previewIframe" scrolling="no" frameborder="0" src="" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe></div></div>';if($("#trainingFrame").length){$("#peviewIframe").attr("src","")}else{$("body").append(previewContainer)}(function(l,h,c){var i={lastVisited:window.localStorage.getItem("Training:last")||"",form:document.getElementById("myForm")||""},k={myTrainingList:document.getElementById("myTrainingList")||"",trainingFrame:document.getElementById("trainingFrame")||"",exit:document.querySelectorAll(".exit")||"",$iframe:h("#previewIframe")||"",matches:document.querySelectorAll(".card")||"",slideshowName:document.getElementsByClassName("slideshowName")||""},n={container:document.getElementById("myusersGroupsContainer")||"",resultList:document.getElementById("trainingResultList")||""},a={feedback:document.getElementsByName("fastMessage")[0]||"",sendFeedbackButton:document.getElementById("sendFastMessage")||""},j={$sortbyButtons:h("#sortBy > button")||"",$sortby:h("#sortBy")||"",$wordFilterButton:h("#filterWord")||"",$orderButtons:h("#sortOrder > button")||"",$order:h("#sortOrder")||"",$filterButtons:h("#filters > button")||"",order:"asc",init:function(){this.$wordFilterButton.bind("keyup",function(){if(h(this).val().length<2){h(k.myTrainingList).find("li").removeClass("hiddenClass");return false}var o=h(this).val().toLowerCase();h(k.myTrainingList).find("li").filter(function(){return h(this).attr("data-name").toLowerCase().indexOf(o)==-1}).addClass("hiddenClass");j.sort()});this.$filterButtons.bind("click",function(q){q.preventDefault();h(this).hasClass("active")?h(this).removeClass("active"):h(this).addClass("active");h(k.myTrainingList).find("li").removeClass("hiddenClass");var o=h(this).attr("isotope-data-filter").replace(/\./g,"");if(o!=="all"){var p=h(k.myTrainingList).find("li").filter(function(){return o.indexOf(h(this).attr("data-category"))==-1});p.toggleClass("hiddenClass")}j.sort()});this.$sortbyButtons.bind("click",function(o){o.preventDefault();h(j.$sortby).find("button").removeClass("active");h(this).addClass("active");j.sort()});this.$orderButtons.bind("click",function(o){o.preventDefault();j.$order.find("button").removeClass("active");h(this).addClass("active");j.sort()});j.sort()},sort:function(){var q=h("#sortBy").find(".btn.active").attr("data-option-value");var o=h("#sortOrder").find(".btn.active").attr("data-option-value");var p=[];j.$filterButtons.filter(function(){return !h(this).hasClass("active")}).each(function(){p.push("colorBar "+h(this).attr("data-option-value"))});h(k.myTrainingList).find("li > .colorBar").filter(function(){return p.indexOf(this.className)!==-1}).parent().addClass("hiddenClass");h(k.myTrainingList).find("li:not(.hiddenClass, .hidden)").tsort({attr:"data-"+q,order:o});h(k.myTrainingList).find("li.hiddenClass, li.mediaElement.hidden").appendTo(k.myTrainingList)}},f={training:{0:"load"},statistic:{0:"load"}},d={0:n.container};_eventHandlers={},initialised=false,trainingId="",ascOrder=true,rowData=[],values={},settings={url:"",data:values,responseType:"json"};var b={init:function(){a.feedback.value="";addEventO(a.sendFeedbackButton,"click",function(){sendPostMessage("feedback",a.feedback);b.sendFeedback()},true,_eventHandlers)},sendPostMessage:function(q,o){var p=sendEmail(q,o.value,h(i.form).serializeArray());sendMessage("alert-"+p.type,p.message);if(p.type=="success"){o.value=""}event.preventDefault()},sendFeedback:function(){var o=sendEmail("feedback",a.feedback.value,h(i.form).serializeArray());sendMessage("alert-"+o.type,o.message);if(o.type=="success"){a.feedback.value=""}event.preventDefault()}};var m={init:function(){if(!i.lastVisited&&isSupported()){window.localStorage.setItem("Training:last",i.lastVisited=0)}k.$iframe.css({width:viewportWidth+"px",height:(viewportHeight-34)+"px"});this.loadTrainings();this.setActions()},loadTrainings:function(){values={};values.form=h(i.form).serializeArray();settings.url="/crawl?/process/home/trainings/";settings.data=values;var o=getJsonData(settings);if(!o){return false}h(k.myTrainingList).html(tmpl("tmpl-trainingElement",o));h(n.resultList).append(tmpl("tmpl-credits",o.extra));k.openTrainingButtons=h(".trainingElement .btn");rowData=calculateLIsInRow(k.myTrainingList);h(k.$openTrainingButtons).live("click",function(){m.displayDetailRow(this)});k.matches=document.querySelectorAll(".card")},setActions:function(){h(".card").live("click",function(){m.selectItem(this)});for(var o=0;o<k.exit.length;o++){var p=k.exit[o];addEventO(p,"click",m.exitTraining,true,_eventHandlers)}},selectItem:function(o){this.displayDetailRow(o)},displayDetailRow:function(q){var o=h(q).closest("li");var s=parseInt(o.index()/rowData[1])+1;var p=o.position().left-o.parent().position().left+85+"px";createDetailRow(o,s,rowData,p,"300px");values={};values.training_id=o.attr("id");settings.data=values;settings.url="/crawl?/process/home/trainingdetails/";settings.responseType="html";var r=getJsonData(settings);if(!r){return false}h("#detailRow").find(".inner").append(r);h(".openSlideshowButton:not(.disabled)").live("click",function(){m.openTraining(this)})},openTraining:function(r){var p=h(r).attr("data-id"),s=h(r),o=h(r).attr("data-sid"),q=s.attr("isotope-author")+" - "+s.attr("isotope-title");k.trainingFrame.className=" fullscreen_show";h(".row.special").addClass("hiddenClass");viewportWidth=w.innerWidth||e.clientWidth||g.clientWidth;viewportHeight=w.innerHeight||e.clientHeight||g.clientHeight;var t="id"+(new Date()).getTime();k.$iframe.attr("src",SUBDOMAIN+p+"/"+o+"/?tokenId="+t).css({width:viewportWidth+"px",height:viewportHeight+"px"});h("#auth_token").val(t);k.$iframe[0].contentWindow.focus();if(isSupported()){window.localStorage.setItem("Training:last",i.lastVisited=p)}},flipItem:function(o){if(h(o).hasClass("flip")){if(e.target.tagName=="A"){window.open(e.target.href);e.preventDefault()}else{h(o).removeClass("flip")}}else{if(e.target.tagName=="A"){window.open(e.target.href);e.preventDefault()}else{h(o).addClass("flip")}}},exitTraining:function(r){switch(r.target.getAttribute("data-type")){case"preview":h("#trainingFrame").attr("class"," fullscreen_hide hide");h(".row.special").removeClass("hiddenClass");cancelFullscreen(document);k.$iframe.attr("src","");if(isSupported()){trainingId=window.localStorage.getItem("Training:last")}if(!h(k.myTrainingList).find('li[id="'+trainingId+'"]').attr("isotope-status")=="solved"){h(k.myTrainingList).find('li[id="'+trainingId+'"]').find(".colorBar").attr("class","colorBar solved");var o=h(n.resultList).find(".name > .count").text(),p=h(n.resultList).find(".badge1 > .count"),q=h(n.resultList).find(".bar");p.text(parseInt(p.text())+1);q.css("width",(parseInt(p.text())+1)/(parseInt(o))*100+"%")}trainingId="";break;case"screen":case"fullscreen":toggleFullscreen();break}}};l.destroy=function(){l.preinit();delete window.myTraining};l.preinit=function(){h(k.myTrainingList).empty();h(n.resultList).empty();a.feedback.value="";removeAllEventsByEvent("click",_eventHandlers)};l.init=function(){l.preinit();initAffix(d);m.init();$window.resize(function(o){viewportWidth=w.innerWidth||o.clientWidth||g.clientWidth;viewportHeight=w.innerHeight||o.clientHeight||g.clientHeight;if(!h(".row.special").hasClass("hiddenClass")){rowData=calculateLIsInRow(k.myTrainingList)}if(window.screenTop&&window.screenY){k.$iframe.css({width:viewportWidth+"px",height:(viewportHeight-34)+"px"})}else{k.$iframe.css({width:viewportWidth+"px",height:(viewportHeight-34)+"px"})}})}}(window.myTraining=window.myTraining||{},jQuery));


var previewContainer = '<div id="trainingFrame" class="hidden fullscreen_hide"><div class="colHeader"><h3 class="orangeT">' +
    '<span class="btn btn-dark exit" id="exitPreview" data-type="preview">Exit</span>' +
    '<span class="btn btn-dark exit" id="gotoFullscreen" data-type="fullscreen">fullscreen</span>' +
    '<span class="slideshowName"></span>' +
    //'<div class="dropdown hidden" id="attachDrop"><a class="dropdown-toggle mediaBox" data-toggle="dropdown" href="#">Attachments<span class="caret"></span></a><ul class="dropdown-menu sub" role="menu" aria-labelledby="dLabel" id="fileList"></ul></div>'+
    '<input type="hidden" id="auth_token" name="auth_token" value="" />' +
	'<div class="progress badge btn-dark pull-right" style=\"color: white;padding: 10px;line-height: 5px;margin-right: 10px;margin-top: 7px;\"></div>' + 
    '</h3></div><div class="clearfix"></div>' +
    '<div class="iframeHolder"><iframe id="previewIframe" scrolling="no" frameborder="0" src="" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe></div></div>';

// setup trainingframe
if ($('#trainingFrame').length)
    $('#peviewIframe').attr('src', '');
//else
    //$('body').append(previewContainer);
	

(function (myTraining, $, undefined) {

    var mainElements = {
            wrapper : $('#wrapper') || '',
            modalWindow : $("#myDetails" ) || ''
        },

        homeElements = {
            lastVisited: window.localStorage.getItem( "Training:last" ) || '',
            form       : document.getElementById( "myForm" ) || ''
        },

        trainingElements = {
            myTrainingList: document.getElementById( 'myTrainingList' ) || '',
            trainingFrame : document.getElementById( 'trainingFrame' ) || '',
            exit          : document.querySelectorAll( '.exit' ) || '',
            $iframe       : $( "#previewIframe" ) || '',
            matches       : document.querySelectorAll( ".card" ) || '',
            slideshowName : document.getElementsByClassName('slideshowName') || ''

        },

        summaryElements = {
            container : document.getElementById( "myusersGroupsContainer" ) || '',
            resultList: document.getElementById( "trainingResultList" ) || ''
        },

        messageElements = {
            feedback          : document.getElementsByName( "fastMessage" )[0] || '',
            sendFeedbackButton: document.getElementById( 'sendFastMessage' ) || ''
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
                        $( trainingElements.myTrainingList ).find( 'li' ).removeClass( 'hiddenClass' );
                        return false;
                    }
                    var searchText = $( this ).val().toLowerCase();
                    $( trainingElements.myTrainingList ).find( 'li' ).filter(function () {
                        return $( this ).attr( 'data-name' ).toLowerCase().indexOf( searchText ) == -1;
                    } ).addClass( 'hiddenClass' );
                    sortingElements.sort();
                } );

                this.$filterButtons.bind( 'click', function ( e ) {
                    e.preventDefault();
                    $( this ).hasClass( 'active' ) ? $( this ).removeClass( 'active' ) : $( this ).addClass( 'active' );
                    $( trainingElements.myTrainingList ).find( 'li' ).removeClass( 'hiddenClass' )
                    var type = $( this ).attr( 'isotope-data-filter' ).replace( /\./g, '' );
                    if ( type !== 'all' ) {
                        var elements = $( trainingElements.myTrainingList ).find( 'li' ).filter( function () {
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
                $( trainingElements.myTrainingList ).find( 'li > .colorBar' ).filter(function () {
                    return filterArray.indexOf( this.className ) !== -1;
                } ).parent().addClass( 'hiddenClass' );

                $( trainingElements.myTrainingList )
                    .find( 'li:not(.hiddenClass, .hidden)' )
                    .tsort( {attr: 'data-' + att, order: order} );
                $( trainingElements.myTrainingList )
                    .find( 'li.hiddenClass, li.mediaElement.hidden' )
                    .appendTo( trainingElements.myTrainingList );
            }
        },

        actions = {
            training : {0: 'load'},
            statistic: {0: 'load'}
        },

        affixElements = {
            0: summaryElements.container
        },

    //filterByWordInput: document.getElementById('isofilterWord') || '',
    //filterByWordDropDown: document.getElementById('isofilterWordType') || '',
    //rotateCardsButton: document.getElementById('rotateCards') || '',

        _eventHandlers = {},
        initialised = false,
        trainingId = '',
    //currentFilter,
    //sortValue,
        ascOrder = true,
        rowData = [],

        values = {},
        settings = {
            url         : "",
            data        : values,
            responseType: 'json'
        };

    var Summary = {

        init : function(){
            messageElements.feedback.value = '';
            addEventO(messageElements.sendFeedbackButton, "click", function(){
                sendPostMessage('feedback', messageElements.feedback)
                Summary.sendFeedback();
            }, true, _eventHandlers);

        },

        sendPostMessage : function(type, $element) {
            var response = sendEmail(type, $element.value, $(homeElements.form).serializeArray());
            sendMessage('alert-' + response.type, response.message);
            if (response.type == 'success')
                $element.value = '';
            event.preventDefault();
        },

        sendFeedback : function() {
            var response = sendEmail('feedback', messageElements.feedback.value, $(homeElements.form).serializeArray());
            sendMessage('alert-' + response.type, response.message);
            if (response.type == 'success')
                messageElements.feedback.value = '';
            event.preventDefault();
        }

    };

    var Trainings = {

        init : function(){

            if (!homeElements.lastVisited && isSupported())
                window.localStorage.setItem("Training:last", homeElements.lastVisited = 0);

            trainingElements.$iframe.css({'width': viewportWidth + 'px', 'height': (viewportHeight - 34) + 'px'});

            this.loadTrainings();

            this.setActions();
        },

        loadTrainings:function(){

            values = {};
            values['form'] = $(homeElements.form ).serializeArray();
            settings.url = "/crawl?/process/home/trainings/";
            settings.data = values;
            var data = getJsonData(settings);

            if(!data)
                return false;

            $(trainingElements.myTrainingList ).html(tmpl("tmpl-trainingElement", data));
            //$(summaryElements.resultList).append(tmpl("tmpl-credits", data.extra));
            trainingElements['openTrainingButtons'] = $('.trainingElement .btn');
            rowData = calculateLIsInRow(trainingElements.myTrainingList);
            /*
             $(trainingElements.$openTrainingButtons).live('click', function () {
             "use strict";
             Trainings.displayDetailRow(this);
             });
             */
            trainingElements.matches = document.querySelectorAll(".card");
            // training elements flip
            //$('.card .front').append('<span class="icon-arrow-right"></span>');
            //$('.card .back').append('<span class="icon-arrow-left"></span>');
        },

        setActions:function(){
            $('.front').live("click", function () {
                "use strict";
                Trainings.selectItem(this);
            });

            // setup fullscreen/exit training
            for (var i = 0; i < trainingElements.exit.length; i++) {
                var node = trainingElements.exit[i];
                addEventO(node, "click", Trainings.exitTraining, true, _eventHandlers);
            }

        },

        selectItem : function(entry){
            this.displayDetailRow(entry);
            //this.flipItem(entry, e);
        },

        displayDetailRow : function(node){
            var parent = $(node).closest('li');
            var row = parseInt(parent.index() / rowData[1]) + 1;
            var pointerleft = parent.position().left - parent.parent().position().left+85+'px';

            //createDetailRow(parent, row, rowData, pointerleft, '300px');
            TOlog('selected', parent)
            /*
             ezt még át kell nézni
             */
            values = {};
            values['training_id'] = parent.attr('id');
            settings.data = values;
            settings.url ="/crawl?/process/home/trainingdetails/";
            settings.responseType = 'html';
            var data = getJsonData(settings);

            if(!data)
                return false;
            $(mainElements.modalWindow).find('.modal-header h4' ).html(parent.attr('isotope-title'));
            $(mainElements.modalWindow).find('.modal-body').html(data);
            $(mainElements.modalWindow).modal('show');

            //$('#detailRow').find('.inner').append(data);
            $('.openSlideshowButton:not(.disabled)').live('click',function(){
                "use strict";
                $("#myDetails").modal('hide');
                Trainings.openTraining(this);
            });
        },

        openTraining : function(node) {
            var trainingId = $(node).attr('data-id'),
            //viewType = $(node).attr('data-type'),
                current = $(node),
                slideshow = $(node).attr('data-sid' ),
                slideshowName = current.attr('isotope-author') + ' - ' + current.attr('isotope-title');

            //$(trainingElements.slideshowName).html('').html(slideshowName);
            trainingElements.trainingFrame.className = ' fullscreen_show';
            $(mainElements.wrapper).addClass('hiddenClass');
            viewportWidth = w.innerWidth || e.clientWidth || g.clientWidth;
            viewportHeight = w.innerHeight || e.clientHeight || g.clientHeight;
            var uniq = 'id' + (new Date()).getTime();
            //trainingElements.$iframe.attr('src', SUBDOMAIN + '/'+viewType+'/' + trainingId + '/').css({'width': viewportWidth + 'px', 'height': viewportHeight + 'px'});
            trainingElements.$iframe.attr('src', SUBDOMAIN  + trainingId + '/' + slideshow + '/?tokenId=' + uniq).css({'width': viewportWidth + 'px', 'height': viewportHeight + 'px'});
            $('#auth_token').val(uniq);
            //toggleFullscreen();
            //requestFullscreen(document.body);
            trainingElements.$iframe[0].contentWindow.focus();
            if (isSupported())
                window.localStorage.setItem("Training:last", homeElements.lastVisited = trainingId);
        },

        flipItem : function(entry){
            if ($(entry).hasClass('flip')) {
                if (e.target.tagName == "A") {
                    window.open(e.target.href);
                    e.preventDefault();
                } else {
                    $(entry).removeClass('flip');
                }
            } else {
                if (e.target.tagName == "A") {
                    window.open(e.target.href);
                    e.preventDefault();
                } else {
                    $(entry).addClass('flip');
                }
            }
        },

        exitTraining : function(event){

            /**/
            switch (event.target.getAttribute('data-type')) {
                case 'preview':
                    $('#trainingFrame').attr('class', ' fullscreen_hide hide');
                    $(mainElements.wrapper).removeClass('hiddenClass');
                    cancelFullscreen(document);
                    //var iframe = $("#previewIframe");
                    trainingElements.$iframe.attr('src', '');
                    if (isSupported())
                        trainingId = window.localStorage.getItem("Training:last");

                    if (!$(trainingElements.myTrainingList).find('li[id="' + trainingId + '"]').attr('isotope-status') == 'solved') {
                        $(trainingElements.myTrainingList).find('li[id="' + trainingId + '"]').find('.colorBar').attr('class', 'colorBar solved');
                        var sumTr = $(summaryElements.resultList).find('.name > .count').text(),
                            solvedTr = $(summaryElements.resultList).find('.badge1 > .count'),
                            bar = $(summaryElements.resultList).find('.bar');
                        solvedTr.text(parseInt(solvedTr.text()) + 1);
                        bar.css('width', ( parseInt(solvedTr.text()) + 1) / ( parseInt(sumTr) ) * 100 + '%');
                    }
                    trainingId = '';
                    //$(trainingElements.myTrainingList ).html('');
                    //Trainings.loadTrainings();

                    break;
                case 'screen':
                case 'fullscreen':
                    toggleFullscreen();
                    break;
            }
        }

    };

    myTraining.destroy = function () {
        myTraining.preinit();
        delete window.myTraining;
    };

    myTraining.preinit = function () {
        $(trainingElements.myTrainingList).empty();
        $(summaryElements.resultList).empty();
        messageElements.feedback.value = '';
        removeAllEventsByEvent('click', _eventHandlers);
    };

    myTraining.init = function () {

        myTraining.preinit();

        initAffix (affixElements);

        Trainings.init();

        //Summary.init();

        // setup window resize
        $window.resize(function (e) {
            viewportWidth = w.innerWidth || e.clientWidth || g.clientWidth;
            viewportHeight = w.innerHeight || e.clientHeight || g.clientHeight;
            if (!$('.row.special').hasClass('hiddenClass'))
                rowData = calculateLIsInRow(trainingElements.myTrainingList);
            if (window.screenTop && window.screenY) {
                //trainingElements.$iframe.css({'width': '100%', 'height': '100%'});
                trainingElements.$iframe.css({'width': viewportWidth + 'px', 'height': (viewportHeight - 34) + 'px'});

            } else {
                //$('#gotoFullscreen').removeClass('hiddenClass').attr('data-type', 'fullscreen').text('Exit fullscreen');
                trainingElements.$iframe.css({'width': viewportWidth + 'px', 'height': (viewportHeight - 34) + 'px'});
                //$('#gotoFullscreen').attr('data-type','fullscreen').text('Fullscreen');
                //$("#gotoFullscreen" ).attr('data-type',"screen" ).html('Exit fullscreen');
            }

        });

    };

}(window.myTraining = window.myTraining || {}, jQuery));
