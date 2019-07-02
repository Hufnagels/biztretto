<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');

    $protocol = connectionType();
    $imageURL = $protocol.$_SESSION['office_nametag'].'.'.DOMAINTAG.'/media/';
    $downloadURL = $protocol.'media.'.DOMAINTAG.'/_zip/';
    $videodomain = $protocol.$_SESSION['office_nametag'].'.'.DOMAINTAG.'/';

    $sql = "SELECT diskArea_id FROM media_diskarea WHERE office_nametag = '".$_SESSION['office_nametag']."' AND office_id = ".$_SESSION['office_id']." AND name = 'default'";
    $result = MySQL::query($sql,false,false);
    $diskArea_id = $result[0]['diskArea_id'];

    $span2Header = 'mediabox';
    $newButtonName = 'new box';
    $newMBFormInputPlaceholder = ' Mediabox...';
    $newDAFormInputPlaceholder = ' Folder...';
    $viewAll = 'View all';
    $trashButton = 'trash';
    $span2Headerright = 'option';
    $span10Header = 'my media';
    $span10Header2 = 'add media';

    $quotaJsonArray = json_encode(Office::helperCalculateDiskUsage($_SESSION['office_id']));

?>

<link rel="stylesheet" href="/css/jquery.fileupload-ui.css">
<noscript><link rel="stylesheet" href="/assets/fileupload/css/jquery.fileupload-ui-noscript.css"></noscript>
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
                                        <span class="add-on"><button type="button" class="btn btn-dark" id="createNew"><i class="icon-plus"></i></button></span>
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
    </div>

    <!-- /LEFT SIDE -->

    <!-- MIDDLE SIDE -->
    <div class="span10">
        <div class=" colHeader">
            <h3 class="orangeT ">
                <span class="pull-left">My media </span>
                <span class="pull-left hiddenClass">
                    <span class="divider">&nbsp;></span>
                    <span class="dropdown">
                        <span class="dropdown-toggle " role="button" data-toggle="dropdown" id="diskAreaButton" data-sortname="default">Default<span class="caret"></span>
                    </span>
                    <ul class="dropdown-menu selectediskArea">
                        <li class="divider"></li>
                            <li class="newForm">
                                <form class="form-vertical" method="post" action="#">
                                    <div class="control-group">
                                        <div class="input-append">
                                            <input type="text" id="newDAName" class="input-newMB" placeholder="<?= $newDAFormInputPlaceholder; ?>">
                                            <span class="add-on"><button type="button" class="btn btn-dark" id="createNewDA"><i class="icon-plus"></i></button></span>
                                        </div>
                                    </div>
                                    <div class="control-group hidden"><span class="help-block"></span></div>
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
        <div id="content" class="closed transitions middlegreyB fileuploadContainer">
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

                    <ul class="files1 thumbnails" id="uploadContainer"></ul>

                    <div class="clearfix"></div>
                    <div class="pull-left">
                        <ul><li>Files left:<div id="filesLeft"></div></li></ul>
                    </div>
                    <div class="pull-right">
                        <button type="button" class="btn btn-dark" id="uploadFinish"><span>Finish</span></button>
                    </div>
                </form>
                <!-- /UPLOAD FORM -->
            </div>
        </div>
        <!-- uploading content -->
        <div class="clearfix"></div>

        <!-- ISOTOPE -->
        <div class="well well-small" id="sortingBar">
            <!-- filters -->
            <div class="pull-left">
                <h3 class="functionHeader middlegreyT2">show:
                    <div id="filters" class="btn-group" data-toggle="buttons-radio" isotope-data-option-key="filters">
                        <button type="button" class="btn btn-dark active" isotope-data-filter="all"><span>all</span></button>
                        <button type="button" class="btn btn-dark" isotope-data-filter=".image, .image_jpeg"><span>image</span></button>
                        <button type="button" class="btn btn-dark" isotope-data-filter=".video"><span>video</span></button>
                        <button type="button" class="btn btn-dark" isotope-data-filter=".audio"><span>audio</span></button>
                        <button type="button" class="btn btn-dark" isotope-data-filter=".pdf, .word, .excel, .powerpoint, .application_vnd.ms-excel, .application_msword, .application_pdf"><span>attachment</span></button>
                        <button type="button" class="btn btn-dark" isotope-data-filter=".selectedElement"><span>selected</span></button>
                    </div>
                </h3>
            </div>
            <!-- sortBy -->
            <div class="pull-left">
                <h3 class="functionHeader middlegreyT2">sort:
                    <div id="sortBy" class="btn-group" data-toggle="buttons-radio" data-option-key="attr">
                        <button type="button" class="btn btn-dark active" data-option-value="name" data-tsort="include"><span>name</span></button>
                        <button type="button" class="btn btn-dark " data-option-value="date" data-tsort="include"><span>date</span></button>
                    </div>
                    <div id="sortOrder" class="btn-group" data-toggle="buttons-radio" data-option-key="order">
                        <button type="button" class="btn btn-dark active" data-option-value="asc" data-tsort="include"><span><i class="icon-arrow-up"></i></span></button>
                        <button type="button" class="btn btn-dark " data-option-value="desc" data-tsort="include"><span><i class="icon-arrow-down"></i></span></button>
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
                            <span class="btn btn-dark dropdown-toggle" data-toggle="dropdown"><span class="icon-ok"></span><span class="caret"></span></span>
                            <ul class="dropdown-menu" id="selectionMenu">
                                <li class=""><a href="#" class="selectall">Select all</a></li>
                                <li class=""><a href="#" class="deselectall">Deselect all</a></li>
                                <li class=""><a href="#" class="invertselection">Invert selection</a></li>

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
    <!-- /MIDDLE SIDE -->
</div>

<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/bootstrap-editable.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/lib/mbFunction.js"></script>
<script type="text/javascript">

    $(function () {
        MyMedia.init();
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

<!-- template: display newly created diskArea -->
<script type="text/x-tmpl" id="tmpl-newDA">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <li class="area">
        <a data-href="{%=file.doname%}" data-sortname="{%=file.doname%}" data-id="{%=file.id%}">{%=file.name%}</a></li>
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

    </li>
    {% } %}
</script>

<!-- template: display myMediaFiles -->
<script type="text/x-tmpl" id="tmpl-mediaElement">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <li class="mediaElement span2 lightgreyB {%=file.type%}"
        data-category="{%=file.type%}"
        data-name="{%=file.name%}"
        data-date="{%=file.uploaded_ts%}"
        data-mediabox="{%=clearNULL(file.boxid)%}"
        data-mediaurl="{% if (file.mediatype == 'remote'){ %}{%=file.mediaurl%}{% } else { %}<?= $imageURL; ?>{%=file.mediaurl%}{% } %}"
        data-mediatype="{%=file.mediatype%}"
        data-id="{%=file.id%}"
        data-video-width="{%=file.videoWidth%}"
        data-video-height="{%=file.videoHeight%}">
        <div class="colorBar {%=file.type%}"></div>
        <div class="thumbnail">
            {% if (file.type == 'image' || file.type == 'audio' || file.type == 'video') { %}<a href="#" rel="popover">{% } %}
                <img src="{% if (file.mediatype == 'remote') { %}{%=file.thumbnail_url%}{% } else { %}<?= $imageURL; ?>{%=file.thumbnail_url%}{% } %}" alt=""/>
                {% if (file.type == 'image' || file.type == 'audio' || file.type == 'video') { %}</a>{% } %}

        </div>
        <div class="caption">
            <p><span class="name">{%=file.name.substring(0,22)%}</span></p>
            <p class="hidden subdetails"><span class="uploaded">{%=file.uploaded%}</span><span class="type">{%=file.type%}</span>
                {% if (file.duration) { %}<span class="duration">{%=file.duration%}</span>{% } %}
                {% if (file.filesize) { %}<span class="filesize">{%=file.filesize%}</span>{% } %}
            </p>
        </div>
        <div class="optionBar">
            <span class="selectButton"><i class="icon-ok"></i></span>
            <span class="detailButton"><i class="icon-info"></i></span>
        </div>
    </li>
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
        data-category="{%=file.typeShort%}"
        data-name="{%=file.name%}"
        data-uploaded="{%=file.uploaded%}"
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

<!-- template: display linked videoInput -->
<script type="text/x-tmpl" id="tmpl-videolink">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <label class="control-label" for="ytTitle">Url</label>
    <input type="text" id="youtube" class="input-fluid"/>
    <div class="clearfix"></div>
    <div class="pull-left">
        <div id="video" style="width:250px;height:140px;border:1px solid black;position:relative;"></div>
    </div>
    <div class="pull-left" style="width: 260px;margin-left: 10px;">
        <p class="greyT" style="word-wrap: break-word;">Samples:<br/>
            http://www.youtube.com/watch?v=8UE6gzNuUKo<br/>
            http://www.youtube.com/v/8UE6gzNuUKo
        </p>
    </div>
    <div class="clearfix"></div>
    <form class="form-vertical">
        <div class="control-group">
            <label class="control-label" for="ytTitle">Title</label>

            <div class="controls"><input type="text" id="ytTitle" class="input-fluid" value=""/></div>
        </div>
        <!--<div class="control-group">
            <label class="control-label" for="ytDesc">Description</label>

            <div class="controls"><textarea id="ytDesc" style="margin: 0px; width: 316px; height: 107px;"></textarea>
            </div>
        </div>-->
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
    <iframe src="<?=$videodomain;?>videoloader/{%=file.name%}-{%=file.videoWidth%}-{%=file.videoHeight%}/" style="width: 100%; height: 100%;" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreeniframe></iframe>
    <!--<video controls preload="auto" width="100%" height="100%" poster="{%=file.poster%}" volume="0.2">
        <source src="{%=file.name%}.mp4" type="video/mp4"/>
        <source src="{%=file.name%}.ogg" type="video/ogg"/>
    </video>-->
    {% } %}
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
                <td colspan="4">{%=o.error%}</td>
            </tr>

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
                            <td style="width:10%">{%=slide.badge%}</td>
                            <td style="width:35%">{%=slide.tag%}</td>
                            <td style="width:55%">{%=slide.description%}</td></tr>
                        {% } %}
                    </table>
                </td>
            </tr>
            {% } %}
            </tbody>
        </table>
    </div>
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