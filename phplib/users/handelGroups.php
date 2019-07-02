<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

$_SESSION['LAST_ACTIVITY'] = time();

/*
actions = {
    department:{0:'load',1:'delete', 2:'rename', 3:'new', 4:'addto', 5:'removefrom'},
    groups:{0:'load*',1:'delete*', 2:'rename*', 3:'new*', 4:'addto*',5:'removefrom*'},
    users:{0:'load',1:'delete', 2:'update', 3:'new', 4:'activate', 5:'deactivate', 6:'loadone',7:'changeData'}
}
*/
/*
printR($_POST);
exit;
    */
switch ($_POST['action'])
{
    case 'load':
        $returnData = UserUserGroup::getGroupsWithMembersWeb($_SESSION['office_id'],$_SESSION['office_nametag']);
        printSortResult($returnData);
        break;

    case 'new':
        if (($result = UserUserGroup::helperCreateNewUserGroup(getRequest('groupname'),$_SESSION['u_id'],$_SESSION['office_id'],$_SESSION['office_nametag'])) instanceof UserUserGroup)
        {
            $returnData = array(
                'type' => 'success',
                'message' => 'Department save was successfull!',
                'result'=> array(
                    'id'=>$result->getId(),
                    'name'=>$result->getDBField('name'),
                    'doname'=>$result->getDBField('doname')
                )
            );
        }
        else
        {
            $returnData = array('type' => 'error', 'message' => 'Department save was unsuccessfull!');
        }

        printSortResult($returnData);
        break;

    case 'rename':
        if (($groupId=(int)getRequest('pk'))>0 and strlen(($groupName=getRequest('value')))>0)
        {
            if (($result = UserUserGroup::helperRenameUserGroup($groupId,$groupName)) instanceof UserUserGroup)
            {
                $returnData = array(
                    'type'=>'success',
                    'message'=>'Department rename was successfull!',
                    'result'=>
                    array(
                        'sortname' => $result->getDBField('doname'),
                        'newname' => $result->getDBField('name')
                    )
                );
            }
            else
            {
                $returnData = array('type'=>'error','message' => 'Department rename was unsuccessfull!');
            }
        }
        else
        {
            $returnData = array('type'=>'error','message' => 'Department rename was unsuccessfull!');
        }

        printSortResult($returnData);
        break;

    case 'delete':
        if (($groupId=(int)getRequest('id'))>0)
        {
            $userTrainingGroupObject = new UserUserGroup($groupId);
            $result = $userTrainingGroupObject->remove();

            if ($result)
                $returnData = array('type' => 'success', 'message' => 'Training group successfully removed!');
            else
                $returnData = array('type' => 'error', 'message' => 'Training group can\'t be deleted!');
        }
        else
            $returnData = array('type' => 'error', 'message' => 'Training group can\'t be deleted!');

        printSortResult($returnData);
        break;

    case 'addto':
        $users = (array)getRequest('users');
        $groupId = (int)getRequest('groupid');
        if ($groupId>0)
        {
            $dbtr = new DBTransaction();

            $ret = true;
            foreach ($users as $userId)
            {
                if ((int)$userId>0)
                {
                    if (!UserUserGroup::setUserGroup($groupId,$userId))
                    {
                        $ret=false;
                    }
                }

            }
            if ($ret)
            {
                $dbtr->destroy();

                $returnData = array('type' => 'success', 'message' => 'Users successfully added to group!');
            }
            else
            {
                $returnData = array('type' => 'error', 'message' => 'Users can\'t be added to group!');
            }
        }
        else
        {
            $returnData = array('type' => 'error', 'message' => 'Users can\'t be added to group!');
        }

        printSortResult($returnData);
        break;

    case 'removefrom':
        $users = (array)getRequest('users');

        $dbtr = new DBTransaction();

        $ret = true;

        foreach ($users as $userId)
        {
            if ((int)$userId>0)
            {
                if (!UserUserGroup::setUserGroup(0,$userId))
                    $ret = false;
            }
        }

        if ($ret)
        {
            $dbtr->destroy();

            $returnData = array('type' => 'success', 'message' => 'Users successfully removed from group!');
        }
        else
        {
            $returnData = array('type' => 'error', 'message' => 'Users can\'t be removed from group!');
        }

        printSortResult($returnData);

    break;
}

?>