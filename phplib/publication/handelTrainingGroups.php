<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');


if (isset($_POST['form']) && !empty($_POST['form'])) {
    $formArray = createArrayFromPostNV();

    //load training group
    if (isset($_POST['action']) && $_POST['action'] == 'load') {

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
    }
} else {
    $returnData = array('type' => 'error', 'message' => 'Misspelled data sent to server!');
    printSortResult($returnData);
}
$_SESSION['LAST_ACTIVITY'] = time();
?>