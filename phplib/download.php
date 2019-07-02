<?php
    if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')
    {
        require_once ($_SERVER['DOCUMENT_ROOT'] . '/errordocuments/404.php');
        //return "papo2";
        exit();
    }
    session_start();
    require($_SERVER['DOCUMENT_ROOT'] . '/../include/authenticate.php');
    if (!$_SESSION['logged_in'])
    {
        include ($_SERVER['DOCUMENT_ROOT'] . '/errordocuments/403forbidden.php');
        //return "papo2";
        exit();
    }
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
//require_once ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');


    $_SESSION['LAST_ACTIVITY'] = time();

//printR($_POST);

//$m = new mysql();
    if (isset($_POST['form']) && !empty($_POST['form']))
    {

        for ($i = 0; $i < count($_POST['form']); $i++)
        {
            $formArray[$_POST['form'][$i]['name']] = $_POST['form'][$i]['value'];
        }

        if (isset($_POST['files']) && !empty($_POST['files']))
        {
            if (extension_loaded('zip'))
            {
                // $zip = new ZipArchive();
                $datetime           = date('Ymd-His', time());
                $archive_file_name1 = 'mymedia_' . $datetime . ".zip";
                $file_names         = $_POST['files'];
                // Archive directory
                $archiveDir = IMGPATH . '_zip';
                // Time-to-live
                $archiveTTL = 3600; // 1 hour
                // Files to ignore
                $ignoreFiles = array('.', '..');
                $mediaDir    = IMGPATH . $formArray['office_nametag'] . '/' . $formArray['diskArea_name'] . '/';

                // Loop the storage directory and delete old files
                if ($dp = opendir($archiveDir))
                {
                    while ($file = readdir($dp))
                    {
                        if (!in_array($file, $ignoreFiles) && filectime("$archiveDir/$file") < (time() - $archiveTTL))
                        {
                            unlink("$archiveDir/$file");
                        }
                    }
                }

                // Re-format the file name
                $archive_file_name = "$archiveDir/" . basename($archive_file_name1);
                // Create the object
                $zip = new ZipArchive();
                // Create the file and throw the error if unsuccessful
                if ($zip->open($archive_file_name, ZIPARCHIVE::CREATE) !== TRUE)
                {
                    echo ("Cannot open '$archive_file_name'\n");
                    exit;
                }
                // Add each file of $file_name array to archive
                foreach ($file_names as $file)
                {
                    $name = $mediaDir . $file['name'];
//printR($name);
                    switch ($file['type'])
                    {
                        case 'audio':
                            $zip->addFile($file_path . str_replace('./', '', $name), strtolower(pathinfo($name, PATHINFO_FILENAME)) . ".wav");
                            $zip->addFile($file_path . str_replace('./', '', $name), strtolower(pathinfo($name, PATHINFO_FILENAME)) . ".mp3");
                            break;
                        case 'video':
                            $zip->addFile($file_path . str_replace('./', '', $name), strtolower(pathinfo($name, PATHINFO_FILENAME)) . ".mp4");
                            $zip->addFile($file_path . str_replace('./', '', $name), strtolower(pathinfo($name, PATHINFO_FILENAME)) . ".ogg");
                            break;
                        default:
                            $zip->addFile($file_path . str_replace('./', '', $name), strtolower(pathinfo($name, PATHINFO_FILENAME)) . '.' . strtolower(pathinfo($name, PATHINFO_EXTENSION)));
                    }
                }
                $zip->close();
                /*
                      // Then send the headers to redirect to the ZIP file
                      header("HTTP/1.1 303 See Other"); // 303 is technically correct for this type of redirect
                      header("Location: http://{DOMAINTAG}/$archive_file_name1");
                      exit;



                      header("Cache-Control: maxage=120");
                      header("Expires: ".date(DATE_COOKIE,time()+120)); // Cache for 2 mins
                      header("Pragma: public");
                      header("Content-type: application/force-download");
                      header("Content-Transfer-Encoding: Binary");
                      header("Content-Type: application/octet-stream");
                      header('Content-Disposition: attachment; filename="'.$archive_file_name1.'"');
                      $filepath = $archiveDir.'/'.$archive_file_name1;
                $fp=fopen($filepath,'r');
                fpassthru($fp);
                fclose($fp);
                exit;
                      //echo file_get_contents ($archiveDir.'/'.$archive_file_name1);
                      //exit;
                */
                $returnData = array('download' => $archive_file_name1);
            } else
            {
                $returnData = array('error' => 'Can not create zip!');
            }
        } else
        {
            $returnData = array('error' => 'Misspelled data sent to server!');
        }
    } else
    {
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
//$resF['result'] = $mediaFilesArray;
    $json = json_encode($returnData, TRUE);
    echo $json;
?>