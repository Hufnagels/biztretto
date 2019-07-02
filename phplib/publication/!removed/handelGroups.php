<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once  ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once  ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
$level_arr = $returnData = array();

if (isset($_POST['form']) && !empty($_POST['form'])) {
    $formArray = createArrayFromPostNV();

    //load usergroup
    if (isset($_POST['load'][0]) && $_POST['load'] == 'load') {
        $mediaBoxesArray = array();
        $organized = 0;

        //collect all users
        $sql = "
    SELECT u_id AS id FROM user_u
      WHERE office_id = " . MySQL::filter($_SESSION['office_id']) . " AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'";

        $query = MySQL::query($sql, false, false);
        $ids = array();
        foreach ($query as $row)
            $ids[] = $row['id'];
        $mediaBoxesArray[0] = array(
            "id" => 'all',
            "name" => 'View all',
            "doname" => 'viewall',
            "ids" => implode(',', $ids),
            "badge" => count($query) //[0]['badge']
        );

        $allUser = count($query);

        if (!empty($query)) {
            //collect users in groups
            $sql2 = "
        SELECT 
          box.usergroup_id AS groupid, box.name AS name, box.doname, users.u_id
        FROM user_usergroup box
        LEFT JOIN user_usergroupUsers users
          ON box.usergroup_id = users.usergroup_id
          WHERE box.office_id = " . MySQL::filter($_SESSION['office_id']) . " AND box.office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "' ORDER BY box.name ASC";
            $query2 = MySQL::query($sql2, false, false);

            //ha van usergroup
            if (!empty($query2)) {
                foreach ($query2 as $row)
                    //not in list count
                    $mediaBoxesArray[1] = array(
                        "id" => 'notInList',
                        "name" => 'Unorganized',
                        "doname" => 'unorganized',
                        "badge" => 0
                    );
                $organized = 0;

                //group by department name
                foreach ($query2 as $key => &$entry) {
                    $level_arr[$entry['doname']][] = $entry;
                }

                $k = 2;
                foreach ($level_arr as $key => $row) {
                    $idTag = $row[0]['groupid'];
                    $nameTag = $row[0]['name'];
                    $doname = $key;
                    $db2 = count($row);
                    $ids = array();
                    for ($j = 0; $j < $db2; $j++) {
                        $ids[] = $row[$j]['u_id'] !== '' ? $row[$j]['u_id'] : "";
                    }

                    $badge = !empty($ids[0]) ? count($ids) : 0;
                    $organized = $organized + $badge;
                    $userIds = !empty($ids[0]) ? implode(',', $ids) : '';
                    $mediaBoxesArray[$k] = array(
                        "id" => $idTag,
                        "name" => $nameTag,
                        "doname" => $doname,
                        "badge" => $badge,
                        "ids" => $userIds
                    );
                    $k++;
                }

                $mediaBoxesArray[1]['badge'] = $allUser - $organized;

                $returnData['result'] = $mediaBoxesArray;
            } else {

                $mediaBoxesArray[1] = array(
                    "id" => 'notInList',
                    "name" => 'Unorganized',
                    "doname" => 'unorganized',
                    "badge" => 0,
                    "ids" => ''
                );

                $returnData['result'] = $mediaBoxesArray;
            }
        } else {
            $returnData = array('error' => 'No users!');
        }
    }


} else {
    $returnData = array('error' => 'Misspelled data sent to server!');
}
$_SESSION['LAST_ACTIVITY'] = time();
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
$json = json_encode($returnData, true);
echo $json;
?>