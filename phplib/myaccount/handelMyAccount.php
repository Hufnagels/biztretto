<?php
//print_r($_POST);
/**
 * User adatok betoltese, visszateres JSON objektummal, ami az adatbazis user_u tabla mezoit tartalmazza
 */
require_once ( $_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_auth.php' );
include ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_header_text.php');
include ($_SERVER['DOCUMENT_ROOT'].'/../include/header/_header_include_base.php');

$imageURL = $_SESSION['office_nametag'] . '.' . DOMAINTAG . '/media/';
$jsonResultArray = array();
$result = false;

$user = new User($_SESSION['u_id']);

if (getRequest('action') == 'changeData')
{
    if (getRequest('name')=='gender')       $user->setDBField(getRequest('name'),getRequest('value')==1?'Male':'Female');
    if (getRequest('name')=='birth')        $user->setDBField('birthDate',getRequest('value'));
    if (getRequest('name')=='language')     $user->setDBField(getRequest('name'),strip_tags(purifyString(@implode(',',($_POST['value'])))));
    if (getRequest('name')=='pemail')       $user->setDBField(getRequest('name'),strip_tags(purifyString(getRequest('value'))));
    if (getRequest('name')=='skills')       $user->setDBField(getRequest('name'),strip_tags(purifyString(getRequest('value'))));
    if (getRequest('name')=='schools')      $user->setDBField(getRequest('name'),strip_tags(purifyString(getRequest('value'))));
    if (getRequest('name')=='cv')           $user->setDBField(getRequest('name'),purifyString(getRequest('value')));
    if (getRequest('name')=='description')  $user->setDBField(getRequest('name'),purifyString(getRequest('value')));
    if (getRequest('name')=='profilePicture')  $user->setDBField(getRequest('name'),getRequest('value'));

    $result = $user->save();
}
elseif (getRequest('action')=='changePassword')
{
	if ($result = $user->changePassword(getRequest('value')))
    {
        unset($_POST['value']);
        $result = $user->save();
    }
}

if ($result)
{
	$jsonResultArray = array('type' => 'success', 'message' => 'Data successfully updated');
}
else
{
	$jsonResultArray = array('type' => 'error', 'message' => 'Data update failed');
}

printSortResult($jsonResultArray);

?>