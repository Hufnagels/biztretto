<?php
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');

    $protocol = connectionType();
    $imageURL = $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/';

    $subdomain = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/training/';

//exit;
    $span2Header   = 'my summary';
    $span10Header1 = 'my slideshows';
    $span10Header2 = 'my trainings';
    $span2Header2  = 'feedback';

?>
<style>
	.front {cursor:pointer;}
</style>
    <section id="inner-headline">
        <div class="container">
            <div class="row">
                <div class="span6">
                    <div class="inner-heading">
                        <h2 id="linkData">My trainings</h2>
                    </div>
                </div>
                <div class="span6">
                    <ul class="breadcrumb" id="trainingResultList" style=""></ul>
                </div>
            </div>
        </div>
    </section>
    <section id="content">
        <div class="container">
            <form id="myForm" action="#" method="POST" class="hidden1">
                <input type="hidden" name="name" value="<?= $_SESSION['full_name'] ?>"/>
                <input type="hidden" name="office_id" value="<?= $_SESSION['office_id'] ?>"/>
                <input type="hidden" name="office_nametag" value="<?= $_SESSION['office_nametag'] ?>"/>
                <input type="hidden" name="owner" value="<?= $_SESSION['u_id'] ?>"/>
            </form>
            <div class="row special">
                
                <!-- middle side-->
                <div class="span12">

                    <div class="clearfix"></div>
                    <div class="myTrainingsContainer">
                        <ul class="thumbnails span10" id="myTrainingList" style="height:100%"></ul>
                    </div>
                </div>
                <!-- /middle side-->
            </div>
            <div class="clearfix"></div>
        </div>
    </section>

    <script type="text/javascript" charset="utf-8">var SUBDOMAIN = '<?=$subdomain;?>';</script>
    <script type="text/javascript" charset="utf-8" src="/lib/homeFunction.js"></script>
    <script type="text/javascript" charset="utf-8">
        $( function () {
            myTraining.init();
            
        } );
    </script>
<?php
    include ($_SERVER['DOCUMENT_ROOT'] . '/templates/home/trainingElement.tmpl');
    include ($_SERVER['DOCUMENT_ROOT'] . '/templates/home/detailsRow.tmpl');
    include ($_SERVER['DOCUMENT_ROOT'] . '/templates/home/credits.tmpl');
?>

<?
//printR($_SERVER);
//print_r($_SESSION);
?>