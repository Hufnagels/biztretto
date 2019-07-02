<?php
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');

if (isset($_POST['form']) && !empty($_POST['form'])) {
    $formArray = createArrayFromPostNV();
    if (isset($formArray['diskArea_name'])) unset($formArray['diskArea_name']);
    if (isset($formArray['diskArea_id'])) unset($formArray['diskArea_id']);

    switch ($_POST['action']){
        case 'load':

            $sql = "
                SELECT diskArea_id, name
                FROM media_diskarea
                WHERE office_id = '" . MySQL::filter($_SESSION['office_id']) . "'
                      AND office_nametag = '" . MySQL::filter($_SESSION['office_nametag']) . "'
                      AND name NOT IN ('bought', 'corporate')
                      ORDER BY diskArea_id ASC
            ";
            $result = MySQL::query($sql,false, false);

            $diskAreasArray = array();

            if (!$result){
                $returnArray = array(
                    'type'=>'error',
                    'message' => 'Can\'t load folderlist'
                );

            } else {
                foreach ($result as $row) {
                    $nameTag = $row['name'];
                    $idTag = $row['diskArea_id'];
                    $sortname = normalize_special_characters(strtolower($nameTag));
                    $sortname = str_replace(' ', '', $sortname);
                    if ($nameTag !== 'corporate')
                        $diskAreasArray[] = array(
                            "id" => $idTag,
                            "name" => $nameTag,
                            "doname" => $sortname
                        );
                }
                $returnArray = array(
                    'type'=>'success',
                    'message' => 'OKSA!',
                    'result' => $diskAreasArray
                );
            }
            break;
        default:
            $returnArray = array(
                'type'=>'error',
                'message' => 'Misspelled data sent to server0',
                'result' => $result
            );
            break;
    }
} else {
    $returnArray = array(
        'type' => 'error',
        'message' => 'Misspelled data sent to server!'
    );
}
$_SESSION['LAST_ACTIVITY'] = time();
printSortResult($returnArray);
exit;


//header 
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
$resF['result'] = $diskAreasArray;
$json = json_encode($resF, true);
echo $json;
?>