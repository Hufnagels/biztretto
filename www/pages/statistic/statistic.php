<?php

    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');

    $protocol = connectionType();
    $previewTraining = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/preview/';

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

    $span2Header0 = 'trainings';
    $span2Header1 = 'traininggroups';
    $span2Header2 = 'users';
    $spanMiddle0Header = 'Statistic by trainings';
    $selectInstanceText = 'Select training ';
    $spanMiddle1Header = 'Training slideshows';
    $spanMiddle2Header = 'Training instances';
    $accordion0_Header = 'training groups';


    $formdata['label'] = array('Training start', 'Training end', 'Workday', '', 'Weekend', '', 'Test', 'Test type', 'Startup code', 'Repeatable', 'Repeat', 'Credit');
    $formdata['pholder'] = array('2012.01.01', '2012.01.02', '18:00', '18:00', '18:00', '18:00', '', '', 'something...', '', 'Repeat....', '160');
    $formdata['id'] = array('startDate', 'endDate', 'timeout1', 'timeout2', 'wtimeout1', 'wtimeout2', 'type', 'testtype', 'startupCode', 'repetable', 'Repeat', 'credit');


?>
<form id="statform" action="#" method="POST" class="hidden1">
    <input type="hidden" name="id" value=""/>
    <input type="hidden" name="diskArea_id" value="<?= $result[0]['diskArea_id']; ?>"/>
    <input type="hidden" name="office_id" value="<?= $_SESSION['office_id'] ?>"/>
    <input type="hidden" name="office_nametag" value="<?= $_SESSION['office_nametag'] ?>"/>
    <input type="hidden" name="owner" value="<?= $_SESSION['u_id'] ?>"/>
</form>
    <section id="inner-headline">
        <div class="container">
            <div class="row">
                <div class="span5">
                    <div class="inner-heading">
                        <h2 id="linkData"><?=$spanMiddle0Header;?></h2>
                    </div>
                </div>
                <div class="span7">

                    <!-- ISOTOPE -->
                    <div class="pull-right" id="sortingBar">
                        <span class="pull-right hiddenClass" id="bc">
                            <span class="functionHeader">
                                <span class="dropdown">
                                    <button class="dropdown-toggle btn-r btn-dark" role="button" data-toggle="dropdown" id="instanceSelector" data-href="#"><?=$selectInstanceText;?><span class="caret"></span></button>
                                    <ul class="dropdown-menu" id="instanceList"></ul>
                                </span>
                            </span>
                        </span>
                    </div>
                    <!-- /ISOTOPE -->
                </div>
            </div>
    </section>
<section id="MainContent">
    <div class="container">
        <div class="row special">
            <!-- LEFT SIDE -->
            <div class="span2">
                <div class="affix-top" id="myStatisticContainer"><!-- mySlidesContainer -->
                    <div class="accordion" id="accordionContent2">
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionContent2" href="#collapse11"><i class="icon-minus"></i><?=$span2Header0;?></a>
                            </div>
                            <div id="collapse11" class="accordion-body collapse in">
                                <div class="accordion-inner">
                                    <!-- trainings -->
                                    <div class="clearfix"></div>
                                    <div class="createNew well well-small" id="themeRow" style="display:none;">
                                        <div class="dropdown">
                                            <h3 class="functionHeader middlegreyT2" id="mbArea"><span>folder:</span>
                                                <div class="pull-right">
                                                    <?
                                                    if (count($result) > 1) {
                                                        echo '<span class="dropdown-toggle selectMenu btn-dark btn-r" role="button" data-toggle="dropdown" data-target="#" data-diskarea-id="' . $result[0]['diskArea_id'] . '" id="daForTrainings"><span class="name">' . $result[0]['name'] . '</span><b class="caret"></b></span><ul class="dropdown-menu" id="themeList">';
                                                        for ($i = 0; $i < count($result); $i++) {
                                                            echo '<li class="' . ($i == 0 ? 'selected"' : '') . '"><a href="javascript:void(0)" class="level1" data-diskarea-id="' . $result[$i]['diskArea_id'] . '" >' . $result[$i]['name'] . '</a></li>';
                                                        }
                                                        echo '</ul>';
                                                    } else {
                                                        echo '<span class="selectMenu btn-dark btn-r" role="button" data-diskarea-id="' . $result[0]['diskArea_id'] . '" id="daForTrainings"><span class="name">' . $result[0]['name'] . '</span></span>';
                                                    }
                                                    ?>
                                                </div>
                                            </h3>
                                        </div>
                                    </div>
                                    <div class="clearfix"></div>
                                    <!-- trainings-->
                                    <div class="trainingHolder">
                                        <ul class="slidesList" id="trainingList" style="">
                                            <?
                                            if ($rows = MySQL::resultArray(MySQL::executeQuery('SELECT training_id AS id, title, cover, authors, activeState AS state FROM training_training WHERE office_id = "'.MySQL::filter($_SESSION['office_id']).'" AND office_nametag = "'.MySQL::filter($_SESSION['office_nametag']).'" AND parent_id = 0 AND activeState = "ready" ORDER BY title'),MySQL::fmAssoc))
                                                foreach($rows as $data){
                                                    echo '<li id="'.$data['id'].'" class="publicationElement  trainingslide span2 '.$data['state'].'"><div class="colorBar '.$data['state'].'"></div><div class="dataHolder"><div class="pointer-right"></div><div class="leftSide">';
                                                    if ($data['cover'] !== null){
                                                        echo'<img src="'.$data['cover'].'"/>';
                                                    }
                                                    echo '</div><div class="rightSide"><span class="name">'.$data['title'].'</span></div></div></li>';
                                                }
                                            ?>
                                        </ul>
                                    </div>
                                    <div class="" style="float:left;display:block;width:100%;height:10px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionContent2" href="#collapse12"><i class="icon-plus"></i><?=$span2Header1;?></a>
                            </div>
                            <div id="collapse12" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="clearfix"></div>
                                    <!-- traininggroups-->
                                    <div class="slidesHolder">
                                        <ul class="mediaBoxList span2" id="GroupList" style="">
                                            <?php
                                            $mediaBoxesArray = array();
                                            if ($groupsWithMembers = UserTrainingGroup::getGroupsWithMembers($_SESSION['office_id'],$_SESSION['office_nametag']))
                                            {
                                                foreach ($groupsWithMembers as $row)
                                                {
                                                    echo '<li class="mediaBox mbcholder span2 "data-object-id="'.$row['groupid'].'" data-object-name="'.$row['doname'].'"><div class="mbHeader"><div class="name">'.$row['name'].'</div><span class="pull-right badge">'.$row['badge'].'</span><div class="pointer-right"></div></div></li>';
                                                    /*$mediaBoxesArray[] = array(

                                                        "id" => $row['groupid'],
                                                        "name" => $row['name'],
                                                        "doname" => $row['doname'],
                                                        "badge" => $row['badge'],
                                                        'members' => $row['members']
                                                    );
                                                    */
                                                }

                                            }

                                            ?>
                                        </ul>
                                    </div>
                                    <!-- traininggroups-->
                                    <div class="" style="float:left;display:block;width:100%;height:10px;"></div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-group">
                            <div class="accordion-heading">
                                <a class="accordion-toggle" data-toggle="collapse" data-parent="#accordionContent2" href="#collapse13"><i class="icon-plus"></i><?=$span2Header2;?></a>
                            </div>
                            <div id="collapse13" class="accordion-body collapse">
                                <div class="accordion-inner">
                                    <div class="clearfix"></div>
                                    <div class="slidesHolder">
                                        <ul class="mediaBoxList span2" id="UsersList" style="">
                                            <?php
                                            $usersArray = array();
                                            $sql = "SELECT u_id, full_name FROM user_u
                                                    WHERE deleted=0 AND userlevel > ".DEMO_USER_LEVEL."
                                                        AND office_id = " . MySQL::filter($_SESSION['office_id']) . "
                                                        AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'
														AND isvisible = 1
                                                    ORDER BY full_name";

                                            $rows = MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc);

                                            $usersArray = array();
                                            $urlRegex = URL_REGEX;

                                            foreach ($rows as &$row) {
                                                $usersArray[] = array(
                                                    'id'         => $row['u_id'],
                                                    'fullname'   => $row['full_name'],
                                                    'department' => !$row['department'] ? '' : UserUserGroup::helperGetGroupNameByGroupId($row['department'])
                                                );
                                            }
                                            foreach ($usersArray as $row)
                                            {
                                                echo '<li class="mediaBox mbcholder span2 "data-object-id="'.$row['id'].'"><div class="mbHeader"><div class="name">'.$row['fullname'].'</div><div class="pointer-right"></div></div></li>';
                                            }

                                            ?>
                                        </ul>
                                    </div>
                                    <div class="" style="float:left;display:block;height:10px;"></div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
            <!-- /LEFT SIDE -->
            <!-- MIDDLE SIDE -->
            <div class="span10">

                <div class="clearfix"></div>
                <div id="sumStatisticFrame" class="">
                    <div id="statisticHolder" class="well well-small">
                        <div class="ratingDiv span2"></div>
                        <div class="detailDiv span8">
                            <div class="headingData">
                                <ul class="summaryzedData pull-left">
                                    <li class="totalUsers1 pull-left">
                                        <h3 class="functionHeader middlegreyT2">Avarage exam result</h3>
                                        <h1 class="functionHeader orangeT">0</h1>
                                    </li>
                                    <li class="finishedTraining pull-left">
                                        <h3 class="functionHeader middlegreyT2"></h3>
                                        <ul>
                                            <li><h3 class="functionHeader middlegreyT2"><span>Attachement: </span></h3></li>
                                            <li><h3 class="functionHeader middlegreyT2"><span>Last modified: </span></h3></li>
                                            <li><h3 class="functionHeader middlegreyT2"><span>Created: </span></h3></li>
                                        </ul>
                                    </li>
                                </ul>
                            </div>
                            <div class="clearfix"></div>
                            <div class="detailedData">
                                <div class="resultsData pull-left">

                                    <h3 class="functionHeader middlegreyT2">result</h3>
                                    <div class="clearfix"></div>
                                    <ul class="pointSelector">
                                        <li class="separator"><div class="text">0</div><br><a href="#" class="btn-dark pole" data-pole="1">1</a></li>
                                        <li class="separator"><div class="text">0</div><br><a href="#" class="btn-dark pole" data-pole="2">2</a></li>
                                        <li class="separator"><div class="text">0</div><br><a href="#" class="btn-dark pole" data-pole="3">3</a></li>
                                        <li class="separator"><div class="text">0</div><br><a href="#" class="btn-dark pole" data-pole="4">4</a></li>
                                        <li class="separator"><div class="text">0</div><br><a href="#" class="btn-dark pole" data-pole="5">5</a></li>
                                    </ul>

                                </div>
                                <div class="resultsData2 pull-left span6">
                                    <ul class="summaryzedData pull-left">
                                        <li class="totalUsers pull-left">
                                            <h3 class="functionHeader middlegreyT2">users</h3>
                                            <h1 class="functionHeader darkgreyT2">0</h1>
                                        </li>
                                        <li class="inprogressTraining pull-left">
                                            <h3 class="functionHeader middlegreyT2">in progress</h3>
                                            <h1 class="functionHeader darkgreyT2">0</h1>
                                        </li>
                                        <li class="finishedTraining pull-left">
                                            <h3 class="functionHeader middlegreyT2">finished</h3>
                                            <h1 class="functionHeader darkgreyT2">0</h1>
                                        </li>

                                        <li class="successfullExam pull-left">
                                            <h3 class="functionHeader middlegreyT2">success</h3>
                                            <h1 class="functionHeader orangeT">0</h1>
                                        </li>
                                        <li class="failedExam pull-left">
                                            <h3 class="functionHeader middlegreyT2">failed</h3>
                                            <h1 class="functionHeader darkgreyT2">0</h1>
                                        </li>
                                    </ul>
                                </div>


                            </div>
                        </div>
                    </div>
                    <div id="userTable">
                        <div class="myUsersListContainer">
                            <div class="clear"></div>
                            <ul id="myUsersList" class="myUsersList span10"></ul>
                            <div class="clear"></div>
                        </div>
                    </div>
                </div>
                <div id="instanceStatisticFrame" class="hiddenClass"></div>
            </div>
            <!-- /RIGHT SIDE -->
        </div>
    </div>
</section>


<script type="text/javascript" charset="utf-8" src="/lib/statFunction.js"></script>

<script type="text/javascript" charset="utf-8">
    $(function () {
        Statistic.init();
    });
</script>

<?php

include ($_SERVER['DOCUMENT_ROOT'] . '/templates/statistic/miniTrainings.tmpl');
include ($_SERVER['DOCUMENT_ROOT'] . '/templates/statistic/statpanel.tmpl');
include ($_SERVER['DOCUMENT_ROOT'] . '/templates/statistic/usertable.tmpl');
?>