<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');

include ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
include ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

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
        $mediaBoxesArray = array();
        if ($groupsWithMembers = UserTrainingGroup::getGroupsWithMembers($_SESSION['office_id'],$_SESSION['office_nametag']))
        {
            foreach ($groupsWithMembers as $row)
            {
                $mediaBoxesArray[] = array(

                    "id" => $row['groupid'],
                    "name" => $row['name'],
                    "doname" => $row['doname'],
                    "badge" => $row['badge'],
                    'members' => $row['members']
                );
            }
            $returnData['result'] = $mediaBoxesArray;
        }
        else
        {
            $returnData = '';//array();//array('type' => 'error', 'message' => 'No users!');
        }

        printSortResult($returnData);
    break;

    case 'new':
        if (($result = UserTrainingGroup::helperCreateNewUserTrainingGroup(getRequest('groupname'),$_SESSION['u_id'],$_SESSION['office_id'],$_SESSION['office_nametag'])) instanceof UserTrainingGroup)
        {
            $returnData = array(
                'type' => 'success',
                'message' => 'Traininggroup save was successfull!',
                'result'=> array(
                    'id'=>$result->getId(),
                    'name'=>$result->getDBField('name'),
                    'doname'=>$result->getDBField('doname')
                )
            );
        }
        else
        {
            $returnData = array('type' => 'error', 'message' => 'Training Group save was unsuccessfull!');
        }

        printSortResult($returnData);
    break;

    case 'rename':
        if (($groupId=(int)getRequest('pk'))>0 and strlen(($groupName=getRequest('value')))>0)
        {
            /*
             * trainer type office can not rename demogroup
             */
            $userTrainingGroupObject = new UserTrainingGroup($groupId);
            $groupArray = $userTrainingGroupObject->getDBFields();
            //printR($groupArray['doname']);
            if (Access::checkIsTrainerOffice() && $groupArray['doname'] == 'demogroup') {
                $returnData = array('type' => 'error', 'message' => 'This Training group can\'t be renamed!');
                printSortResult($returnData);
                exit;
            }

            if (($result = UserTrainingGroup::helperRenameUserTrainingGroup($groupId,$groupName)) instanceof UserTrainingGroup)
            {
                $returnData = array(
                    'type'=>'success',
                    'message'=>'Training group rename was successfull!',
                    'result'=>
                        array(
                            'sortname' => $result->getDBField('doname'),
                            'newname' => $result->getDBField('name')
                        )
                    );
            }
            else
            {
                $returnData = array('type'=>'error','message' => 'Training group rename was unsuccessfull!');
            }
        }
        else
        {
            $returnData = array('type'=>'error','message' => 'Training group rename was unsuccessfull!');
        }

        printSortResult($returnData);
    break;

    case 'delete':
        if (($groupId=(int)getRequest('id'))>0)
        {
            $userTrainingGroupObject = new UserTrainingGroup($groupId);
            /*
            * trainer type office can not delete demogroup
            */
            $groupArray = $userTrainingGroupObject->getDBFields();
            //printR($groupArray['doname']);
            if (Access::checkIsTrainerOffice() && $groupArray['doname'] == 'demogroup') {
                $returnData = array('type' => 'error', 'message' => 'This Training group can\'t be deleted!');
                printSortResult($returnData);
                exit;
            }

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
                    if (!UserTrainingGroup::addUserToGroup($groupId,$userId,$_SESSION['u_id'],$_SESSION['office_id'],$_SESSION['office_nametag']))
                    {
                        $ret=false;
                    }
                }

            }
            if ($ret)
            {
                $dbtr->destroy();

                $returnData = array('type' => 'success', 'message' => 'Users successfully added to training group!');
            }
            else
            {
                $returnData = array('type' => 'error', 'message' => 'Users can\'t be added to training group!');
            }
        }
        else
        {
            $returnData = array('type' => 'error', 'message' => 'Users can\'t be added to training group!');
        }

        printSortResult($returnData);
    break;

    case 'removefrom':
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
                    if (!UserTrainingGroup::removeUserFromGroup($groupId,$userId,$_SESSION['office_id'],$_SESSION['office_nametag']))
                        $ret = false;
                }
            }

            if ($ret)
            {
                $dbtr->destroy();

                $returnData = array('type' => 'success', 'message' => 'Users successfully removed from training group!');
            }
            else
            {
                $returnData = array('type' => 'error', 'message' => 'Users can\'t be removed from training group!');
            }

        }
        else
        {
            $returnData = array('type' => 'error', 'message' => 'Users can\'t be removed from training group!');
        }

        printSortResult($returnData);

        break;
}


?>