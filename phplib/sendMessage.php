<?php

if (Access::getAccessLevel()<=5) exit;

/**
 * Fast message uzenetek kuldese a kijelolt felhasznaloknak, a usermanager oldalon
 */


$returnData = array();
$formData = createArrayFromPostNV('form');

switch (getRequest('action'))
{
    case 'fastmessage':

        $selectedUsers = array(); //kijelolt userek a feluleten

        foreach (getRequest('users') as $tmp)
            if ($tmp['id']>0)   $selectedUsers[] = $tmp['id'];

        $userList=array(); //akiknek kuldjuk a leveleket
        $tmpUser = new User($_SESSION['u_id']); //a kuldo user obj.
        $sender = $tmpUser->getDBField('full_name'); //a kuldo neve
        $message = getRequest('message'); //a kuldott uzenet

        foreach ($selectedUsers as $userId)
        {
            $tmpUser = new User($userId);
            $userList[] = array('name'=>$tmpUser->getDBField('full_name'),'email'=>$tmpUser->getDBField('user_email'));
        }

        if (count($userList)>0 and strlen($sender)>0 and strlen($message)>0)
        {
            ActionLogger::addToActionLog('usermanager.sendmessage',null,'message:'.$message);

            SkillMailer::sendFastmessage($userList,$sender,'Fastmessage',$message);

            $returnData = array('type' => 'success', 'message' => 'Message sent successfully!');
        }
        else
        {
            $returnData = array('type' => 'error', 'message' => 'Send error!');
        }

        printSortResult($returnData);
    break;
}

exit;
?>