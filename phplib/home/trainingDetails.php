<?php

$htmlCode='';
$result = Statistics::getTrainingDetailsHomePage($_SESSION['office_id'],$_SESSION['u_id'],$trainingId=(int)$_POST['training_id']);
$result = $result[$trainingId];
//$starRatingTraining = Statistics::getStarRating($_SESSION['office_id'],'training_'.(int)$_POST['training_id']);
//$starRatingTrainer = Statistics::getStarRating($_SESSION['office_id'],'trainer_');
//printR($result);
//$htmlCode.='<b>Training title:'.$result['data']['meta']['title'].'</b><br/>';
//$htmlCode.='<b>Training description:'.$result['data']['meta']['description'].'</b><br/>';
$author = $result['data']['meta']['authors'];
$attachment = $result['data']['meta']['attachment'];
foreach ($result['slideshows'] as $slideShowId=>$slideShowData)
{
    $exam =(int)$slideShowData['type'] == 0 ?'No':'Yes';
    if((int)$slideShowData['type'] == 0){
        $examType='-';
    } else {
        $examType='';
        switch ((int)$slideShowData['testtype']){
            case 0:$examType='2 pole';break;
            case 1:$examType='5 pole';break;
            case 2:$examType='Eval test';break;
        }
    }

    $solved = (int)$slideShowData['scores']['success'] == 0 ? 'No':'Yes';
    $htmlCode.='
    <tr>
        <td>
        <ul>
        <li>SlideShow Name: ' . $slideShowData['meta']['name'] . '</li>
        <li><button style="margin-left:0" class="openSlideshowButton btn-dark ' . ((!$slideShowData['_allow_exec']) ? 'disabled' : '') . '"
                data-id="' . $_POST['training_id'] . '"
                data-sid="' . $slideShowData['slideshow_id'] . '"
                isotope-title="' . strip_tags($slideShowData['meta']['name']) . '"
                isotope-author="'.$author.'">' . ($slideShowData['_allow_exec_message'] ? '' : 'Start Slideshow') . $slideShowData['_allow_exec_message'] . '</button></li>
        <li>Start date: ' . ($slideShowData['startDate'] == '' ? 'Continous' : $slideShowData['startDate']) . '</li>
        <li>End date: ' . ($slideShowData['endDate'] == '' ? 'Continous' : $slideShowData['endDate']) . '</li>
        </ul>';
    /*
            SlideShow Name: ' . $slideShowData['meta']['name'] . '<br/>
            <button style="margin-left:0" class="openSlideshowButton btn-dark ' . ((!$slideShowData['_allow_exec']) ? 'disabled' : '') . '"
                data-id="' . $_POST['training_id'] . '"
                data-sid="' . $slideShowData['slideshow_id'] . '"
                isotope-title="' . strip_tags($slideShowData['meta']['name']) . '"
                isotope-author="'.$author.'">' . ($slideShowData['_allow_exec_message'] ? '' : 'Start Slideshow') . $slideShowData['_allow_exec_message'] . '</button><br/>
            Start date: ' . ($slideShowData['startDate'] == '' ? 'Continous' : $slideShowData['startDate']) . '<br/>
            End date: ' . ($slideShowData['endDate'] == '' ? 'Continous' : $slideShowData['endDate']) . '<br/>
            <!--Timeout:' . $slideShowData['timeout1'] . ' - ' . $slideShowData['timeout2'] . '<br/>
            Weekend timeout:' . $slideShowData['wtimeout1'] . ' - ' . $slideShowData['wtimeout2'] . '<br/>-->
*/
    $htmlCode.='
        </td>
        <td>
            <ul>';
                
                //<li>Points:' . $slideShowData['scores']['max_point'] . ' / ' . $slideShowData['scores']['point'] . '</li>
    $htmlCode.='<li>Credits:' . $slideShowData['credit'] . ' / ' . $slideShowData['scores']['credit'] . '</li>
                <li>Solved:' . $solved . '</li>
                <li>Exam:' . $exam . '</li>
                <li>Exam type: ' . $examType . '</li>
            </ul>
        </td>
    </tr>';
}
//print $htmlCode;
?>

<div class="well well-small trainingDetailsDiv">

    <div class="tabbable tabs-left">
        <ul class="nav nav-tabs bold">
            <li class="active"><a href="#topone" data-toggle="tab">Slideshows</a></li>
            <li class=""><a href="#toptwo" data-toggle="tab">Description</a></li>
            <li class=""><a href="#topthree" data-toggle="tab">Attachment</a></li>
        </ul>
        <div class="tab-content">
            <div class="tab-pane active" id="topone">
                <div class="inlineContent">
                    <table class="table-striped">
                        <thead></thead>
                        <tbody>
                        <?=$htmlCode?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="tab-pane" id="toptwo">
                <div class="inlineContent">
                    <?=$result['data']['meta']['description']?>
                </div>
            </div>
            <div class="tab-pane" id="topthree">
                <div class="inlineContent">
                    <?
                    //////////////////////////////////////////////////////////////
                    // check for attachment
                    //////////////////////////////////////////////////////////////
                    $sql = "
          SELECT *
          FROM media_mymedia
          WHERE FIND_IN_SET (mymedia_id, ( SELECT attachment FROM training_training WHERE training_id = " . $_POST['training_id'] . " ) )";

                    $attRes = MySQL::query($sql, false, false);
                    $attHtml = '';
                    $db = count($attRes);
                    if ($db > 0) {
                        $imageURL = connectionType() . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/';
                        $downloadUrl = connectionType() . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/download/';
                        $attHtml = createAttachLinks($attRes, $imageURL, $downloadUrl);
                    }
                    print '<ul>'.$attHtml.'</ul>';
                    ?>
                </div>
            </div>
        </div>
    </div>

</div>
<script>
    $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
    })
    $('.inlineContent').slimscroll({
        width: '400px',
        height: '180px',
        allowPageScroll: false
    })
</script>

<style>
    .inlineContent {width:300px;}
    .inlineContent ul li { padding: 3px 0; float:left;width:100%}
    .trainingDetailsDiv.well.well-small {
        background-color: rgba(0, 0, 0, 0);
        margin: 15px 0 0 0;
    }
    .trainingDetailsDiv a {color:#333333}
    table {
        width: 100%;
    }
    table td {
        width:50%;
        vertical-align: top;
    }
    .table-striped tbody tr:nth-child(odd) td, .table-striped tbody tr:nth-child(odd) th {
        background-color: #efefef;
    }
</style>

<?
/*
    <form id="rating" action="#">
<fieldset class="rating">
    <legend>Please rate:</legend>
    <input type="radio" id="star5" name="rating" value="5" /><label for="star5" title="Rocks!">5 stars</label>
    <input type="radio" id="star4" name="rating" value="4" /><label for="star4" title="Pretty good">4 stars</label>
    <input type="radio" id="star3" name="rating" value="3" /><label for="star3" title="Meh">3 stars</label>
    <input type="radio" id="star2" name="rating" value="2" /><label for="star2" title="Kinda bad">2 stars</label>
    <input type="radio" id="star1" name="rating" value="1" /><label for="star1" title="Sucks big time">1 star</label>
</fieldset>
</form>
<style>
    .rating {
    float:left;
}

/*
 *  :not(:checked) is a filter, so that browsers that don’t support :checked don’t
   follow these rules. Every browser that supports :checked also supports :not(), so
   it doesn’t make the test unnecessarily selective */
    /*
.rating:not(:checked) > input {
    position:absolute;
    top:-9999px;
    clip:rect(0,0,0,0);
}

.rating:not(:checked) > label {
    float:right;
    width:1em;
    padding:0 .1em;
    overflow:hidden;
    white-space:nowrap;
    cursor:pointer;
    font-size:200%;
    line-height:1.2;
    color:#ddd;
    text-shadow:1px 1px #bbb, 2px 2px #666, .1em .1em .2em rgba(0,0,0,.5);
}

.rating:not(:checked) > label:before {
    content: '★ ';
}

.rating > input:checked ~ label {
    color: #f70;
    text-shadow:1px 1px #c60, 2px 2px #940, .1em .1em .2em rgba(0,0,0,.5);
}

.rating:not(:checked) > label:hover,
.rating:not(:checked) > label:hover ~ label {
    color: gold;
    text-shadow:1px 1px goldenrod, 2px 2px #B57340, .1em .1em .2em rgba(0,0,0,.5);
}

.rating > input:checked + label:hover,
.rating > input:checked + label:hover ~ label,
.rating > input:checked ~ label:hover,
.rating > input:checked ~ label:hover ~ label,
.rating > label:hover ~ input:checked ~ label {
    color: #ea0;
    text-shadow:1px 1px goldenrod, 2px 2px #B57340, .1em .1em .2em rgba(0,0,0,.5);
}

.rating > label:active {
    position:relative;
    top:2px;
    left:2px;
}
</style>
    <script type="text/javascript">
    $('input').bind('click', function(e){
        e.preventDefault();
        alert($('form').serialize());
    })
    </script>
*/
?>