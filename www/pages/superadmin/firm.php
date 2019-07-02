<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');

$protocol = connectionType();
$imageURL = $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/';
$subdomain = $protocol . $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/training/';


$oTableName = "users";
$dbTableName = "u";
$tableTopId = 'usersTableTop';
$formName = 'muveletekUsers';
$tableFooterId = 'usersTableFooter';
//printR($_GET);
?>
<!---->
<link rel="stylesheet" type="text/css" charset="utf-8" media="all" href="/pages/superadmin/css/admin.css" />
<script type="text/javascript" charset="utf-8" src="/assets/bootstrap/bootstrap-tab.js" ></script>

<h3>itt beállítom a következőket</h3>
<p> server oldalon az alapkönyvtárakat</p>
<p> </p>
<ul>
<li>cegadatok</li>
<li>ceg admin</li>
</ul>

<div class="row">
  <div class="span12">
    <ul id="myTab" class="nav nav-tabs">
      <li class="active"><a href="#clist" data-toggle="tab">Company list</a></li>
      <li class="dropdown">
        <a href="#" class="dropdown-toggle" data-toggle="dropdown">New company registration<b class="caret"></b></a>
        <ul class="dropdown-menu">
          <li class=""><a href="#dropdown1" data-toggle="tab"><span class="badge">1</span>Create company profile</a></li>
          <li class=""><a href="#dropdown2" data-toggle="tab"><span class="badge">2</span>Create local admin profile</a></li>
          <li class=""><a href="#dropdown3" data-toggle="tab"><span class="badge">3</span>Create db and folder structure</a></li>
        </ul>
      </li>
      <li class=""><a href="#individualPassword" data-toggle="tab">Individual password</a></li>
    </ul>
    <div class="inlineMessage"></div>
    <div id="myTabContent" class="tab-content">
      <div class="tab-pane fade active in" id="clist">
<!-- company list -->
        <div class="dataTables_top functionHeader lightgreyB" id="<?=$tableTopId;?>">
        <div class="clearfix"></div>
        <div class="btn-group" data-toggle="buttons-radio" style="float:left"><!-- type_param -->
              <button type="button" class="btn btn-dark active" id="sparam_all">all</button>
              <?
              $i = 0;
              foreach(range('a', 'z') as $letter) {
                echo '<button type="button" class="btn btn-dark" id="sparam'.$i.'">'.strtoupper($letter).'</button>';
                $i++;
              }
              ?>
        </div>
        </div>
        <!-- /type_param -->
            <input type="hidden" id="sparamI" name="sparamI" value="" />
        <div class="clearfix"></div>
        <table id="<?=$oTableName;?>" class="table table-striped table-hover" cellpadding="0" cellspacing="0" border="0"><!-- table -->
          <thead>
            <tr>
              <th width="0"></th><!-- u id -->
              <th width="0"></th><!-- office_nametag -->
              <th width="20%">office_name_hu</th>
              <th width="10%">office_email</th>
              <th width="10%">office_tel</th>
              <th width="15%">address</th>
              <th width="15%">contact_name</th>
              <th width="17%">contact_title</th>
              <th width="5%">createdDate</th>
              
            </tr>
          </thead>
          <tbody class="draggable">
            <tr>
              <td colspan="8" class="dataTables_empty"></td>
            </tr>
          </tbody>
        </table><!-- /table -->
        <div class="actionBar"><!-- actionBar -->
          <div class="actionButtons">
            <input type="submit" class="btn lgrey btn-l btn-middle" rel="active" value="Aktivál" />
              <input type="submit" class="btn lgrey btn-l btn-middle" rel="inactive" value="Inaktivál" />
              <input type="submit" class="btn lgrey btn-l btn-middle" rel="del" value="Töröl" />
          </div>
        </div><!-- /actionBar -->
        <div class="clear"></div>
        <div class="dataTables_footer" id="<?=$tableFooterId;?>"></div>
<!-- /company list -->
      </div>
      <div class="tab-pane fade" id="individualPassword">
      <?
      require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_header_text.php');
      include ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_include_base.php');
      require_once ($_SERVER['DOCUMENT_ROOT'].'/../include/class/class.encrypt.php');


        $pwd = 'kovax';//rand_string(8);
        $email = 'balint.kovacs@enterstudio.hu';
        $key = uniqid("", true);
        $enc = new Encryption();
        $enc->addKey($email);
        $encrypted = $enc->encode($key);
        $saveToDb = array(
          'email' => $email,
          'pwd' => $pwd
        );
        
        printR($saveToDb);
        
        /////////////////////////////////
        //adatbazisba
        /////////////////////////////////
        //mentett passwd
        $hashPwd = HashPassword($pwd);
        //activation code
        $activation_code = $key;
        $cryptedText = $encrypted;
        
        $saveToDb = array(
          'hashpwd' => $hashPwd,
          'activation_code' => $key,
          'cryptedText' => $encrypted
        );
        
        printR($saveToDb);
        
        
      ?>
      
      </div>
      <div class="tab-pane fade" id="dropdown1">
        <form class="form-horizontal">
          <legend class=""><span class="badge">1</span> Company data</legend>

          <div class="span5">
            <div class="control-group">
              <label class="control-label" for="input01">Office name</label>
              <div class="controls">
                <input type="text" placeholder="Some Firm Ltd." class="input-xlarge" name="office_name_hu">
                <p class="help-block">Complete firm name</p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Subdomain</label>
              <div class="controls">
                <div class="input-append">
                  <input class="span2" placeholder="trillala" type="text" name="office_nametag">
                  <span class="add-on">???</span>
                </div>
                <p class="help-block">trillala.skillbi.com</br>check if exist</p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="input01">City</label>
              <div class="controls">
                <input type="text" placeholder="" class="input-xlarge" name="office_city">
                <p class="help-block"></p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="input01">Postcode</label>
              <div class="controls">
                <input type="text" placeholder="" class="input-xlarge" name="office_postcode">
                <p class="help-block"></p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="input01">Address</label>
              <div class="controls">
                <input type="text" placeholder="" class="input-xlarge" name="office_street">
                <p class="help-block"></p>
              </div>
            </div>
          </div>
          
          <div class="span6" style="margin-left:5px;">
            <div class="control-group">
              <label class="control-label" for="input01">Office phone</label>
              <div class="controls">
                <input type="text" placeholder="" class="input-xlarge" name="office_tel">
                <p class="help-block">+36 20 1234567</p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="input01">Contact name</label>
              <div class="controls">
                <input type="text" placeholder="" class="input-xlarge" name="contact_name">
                <p class="help-block">Office contact like director...</p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="input01">Contact title</label>
              <div class="controls">
                <input type="text" placeholder="" class="input-xlarge" name="contact_title">
                <p class="help-block">managing director</p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Office email</label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-envelope"></i></span>
                  <input class="span2" placeholder="email" name="office_email" type="text">
                </div>
                <p class="help-block">contact email (not local admin)</p>
              </div>
            </div>
          </div>
        </form>
      </div>
      
      <div class="tab-pane fade" id="dropdown2">
        <form class="form-horizontal">
          <div class="span6">
            <div id="legend" class=""><legend class=""><span class="badge">2</span>Local admin</legend></div>
            <div class="control-group">
              <label class="control-label" for="input01">Pre</label>
              <div class="controls">
                <input type="text" placeholder="" class="input-xlarge" name="elotag">
                <p class="help-block">Dr.</p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="input01">Vorname</label>
              <div class="controls">
                <input type="text" placeholder="" class="input-xlarge"  name="keresztnev">
                <p class="help-block">like Sherlock</p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label" for="input01">Surename</label>
              <div class="controls">
                <input type="text" placeholder="" class="input-xlarge"  name="vezeteknev">
                <p class="help-block">like Holms</p>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">Gender</label>
              <div class="controls">
                <select class="input-xlarge" name="gender">
                  <option value="male">male</option>
                  <option value="female">female</option>
                </select>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">User level</label>
              <div class="controls">
                <select class="input-xlarge" name="userlevel">
                  <option value="1">Test user</option>
                  <option value="3">Simple user</option>
                  <option value="5">5</option>
                  <option value="7">Local Admin</option>
                </select>
              </div>
            </div>
            <div class="control-group">
              <label class="control-label">E-mail</label>
              <div class="controls">
                <div class="input-prepend">
                  <span class="add-on"><i class="icon-envelope"></i></span>
                  <input class="span2" placeholder="placeholder" name="user_email" type="text">
                </div>
                <p class="help-block">login data</p>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="tab-pane fade" id="dropdown3">
      </div>
    </div>
 

  </div>
</div>
<script>
  $(function () {
    $('#myTab a[data-toggle="tab"]').bind('click.tab', function(){
      var elem = $(this).attr('href').replace('#','');
      $('#myTabContent').find('div[id="'+elem+'"]').attr('class','tab-pane fade active in');
    });
  })
</script>
<script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.json-2.2.min.js"></script>
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.base64.js"></script>
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.treeview.js"></script>
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.cookie.js"></script>

  
  <!-- FORM -->  
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.form.js"></script>
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.validate.min.js"></script>
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.maxlength.min.js"></script>
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.textpandable.js"></script>
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.maskedinput.js"></script>
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.pstrength.min.js"></script>
  <!--
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/json_parse.min.js"></script>
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.validate.message_hu.js"></script>


  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.placeholder.js"></script>
  -->

  <!-- DATATABLES -->  
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.datatables.min.js"></script>
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.editable.min.js"></script>
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.datatables.addon.js"></script>
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.autogrow.js"></script>
  <script type="text/javascript" charset="utf-8" src="/pages/superadmin/js/jquery.charcounter.js"></script>
<script type="text/javascript" charset="utf-8">
////////////////////////////////////////////////////////////////////////////
// Egyedi valtozok
////////////////////////////////////////////////////////////////////////////
var tipus;
var sparamI;
var subType;
var extra = '';
var worksTip = '';
var intezmenyTip = '';
sparamI = $('#sparamI').val();
worksTip;

////////////////////////////////////////////////////////////////////////////
var TableData;
var TableRowPos;
var <?=$oTableName;?>Table;
var <?=$oTableName;?>Settings;
var <?=$oTableName;?>TableSettings;

<?=$oTableName;?>TableSettings = {
    "sAjaxSource": "/pages/superadmin/process-office.php",
    "fnServerParams": function ( aoData ) {
       var names = ["ont", "u"]; 
       var params = ["<? echo base64_encode($ont); ?>", "<? echo base64_encode($uid); ?>"];
       for(var index in names) {
         aoData.push({name : names[index], value : params[index]});
       }
    },
    "sSortable" : "icon-sort",
    "sSortAsc" : "icon-sort-up",
    "sSortDesc" : "icon-sort-down",
    "bProcessing": true,
    "bServerSide": true,
    "bStateSave": true,
    "sDom": "lfrtipP",
    "sPaginationType": "bootstrap",
    "oLanguage": {
      "sProcessing":   "Feldolgozás...",
      "sLengthMenu":   "_MENU_ találat oldalanként",
      "sZeroRecords":  "Nincs a keresésnek megfelel\u0151 találat",
      "sInfo":         "Találatok: _START_ - _END_ Összesen: _TOTAL_",
      "sInfoEmpty":    "Nulla találat",
      "sInfoFiltered": "", // (_MAX_ összes rekord közül sz\u0171rve)
      "sInfoPostFix":  "",
      "sSearch":       "Keresés:",
      "sUrl":          "",
      "oPaginate": {
        "sFirst":    "Első",
        "sPrevious": "Előző",
        "sNext":     "Következő",
        "sLast":     "Utolsó"
      }
    },
    "aoColumns": [
      { "bVisible": false},                                              // u id
      { "bVisible": false},                                              // office_nametag 
      { "bVisible": true, "bSortable": true,  "sClass": "left"},         // office_name_hu
      { "bVisible": true, "bSortable": true,  "sClass": "left"},         // office_email
      { "bVisible": true, "bSortable": true,  "sClass": "left"},         // office_tel
      { "bVisible": true, "bSortable": false,  "sClass": "left"},        // address
      { "bVisible": true, "bSortable": true, "sClass": "left"},          // contact_name 
      { "bVisible": true, "sClass": "left", "bSortable": true},          // contact_title 
      { "bVisible": true, "sClass": "left", "bSortable": false}         // createdDate 
      //{ "bVisible": true, "bSortable": false} // muveletek
    ],
    //"aaSorting": [[6, 'asc']],
    "fnInitComplete": function () {
      $('#<?=$oTableName;?>_length, #<?=$oTableName;?>_filter').prependTo('#<?=$tableTopId;?>');
      $('#<?=$oTableName;?>_info, #<?=$oTableName;?>_paginate, .dataTables_paginate').appendTo('#<?=$tableFooterId;?>');
      fixTableWidth('<?=$oTableName;?>');
    },
    "fnDrawCallback": function(<?=$oTableName;?>Settings) { 
      fixTableWidth('<?=$oTableName;?>');

    }
  };

function fnFormatDetails ( nTr ) {
    var aData = <?=$oTableName;?>Table.fnGetData( nTr );
    subType = aData[0];
    var sOut = '<div id="dynTables" style="" class=""></div><div class="clear"></div>';
    return sOut;
}

<?=$oTableName;?>Table = $('#<?=$oTableName;?>').dataTable(<?=$oTableName;?>TableSettings);

//a tablazat soraban levo cellak inline szerkesztesenek modjai
$('#<?=$oTableName;?> tbody tr td div').live('click', function () {
  var divClass = $(this).attr('class');
  switch (divClass) {
    case 'hybrid':
      var divCharCount = $(this).attr('rel');
      inlineEditHybrid(<?=$oTableName;?>Table, '<?=$oTableName;?>', '/process/<?=$oTableName;?>/inlineEdit/', divCharCount);
      break;
    case 'autogrow':
      inlineEditAgrow(<?=$oTableName;?>Table, '<?=$oTableName;?>', '/process/<?=$oTableName;?>/inlineEdit/');
      break;
    case 'editable':
      inlineEdit(<?=$oTableName;?>Table, '<?=$oTableName;?>', '/process/<?=$oTableName;?>/inlineEdit/');
      break;
  }
});

//edit - preview muveletek
$('#<?=$oTableName;?> tbody td i').die( 'click');
$('#<?=$oTableName;?> tbody td i').live( 'click', function () {
  var nTr = this.parentNode.parentNode.parentNode;
  var saData = <?=$oTableName;?>Table.fnGetData( nTr );
  var doType = $(this).attr('class');
    switch (doType) {
      case 'icon-edit':
        var nTr2 = $(this).parents('tr')[0];
        if ($('#dynTables').length > 0 ) { $('#dynTables').parent().parent().remove(); }
        <?=$oTableName;?>Table.fnOpen( nTr2, fnFormatDetails(nTr2), 'detail' );
        goToByScroll('dynTables');
        TableData = <?=$oTableName;?>Table.fnGetData( nTr2 );
        TableRowPos = <?=$oTableName;?>Table.fnGetPosition( nTr2 );
        var ia = $('input[name="ia"]').val();
        var hh_url ='/users/profile/base/'+$.base64.encode(ia)+'-'+$.base64.encode(saData[0])+'-'+$.base64.encode(saData[0]);
        loadAjaxPage('dynTables', hh_url);
        return false;
        break;
      case 'details':
        var nTr2 = $(this).parents('tr')[0];
        //alert(nTr2);
        //return false;
        if ($('.detailsTable').length > 0 ) {
          $('.detailsTable').parent().parent().remove();
        }
        <?=$oTableName;?>Table.fnOpen( nTr2, fnFormatDetails(nTr2), 'detail' );
        break;
    }
})
  
//muveleti oszlop checkbox bejeloles eseten az actionBar gombjai enabled/disabled mod
$("#<?=$oTableName;?> tbody").on("change", "input[type=checkbox]", function(event){
  var obj = $(".actionButtons input:submit");
  ($('#<?=$oTableName;?> input:checked').length > 0 ? obj.removeAttr("disabled") : obj.attr("disabled", "disabled"));
});

//az actionBar muveletei
$('form#<?=$formName;?> input:submit').click( function() {
  var action = $(this).attr('rel');
  $('#<?=$formName;?> input[name=ac]').val(action);
  var sData = $('#<?=$formName;?>').serialize();
  var serial = $('input[type="checkbox"].DTInputs').serialize(); 
  sData += '&'+ serial;//$('input[type="checkbox"]', <?=$oTableName;?>.fnGetNodes()).serialize();
  //sData += '&'+$('input', oTable.fnGetNodes()).serialize();
  alert(sData);
return false;
  sData = $.base64.encode(sData);
  var ssData = ajaxSaveData('<?=$formName;?>',sData);
  showMessage({
    text: ssData.sResult, 
    type: ssData.sType
  });
  /**/
  //var sData = $('input', oTable.fnGetNodes()).serialize();
  //alert( "The following data would have been submitted to the server: \n\n"+$('form#muveletek').serialize() );
  $('form#<?=$formName;?> input[name=ac]').val('');
  <?=$oTableName;?>Table.fnDraw();
  return false;
});
  
//muveleti oszlop: check/uncheck all or invert selection 
$( '#<?=$oTableName;?> thead th.Clickable_img' ).die( 'click');
$( '#<?=$oTableName;?> thead th.Clickable_img' ).live( 'click', function() {
  $( 'table#<?=$oTableName;?> input[type="checkbox"]' ).each( function() {
    $( this ).attr( 'checked', $( this ).is( ':checked' ) ? false : true );
  })
});
////////////////////////////////////////////////////////////////////////////

$(document).ready(function() {
  $(".actionButtons input:submit").attr("disabled", "disabled");
});	
</script>