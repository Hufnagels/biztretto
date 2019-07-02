<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');

	$myUserId = $_SESSION['u_id'];
    $myUser = new User($myUserId);

    $credit = 0;

	$returnArray = Statistics::getSumCreditByUserId($myUserId);
    
    $maxCredit = $returnArray['max_credit'];
    $credit = $returnArray['credit'];
	
?>

<section id="inner-headline">
    <div class="container">
        <div class="row">
            <div class="span6">
                <div class="inner-heading">
                    <h2 id="linkData"><?=$myUser->getDBField('full_name');?></h2>
                </div>
            </div>
            <div class="span6">
                <ul class="breadcrumb" id="trainingResultList" style="">
                    <li><h2 class="summ">Credits <i class="icon-angle-right"></i></h2></li>
                    <li id="linkName" class="active"><h2 class="count"> <strong><?=$credit;?></strong></h2></li>
                </ul>
            </div>
        </div>
    </div>
</section>
<section id="content">
<?
	//printR($_SESSION);
	//print HashPassword('fakutya');
	?>
<div class="container">
    <form id="usersform" action="#" method="POST" class="hidden">
        <input type="hidden" name="id" id="userid" value="<?= $myUserId; ?>" />
        <input type="hidden" name="office_id" value="<?= $_SESSION['office_id']; ?>"/>
        <input type="hidden" name="office_nametag" value="<?= $_SESSION['office_nametag']; ?>"/>
    </form>
    <input id="upload" type="file" class="hidden" accept="image/*"/>

    <form id="fileupload" action="" method="POST" enctype="multipart/form-data" style="margin-left:-10000px;display:none;">
        <span class="btn btn-success fileinput-button"><span>Add files</span><input type="file" name="files[]" multiple></span>
        <button type="submit" class="btn btn-primary start"><span>Start upload</span></button>
    </form>

    <div class="row">
        <div class="offset2 span8">
            <div class="accordion" id="AccountAccordion">
                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#AccountAccordion" href="#collapse1"><i class="icon-minus"></i>Personal data</a>
                    </div>
                    <div id="collapse1" class="accordion-body collapse in">
                        <div class="accordion-inner">
                            <div class="span2">
                                <div class="myprofile thumbnail">
                                    <img data-src="" data-pk="<?= $myUserId; ?>" 
										id="profilePicture" alt="160x160" 
										src="<?= $myUser->getDBField('profilePicture'); ?>">
                                </div>
                                <div class="clearfix"></div>
                                <div class="myProfile well well-small aligncenter">
                                    <h6><?= $myUser->getDBField('full_name'); ?></h6>
                                    <p><?= $myUser->getDBField('user_email'); ?></p>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                            <div class="span5">

                                <div class="form-horizontal myaccountData" id="detailsForm1">
                                    <div class="control-group">
                                        <label class="control-label" for="gender">Gender</label>

                                        <div class="controls">
                                            <a href="#" id="gender" data-type="select" 
												data-pk="<?= $myUserId; ?>" 
												data-value="<?= $myUser->getDBField('gender') == 'Male' ? '1' : '2'; ?>"
												data-original-title="Select gender"><?=$myUser->getDBField('gender');?></a>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="birth">Date of birth</label>

                                        <div class="controls">
                                            <a href="#" id="birth" data-type="date" data-viewformat="yyyy.mm.dd"
                                               data-pk="<?= $myUserId; ?>" data-placement="right"
                                               data-original-title="Date of birth"><?=str_replace('-', '.', $myUser->getDBField('birthDate'));?></a>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="language">Language</label>

                                        <div class="controls">
                                            <a href="#" id="language" data-type="checklist" data-pk="<?= $myUserId; ?>"
                                               data-value="<?= $myUser->getDBField('language'); ?>"
                                               data-original-title="Spoken languages"><?=$myUser->getDBField('language');?></a>
                                        </div>
                                    </div>

                                </div>
                                <div class="form-horizontal myaccountData" id="detailsForm">
                                    <div class="control-group">
                                        <label class="control-label" for="pemail">Private email</label>

                                        <div class="controls">
                                            <a href="#" id="pemail" data-type="email" data-pk="<?= $myUserId; ?>"
                                               data-original-title="Enter private email"><?=$myUser->getDBField('pemail');?></a>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="skills">Skills</label>

                                        <div class="controls">
                                            <a href="#" id="skills" data-type="text" data-pk="<?= $myUserId; ?>"
                                               data-original-title="Enter skills"><?=$myUser->getDBField('skills');?></a>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="schools">Schools</label>

                                        <div class="controls">
                                            <a href="#" id="schools" data-type="text" data-pk="<?= $myUserId; ?>"
                                               data-original-title="Enter schools"><?=$myUser->getDBField('schools');?></a>
                                        </div>
                                    </div>

                                </div>

                                <div class="clearfix"></div>

                                <div class="form-horizontal myaccountData" id="passwordChange">
                                    <div class="control-group">
                                        <label class="control-label" for="inputPassword">Password</label>

                                        <div class="controls">
                                            <a href="#" id="pwd" class="myPass editable editable-click editable-empty" data-type="password" data-pk="12" data-original-title="Type Password">Change here</a>
                                        </div>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="inputPassword">ReType</label>

                                        <div class="controls">
                                            <a href="#" id="pwd2" class="myPass editable editable-click editable-empty" data-type="password" data-pk="12" data-original-title="ReType Password">Change here</a>
                                        </div>

                                    </div>
                                    <div class="control-group">
                                        <span class="help-block"></span>
                                    </div>

                                    <div class="control-group">
                                        <label class="control-label" for="inputPassword"></label>
                                        <div class="controls">
                                            <button type="submit" id="submit" class="btn btn-dark">Update password</button>
                                        </div>
                                    </div>
                                </div>

                                <div class="clearfix"></div>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="accordion-group">
                    <div class="accordion-heading">
                        <a class="accordion-toggle" data-toggle="collapse" data-parent="#AccountAccordion" href="#collapse3"><i class="icon-plus"></i>Results</a>
                    </div>
                    <div id="collapse3" class="accordion-body collapse">
                        <div class="accordion-inner">
                            <div id="sumStatisticFrame" class="myuser">
                                <div id="statisticHolder" class=""></div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </div>
</section>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/bootstrap-editable.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/pwmeter.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/wysihtml5-0.3.0.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/bootstrap-wysihtml5-0.0.2.min.js"></script>
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap-plugins/wysihtml5.js"></script>
<script type="text/javascript" charset="utf-8" src="/lib/maFunction.js"></script>
<style>
    .colHeader .nav {margin-bottom:0;}
    .colHeader .nav-tabs > li > a {
        padding-top: 0px;
        padding-bottom: 0px;
        line-height: 34px;
    }

    .tab-pane .span2 {margin-left:0px;}

    #profile .editable-inline .editable-container {margin-bottom: 10px;}

</style>

<script type="text/javascript">
    $(function () {
        myAccount.init();

    })
</script>

<script type="text/x-tmpl" id="tmpl-statpanel2">
    {% for (var i=0, stat; stat=o.result[i]; i++) { %}
    <h4 class="strdata">{%=stat.title%}</h4>
    <table class="table">
        <tbody>
    <tr>
        <td class="span4"><span class="label">Result:</span> {%=clearNULL(stat.trainingrates)%}</td>
        <td class="span3"><span class="label">Credits:</span> {%=clearNULL(stat.sumcredit)%}</td>
    </tr>
    </tbody>
    </table>
    {% } %}
</script>

<?
//printR($_SESSION);
exit;
?>