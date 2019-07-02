<?php
    if (($_SESSION['logged_in']) == TRUE)
    {
        session_start();
        $_SESSION['LAST_ACTIVITY'] = time();
//print_r( $_SESSION );;
    }

    require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/functions/function.system.php');
    require_once($_SERVER['DOCUMENT_ROOT'] . '/../include/functions/function.text.php');
    /*
    print 'post\n';
    print_r($_SERVER['REQUEST_METHOD']);

    print_r($_POST);*/
//print_r($_FILES);
//exit;

    class UploadHandler
    {
        protected $diskAreaSortname;
        protected $options;
        // PHP File Upload error message codes:
        // http://php.net/manual/en/features.file-upload.errors.php
        protected $error_messages = array(
            1                     => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            2                     => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3                     => 'The uploaded file was only partially uploaded',
            4                     => 'No file was uploaded',
            6                     => 'Missing a temporary folder',
            7                     => 'Failed to write file to disk',
            8                     => 'A PHP extension stopped the file upload',
            9                     => 'Directory structure is missing',
            'post_max_size'       => 'The uploaded file exceeds the post_max_size directive in php.ini',
            'max_file_size'       => 'File is too big',
            'min_file_size'       => 'File is too small',
            'overquota'           => 'You reached your quota',
            'accept_file_types'   => 'Filetype not allowed',
            'max_number_of_files' => 'Maximum number of files exceeded',
            'max_width'           => 'Image exceeds maximum width',
            'min_width'           => 'Image requires a minimum width',
            'max_height'          => 'Image exceeds maximum height',
            'min_height'          => 'Image requires a minimum height'
        );

        //$protocol = connectionType();

        function __construct($options = null, $initialize = TRUE)
        {
            /**/
            switch ($_SERVER['REQUEST_METHOD'])
            {
                case 'GET':
                    if (isset($_GET['diskArea_name']) && $_GET['diskArea_name'] !== '')
                        $this->diskAreaSortname = $_POST['diskArea_name'];
                    break;
                case 'POST':
                    if (isset($_POST['diskArea_name']) && $_POST['diskArea_name'] !== '')
                        $this->diskAreaSortname = $_POST['diskArea_name'];
                    break;
                case 'DELETE':
                    if (isset($_SERVER['QUERY_STRING']) && $_SERVER['QUERY_STRING'] !== '')
                    {
                        $fp1                    = explode('=', $_SERVER['QUERY_STRING']);
                        $fp2                    = $fp1[1];
                        $fp3                    = explode('/', $fp2);
                        $this->diskAreaSortname = $fp3[0];
                    }
//print_r($this->diskAreaSortname);
                    break;
            }

//print_r($this->diskAreaSortname);
//exit;
            $this->options = array(
                'script_url'                       => '/crawl?/process/upload/',///$this->get_full_url() . '/',
                //'upload_dir' => dirname($_SERVER['SCRIPT_FILENAME']).'/files/',
                'upload_dir'                       => IMGPATH . $_SESSION['office_nametag'] . '/' . $this->diskAreaSortname . '/', //.$_POST['diskArea_id'].'/',
                'upload_url'                       => $this->connectionType() . IMG_SITE_URL . '/' . $_SESSION['office_nametag'] . '/' . $this->diskAreaSortname . '/', //.$_POST['diskArea_id'].'/', //$this->get_full_url().'/files/',
                'response_dir'                     => $this->diskAreaSortname . '/',
                //$_SESSION['office_nametag'].'/'.
                //'diskArea_id' => $_POST['diskArea_id'],
                'user_dirs'                        => FALSE,
                'mkdir_mode'                       => 0755,
                'param_name'                       => 'files',
                // Set the following option to 'POST', if your server does not support
                // DELETE requests. This is a parameter sent to the client:
                'delete_type'                      => 'DELETE',
                'access_control_allow_origin'      => '*',
                'access_control_allow_credentials' => FALSE,
                'access_control_allow_methods'     => array(
                    'OPTIONS',
                    'HEAD',
                    'GET',
                    'POST',
                    'PUT',
                    'DELETE'
                ),
                'access_control_allow_headers'     => array(
                    'Content-Type',
                    'Content-Range',
                    'Content-Disposition',
                    'Content-Description'
                ),
                // Enable to provide file downloads via GET requests to the PHP script:
                'download_via_php'                 => FALSE,
                // Defines which files can be displayed inline when downloaded:
                'inline_file_types'                => '/\.(gif|jpe?g|png)$/i',
                // Defines which files (based on their names) are accepted for upload:
                'accept_file_types'                => '/.+$/i',
                // The php.ini settings upload_max_filesize and post_max_size
                // take precedence over the following max_file_size setting:
                'max_file_size'                    => null,
                'min_file_size'                    => 1,
                // The maximum number of files for the upload directory:
                'max_number_of_files'              => null,
                // Image resolution restrictions:
                'max_width'                        => null,
                'max_height'                       => null,
                'min_width'                        => 1,
                'min_height'                       => 1,
                // Set the following option to false to enable resumable uploads:
                'discard_aborted_uploads'          => TRUE,
                // Set to true to rotate images based on EXIF meta data, if available:
                'orient_image'                     => FALSE,
                'image_versions'                   => array(
                    // Uncomment the following version to restrict the size of
                    // uploaded images:
                    /*
                    '' => array(
                      'max_width' => 1920,
                      'max_height' => 1200,
                      'jpeg_quality' => 95
                    ),
                    */
                    // Uncomment the following to create medium sized images:
                    /*
                    'medium' => array(
                      'max_width' => 800,
                      'max_height' => 600,
                      'jpeg_quality' => 80
                    ),
                    */
                    'thumbnail' => array(
                        'max_width'  => 160,
                        'max_height' => 120
                    )
                ),
                //accepted video types for upload
                'uploadVideoTypes'                 => array(
                    'wmv', 'flv', 'avi', 'ogg', 'mp4', 'webm'
                ),
                //video types for server
                'videoTypes'                       => array(
                    'ogg', 'mp4'
                ),
                //accepted audio types
                'uploadAudioTypes'                 => array(
                    'wav', 'ogg', 'mp3'
                ),
                //audio types for server
                'audioTypes'                       => array(
                    'wav', 'mp3'
                )
            );
            if ($options)
            {
                $this->options = array_merge($this->options, $options);
            }

            if ($initialize)
            {
                $this->initialize();
            }
        }

        /////////////////////////////////////////////////
        //modified or new functions
        /////////////////////////////////////////////////

        /////////////////////////////////////////////////
        //check connection type: http / https
        /////////////////////////////////////////////////
        protected function connectionType()
        {
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

            return $protocol;
            //$baseurl= connectionType().IMG_SITE_URL.'/';
        }

        /////////////////////////////////////////////////
        //check upload dirs and permissions
        /////////////////////////////////////////////////
        protected function checkDirs($path)
        {
            $oldmask = umask(0);
            if (!is_dir($path))
            {
                $ret = mkdir($path, 0755); // use @mkdir if you want to suppress warnings/errors
            }
            umask($oldmask);

            return $ret === TRUE || is_dir($path);
        }

        /////////////////////////////////////////////////
        // return file size in B/KB/MB/GB ...
        /////////////////////////////////////////////////
        protected function filesize_formatted($size)
        {
            //$size = filesize($path);
            $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB');
            $power = $size > 0 ? floor(log($size, 1024)) : 0;

            return number_format($size / pow(1024, $power), 2, '.', ',') . ' ' . $units[$power];
        }

        /////////////////////////////////////////////////
        //create thumbnail
        /////////////////////////////////////////////////
        protected function create_scaled_image($file_name, $version, $options)
        {

            $file_path = $this->get_upload_path($file_name);
            if (!empty($version))
            {
                $version_dir = $this->get_upload_path(null, $version);
                if (!is_dir($version_dir))
                {
                    mkdir($version_dir, $this->options['mkdir_mode']);
                }
                $new_file_path = $version_dir . '/' . $file_name;
            } else
            {
                $new_file_path = $file_path;
            }
            list($img_width, $img_height) = @getimagesize($file_path);
            if (!$img_width || !$img_height)
            {
//////////////////////////////////////////////////////////////////////
//itt nezem meg, ha nem image, akkor mit csinaljon az attachementekkel

//print 'ez mas';
//////////////////////////////////////////////////////////////////////
                return FALSE;
            }
//print_r($version);

            $scale = min(
                $options['max_width'] / $img_width,
                $options['max_height'] / $img_height
            );
            if ($scale >= 1)
            {
                if ($file_path !== $new_file_path)
                {
                    return copy($file_path, $new_file_path);
                }

                return TRUE;
            }

/////////////////////////////////////////////////
// resize and crop uploaded image
/////////////////////////////////////////////////
            /*
            create 160x120 thumbnail
            the method: resize and than crop
             */
            $new_file_path = preg_replace("#/+#", "/", $new_file_path);
//WIDE IMAGE
            if (($img_width > $img_height) && ($img_height < 121))
            {
//echo $img_height;
                exec("convert " . $file_path . " -resize 160 -quality 100 -background white -gravity center -extent 160x120 " . $new_file_path);
            } else if (($img_width > $img_height) && ($img_height > 120))
            {
//echo 'nem lehet1';
                exec("convert " . $file_path . " -resize x120 -quality 100 -gravity Center -crop 160x120+0+0 " . $new_file_path);
            }
//TALL IMAGE
            if ($img_width < $img_height)
            {
//echo 'nem lehet2';
                exec("convert " . $file_path . " -resize 160 -quality 100 -gravity Center -crop 160x120+0+0 " . $new_file_path);
            }
//SQUARE IMAGE
            if ($img_width = $img_height)
            {
//echo 'nem lehet2';
                exec("convert " . $file_path . " -resize x120 -quality 100 -background none -gravity Center -extent 160x120  " . $new_file_path);
            }
            $success = TRUE;

            return $success;
        }

        /////////////////////////////////////////////////
        // check disk quota to enable upload
        /////////////////////////////////////////////////
        protected function checkDiskQuotaStatus($content_length)
        {
            $quota = Office::helperCalculateDiskUsage($_SESSION['office_id']);
            //print_r($quota);
            //print "\r\n";
            //print $content_length*1.6;
            if ($quota)
            {
                return !($quota['free'] <= $content_length * 1.6);
            }
            return TRUE;
        }


        /////////////////////////////////////////////////
        // inserted function
        // upload video, create conversation beetween .mp4 and .ogg
        /////////////////////////////////////////////////
        protected function videoconversation($file_name, $version, $options, $typ)
        {
            if ($version != '')
            {
                $version_dir = $this->get_upload_path(null, $version);
                $version_dir = preg_replace("#/+#", "/", $version_dir);
                if (!is_dir($version_dir))
                {
                    mkdir($version_dir, $this->options['mkdir_mode']);
                }
                //ide megy a poster kep
                $new_file_path = $version_dir . '/' . $file_name;
                $new_file_path = preg_replace("#/+#", "/", $new_file_path);
            }

            $base_dir = $this->get_upload_path(null, '');
            //ide kerul a tobbi video
            $file_path = $base_dir . '/'; //.$file_name;
            $file_path = preg_replace("#/+#", "/", $file_path);


            $targetFileExt  = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $file_base_name = pathinfo($file_name, PATHINFO_FILENAME);

            $arr     = array($targetFileExt);
            $temparr = ($typ == 'video') ? $options['videoTypes'] : $options['audioTypes'];
            $arr     = array_diff($temparr, $arr);
//printR($arr);
            /////////////////////////////////////////////////
            // create thumbnail and get video length and resolution
            /////////////////////////////////////////////////

            if ($typ == 'video'){
                exec("ffmpegthumbnailer -i " . $file_path . $file_name . " -s 0 -q 10 -o " . $version_dir . $file_base_name . ".jpg");

                $extension = pathinfo($file_path . $file_name,PATHINFO_EXTENSION);

                exec("ffprobe -v quiet -print_format json -show_format -show_streams " . $file_path . $file_name, $response);
                $ffprobeResponse = json_decode(implode(null, $response));

                switch ($extension) {
                    case 'wmv':
                        $videoWidth      = $ffprobeResponse->streams[1]->width;
                        $videoHeight     = $ffprobeResponse->streams[1]->height;
                        $dur             = gmdate("H:i:s", (int)$ffprobeResponse->streams[1]->duration);
                        break;
                    default:
                        $videoWidth      = $ffprobeResponse->streams[0]->width;
                        $videoHeight     = $ffprobeResponse->streams[0]->height;
                        $dur             = gmdate("H:i:s", (int)$ffprobeResponse->streams[0]->duration);
                        break;
                }


            }

            // 160x90
            /*
            //get video duration
            ob_start();
            //$whereis ffmpeg: change with /usr/local/bin/ffmpeg
            passthru("avconv -i ".$file_path.$file_name." 2>&1");
            $duration = ob_get_contents();
            ob_end_clean();
            //the full output:
            echo $duration."<br/>";
            $search='/Duration: (.*?)[.]/';
            $duration=preg_match($search, $duration, $matches, PREG_OFFSET_CAPTURE);
            $duration = $matches[1][0];
            //i suppose that our video hasn't duration of a day+ :
            list($hours, $mins, $secs) = split('[:]', $duration);
            $dur = $hours.":".$mins.":".$secs;
            */

            /*
             * determine video length and size(w:h)
             * response is a json object class
             */

//print_r($ffprobeResponse);
//print $videoWidth;

//exit;
            /////////////////////////////////////////////////
            // create conversation
            /////////////////////////////////////////////////
            foreach ($arr as $k => $v)
            {
                switch ($v)
                {
                    //video section
                    case 'mp4' :
                        //convert pl wmv to mp4
                        exec("ffmpeg -i " . $file_path . $file_name . " -y " . $file_path . $file_base_name . ".mp4");
                        $_SESSION['LAST_ACTIVITY'] = time();
                        break;
                    case 'ogg' :
                        //mukodik, kb ua meret
                        //convert to ogg
                        exec("ffmpeg2theora -i " . $file_path . $file_name . " -v 5 -a 3 -o " . $file_path . $file_base_name . ".ogg");
                        $_SESSION['LAST_ACTIVITY'] = time();
                        break;
                    case 'webm' :
                        //convert to webm
                        //avconv -i zoldovezet1128.wmv z.webm
                        break;
                    //audio section
                    case 'mp3' :
                        //convert to mp3
                        exec("ffmpeg -i " . $file_path . $file_name . " -acodec libmp3lame -y " . $file_path . $file_base_name . ".mp3");
                        $_SESSION['LAST_ACTIVITY'] = time();
                        break;
                    case 'wav' :
                        //convert to wav
                        exec("ffmpeg -i " . $file_path . $file_name . " -y " . $file_path . $file_base_name . ".wav");
                        $_SESSION['LAST_ACTIVITY'] = time();
                        break;
                    case 'm4a' :
                        //convert to wav
                        exec("ffmpeg -i " . $file_path . $file_name . " -y " . $file_path . $file_base_name . ".m4a");
                        $_SESSION['LAST_ACTIVITY'] = time();
                        break;
                }
            }

            if (count($arr) > (count($temparr) - 1)) unlink($file_path . $file_name);

            $poster = ($typ == 'video') ? preg_replace("#/+#", "/", "/" . $_SESSION['office_nametag'] . "/" . $this->options['diskArea_id'] . '/' . $version . "/" . $file_base_name . ".jpg") : '';
            $return = array($dur, $poster, $videoWidth, $videoHeight);

            return $return;
        }
        /////////////////////////////////////////////////
        // end extra section
        /////////////////////////////////////////////////

        protected function initialize()
        {
///jellemzoen post a method
            switch ($_SERVER['REQUEST_METHOD'])
            {
                case 'OPTIONS':
                case 'HEAD':
                    $this->head();
                    break;
                case 'GET':
                    $this->get();
                    break;
                case 'POST':
                    $this->post();
                    break;
                case 'DELETE':
                    $this->delete();
                    break;
                default:
                    header('HTTP/1.1 405 Method Not Allowed');
            }
        }

        protected function get_full_url()
        {
            $https = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';

            return
                ($https ? 'https://' : 'http://') .
                (!empty($_SERVER['REMOTE_USER']) ? $_SERVER['REMOTE_USER'] . '@' : '') .
                (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : ($_SERVER['SERVER_NAME'] .
                ($https && $_SERVER['SERVER_PORT'] === 443 ||
                $_SERVER['SERVER_PORT'] === 80 ? '' : ':' . $_SERVER['SERVER_PORT']))) .
                substr($_SERVER['SCRIPT_NAME'], 0, strrpos($_SERVER['SCRIPT_NAME'], '/'));
        }

        protected function get_user_id()
        {
            @session_start();

            return session_id();
        }

        protected function get_user_path()
        {
            if ($this->options['user_dirs'])
            {
                return $this->get_user_id() . '/';
            }

            return '';
        }

        protected function get_upload_path($file_name = null, $version = null, $diskArea_id = null)
        {
            $file_name    = $file_name ? $file_name : '';
            $version_path = empty($version) ? '' : $version . '/';

//image eseten a thumbnail konyvtar beallitasa
            return $this->options['upload_dir'] . $diskArea_id . $this->get_user_path()
                . $version_path . $file_name;
        }

        protected function get_download_url($file_name, $version = null)
        {
            if ($this->options['download_via_php'])
            {
                $url = $this->options['script_url'] . '?file=' . rawurlencode($file_name);
                if ($version)
                {
                    $url .= '&version=' . rawurlencode($version);
                }

                return $url . '&download=1';
            }
            $version_path = empty($version) ? '' : rawurlencode($version) . '/';

            return $this->options['response_dir'] . $version_path . rawurlencode($file_name);
            /*$this->options['upload_url'].$this->get_user_path()*/
        }

        protected function set_file_delete_properties($file)
        {
            $file->delete_url  = $this->options['script_url']
                . '?file=' . $this->diskAreaSortname . '/' . rawurlencode($file->name);
            $file->delete_type = $this->options['delete_type'];
            if ($file->delete_type !== 'DELETE')
            {
                $file->delete_url .= '&_method=DELETE';
            }
            if ($this->options['access_control_allow_credentials'])
            {
                $file->delete_with_credentials = TRUE;
            }
        }

        // Fix for overflowing signed 32 bit integers,
        // works for sizes up to 2^32-1 bytes (4 GiB - 1):
        protected function fix_integer_overflow($size)
        {
            if ($size < 0)
            {
                $size += 2.0 * (PHP_INT_MAX + 1);
            }

            return $size;
        }

        protected function get_file_size($file_path, $clear_stat_cache = FALSE)
        {
            if ($clear_stat_cache)
            {
                clearstatcache();
            }

            return $this->fix_integer_overflow(filesize($file_path));

        }

        protected function is_valid_file_object($file_name)
        {
            $file_path = $this->get_upload_path($file_name);
            if (is_file($file_path) && $file_name[0] !== '.')
            {
                return TRUE;
            }

            return FALSE;
        }

        protected function get_file_object($file_name)
        {
            if ($this->is_valid_file_object($file_name))
            {
                $file       = new stdClass();
                $file->name = $file_name;
                $file->size = $this->get_file_size(
                    $this->get_upload_path($file_name)
                );
                $file->url  = $this->get_download_url($file->name);
                foreach ($this->options['image_versions'] as $version => $options)
                {
                    if (!empty($version))
                    {
                        if (is_file($this->get_upload_path($file_name, $version)))
                        {
                            $file->{$version . '_url'} = $this->get_download_url(
                                $file->name,
                                $version
                            );
                        }
                    }
                }
                $this->set_file_delete_properties($file);

                return $file;
            }

            return null;
        }

        protected function get_file_objects($iteration_method = 'get_file_object')
        {
            $upload_dir = $this->get_upload_path();
            if (!is_dir($upload_dir))
            {
                mkdir($upload_dir, $this->options['mkdir_mode']);
            }

            return array_values(array_filter(array_map(
                array($this, $iteration_method),
                scandir($upload_dir)
            )));
        }

        protected function count_file_objects()
        {
            return count($this->get_file_objects('is_valid_file_object'));
        }

        protected function get_error_message($error)
        {
            return array_key_exists($error, $this->error_messages) ?
                $this->error_messages[$error] : $error;
        }

        function get_config_bytes($val)
        {
            $val  = trim($val);
            $last = strtolower($val[strlen($val) - 1]);
            switch ($last)
            {
                case 'g':
                    $val *= 1024;
                case 'm':
                    $val *= 1024;
                case 'k':
                    $val *= 1024;
            }

            return $this->fix_integer_overflow($val);
        }

        protected function validate($uploaded_file, $file, $error, $index)
        {
            if ($error)
            {
                $file->error = $this->get_error_message($error);

                return FALSE;
            }
            $content_length = $this->fix_integer_overflow(intval($_SERVER['CONTENT_LENGTH']));
            /*
            print_r($content_length);
            print "\r\n";
            print_r($this->get_config_bytes(ini_get('post_max_size')));
            print "\r\n";
            print_r(ini_get('post_max_size'));
            print "\r\n";
            */


            if ($content_length > $this->get_config_bytes(ini_get('post_max_size')))
            {
                $file->error = $this->get_error_message('post_max_size');

                return FALSE;
            }

            if (!preg_match($this->options['accept_file_types'], $file->name))
            {
                $file->error = $this->get_error_message('accept_file_types');

                return FALSE;
            }

            /////////////////////////////////////////////////
            // check disk quota
            /////////////////////////////////////////////////
            if(!$this->checkDiskQuotaStatus($content_length))
            {
                $file->error = $this->get_error_message('overquota');
                return FALSE;
            }

            if ($uploaded_file && is_uploaded_file($uploaded_file))
            {
                $file_size = $this->get_file_size($uploaded_file);
            } else
            {
                $file_size = $content_length;
            }
            if ($this->options['max_file_size'] && (
                $file_size > $this->options['max_file_size'] ||
                    $file->size > $this->options['max_file_size'])
            )
            {
                $file->error = $this->get_error_message('max_file_size');

                return FALSE;
            }
            if ($this->options['min_file_size'] &&
                $file_size < $this->options['min_file_size']
            )
            {
                $file->error = $this->get_error_message('min_file_size');

                return FALSE;
            }
            if (is_int($this->options['max_number_of_files']) && (
                $this->count_file_objects() >= $this->options['max_number_of_files'])
            )
            {
                $file->error = $this->get_error_message('max_number_of_files');

                return FALSE;
            }
            list($img_width, $img_height) = @getimagesize($uploaded_file);
///megnezzuk, hogy image-e es ellenorizzuk
            if (is_int($img_width))
            {
                if ($this->options['max_width'] && $img_width > $this->options['max_width'])
                {
                    $file->error = $this->get_error_message('max_width');

                    return FALSE;
                }
                if ($this->options['max_height'] && $img_height > $this->options['max_height'])
                {
                    $file->error = $this->get_error_message('max_height');

                    return FALSE;
                }
                if ($this->options['min_width'] && $img_width < $this->options['min_width'])
                {
                    $file->error = $this->get_error_message('min_width');

                    return FALSE;
                }
                if ($this->options['min_height'] && $img_height < $this->options['min_height'])
                {
                    $file->error = $this->get_error_message('min_height');

                    return FALSE;
                }
            }

            return TRUE;
        }

        protected function upcount_name_callback($matches)
        {
            $index = isset($matches[1]) ? intval($matches[1]) + 1 : 1;
            $ext   = isset($matches[2]) ? $matches[2] : '';

            return '_' . $index . $ext;
///javitva. nincs szokoz, ha van ugyan olyan nevű több is
        }

        protected function upcount_name($name)
        {
            return preg_replace_callback(
                '/(?:(?: \(([\d]+)\))?(\.[^.]+))?$/',
                array($this, 'upcount_name_callback'),
                $name,
                1
            );
        }

        protected function trim_file_name($name, $type, $index, $content_range)
        {
            // Remove path information and dots around the filename, to prevent uploading
            // into different directories or replacing hidden system files.
            // Also remove control characters and spaces (\x00..\x20) around the filename:
//print_r($name);
            $file_name = trim(basename(@stripslashes($name)), ".\x00..\x20");
            // Add missing file extension for known image types:
            if (strpos($file_name, '.') === FALSE &&
                preg_match('/^image\/(gif|jpe?g|png)/', $type, $matches)
            )
            {
                $file_name .= '.' . $matches[1];
            }

            while (is_dir($this->get_upload_path($file_name)))
            {
                $file_name = $this->upcount_name($file_name);
            }
            $uploaded_bytes = $this->fix_integer_overflow(intval($content_range[1]));
            while (is_file($this->get_upload_path($file_name)))
            {
                if ($uploaded_bytes === $this->get_file_size(
                    $this->get_upload_path($file_name))
                )
                {
                    break;
                }
                $file_name = $this->upcount_name($file_name);
            }

            return $file_name;
        }

        protected function handle_form_data($file, $index)
        {
            // Handle form data, e.g. $_REQUEST['description'][$index]
        }

        protected function orient_image($file_path)
        {
            $exif = @exif_read_data($file_path);
            if ($exif === FALSE)
            {
                return FALSE;
            }
            $orientation = intval(@$exif['Orientation']);
            if (!in_array($orientation, array(3, 6, 8)))
            {
                return FALSE;
            }
            $image = @imagecreatefromjpeg($file_path);
            switch ($orientation)
            {
                case 3:
                    $image = @imagerotate($image, 180, 0);
                    break;
                case 6:
                    $image = @imagerotate($image, 270, 0);
                    break;
                case 8:
                    $image = @imagerotate($image, 90, 0);
                    break;
                default:
                    return FALSE;
            }
            $success = imagejpeg($image, $file_path);
            // Free up memory (imagedestroy does not delete files):
            @imagedestroy($image);

            return $success;
        }

        protected function handle_file_upload($uploaded_file, $name, $size, $type, $error, $index = null, $content_range = null)
        {
            $_SESSION['LAST_ACTIVITY'] = time();
//print_r('handle_file_upload');
//print_r($name);
            $file       = new stdClass();
            $file->name = $this->trim_file_name($name, $type, $index, $content_range);
            $file->size = $this->fix_integer_overflow(intval($size));
            $file->type = $type;
            if ($this->validate($uploaded_file, $file, $error, $index))
            {
                $this->handle_form_data($file, $index);
                $upload_dir = $this->get_upload_path();
                if (!is_dir($upload_dir))
                {
                    mkdir($upload_dir, $this->options['mkdir_mode']);
                }
                $file_path   = $this->get_upload_path($file->name);
                $append_file = $content_range && is_file($file_path) &&
                    $file->size > $this->get_file_size($file_path);
                if ($uploaded_file && is_uploaded_file($uploaded_file))
                {
                    // multipart/formdata uploads (POST method uploads)
                    if ($append_file)
                    {
                        file_put_contents(
                            $file_path,
                            fopen($uploaded_file, 'r'),
                            FILE_APPEND
                        );
                    } else
                    {
///mindig ez az opcio el??!!
                        move_uploaded_file($uploaded_file, $file_path);
                        if (substr(sprintf('%o', fileperms($file_path)), -4) !== '755')
                            chmod($file_path, 0755);
                    }
                } else
                {
                    // Non-multipart uploads (PUT method support)
                    file_put_contents(
                        $file_path,
                        fopen('php://input', 'r'),
                        $append_file ? FILE_APPEND : 0
                    );
                }
                $file_size = $this->get_file_size($file_path, $append_file);
                if ($file_size === $file->size)
                {
                    if ($this->options['orient_image'])
                    {
                        $this->orient_image($file_path);
                    }
                    $file->url = $this->get_download_url($file->name);

                    /////////////////////////////////////////////////
                    // add extra parameters to responded json
                    /////////////////////////////////////////////////
                    /*
                     * attachment   type (doc?, xls?, pdf, ppt?)
                     * video        poster url
                     * image        thumbnail
                     */
///hozzaadjuk a feltoltes datumat
                    $file->uploaded     = time();
                    $file->uploadedTime = date('Y.m.d');
//hozza adjuk a roviditett tipust
//valamint a megjelenitendo thumbnail kepet
                    $targetFileExt = strtolower(pathinfo($file->name, PATHINFO_EXTENSION));
                    $res           = '';
                    switch ($targetFileExt)
                    {
                        case 'doc' :
                        case 'docx':
                            $res                 = 'word';
                            $file->thumbnail_url = /*$this->connectionType().IMG_SITE_URL.*/
                                'doc-grey.png';
                            $file->filesize      = $this->filesize_formatted($this->fix_integer_overflow(intval($size)));
                            break;
                        //wordBase64; break;
                        case 'xls' :
                        case 'xlsx':
                            $res                 = 'excel';
                            $file->thumbnail_url = /*$this->connectionType().IMG_SITE_URL.*/
                                'excel-grey.png';
                            $file->filesize      = $this->filesize_formatted($this->fix_integer_overflow(intval($size)));
                            break;
                        //excelBase64; break;
                        case 'pdf' :
                        case 'pdf' :
                            $res                 = 'pdf';
                            $file->thumbnail_url = /*$this->connectionType().IMG_SITE_URL.*/
                                'pdf-grey.png';
                            $file->filesize      = $this->filesize_formatted($this->fix_integer_overflow(intval($size)));
                            break;
                        case 'ppt' :
                        case 'pptx' :
                            $res                 = 'powerpoint';
                            $file->thumbnail_url = /*$this->connectionType().IMG_SITE_URL.*/
                                'ppt-grey.png';
                            $file->filesize      = $this->filesize_formatted($this->fix_integer_overflow(intval($size)));
                            break;
                        //pdfBase64;break;
                        case 'png' :
                        case 'gif' :
                        case 'jpg' :
                        case 'jpeg':
                            $res            = 'image';
                            $file->filesize = $this->filesize_formatted($this->fix_integer_overflow(intval($size)));
                            break;
                        case 'mpg' :
                        case 'mpeg':
                        case 'mp4' :
                        case 'flv' :
                        case 'ogg' :
                        case 'webm':
                        case 'wmv' :
                            $res = 'video';
                            break;
                        case 'mp3' :
                        case 'wav' :
                            $res                 = 'audio';
                            $file->thumbnail_url = /*$this->connectionType().IMG_SITE_URL.*/
                                'audio-grey.png'; //audioBase64; break;
                    }
                    $file->typeShort = $res;
//ha nem image a file, akkor itt kezelhetem

                    switch ($res)
                    {

                        case 'video':
                            $getArray            = $this->videoconversation($file->name, 'thumbnail', $this->options, 'video');
                            $file->duration      = $getArray[0];
                            $file->thumbnail_url = $this->options['response_dir'] . 'thumbnail/' . pathinfo($file->name, PATHINFO_FILENAME) . ".jpg";
                            $file->videoWidth    = $getArray[2];
                            $file->videoHeight   = $getArray[3];
                            //$this->connectionType().IMG_SITE_URL
                            $file->name = pathinfo($file->name, PATHINFO_FILENAME) . ".mp4";
                            break;
                        case 'audio':
                            $getArray       = $this->videoconversation($file->name, '', $this->options, 'audio');
                            $file->duration = $getArray[0];
                            //$file->thumbnail_url = $this->connectionType().IMG_SITE_URL. $getArray[1];
                            $file->name = pathinfo($file->name, PATHINFO_FILENAME) . ".mp3";
                            break;
                    }
                    $file->mediaurl = $this->options['response_dir'] . $file->name;
                    /*$this->connectionType().IMG_SITE_URL.'/'.$_SESSION['office_nametag']*/ /*$this->options['upload_url']*/
                    //   '/'.$this->options['diskArea_id'].

                    $file->mediatype = 'local';
//elokep kell a videorol
//poster_ ... . ext
                    $_SESSION['LAST_ACTIVITY'] = time();

                    foreach ($this->options['image_versions'] as $version => $options)
                    {
                        if ($this->create_scaled_image($file->name, $version, $options))
                        {
                            if (!empty($version))
                            {
                                $file->{$version . '_url'} = $this->get_download_url(
                                    $file->name,
                                    $version
                                );
                            } else
                            {
                                $file_size = $this->get_file_size($file_path, TRUE);
                            }
                        }
                    }
                } else if (!$content_range && $this->options['discard_aborted_uploads'])
                {
                    unlink($file_path);
                    $file->error = 'abort';
                }
                $file->size = $file_size;
                $this->set_file_delete_properties($file);
            }

            return $file;
        }

        protected function generate_response($content, $print_response = TRUE)
        {
            if ($print_response)
            {
                $json     = json_encode($content);
                $redirect = isset($_REQUEST['redirect']) ?
                    stripslashes($_REQUEST['redirect']) : null;
                if ($redirect)
                {
                    header('Location: ' . sprintf($redirect, rawurlencode($json)));

                    return;
                }
                $this->head();
                if (isset($_SERVER['HTTP_CONTENT_RANGE']) && is_array($content) &&
                    is_object($content[0]) && $content[0]->size
                )
                {
                    header('Range: 0-' . ($this->fix_integer_overflow(intval($content[0]->size)) - 1));
                }
                echo $json;
            }

            return $content;
        }

        protected function get_version_param()
        {
            return isset($_GET['version']) ? basename(stripslashes($_GET['version'])) : null;
        }

        protected function get_file_name_param()
        {
            return isset($_GET['file']) ? basename(stripslashes($_GET['file'])) : null;
        }

        protected function get_file_type($file_path)
        {
            switch (strtolower(pathinfo($file_path, PATHINFO_EXTENSION)))
            {
                case 'jpeg':
                case 'jpg':
                    return 'image/jpeg';
                case 'png':
                    return 'image/png';
                case 'gif':
                    return 'image/gif';
                default:
                    return '';
            }
        }

        protected function download()
        {
            if (!$this->options['download_via_php'])
            {
                header('HTTP/1.1 403 Forbidden');

                return;
            }
            $file_name = $this->get_file_name_param();
            if ($this->is_valid_file_object($file_name))
            {
                $file_path = $this->get_upload_path($file_name, $this->get_version_param());
                if (is_file($file_path))
                {
                    if (!preg_match($this->options['inline_file_types'], $file_name))
                    {
                        header('Content-Description: File Transfer');
                        header('Content-Type: application/octet-stream');
                        header('Content-Disposition: attachment; filename="' . $file_name . '"');
                        header('Content-Transfer-Encoding: binary');
                    } else
                    {
                        // Prevent Internet Explorer from MIME-sniffing the content-type:
                        header('X-Content-Type-Options: nosniff');
                        header('Content-Type: ' . $this->get_file_type($file_path));
                        header('Content-Disposition: inline; filename="' . $file_name . '"');
                    }
                    header('Content-Length: ' . $this->get_file_size($file_path));
                    header('Last-Modified: ' . gmdate('D, d M Y H:i:s T', filemtime($file_path)));
                    readfile($file_path);
                }
            }
        }

        protected function send_content_type_header()
        {
            header('Vary: Accept');
            if (isset($_SERVER['HTTP_ACCEPT']) &&
                (strpos($_SERVER['HTTP_ACCEPT'], 'application/json') !== FALSE)
            )
            {
                header('Content-type: application/json');
            } else
            {
                header('Content-type: text/plain');
            }
        }

        protected function send_access_control_headers()
        {
            header('Access-Control-Allow-Origin: ' . $this->options['access_control_allow_origin']);
            header('Access-Control-Allow-Credentials: '
                . ($this->options['access_control_allow_credentials'] ? 'true' : 'false'));
            header('Access-Control-Allow-Methods: '
                . implode(', ', $this->options['access_control_allow_methods']));
            header('Access-Control-Allow-Headers: '
                . implode(', ', $this->options['access_control_allow_headers']));
        }

        public function head()
        {
            header('Pragma: no-cache');
            header('Cache-Control: no-store, no-cache, must-revalidate');
            header('Content-Disposition: inline; filename="files.json"');
            // Prevent Internet Explorer from MIME-sniffing the content-type:
            header('X-Content-Type-Options: nosniff');
            if ($this->options['access_control_allow_origin'])
            {
                $this->send_access_control_headers();
            }
            $this->send_content_type_header();
        }

        public function get($print_response = TRUE)
        {
            if ($print_response && isset($_GET['download']))
            {
                return $this->download();
            }
            $file_name = $this->get_file_name_param();
            if ($file_name)
            {
                $info = $this->get_file_object($file_name);
            } else
            {
                $info = $this->get_file_objects();
            }

            return $this->generate_response($info, $print_response);
        }

        public function post($print_response = TRUE)
        {
/// ebben van minden file info
//print_r($_FILES);
//exit;
///
            $file = new stdClass();
            if (isset($_REQUEST['_method']) && $_REQUEST['_method'] === 'DELETE')
            {
                return $this->delete($print_response);
            }

            if (!$this->checkDirs($this->options['upload_dir']) && !$this->checkDirs($this->options['upload_dir'] . 'thumbnail/'))
            {
                $file->error = $this->get_error_message(9);
                //return false;
            }
            /*
            print_r($this->options['upload_dir']);
            print 'response\r\n';
            print_r($this->checkDirs($this->options['upload_dir']));
            print 'error\r\n';
            print_r($file->error);
            //exit;
            */
            $upload = isset($_FILES[$this->options['param_name']]) ?
                $_FILES[$this->options['param_name']] : null;
            // Parse the Content-Disposition header, if available:
            /////////////////////////////////////////////////
            // make filename readable
            //remove special characters
            /////////////////////////////////////////////////

//print_r($upload);
            $vowels        = array('-', '@', ' ', '.', '%');
            $fileParts     = pathinfo($upload['name'][0]);
            $targetFileExt = strtolower(pathinfo($upload['name'][0], PATHINFO_EXTENSION));

            $targetFileName = strtolower(pathinfo($upload['name'][0], PATHINFO_FILENAME));
//$targetFileName = str_replace($vowels, "_", $targetFileName);
            $targetFileName = normalize_special_characters($targetFileName);
            $targetFileName = preg_replace('/[^a-zA-Z0-9_\-()\-]/s', '', $targetFileName);
            $targetFile     = $targetFileName . '.' . $targetFileExt;
            $upload['name'] = array($targetFile);

//print_r($upload);
//print_r($this->diskAreaSortname);
//exit;
            /*
            Array
            (
                [name] => Array
                    (
                        [0] => energetika.doc
                    )

                [type] => Array
                    (
                        [0] => application/msword
                    )

                [tmp_name] => Array
                    (
                        [0] => /tmp/phpqT4jge
                    )

                [error] => Array
                    (
                        [0] => 0
                    )

                [size] => Array
                    (
                        [0] => 1047552
                    )

            )
            */
//print_r($upload);
            $file_name = isset($_SERVER['HTTP_CONTENT_DISPOSITION']) ?
                rawurldecode(preg_replace(
                    '/(^[^"]+")|("$)/',
                    '',
                    $_SERVER['HTTP_CONTENT_DISPOSITION']
                )) : null;
            $file_type = isset($_SERVER['HTTP_CONTENT_DESCRIPTION']) ?
                $_SERVER['HTTP_CONTENT_DESCRIPTION'] : null;

            // Parse the Content-Range header, which has the following form:
            // Content-Range: bytes 0-524287/2000000
            $content_range = isset($_SERVER['HTTP_CONTENT_RANGE']) ?
                preg_split('/[^0-9]+/', $_SERVER['HTTP_CONTENT_RANGE']) : null;
            $size          = $content_range ? $content_range[3] : null;
            $info          = array();
            if ($upload && is_array($upload['tmp_name']))
            {
                // param_name is an array identifier like "files[]",
                // $_FILES is a multi-dimensional array:
                foreach ($upload['tmp_name'] as $index => $value)
                {
                    $info[] = $this->handle_file_upload(
                        $upload['tmp_name'][$index],
                        $file_name ? $file_name : $upload['name'][$index],
                        $size ? $size : $upload['size'][$index],
                        $file_type ? $file_type : $upload['type'][$index],
                        $upload['error'][$index],
                        $index,
                        $content_range
                    );
                }
            } else
            {
                // param_name is a single object identifier like "file",
                // $_FILES is a one-dimensional array:
                $info[] = $this->handle_file_upload(
                    isset($upload['tmp_name']) ? $upload['tmp_name'] : null,
                    $file_name ? $file_name : (isset($upload['name']) ?
                        $upload['name'] : null),
                    $size ? $size : (isset($upload['size']) ?
                        $upload['size'] : $_SERVER['CONTENT_LENGTH']),
                    $file_type ? $file_type : (isset($upload['type']) ?
                        $upload['type'] : $_SERVER['CONTENT_TYPE']),
                    isset($upload['error']) ? $upload['error'] : null,
                    null,
                    $content_range
                );
            }

            return $this->generate_response($info, $print_response);
        }

        public function delete($print_response = TRUE)
        {

//        $disk = explode('/', $_GET['file']);
//        $diskArea_id = $disk[0];
        //$file_name = $this->get_file_name_param();
            $folderT = explode('=', $_GET['subpage']);
            $folder = $folderT[1];
            $filename = $_GET['i'];
//print_r($_GET);
            $file_name      = IMGPATH.$_SESSION['office_nametag'].'/'.basename($folder).'/'.basename($filename);//$this->get_file_name_param();
//print_r($file_name);
            $file_path      = preg_replace("#/+#", "/", $this->get_upload_path($file_name));
            $file_name_base = pathinfo($file_path, PATHINFO_FILENAME);
            $file_ext       = pathinfo($file_path, PATHINFO_EXTENSION);
            $base_dir       = preg_replace("#/+#", "/", $this->get_upload_path(null, '') /*.'/'.$diskArea_id.'/'*/);
//$file_path = $base_dir.$file_name;
//print $file_path.' ';
            $success = is_file($file_path) && $file_name[0] !== '.' && unlink($file_path);

            /////////////////////////////////////////////////
            // search for extra files, when delete main
            // mp4 & ogg & poster
            /////////////////////////////////////////////////
            switch ($file_ext)
            {
                case 'mp4':
                case 'ogg':
                    foreach (GLOB($base_dir . $file_name_base . '.*') as $filename)
                    {
                        if (file_exists($filename))
                            unlink($filename);
                    }
                    unlink($base_dir . 'thumbnail/' . $file_name_base . '.jpg');
                    $success = TRUE;
                    break;
                case 'mp3':
                case 'wav':
                    foreach (GLOB($base_dir . $file_name_base . '.*') as $filename)
                    {
                        if (file_exists($filename))
                            unlink($filename);
                    }
                    $success = TRUE;
                    break;
            }

            if ($success)
            {
                foreach ($this->options['image_versions'] as $version => $options)
                {
//print_r($version);
                    if (!empty($version))
                    {
                        $file = preg_replace("#/+#", "/", $this->get_upload_path($this->get_file_name_param(), $version /*, $diskArea_id.'/'*/));
//print_r($file);
                        if (is_file($file))
                        {
                            unlink($file);
                        }
                    }
                }
            }

            return $this->generate_response($success, $print_response);
        }

        /////////////////////////////////////////////////
        protected $audioBase64 = "data:image/png;base64,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";

        protected $wordBase64 = "data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAKAAAAB4CAYAAAB1ovlvAAAACXBIWXMAAAsTAAALEwEAmpwYAAAKT2lDQ1BQaG90b3Nob3AgSUNDIHByb2ZpbGUAAHjanVNnVFPpFj333vRCS4iAlEtvUhUIIFJCi4AUkSYqIQkQSoghodkVUcERRUUEG8igiAOOjoCMFVEsDIoK2AfkIaKOg6OIisr74Xuja9a89+bN/rXXPues852zzwfACAyWSDNRNYAMqUIeEeCDx8TG4eQuQIEKJHAAEAizZCFz/SMBAPh+PDwrIsAHvgABeNMLCADATZvAMByH/w/qQplcAYCEAcB0kThLCIAUAEB6jkKmAEBGAYCdmCZTAKAEAGDLY2LjAFAtAGAnf+bTAICd+Jl7AQBblCEVAaCRACATZYhEAGg7AKzPVopFAFgwABRmS8Q5ANgtADBJV2ZIALC3AMDOEAuyAAgMADBRiIUpAAR7AGDIIyN4AISZABRG8lc88SuuEOcqAAB4mbI8uSQ5RYFbCC1xB1dXLh4ozkkXKxQ2YQJhmkAuwnmZGTKBNA/g88wAAKCRFRHgg/P9eM4Ors7ONo62Dl8t6r8G/yJiYuP+5c+rcEAAAOF0ftH+LC+zGoA7BoBt/qIl7gRoXgugdfeLZrIPQLUAoOnaV/Nw+H48PEWhkLnZ2eXk5NhKxEJbYcpXff5nwl/AV/1s+X48/Pf14L7iJIEyXYFHBPjgwsz0TKUcz5IJhGLc5o9H/LcL//wd0yLESWK5WCoU41EScY5EmozzMqUiiUKSKcUl0v9k4t8s+wM+3zUAsGo+AXuRLahdYwP2SycQWHTA4vcAAPK7b8HUKAgDgGiD4c93/+8//UegJQCAZkmScQAAXkQkLlTKsz/HCAAARKCBKrBBG/TBGCzABhzBBdzBC/xgNoRCJMTCQhBCCmSAHHJgKayCQiiGzbAdKmAv1EAdNMBRaIaTcA4uwlW4Dj1wD/phCJ7BKLyBCQRByAgTYSHaiAFiilgjjggXmYX4IcFIBBKLJCDJiBRRIkuRNUgxUopUIFVIHfI9cgI5h1xGupE7yAAygvyGvEcxlIGyUT3UDLVDuag3GoRGogvQZHQxmo8WoJvQcrQaPYw2oefQq2gP2o8+Q8cwwOgYBzPEbDAuxsNCsTgsCZNjy7EirAyrxhqwVqwDu4n1Y8+xdwQSgUXACTYEd0IgYR5BSFhMWE7YSKggHCQ0EdoJNwkDhFHCJyKTqEu0JroR+cQYYjIxh1hILCPWEo8TLxB7iEPENyQSiUMyJ7mQAkmxpFTSEtJG0m5SI+ksqZs0SBojk8naZGuyBzmULCAryIXkneTD5DPkG+Qh8lsKnWJAcaT4U+IoUspqShnlEOU05QZlmDJBVaOaUt2ooVQRNY9aQq2htlKvUYeoEzR1mjnNgxZJS6WtopXTGmgXaPdpr+h0uhHdlR5Ol9BX0svpR+iX6AP0dwwNhhWDx4hnKBmbGAcYZxl3GK+YTKYZ04sZx1QwNzHrmOeZD5lvVVgqtip8FZHKCpVKlSaVGyovVKmqpqreqgtV81XLVI+pXlN9rkZVM1PjqQnUlqtVqp1Q61MbU2epO6iHqmeob1Q/pH5Z/YkGWcNMw09DpFGgsV/jvMYgC2MZs3gsIWsNq4Z1gTXEJrHN2Xx2KruY/R27iz2qqaE5QzNKM1ezUvOUZj8H45hx+Jx0TgnnKKeX836K3hTvKeIpG6Y0TLkxZVxrqpaXllirSKtRq0frvTau7aedpr1Fu1n7gQ5Bx0onXCdHZ4/OBZ3nU9lT3acKpxZNPTr1ri6qa6UbobtEd79up+6Ynr5egJ5Mb6feeb3n+hx9L/1U/W36p/VHDFgGswwkBtsMzhg8xTVxbzwdL8fb8VFDXcNAQ6VhlWGX4YSRudE8o9VGjUYPjGnGXOMk423GbcajJgYmISZLTepN7ppSTbmmKaY7TDtMx83MzaLN1pk1mz0x1zLnm+eb15vft2BaeFostqi2uGVJsuRaplnutrxuhVo5WaVYVVpds0atna0l1rutu6cRp7lOk06rntZnw7Dxtsm2qbcZsOXYBtuutm22fWFnYhdnt8Wuw+6TvZN9un2N/T0HDYfZDqsdWh1+c7RyFDpWOt6azpzuP33F9JbpL2dYzxDP2DPjthPLKcRpnVOb00dnF2e5c4PziIuJS4LLLpc+Lpsbxt3IveRKdPVxXeF60vWdm7Obwu2o26/uNu5p7ofcn8w0nymeWTNz0MPIQ+BR5dE/C5+VMGvfrH5PQ0+BZ7XnIy9jL5FXrdewt6V3qvdh7xc+9j5yn+M+4zw33jLeWV/MN8C3yLfLT8Nvnl+F30N/I/9k/3r/0QCngCUBZwOJgUGBWwL7+Hp8Ib+OPzrbZfay2e1BjKC5QRVBj4KtguXBrSFoyOyQrSH355jOkc5pDoVQfujW0Adh5mGLw34MJ4WHhVeGP45wiFga0TGXNXfR3ENz30T6RJZE3ptnMU85ry1KNSo+qi5qPNo3ujS6P8YuZlnM1VidWElsSxw5LiquNm5svt/87fOH4p3iC+N7F5gvyF1weaHOwvSFpxapLhIsOpZATIhOOJTwQRAqqBaMJfITdyWOCnnCHcJnIi/RNtGI2ENcKh5O8kgqTXqS7JG8NXkkxTOlLOW5hCepkLxMDUzdmzqeFpp2IG0yPTq9MYOSkZBxQqohTZO2Z+pn5mZ2y6xlhbL+xW6Lty8elQfJa7OQrAVZLQq2QqboVFoo1yoHsmdlV2a/zYnKOZarnivN7cyzytuQN5zvn//tEsIS4ZK2pYZLVy0dWOa9rGo5sjxxedsK4xUFK4ZWBqw8uIq2Km3VT6vtV5eufr0mek1rgV7ByoLBtQFr6wtVCuWFfevc1+1dT1gvWd+1YfqGnRs+FYmKrhTbF5cVf9go3HjlG4dvyr+Z3JS0qavEuWTPZtJm6ebeLZ5bDpaql+aXDm4N2dq0Dd9WtO319kXbL5fNKNu7g7ZDuaO/PLi8ZafJzs07P1SkVPRU+lQ27tLdtWHX+G7R7ht7vPY07NXbW7z3/T7JvttVAVVN1WbVZftJ+7P3P66Jqun4lvttXa1ObXHtxwPSA/0HIw6217nU1R3SPVRSj9Yr60cOxx++/p3vdy0NNg1VjZzG4iNwRHnk6fcJ3/ceDTradox7rOEH0x92HWcdL2pCmvKaRptTmvtbYlu6T8w+0dbq3nr8R9sfD5w0PFl5SvNUyWna6YLTk2fyz4ydlZ19fi753GDborZ752PO32oPb++6EHTh0kX/i+c7vDvOXPK4dPKy2+UTV7hXmq86X23qdOo8/pPTT8e7nLuarrlca7nuer21e2b36RueN87d9L158Rb/1tWeOT3dvfN6b/fF9/XfFt1+cif9zsu72Xcn7q28T7xf9EDtQdlD3YfVP1v+3Njv3H9qwHeg89HcR/cGhYPP/pH1jw9DBY+Zj8uGDYbrnjg+OTniP3L96fynQ89kzyaeF/6i/suuFxYvfvjV69fO0ZjRoZfyl5O/bXyl/erA6xmv28bCxh6+yXgzMV70VvvtwXfcdx3vo98PT+R8IH8o/2j5sfVT0Kf7kxmTk/8EA5jz/GMzLdsAAAAgY0hSTQAAeiUAAICDAAD5/wAAgOkAAHUwAADqYAAAOpgAABdvkl/FRgAALyRJREFUeNrsvXmUXNd93/m5971Xr6qruru6G419IRaCBEAQIAkCJEEKFBeQ1ELKsnOcxEnGc0zNyWQ9WTw+43gS2zqJncjJsWRpbMeyo7EiWZEti6S5AdwXgARAQgQBYuWCnVh6q/2t984fb6lX3Q2SdsgIAOuHU+haXi3d71u/5fv73t8VWmu61rWflsnun6BrXQB2rQvArnWtC8CudQHYta51Adi1LgC71rUuALvWBWDXutYFYNe6AOxa17oA7FoXgF3rWheAXesCsGtd6wKwa10Adq1rXQB27ZI18+N+wS9+8YEp9ymlCIIAKWV6EfFPQxpIKaL7DQMhROcxQsb3CYQQCAFCxI/HjwkpouMyP4WIrwuJkMTHZ+8XHceRvofMfM7MZxHZ20bHMUJKDCkRwsAwRPzZo9/LiJ9rGFJKaZhSihlCyM9JKe5XSs8fHx9/SWv9O0qpE2hNqBVaaZRSKKXQWqGURqkQpTRa68x90eNhqNE6elxpBcnz49cKVRi9Zvw8PeUxlT43va5ClFK0Wi0mxscZHxtDa82rO165uAHYtQvaAHA1sArYBFwXhsEsrbUH3Ayc6HrArn3cNgxcJwTXADeB/gyIWUKA1lCr1QjDcIOU4k/idOgHXQB27X/WZgMbgOvjy83AULT4UKMUCKFxHBfP8xEItNZFEH+IwELz3S4Au/bXtXnArUJwC7AazRo0g2hQWoPSSKlQSiKlRmtoNGpRPic0QklA9QkhvgnkgT/qArBrHwV0nwWxScAqrblaowe0FiihkagoqmoBov0kIaDZbOL7PhA/phWRf9R9aP01QAPf7gKwa5NtPnAvgruAq0Av0joCHSLydBg6go+OPB0iqVwFWmuCIKDRaKKURghAJciMXkJr+nUEQoTg25f73IAuAD/cFgAPAPcAy0DMQet+LSJQCaEjh6UF0X0RmLQGrTUivV8DklYr8X6kz8t6yPh5ZdBfi27xx10AfvpsKfCzIO4FsRQYAopJIaG1iLwViWcjdl+iE3To+HYEyrb3U0gZPy8DxLazU2hNWSnxX3SE3D/pAvDyt5XAzwBfiACoe7XW+SR8QhQyldIYRgQ+ESVwKciUAIPEI8aZnIiirCGg0WgShAECEYdgER/Uzg/bIAet6QP9ddBmXJjoLgAvD5Px5RrgS8CXBCzVGlsIbaXIIeOhiDyWlJ2hlRSEAtnhEUk9oNQC3w9wnFbk/aJ2TvQ6He+ThODkXRVoXdKab4CwQP9XwO8C8H89WPTH8O3PxZcVwP0Ifi4Or4ZGS0iLgLho0Gl+1871BEqBYeg2CGO0iTj/E6L9WBRZNQhoNpoEQZjCGXQE3fZ/k4AXFcfx9DJba/2NTHXsdQH4yVoO6AXmAOuBOrATOPbXBGIPUIoqVr4E4ssgrkg8jUg8WwIL0fZHWSAmIIzCcAJIOsCZVLkdhUmc23muh+M4KKXisMskJ5u5EUfl6P1VFpgS+FZ8xGXjCS8mABaAwbjq3CyEeEAgrk9yIyHEceBfAI8B7ge8Tl/8OsuBBwQ8IBDzEmokpeZ06njic54FUpKPiQ5P2EZMm1pJABIdn9xP+lNIjeM4BEGQ5nzt4kMk34TJVAxK6U6v2P4yfDP+MP8VCLoA/J+zEjAXWAzcBdwrENdApKCRhohbVQKt9UIp5Y+A3wB+DxjNvM5A7C2vBj4XFxKzNBqFjjVnGoTsdDgJEjPhV6DbhYWIyOQkL5sckqNjMkCVoiOXA1Lv90GDQBMA06YQk0p4MviSz5J4wj+81EH40wDgAHAFcCWRKuQeYKlO/tAizp9ETOYKHYEi9ipSyn8H4mqt+U9xxLw6fo27BWL2lPCmE4pDZCNc6rHSe0SUsyHJ5HIiBVlngdAOw+230mil0bIToI7jxlK0LO3Sae3qdwofmAKwDb4OT2gD37iUQfixA/AC33Q7zuWuAW4B7gTmJF/7bC2YgDCKj9GZUOhI15d6JvHzQnBj7EFnisnvnWU3YqCJ5DoptjNeR0QgT44VnYVH2yMmj4v0/dpEs8h4yAjJnufiOE7szSZrf2O0XwCMnXSMnhaMwH8GLOB3gLALQGgn2e0/qAxD/SWt9e8RyZOmxp40d4pPbqocUbFIVcc5kcAwUhe3JGbYYtojCpciQVmW4zViiOl2DYoWaKmRSRdNgJFUtWkLTaT5WFT5tnNApRSGIVMOUGcKGkH0nCj385FSpuCRss0pTv9lTcDdLkCi313FVNCUcPxbMaPztUuRJ/zYAbhgwcJODkXKvkpl4mdPnTo1nD0RQiedAhCi04NGYbftAYXuTP6FiOgQKSOgCIgQJEGhIoVzR65HO5QKAQmI4+ekx6QeMbrRLj4yHjMbfjORWyS/V9wPjryflwFUAhrJtG46/ltIqTtut8EXvUZSnEReVaC1FqD/fexOf/tTD8C58+al130/wDBkIV8o9FarVZrNZiqNj5xMpn+qY48nZSYMt91YRPrKtmeUIvIISqGkJMr/FUJLUKBlVPEKRCRHlwqhI/m9jrsQQggUAqEFAonWCimNGCwqDZFtikUhhMwAJ75PGWip0EqijQgoruum3k+pSHUQ5YE69YRag+e6+EGAQGNZFoZhYFgWMv6SJeBTqu0VE11h20tqMy7OEPDb+tMMwL1v7oldg2Th4isIg2DMNK2dg0ND99br9Xao1TomXI0YjJ2usO3pIiBII1nvINNuROQFRVo9RpjRkRfUMiWC07CcdDFoCwC0iumPbC83fjwCQKTli6rhdkiWUrR/opAR2qPK1/NxHCfjMVUGyNGXrNVqocKQ2bNnMXvWTAbKA0gBR955hxOnztDTU4y9nM4UJPqDbudA/1si//7vP7UAPHf2DFprPM+nWqsiwC0PDDxk2/m/Y9v2lRGg2jng5HDbzgVlJ7+mkvwpS4lkhABJsEvbsG0PqtFRWM6E5OSxFPMZb5wUQUJMTld1Z1zP/ozfTqkQx3EIwzDO/XSc8wmCMECFCqfVZMGC+Vy/di1z582jXO4nn88DMDg4SLX2PPVGE8uypiHEJwOwA4wFrfk3gI3gN9AXf2HysQPQNM1Y9xZSmRjHsiwUOrBM632t9ZUdvJZOCYz4BGc9oMqAMLpfaY0UbUJFJ+SKJtPRSPIynWJDRslh5AHjaldokWmJiZT+SbwymW5GZ7djagckoVzQGj8I48o3Q0bHq/kcx6FerbL6mmu45567mTVrVvr7jo2NcvDgYU6cOInveR1fRq3VBWiZqWBE64JG/+v41lcvdormk+QBlxim8beFEJs911sceP5wspRxMm2T0hoZL5j1WGnelFTMtENlu6nQ6QWTeChUVO1qoZFapt42Ba8QadHR7pAkvVwdgX4K+SwmdUIigCmtcV0HpcI0VxRxQRQEIc16A8OQ3H33XSn4PM9j27bt7N79Bq7vEoYK0zAwzNw04LsA6DL3R7SVKqD4ZaGF1OivchH3jj8JHnCD1rqstfqu1GYvkEcrEEYHq5/kgWTCZpIH6rhCTkJju4XVJu9SsrgDdKSEcwJIpeN8kDi0Zmk4QIdRbmkaBlLmUg5Oi8lMXWcPeGoYhsD3aLWc2AsnAI2SzGSN7TWrVjA4OEDS7Xnn7bd5edt2TCuHlCYyXcM8bYid9ja6rT1MuzCagkb9skaHcXXsfCoAOH/BwjvDMBw8cvjwcLQAp/MymSds0y5xnpUBYRt4EZErRKbHJabzghkIJqCWIj0xAkEYhnF+Fi0g7+/r44pFCzl77hxjExVs2+4IvdmuyORWXLTIO0iJ7iAIIF6Ej9BIw8SQRrwIX2BZJqZppZ9HqZDx8XH8ICBfKHRwgRfycB8EPp328RQqep6tNb8aJyX/EWhe9gC8eePG+WEYDniex4njx5GGvHDHJEPEdviSTK4jEO0cMKZpECIOjR/sBUWG1xNRMkgQ+lQrVZYuvoJ777uXK5ctxbJyHDp4kCe2PEWj2cTO5+P31nFK0M73IkWLJgw0eTvH4qVLWLhwAYODg1iWSRgqHMdh5Px5jp88xfHjx2k0W5iWRbFY4sSJk3ieRz6fR0qDoaFB+ntLOJ6HZVkZb8uUfC/iAMnme1PApxPwpYptbWnNr8XV8W8TKYsuXwBaprXNztkDd27e/HMP/+VfmuPjY+Ryucy3NpNHZbogUV411QNqohAplGjnj0kPVzDFC6YdBpFwzTFFotu5YRiGvHf0GP39/QwOzQBg5apVeL7Plq1P43ketp3PiJqj/4MgIPB9pBSsWXMtG2++ifLAAKZpdowfMU2TZVdeybX1OqOjI7y1/wB739pPGIaMjY2y/8ABbtqwASklc+fN44brr+Pl7a/i+z6GYaY5ZiJISIq6KJVJijKRAWAbjG3eWmWBKTX63xApjn4dqF0sADR+/dd//WN9wZ27XturlHozny/Mn7dg3rXvvv22zP5hOy6AiInppI2X3E96XSCJjxGkHjE5CUmF2T4xsXwr/Qc6o/MzTAOlNePjY7zxkzdYu3Yt/f39WLkc5XIZO2dx9NgxwlBhmGac80Vv4DhNKpUJ7rjjdu7ZfDel3l6klIyMjLBr12ts27adQ4cO47ouhUKBvv5++stl5s6by6zhYcbHx3Fdl8OHjrBs2VL6+/uxbZtyuR/bMjlz9jy+5xGG0cyXINRxyhBg50yWLF5Eq9Wi5TixqpopoTiOvajpveTNQA6tt2u0l3SIkhV8msz12DEEQYDjODitFgAPfuXBixuAf/7nP2J8bCxoNOrbZ82e86WhoaEZ777zDlprjKiR2x4SJGUH2FJ+OAZSFG6T21ngtY9p32yL6rJ5ZlKFpnyjANu2EUIwOjrKoUOHue66tRSLRWzbTguEY8ePo7TGNM2YEA+pTEwwPDzMz375ZyjEOdvZs2f5zv/3p7y6YxdHjx5lz5432fLkFva99RYDAwPMmjmTfD7P4OAgs2fOpDIxwfmREQ4ePMjChQsjDrBQYNasWcwaHiYMAjzPAwGmaZC3csybO4fN92zmxnU3MHN4BmOjo0xUJ5DCmFR8RB5Qk/GMk/NDrW8BChq9DY132QHw23/07YGJiYn+eqPxv50/d27l8uVXze4pFDh27HgbdFlPl/WATPKMMbBk3FJLCN30mCwwmcYLirbKOPGiyb98Po9pmpw/f57333+fNWuuxbZt7HyeocFBQt/n5MmTaCGwDBOtFaOjo6xdcy3XXrs6Drsh+/buZeeuXQwMDlEoFCgWS9j5PEePHuXFF17EtnMsW7oMK2dR6u1lcLDMuXPnOXP2HHv37Y3SgMFB8oUCM4aHWbJkMVcuW8aihfO5ctlSNmy4kY0bNzJjaIhczqZYLDIxPsHx4yeRhnHhSniKB8xcR98M2Gi2a629ywqAjz762N81pHFzZaLytUarOdv3fVavvhbX9Thz5n2MeARb9tLpATtB2K4Lk7FqKdLSH9ljUsSJdrtNJlLjTDHjulG3wrZtTpw8ycjIKMuXL6dQKFDo6WFocIBWq8Xp0++DEJimRaNRZ3jGDFavvibtUoyPjXHo0CG0FJiGST6fp7e3l1Kpl0ajwSuv7GBwoMyyZUsxDJNib4mcZXH8+AlqjSbbXt5GZXwCwzQolUr0FIuUekvMGB5meOZMSr29CCFwXZd333uPbS+/wo5dr2OYZoZyyYBPT8r/VOdjKcWluQXIa61fQWvvpwXAj70IWbx06aowCAfeemsfhCHH3nuPvt4+Nty0gUqlwulTJ9NQnJUjddIupH3gxGMm+U7S7xVxIZJIBqLuRyLJoi1ElbJjHW+r1cIyTebNncPM4WF6e3vRWlGp1jh96lQagodnzeLW227FcRwOHn4bwzDp7y9z8OAhWq0WhUIhKiLmzmX58uXsfWs/xWIxLYoGBgawTJN33nmbH/yPH3L1ihVcddVyTMNk8RVXMG/uXPzgOGEY8NgTT/D0s8+yds21LFu2jKGhIXp7e5FS0Go5VCoVTpw4wf4DB6jVGwwODmJgtgEmEqoq4h0VOupxp4WJSgQ0aXiOwfgvBcLUgn8HTFwWVfDsOXN8rXXgei4H3noLwzB46629lPpKfOb2TWx5/HEqlYhvS5QvWSFCh7RJZFt1SQ4nMsRNCr9UGJBoEkSswJdxZyMMFI1Gnfnz5rFx482sWrWSuXPmkrPtC/4u8+bNY9Omz+C6PsdPnqDQ08Po6AivvrqDz33uvrR3e/NNNzE+PsHxU6co9RSRhiQMfIrFIgsXLeLggQPs2fMmS5YsxrIsiqUSc+bM5t2jR+nt7WPx4iWMjo3y0ksvs3XrU5RKJcrlMoZh4LgurWYTu1BgYGAgqroNs03Mw+QuSKpbzIItAZ+KV9xFq+40Gv3PiATDvwJULvkq+NUdu0aFFkeGhod/ptVq5kfHxtBKMToywqLFVzB7zhxOnTgRUw5GR1HRzgWZel880ZSOAkOkVE1aDWdzSREVMkEQUqlMMDxjBr/wC3+Xz3zmNsrlAYy4b/3m3n28/trr7D9wgGq1yvDMmamX7i+X6e8tce7sWWq1GsVSiUMHD0WV7cyZSCnp6+tjcKBMNS4w/EnTYBuNBlcuW8qqVaui1xWCo0ePcuzYcQzTJJ+3KZVKDAwOMjQ0RE+pGE9VNejpKTJjxjADAwMUCj3kTCvt8Og219cOm8k3UGmUiKagCi0Svxg9puPnk1bO64ABjX4Jrd1LOgd8dceuM0qro6YhJmbMGP5CZWKCSrVKEASMj0+wYuUKCvkCp0+f7gixndVwZ8XbUf1mcjwxTe7XQcvE3RPXdTh//jx/62/9HLffvil9lVq1xve+/2c899zzvPrqDna9tovXX3udvXv3Mjg0yOzZsxEiCqelYg+nT5/G9QOUUrz5xh7mzZvL8PAwhmEwMDDAnNmzyFkmruNQq9Wo1qp4rsvSJVdw7733MGPGjFgCpnjuueeo1uqpVExIgW3bFAo99PQUKZZKFItFCj0FbNuOaKxYpKE+qAuSgEtPyvtEpCjK5oEdFbDWNwCDaP2i1riXLAB37HwtUbP8xLbzpf5y/83nz53HcV2azSaO02L1mjW4jsvIyEhnVZwBXAQmUkCiMxVwZp5PJw/YAVGItYK1WhUp4IEHHmD27NlpCH/pxZd49vnnCcIQ285TyBdoNpvsf2s/Bw9ENMncuXMQUjI4NEhPvsCpU6fxXJdKtcq+vfswTIMlS5YgDYPywAALFi5k/tx5LFgwj2VLl3D92jVs2rSJRYsWpr79xRdf4id73kRKoyPFSOY3p4VTXPmreG6znq6izYJxUjGSyfU6wTgdACMx5HXADK15QWvtXdIABHQYqt2lUt+yQqGw4uyZ91FhyMTEBIZpsXr1akZHRqnValOr4QyN0qZiZLtKnswJpl5QwDTesF6vo1TA7bffngJQKcXu3T/h5MlTWLZNzrQoFAr095fpKfZw/Pgx3n3nHZZftZwZM2YgpcHA4AD5nMWRI28jpMRxHPbv38+hg4cYGCgzPDxMLpdjcGiIBQsXsmTJEhYsWEBfXx8gqNVrPPzwI7z88rYoezWmW6jUBpNCJXlatrU2Pb2SBZViauWr2+KMdHRcdtFTlBRKrVkDDGutX9Bae5csAGNsNEHvLw+UbxZCzjlz5n0EgnNnzzI4OMBVK67m1KmTuI7brnZjk1MI5/Z0+5T0m8YLZm9rAYY08Dyf48eOsf6mDSxbujQ+x4Iw8Njz5t72cxAYpklPsYe8XeD4ieOcPz/CypUr6e0tYZpm2i159+ixOIeEEydOsnPHTrZt3865c+dpNVt4vkez2eTMmbPs2/cWT255kkceeZTDh99Ga7BzdkaImxEhZH81dSHQTS0+UhBNKjYu6PFS0HWqaLRCKvS1oBdrzfNB4LcuKRpmstolCIJ9CH71yuXLv1uv12ccOXwI0zR59dVXuePOu9i48Taee/aZaHLApGqYuDc8WYwgsiuCMnSOyPSHIy0gaKGwchZmzmLby9tYv349A+UyUgoWLFzEjTfewLbtr0a9X0OjQoU0JEMzhgD4yRt7+Mnu3cz5/OcAKJZKrL3+elzf55lnnyOfz2MYBtVqlaNHj3L06DEsw8CM13fIuOetlUYaRprXCSk6VNvtCQ10rG37INVLhtObEno7wu+HgG+SF4Roqeffjj/CPwPGPqkq+H/JRjW+529VSv3aNauv0XPnzcP3fVzH4eUXX6BYLHL9unURJcP0ql8VXzpOCkxZM0tHCyryJEprCvk8M2fOZOuWrex4ZUf6OuVymds2buSG69bi+1570U8siO3v76dUKuL6nXrOcn8/N65bx+2fuQ2hNZZpMTg4xPz5C5gxYwZ2oRALXaNOj23b9JfLDA4N0VMsxkrvqVK1dnhMIKQ+Ur6XLFlIw3biCae8x4eBL37XaJ8RIwzDv4PW3ySaj3jJiBGm84ra9/3dhUKhWC4P3DQ2OiqTOcnjY+Osvf46As9ndHRkqjAhzgPl5JwvrYIn54IZtUj8vzQMpDTwg4A339zDypUrmDVrFkIISr29zJo5jGVZnDlzBt8P0hColGLRggXccednGRoc4r333sPzPEqlEvlCgVKphO95HD12DMvMIQ0DO2fTUyxRKvVGnY2eHvJ2HtMy48X1TAq7nekEHVMSPkK+N8XLRfyeirshWenbVABOBaRSmjBUeJ6H67SE4zjXBIG/wHWc5wDx4FceDC45AKae0Pd3lMvlK/N5e+W5c+eE73nU6zVc1+WGG9cxNjZOrVqNWnCis9eblWgl+RrTgLKjRM6cWMuyyOVs6rUau3fv5qqrrmJ4eBgpJb29vcyfP4+lixdTLBbI53IM9PdzzcqV3P/AF1m0cBEA217exve//wPmz5/H6dOnefqZZ3ljz56Iz5QdjcDOaxnUpUrvSZ9xCjA1Hy30TgO+dgiezsMxrffTShEEYbyeuUWr1cJptWi1WqLVbK4OAl9ora/9yv/xlW0fa8qmP+Yp2L/3rT+4oAA1Bs+Kvr6+7+x9c8/6PXv2pLTD+ptu4splV/LU1i1MVCqYppl6PZFulyU6lTQZILa38spU1IhU+ayJNHVBEOA0m9h2jgcffJB1626gVCplvyQEMUme7ZLUalW+8Y1v8ebeval37O3tpVgskcvl0m2/srxkRz7c8UWZ5kRMASsk63+ZNJrjAr3ddihHg1IfKeQqrQl8H9/zcb1o7xLf8/A8F89zcR0X13EIg+A4MLHvwFtrLvIi5IOLE63VgXq99v9cs3r1f6vX63MPHTyIlJLXdu6kt7eP2zZt4qmtW3EcB8uyopAaLcaNRiFk57II0dG6m3JCBYShwvd9TENSLPSg0eQsC8/z+d1vfIONN9/M5s2bmTdvLuVyGcuyMspkaLVanDt3jh//+MccOXKE3lIJjcAwJLmcHR2bnaiQdWx6MsgyetpJfywdj+tg8rKVDwi5acGhsnKstO/2geDTsYDW8zxc141B50fAc10c18FzHDzPQwVBhzDkovaA3/x//+BDj1FKkbPtB03D/L3nn30mf+LECUzTpKdY5L7PfZ5Ws8kLLzwPmljIStvDSTmNrEuko9HS+6VEhQHNZotZM4dZf+ONzJs/j1azyaFDR3jnvfcIgoDRkRGazRZr1qxm1aqVLFy4iN7eEkJIqtUqhw8fYceOHdQbDfr7+1MpvZDtCQlZ7zXl+rRqnekZgwtFjg/M9zpCcqwH1NMBMFnMnwAvAprrRt7Oj4HouS5OK6KRVLy22bZtrFzud4Cx13a//lsXtQf8qFSN6zjfNovFJRtuuvn/rtfrjI+PUa/VePGF57n3vvtYu/Y6XnttF0IKDBErWkgH9XVQLzo7Ya29mALHcXGdFps3383999+f3n/rxnEeffxxtr+6gxnDM3GcFvv3H2DHjp34nodpWeSsHIZlRhq/UilSt1i5qIKNQ9zkpC9aV/whX+hJc3Amg3IyL51u8fAh4EsLDUW7eo6jR7JgPuvxXNeJwm4MOtdxcB0H33PToVD5nh4K+QJWznpWa/E10B/7oqaf2oDKeDzFb5R6e5evv+mmn33umacjzeD777Nt2zY23f5ZKtUKhw8fomAX4n5m6kIRsVggGoshQMtk6nLKEwZhiOu66dSBJOyVBwe5757N7N27l1qtRV9/P8ViiSDw00mmpmEiYrWOYRqYqf4umbTVHkipogkzqfIaQCqZXk+GK2mpLzy/avKsogSDamrBkV5PpVix91PtIif5G4Sxx4tA56bhNr3ttHAdB8+NaCjTNOkpFLDsPLmcddI0rYcNQ/6G47jn9Sewa84nTkR/yLFuo1H/V/Pmz196w7r1a7e99CJKaw4fOkh5YID169fTrNc5depULIFvE9AqlnEl/GF2vW40Os3AtizONZv88Id/wfwF81m5YmX63hOVKkHQHhOSy+XIFyJSWRAto/Q8D8/3O3JMLUDG82TSXY5k26Ol1+N/UsvMGudMKM6MDekYFzJpJPt0RUb2fqXarbUkFUmWiHqej+s6Kdg8z8NznTjMOjhOE9+LRk0bhoFt92Dn8xiW+Z5APJSzc4+HQfh0xziVy8EDTqJOjtVrtX949YqrH69Vq4Ov7dpJLpfj9V07Kff1ccutt/L0U08xMT4Re7Kky5HMZyMlYLNEiFIKy7IYHp7JW/v28dXf/Cq33nobs2fPxnVd9u/fT61ej5XN8YmMQSKlJggVjusAIp4hGI9yUxo1qdqVSkatr/g6EE3jUpEMKqX7VWeBxHScoJochSeRykwNv8mm2FprgjCIczuXVsuJq1kP13HxXYeW08JtOQS+hwZMy8S289h2HsM0jwghvq8FW3Wotn+SwPvEipBv/f4f/rWfEwQBlmX9gmVZ//35Z5/h8KFDWFa0Su3ez38eKQyee+4ZAt+PKuNJkq0sJdN+LF7UpDX1RoPz587iez795QGEgJ5iMVrLm8t17LYe6fUg8AN83489Siet0l7/JKYA6kIFyRSqZZoq+YJ54JTOh4p740Y6AMn3o4lcySUbZj3PxWm2cJwWge9HKYZpYufz5Ow8hmkcEEJ8B83TWqvdSTvQylnpUCjP89Fas/2VbZe+B5zOI/q+/z3DMJZsuPmW36xMVDh37izj4+O8+Pzz3Hvf57hh3Tp2vrojWi5pyEmDjPSkYiBanJ70jnt7e+kp9OAHflSB53LYdlTNxouRM0OuNDpU+En7LTMteNIIhvYGD9Plb3pq9avR03q9aR+fDMDMcE7LtJGGRCkVD0Fv4cRFRBJqk0LDjQnlwPcRMbeZs23ydh5pGvvQ4veFEM8pHR7gp7Az4kWzTUMMwt8tFourbv3Mpp9//NFHcByHE8ePs33by3z2zruojE1w4OCBiJLRU09XuqA7I1ZN8kErZ2HlclG+IwUypneSnnI6iFxpfM9PJf7pfGk+HIgX7LDp6VVXF6qMp6NipCkxDRPDMAjDENfxaLUaOK1WvPl1XFR4Lp4TUSmO2yLwfAxpYBcK5Gw7Erea5htovi6EeFFp9e5P87xfbBvV1MIw+JVZs2ctvXXT7eu2PvEEUij27X2T/v5+brhxPbVGnZMnjmNZFjKuhCd3W0hJWpFSM8kmMUIIlBYIHS9gStTC8XBKpVQ0sVRk9nPTGmLQTw/EDwajnoYvnPy5L2SWZWGa0YyZIAhoNpqpsNeJiWIvBp7bctKqNvD9lErJ2za5nI00jZ1oflcI+YLS6rS4CE74xw7Av0nimp0673nesSAI/9GyZVc+Xr2lMuOl558nl8vxyvZt9JXL3Lh+Pa1Wk4mxsbQCvhCtITIFiYySQrQQGCkk2sMliVeW+X7QGQyTElvpdJH8VCBmB49ne7u6c8eIC3nEyRSVkORyOaycFa1p8QPq9RqNRgZ4CY3ieXhOC6fl4DrNOEUxyBeL2DmbXN7GkMZOpdVvCSFe0lqPcRENM5cXmQdEawjDcJfne//w2jVr1LXXrcVxWviez7NPP8Xo6Cjr1q2nWCoRhuEHexGdCa9JwRDPFyQ7yiwdCK4IAr89OGnKgKBsa4x0P4VUPNUhrUqOb2+70JbGT38RQlDIF+jt6yVfyKNCRaVS4fy5c4yOjjIxMU61UqFWrVKtVqhMTFAZG6MyNk6z0UAISU+xSG9fP719fWFPqfSyNIx7hRCfBR4m2tznohohfdEBMBUFeN5DQshfWXfj+mDxkiV4nku9VuPpLU9gWQbXrF5NLpdDKzVJIaynUBYqBle2S9L+2ebWko2kO6rOjMaw3U9l0gTTNog6njf586i2bi/7WlIa9BSLlPvL5PP5WKY2xtmzZxgdGaFSmaBajYFXqVCZGKcyPk51Yhyn2UCaBsVSid7+fnr7+ps9xeKzhmHcheZ2YAvRWLaLcnb5xbxbZuh53h+VywPLb9l424OViYoYHx/l9KlTbN2yhfu/9CWq1SpHDh2alP9NSvbjTUC0yO6CSVqsJB4yDIPYo7bzxg6q7kPyvmxdIrK8s5jeyyeUT6FQIGfnQEfCh0ajTqPRiDoWftwqcxwc14nDbAvPddE6mqpv5/NYdh7LssZM03hFBeo/KKW2C8ElYRf7dq2VZrPxW3MXzF9+66ZNm7Y89hie57J/317KAwN89o47adTrnDzZnpNyoTyQdGJ9Zsik1mlzxff9zEY2U9E2NY+bvnd2QTAmW17G49t6eork7BxKKRqNJvVajUajgR8Tx57vx8BzcVtxu8yLgZfLRRxeLo9lme9L09ymlfqaCtXOj8wvdgH4UQqakDDU79VrtV9esXLlD6sTE1c89+yzSCl55eUXGRwYYN369TRbLcZGR9N5Kdm8UIlogIdQEi1FOsA8s/UNKlRR8ZTZSjWZV9je00N3FiAZsrkT+CLL8kfaPCJVtmVZ5AsF7FwEvHqtRq1Wp9Go47tuBLoMgew0W3huKyWBc7ZNvhCRx5ZpHpVSvqjh6yoMd4tLxeVdYh4w2t4gCHbVG41/ccOGDf9tfGy8/NquHZimydYnn6A8MMj6DTfx4vPP0Wy10okGaXWdTEgV2fnJmV2atE4Lj86h5ZO4xWzzQoj2Boq0h2WKjvQyAZ7EztkUegrkrBxBGFKtVKnXajRbTbwYeH5c0bqOi+M0UypFQzy1qxAtgDLNw1LKZ7XWfxSqcHeytvhStUthx/RkOtRDlmUtu/X22786Nj6af+fwYZTS/NXDD/H3/8Evcv0N63hl+7b2GN9JtEYy1d4Q6V7lJFNPkz09Lkw6d4bkyVGuc6vVDPBsm3y+gJXLEQY+4+MT1OtVWq1WJAD1/QiAnofrODhOC7fVShU5uXw+8pi2jWmae6WUT2nEd1UQvPFJCUS7ALxQuS4lrWbzm+Vyefkdd2/+ysT4OCPnzzM+cp5HHv4xv/D3/gGrrrmGN954Y2qbzognDeh034dIUaOilf+dK+uyXJ9IV9aJLCplsnEgcXhuy99N0ySXi0KlZVkEns/4+Cj1egOn2cL3PQI/wPOctI3mxv1bFQQgJfl8gXxPD7m8jWmYuwxDbtWKH4RhuE9eJsD7xAD4SeYiQginUqn85tx586666557P/PjP/8fuI7LO0cO88Tjj3L/l79MrVbj8OHDnRrAeCaPIUg1dEJrlA7jbkd2Y5wIbErrWA/YTg1T9ZVqp3vJyAwpBfl8gUKhgGGa+L5HrVqlXq+nFW3k9Tz8DPBcx4mUx4ZBvtBDvpAAz9gmDfkIioe10oc0l6eZl9KHjT3VyfGxsX961YqVD3/2rs1XPPHIIyAFr7+2k4HBATZ99k7q9Trvv/9+vOVCTKnIzJaqccstDGMtnRDR/G+h0i+REPFmcVKAAiXibRo0KCGJNVIIZJyj2RiGge/5TFQmaDabaXgN4uLC9zIer+WAjuZQ53t6yPcUsHM20jCfkYbxF8BTWqt3uMzNvNQ+sBACFao3qxMT/2jdjev/fGR0pPjKSy9iKIMXn3uOwaEhNt52G09t3UKtWou2ClPRpHwto2JECBkT07GGUCV0jOiQ9SfbEHduci2S6QFYVkSHSCHwYv2d22rhxDtl+jHwvOxaC9dBaY1hmuTsAoV8nly0zuRJAX+GEC9opY5dqlXtZQ/ATGX8hOM4v3b7HXf8l9HzI+LIwQM0Gg22PPoYg4NDbLxlI888/QxKhbQrxfYevGlnJLOeYzJBk32/bLfFsixydjTbxXMzGjzXi0Jt0C4uImC2orUWOlqRl8/lyOcLUZ/WMP4KzXcQ4lWt1OlPC/DS3P5S/eCRfMv7fUOaf3zfF77A8Mxo77Xz58/z6MMPYVk5rl+3jjAMO1pfIuYXEwB2jDJr8zRT+rdJp8XO5zFMA9dxqFQmGB8bo1KpUK/XqTfq1Oq1uGVWpTI+RmViHN9xMEyTUqlEqa+fvv4y+ULhESnkfUKIr2it/xI4zafQ5CcBjE/8ItNpqa7jOP+6r6//5ft/5svk83mEFLzz9ts8+vBDLFq0kJWrrom7HO3xZAko07ySzh5uKhLMzJmJNpM2cR2XWqVGZaKSFhn1Wo16rUq9WqMWA69WncDzfSwrR09vb9Sn7e8nXyj8SEixCSH+d631k8BZPsVmXvq/gq44TusX586f/+zn739g4Y9++AOklLzxk90MDg1x9z330qjXOXr0KIVCoVOUMN0XKN66NbmeqK8dp53XBX47v/N9D89x8XwvWmsR+AgRLXJK1Mc520ZK44dKhf9ZCHFQKVX9lEXayxmAoLV+x2m1/v7VK1c9ecddmwtbn3wCwzB46cUXGBwc5IZ162g2m4yOjnZslpPderWtpiZemBRt7ep5HmEQrQ9pXzx8L8nzXDzXi4En4kXcNrZtaytnOcIwfqTC8D9prY+AcLqQuwwBCKCUejkMg39y4003/eHI+fPmT3a/jus4PLVlC0Mzhrlh3Y1se/klGo0GxiThwhQQEi9KitcJB1nwJVWt16ZYoiWNNpadx87lQitnVaU0fqyU+o9aqbeFQHWhdpkVIdNhMAzD7xuG+Y07N2/Wi65YjNaakZHzPPrIQ6A1165Zg2WaU3PAREQahunqsmajTrPRoNlo0KjXadRr1GMhaL1WpdlooJWKxrT19VHq6/N7isVTdj7/ByCuU0r9EnAYuuC7/IqQCxQmWmvH89yv9vWXt37ui1+kPDCIlDIqSh55mDlz5nDVihVRlM3kgkopwnh6QKvZpNloUG82Im1ePSowqpUIeE6ziVI6Al5vL6XeXq+np+ednG1/XQixQcM/AY51ofUpC8GZfHCiUa//n/MXLHj88/fff/WPfvADAF7fuZOBgQE+f/8DNOsN3n33HbRSMfhCgjDK88I4x/N8nyBWqASeF6+uM6O9OmwbK2d5pmkeNgzzoVCF3xJwpgunLgCTTsl7jUbjwRUrV/7FHZs3z37ysUfJ5Syee+YZhmfO5IYb1zMyMsK582cj8AUBQRDEQoFIGhVVuD46XtZZKBajcWw5y5eGscc0rUdUGP6+1nqkC6MuAKeA0Pf9bZ7r/eq6DRu+Pjoy0rvjle0IAY8+/BALFi7iqhUrOHXqBI7jRAD0vLRfm+jwDMNM19OaphUYhvGyNI3HwzD843h1WdcuNgBeFK2keHtX1/e+XyqVrr79zrv+r9GREY4cPkS9VuOxv3qYX/ylBykWi4yNjMa0SkS36LRPa0fKY8sKpZRbpCGe0KH+nlZ6vAubLgA/6mdwG/X61wYGylfd+4UvPFD5XoVzZ8+wf+8+Thw7zoKFCzly4CBePIbDNA2sXEQcW1ZOSSl/LIR4VGv9l1rpahcuXRrmb1KUjIyPj//LBQsX7rnz7s3kCwWUDnnzjTeYNWsOQkaDego9RYqlPoqlkrJz9ncNw/h5rfUvKaW/A3TB180B/+beMAzDd8fHxv7xqtWrfzg2Njr3sUceplKdiHamLBYJQ0UulwuElH+K0H+hNdu0Ul3QdQH48RYlruv885s3bvzj/v5y38zZs3A8FyuX83JC/kkYBn+G4A2tdFV0G7VdAH5CIPyRlLJ27dq1/9YwjJWvvfban16xeMn3Tp44cSAI/FoXdl0AftIg1GEYvm0Y4REhRD7w/QM52z4ENLpQ+CmdE61196/QtW4V3LUuALvWtS4Au9YFYNe61gVg17oA7FrXugDsWheAXetaF4Bd6wKwa13rArBrXQB2rWtdAHatC8Cuda0LwK51Adi1rv2N7f8fAA7LsgfEw5G0AAAAAElFTkSuQmCC";

        protected $excelBase64 = "data:image/png;base64,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";

        protected $pdfBase64 = "data:image/png;base64,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";

    }
