<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');

$protocol = connectionType();
$imageURL = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/';
$downloadURL = $protocol . 'media.' . DOMAINTAG . '/_zip/';

$sql = "SELECT diskArea_id FROM media_diskarea WHERE office_nametag = '" . $_SESSION['office_nametag'] . "' AND office_id = " . $_SESSION['office_id'] . " AND name = 'default'";
$result = MySQL::query($sql, FALSE, FALSE);
$diskArea_id = $result[0]['diskArea_id'];

$span2Header = 'mediabox';
$newButtonName = 'new box';
$newMBFormInputPlaceholder = ' Mediabox...';
$newDAFormInputPlaceholder = ' Theme...';
$viewAll = 'View all';
$trashButton = 'trash';
$span2Headerright = 'option';
$span10Header = 'my media';
$span10Header2 = 'add media';

$quotaJsonArray = json_encode(Office::helperCalculateDiskUsage($_SESSION['office_id']));

?>
<!--<script type="text/javascript" charset="utf-8" src="/pages/mediabox/infinite.min.js"></script>-->
<noscript>
    <link rel="stylesheet" href="/assets/fileupload/css/jquery.fileupload-ui-noscript.css">
</noscript>
<script type="text/javascript" charset="utf-8" src="/assets/editable/editable.js"></script>
<script type="text/javascript" charset="utf-8" src="/lib/mbFunction.js"></script>
<script type="text/javascript" charset="utf-8"> var glbQuota = <?=$quotaJsonArray?>; </script>
<div class="row special">
<!-- LEFT SIDE -->
<div class="span2">

    <div class="affix-top" id="myMediaBoxesContainer"><!-- myMediaBoxesContainer -->
        <div class=" colHeader"><h3 class="orangeT "><?=$span2Header;?></h3></div>
        <div class="clearfix"></div>
        <!-- createNew -->
        <div class="createNew well well-small">

            <div class="dropdown">
                <h3 class="functionHeader middlegreyT2">
                    <span>create:</span>
                    <button class="dropdown-toggle btn btn-dark btn-r" data-toggle="dropdown" role="button" data-href="#" id="newMB"><?=$newButtonName;?></button>
                    <ul class="dropdown-menu newMediaBox">
                        <li class="newForm">
                            <form class="form-vertical" method="post" action="#">
                                <div class="input-append">
                                    <input type="text" id="newMBName" class="input-newMB" placeholder="<?= $newMBFormInputPlaceholder; ?>" maxlength="15">
                                    <span class="add-on"><button type="button" class="btn btn-dark" id="createNew">
                                            <i class="icon-plus"></i></button></span>
                                </div>

                            </form>
                        </li>
                    </ul>
                </h3>
            </div>

        </div>
        <!-- /createNew -->
        <!-- mediagroups -->
        <div class="sideBar span2">
            <ul class="mediaBoxList span2" id="mediaBoxList" data-object-type="mediaBoxList">
                <!-- viewAll -->
                <li class="viewAll mbcholder span2 selected" data-object-id="viewAll">
                    <div class="mbHeader">
                        <div class="name">view all</div>
                        <div class="pointer-right"></div>
                    </div>
                </li>
                <!-- /viewAll -->

            </ul>
        </div>
        <!-- /mediagroups -->

        <div class="clearfix"></div>

        <div class="colHeader">
            <h3 class="orangeT"><?=$span2Headerright;?></h3>
        </div>
        <div class="clearfix"></div>
        <div class="Options well well-small">
              <span class="functionHeader middlegreyT2">
                <span id="selectedCount" class="selectedCount pull-left">0</span><span class="pull-left">&nbsp; selected files</span>
                <div class="clear"></div>
                <div class="optionButtons">

                    <button type="button" class="btn btn-dark btn-l disabled" data-option-value="delete">
                        <span class="delete">Delete</span></button>
                    <button type="button" class="btn btn-dark btn-l disabled" data-option-value="removefrom">
                        <span class="delete">Remove from</span></button>
                </div>
              </span>
        </div>
    </div>
    <!-- /myMediaBoxesContainer -->


    <!-- trash --><!--
      <div id="trashContainer" class="">
        <h3 class="functionHeader darkgreyB">
          <button class="btn btn-dark emptyTrash" role="button" id="emptyTrash">trash</button>
          <i class="icon-refresh"></i> 
        </h3>
        <ul id="trash" class="sortable"></ul>
      </div>-->
    <!-- /trash -->
</div>
<!-- /LEFT SIDE -->

<!-- MIDDLE SIDE -->
<div class="span10">
    <div id="myMediaFiles">
        <div class=" colHeader">
            <h3 class="orangeT ">
                <span class="pull-left">My media </span>
          <span class="pull-left">
            <span class="divider">&nbsp;></span>
            <span class="dropdown">
              <span class="dropdown-toggle " role="button" data-toggle="dropdown" id="diskAreaButton" data-sortname="default">Default<span class="caret"></span></span>
              <ul class="dropdown-menu selectediskArea">
                  <li class="divider"></li>
                  <li class="newForm">
                      <form class="form-vertical" method="post" action="#">
                          <div class="control-group">
                              <div class="input-append">
                                  <input type="text" id="newDAName" class="input-newMB" placeholder="<?= $newDAFormInputPlaceholder; ?>">
                                  <span class="add-on"><button type="button" class="btn btn-dark" id="createNewDA">
                                          <i class="icon-plus"></i></button></span>
                              </div>
                          </div>
                          <div class="control-group hidden">
                              <span class="help-block"></span>
                          </div>
                      </form>
                  </li>
              </ul>
              
            </span>
          </span>
                <span class="pull-left" id="bc"></span>
                <span class="pull-right functionHeader" id="addFiles">Add media</span>
            </h3>
        </div>
        <div class="clearfix"></div>
        <!-- uploading content -->
        <div id="content" class="closed transitions middlegreyB">
            <div class="inner">
                <!-- UPLOAD FORM -->
                <form id="fileupload" action="#" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="diskArea_id" value="<?= $diskArea_id; ?>"/>
                    <input type="hidden" name="diskArea_name" value="default"/>
                    <input type="hidden" name="office_id" value="<?= $_SESSION['office_id'] ?>"/>
                    <input type="hidden" name="office_nametag" value="<?= $_SESSION['office_nametag'] ?>"/>
                    <input type="hidden" name="owner" value="<?= $_SESSION['u_id'] ?>"/>
                    <!-- Redirect browsers with JavaScript disabled to the origin page -->
                    <!--<noscript><input type="hidden" name="redirect" value="http://blueimp.github.com/jQuery-File-Upload/"></noscript>
                     The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
                    <div class="fileupload-buttonbar">
                        <div class="1span2 pull-left">
                            <button type="button" class="btn btn-l btn-dark" id="addVideoLink">add video link</button>
                        </div>
                        <div class="1span4 pull-right">
                            <!-- The fileinput-button span is used to style the file input field as button -->
                            <!---->


                            <span class="btn btn-l btn-dark fileinput-button"><span>Select files</span><input type="file" name="files[]" multiple></span>
                            <button type="submit" class="btn btn-l btn-dark start"><span>Upload all</span></button>
                            <button type="reset" class="btn btn-l btn-dark cancel"><span>Cancel all</span></button>
                            <button type="button" class="btn btn-l btn-dark delete"><span>Delete all</span></button>
                            <input type="checkbox" class="toggle">
                        </div>

                        <!-- The global progress information -->
                        <div class="span4 fileupload-progress fade">
                            <!-- The global progress bar -->
                            <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                                <div class="bar" style="width:0%;"></div>
                            </div>
                            <!-- The extended global progress information -->
                            <div class="progress-extended">&nbsp;</div>
                        </div>

                    </div>
                    <!-- The loading indicator is shown during file processing -->
                    <div class="fileupload-loading"></div>
                    <div class="clearfix"></div>

                    <ul class="files1 thumbnails"></ul>

                    <div class="clearfix"></div>
                    <div class="pull-right">
                        <!--<button type="button" class="btn btn-dark" id="uploadSave"><span>Save</span></button>-->
                        <button type="button" class="btn btn-dark" id="uploadFinish"><span>Finish</span></button>
                    </div>
                </form>
                <!-- /UPLOAD FORM -->
            </div>
        </div>
        <!-- uploading content -->

        <!-- ISOTOPE -->

        <div class="well well-small" id="sortingBar">
            <!-- filters -->
            <div class="pull-left">
                <h3 class="functionHeader middlegreyT2">show:
                    <div id="filters" class="btn-group" data-toggle="buttons-checkbox" isotope-data-option-key="filters">
                        <button type="button" class="btn btn-dark active" isotope-data-filter="all"><span>all</span>
                        </button>
                        <button type="button" class="btn btn-dark" isotope-data-filter=".image, .image_jpeg">
                            <span>image</span></button>
                        <button type="button" class="btn btn-dark" isotope-data-filter=".video"><span>video</span>
                        </button>
                        <button type="button" class="btn btn-dark" isotope-data-filter=".audio"><span>audio</span>
                        </button>

                        <button type="button" class="btn btn-dark" isotope-data-filter=".pdf, .word, .excel, .powerpoint, .application_vnd.ms-excel, .application_msword, .application_pdf">
                            <span>attachment</span></button>
                        <button type="button" class="btn btn-dark" isotope-data-filter=".selectedElement">
                            <span>selected</span></button>
                    </div>
                </h3>
            </div>
            <!-- sortBy -->
            <div class="pull-left">
                <h3 class="functionHeader middlegreyT2">sort:
                    <div id="sortBy" class="btn-group" data-toggle="button-radio" isotope-data-option-key="sortBy">
                        <button type="button" class="btn btn-dark active" isotope-data-option-value="original-order">
                            <span>name</span></button>
                        <!--<button type="button" class="btn btn-dark " isotope-data-option-value="name"><span>name</span></button>-->
                        <button type="button" class="btn btn-dark " isotope-data-option-value="number"><span>date</span>
                        </button>
                    </div>
                    <div id="sortOrder" class="btn-group" data-toggle="" isotope-data-option-key="sortAscending">
                        <button type="button" class="btn btn-dark active" isotope-data-option-value="true">
                            <span><i class="icon-arrow-up"></i></span></button>
                        <button type="button" class="btn btn-dark " isotope-data-option-value="false">
                            <span><i class="icon-arrow-down"></i></span></button>
                    </div>
                </h3>
            </div>
            <?
            if (!$_SESSION['isTablet'] && !$_SESSION['isMobile'])
            {
                ?>
                <!-- select all -->
                <div class="pull-right">
                    <h3 class="">
                        <div class="btn-group dropdown">
                            <span type="button" class="btn btn-dark" id="selectAll"><span>Select all</span></span>
                            <span class="btn btn-dark dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></span>
                            <ul class="dropdown-menu" id="selectionMenu">
                                <li class="">
                                    <a href="#" onclick="collectDownload('<?= $downloadURL; ?>')">Download selected</a>
                                <li>
                                <li class="">
                                    <a href="#" onclick="$('#myMediaList li').toggleClass('selectedElement');($('#selectAll').hasClass('active') ? $('#selectAll').removeClass('active').find('span').text('Select all'):'');">Invert selection</a>
                                <li>
                                <li class="">
                                    <a href="#" onclick="$('#myMediaList li').removeClass('selectedElement');($('#selectAll').hasClass('active') ? $('#selectAll').trigger('click'):'');">Deselect all</a>
                                <li>
                            </ul>
                        </div>
                    </h3>
                </div>
                <!-- /select all -->
            <?
            }
            ?>
        </div>
        <!-- /ISOTOPE -->

        <!-- myMediaListContainer -->
        <div class="myMediaListContainer">
            <ul class="thumbnails" id="myMediaList" data-object-type="myMediaList" style="height:100%"></ul>
        </div>
        <!-- myMediaListContainer -->

    </div>
    <!-- /myMediaFiles -->
</div>
<!-- /MIDDLE SIDE -->
</div>
<ul id="allElementContainer"></ul>

<!-- FILE UPLOAD -->

<script type="text/javascript">
    $(function () {
        'use strict';
        // Initialize the jQuery File Upload widget:
        $('#fileupload').fileupload({
            url: '/crawl?/process/upload/',
            filesContainer: $('.files1')

        });
        $('#fileupload').fileupload('option', {
            maxNumberOfFiles: 1,
            url: '/crawl?/process/upload/',
            maxFileSize: 1200000000,
            // The maximum width of the preview images:
            previewMaxWidth: 150,
            // The maximum height of the preview images:
            previewMaxHeight: 100,
            acceptFileTypes: /(\.|\/)(gif|jpe?g|png|pdf|xls?|doc?|swf|ogg|flv|mp4|wmv|mp3|ppt?)$/i,
            process: [
                {
                    action: 'load',
                    fileTypes: /^image\/(gif|jpeg|png)$/,
                    maxFileSize: 20000000 // 20MB
                },
                {
                    action: 'resize',
                    maxWidth: 1920,
                    maxHeight: 1200,
                    minWidth: 800,
                    minHeight: 600
                },
                { action: 'save' }
            ]
        }).bind('fileuploadadd',function (e, data) {
                //$(this).fileupload('option', 'maxNumberOfFiles')

            }).bind('fileuploadchange',function (e, data) {
                $.each(data.files, function (index, file) {
                    var matches = file.type.match(/(word|excel|powerpoint|pdf)/gi);
                    data.files[index].typeShort = matches;
                });
                calculateFreeSpace(data.files, 1338);
            }).bind('fileuploadsubmit',function (e, data) {
                $('#loading').show();
                $('#loading').find('.loadingMessage').text('Upload & conversation in progress...')
            }).bind('fileuploaddone',function (e, data) {
                $('#loading').hide();
                $('#loading').find('.loadingMessage').text('Loading in progress...');
            }).bind('fileuploadstop',function (e) {
                $('#loading').hide();
                $('#loading').find('.loadingMessage').text('Loading in progress...');
            }).bind('fileuploadfail', function (e, data) {
                $('#loading').hide();
                $('#loading').find('.loadingMessage').text('Loading in progress...');
            });

        $('.files1').slimScroll({
            height: '237px',
            allowPageScroll: false
        });

    });
</script>

<script type="text/javascript">
$(function () {
    MyMedia.init();
//teszting

    $('#loading').show();
//mm_initPageLoad();
    $.when(mm_loadMediaGroups(), mm_loadMediaFiles())
        .done(function (resp1, resp2) {
            //resp2[1] == 'success' ? setmyMediaArray(resp2[0]) : sendMessage('alert-error', 'mymedia hiba');
            //resp1[1] == 'success' ? setmyMediaArray(resp1[0]) : sendMessage('alert-error', 'mymedia hiba');
////console.log('loading phase');
////console.log( resp1[0] );
            setTimeout(function () {

                initMyMedia();

                initIso();

                //popover
                $('[rel="popover"]').clickover(popoveroptions);

            }, 100);
        });

    //getHoverScrollHeight(viewportHeight);

    //window resize
    $(window).smartresize(function () {
        viewportWidth = w.innerWidth || e.clientWidth || g.clientWidth;
        viewportHeight = w.innerHeight || e.clientHeight || g.clientHeight;
        if (viewportWidth < 980) {

        }
        //getHoverScrollHeight(viewportHeight);
        //$('.fixed-listcontainer').css('height',scrollContainerInnerHeight+'px');
        //$('.hoverscroll').css('height',scrollContainerHeight+'px');
//teszt
        $('#xxx').html('width:' + viewportWidth + ' Height:' + viewportHeight);
//teszt
        var $elem = $('#myMediaList:data(isotope)');
        if ($mymedia != null && $elem.length)
            $mymedia.isotope('reLayout');
    });
//teszt
    //$('#xxx').html('width:'+viewportWidth+' Height:'+viewportHeight);
//teszt
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
    //$medaiBoxScroller.hoverscroll();
    $medaiBoxScroller.slimScroll({
        position: 'left',
        height: '400px',
        allowPageScroll: false,
        width: '190px'
    });

    //affix for #myMediaBoxesContainer
    $('#myMediaBoxesContainer').affix({
        offset: {
            top: function () {
                return $window.width() <= 1200 ? 150 : 180
            },
            bottom: -100
        }
    });


    // create new mediabox
    $('#newMBName, #newDAName').bind('keypress', function (e) {
//console.log( 'key pressed' );
        var code = (e.keyCode ? e.keyCode : e.which);
        if (code == 13) return false;
    });

    $('#createNew').bind('click', function () {
        var newName = $('#newMBName').val();
        if (newName.length == 0) {
            var indexes = [],
                elems = $mediaBoxList.find('li.mediaBox'),//'li[data-object-type="mediaBox"]');
                regexp = /mediabox/g;
            for (var i = 0; i < elems.length; i++) {
                var str = $(elems[i]).attr('data-object-name');
                if (/mediabox/g.test(str))
                    indexes.push(str.replace(/mediabox/g, ''));
            }
            var newIndex = indexes.length == 0 ? 1 : Math.max.apply(Math, indexes) + 1;
            newName = 'Mediabox ' + newIndex;
        }
        var boxid = handelMediaGroup(newName, $('#fileupload').serializeArray(), 'save');
////console.log( boxid );
        var data = [];
        data['result'] = [];
        data['result'].push({
            'id': boxid.resultId,
            'name': newName,
            'doname': newName.toLowerCase().replace(' ', '')
        });
        var resHtml = tmpl("tmpl-newMediaBox", data, true);
        $mediaBoxList.append(resHtml);
        $mediaBoxList.find('li.mediaBox').tsort({attr: 'data-object-name'});
        $('.mediaBox').droppable(mediaBoxDropOption);
        $('.sub li').draggable(mediaBoxElementDelete);
        $('.mediaBoxList .mediaBox').draggable(mediaBoxDelete);
        $('#newMBName').val('');
    });

    //select mediaboxes
    $('.mbHeader > div').die('click');
    $('.mbHeader > div').live('click', function () {
        //$('.mbHeader').removeClass('open');
        if ($mymedia.length == 0) return false;
        var mediaboxGroup = $(this).parent().parent();
        $(this).parent().removeClass('open');
        var mgName = $(this).text();
        if (mediaboxGroup.hasClass('selected')) return false;
        destroyIso();
        $('#myMediaList li').draggable('destroy');
        $(this).next('i').removeClass(openedClass).addClass(closedClass);
        $(this).parent().next('.sub').css('display', 'none');
        $('#myMediaBoxesContainer .mbcholder').removeClass('selected');
        mediaboxGroup.addClass('selected');
        mediaBoxSelect(mediaboxGroup, mgName);

        //set selected mediabox name editable
        var mediaboxGroup = $mediaBoxList.find('.selected');
        var mgId = mediaboxGroup.attr('data-object-id');
        var obj = mediaboxGroup.find('div.name');

        $('#mbName_' + mgId).editable({
            title: 'Rename selected mediabox',
            placement: 'bottom',
            send: 'never'
        });
        $('#mbName_' + mgId).on('update', function (e, editable) {
////console.log('editable id: '+ mgId );
            var mgData = [];
            mgData.push({'name': editable.value, 'id': mgId});
            var response = handelMediaGroup(mgData, $('#fileupload').serializeArray(), 'rename');
            if (!response.newname) {
                sendMessage('alert-error', response.error);
                return false;
            }
            mediaboxGroup.attr('data-object-name', response.sortname);
            obj.text(response.newname);
            sendMessage('alert-success', 'Succesfully renamed');
        });
        MyMedia.countSelected();
    });

    /*
     $('.mbHeader > div').die('dblclick');
     $('.mbHeader > div').live('dblclick', function(){
     var mediaboxGroup = $(this).parent().parent();
     var mgId = mediaboxGroup.attr('data-object-id');
     var obj = $(this);
     var mgName = obj.text();
     var data = [];
     data['result'] = [];
     data['result'].push({'oldname':mgName});
     var bodytext = tmpl("tmpl-renameMBox", data, false);
     $("#confirmDiv").confirmModal({
     heading: 'Rename selected mediabox',
     body: bodytext,
     text:'Rename',
     callback: function () {
     var mgData = [];
     mgData.push({'name':$('#newMGName').val(),'id':mgId});

     var response = handelMediaGroup(mgData, $('#fileupload').serializeArray(), 'rename');
     if(!response.newname){
     sendMessage('alert-error', response.error);
     return false;
     }
     mediaboxGroup.attr('data-object-name',response.sortname);
     obj.text(response.newname);
     ////console.log( video.provider + ' ' + video.id +' videores: ' + videoResult.title);
     }
     });
     });


     //open/close mediaboxes
     $('.mbHeader > i.opener').die('click');
     $('.mbHeader > i.opener').live('click', function(){
     var mediaboxGroup = $(this).parent().parent();
     if (mediaboxGroup.hasClass('selected')) return false;
     $('.mbHeader').removeClass('open');
     var thisClass = $(this).attr('class') == closedClass ? openedClass : closedClass;
     if (thisClass == openedClass) $(this).parent().addClass('open');
     $('.mbcholder .sub').css('display','none');
     $('.mbcholder .mbHeader > i.opener').attr('class',closedClass);
     $(this).attr('class', thisClass);
     thisClass == closedClass ? $(this).parent().next().css('display','none') : $(this).parent().next().css('display','block');
     });
     */
    /*
     // open trash on mousemove/touchstart
     var trashTimeout;
     $trashContainer.bind('mouseenter mouseover touchstart', function(){
     $(this).css({'height':'auto'});//.parent()
     //$('li','#trash').css({'display':'none'});
     ////console.log('enter trash');
     clearTimeout(trashTimeout);
     }).bind('mouseleave mouseout touchend', function(){
     if ($(this).children('h3').hasClass('open') == false) {
     ////console.log('leave trash');
     trashTimeout = setTimeout(function(){
     ////console.log('count down');
     $trashContainer.css({'height':'40px'});//.parent()
     //$('#trash li').css({'display':'block'});
     }, 4000);
     }
     });
     */
    /*
     //empty trash
     $('#emptyTrash').bind('click', function(){

     var trashElements = $trash.find('li');
     if (trashElements.length == 0) return false;
     var mediagroups = $mediaBoxList.find('li.mediaBox');
     var bodytext,
     trashE = [],
     trashEId = [],
     mediaG = [],
     mediaGE = [],
     findedElements = [];
     findedElements['result'] = []
     //collect trash elements data
     $.each(trashElements, function(i,e){
     trashE.push({
     'name': $(e).attr('isotope-data-name'),
     'id':$(e).attr('data-id'),
     'type':$(e).attr('isotope-data-category') ,
     'mediatype':$(e).attr('data-mediatype')
     });
     trashEId.push(  parseInt($(e).attr('data-id')) );
     });

     //loop through mediagroups and find match
     $.each(mediagroups, function(i,e){
     var mediaGEl = $(e).find('li.mediaElement');//.attr('data-id')
     $.each(mediaGEl, function(i,e){
     mediaG.push( parseInt($(e).attr('data-id')) );
     mediaGE.push({
     'name': $(e).parent().parent().find('div.name').text(),
     'id': parseInt($(e).attr('data-id'))
     });
     });
     });
     //if match record it to array
     //console.log( trashE.length );
     for(var i=0;i< trashE.length;i++){
     findedElements['result'].push({
     'mediaelement':trashE[i].name,
     'id': trashE[i].id,
     'textid':trashE[i].name.toLowerCase().latinize().substring(0,4),
     'mediatype':trashE[i].mediatype,
     'type' : trashE[i].type,
     'groupname': (mediaG.indexOf( parseInt(trashE[i].id) ) != -1) ? mediaGE[mediaG.indexOf( parseInt(trashE[i].id) )].name : '',
     'groupid': (mediaG.indexOf( parseInt(trashE[i].id) ) != -1) ? mediaGE[mediaG.indexOf( parseInt(trashE[i].id) )].id : ''
     })
     }
     mediaG = [];
     mediaGE = [];


     //sort result by name
     findedElements['result'].sort(dynamicSort('textid'));

     bodytext = tmpl("tmpl-emptytrash", findedElements);

     $("#confirmDiv").confirmModal({
     heading: 'Delete from trash',
     body: bodytext,
     text:'Empty trash',
     callback: function () {
     var response = deletMediaFiles( findedElements['result'], $('#fileupload').serializeArray() );
     if (!response.result){
     sendMessage('alert-error', 'mymedia hiba');
     //return false;
     }
     ////console.log( allMediaFiles );
     for(i in allMediaFiles['result']){
     ( $.inArray( parseInt(allMediaFiles['result'][i].id), trashEId) !== -1 ) ? allMediaFiles['result'].remove(i) : '';
     }
     $trash.empty();
     ////console.log( allMediaFiles );
     }
     });

     });
     */
    /*
     //recycle items
     $('#trashContainer i').bind('click', function(){
     var recycled = $trash.find('li.mediaElement');
     if (recycled.length == 0) return false;
     for(var i=0; i<recycled.length;i++) {
     $mymedia.isotope( 'insert', $(recycled[i]) );
     };
     initIso();
     $('#myMediaList li').draggable(myMediaDragOption);
     });
     */

    $('.mediaBoxList .mediaBox').bind('touchstart', function (e) {
        e.preventDefault();
    }, false);


    /*
     $('#newMB, #selectdiskArea').dropdown();

     $('.dropdown input, .dropdown label, .dropdown li').click(function(e) {
     e.stopPropagation();
     return false;
     });*/
    /*

     */
    /*
     //select diskArea
     $('.dropdown-menu li').bind('click', function(e){
     e.preventDefault();
     alert( 'works');
     return false;
     });

     //$('.dropdown-menu').on('click', function(e){ e.stopPropagation(); });​

     $('#selectdiskArea a').bind('click', function(e){
     var _this = $(this);
     e.preventDefault();
     e.stopPropagation();

     _this.parent().siblings().removeClass('selected');
     _this.parent().addClass('selected');

     //console.log( 'papo' );

     });
     */
    $('.selectediskArea a').live('click', function (e) {//li:not(.newForm)
        e.preventDefault();
        e.stopPropagation();
        var buttonSortname = $('#diskAreaButton').attr('data-sortname');
        var sortname = $(this).attr('data-sortname');//.find('a')
        if (sortname == buttonSortname) return false;
        $('#bc').empty();
        var id = $(this).attr('data-id');//.find('a')
        document.getElementById('diskAreaButton').firstChild.nodeValue = $(this).text();
        //$('#diskAreaButton').text( $(this).text() );//.find('a')
        $(this).parent().siblings().removeClass('selected');
        $(this).parent().toggleClass('selected');
        $('#diskAreaButton').attr('data-sortname', sortname);
        $('#fileupload').find('input[name="diskArea_id"]').val(id);
        $('#fileupload').find('input[name="diskArea_name"]').val(sortname.replace(/#/g, ''))
//console.log('.selectediskArea noclick: ' + sortname);
        mm_initPageLoad();
    });

    $('#createNewDA').bind('click', function (e) {
        e.preventDefault();
        if ($('#newDAName').val() == '' || $('#newDAName').val().length < 3) {
            sendMessage('alert-error', 'Name must minimum 3 character length!');
            return false;
        }
//console.log( 'key upped' );
        var result = checkData($('#newDAName').val(), $('form#fileupload').serializeArray(), 'diskarea');
        if (result.error) {
            sendMessage('alert-error', result.message);
            return false;
        }
        var data = [];
        data['result'] = [];
        data['result'].push({'name': result.name, 'id': result.id, 'doname': result.doname});
        var html = tmpl("tmpl-newDA", data);
        var divider = $('.selectediskArea li.divider');//.find('.divider');
        $(html).insertBefore(divider);
        sendMessage('alert-success', result.message);
        $('#newDAName').val('');
        return false;
    });


    // open/close uploadcontainer
    var content = $('#content');
    content.inner = $('#content .inner');
    content.on('transitionEnd webkitTransitionEnd transitionend oTransitionEnd msTransitionEnd', function (e) {
        if (content.hasClass('open')) {
            content.css('max-height', '360px');
        }
    });

    $('#addFiles').on('click', function (e) {
        content.toggleClass('open closed');
        content.contentHeight = content.outerHeight();
        if (content.hasClass('closed')) {
            content.removeClass('transitions').css('max-height', content.contentHeight);
            setTimeout(function () {
                content.addClass('transitions').css({'max-height': 0, 'opacity': 0});
            }, 10);
        } else if (content.hasClass('open')) {
            content.contentHeight += content.inner.outerHeight();
            content.css({'max-height': content.contentHeight, 'opacity': 1});
        }
        $('#loading').hide();
        $('#loading').find('.loadingMessage').text('Loading in progress...');
    });

    //add youtube video link
    $('#addVideoLink').bind('click', function () {
        if ($(this).hasClass('disabled')) return false;
        var isInserted = false;
        var video = null;
        var videoResult = {};
        $('#youtube').live("blur", function () {//paste keyup attach
            var url = $(this).val();
            video = testUrlForMedia(url); //parseVideoURL(url);
            if (isInserted == false && video.id) {
                $('#video').html('')
                    .append('<iframe width="100%" height="100%" src="http://www.youtube.com/embed/' + video.id + '?fs=1&feature=oembed" frameborder="0" allowfullscreen></iframe>');
                videoResult = getYouTubeInfo(video.id);
                isInserted = true;
            }
        });

        var data = [];
        data['result'] = [];
        data['result'].push({'provider': '', 'id': '', 'title': '', 'desc': ''});
        var bodytext = tmpl("tmpl-videolink", data, false);

        $("#confirmDiv").confirmModal({
            heading: 'Add video link',
            body: bodytext,
            text: 'Add video',
            callback: function () {
                var data = [], ts = Math.round(new Date().getTime() / 1000), date = new Date, uploaded;
                uploaded = date.customFormat("#YYYY#.#MM#.#DD#.");
                data['result'] = [];
                data['result'].push({
                    'type': 'video',
                    'mediatype': 'remote',
                    'name': $('#ytTitle').val(),//videoResult.title,
                    'uploaded_ts': ts,
                    'uploaded': uploaded,
                    'thumbnail_url': 'http://img.youtube.com/vi/' + video.id + '/hqdefault.jpg',
                    'mediaurl': 'http://www.youtube.com/watch?v=' + video.id + '/',
                    'duration': secondsToTime(videoResult.duration)});
                var datas = [];
                datas['result'] = [];
                datas['result'].push(saveMediaFiles(data, $('#fileupload').serializeArray(), 'remote'));
                var result = tmpl("tmpl-mediaElement", datas);
                setmyMediaArray(datas);
                $mymedia.isotope('insert', $(result));
                $('#myMediaList li').draggable(myMediaDragOption);
////console.log( video.provider + ' ' + video.id +' videores: ' + videoResult.title);
            }
        });

    });

    //save uploaded and add to isotope
    $('#uploadFinish').bind('click', function () {
        if ($('ul.files1 li.template-download').length == 0) {
            $('#addFiles').trigger('click');
            return false;
        }
        $('#loading').hide();
        $('#loading').find('.loadingMessage').text('Loading in progress...');
        var data = [];
        data['result'] = [];
        $('ul.files1 li.template-download').each(function (i, e) {
            /*
             data['result'].push({
             'type':$(this).attr('isotope-data-category'),
             'mediatype':$(this).attr('data-mediatype'),
             'name': $(this).attr('isotope-data-name'),
             'uploaded_ts':$(this).attr('isotop-data-uploaded'),
             'uploaded':$(this).find('span.uploaded').text(),
             'thumbnail_url':$(this).find('img').attr('src'),
             'mediaurl':$(this).attr('data-mediaurl'),
             'additional':$(this).find('span.additional').text()
             });
             */
            $(this).filter(function () {
                return !$(this).hasClass('error') ? data['result'].push({
                    'type': $(this).attr('isotope-data-category'),
                    'mediatype': $(this).attr('data-mediatype'),
                    'name': $(this).attr('isotope-data-name'),
                    'uploaded_ts': $(this).attr('isotop-data-uploaded'),
                    'uploaded': $(this).find('span.uploaded').text(),
                    'thumbnail_url': $(this).find('img').attr('src'),
                    'mediaurl': $(this).attr('data-mediaurl'),
                    'additional': $(this).find('span.additional').text(),
                    'size': $(this).attr('data-video-width') + ',' + $(this).attr('data-video-height')
                }) : '';
            });

        });
        saveMediaFiles(data, $('#fileupload').serializeArray(), 'local');

        $('#addFiles').trigger('click');
    });

    // select/deselect all medialist elements
    $('#selectAll').bind('click', function () {
        var elements = $('#myMediaList li').filter(function () {
            return ($(this).hasClass("isotope-hidden") == false)
        });
        var textElement = $(this).children('span');
        $(this).toggleClass('active');
        (textElement.text() == 'Select all') ? elements.addClass('selectedElement') : elements.removeClass('selectedElement');
        (textElement.text() == 'Select all') ? textElement.text('Deselect all') : textElement.text('Select all');
    });


    /*
     setTimeout(function(){
     $mymedia.css('height','').isotope('reLayout');
     }, 500);
     */
});
</script>

<!-- template: display newly created mediaBox -->
<script type="text/x-tmpl" id="tmpl-newMediaBox">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <li class="mediaBox mbcholder span2"
        data-object-type="mediaBox"
        data-object-id="{%=file.id%}"
        data-object-name="{%=file.doname%}">
        <div class="mbHeader">
            <div class="name">{%=file.name%}</div>
            <!--<i class="icon-eye-open opener"></i>-->
            <div class="pointer-right"></div>
            <!--<div class="pointer-bottom1"></div>
            <div class="pointer-bottom2"></div>-->
        </div>
        <!--<ul class="sub greyB" id="{%=file.id%}"></ul>-->
    </li>
    {% } %}
</script>

<!-- template:mediaboxes and elements -->
<script type="text/x-tmpl" id="tmpl-mediaBoxList">
    {% for (var i=0, boxes; boxes=o.result[i]; i++) { %}
    <li class="mediaBox mbcholder span2"
        data-object-type="mediaBox"
        data-object-id="{%=boxes.idTag%}"
        data-object-name="{%=boxes.doname%}">
        <div class="mbHeader">
            <!-- <i class="icon-trash deleter"></i> -->
            <div class="name">{%=boxes.nameTag%}</div>
            <!--<i class="icon-eye-open opener"></i>-->
            <div class="pointer-right"></div>
            <div class="pointer-bottom1"></div>
            <div class="pointer-bottom2"></div>
        </div>
        <ul class="sub greyB" id="{%=boxes.idTag%}">
            {% for (var j=0, file; file=boxes.files[j]; j++) { %}
            <li class="mediaElement span2 lightgreyB {%=file.type%}"

                data-parent-object-id="{%=boxes.idTag%}"
                isotope-data-category="{%=file.type%}"
                isotope-data-name="{%=file.name%}"
                isotop-data-uploaded="{%=file.uploaded%}"
                data-mediaurl="{% if (file.mediatype == 'remote') { %}{%=file.mediaurl%}{% } else { %}<?= $imageURL; ?>{%=file.mediaurl%}{% } %}"
                data-mediatype="{%=file.mediatype%}"
                data-id="{%=file.id%}">
                <div class="colorBar {%=file.type%}"></div>
                <div class="thumbnail">
                    {% if (file.type == 'image' || file.type == 'audio' || file.type == 'video') { %}<a href="#" rel="popover">{% } %}
                        <img src="{% if (file.mediatype == 'remote') { %}{%=file.thumbnail_url%}{% } else { %}<?= $imageURL; ?>{%=file.thumbnail_url%}{% } %}" alt=""/>
                        {% if (file.type == 'image' || file.type == 'audio' || file.type == 'video') { %}</a>{% } %}
                    <div class="caption">
                        <p><span class="name">{%=file.name.substring(0,22)%}</span></p>

                        <p><span class="uploaded">{%=file.uploaded%}</span></p>

                        <p><span class="type">{%=file.type%}</span>
                            {% if (file.duration) { %}<span class="additional">{%=file.duration%}</span>{% } %}
                            {% if (file.filesize) { %}<span class="additional">{%=file.filesize%}</span>{% } %}
                        </p>
                    </div>
                </div>
            </li>

            {% } %}
        </ul>
    </li>
    {% } %}
</script>

<!-- template: display myMediaFiles -->
<script type="text/x-tmpl" id="tmpl-mediaElement">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <li class="mediaElement span2 lightgreyB {%=file.type%} isotope-item"
        isotope-data-category="{%=file.type%}"
        isotope-data-name="{%=file.name%}"
        isotop-data-uploaded="{%=file.uploaded_ts%}"
        isotope-mediabox=""
        data-mediaurl="{% if (file.mediatype == 'remote'){ %}{%=file.mediaurl%}{% } else { %}<?= $imageURL; ?>{%=file.mediaurl%}{% } %}"
        data-mediatype="{%=file.mediatype%}"
        data-id="{%=file.id%}">
        <div class="colorBar {%=file.type%}"></div>
        <div class="thumbnail">
            {% if (file.type == 'image' || file.type == 'audio' || file.type == 'video') { %}<a href="#" rel="popover">{% } %}
                <img src="{% if (file.mediatype == 'remote') { %}{%=file.thumbnail_url%}{% } else { %}<?= $imageURL; ?>{%=file.thumbnail_url%}{% } %}" alt=""/>
                {% if (file.type == 'image' || file.type == 'audio' || file.type == 'video') { %}</a>{% } %}
            <div class="caption">
                <p><span class="name">{%=file.name.substring(0,22)%}</span></p>

                <p><span class="uploaded">{%=file.uploaded%}</span></p>

                <p><span class="type">{%=file.type%}</span>
                    {% if (file.duration) { %}<span class="additional">{%=file.duration%}</span>{% } %}
                    {% if (file.filesize) { %}<span class="additional">{%=file.filesize%}</span>{% } %}
                </p>
            </div>
        </div>
    </li>
    {% } %}
</script>


<!-- template: display newly created mediaBox -->
<script type="text/x-tmpl" id="tmpl-newMediaBox">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <li class="mediaBox mbcholder"
        data-object-type="mediaBox"
        data-object-id="{%=file.id%}"
        data-object-name="{%=file.doname%}">
        <div class="mbHeader">
            <div class="name">{%=file.name%}</div>
            <!--<i class="icon-eye-open opener"></i>-->
            <div class="pointer-right"></div>
            <!--<div class="pointer-bottom1"></div>
            <div class="pointer-bottom2"></div>-->
        </div>
        <!--<ul class="sub greyB" id="{%=file.id%}"></ul>-->
    </li>
    {% } %}
</script>

<!-- template: display newly created diskArea -->
<script type="text/x-tmpl" id="tmpl-newDA">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <li class="area">
        <a data-href="{%=file.doname%}" data-sortname="{%=file.doname%}" data-id="{%=file.id%}">{%=file.name%}</a></li>
    {% } %}
</script>

<!-- template:files available for upload -->
<script id="template-upload" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    <li class="template-upload span2 lightgreyB ">
        <div class="colorBar {%=file.typeShort%}"></div>
        <div class="thumbnail ">
            <div class="preview"><span class="fade"></span></div>
            <div class="caption">
                <p class="name">{%=file.name.substring(0,22)%}</p>
                {% if (file.error) { %}
                <p class="error"><span class="label label-important">Error</span> {%=file.error%}</p>
              <span class="cancel">
                <button class="btn btn-dark"><span>Cancel</span></button>
              </span>
                {% } else if (o.files.valid && !i) { %}
                <p class="uploaded">na</p>

                <p class="type">{%=file.typeShort%}</p>

                <p>

                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0">
                    <div class="bar" style="width:0%;"></div>
                </div>
                </p>
              <span class="start">
                {% if (!o.options.autoUpload) { %}
                  <button class="btn btn-dark"><span>upload</span></button>
                {% } %}
              </span>
              <span class="cancel">
                {% if (!i) { %}
                <button class="btn btn-dark"><span>Cancel</span></button>
                {% } %}
              </span>
                {% } else { %}
                <p></p>
                <button class="btn btn-dark"><span>Cancel</span></button>
                {% } %}
            </div>
        </div>
    </li>
    {% } %}
</script>

<!-- template:files available for download -->
<script id="template-download" type="text/x-tmpl">
    {% for (var i=0, file; file=o.files[i]; i++) { %}
    <li class="template-download {% if (file.error) { %}error {% } %}span2 lightgreyB "
        isotope-data-category="{%=file.typeShort%}"
        isotope-data-name="{%=file.name%}"
        isotop-data-uploaded="{%=file.uploaded%}"
        data-mediaurl="<?= $imageURL; ?>{%=file.mediaurl%}"
        data-mediatype="{%=file.mediatype%}"
        data-video-width="{%=file.videoWidth%}"
        data-video-height="{%=file.videoHeight%}">
        {% if (file.error) { %}
        <p><span class="title">File:</span> {%=file.name.substring(0,22)%}</p>
        <!--<p><span class="title">Type:</span> {%=file.type%}</p>-->
        <p class="error"><span class="label label-important">Error</span> {%=file.error%}</p>
        {% } else { %}
        <div class="colorBar {%=file.typeShort%}"></div>
        <div class="thumbnail">
            {% if (file.thumbnail_url) { %}<img src="<?= $imageURL; ?>{%=file.thumbnail_url%}" width="160" height="120">{% } %}

            <div class="caption">
                <p><span class="name">{%=file.name.substring(0,22)%}</span></p>

                <p><span class="uploaded">{%=file.uploadedTime%}</span></p>

                <p><span class="type">{%=file.typeShort%}</span>
                    {% if (file.duration) { %}<span class="additional">{%=file.duration%}</span>{% } %}
                    {% if (file.filesize) { %}<span class="additional">{%=file.filesize%}</span>{% } %}
                </p>

                <span class="delete">
                  <button class="btn btn-dark" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}><span>Delete</span></button>
                    <input type="checkbox" name="delete" value="1">
                </span>

            </div>
        </div>
        {% } %}
    </li>
    {% } %}
</script>
<!--<p class="size"><span>Uploaded: {%=o.formatFileSize(file.size)%}</span></p>
                
                <span class="delete"><button class="btn btn-dark" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}><span>Delete</span></button>
                
                <input type="checkbox" name="delete" value="1"></span>-->

<!-- {% include('tmpl-link', {name: "Website", url: "http://example.org"}); %} -->
<!-- data-object-type="myMediaListElement"
         data-parent-object-type="mediaBox" -->
<!--
      {% include('tmpl-mediaElement', {'result' : {'id':file.id, 'thumbnail_url':file.thumbnail_url,'mediaurl':file.mediaurl,'name':file.name,'uploaded':file.uploaded, 'uploaded_ts':file.uploaded_ts,'type': file.type}} ); %} 
-->


<!-- template: display linked videoInput -->
<script type="text/x-tmpl" id="tmpl-videolink">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <input type="text" id="youtube" class="input-fluid"/>
    <span class="help-block">Enter a url.</span>

    <div class="clearfix"></div>
    <div class="pull-left">
        <div id="video" style="width:250px;height:140px;border:1px solid black;position:relative;"></div>
    </div>
    <div class="pull-left" style="width: 260px;margin-left: 10px;">
        <p class="greyT" style="word-wrap: break-word;">Samples:<br/>
            http://www.youtube.com/watch?v=8UE6gzNuUKo<br/>
            http://www.youtube.com/v/8UE6gzNuUKo<br/>
            http://vimeo.com/22080133?asdasdjk
        </p>
    </div>
    <div class="clearfix"></div>
    <form class="form-horizontal">
        <div class="control-group">
            <label class="control-label" for="ytTitle">Title</label>

            <div class="controls"><input type="text" id="ytTitle" value=""/></div>
        </div>
        <div class="control-group">
            <label class="control-label" for="ytDesc">Description</label>

            <div class="controls"><textarea id="ytDesc" style="margin: 0px; width: 316px; height: 107px;"></textarea>
            </div>
        </div>
        <input type="hidden" id="ytAuthor" value=""/>
        <input type="hidden" id="ytUploaded" value=""/>
    </form>
    {% } %}
</script>


<!-- template: for audio -->
<script type="text/x-tmpl" id="tmpl-audio">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <audio controls="" preload="auto" width="200px" height="20px" style="display: block;">
        <source src="{%=file.name%}.mp3" type="audio/mpeg">
        <source src="{%=file.name%}.wav" type="audio/wav">
    </audio>
    {% } %}
</script>

<!-- template: for video -->
<script type="text/x-tmpl" id="tmpl-video">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <video controls preload="auto" width="100%" height="100%" poster="{%=file.poster%}" volume="0.2">
        <source src="{%=file.name%}.mp4" type="video/mp4"/>
        <source src="{%=file.name%}.ogg" type="video/ogg"/>
    </video>
    {% } %}
</script>

<!-- template: for emptytrash -->
<script type="text/x-tmpl" id="tmpl-emptytrash">
    <div class="listHolder">
        <table class="table table-striped table-condensed">
            <caption>Files to delete</caption>
            <thead>
            <tr>
                <th>Name</th>
                <th>in Mediabox</th>
            </tr>
            </thead>
            <tbody>
            {% for (var i=0, file; file=o.result[i]; i++) { %}
            <tr>
                <td>{%=file.mediaelement%}</td>
                <td>{%=file.groupname%}</td>
            </tr>
            {% } %}
            </tbody>
        </table>
    </div>
</script>
<!-- template: for emptytrash2:affected slides -->
<script type="text/x-tmpl" id="tmpl-emptytrash2">
    <div class="listHolder">
        <table class="table table-striped table-condensed" style="width:100%;">
            <caption>Affected slides in Slideshows</caption>
            <thead>
            <tr>
                <th style="width:20%;">Name</th>
                <th style="width:10%;">badge</th>
                <th style="width:25%;">tag</th>
                <th style="width:45%;">description</th>
            </tr>
            </thead>
            <tbody>
            {% for (var i=0, file; file=o.result[i]; i++) { %}
            <tr>
                <td>{%=file.name%}</td>
                <td colspan="3">
                    <table style="width:100%">
                        {% for (var j=0, slide; slide=file.slides[j]; j++) { %}
                        <tr>
                            <td style="width:10%"
                            ;>{%=slide.badge%}</td>
                            <td style="width:35%">{%=slide.tag%}</td>
                            <td style="width:55%"
                            ;>{%=slide.description%}</td></tr>
                        {% } %}
                    </table>
                </td>
            </tr>
            {% } %}
            </tbody>
        </table>
    </div>
</script>
<!-- template: for emptytrash2:affected slides -->
<script type="text/x-tmpl" id="tmpl-emptytrashMessage">
    <div class="listHolder">
        <table class="table table-striped table-condensed" style="width:100%;">
            <caption>Affected slides in Slideshows</caption>
            <thead>
            <tr>
                <th style="width:20%;">Name</th>
                <th style="width:10%;">badge</th>
                <th style="width:25%;">tag</th>
                <th style="width:45%;">description</th>
            </tr>
            </thead>
            <tbody>

            <tr>
                <td colspan="4">{%=o.result.error%}</td>
            </tr>

            </tbody>
        </table>
    </div>
</script>

<!-- template: for emptytrash2:affected slides -->
<script type="text/x-tmpl" id="tmpl-download">
    <div class="listHolder">
        <table class="table table-striped table-condensed" style="width:100%;">
            <caption>Local files</caption>
            <thead>
            <tr>
                <th style="width:85%;">Name</th>
                <th style="width:15%;">Type</th>
            </tr>
            </thead>
            <tbody>
            {% for (var j=0, file; file=o[j]; j++) { %}
            <tr>
                <td style="width:85%">{%=file.name%}</td>
                <td style="width:15%">{%=file.type%}</td>
            </tr>
            {% } %}
            </tbody>
        </table>
    </div>
</script>