<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');

$user = new User($_SESSION['u_id']);

$credit = 0;

$returnArray = Statistics::getSumCreditByUserId($_SESSION['u_id']);

$maxCredit = $returnArray['max_credit'];
$credit = $returnArray['credit'];
?>
<link rel="stylesheet" type="text/css" charset="utf-8" media="screen" href="/css/myaccount.css"/>
<form id="usersform" action="#" method="POST" class="hidden">
    <input type="hidden" name="id" id="userid" value="<?= $_SESSION['u_id'] ?>" />
    <input type="hidden" name="office_id" value="<?= $_SESSION['office_id'] ?>"/>
    <input type="hidden" name="office_nametag" value="<?= $_SESSION['office_nametag'] ?>"/>
</form>
<input id="upload" type="file" class="hidden" accept="image/*"/>

<form id="fileupload" action="" method="POST" enctype="multipart/form-data" style="margin-left:-10000px;display:none;">
    <span class="btn btn-success fileinput-button"><span>Add files</span><input type="file" name="files[]" multiple></span>
    <button type="submit" class="btn btn-primary start"><span>Start upload</span></button>
</form>
<div class="row">
    <div class="span12">
        <div class="colHeader">
            <h3 class="orangeT"><span class="pull-left"><?=$user->getDBField('full_name')?>'s personal data</span></h3>
        </div>
    </div>
    <div class="span2">
        <div class="myprofile thumbnail">
            <img data-src="" data-pk="<?= $_SESSION['u_id'] ?>" id="profilePicture" alt="160x160" src="<?= $user->getDBField('profilePicture') ?>">
        </div>
        <div class="clearfix"></div>
        <div class="myProfile well well-small">
            <p><?= $user->getDBField('full_name') ?></p>
            <p><?= $user->getDBField('user_email') ?></p>
        </div>
        <div class="clearfix"></div>
        <div class="trainingResults">
            <ul class="slidesList" id="testResultList" style="">
                <li class="mediaBox mbcholder span2 lightgreyB">
                    <div class="mbHeader">
                        <div class="name test">my Skillpoints</div>

                        <div class="badge test" id="testPointDiv"><?=$credit;?></div>
                        <div class="clearfix"></div>
                    </div>
                </li>
            </ul>
            <div class="clearfix"></div>
            <ul class="slidesList" id="trainingResultList" style="">
                <li class="mediaBox mbcholder span2 middlegreyB2">
                    <div class="mbHeader">

                        <div class="clearfix"></div>
                        <div class="progress">
                            <div class="bar bar-success" style="width:<?= $credit / $maxCredit * 100; ?>%"></div>
                        </div>
                        <div class="clearfix"></div>
                        <div class="pull-left"><span class="badge1"><span class="count"><?=$maxCredit;?>/<?=$credit;?></span></span></div>
                    </div>
                </li>

            </ul>
        </div>

    </div>
    <div class="span5">

        <div class="form-horizontal myaccountData" id="detailsForm1">
            <div class="control-group">
                <label class="control-label" for="gender">Gender</label>

                <div class="controls">
                    <a href="#" id="gender" data-type="select" data-pk="<?= $_SESSION['u_id'] ?>" data-value="<?= $user->getDBField('gender') == 'Male' ? '1' : '2'; ?>"
                       data-original-title="Select gender"><?=$user->getDBField('gender')?></a>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="birth">Date of birth</label>

                <div class="controls">
                    <a href="#" id="birth" data-type="date" data-viewformat="yyyy.mm.dd"
                       data-pk="<?= $_SESSION['u_id'] ?>" data-placement="right"
                       data-original-title="Date of birth"><?=str_replace('-', '.', $user->getDBField('birthDate'))?></a>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="language">Language</label>

                <div class="controls">
                    <a href="#" id="language" data-type="checklist" data-pk="<?= $_SESSION['u_id'] ?>"
                       data-value="<?= $user->getDBField('language') ?>"
                       data-original-title="Spoken languages"><?=$user->getDBField('language')?></a>
                </div>
            </div>


        </div>

        <div class="clearfix"></div>

        <div class="form-horizontal pill myaccountData" id="detailsForm">
            <div class="control-group">
                <label class="control-label" for="pemail">Private email</label>

                <div class="controls">
                    <a href="#" id="pemail" data-type="email" data-pk="<?= $_SESSION['u_id'] ?>"
                       data-original-title="Enter private email"><?=$user->getDBField('pemail')?></a>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="skills">Skills</label>

                <div class="controls">
                    <a href="#" id="skills" data-type="textarea" data-pk="<?= $_SESSION['u_id'] ?>"
                       data-original-title="Enter skills"><?=$user->getDBField('skills')?></a>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="schools">Schools</label>

                <div class="controls">
                    <a href="#" id="schools" data-type="textarea" data-pk="<?= $_SESSION['u_id'] ?>"
                       data-original-title="Enter schools"><?=$user->getDBField('schools')?></a>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="form-horizontal myaccountData" id="detailsForm1">
            <div class="control-group">
                <label class="control-label" for="cv">CV</label>

                <div class="controls">
                    <div id="cv" data-pk="<?= $_SESSION['u_id'] ?>" data-type="wysihtml5"
                         data-original-title="Enter CV data" class="editable"><?=$user->getDBField('cv')?></div>
                </div>
            </div>
        </div>

        <div class="clearfix"></div>

        <div class="form-horizontal myaccountData" id="detailsForm1">
            <div class="control-group">
                <label class="control-label" for="description">Description</label>

                <div class="controls">
                    <div id="description" data-pk="<?= $_SESSION['u_id'] ?>" data-type="wysihtml5"
                         data-original-title="Enter description"
                         class="editable editable-click"><?=$user->getDBField('description')?></div>
                </div>
            </div>
        </div>

    </div>
    <div class="span5">
        <div class="form-horizontal span4 myaccountData" id="passwordChange">
            <div class="control-group">
                <label class="control-label" for="inputPassword">Password</label>

                <div class="controls">
                    <a href="#" id="pwd" class="myPass" data-type="password" data-pk="<?= $_SESSION['u_id'] ?>"
                       data-original-title="Type Password"></a>
                </div>
            </div>

            <div class="control-group">
                <label class="control-label" for="inputPassword">ReType Password</label>

                <div class="controls">
                    <a href="#" id="pwd2" class="myPass" data-type="password" data-pk="<?= $_SESSION['u_id'] ?>"
                       data-original-title="ReType Password"></a>
                </div>

            </div>
            <div class="control-group">
                <span class="help-block"></span>
            </div>

            <div class="control-group">
                <button type="submit" id="submit" class="btn btn-r btn-dark">Update password</button>
            </div>
        </div>

        <div class="clearfix"></div>
    </div>
    <div class="span12">
        <div class="colHeader">
            <h3 class="orangeT"><span class="pull-left"><?=$user->getDBField('full_name')?>'s personal statistic</span></h3>
        </div>
    </div>
    <div class="span12">
        <div id="sumStatisticFrame" class="myuser">
            <div id="statisticHolder" class=""></div>
        </div>
    </div>
</div>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/bootstrap-editable.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/pwmeter.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/wysihtml5-0.3.0.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/bootstrap-wysihtml5-0.0.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/wysihtml5.js"></script>
<!--<script type="text/javascript" charset="utf-8" src="/assets/editable/editable.js" ></script>-->
<script type="text/javascript" charset="utf-8" src="/lib/maFunction.js"></script>


<script type="text/javascript">


    $(function () {
        myAccount.init();

    })
</script>

<script type="text/x-tmpl" id="tmpl-statpanel2">

    {% for (var i=0, stat; stat=o.result[i]; i++) { %}

    <div class="detailDiv span6 well well-small">
        <div class="headingData">
            <h1 class="functionHeader darkgreyT2">Training title: <span class="strdata">{%=stat.title%}</span></h1>
        </div>
        <div class="clearfix"></div>
        <div class="detailedData">
            <div class="resultsData pull-left">

                <h3 class="functionHeader middlegreyT2">result</h3>
                <div class="clearfix"></div>
                <ul class="pointSelector">
                    <li class="separator"><h1 class="functionHeader darkgreyT2"><span class="intdata">{%=clearNULL(stat.trainingrates)%}</span></h1></li>
                </ul>
            </div>
            <div class="resultsData2 pull-left">
                <ul class="summaryzedData pull-left">

                    <li class="inprogressTraining pull-left">
                        <h3 class="functionHeader middlegreyT2">in progress</h3>
                        <h1 class="functionHeader darkgreyT2"><span class="intdata">{%=stat.in_progress%}</span></h1>
                    </li>
                    <li class="finishedTraining pull-left">
                        <h3 class="functionHeader middlegreyT2">finished</h3>
                        <h1 class="functionHeader darkgreyT2"><span class="intdata">{%=stat.finished%}</span></h1>
                    </li>

                    <li class="successfullExam pull-left">
                        <h3 class="functionHeader middlegreyT2">success</h3>
                        <h1 class="functionHeader orangeT"><span class="intdata">{%=stat.successful_exam%}</span></h1>
                    </li>
                    <li class="failedExam pull-left">
                        <h3 class="functionHeader middlegreyT2">failed</h3>
                        <h1 class="functionHeader darkgreyT2"><span class="intdata">{%=stat.failed_exam%}</span></h1>
                    </li>
                </ul>
            </div>


        </div>
    </div>
    {% } %}

</script>