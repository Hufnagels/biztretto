<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/class/class.encrypt.php');

$protocol = connectionType();

$returnData = array();

switch (getRequest('action'))
{
    case 'load':
        //printR($_POST);
        $sql = "SELECT
                u_id, elotag, vezeteknev, keresztnev, full_name, user_email, createdDate, ctime, profilePicture,
                department, gender, birthDate, language, schools, position, activeState
                FROM user_u
                WHERE deleted=0 AND isvisible =1 AND userlevel > ".DEMO_USER_LEVEL." AND office_id = " . MySQL::filter($_SESSION['office_id']) . " AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'
                ORDER BY full_name
                LIMIT " . MySQL::filter($_POST['from']) . ",50";

        $rows = MySQL::resultArray(MySQL::executeQuery($sql),MySQL::fmAssoc);

        $usersArray = array();
        $urlRegex = URL_REGEX;

        foreach ($rows as &$row) {
            $usersArray[] = array(
                'id'         => $row['u_id'],
                'fullname'   => $row['full_name'],
                'name'       => normalize_special_characters(str_replace(' ', '', strtolower($row['vezeteknev']))),
                'department' => !$row['department'] ? '' : UserUserGroup::helperGetGroupNameByGroupId($row['department']),
                'doname'     => $row['department'],
                'registered' => date('Y.m.d', strtotime($row['createdDate'])),
                'skills'     => array(),
                'img'        => (is_null($row['profilePicture']) || $row['profilePicture'] == "") ? '' : ($row['profilePicture']),
                'email'      => $row['user_email'],
                'active'     => ($row['activeState'] == 1) ? 'active' : 'inactive'
            );
        }
        printResult($usersArray);
    break;
    case 'loadone':
        if (($userId = (int)getRequest('id'))>0)
        {
            $tmpUserObject = new User($userId);

            $userArray = array(
                'id'            => $tmpUserObject->getId(),
                'pre'           => $tmpUserObject->getDBField('elotag'),
                'forname'       => $tmpUserObject->getDBField('keresztnev'),
                'surname'       => $tmpUserObject->getDBField('vezeteknev'),
                'fullname'      => $tmpUserObject->getDBField('full_name'),
                'department'    => !$tmpUserObject->getDBField('department') ? '' : UserUserGroup::helperGetGroupNameByGroupId($tmpUserObject->getDBField('department')),
                'doname'        => $tmpUserObject->getDBField('department'),
                'registered'    => date('Y.m.d', strtotime($tmpUserObject->getDBField('createdDate'))),
                'skills'        => array(),
                'img'           => (is_null($row['profilePicture']) || $row['profilePicture'] == "") ? ($row['gender'] == 'male' ? User::IMG_MALE : User::IMG_FEMALE) : ($row['profilePicture']),
                'email'         => $tmpUserObject->getDBField('user_email'),
                'gender'        => $tmpUserObject->getDBField('gender'),
                'birth'         => !$tmpUserObject->getDBField('birthDate') ? '' : date('Y.m.d', strtotime($tmpUserObject->getDBField('birthDate'))),
                'languages'     => $tmpUserObject->getDBField('language'),
                'schools'       => $tmpUserObject->getDBField('schools'),
                'position'      => $tmpUserObject->getDBField('position'),
                'active'        => ($tmpUserObject->getDBField('activeState') == 1) ? 'active' : 'inactive',
                'userlevel'     => $tmpUserObject->getDBField('userlevel'),
                'userlevelText' => ($tmpUserObject->getDBField('userlevel') == 3) ? 'user' : (($tmpUserObject->getDBField('userlevel') == 5) ? 'editor' : 'admin')
            );

            $returnData = array('result'=>array($userArray));
        }
        else
            $returnData = array('type'=>'error','message'=>'Param error!');

        printSortResult($returnData);
    break;

    case 'active':
    case 'inactive':
        $setActive = (int)(getRequest('action')=='active');

        if (getRequest('users'))
        {
		
            $userIds = (array)getRequest('users');
            $error = false;
            $dbtr=new DBTransaction();

            foreach ($userIds as $userId)
            {
                $tmpUserObject = new User((int)$userId);
                $tmpUserObject->setDBField('activeState',$setActive);
                $tmpUserObject->setDBField('approved',$setActive);

                //$tmpUserObject->setDBField('active', $tmpUserObject->getDBField('activeState') == 1 ? 'active' : 'inactive');
                if (!$tmpUserObject->save())
                    $error = true;
            }
            if (!$error)
            {
                $dbtr->destroy();
                $returnData = array('type'=>'success','message'=>($setActive?'Ac':'Inac').'tivation success!');
            }
            else
            {
                $returnData = array('type'=>'error','message'=>($setActive?'Ac':'Inac').'tivation unsuccess!');
            }
        }
        else
            $returnData = array('type'=>'error','message'=>($setActive?'Ac':'Inac').'tivation unsuccess!');

        printSortResult($returnData);
    break;
    case 'delete':
        if (getRequest('users'))
        {
            $userIds = (array)getRequest('users');
            $error = false;
            $dbtr=new DBTransaction();

            foreach ($userIds as $userId)
            {
                $tmpUserObject = new User((int)$userId);
                if (!$tmpUserObject->remove())
                    $error = true;
            }
            if (!$error)
            {
                $dbtr->destroy();
                $returnData = array('type'=>'success','message'=>'Remove success!');
            }
            else
            {
                $returnData = array('type'=>'error','message'=>'Remove unsuccess1!');
            }
        }
        else
            $returnData = array('type'=>'error','message'=>'Remove unsuccess!');

        printSortResult($returnData);

    break;
    case 'new':
        $dbtr = new DBTransaction();

        $userPostData = createArrayFromPostNV('id');

        if (User::getUserObjectByEmail($userPostData['email']))
        {
            $returnData = array(
                'type' => 'error',
                'message' => 'E-mail already exists!');
        }
        elseif (strlen($userPostData['email'])==0)
        {
            $returnData = array(
                'type' => 'error',
                'message' => 'E-mail is empty!');
        }
        else
        {
            $tmpUserObject = new User(null);

            $elotag = (strlen(str_replace(' ', '', $userPostData['elotag'])) == 0 ? '' : $userPostData['elotag'] . ' ');
            $fullName = $elotag . $userPostData['keresztnev'] . ' ' . $userPostData['vezeteknev'];

            $tmpUserObject->setDBField('elotag',$userPostData['elotag']);
            $tmpUserObject->setDBField('vezeteknev',$userPostData['vezeteknev']);
            $tmpUserObject->setDBField('keresztnev',$userPostData['keresztnev']);
            $tmpUserObject->setDBField('full_name',$fullName);
            $tmpUserObject->setDBField('gender',$userPostData['gender']);
            $tmpUserObject->setDBField('user_email',$userPostData['email']);
            if ($departmentUserUserGroupObject = UserUserGroup::createNewOrLoadExistingGroupByName($userPostData['department'],$_SESSION['office_id'],$_SESSION['office_nametag'],$_SESSION['u_id']))
            {
                $tmpUserObject->setDBField('department',$departmentUserUserGroupObject->getId());
            }

            $tmpUserObject->setDBField('position',$userPostData['position']);
            $tmpUserObject->setDBField('birthDate',$userPostData['birthDate']?$userPostData['birthDate']:null);
            $tmpUserObject->setDBField('language',$userPostData['language']);
            $tmpUserObject->setDBField('schools',$userPostData['schools']);
            $tmpUserObject->setDBField('skills',$userPostData['skills']);
            $tmpUserObject->setDBField('user_type','office');
            $tmpUserObject->setDBField('userlevel',3);
            $tmpUserObject->changePassword($userPassword = rand_string(8));

            $tmpUserObject->setDBField('office_id',$_SESSION['office_id']);
            $tmpUserObject->setDBField('office_nametag',$_SESSION['office_nametag']);
            $tmpUserObject->setDBField('activeState',0);

            $tmpUserObject->setDBField('active', $tmpUserObject->getDBField('activeState') == 1 ? 'active' : 'inactive');

            //create activation code
            $key = uniqid('', true);$enc = new Encryption();$enc->addKey($tmpUserObject->getDBField('user_email'));
            $encrypted = $enc->encode($key);

            $tmpUserObject->setDBField('activation_code',$key);
            $tmpUserObject->setDBField('cryptedText',$encrypted);

            if ($res = $tmpUserObject->save())
            {
                User::globalSkillUserAdd($tmpUserObject->getDBField('user_email'),$_SESSION['office_nametag']);

                $tmpAdminUserObject = new User($_SESSION['u_id']);

                SkillMailer::sendRegisteredEmail(array('email'=>$tmpUserObject->getDBField('user_email'),'name'=>$fullName,'password'=>$userPassword),$tmpAdminUserObject->getDBField('full_name'),$encrypted);

                $dbtr->destroy();

                //refresh user
                $tmpUserObject = new User($tmpUserObject->getId());

                $usersArray = array(array(
                    'id'         => $tmpUserObject->getId(),
                    'fullname'   => $tmpUserObject->getDBField('full_name'),
                    'name'       => normalize_special_characters(str_replace(' ', '', strtolower($tmpUserObject->getDBField('full_name')))),
                    'department' => !$tmpUserObject->getDBField('department') ? '' : UserUserGroup::helperGetGroupNameByGroupId($tmpUserObject->getDBField('department')),
                    'doname'     => $tmpUserObject->getDBField('department'),
                    'registered' => date('Y.m.d', strtotime($tmpUserObject->getDBField('createdDate'))),
                    'img'        => !$tmpUserObject->getDBField('profilePicture') ? '' : ($tmpUserObject->getDBField('profilePicture')),
                    'email'      => $tmpUserObject->getDBField('user_email'),
                    'active'     => ($tmpUserObject->getDBField('activeState') == 1) ? 'active' : 'inactive'
                ));

                $returnData = array(
                    'type' => 'success',
                    'message' => 'User created. Email from registration sended',
                    'result'=> $usersArray
                    );

            }
            else
            {
                $returnData = array(
                    'type' => 'error',
                    'message' => 'User creation failed');
            }
        }

        printSortResult($returnData);

    break;
    case 'changeData':
    case 'changePassword':

        $result = false;

        if (getRequest('pk')>0)
        {
            $user = new User(getRequest('pk'));

            if (getRequest('action') == 'changeData')
            {
                if (getRequest('name')=='gender')           $user->setDBField(getRequest('name'),getRequest('value')==1?'Male':'Female');
                if (getRequest('name')=='birth')            $user->setDBField('birthDate',getRequest('value'));
                if (getRequest('name')=='language')         $user->setDBField(getRequest('name'),strip_tags(purifyString(@implode(',',($_POST['value'])))));
                if (getRequest('name')=='pemail')           $user->setDBField(getRequest('name'),strip_tags(purifyString(getRequest('value'))));
                if (getRequest('name')=='skills')           $user->setDBField(getRequest('name'),strip_tags(purifyString(getRequest('value'))));
                if (getRequest('name')=='schools')          $user->setDBField(getRequest('name'),strip_tags(purifyString(getRequest('value'))));
                if (getRequest('name')=='cv')               $user->setDBField(getRequest('name'),purifyString(getRequest('value')));
                if (getRequest('name')=='description')      $user->setDBField(getRequest('name'),purifyString(getRequest('value')));
                if (getRequest('name')=='profilePicture')   $user->setDBField(getRequest('name'),getRequest('value'));
                if (getRequest('name')=='userlevel')        $user->setDBField('userlevel',getRequest('value'));

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
                $jsonResultArray = array(
                    'type' => 'success',
                    'message' => 'Data successfully updated');
            }
            else
            {
                $jsonResultArray = array(
                    'type' => 'error',
                    'message' => 'Data update failed');
            }
        }
        else
            $jsonResultArray = array(
                'type' => 'error',
                'message' => 'Data update failed');


        printSortResult($jsonResultArray);
    break;
}

exit;
/*
 * a js-ben lecseréltem a $_post[load/activate, ....] $_post[action] = load, save, rename,
 * actions = {
            department:{0:'load',1:'delete', 2:'rename', 3:'new', 4:'update'},
            groups:{0:'load',1:'delete', 2:'rename', 3:'new', 4:'update'},
            users:{0:'load',1:'delete', 2:'update', 3:'new', 4:'activate', 5:'deactivate', 6:'loadone',7:'groupChange'}
        }
 */
if (isset($_POST['form']) && !empty($_POST['form'])) {
    $formArray = array();
    for ($i = 0; $i < count($_POST['form']); $i++) {
        $formArray[$_POST['form'][$i]['name']] = $_POST['form'][$i]['value'];
    }

    //////////////////////////////////////////////////////////
    //activate users
    if (isset($_POST['active'])) {
        if (!empty($_POST['id'])) {
            if (isset($formArray['owner'])) unset($formArray['owner']);
            $userslist = implode(',', $_POST['id']);
            $userslist = MySQL::filter($userslist);
            $datetime = date('Y-m-d H:i:s', time());
            $sql = " UPDATE user_u SET approved = 1, activeState = 1, activation_time = '" . $datetime . "' WHERE
        u_id in (" . $userslist . ") AND office_id = " . $formArray['office_id'] . " AND office_nametag = '" . $formArray['office_nametag'] . "'";
            $result = MySQL::execute($sql);
//printR($result);

            if ($result == TRUE)
                $message = array('type' => 'success', 'message' => 'Activation success!', 'type2' => 'active');
            else
                $message = array('type' => 'error', 'message' => 'Activation unsuccess!');

            $returnData = $message;
            printSortResult($returnData);
        } else {
            $message = array('type' => 'error', 'message' => 'No data was sended1!');
            //$returnArray['message'] = $message;
            $returnData = $message;
            printSortResult($returnData);
        }
    }
    //////////////////////////////////////////////////////////
    //deactivate users
    if (isset($_POST['inactive'])) {
        if (!empty($_POST['id'])) {
            if (isset($formArray['owner'])) unset($formArray['owner']);
//printR($_POST['id']);
            $userslist = implode(',', $_POST['id']);
            $userslist = MySQL::filter($userslist);
            $sql = " UPDATE user_u SET activeState = 0 WHERE
        u_id in (" . $userslist . ") AND office_id = " . $formArray['office_id'] . " AND office_nametag = '" . $formArray['office_nametag'] . "'";
            $result = MySQL::execute($sql);
//printR($result);

            if ($result == TRUE)
                $message = array('type' => 'success', 'message' => 'Inctivation success!', 'type2' => 'inactive');
            else
                $message = array('type' => 'error', 'message' => 'Inctivation unsuccess!');

            $returnData = $message;
            printSortResult($returnData);
        } else {
            $message = array('type' => 'error', 'message' => 'No data was sended1!');
            //$returnArray['message'] = $message;
            $returnData = $message;
            printSortResult($returnData);
        }
    }

    //////////////////////////////////////////////////////////
    //delete users
    if (isset($_POST['delete'])) {
        if (!empty($_POST['id'])) {
            if (isset($formArray['owner'])) unset($formArray['owner']);
//printR($_POST);
            $del_id = array();
            foreach ($_POST['id'] as $k => $v)
                $del_id[] = MySQL::filter($v);
            $delIds = implode(',', $del_id);
            $sqlDelete = "DELETE FROM user_u WHERE u_id IN (" . $delIds . ") AND office_id = " . $formArray['office_id'] . " AND office_nametag = '" . $formArray['office_nametag'] . "'";
            $result = MySQL::execute1($sqlDelete);
            $sqlDelete = "DELETE FROM user_usergroupUsers WHERE u_id IN (" . $delIds . ") AND office_id = " . $formArray['office_id'] . " AND office_nametag = '" . $formArray['office_nametag'] . "'";
            $result = MySQL::execute1($sqlDelete);

            //training groupbol is torolni kell
            $delID = MySQL::filter($_POST['id'][0]['id']);
            $sqlDelete = "DELETE FROM user_traininggroupusers WHERE u_id IN (" . $delIds . ") AND office_id = " . $formArray['office_id'] . " AND office_nametag = '" . $formArray['office_nametag'] . "'";
            $result = MySQL::execute($sqlDelete);


//printR( $result);
            if ($result == TRUE)
                $message = array('type' => 'success', 'message' => 'Delete was successfully!', 'type2' => 'delete');
            else
                $message = array('type' => 'error', 'message' => 'Nothing to delete yet!!!!!!!');

            $returnData = $message;
            printSortResult($returnData);
        } else {
            $message = array('type' => 'error', 'message' => 'No data was sended1!');
            //$returnArray['message'] = $message;
            $returnData = $message;
            printSortResult($returnData);
        }
    }

    //////////////////////////////////////////////////////////
    //remove users from group (department / training group
    if (isset($_POST['remove'])) {
        if (!empty($_POST['id'])) {
            if (isset($formArray['owner'])) unset($formArray['owner']);

            $del_id = array();
            foreach ($_POST['id'] as $k => $v)
                $del_id[] = MySQL::filter($v);

//printR($del_id);
//exit;      
            $delIds = implode(',', $del_id);

            // usergroupból veszem ki, akkor a department tagot nullázom
            if ($_POST['remove'] == 'usersGroupList') {
                $sqlUpdate = "UPDATE user_u SET department = '' WHERE u_id IN (" . $delIds . ") AND office_id = " . $formArray['office_id'] . " AND office_nametag = '" . $formArray['office_nametag'] . "'";
                $result = MySQL::execute1($sqlUpdate);
//printR($result);
                //törlöm a usergroupUser táblából
                $sqlDelete = "DELETE FROM user_usergroupUsers WHERE u_id IN (" . $delIds . ") AND office_id = " . $formArray['office_id'] . " AND office_nametag = '" . $formArray['office_nametag'] . "'";
                $result = MySQL::execute1($sqlDelete);
            } else if ($_POST['remove'] == 'trainingGroupList') {

                //training groupbol is torolni kell
                $delID = MySQL::filter($_POST['id'][0]['id']);
                $sqlDelete = "DELETE FROM user_traininggroupusers WHERE u_id IN (" . $delIds . ") AND office_id = " . $formArray['office_id'] . " AND office_nametag = '" . $formArray['office_nametag'] . "'";
                $result = MySQL::execute($sqlDelete);
            }

//printR( $result);
            if ($result == true)
                $message = array('type' => 'success', 'message' => 'Delete was successfully!');
            else
                $message = array('type' => 'error', 'message' => 'Delete was unsuccessfully!');

            //$returnArray['message'] = $message;
            $returnData = $message;
            printSortResult($returnData);
        } else {
            $message = array('type' => 'error', 'message' => 'No data was sended1!');
            //$returnArray['message'] = $message;
            $returnData = $message;
            printSortResult($returnData);
        }
    }
    //////////////////////////////////////////////////////////
    //update users group/department in user_u and user_usergroupUsers
    //check if user prev dept was empty
    if (isset($_POST['groupChange'])) {
        if (!empty($_POST['id'])) {
            //if (isset($formArray['owner'])) unset($formArray['owner']);
            $oid = $formArray['office_id'];
            $ont = $formArray['office_nametag'];
            $ow = $formArray['owner'];
            $users = array();
            $onDuplicate = array();
            foreach ($_POST['id'] as $row) {
                $users[] = $row['id'];
                $groupName = $row['groupName'];
                $groupid = $row['groupid'];
                $onDuplicate[] = "(" . $row['id'] . ", " . $groupid . ", " . $oid . ", '" . $ont . "', " . $ow . ")";
            }
            $sql = "
        UPDATE user_u 
        SET department = '" . $groupName . "'
        WHERE u_id IN (" . implode(',', $users) . ") AND office_id = " . $oid . " AND office_nametag = '" . $ont . "'
      ";
            $result = MySQL::execute($sql);
            if ($result == TRUE) {
                $sqlReplace = "
          INSERT into user_usergroupUsers 
          ( u_id, usergroup_id, office_id, office_nametag, owner) 
          VALUES 
          " . implode(',', $onDuplicate) . "
          ON DUPLICATE KEY 
          UPDATE usergroup_id = VALUES(usergroup_id)
        ";
//printR( $sqlReplace );
                $result = MySQL::execute1($sqlReplace);
//printR( $result );
                if ($result == TRUE)
                    $message = array('type' => 'success', 'message' => 'Membership successfully updated!');
                else
                    $message = array('type' => 'error', 'message' => 'Update membership was unsuccessfull!');

                $returnData = $message; //$returnArray;
                printSortResult($returnData);
            } else //user tablaban nem sikerült
                $message = array('type' => 'error', 'message' => 'Update membership was unsuccessfull!');

            //$returnArray['message'] = $message;
            $returnData = $message; //$returnArray;
            printSortResult($returnData);
        } else {
            $message = array('type' => 'error', 'message' => 'No data was sended1!');
            //$returnArray['message'] = $message;
            $returnData = $message; //$returnArray;
            printSortResult($returnData);
        }
    }

    //////////////////////////////////////////////////////////
    //load existing users
    if (isset($_POST['action']) && $_POST['action'] == 'load') {
        if (isset($formArray['id'])) unset($formArray['id']);
        if (isset($formArray['name'])) unset($formArray['name']);
        if (isset($formArray['owner'])) unset($formArray['owner']);

        $options = array(
            'table' => 'user_u',
            'fields' => 'u_id, elotag, vezeteknev, keresztnev, full_name, user_email, createdDate, ctime, profilePicture, department, gender, birthDate, language, schools, position, activeState',
            'condition' => $formArray,
            'conditionExtra' => '', //"name LIKE '%".$newDiskareaName['name']."%'",
            'order' => 'full_name',
            'limit' => 100
        );
        //$result = MySQL::select($options);
        $sql = "SELECT u_id, elotag, vezeteknev, keresztnev, full_name, user_email, createdDate, ctime, profilePicture,
    department, gender, birthDate, language, schools, position, activeState FROM user_u WHERE office_id = "
            . $_SESSION['office_id'] . " AND office_nametag = '" . $_SESSION['office_nametag'] . "' ORDER BY full_name";
        $result = MySQL::query($sql, false, false);


        $usersArray = array();
        $db = count($result);
        $urlRegex = URL_REGEX;

        foreach ($result as &$row) {
            $usersArray[] = array(
                'id' => $row['u_id'],
                /*'pre' => $row['elotag'],
                'forname' => $row['keresztnev'],
                'surname' => $row['vezeteknev'],*/
                'fullname' => $row['full_name'],
                'name' => normalize_special_characters(str_replace(' ', '', strtolower($row['full_name']))),
                'department' => (is_null($row['department']) || $row['department'] == '') ? '' : $row['department'],
                'doname' => is_null($row['department']) ? '' : normalize_special_characters(str_replace(' ', '', strtolower($row['department']))),
                'registered' => date('Y.m.d', strtotime($row['createdDate'])),
                'skills' => array(),
                'img' => (is_null($row['profilePicture']) || $row['profilePicture'] == "") ? '' : ($row['profilePicture']),
                'email' => $row['user_email'],
                /*'gender' => $row['gender'],
                //ell hogy nem 0000.00
                'birth' => ($row['birthDate'] == '0000-00-00') ? '' : date('Y.m.d', strtotime($row['birthDate'])),
                'languages' => $row['language'], //$row['language'],
                'schools' => $row['schools'], //$row['schools'],
                'position' => $row['position'], //$row['position']*/
                'active' => ($row['activeState'] == 1) ? 'active' : 'inactive'
            );
        }
        printResult($usersArray);

//print_r( $result);
//exit;
    }

    //////////////////////////////////////////////////////////
    //load selected users
    if (isset($_POST['action']) && $_POST['action'] == 'loadone' && !empty($_POST['id'])) {
//print_r( $_POST);
        if (isset($formArray['id'])) unset($formArray['id']);
        if (isset($formArray['name'])) unset($formArray['name']);
        if (isset($formArray['owner'])) unset($formArray['owner']);

        //$result = MySQL::select($options);
        $sql = "
            SELECT u_id, elotag, vezeteknev, keresztnev, full_name, user_email, createdDate, ctime, profilePicture, department, gender, birthDate, language, schools, position, activeState
            FROM user_u
            WHERE u_id = " . $_POST['id'] . " AND office_id = " . $_SESSION['office_id'] . " AND office_nametag = '" .
            $_SESSION['office_nametag'] . "'
            ORDER BY full_name LIMIT 1";

        $result = MySQL::query($sql, false, false);

//print_r( $result);
        $usersArray = array();
        $db = count($result);
        $urlRegex = URL_REGEX;

        foreach ($result as &$row) {
            $usersArray[] = array(
                'id' => $row['u_id'],
                'pre' => $row['elotag'],
                'forname' => $row['keresztnev'],
                'surname' => $row['vezeteknev'],
                'fullname' => $row['full_name'],
                'department' => (is_null($row['department']) || $row['department'] == '') ? '' : $row['department'],
                'doname' => is_null($row['department']) ? '' : normalize_special_characters(str_replace(' ', '', strtolower($row['department']))),
                'registered' => date('Y.m.d', strtotime($row['createdDate'])),
                'skills' => array(),
                'img' => (is_null($row['profilePicture']) || $row['profilePicture'] == "") ? ($row['gender'] == "férfi" ? $male : $female) : ($row['profilePicture']),
                'email' => $row['user_email'],
                'gender' => $row['gender'],
                //ell hogy nem 0000.00
                'birth' => ($row['birthDate'] == '0000-00-00') ? '' : date('Y.m.d', strtotime($row['birthDate'])),
                'languages' => $row['language'], //$row['language'],
                'schools' => $row['schools'], //$row['schools'],
                'position' => $row['position'], //$row['position']
                'active' => ($row['activeState'] == 1) ? 'active' : 'inactive'
            );
        }
        printResult($usersArray);

//print_r( $result);
//exit;
    }
    //////////////////////////////////////////////////////////
    //save individual user
    if (isset($_POST['save'])) {
        if (!empty($_POST['id'])) {

            //get registar user
            $sqlRegistrar = "SELECT full_name FROM user_u WHERE u_id = " . $formArray['owner'] . " AND office_id = " . $formArray['office_id'] . " LIMIT 1";
            $registrar1 = MySQL::query($sqlRegistrar, false, false);
            $registrar = $registrar1[0]['full_name'];

            $formUser = array();
            for ($i = 0; $i < count($_POST['id']); $i++) {
                $formUser[$_POST['id'][$i]['name']] = $_POST['id'][$i]['value'];
            }

            if (!is_null($formUser['vezeteknev']) && !is_null($formUser['keresztnev']) && !is_null($formUser['gender']) && !is_null($formUser['email'])) {
                $elotag = (strlen(str_replace(' ', '', $formUser['elotag'])) == 0 ? '' : $formUser['elotag'] . ' ');
                $fullname = $elotag . $formUser['vezeteknev'] . ' ' . $formUser['keresztnev'];
                $formUser['full_name'] = $fullname;
                $formUser['gender'] = ($formUser['gender'] == 'male') ? 'férfi' : 'nő';
                $formUser['user_email'] = $formUser['email'];
                $owner = $formArray['owner'];
                unset($formUser['email']);
                unset($formUser['something']);
                if (isset($formArray['owner'])) unset($formArray['owner']);

                //pwd
                $pwd = rand_string(8);
                $oid = $formArray['office_id'];
                $ont = $formArray['office_nametag'];
                //create activation code
                $email = $formUser['user_email'];
                $key = uniqid("", true);
                $enc = new Encryption();
                $enc->addKey($email);
                $encrypted = $enc->encode($key);

                //data to store in db
                $array_of_values = array(
                    'elotag' => $formUser['elotag'],
                    'keresztnev' => $formUser['keresztnev'],
                    'vezeteknev' => $formUser['vezeteknev'],
                    'user_email' => $formUser['user_email'],
                    'department' => $formUser['department'],
                    'birthDate' => $formUser['birth'],
                    'gender' => $formUser['gender'],
                    'language' => $formUser['language'],
                    'schools' => $formUser['schools'],
                    'skills' => $formUser['skills'],
                    'createdDate' => date('Y-m-d H:j:s', time()),
                    'full_name' => $fullname,
                    'pwd' => HashPassword($pwd),
                    'userlevel' => 3,
                    'office_id' => $oid,
                    'office_nametag' => $ont,
                    'approved' => 0,
                    'user_type' => 'office',
                    'activeState' => 0,
                    'banned' => 0,
                    'activation_code' => $key,
                    'cryptedText' => $encrypted
                );
/*
 * ezt tettem itt bele teszt miatt
 */
$returnArray['result'] = $array_of_values;
$returnArray['group'] = $returnGroup;
                $returnData = array(
                    'type' => 'success',
                    'message' => 'User created. Email from registration sended',
                    'result'=> array(
                        'id'=>12,
                        'name'=>'',
                        'doname'=>''
                    )
                );
$returnArray['message'] = array('type' => 'success', 'message' => 'User created. Email from registration sended');
printSortResult($returnArray);
exit;
                //check if exist or not?
                $sql = "SELECT full_name, user_email FROM user_u WHERE office_id = " . $_SESSION['office_id'] . " AND office_nametag = '" . $_SESSION['office_nametag'] . "' AND full_name = '" . $fullname . "' AND user_email = '" . $formUser['user_email'] . "'";
                $rr = MySQL::query($sql, false, false);

                if (empty($rr[0])) { //user dosen't exist
                    $resUser = MySQL::insertIgnore('user_u', $array_of_values);
//$resUser = 1;
                    if (is_numeric($resUser)) {
                        User::globalSkillUserAdd($array_of_values['user_email'],$_SESSION['office_nametag']);
                        //insert success
                        //check if department is not empty add row to table if not exist
                        $returnGroup = array();
                        if (!is_null($array_of_values['department']) &&
                            ($array_of_values['department'] != '') &&
                            strlen(str_replace(' ', '', $array_of_values['department'])) > 0
                        ) {
                            $array_of_values2 = array(
                                'name' => $array_of_values['department'],
                                'office_id' => $oid,
                                'office_nametag' => $ont,
                                //'createdDate'=>date('Y-m-d H:j:s', time()),
                                'owner' => $owner
                            );
                            //mégegyszer ua groupot nem hozom létre
                            $resGroup = MySQL::insertIgnore('user_usergroup', $array_of_values2);
                            //ha mar letezik a usergroup, akkor lekerdezem az ID-jét
                            $typeOfGroup = 'new';
                            if (is_numeric($resGroup) && $resGroup == 0) {
                                $tres = MySQL::query("SELECT usergroup_id FROM user_usergroup WHERE name ='" . $array_of_values['department'] . "' AND office_id = " . $oid . " AND office_nametag = '" . $ont . "'", false, false);
                                $resGroup = $tres[0]['usergroup_id'];
                                //$returnGroup = array('id'=> $resGroup, 'name'=> $array_of_values['department']);
                                $typeOfGroup = 'exist';
                            }

                            $returnGroup = array('type' => $typeOfGroup, 'id' => $resGroup, 'name' => $array_of_values['department']);

                            //insert user into usergroupUsers table
                            $array_of_values3 = array(
                                'usergroup_id' => $resGroup,
                                'u_id' => $resUser,
                                'office_id' => $oid,
                                'office_nametag' => $ont,
                                //'createdDate'=>date('Y-m-d H:j:s', time()),
                                'owner' => $owner
                            );
                            $res2 = MySQL::insertIgnore('user_usergroupUsers', $array_of_values3);

                        }

                        //create user array for sending email
                        $newUserArray = array();
                        //$newUserArray[] = array('name' =>$fullname, 'email' =>$array_of_values['user_email'],'password' =>$pwd);
                        $newUserArray[] = array('forname' => $formUser['keresztnev'], 'name' => $fullname, 'email' => $array_of_values['user_email'], 'password' => $pwd);

                        //create retur array for user
                        $usersArray[] = array(
                            'id' => $resUser,
                            'pre' => $array_of_values['elotag'],
                            'forname' => $array_of_values['keresztnev'],
                            'surname' => $array_of_values['vezeteknev'],
                            'fullname' => $array_of_values['full_name'],
                            'department' => $array_of_values['department'],
                            'doname' => is_null($array_of_values['department']) ? '' : normalize_special_characters(str_replace(' ', '', strtolower($array_of_values['department']))),
                            'registered' => date('Y.m.d', strtotime($array_of_values['createdDate'])),
                            'skills' => $array_of_values['skills'],
                            'img' => '', //$array_of_values['user_email'],
                            'email' => $array_of_values['user_email'],
                            'gender' => $array_of_values['gender'],
                            'birth' => $array_of_values['birthDate'],
                            'languages' => $array_of_values['language'],
                            'schools' => $array_of_values['schools'],
                            'active' => 'inactive'
                        );

                        $returnArray['users']['result'] = $usersArray;
                        $returnArray['group'] = $returnGroup;
//printR($newUserArray);
                        foreach ($newUserArray as $user)
                            SkillMailer::sendRegisteredEmail($user, $registrar, $encrypted);
                        $returnArray['message'] = array('type' => 'success', 'message' => 'User created. Email from registration sended');
                        //$returnData = $message;
                        printSortResult($returnArray);

                    } else {
                        $message = array('type' => 'error', 'message' => 'Insert user unsuccessfull');
                    }
                } else {
                    $message = array('type' => 'error', 'message' => $fullname . ' - User exist');
                }
                /**/
                //printR( $formUser );
            } else {
                $message = array('type' => 'error', 'message' => 'Main data is not correct');
            }

            //$returnArray['message'] = $message;
            $returnData = $message;
            printSortResult($returnData);
        } else {
            $message = array('type' => 'error', 'message' => 'No data was sendedsave!');
            //$returnArray['message'] = $message;
            $returnData = $message;
            printSortResult($returnData);
        }
    }

    //update user data
    if (isset($_POST['update']) && $_POST['update'] == 'update') {
        if (!empty($_POST['id'])) {
            if (isset($formArray['owner'])) unset($formArray['owner']);
            $userArray = array();
            for ($i = 0; $i < count($_POST['id']); $i++) {
                $userArray[$_POST['id'][$i]['name']] = $_POST['id'][$i]['value'];
            }
            if (isset($userArray['email'])) unset($userArray['email']);
            if (isset($userArray['id'])) {
                $formArray['u_id'] = $userArray['id'];
                unset($userArray['id']);
            }
//printR($formArray);
            $result = MySQL::update('user_u', $userArray, $formArray);
//printR( $result );
            if ($result == TRUE)
                $message = array('type' => 'success', 'message' => 'Data updated!');
            else
                $message = array('type' => 'error', 'message' => 'Can\t be updated!');

            $returnArray['message'] = $message;
            $returnData = $returnArray;
            printSortResult($message);

        } else {
            $message = array('type' => 'error', 'message' => 'No data was sendedupdate!');
            //$returnArray['message'] = $message;
            $returnData = $message;
            printSortResult($returnData);
        }
    }

} else {
    $message = array('type' => 'error', 'message' => 'No data was sendedmain!');
    //$returnArray['message'] = $message;
    $returnData = $message;
    printSortResult($returnData);
}
$_SESSION['LAST_ACTIVITY'] = time();
exit;

header('Pragma: no-cache');
header('Cache-Control: no-store, no-cache, must-revalidate');
header('Content-Disposition: inline; filename="files.json"');
// Prevent Internet Explorer from MIME-sniffing the content-type:
header('X-Content-Type-Options: nosniff');
header('Access-Control-Allow-Credentials:false');
header('Access-Control-Allow-Headers:Content-Type, Content-Range, Content-Disposition, Content-Description');
header('Access-Control-Allow-Origin:*');
header('Content-type: application/json');
header('Expires:Thu, 19 Nov 1981 08:52:00 GMT');
header('Keep-Alive:timeout=15, max=100');
header('Pragma:no-cache');
$resF['result'] = $returnData;
$json = json_encode($resF, true);
echo $json;

?>