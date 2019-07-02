<?php

require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/class/class.encrypt.php');

$errorCodes = array(
    0 => 'Users created successfully',
    1 => 'Misspelled data sent to server',
    2 => 'The file is empty',
    3 => 'Upload failed!',
    4 => 'No header presented',
    5 => 'The following user exist:',
    6 => 'No data presented',
    7 => 'ERROR:invalid upload',
    8 => 'DB error (user save)',
    9 => 'DB error (department group save)'
);

$returnData = $userItems = $existsUserItems = $userEmailItems = $userEmailItems = array();

$tmpUser = new User($_SESSION['u_id']); $registrar = $tmpUser->getDBField('full_name'); unset($tmpUser);

if (isset($_POST['form']) && !empty($_POST['form'])) {

    if ($_FILES['files']['size'][0] > 0) {

        $file = $_FILES['files']['tmp_name'][0];

        $handle = fopen($file, "r");

        //load first row
        $header = fgetcsv($handle, 1000, ";", "'");

        //check if header presented?
        if ($header[0] == 'elotag')
        {
            while (($data = fgetcsv($handle, 1000, ";")) !== FALSE)
            {
                if (!empty($data))
                {
                    if (is_numeric($data[0]))   $data[0] = '';

                    //check if file is not UTF-8 encoded, convert text to it
                    for ($i = 0; $i < count($data); $i++)
                    {
                        if (mb_detect_encoding($data[$i], 'UTF-8', true) === FALSE)
                            $data[$i] = iconv('ISO-8859-2//IGNORE//TRANSLIT', 'UTF-8', $data[$i]);
                        $values[] = $data[$i];
                    }

                    //full_name
                    $elotag = (strlen(str_replace(' ', '', $values[0])) == 0 ? '' : $values[0] . ' ');
                    $fullname = str_replace("'", "", $elotag) . str_replace("'", "", $values[1]) . ' ' . str_replace("'", "", $values[2]);
                    //pwd
                    $pwd = rand_string(8);
                    //create activation code
                    $email = $values[3];
                    $key = uniqid("", true);
                    $enc = new Encryption();
                    $enc->addKey($email);
                    $encrypted = $enc->encode($key);

                    $userItem = array(
                        'elotag' => $values[0],
                        'keresztnev' => $values[2],
                        'vezeteknev' => $values[1],
                        'user_email' => $values[3],
                        'department' => (strlen(str_replace(' ', '', $values[4])) == 0 ? '' : $values[4]),
                        'position' => $values[5],
                        'birthDate' => $values[6],
                        'gender' => $values[7],
                        'language' => $values[8],
                        'schools' => $values[9],
                        'skills' => $values[10],
                        'createdDate' => date('Y-m-d H:j:s', time()),
                        'full_name' => $fullname,
                        'pwd' => HashPassword($pwd),
                        'password' => $pwd, //plaintext for mail
                        'userlevel' => 3,
                        'office_id' => $_SESSION['office_id'],
                        'office_nametag' => $_SESSION['office_nametag'],
                        'approved' => 0,
                        'user_type' => 'office',
                        'activeState' => 0,
                        'banned' => 0,
                        'activation_code' => $key,
                        'cryptedText' => $encrypted
                    );

                    if (!User::getUserObjectByEmail($values[3]))    $userItems[] = $userItem;
                    else    $existsUserItems[] = $userItem;
                }
                else
                    $errorCode = 6;

                $values = array();
            }
        }
        else
            $errorCode = 4;
    }
    else
        $errorCode = 2;
}
else
    $errorCode = 1;

if (!$errorCode)
{
    $dbtr = new DBTransaction();

    //ha nincs hiba, akkor indulhat a moka
    foreach ($userItems as $userData)
    {
        $tmpUser = new User(null);

        $tmpUser->setDBField('user_type',$userData['user_type']);
        $tmpUser->setDBField('office_id',$userData['office_id']);
        $tmpUser->setDBField('office_nametag',$userData['office_nametag']);
        $tmpUser->setDBField('elotag',$userData['elotag']);
        $tmpUser->setDBField('vezeteknev',$userData['vezeteknev']);
        $tmpUser->setDBField('keresztnev',$userData['keresztnev']);
        $tmpUser->setDBField('full_name',$userData['full_name']);
        $tmpUser->setDBField('user_email',$userData['user_email']);
        $tmpUser->setDBField('userlevel',$userData['userlevel']);
        $tmpUser->setDBField('pwd',$userData['pwd']);
        $tmpUser->setDBField('position',$userData['position']);
        $tmpUser->setDBField('birthDate',$userData['birthDate']);
        $tmpUser->setDBField('gender',$userData['gender']);
        $tmpUser->setDBField('language',$userData['language']);
        $tmpUser->setDBField('schools',$userData['schools']);
        $tmpUser->setDBField('skills',$userData['skills']);
        $tmpUser->setDBField('approved',$userData['approved']);
        $tmpUser->setDBField('activation_code',$userData['activation_code']);
        $tmpUser->setDBField('banned',$userData['banned']);
        $tmpUser->setDBField('cryptedText',$userData['cryptedText']);
        $tmpUser->setDBField('activeState',$userData['activeState']);

        if ($tmpUser->save())
        {
            if (strlen($department = $userData['department'])>0)
            {
                if ($groupObject = UserUserGroup::createNewOrLoadExistingGroupByName($department,$_SESSION['office_id'],$_SESSION['office_nametag'],$_SESSION['u_id']))
                {
                    UserUserGroup::setUserGroup($groupObject->getId(),$tmpUser->getId());
                }
                else
                    $errorCode = 9;
            }

            //all ok
            if (!$errorCode)
            {
                $tmpUser = new User($tmpUser->getId()); //refresh object

                ActionLogger::addToActionLog('users.csvupload.addnew',$tmpUser,'name:'.$tmpUser->getDBField('full_name').',email:'.$tmpUser->getDBField('user_email'));

                User::globalSkillUserAdd($tmpUser->getDBField('user_email'),$_SESSION['office_nametag']);

                $userEmailItems[]=$userData;

                $returnUserItems[] = array(
                    'id'        => $tmpUser->getId(),
                    'pre'       => $tmpUser->getDBField('elotag'),
                    'forname'   => $tmpUser->getDBField('keresztnev'),
                    'surname'   => $tmpUser->getDBField('vezeteknev'),
                    'fullname'  => $tmpUser->getDBField('full_name'),
                    'department'=> $tmpUser->getDBField('department'),
                    'registered'=> $tmpUser->getDBField('createdDate'),
                    'skills'    => $tmpUser->getDBField('skills'),
                    'img'       => '',
                    'email'     => $tmpUser->getDBField('user_email'),
                    'gender'    => $tmpUser->getDBField('gender'),
                    'birth'     => $tmpUser->getDBField('birthDate'),
                    'languages' => $tmpUser->getDBField('language'),
                    'schools'   => $tmpUser->getDBField('schools'),
                    'active'    => 'inactive'
                );

            }
        }
        else
            $errorCode = 8;


        if ($errorCode) break;

    }
}


if (!$errorCode)
{

    $dbtr->destroy(); //commit

    //send email messages
    foreach ($userEmailItems as $userEmailItem)
    {
        SkillMailer::sendRegisteredEmail(
                array(
                        'email'=>$userEmailItem['user_email'],
                        'name'=>$userEmailItem['full_name'],
                        'password'=>$userEmailItem['password']
                ),$registrar,$userEmailItem['cryptedText']);
    }

    $existsMessage='';

    if (count($existsUserItems)>0)
    {
        $tmpEmails=array();
        foreach ($existsUserItems as $existsUserItem)   $tmpEmails[] = $existsUserItem['user_email'];
        $existsMessage = ' '.$errorCodes[5].implode(', ',$tmpEmails);
        unset($tmpEmails);
    }

    if (count($userEmailItems)>0)
        $returnData = array('type'=>'success','result'=>$returnUserItems,'message'=>$errorCodes[0].$existsMessage);
    else
        $returnData = array('type'=>'success','result'=>array(),'message'=>$existsMessage);
}
else
    $returnData = array('type'=>'error','message'=>$errorCodes[$errorCode]);


printSortResult($returnData);

exit;
?>