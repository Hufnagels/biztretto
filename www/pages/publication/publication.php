<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');

$protocol = connectionType();
$previewTraining = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/preview/';
$publicTrainingUrl = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/public/';
$imageURL = $protocol . $_SESSION['office_nametag'].'.'.DOMAINTAG.'/media/';
$selectArray = array(
    'table' => 'media_diskarea',
    'fields' => 'diskArea_id, name',
    'condition' => array('office_nametag' => $_SESSION['office_nametag']),
    'conditionExtra' => " NOT name = 'corporate'",
    'order' => '1',
    'limit' => 50
);
$result = MySQL::select($selectArray);
$diskAreaJson = json_encode($result, true);
//printR($result);
//printR($_SESSION);
//select possible authors from db
//u_id AS id,
$sql = "
    SELECT u_id AS id, full_name AS name
    FROM user_u
    WHERE office_id = ".$_SESSION['office_id']." AND office_nametag ='".$_SESSION['office_nametag']."' AND userlevel in (5,7)";
$result1 = MySQL::query($sql,false, false);

$authors = array();
foreach($result1 as $row)
    $authors[] = $row['name'];
/*
//x-editable select2
$authorsJson = '["'.implode('","',$authors).'"]';//json_encode($result1, true);
//['html', 'javascript', 'css', 'ajax']
//printR($authorsJson);
*/
//author select
foreach ($result1 as $row){
    $authorsHtml .= "<option value='".$row['id']."'>".$row['name']."</options>";
}
//printR($authorsHtml);

$span2Header = 'slideshows';
$spanMiddle0Header = 'Training details';
$spanMiddle1Header = 'Training slideshows';
$spanMiddle2Header = 'Training attachments';
$spanMiddle3Header = 'Training instances';
$accordion0_Header = 'training groups';


$formdata['label'] = array('Training start', 'Training end', 'Workday', '', 'Weekend', '', 'Test', 'Test type', 'Startup code', 'Repeatable', 'Repeat', 'Credit');
$formdata['pholder'] = array('2012.01.01', '2012.01.02', '18:00', '18:00', '18:00', '18:00', '', '', 'something...', '', 'Repeat....', '160');
$formdata['id'] = array('startDate', 'endDate', 'timeout1', 'timeout2', 'wtimeout1', 'wtimeout2', 'type', 'testtype', 'startupCode', 'repetable', 'Repeat', 'credit');


?>
<input id="upload" type="file" class="hidden" accept="image/gif, image/jpeg, image/png"/>

<input type="file" id="files" name="files[]" class="hidden" multiple/>
<form id="fileupload" action="" method="POST" enctype="multipart/form-data" style="margin-left:-10000px;display:none;">
    <span class="btn btn-success fileinput-button"><span>Add files</span><input type="file" name="files[]" multiple></span>
    <button type="submit" class="btn btn-primary start"><span>Start upload</span></button>
</form>
<section id="inner-headline">
    <div class="container">
        <div class="row">
            <div class="span3">
                <div class="inner-heading">
                    <h2 id="linkData">Publication</h2>
                </div>
            </div>
            <div class="span9">
                <div class="pull-right" id="sortingBar">
                    <!--<span class="pull-left" data-type="details"><?=$spanMiddle0Header;?></span>--><span class="pull-left" id="bc"></span>
					
                    <button class="btn btn-r btn-dark" id="saveAll">Save training</button>
					<button class="btn btn-r btn-dark" id="createNewTrainng">Create new</button>
                </div>

            </div>
        </div>
</section>
<section id="content">
    <div class="container">
        <div class="row special">
            <!-- LEFT SIDE -->
            <div class="span2">
                <div class="affix-top" id="mySlideshowsContainer"><!-- mySlidesContainer -->
                    <div id="accordionLeft">
                        <div class="accordion" id="accordionContent">
                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle active" data-toggle="collapse" data-parent="#accordionContent" href="#collapse1"><i></i>trainings</a>
                                </div>
                                <div id="collapse1" class="accordion-body collapse in">
                                    <div class="accordion-inner">
                                        <!-- slideshowHolder -->
                                        <div class="trainingHolder">
                                            <ul class="slidesList" id="trainingList" style="">
                                                <?
                                                if ($rows = MySQL::resultArray(MySQL::executeQuery('SELECT training_id AS id, title, cover, authors, activeState AS state FROM training_training WHERE office_id = "' . MySQL::filter($_SESSION['office_id']) . '" AND office_nametag = "' . MySQL::filter($_SESSION['office_nametag']) . '" AND parent_id = 0 ORDER BY title'), MySQL::fmAssoc))
                                                    foreach ($rows as $data)
                                                    {
                                                        echo '<li id="' . $data['id'] . '" class="publicationElement trainingslide span2 ' . $data['state'] . '"><div class="colorBar ' . $data['state'] . '"></div><div class="dataHolder"><div class="pointer-right"></div><div class="leftSide">';
                                                        if ($data['cover'] !== null)
                                                        {
                                                            echo'<img src="' . $data['cover'] . '"/>';
                                                        }
                                                        echo '</div><div class="rightSide"><span class="name">' . $data['title'] . '</span><span class="colorTr ' . $data['state'] . '"></span></div></div></li>';
                                                    }
                                                ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionContent" href="#collapse2"><i></i>slideshows</a>
                                </div>
                                <div id="collapse2" class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <!-- slideshowHolder -->
                                        <div class="slidesHolder">
                                            <ul class="slidesList" id="slideShowList" style="">

                                                <?
                                                $sqlSS = "SELECT *
                                            FROM slide_slideshow
                                            WHERE diskArea_id = 1
                                                AND office_id = ".$_SESSION['office_id']."
                                                AND office_nametag ='".$_SESSION['office_nametag']."'
                                                ORDER BY name";

                                                $resultSS = MySQL::query($sqlSS,false,false);
                                                if (!empty($resultSS)) {

                                                    foreach ($resultSS as $row) {
                                                        $cover = (strlen(str_replace(' ', '', $row['cover'])) == 0 ? '' : $row['cover']);
                                                        echo'<li id="'.$row['slideshow_id'].'" class="slideElement slideshowslide span2" data-name="'.$row['name'].'" data-description="'.$row['description'].'" data-cover="'.$row['cover'].'"><div class="dataHolder"><div class="leftSide">';
                                                        if ($cover !== ''){
                                                            echo'<img src="'.$cover.'"/>';
                                                        }
                                                        echo'</div><div class="rightSide"><span class="name">'.$row['name'].'</span></div></div></li>';
                                                    };
                                                }
                                                ?>
                                            </ul>
                                        </div>
                                        <div class="" style="float:left;display:block;height:10px;"></div>

                                        <!-- /slideshowHolder -->
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-group">
                                <div class="accordion-heading">
                                    <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionContent" href="#collapse3"><i></i>mymedia</a>
                                </div>
                                <div id="collapse3" class="accordion-body collapse">
                                    <div class="accordion-inner">
                                        <div class="createNew well well-small">
                                            <div class="dropdown">
                                                <h3 class="functionHeader" id="mbArea">
                                                    <div class="pull-right">
                                                        <button class="dropdown-toggle selectMenu btn-dark btn" role="button" data-toggle="dropdown" data-target="#" ><span class="name">select</span><b class="caret"></b></button>
                                                        <ul class="dropdown-menu" id="mediaBoxList">
                                                            <?

                                                            $mediaBoxesArray = array();
                                                            $i = 0;

                                                            //egy diskarea-hoz tartozo mediabox lista legyujtesre,
                                                            //valamint hogy összesen, illetve egyenkent mennyi file van
                                                            //all files
                                                            $sql2 = "SELECT COUNT(mymedia_id) AS badge FROM media_mymedia
                                                            WHERE office_id = " . MySQL::filter($_SESSION['office_id']) . "
                                                                AND diskArea_id = '" . MySQL::filter($result[0]['diskArea_id']) . "'
                                                                AND mediatype = 'local'";

                                                            $query2 = MySQL::query($sql2, false, false);

                                                            $mediaBoxesArray[0] = array(
                                                                "id" => 'all',
                                                                "name" => 'All files',
                                                                "doname" => 'all files',
                                                                "badge" => $query2[0]['badge']
                                                            );

                                                            echo '<li> <a href="javascript:void(0)" class="level2" data-id="'.$mediaBoxesArray[0]['id'].'"><span class="name">'.$mediaBoxesArray[0]['name'].'</span></a></li>';

                                                            //egy diskarea-hoz tartozo mediabox lista legyujtesre,
                                                            //valamint hogy összesen, illetve egyenkent mennyi file van
                                                            $sql = "SELECT box.mediabox_id AS boxid, box.name AS name, COUNT(media.mymedia_id) AS darab
                                                        FROM media_mediabox box
                                                        LEFT JOIN media_mymedia media
                                                        ON box.mediabox_id = media.mediabox_id
                                                        WHERE box.office_id = " . MySQL::filter($_SESSION['office_id']) . "
                                                            AND media.office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'
                                                            AND media.diskArea_id = " . MySQL::filter($result[0]['diskArea_id']) . "
                                                            AND media.mediatype = 'local'
                                                            GROUP BY media.mediabox_id
                                                            ORDER BY media.name ASC";

                                                            $query = MySQL::query($sql, false, false);
                                                            $db = count($query);

                                                            //mediaboxes and files count
                                                            for ($j = 0; $j < $db; $j++) {
                                                                $nameTag = $query[$j]['name'];
                                                                $idTag = $query[$j]['boxid'];
                                                                $badge = $query[$j]['darab'];
                                                                $doname = str_replace(' ', '', strtolower($nameTag));
                                                                $mediaBoxesArray[$j + 1] = array(
                                                                    "id" => $idTag,
                                                                    "name" => $nameTag,
                                                                    "doname" => $doname,
                                                                    "badge" => $badge
                                                                );
                                                                echo '<li> <a href="javascript:void(0)" class="level2" data-id="'.$mediaBoxesArray[$j+1]['id'].'"><span class="name">'.$mediaBoxesArray[$j+1]['name'].'</span></a></li>';
                                                            }
                                                            ?>
                                                        </ul>
                                                    </div>
                                                </h3>
                                            </div>
                                        </div>
                                        <!-- sortingBar -->
                                        <div class="createNew well well-small" id="sortingIconBar">
                                            <div class="btn-group" data-toggle="buttons-radio">
                                                <button type="button" class="btn btn-dark active" data-class=""><i class="icon-eye-open"></i></button>
                                                <button type="button" class="btn btn-dark" data-class="image"><i class="icon-camera"></i></button>
                                                <button type="button" class="btn btn-dark" data-class="video"><i class="icon-film"></i></button>
                                                <button type="button" class="btn btn-dark" data-class="audio"><i class="icon-music"></i></button>
                                                <button type="button" class="btn btn-dark" data-class="word excel pdf"><i class="icon-file"></i></button>
                                            </div>
                                        </div>
                                        <!-- /sortingBar -->
                                        <!-- selected mediaBox -->
                                        <div class="sideBar span2">
                                            <ul class="mediaBox span2 sub greyB" id="editorsMediaBox"></ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        </div>


                            <div class="createNew well well-small" id="themeRow" style="display:none;">
                                <div class="dropdown">
                                    <h3 class="functionHeader middlegreyT2" id="mbArea"><span>folder:</span>
                                        <!--<button class="btn btn-dark btn-r" data-dropdown="#mediaBoxList" data-horizontal-offset="-20" data-vertical-offset="50" id="selectMediaBox">mediabox<span class="caret"></span></button>-->
                                        <div class="pull-right">
                                            <?
                                            if (count($result) > 1)
                                            {
                                                echo '<span class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#" data-diskarea-id="' . $result[0]['diskArea_id'] . '" id="daForTrainings"><span class="name">' . $result[0]['name'] . '</span><b class="caret"></b></span><ul class="dropdown-menu" id="themeList">';
                                                for ($i = 0; $i < count($result); $i++)
                                                {
                                                    echo '<li class="' . ($i == 0 ? 'selected"' : '') . '"><a href="javascript:void(0)" class="level1" data-diskarea-id="' . $result[$i]['diskArea_id'] . '" >' . $result[$i]['name'] . '</a></li>';
                                                }
                                                echo '</ul>';
                                            } else
                                            {
                                                echo '<span class="selectMenu btn-dark btn-r" role="button" data-diskarea-id="' . $result[0]['diskArea_id'] . '" id="daForTrainings"><span class="name">' . $result[0]['name'] . '</span></span>';
                                            }
                                            ?>
                                        </div>
                                    </h3>
                                </div>
                            </div>



                            <div class="createNew well well-small" id="themeRow" style="display:none;">
                                <div class="dropdown">
                                    <h3 class="functionHeader middlegreyT2" id="mbArea"><span>folder:</span>
                                        <!--<button class="btn btn-dark btn-r" data-dropdown="#mediaBoxList" data-horizontal-offset="-20" data-vertical-offset="50" id="selectMediaBox">mediabox<span class="caret"></span></button>-->
                                        <div class="pull-right">
                                            <?
                                            if (count($result) > 1)
                                            {
                                                echo '<span class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#" data-diskarea-id="' . $result[0]['diskArea_id'] . '" id="daForSlideShow"><span class="name">' . $result[0]['name'] . '</span><b class="caret"></b></span><ul class="dropdown-menu" id="themeList">';
                                                for ($i = 0; $i < count($result); $i++)
                                                {
                                                    echo '<li class="' . ($i == 0 ? 'selected"' : '') . '"><a href="javascript:void(0)" class="level1" data-diskarea-id="' . $result[$i]['diskArea_id'] . '" >' . $result[$i]['name'] . '</a></li>';
                                                }
                                                echo '</ul>';
                                            } else
                                            {
                                                echo '<span class="selectMenu btn-dark btn-r" role="button" data-diskarea-id="' . $result[0]['diskArea_id'] . '" id="daForSlideShow"><span class="name">' . $result[0]['name'] . '</span></span>';
                                            }
                                            ?>
                                        </div>
                                    </h3>
                                </div>
                            </div>


                            <div class="createNew well well-small" id="themeRow" style="display:none;">
                                <div class="dropdown">
                                    <h3 class="functionHeader middlegreyT2" id="mbArea"><span>folder:</span>
                                        <!--<button class="btn btn-dark btn-r" data-dropdown="#mediaBoxList" data-horizontal-offset="-20" data-vertical-offset="50" id="selectMediaBox">mediabox<span class="caret"></span></button>-->
                                        <div class="pull-right">
                                            <?
                                            if (count($result) > 1)
                                            {
                                                echo '<span class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#" data-diskarea-id="' . $result[0]['diskArea_id'] . '" id="daForSlideShow"><span class="name">' . $result[0]['name'] . '</span><b class="caret"></b></span><ul class="dropdown-menu" id="themeList">';
                                                for ($i = 0; $i < count($result); $i++)
                                                {
                                                    echo '<li class="' . ($i == 0 ? 'selected"' : '') . '"><a href="javascript:void(0)" class="level1" data-diskarea-id="' . $result[$i]['diskArea_id'] . '" >' . $result[$i]['name'] . '</a></li>';
                                                }
                                                echo '</ul>';
                                            } else
                                            {
                                                echo '<span class="selectMenu btn-dark btn-r" role="button" data-diskarea-id="' . $result[0]['diskArea_id'] . '" id="daForSlideShow"><span class="name">' . $result[0]['name'] . '</span></span>';
                                            }
                                            ?>
                                        </div>
                                    </h3>
                                </div>
                            </div>


                    </div>
                </div>

            </div>
            <!-- /LEFT SIDE -->

            <!-- MIDDLE SIDE -->
            <div class="span8">


                    <div class="accordion" id="accordionContent2">
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle active" data-toggle="collapse" data-parent="#accordionContent2" href="#collapse11"><i></i>Details</a>
                            </div>
                            <div id="collapse11" class="accordion-body collapse in">
                                <div class="accordion-inner">
                                    <div class="colHeader" id="sortingBar">
                                        <button class="btn btn-l btn-dark" id="deleteTraining">Delete training</button>
                                        <!--<button class="btn btn-r btn-dark" id="createNewTrainng">Create new training</button>-->

                                    </div>
                                    <div class="clearfix"></div>
                                    <form id="trainingsform" action="#" method="POST" class="hidden1">
                                        <input type="hidden" name="id" value=""/>
                                        <input type="hidden" name="diskArea_id" value="<?= $result[0]['diskArea_id']; ?>"/>
                                        <input type="hidden" name="office_id" value="<?= $_SESSION['office_id'] ?>"/>
                                        <input type="hidden" name="office_nametag" value="<?= $_SESSION['office_nametag'] ?>"/>
                                        <input type="hidden" name="owner" value="<?= $_SESSION['u_id'] ?>"/>
                                    </form>
                                    <form id="detailsform" action="#" method="POST" class="hidden1">
                                        <input type="hidden" name="cover" value=""/>
                                        <input type="hidden" name="office_id" value="<?= $_SESSION['office_id'] ?>"/>
                                        <div class="well well-small lightgreyB" id="trainingDataBar">
                                            <div class="span3">
                                                <div class="pull-left"><div class="imageHolder" title="160x224"><img id="coverImg" src="" width="160" height="224" /></div></div>
                                                <div class="pull-right">
                                                    <div class="btn btn-dark" id="uploadCover"><i class="icon-picture icon-white"></i></div>
                                                    <div class="clearfix"></div>
                                                    <div class="btn btn-dark" id="clearCover"><i class="icon-minus icon-white"></i></div>
                                                    <div class="clearfix"></div>
                                                </div>
                                            </div>


                                            <div class="span5">
                                                <div class="control-group group1">
                                                    <label class="control-label" for="title">Title</label>
                                                    <div class="controls">
                                                        <input type="text" name="name" id="name" class="input-xlarge" value=""/>
                                                    </div>
                                                </div>
                                                <div class="control-group group2">
                                                    <label class="control-label" for="title">Author</label>
                                                    <div class="controls">
                                                        <select name="authors" id="authors">
                                                            <?=$authorsHtml;?>
                                                        </select>
                                                    </div>
                                                </div>

                                                <div class="clearfix"></div>
                                            </div>
											
											<div class="span8">
                                                <div class="control-group">
                                                    <label class="control-label" for="title">insertable HTML code</label>
                                                    <div class="controls">
                                                        <textarea id="insertCode" style="width: 95%;height: 65px;"></textarea>
                                                    </div>
                                                </div>

                                            </div>
											
											
                                            <div class="span8">
                                                <div class="control-group">
                                                    <label class="control-label" for="title">Description</label>
                                                    <div class="controls">
                                                        <textarea id="description" name="description"></textarea>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="clearfix"></div>


                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionContent2" href="#collapse12"><i></i>Slideshows</a>
                            </div>
                            <div id="collapse12" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="colHeader" id="sortingBar">
                                        <!-- filters -->
                                        <!-- sortBy -->
                                        <div class="bottomBar">
                                            <h3 class="functionHeader middlegreyT2">

                                                <!--<span class="pull-left"></span>-->
                                                <div class="btn-group">
                                                    <a class="btn dropdown-toggle btn-l btn-dark extra" data-toggle="dropdown" id="previewTraining" data-type="preview" href="#">
                                                        <span>Preview</span>
                                                        <span class="caret"></span>
                                                    </a>
                                                    <ul class="dropdown-menu previewSlideshowList">
                                                        <li><a href="#" data-sid="12">Action</a></li>
                                                        <li><a href="#" data-sid="12">Another action</a></li>
                                                        <li><a href="#" data-sid="12">Something else here</a></li>
                                                    </ul>
                                                </div>


                                            </h3>
                                        </div>
                                    </div>
                                    <div class="myTrainingsContainer">
                                        <ul id="myTrainingsList" class="myUsersList sortable lightgreyB span8"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionContent2" href="#collapse13"><i></i>Attachments</a>
                            </div>
                            <div id="collapse13" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="myMediaListContainer" style="margin-top:5px;">
                                        <ul class="thumbnails" id="myMediaList" data-object-type="myMediaList" style="min-height:220px; height:100%"></ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionContent2" href="#collapse14"><i></i>Assigned traininggroups</a>
                            </div>
                            <div id="collapse14" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="colHeader" id="sortingBar">
                                        <!-- filters -->
                                        <!-- sortBy -->
                                        <div class="bottomBar">
                                            <h3 class="functionHeader middlegreyT2">
                                                <!--<span class="pull-right"></span>-->
                                                <button type="button" class="btn btn-r btn-dark" id="addNewInstance"><span>add new instance</span></button>
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <ul id="myInstances" class="myUsersList span8"></ul>
                                </div>
                            </div>
                        </div>

                    </div>


            </div>
            <!-- /MIDDLE SIDE -->

            <!-- RIGHT SIDE -->
            <div class="span2">
                <div class="affix-top" id="myGroupsContainer"><!-- myUsersContainer -->
                    <div class="accordion-heading"><span class="accordion-toggle">Training status</span></div>
                    <div class="clearfix"></div>
                    <div class="createNew well well-small">
                        <div class="dropdown">
                            <h3 class="functionHeader middlegreyT2" id="mbArea">
                                <div class="pull-right">
                                    <span class="dropdown-toggle  btn-r" role="button" data-toggle="dropdown" data-target="#" id="trainingStatusSelector">
                                        <span class="label label-draft">&nbsp;&nbsp;</span>
                                        <span class="name"> Draft</span><b class="caret"></b>
                                    </span>
                                    <ul class="dropdown-menu" id="trainingStatus">
                                        <li class="selected"><a href="javascript:void(0)" data-status="draft"><span class="label label-draft">&nbsp;&nbsp;</span><span class="name">Draft</span></a></li>
                                        <!--<li><a href'javascript:void(0)" data-status="review"><span class="label label-review">&nbsp;&nbsp;</span><span class="name">to Review</span></a></li>-->
                                        <? if(Access::getAccessLevel() > 5) { ?>
                                        <li><a href="javascript:void(0)" data-status="ready"><span class="label label-ready">&nbsp;&nbsp;</span><span class="name">go Public</span></a></li>
                                        <? } ?>
                                    </ul>
                                </div>
                            </h3>
                        </div>
                    </div>

                    <div class="accordion-heading"><span class="accordion-toggle"><?=$accordion0_Header;?></span></div>

                    <div class="sideBar2 span2">
                        <ul class="mediaBoxList span2" id="GroupList" data-object-type="usersGroupList">
                            <?
                            if ($groupsWithMembers = UserTrainingGroup::getGroupsWithMembers($_SESSION['office_id'],$_SESSION['office_nametag']))
                            {
                                foreach ($groupsWithMembers as $row)
                                {
                                    $mediaBoxesArray[] = array(

                                        "id" => $row['groupid'],
                                        "name" => $row['name'],
                                        "doname" => $row['doname'],
                                        "badge" => $row['badge'],
                                        'members' => $row['members']
                                    );
                                    echo '<li class="mediaBox mbcholder span2';
                                    if ($row['badge'] == 0){ echo ' disabled'; }
                                        echo '" data-object-id="'.$row['groupid'].'" data-object-type="traininggroup" data-object-name="'.$row['doname'].'">
                                        <div class="mbHeader"><div class="name">'.$row['name'].'</div><span class="pull-right badge">'.$row['badge'].'</span><div class="pointer-left"></div></div>
                                        </li>';
                                }
                            }
                            ?>
                        </ul>
                    </div>
                </div>
            </div>
            <!-- /RIGHT SIDE -->
        </div>
    </div>
</section>

<div class="row preview hidden">
    <div class="span12">
        <div class="colHeader"><h3 class="orangeT"><span class="btn brn-dark exit" id="exitPreview" data-type="preview">Exit preview</span></h3></div>
        <div class="clearfix"></div>
        <iframe id="previewIframe" width="100%" height="700" scrolling="no" frameborder="0" src="" allowfullscreen webkitallowfullscreen mozallowfullscreen oallowfullscreen msallowfullscreen></iframe>
    </div>
</div>


<div id="toArrayOutput"></div>

<link href="/css/select2.css" rel="stylesheet">
<link href="/css/select2-bootstrap.css" rel="stylesheet">
<script type="text/javascript" charset="utf-8" src="/lib/nested.sortable.js"></script>

<script type="text/javascript" charset="utf-8" src="/assets/bootstrap/bootstrap-datepicker.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap/bootstrap-timepicker.js"></script>

<!----><script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/bootstrap-editable.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/wysihtml5-0.3.0.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/bootstrap-wysihtml5-0.0.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/wysihtml5.js"></script>
<script type="text/javascript" charset="utf-8" src="/lib/pubFunction.js"></script>

<script type="text/javascript" charset="utf-8">

////////////////////////////////////
$(function () {
    var folderString = $.parseJSON('<?=$diskAreaJson;?>');
    var iframeUrl = '<?=$previewTraining;?>';
	var publicUrl = '<?=$publicTrainingUrl;?>';
    //var authorsString = <?=$authorsJson;?>;
    var diskareaId = <?=$result[0]['diskArea_id'];?>;
    //console.log(authorsString)
    Publication.init(iframeUrl, publicUrl, diskareaId);

});
</script>

<?php
//list trainings on left side
include ($_SERVER['DOCUMENT_ROOT'] . '/templates/publication/miniTrainings.tmpl');
//list slideshows on left side
include ($_SERVER['DOCUMENT_ROOT'] . '/templates/publication/miniSlides.tmpl');
//list mediafiles
include ($_SERVER['DOCUMENT_ROOT'] . '/templates/publication/mediafiles.tmpl');

//load selected training slideshows
include ($_SERVER['DOCUMENT_ROOT'] . '/templates/publication/loadtraining.tmpl');
//load training instances
include ($_SERVER['DOCUMENT_ROOT'] . '/templates/publication/traininginstances.tmpl');

include ($_SERVER['DOCUMENT_ROOT'] . '/templates/publication/trainingslides.tmpl');

//include ($_SERVER['DOCUMENT_ROOT'] . '/templates/publication/userGroupList.tmpl');
include ($_SERVER['DOCUMENT_ROOT'] . '/templates/publication/trainingGroupList.tmpl');

?>

