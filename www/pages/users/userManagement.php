<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');

$protocol = connectionType();
$imageURL = $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/';
$subdomain = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/training/';


//SureRemoveDir(IMGPATH.'/'.$_SESSION['office_nametag'].'/', false);
//SureRemoveDir(IMGPATH.'/'.$_SESSION['office_nametag'].'/thumbnail', false);

//printR($_GET);
//name convention

$span2Header = 'Company';
$span2Headerright = 'selection options';
$span2Headerright2 = 'fast message';
$newDepName = 'create department';
$newTrainingName = ' create group';
$newMBFormInputPlaceholder = ' sales ...';
$newTGFormInputPlaceholder = ' english interm...';
$viewAll = 'Company all';
$notInList = 'Unorganized';

$span8Header = 'users';
$span8Header2 = 'add';
$formdata = array();
//$formdata['label'] = array('Előtag', 'Vezetéknév', 'Keresztnév', 'Neme', 'Email', 'Csoport', 'Pozíció', 'Született', 'Nyelv', 'Iskolák', 'Skills');
$formdata['label'] = array('Prefix', 'Last name', 'First name', 'Gender', 'Email', 'Department', 'Position', 'Born', 'Language', 'Schools', 'Skills');
$formdata['pholder'] = array('DR.', 'Doe', 'John', 'Male', 'name@domain.tld', 'Department', 'director', '1955.01.01', 'english, hungarian', 'highschool', 'skills');
//$formdata['pholder'] = array('DR.', 'Vezetéknév', 'Keresztnév', 'Neme', 'name@domain.tld', 'Csoport', 'Pozíció', '1955.01.01', 'magyar, angol', 'főiskola', 'skills');
$formdata['id'] = array('elotag', 'vezeteknev', 'keresztnev', 'gender', 'email', 'department', 'position', 'birthDate', 'language', 'schools', 'skills');

//require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/class/class.encrypt.php');

?>
<form id="usersform" action="#" method="POST" class="hidden">
    <input type="hidden" name="office_id" value="<?= $_SESSION['office_id'] ?>"/>
    <input type="hidden" name="office_nametag" value="<?= $_SESSION['office_nametag'] ?>"/>
    <input type="hidden" name="owner" value="<?= $_SESSION['u_id'] ?>"/>
</form>

<input id="upload" type="file" class="hidden" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
<form id="fileupload" action="" method="POST" enctype="multipart/form-data" style="margin-left:-10000px;display:none;">
    <span class="btn btn-success fileinput-button"><span>Add files</span><input type="file" name="files[]" multiple></span>
    <button type="submit" class="btn btn-primary start"><span>Start upload</span></button>
</form>
<section id="inner-headline">
    <div class="container">
        <div class="row">
            <div class="span3">
                <div class="inner-heading">
                    <h2 id="linkData"><div class="pull-left1" id="bc">Company all</div></h2>
                </div>
            </div>
            <div class="span9">

                <div class="pull-right" id="sortingBar">
                    <!-- filters -->
                    <!-- sortBy -->
                    <div class="pull-left">
                        <h3 class="functionHeader middlegreyT2">
                        <span class="pull-left" id="tsort">sort:
                          <div id="sortBy" class="btn-group" data-toggle="buttons-radio" data-option-key="attr">
                              <button type="button" class="btn btn-dark active" data-option-value="name" data-tsort="include">
                                  <span>name</span></button>
                              <button type="button" class="btn btn-dark " data-option-value="id" data-tsort="include">
                                  <span>registered</span></button>
                              <!--<button type="button" class="btn btn-dark " data-option-value="skills" data-tsort="include"><span>skills</span></button>-->
                          </div>
                          <div id="filterByIA" class="btn-group" data-toggle="">
                              <button type="button" class="btn btn-danger active" data-option-value="inactive" data-tsort="exclude">
                                  <span>in</span></button>
                              <button type="button" class="btn btn-success active" data-option-value="active" data-tsort="exclude">
                                  <span>active</span></button>
                          </div>
                          <div id="sortOrder" class="btn-group" data-toggle="buttons-radio" data-option-key="order">
                              <button type="button" class="btn btn-dark" data-option-value="asc" data-tsort="include">
                                  <span><i class="icon-arrow-up"></i></span></button>
                              <button type="button" class="btn btn-dark active" data-option-value="desc" data-tsort="include">
                                  <span><i class="icon-arrow-down"></i></span></button>
                          </div>
                        </span>
                        <span class="pull-left" style="margin-left: 10px;"><span>word:</span>
                          <div id="wordfilter">
                              <div class="btn-group">
                                  <input type="text" class="input-small" id="filterWord">
                              </div>
                          </div>
                        </span>
                        <span class="pull-left">
                        <div class="dropdown">
                            <button class="dropdown-toggle btn-r btn-dark" data-toggle="dropdown" id="selectUserButton">Select<span class="caret"></span></button>
                            <ul class="dropdown-menu" id="selectionMenu">
                                <li class=""><a href="#" class="selectall">Select all</a></li>
                                <li class=""><a href="#" class="deselectall">Deselect all</a></li>
                                <li class=""><a href="#" class="invertselection">Invert selection</a></li>
                            </ul>
                        </div>
                    </span><span class="pull-left">
                        <div class="dropdown">
                            <button class="dropdown-toggle btn-r btn-dark" data-toggle="dropdown" id="addUserButton" data-href="#">add<span class="caret"></span></button>
                            <ul class="dropdown-menu" id="addUser">
                                <li class="action"><a href="#" data-action="one">One user</a></li>
                                <li class="action"><a href="#" data-action="any">Users from csv file</a></li>
                                <li class=""><a href="<?=SITE_URL;?>users.csv">CSV file to use by upload</a></li>
                            </ul>
                        </div>
                    </span></h3>
                    </div>

            </div>
        </div>
    </div>
</section>
<section id="content">
    <div class="container">
        <div class="row special">
            <!-- LEFT SIDE -->
            <div class="span2">

                <div class="affix-top" id="myusersGroupsContainer"><!-- myusersGroupsContainer -->
                    <div class="accordion-heading"><span class="accordion-toggle"><?=$span2Header;?></span></div>
                    <div class="clearfix"></div>

                    <!-- departments -->
                    <div class="createNew well well-small">
                        <div class="dropdown">
                            <h3 class="functionHeader middlegreyT2">
                                <span></span>
                                <button class="dropdown-toggle btn btn-dark btn-r" data-toggle="dropdown" role="button" data-href="#"><?=$newDepName;?></button>
                                <ul class="dropdown-menu newusersGroup">
                                    <li class="newForm">
                                        <form class="form-vertical" method="post" action="#" id="newDepartmentForm">
                                            <div class="input-append">
                                                <input type="text" class="input-newMB" id="newDepartmentName" placeholder="<?= $newMBFormInputPlaceholder; ?>" maxlength="15">
                                                <span class="add-on">
                                                    <button type="button" class="btn btn-dark" id="addNewDepartment" data-func="departmentAdd">
                                                        <i class="icon-plus"></i></button>
                                                </span>
                                            </div>
                                        </form>
                                    </li>
                                </ul>
                            </h3>
                        </div>
                    </div>
                    <div class="clearfix"></div>
                    <div class="sideBar span2">
                        <ul class="mediaBoxList span2" id="usersGroupList"></ul>
                    </div>
                    <!-- /departments -->
                    <div class="clearfix"></div>

                    <div class="accordion-heading"><span class="accordion-toggle"><?=$span2Headerright;?></span></div>
                    <div class="clearfix"></div>
                    <div class="Options well well-small">
                      <span class="functionHeader middlegreyT2">
                        <span id="selectedCount" class="selectedCount pull-left">0</span><span class="pull-left">&nbsp; selected user</span>
                        <div class="clear"></div>
                        <div class="optionButtons">
                            <button type="button" class="btn btn-dark btn-l disabled" data-option-value="active">
                                <span class="success">Activate</span></button>
                            <button type="button" class="btn btn-dark btn-l disabled" data-option-value="inactive">
                                <span class="danger">DeActivate</span></button>
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
            <div class="span8">
                <div id="myUsers">

                    <!-- myUsersListContainer -->
                    <div class="myUsersListContainer">
                        <div class="clear"></div>
                        <ul id="myUsersList" class="myUsersList span8">

                            <?
                            $sql = "SELECT
                        u_id, elotag, vezeteknev, keresztnev, full_name, user_email, createdDate, ctime, profilePicture,
                        department, gender, birthDate, language, schools, position, activeState
                        FROM user_u
                        WHERE deleted=0 AND isvisible =1 AND userlevel > ".DEMO_USER_LEVEL." AND office_id = " . MySQL::filter($_SESSION['office_id']) . " AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'
                        ORDER BY vezeteknev ASC"; // LIMIT 0,50

                            $rows = MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc);

                            $usersArray = array();
                            $urlRegex = URL_REGEX;
                            if(!empty($rows))
                            foreach ($rows as &$row) {
                                $usersArray = array(
                                    'id'         => $row['u_id'],
                                    'fullname'   => $row['full_name'],
                                    'name'       => normalize_special_characters(str_replace(' ', '', strtolower($row['full_name']))),
                                    'department' => !$row['department'] ? '' : UserUserGroup::helperGetGroupNameByGroupId($row['department']),
                                    'doname'     => $row['department'],
                                    'registered' => date('Y.m.d', strtotime($row['createdDate'])),
                                    'skills'     => array(),
                                    'img'        => (is_null($row['profilePicture']) || $row['profilePicture'] == "") ? '' : ($row['profilePicture']),
                                    'email'      => $row['user_email'],
                                    'active'     => ($row['activeState'] == 1) ? 'active' : 'inactive'
                                );
                                echo '<li class="userElement span2 rootClass" data-category="'.$usersArray['doname'].'" data-name="'.$usersArray['name'].'" data-registered="'.$usersArray['registered'].'" id="'.$usersArray['id'].'" data-email="'.$usersArray['email'].'">
                <div class="colorBar '.$usersArray['active'].'"></div>
                <div class="thumbBorder"><div class="thumbnail" style="width:100px;height:100px"><img src="'.$usersArray['img'].'" alt="" /></div></div>
                <div class="optionBar">
                    <span class="dropdown-toggle" data-toggle="dropdown"><i class="icon-caret-right"></i></span>
                    <ul class="dropdown-menu userAction">
                        <li class=""><a href="#" data-action="active">Activate</a></li>
                        <li class=""><a href="#" data-action="inactive">Deactivate</a></li>
                        <li class=""><a href="#" data-action="removefrom">Remove from</a></li>
                        <li class=""><a href="#" data-action="delete">Delete</a></li>
                    </ul>
                    <span class="selectButton" ><i class="icon-ok"></i></span>
                    <span class="detailButton" ><i class="icon-edit"></i></span>

                </div>
                <div class="userdata aligncenter">
                    <span class="name span2">'.$usersArray['fullname'].'</span>
                    <span class="department span2">'.$usersArray['department'].'</span>
                </div>
            </li>';
                                //<a href="#myDetails" class="detailButton" role="button" data-toggle="modal"><i class="icon-arrow-down"></i></a>
                            }
                            ?>
                        </ul>
                        <div class="clear"></div>
                    </div>
                    <!-- myUsersListContainer -->
                    <div id="x1"></div>
                </div>
                <!-- /myMediaFiles -->
            </div>
            <!-- /MIDDLE SIDE -->
            <!-- RIGHT SIDE -->
            <div class="span2">
                <div class="affix-top" id="myOptionsContainer"><!-- myUsersContainer -->
                    <div class="accordion-heading"><span class="accordion-toggle">training groups</span></div>
                    <div class="clearfix"></div>
                    <!-- tarininggroups -->
                    <div class="createNew well well-small">

                        <div class="dropdown">
                            <h3 class="functionHeader middlegreyT2">
                                <span></span>
                                <button class="dropdown-toggle btn btn-dark btn-r" data-toggle="dropdown" role="button" data-href="#" id="newMB"><?=$newTrainingName;?></button>
                                <ul class="dropdown-menu newtrainingGroup">
                                    <li class="newForm">
                                        <form class="form-vertical" method="post" action="#" id="newGroupForm">
                                            <div class="input-append">
                                                <input type="text" class="input-newMB" id="newGroupName" placeholder="<?= $newMBFormInputPlaceholder; ?>" maxlength="15">
                                                <span class="add-on">
                                                    <button type="button" class="btn btn-dark" id="addNewGroup" data-func="departmentAdd">
                                                        <i class="icon-plus"></i></button>
                                                </span>
                                            </div>
                                        </form>
                                    </li>
                                </ul>
                            </h3>
                        </div>

                    </div>

                    <div class="clearfix"></div>
                    <div class="sideBar training span2">
                        <ul class="mediaBoxList span2" id="trainingGroupList"></ul>
                    </div>
                    <!-- /tarininggroups -->
                    <div class="clearfix"></div>
                    <div class="accordion-heading"><span class="accordion-toggle"><?=$span2Headerright2;?></span></div>
                    <div class="clearfix"></div>
                    <div class="Options well well-small">
                        <span class="functionHeader middlegreyT2">

                            <div class="clear"></div>
                            <textarea class="fastMessage"></textarea>
                            <button type="button" class="btn btn-dark whiteT disabled" id="sendFastMessage"><span>send</span></button>
                        </span>
                    </div>
                    <div class="clearfix"></div>
                </div>
            </div>
            <!-- /RIGHT SIDE -->
        </div>
    </div>
</section>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/pwmeter.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/lib/umFunction.js"></script>

<script type="text/javascript">
$('#loading').show();
userManager.init();
</script>

<?php
include ($_SERVER['DOCUMENT_ROOT'] . '/templates/users/users.tmpl');
include ($_SERVER['DOCUMENT_ROOT'] . '/templates/users/userDetail.tmpl');
include ($_SERVER['DOCUMENT_ROOT'] . '/templates/users/userGroupList.tmpl');
include ($_SERVER['DOCUMENT_ROOT'] . '/templates/users/trainingGroupList.tmpl');
include ($_SERVER['DOCUMENT_ROOT'] . '/templates/users/newDepartment.tmpl');
?>
