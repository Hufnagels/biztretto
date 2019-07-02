<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');

$protocol = connectionType();
$imageURL = $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/';
$subdomain = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/slideshow/';
$videodomain = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/';

$selectArray = array(
    'table'          => 'media_diskarea',
    'fields'         => 'diskArea_id As id, name',
    'condition'      => array('office_nametag' => MySQL::filter($_SESSION['office_nametag'])),
    'conditionExtra' => " NOT name = 'corporate'",
    'order'          => '1',
    'limit'          => 50
);
$result = MySQL::select($selectArray);
$diskAreaJson = json_encode($result, TRUE);

$span2Header = 'slideshow';
$loadslideshow_Button = '';
$saveslideshow_Button = 'save';
$saveasslideshow_Button = 'save as';
$span10Header = 'slide designer';
$createtext_Button = 'insert text';

$addnewslide_Button = 'new slide';
$duplicateslide_Button = 'duplicate slide';
$saveslide_Button = 'save slide';

$newFormInputPlaceholder = ' Slide...';

$accordion0_Header = 'mediabox';
$accordion0_addmedia_Button = 'add media';
$accordion1_Header = 'all slideshow';
$accordion1_select_Button = '';
$accordion2_Header = "tests";

?>
<link rel="stylesheet" type="text/css" charset="utf-8" media="screen"  href="/css/colorpicker.css">
<link rel="stylesheet" type="text/css" charset="utf-8" media="screen"  href="/css/matrices.css">
<!---->
<script type="text/javascript" charset="utf-8" src="/lib/matrices.js"></script>

<script type="text/javascript" charset="utf-8">CKEDITOR_BASEPATH = '/assets/ckeditor/';</script>
<script type="text/javascript" charset="utf-8" src="/assets/ckeditor/ckeditor.js"></script>


<script type="text/javascript" charset="utf-8">
    var folderString = $.parseJSON( '<?=$diskAreaJson;?>' );
    var subDomain = '<?=$subdomain;?>';
</script>
<style>
    .textBGLeftSlideshow:after {
        content: "select:";
    }

    .textBGRightMediabox:after {
        content: "select:";
    }
</style>
<form id="saveslidesform" action="#" method="POST" class="hidden">
    <input type="text" name="id" value="" id="slideshowID"/>
    <input type="text" name="name" value="" id="slideshowNAME"/>
    <input type="text" name="diskArea" value="<?= $result[0]['id']; ?>"/>
    <input type="hidden" name="office_id" value="<?= $_SESSION['office_id'] ?>"/>
    <input type="hidden" name="office_nametag" value="<?= $_SESSION['office_nametag'] ?>"/>
    <input type="hidden" name="owner" value="<?= $_SESSION['u_id'] ?>"/>
</form>
<section id="inner-headline">
    <div class="container">
        <div class="row">
            <div class="span51 pull-left" style="width:auto">
                <div class="inner-heading">
                    <h2 id="linkData"><span class="pull-left" id="bc"></span></h2>
                </div>
            </div>
            <div class="span81 pull-right" style="margin-left:0;">

                <!-- ISOTOPE -->
                <div class="pull-right" id="sortingBar">
                    <!-- editorTopBar -->
                    <div class="createNew" id="editorTopBar">
                        <div class="pull-left">
                            <h3 class="functionHeader middlegreyT2">
                                <div class="btn-toolbar">
                                    <div class="btn-group" data-toggle="button">
                                        <button type="button" class="btn btn-dark active" id="removeBG"><i class="icon-th"></i></button>
                                        <button type="button" class="btn btn-dark" id="addText"><?=$createtext_Button;?></button>
                                        <button type="button" class="btn btn-dark" id="clearArea">clear area</button>
                                        <button type="button" class="btn btn-dark" id="duplicateSlide"><?=$duplicateslide_Button;?></button>
                                        <button type="button" class="btn btn-dark" id="newSlide"><?=$addnewslide_Button;?></button>
                                        <button type="button" class="btn btn-dark" id="colorPicker" data-color-format="rgba" data-color="rgba(255, 255, 255,1)" data-colorpicker-guid="1">
                                            <i style="background-color: rgba(255, 255, 255,1);display: block;"></i>
                                        </button>
                                        <!--<button class="btn btn-dark"id="toCanvas"><i class="icon-screenshot"></i></button>-->
                                        <button id="saveSlide" class="btn btn-dark" title="You can use CTRL+S to save slide" data-toggle="tooltip"><?=$saveslide_Button;?></button>

                                        <span class="dropdown" style="position: relative;float: right;">
                                            <button class="dropdown-toggle btn btn-dark" role="button" data-toggle="dropdown">Create<span class="caret"></span></button>
                                            <ul class="dropdown-menu" id="slideshowOption">
                                                <li class=""><a href="#" id="createNewSlideShow">New slideshow</a></li>
                                                <li class="extra"><a href="javascript:void(0)" id="previewSlideShow" data-type="preview">Preview slideshow</a></li>
                                                <!--<li class="extra"><a href="javascript:void(0)" id="orchestrationView" data-type="orchestration">Orchestration</a></li>-->
                                            </ul>
                                        </span>

                                    </div>
                                </div>
                            </h3>
                        </div>
                    </div>
                    <!-- /editorTopBar -->
                </div>
                <!-- /ISOTOPE -->
            </div>
        </div>
</section>
<section id="MainContent" class="special">
    <div class="container">
        <div class="row">
            <!-- LEFT SIDE -->
            <div class="span2">
                <div class="affix-top" id="mySlideshowsContainer"><!-- mySlidesContainer -->
                    <div class="accordion" id="accordionContent2">
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionContent2" href="#collapse11"><i class="icon-minus"></i><?=$span2Header;?></a>
                            </div>
                            <div id="collapse11" class="accordion-body collapse in">
                                <div class="accordion-inner">
                                    <div class="clearfix"></div>
                                    <!--<div class="createNew well well-small" id="themeRow" style="display:none;">
                                        <div class="dropdown">
                                            <h3 class="functionHeader middlegreyT2" id="mbArea"><span>folder:</span>
                                                <div class="pull-right">
                                                    <?
                                                    if (count($result) > 1)
                                                    {
                                                        echo '<span class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#" data-diskarea-id="' . $result[0]['id'] . '" id="daForSlideShow"><span class="name">' . $result[0]['name'] . '</span><b class="caret"></b></span><ul class="dropdown-menu" id="themeList">';
                                                        for ($i = 0; $i < count($result); $i++)
                                                        {
                                                            echo '<li class="' . ($i == 0 ? 'selected"' : '') . '"><a href="javascript:void(0)" class="level1" data-diskarea-id="' . $result[$i]['id'] . '" >' . $result[$i]['name'] . '</a></li>';
                                                        }
                                                        echo '</ul>';
                                                    } else
                                                    {
                                                        echo '<span class="selectMenu btn-dark btn-r" role="button" data-diskarea-id="' . $result[0]['id'] . '" id="daForSlideShow"><span class="name">' . $result[0]['name'] . '</span></span>';
                                                    }
                                                    ?>
                                                </div>
                                            </h3>
                                        </div>
                                    </div>-->

                                    <div class="createNew well well-small">
                                        <div class="dropdown">
                                            <h3 class="functionHeader middlegreyT2" id="myslidesContainer">
                                                    <button class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#" id="loadSlideShowButton"><span class="name">select slideshow</span><b class="caret"></b></button>
                                                    <ul class="dropdown-menu" id="loadSlideshow">
                                                        <?
                                                        /*
                                                        $additional = '';
                                                        $additional = "AND ss.diskArea_id = " . $result[0]['id'];

                                                        $sql = "SELECT ss.slideshow_id, ss.name, ss.description, COUNT(sa.slides_id) AS darab,
                                                                    IF ((SELECT slideshow_id FROM training_slideshow WHERE training_slideshow.slideshow_id=ss.slideshow_id LIMIT 1)>0,1,0) AS readonly
                                                                FROM slide_slideshow ss
                                                                LEFT JOIN slide_slides sa
                                                                ON ss.slideshow_id = sa.slideshow_id
                                                                WHERE ss.office_id = " . MySQL::filter($_SESSION['office_id']) . " AND
                                                                    ss.office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' AND
                                                                    ss.isEnabled = 1 " . $additional . "
                                                                    GROUP BY ss.slideshow_id
                                                                    ORDER BY ss.name ASC";

                                                        $query = MySQL::query($sql, false, false);
                                                        $slideShowArray = array();

                                                        foreach ($query as $row) {
                                                            $slideShowArray = array(
                                                                'id' => $row['slideshow_id'],
                                                                'name' => $row['name'],
                                                                'readonly' => $row['readonly'],
                                                                'doname' => str_replace(' ', '', strtolower($row['name'])),
                                                                'count' => ($row['darab'] == NULL ? 0 : $row['darab']),
                                                                'type' => 'normal',
                                                                'readonly' => $row['readonly'],
                                                                'description' => ($row['description'] == NULL ? '' : $row['description'])
                                                            );
                                                            //echo '<li class="'.($slideShowArray['readonly'] == 1 ? 'disabled':'').'"><a href="#" data-id="'.$slideShowArray['id'].'" rel="tooltip" title="'.$slideShowArray['description'].'" ><span class="sname">'.$slideShowArray['name'].'</span><span class="pull-right badge">'.$slideShowArray['count'].'</span></a></li>';
                                                        }
                                                        */
                                                        ?>
                                                    </ul>

                                            </h3>
                                        </div>
                                    </div>
                                    <!-- slideshowHolder -->
                                    <div class="slidesHolder"><ul class="slidesList span2" id="slidesList" data-object-type="slidesList" style=""></ul></div>
                                    <!-- /slideshowHolder -->
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionContent2" href="#collapse12"><i class="icon-plus"></i><?=$accordion0_Header;?></a>
                            </div>
                            <div id="collapse12" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="createNew well well-small">
                                        <div class="dropdown">
                                            <h3 class="functionHeader middlegreyT2" id="mbArea">

                                                    <button class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#"><span class="name">select</span><b class="caret"></b></button>
                                                    <ul class="dropdown-menu" id="mediaBoxList2">
                                                        <?

                                                        $mediaBoxesArray = array();
                                                        $i = 0;

                                                        //egy diskarea-hoz tartozo mediabox lista legyujtesre,
                                                        //valamint hogy összesen, illetve egyenkent mennyi file van
                                                        //all files
                                                        $sql2 = "SELECT COUNT(mymedia_id) AS badge FROM media_mymedia
                                                                        WHERE office_id = " . MySQL::filter($_SESSION['office_id']) . "
                                                                            AND diskArea_id = '" . MySQL::filter($result[0]['id']) . "'
                                                                            AND mediatype = 'local'";

                                                        $query2 = MySQL::query($sql2, false, false);

                                                        $mediaBoxesArray[0] = array(
                                                            "id" => 'all',
                                                            "name" => 'All files',
                                                            "doname" => 'all files',
                                                            "badge" => $query2[0]['badge']
                                                        );

                                                        echo '<li> <a href="javascript:void(0)" class="level2" data-id="'.$mediaBoxesArray[0]['id'].'"><span class="name">'.$mediaBoxesArray[0]['name'].'</span><span class="pull-right badge">'.$mediaBoxesArray[0]['badge'].'</span></a></li>';

                                                        //egy diskarea-hoz tartozo mediabox lista legyujtesre,
                                                        //valamint hogy összesen, illetve egyenkent mennyi file van
                                                        $sql = "SELECT box.mediabox_id AS boxid, box.name AS name, COUNT(media.mymedia_id) AS darab
                                                                    FROM media_mediabox box
                                                                    LEFT JOIN media_mymedia media
                                                                    ON box.mediabox_id = media.mediabox_id
                                                                    WHERE box.office_id = " . MySQL::filter($_SESSION['office_id']) . "
                                                                        AND media.office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'
                                                                        AND media.diskArea_id = '" . MySQL::filter($result[0]['id']) . "'
                                                                        AND media.mediatype = 'local'
                                                                        GROUP BY media.mediabox_id
                                                                        ORDER BY media.name ASC";

                                                        $query3 = MySQL::query($sql, false, false);
                                                        $db = count($query3);
                                                        //mediaboxes and files count
                                                        for ($j = 0; $j < $db; $j++) {
                                                            $nameTag = $query3[$j]['name'];
                                                            $idTag = $query3[$j]['boxid'];
                                                            $badge = $query3[$j]['darab'];
                                                            $doname = str_replace(' ', '', strtolower($nameTag));
                                                            $mediaBoxesArray[$j + 1] = array(
                                                                "id" => $idTag,
                                                                "name" => $nameTag,
                                                                "doname" => $doname,
                                                                "badge" => $badge
                                                            );
                                                            echo '<li> <a href="javascript:void(0)" class="level2" data-id="'.$mediaBoxesArray[$j+1]['id'].'"><span class="name">'.$mediaBoxesArray[$j+1]['name'].'</span><span class="pull-right badge">'.$mediaBoxesArray[$j+1]['badge'].'</span></a></li>';
                                                        }
                                                        ?>
                                                    </ul>

                                            </h3>
                                        </div>
                                    </div>
                                    <!-- sortingBar -->
                                    <div class="createNew well well-small" id="sortingIconBar">
                                        <div class="btn-group" data-toggle="buttons-radio">
                                            <button type="button" class="btn btn-dark active" data-class=""><i class="icon-eye-open"></i>
                                            </button>
                                            <button type="button" class="btn btn-dark" data-class="image"><i class="icon-camera"></i></button>
                                            <button type="button" class="btn btn-dark" data-class="video"><i class="icon-film"></i></button>
                                            <button type="button" class="btn btn-dark" data-class="audio"><i class="icon-music"></i></button>
                                            <button type="button" class="btn btn-dark" data-class="word excel pdf"><i class="icon-file"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <!-- /sortingBar -->
                                    <div class="sideBar span2"><ul class="mediaBox span2 sub" id="editorsMediaBox"></ul></div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionContent2" href="#collapse13"><i class="icon-plus"></i><?=$accordion2_Header;?></a>
                            </div>
                            <div id="collapse13" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div id="templateContainer"><ul class="slidesList span2" id="testSlides" data-object-type="slidesList"><? include($_SERVER['DOCUMENT_ROOT'] . '/pages/slideeditor2/templates.php'); ?></ul></div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionContent2" href="#collapse14"><i class="icon-plus"></i><?=$accordion1_Header;?></a>
                            </div>
                            <div id="collapse14" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="createNew well well-small">
                                        <div class="dropdown">
                                            <h3 class="functionHeader middlegreyT2"><span><?=$accordion1_select_Button;?></span>



                                                    <button class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#" id="loadSlideShow2Button"><span class="name">select</span><b class="caret"></b></button>
                                                    <ul class="dropdown-menu" id="loadSlideshow2"></ul>
                                                        <?
                                                        /*
                                                        foreach ($query as $row) {
                                                            $slideShowArray = array(
                                                                'id' => $row['slideshow_id'],
                                                                'name' => $row['name'],
                                                                'readonly' => $row['readonly'],
                                                                'doname' => str_replace(' ', '', strtolower($row['name'])),
                                                                'count' => ($row['darab'] == NULL ? 0 : $row['darab']),
                                                                'type' => 'normal',
                                                                'description' => ($row['description'] == NULL ? '' : $row['description'])
                                                            );
                                                            echo '<li><a href="#" data-id="'.$slideShowArray['id'].'" rel="tooltip" title="'.$slideShowArray['description'].'" ><span class="sname">'.$slideShowArray['name'].'</span><span class="pull-right badge">'.$slideShowArray['count'].'</span></a></li>';
                                                        }
                                                        */
                                                        ?>



                                            </h3>
                                        </div>
                                    </div>
                                    <div id="slidesList2Container">
                                        <ul class="slidesList span2" id="slidesList2" data-object-type="slideList"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <!-- /LEFT SIDE -->

            <!-- MIDDLE SIDE -->
            <div class="span10">
                <!-- slideEditor -->
                <div id="slideEditor" class="affix-top whiteB">

                    <div class="clearfix"></div>
                    <div class="well well-small middlegreyB" id="editorAreaContainer">
                        <!-- editorArea -->
                        <form action="#" id="editorAreaForm">
                            <div class="editorArea" id="editorArea" data-template-type="" data-slide-type="normal"></div>
                        </form>
                        <!-- /editorArea -->
                        <div class="clearfix"></div>

                    </div>
                    <!-- editorBottomBar -->
                    <div id="editorBottomBar" class="well well-small middlegreyB">
                        <div class="pull-left">
                            <form class="form-inline">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="icon-tag"></i></span>
                                    <input type="text" name="slideTag" id="slideTag" maxlength="25" class="input-large" placeholder="New tag">
                                </div>
                            </form>
                        </div>

                        <div class="pull-left level1">
                            <form class="form-inline">
                                <div class="input-prepend">
                                    <span class="add-on"><i class="icon-adjust"></i> Level </span>
                                    <select name="levelCount" class="input-small">
                                        <option value="1">1</option>
                                        <option value="2">2</option>
                                        <option value="3">3</option>
                                    </select>
                                </div>
                            </form>
                        </div>
                        <!--<button class="btn btn-dark" onclick="console.log( 'submitform' );console.log( $('#editorAreaForm').serialize() )">submit form</button>-->

                    </div>
                    <div class="pull-right">

                    </div>

                    <div class="clearfix"></div>
                    <div class="testSlideEditorContainer">
                        <div class="trafficmanagerContainer pull-left">
                            <h3 class="functionHeader darkgreyT">Traffic data</h3>

                            <div class="pull-left trafficDataHolder">
                                <!--<form style="display: block;" class="form-inline" id="trafficForm" action="" method=""></form>
                                  <div class="control-group"><input type="text" placeholder="goto" class="square" id="slidePrev"><i class="icon-chevron-left"></i><input type="text" placeholder="score" class="square" id="slideScore"><i class="icon-chevron-left"></i><input type="text" placeholder="goto" class="square" id="slideNext"></div>-->
                            </div>

                        </div>
                        <div class="toolsContainer span4 pull-right">
                            <h3 class="functionHeader darkgreyT">Test elements</h3>

                            <div class="toolsContainerElements pull-left">
                                <input type="hidden" id="tmpType" value=""/>

                                <div class="control-group">
                                    <ul class="tools"></ul>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="description">
                        <h3 class="functionHeader darkgreyT">Description</h3>
                        <textarea id="descArea" style="width:100%;height:100px"></textarea>
                    </div>
                </div>
                <!-- /editorBottomBar -->
            </div>
            <!-- /slideEditor -->
        </div>
    <!-- MIDDLE SIDE -->
    </div>
</section>

<?
/*

<!-- RIGHT SIDE -->
<div class="span2" style="display: none;visibility: hidden">
    <!--<div class=" colHeader"><h3 class="orangeT "><span class="pull-left">Slideshow status</span></div>
    <div class="clearfix"></div>
    <div class="createNew well well-small">
      <div class="dropdown">
        <h3 class="functionHeader middlegreyT2" id="mbArea">
          <div class="pull-right">
            <span class="dropdown-toggle  btn-r" role="button" data-toggle="dropdown" data-target="#" id="slideshowStatusStatusSelector"><span class="label">&nbsp;&nbsp;</span><span class="name">Draft</span><b class="caret"></b></span>
                      <ul class="dropdown-menu" id="slideshowStatus">
                      <li><a href="#"><span class="label">&nbsp;&nbsp;</span><span class="name">Draft</span></a></li>
                      <li><a href="#"><span class="label label-warning">&nbsp;&nbsp;</span><span class="name">Review needed</span></a></li>
                      <li><a href="#"><span class="label label-success">&nbsp;&nbsp;</span><span class="name">Can public</span></a></li>
                      </ul>
          </div>
        </h3>
      </div>
    </div>-->
    <div id="accordionRight">
        <div class=" colHeader"><h3 class="orangeT "><span class="pull-left"><?=$accordion0_Header;?></span>
                <!--<span class="pull-right functionHeader" id="addFilesEditor"><?=$accordion0_addmedia_Button;?></span>-->
            </h3></div>
        <!--accordion content 1 -->
        <div id="accordionContent" style="dispaly:block;">
            <div class="createNew well well-small" id="themeRow" style="display:block;">
                <div class="dropdown">
                    <h3 class="functionHeader middlegreyT2" id="mbArea"><span>folder:</span>
                        <!--<button class="btn btn-dark btn-r" data-dropdown="#mediaBoxList" data-horizontal-offset="-20" data-vertical-offset="50" id="selectMediaBox">mediabox<span class="caret"></span></button>-->
                        <div class="pull-right">
                            <?
                            if (count($result) > 1)
                            {
                                echo '<span class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#" data-diskarea-id="' . $result[0]['id'] . '" id="daForMediaGroups"><span class="name">' . $result[0]['name'] . '</span><b class="caret"></b></span><ul class="dropdown-menu" id="themeList">';
                                for ($i = 0; $i < count($result); $i++)
                                {
                                    echo '<li class="' . ($i == 0 ? 'selected"' : '') . '"><a href="javascript:void(0)" class="level1" data-diskarea-id="' . $result[$i]['id'] . '">' . $result[$i]['name'] . '</a></li>';
                                }
                                echo '</ul>';
                            } else
                            {
                                echo '<span class="selectMenu btn-dark btn-r" role="button" data-diskarea-id="' . $result[0]['id'] . '" id="daForMediaGroups"><span class="name">' . $result[0]['name'] . '</span></span>';
                            }
                            ?>
                        </div>
                    </h3>
                </div>
            </div>
            <div class="createNew well well-small">
                <div class="dropdown">
                    <h3 class="functionHeader middlegreyT2" id="mbArea"><!--<span class="textBGRightMediabox"></span>
                  <button class="btn btn-dark btn-r" data-dropdown="#mediaBoxList" data-horizontal-offset="-20" data-vertical-offset="50" id="selectMediaBox">mediabox<span class="caret"></span></button>-->
                        <div class="pull-right">
                            <span class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#"><span class="name">select</span><b class="caret"></b></span>
                            <ul class="dropdown-menu" id="mediaBoxList"></ul>
                        </div>
                    </h3>
                </div>
            </div>
            <!-- sortingBar -->
            <div class="createNew well well-small" id="sortingIconBar">
                <div class="btn-group" data-toggle="buttons-radio">
                    <button type="button" class="btn btn-dark active" data-class=""><i class="icon-eye-open"></i>
                    </button>
                    <button type="button" class="btn btn-dark" data-class="image"><i class="icon-camera"></i></button>
                    <button type="button" class="btn btn-dark" data-class="video"><i class="icon-film"></i></button>
                    <button type="button" class="btn btn-dark" data-class="audio"><i class="icon-music"></i></button>
                    <button type="button" class="btn btn-dark" data-class="word excel pdf"><i class="icon-file"></i>
                    </button>
                </div>
            </div>
            <!-- /sortingBar -->
            <!-- selected mediaBox -->
            <div class="sideBar span2">
                <ul class="mediaBox span2 sub greyB" id="editorsMediaBox">

                </ul>
            </div>
            <!-- selected mediaBox -->
        </div>
        <!--accordion content 1 -->

        <div class=" colHeader"><h3 class="orangeT "><span class="pull-left"><?=$accordion2_Header;?></span></h3></div>
        <!--accordion content 2 -->
        <div id="accordionContent" style="dispaly:none;">
            <div id="templateContainer">
                <ul class="slidesList span2" id="testSlides" data-object-type="slidesList">
                    <? include($_SERVER['DOCUMENT_ROOT'] . '/pages/slideeditor/templates.php'); ?>
                </ul>
            </div>
        </div>
        <!--/accordion content 2 -->

        <div class=" colHeader"><h3 class="orangeT "><span class="pull-left"><?=$accordion1_Header;?></span></h3></div>
        <!--accordion content 2 -->
        <div id="accordionContent" style="dispaly:none;">
            <div class="createNew well well-small">
                <div class="dropdown">
                    <h3 class="functionHeader middlegreyT2"><span><?=$accordion1_select_Button;?></span>


                        <div class="pull-right">
                            <span class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#" id="loadSlideShow2Button"><span class="name">select</span><b class="caret"></b></span>
                            <ul class="dropdown-menu" id="loadSlideshow2"></ul>
                        </div>

                    </h3>
                </div>
            </div>
            <div id="slidesList2Container">
                <ul class="slidesList span2" id="slidesList2" data-object-type="slideList"></ul>
            </div>
        </div>
        <!--/accordion content 2 -->
    </div>

</div>
<!-- /RIGHT SIDE -->
*/
?>

<div class="clearfix"></div>
<div class="span2"></div>
<div class="offset2 span10">
<a id="renderLink" target="_blank"><img src="" id="render"></a>
<a id="renderWorkLink" target="_blank"><img src="" id="renderWork"></a>
</div>

<section id="MainContent" class="preview hidden">
    <div class="container">
        <div class="row">
            <div class="span12">
                <div class="colHeader"><h3 class="orangeT">
                        <span class="btn btn-dark exit" id="exitPreview" data-type="preview">Exit preview</span></h3></div>
                <div class="clearfix"></div>
                <iframe id="previewIframe" width="100%" height="700" scrolling="no" frameborder="0" src="" allowFullScreen></iframe>
            </div>
        </div>
        <div class="clearfix"></div>
        <div class="row orchestration hidden">
            <div class="span12">
                <div class="colHeader"><h3 class="orangeT">
                        <span class="btn brn-dark exit" id="exitOrch" data-type="orchestration">Exit orchestration view</span>
                    </h3></div>
                <div class="clearfix"></div>
                <div class="orchBoundary">
                    <div class="orch"></div>
                </div>
            </div>
        </div>
    </div>

</section>
<script type="text/javascript" charset="utf-8" src="/lib/nested.sortable.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/bootstrap-editable.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/spectrum.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/html2canvas2.js"></script>
<script type="text/javascript" charset="utf-8" src="/lib/seFunction_pre.js"></script>
<script type="text/javascript" charset="utf-8">


/*
function screenshot(id,renderId) {
    thiscreen = document.getElementById(id);
    //thiscreen = thiscreen[0];

    var options = options || {};
    options.elements = [thiscreen];

    html2canvas.logging = options && options.logging;
    options.complete = function(images){
        var queue = html2canvas.Parse(thiscreen, images, options),
            canvas = html2canvas.Renderer(queue, options);
        document.getElementById(renderId).src = canvas.toDataURL();
        document.getElementById(renderId + 'Link').href = canvas.toDataURL();
    };

    html2canvas.Preload(thiscreen,  options);
}
*/

$( function () {

    slideEditor.init(subDomain);

} );
</script>
<!-- load/save slideshows and display slides -->

<!-- template: Load SlideShows  -->
<script type="text/x-tmpl" id="tmpl-lsslist">
    {% for (var i=0, lsslist; lsslist=o.result[i]; i++) { %}<li class="{% if (lsslist.readonly == 1) { %}disabled{% } %}" data-name="lsslist.name"><a href="#" data-id="{% if (lsslist.readonly !== 1) { %}{%=lsslist.id%}{% } %}" rel="tooltip" title="{%=lsslist.name%}"><span class="sname">{%= lsslist.name.length > 19 ? lsslist.name.substring(0,19) : lsslist.name %}</span></a></li>{% } %}
</script>

<!-- template: Load SlideShows  -->
<script type="text/x-tmpl" id="tmpl-lsslist1">
    <div class="listHolder">
        <table class="table table-striped table-condensed">
            <caption>Files to delete</caption>
            <thead>
            <tr>
                <th>Name</th>
                <th>Description</th>
                <th>Slides</th>
            </tr>
            </thead>
            <tbody>
            {% for (var i=0, lsslist; lsslist=o.result[i]; i++) { %}
            <tr>
                <td>{%=lsslist.name%}</td>
                <td>{%=lsslist.description%}</td>
                <td><span class="pull-right badge">{%=lsslist.count%}</span></td>
            </tr>
            {% } %}
            </tbody>
        </table>
    </div>
</script>

<!-- template: save slideshow -->
<script type="text/x-tmpl" id="tmpl-saveslideshow">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <form class="form-inline">
        <div class="control-group">
            <div class="controls">
                <input type="text" id="slideshowName" class="input-xlarge" placeholder="Name...." value="{%=file.input%}"/>
            </div>
        </div>
        <div class="control-group hidden">
            <div class="controls">
                <select name="folder">
                    {% for (var j=0, folder; folder=file.folders[j]; j++) { %}
                    <option value="{%=folder.id%}"
                    {% if (folder.id == file.selected) { %}selected="selected"{% } %}>{%=folder.name%}</option>
                    {% } %}
                </select>
            </div>
        </div>
        <div class="control-group">
            <div class="controls">
                <textarea id="slideshowDescription" placeholder="Description..." style="width:90%">{%=file.desc%}</textarea>
            </div>
        </div>
    </form>
    {% } %}
</script>

<!-- template mini slides and traffic datas-->
<script type="text/x-tmpl" id="tmpl-miniSlides">
    {% for (var i=0, slides; slide=o.result[i]; i++) { %}
    <li id="{%=slide.id%}" class="slideElement span2 {% if (slide.type == 'template') { %}templateSlide{% } %}" data-slide-type="{%=slide.type%}"
    {% if (slide.type == 'template') { %} data-template-type="{%=slide.templateType%}" data-slideLevel="{%=slide.slideLevel%}" {% } %}
    data-bg="{%=slide.background%}">
    <div class="dataHolder">

        <div class="pointer-right"></div>

        <div class="descriptionTag">{%=slide.description%}</div>
        <div class="rightSide {% if (slide.error) { %}redB{% } %}" {% if (slide.error) { %}title="{%=slide.error%}"{% } %}>
            <span class="name">{%=slide.tag%}</span>
            <span class="badge nr">{%=slide.badge%}</span>
        {% if (slide.templateOption) { %}
        <div class="trafficDetail"><i class="icon-eye-close opener"></i></div>
        {% } %}
    </div>
    <div class="leftSide" style="background:{% if (clearNULL(slide.background) == '') { %}white;{% } else { %}{%=slide.background%}{% } %}">{%=slide.html%}</div>
    </div>
    {% if (slide.templateOption) { %}
    <div class="trafficData span2">
        <div class="pointer-bottom2"></div>
        <div class="pointer-bottom1"></div>
        {% for (var j=0, slides2; tmpO=slide.templateOption[j]; j++) { %}
        <div class="whiteLine">
            <span class="badge prev" data-slide-id="{%=tmpO.prevID%}">{%=tmpO.prev%}</span>
            <i class="icon-chevron-left"></i>
            <span class="badge score">{%=tmpO.score%}</span>
            <i class="icon-chevron-left"></i>
            <span class="badge next" data-slide-id="{%=tmpO.nextID%}">{%=tmpO.next%}</span>
        </div>
        {% } %}
    </div>
    {% } %}
    </li>
    {% } %}
</script>
<!-- template mini slides and traffic datas-->
<script type="text/x-tmpl" id="tmpl-miniSlides2">
    {% for (var i=0, slides; slide=o.result[i]; i++) { %}
    <li id="{%=slide.id%}" class="slideElement span2 {% if (slide.templateType) { %} templateSlide {% } %}"
        data-slide-type="{%=slide.type%}"
    {% if (slide.templateType) { %}
    data-template-type="{%=slide.templateType%}"
    data-slideLevel="{%=slide.slideLevel%}" {% } %}
    data-editable="{%=slide.editable%}">
    <div class="dataHolder">

        <div class="pointer-right"></div>

        <div class="descriptionTag">{%=slide.description%}</div>
        <div class="rightSide">
            <span class="name">{%=slide.tag%}</span>
            <span class="badge nr">{%=slide.badge%}</span>
            {% if (slide.templateOption) { %}
            <div class="trafficDetail"><i class="icon-eye-close opener"></i></div>
            {% } %}
        </div>
        <div class="leftSide">
            {%=slide.html%}
        </div>
    </div>
    {% if (slide.templateOption) { %}
    <div class="trafficData span2">
        <div class="pointer-bottom2"></div>
        <div class="pointer-bottom1"></div>
        {% for (var j=0, slides2; tmpO=slide.templateOption[j]; j++) { %}
        <div class="whiteLine">
            <span class="badge prev" data-slide-id="{%=tmpO.prevID%}">{%=tmpO.prev%}</span>
            <i class="icon-chevron-left"></i>
            <span class="badge score">{%=tmpO.score%}</span>
            <i class="icon-chevron-left"></i>
            <span class="badge next" data-slide-id="{%=tmpO.nextID%}">{%=tmpO.next%}</span>
        </div>
        {% } %}
    </div>
    {% } %}
    </li>
    {% } %}
</script>
<!-- template: display traffic data on bottombar -->
<script type="text/x-tmpl" id="tmpl-trafficdata">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <div class="control-group"><input type="text" placeholder="goto" class="square" id="slidePrev" value="{%=file.prev%}"><i class="icon-chevron-left"></i><input type="text" placeholder="score" class="square" id="slideScore" value="{%=file.score%}"><i class="icon-chevron-left"></i><input type="text" placeholder="goto" class="square" id="slideNext" value="{%=file.next%}"></div>{% } %}
</script>

<!-- template: display update traffic data on slider -->
<script type="text/x-tmpl" id="tmpl-slidetrafficdata">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <div class="whiteLine"><span data-slide-id="{%=file.previd%}" class="badge prev">{%=file.prev%}</span><i class="icon-chevron-left"></i><span class="badge score">{%=file.score%}</span><i class="icon-chevron-left"></i><span data-slide-id="{%=file.nextid%}" class="badge next">{%=file.next%}</span></div>{% } %}
</script>

<!-- template: list madiaboxes only -->
<script type="text/x-tmpl" id="tmpl-mblist">
    {% for (var i=0, mblist; mblist=o.result[i]; i++) { %}
    <li><a href="javascript:void(0)" class="level2" data-id="{%=mblist.id%}"><span class="name">{%=mblist.name%}</span><span class="pull-right badge">{%=mblist.badge%}</span></a></li>{% } %}
</script>

<!-- template: diskareas and mediaboxes -->
<script type="text/x-tmpl" id="tmpl-diskareas">
    {% for (var i=1, disk; disk=o.result[i]; i++) { %}
    <li><a href="javascript:void(0)" class="level1" data-diskarea-id="{%=disk.id%}"><b>{%=disk.name%}</b></a>
        <ul class="sub" id="{%=disk.id%}">
            <li><a href="javascript:void(0)" class="level2" data-id="all"><i>All files</i></a></li>
            {% for (var j=0, box; box=disk.boxes[j]; j++) { %}
            <li><a href="javascript:void(0)" class="level2" data-id="{%=box.id%}">{%=box.name%}</a></li>
            {% } %}
        </ul>
    </li>
    {% } %}
</script>

<!-- template: diskareas and mediaboxes II. (default only) -->
<script type="text/x-tmpl" id="tmpl-diskareas2">
    {% for (var i=1, disk; disk=o.result[i]; i++) { %}
    <li class="subs"><a href="#" data-id="all"><b>All files</b></a></li>
    {% for (var j=0, box; box=disk.boxes[j]; j++) { %}
    <li class="subs"><a href="#" data-id="{%=box.id%}">{%=box.name%}</a></li>
    {% } %}
    {% } %}
</script>

<!-- load mediabox mediaelements -->

<!-- template: display myMediaFiles -->
<script type="text/x-tmpl" id="tmpl-mediaElement">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <li class="mediaElement span2 lightgreyB {%=file.type%} isotope-item"
        isotope-data-category="{%=file.type%}"
        isotope-data-name="{%=file.name%}"
        isotop-data-uploaded="{%=file.uploaded_ts%}"
        data-mediaurl="{% if (file.mediatype == 'remote') { %}{%=file.mediaurl%}{% } else { %}<?= connectionType() . $imageURL; ?>{%=file.mediaurl%}{% } %}"
        data-mediatype="{%=file.mediatype%}"
        data-video-width="{%=file.videoWidth%}"
        data-video-height="{%=file.videoHeight%}"
    {% if(file.type == 'video') { %}
    data-poster="{% if (file.mediatype == 'remote') { %}{%=file.thumbnail_url%}{% } else { %}<?=connectionType() . $imageURL;?>{%=file.thumbnail_url%}{% } %}"
    {% } %}
    data-id="{%=file.id%}">
    <div class="colorBar {%=file.type%}"></div>
    <div class="thumbnail">
        <img src="{% if (file.mediatype == 'remote') { %}{%=file.thumbnail_url%}{% } else { %}<?= connectionType() . $imageURL; ?>{%=file.thumbnail_url%}{% } %}" alt=""/>
    </div>
    <div class="caption">
        <p><span class="name">{% if(file.name){ %}{%=file.name.substring(0,16)%}{% } %}</span></p>
        <p><span class="uploaded">{%=file.uploaded%}</span></p>
    </div>
    </li>
    {% } %}
</script>

<!-- templates for dropped mediaelements -->

<!-- template: display image on ea -->
<script type="text/x-tmpl" id="tmpl-eaImage">
    {% for (var i=0, file; file=o.result[i]; i++) { %}<div class="ResizableClass image isSelected slideItem" style="left: {%=file.left%}; top: {%=file.top%}; width: {%=file.width%}; height: {%=file.height%}; position: absolute;" data-item-type="Image"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><img src="{%=file.mediaurl%}" style="width: 100%; max-height:100%"></div>{% } %}
</script>


<!-- template: display local video on ea -->
<script type="text/x-tmpl" id="tmpl-eaLVideo">
    {% for (var i=0, file; file=o.result[i]; i++) { %}<div class="ResizableClass video isSelected slideItem" style="left: {%=file.left%}; top: {%=file.top%}; width: {%=file.width%}; height: {%=file.height%}; position: absolute;" data-item-type="lVideo"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><img src="{%=file.poster%}" class="poster"/><iframe src="<?= $videodomain; ?>videoloader/{%=file.fname%}.mp4-{%=file.w%}-{%=file.h%}/" style="width: 100%; height: 100%;" frameborder="0" border="0" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreeniframe></iframe></div>{% } %}
</script>

<!-- template: display remote video on ea -->
<script type="text/x-tmpl" id="tmpl-eaRVideo">
    {% for (var i=0, file; file=o.result[i]; i++) { %}<div class="ResizableClass video isSelected slideItem" style="left: {%=file.left%}; top: {%=file.top%}; width: {%=file.width%}; height: {%=file.height%}; position: absolute;" data-item-type="rVideo"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><img src="{%=file.poster%}" class="poster"/><iframe width="100%" height="100%" src="{%=file.mediaurl%}" frameborder="0" allowfullscreen=""></iframe></div>{% } %}
</script>

<!-- template: display local audio on ea -->
<script type="text/x-tmpl" id="tmpl-eaLAudio">
    {% for (var i=0, file; file=o.result[i]; i++) { %}
    <div class="nonResizableClass audio isSelected slideItem" style="left: {%=file.left%}; top: {%=file.top%}; width: {%=file.width%}; height: {%=file.height%}; position: absolute;" data-item-type="Audio">
        <div class="movingBox"><i class="icon-move"></i></div>
        <div class="deleteBox"><i class="icon-remove"></i></div>
        <audio controls="controls">
            <source src="{%=file.mediaurl%}.mp3" type="audio/mpeg">
            <source src="{%=file.mediaurl%}{%=file.mediaurl%}.wav" type="audio/wave">
        </audio>
    </div>
    {% } %}
</script>

<!-- template: display text on ea -->
<script type="text/x-tmpl" id="tmpl-eatext">
    {% for (var i=0, file; file=o.result[i]; i++) { %}<div class="textClass ResizableClass isSelected slideItem" style="left: {%=file.left%}; top: {%=file.top%}; width: {%=file.width%}; height:4%;position:absolute;" data-item-type="Text"><div class="movingBox"><i class="icon-move"></i></div><div class="deleteBox"><i class="icon-remove"></i></div><div id="myInstance_{%=file.instanceID%}" class="textDiv" contenteditable="true" onpaste="handlepaste(this, event)" style="position: relative;">{% if (file.text) { %}{%=file.text%}{% } %}</div></div>{% } %}
</script>
<!--onpaste="handlepaste(this, event)" -->

<!-- template mini slides and traffic datas-->
<script type="text/x-tmpl" id="tmpl-orchSlides">
    {% for (var i=0, slides; slide=o.result[i]; i++) { %}
    <div id="{%=slide.id%}" class="slideElement span2 {% if (slide.templateType) { %} templateSlide {% } %}" data-slide-type="{%=slide.type%}"
    {% if (slide.templateType) { %} data-template-type="{%=slide.templateType%}" data-slideLevel="{%=slide.slideLevel%}" {% } %}>
    <div class="dataHolder">
        <div class="leftSide">
            {%=slide.html%}
        </div>
        <div class="rightSide {% if (slide.error) { %}redB{% } %}"
        {% if (slide.error) { %}title="{%=slide.error%}"{% } %}>
        <span class="name">{%=slide.tag%}</span>
        <span class="badge nr">{%=slide.badge%}</span>
    </div>
    </div>

    </div>
    {% } %}
</script>