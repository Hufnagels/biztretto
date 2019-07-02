<?php
/*
 * jQuery File Upload Plugin PHP Example 5.14
 * https://github.com/blueimp/jQuery-File-Upload
 *
 * Copyright 2010, Sebastian Tschan
 * https://blueimp.net
 *
 * Licensed under the MIT license:
 * http://www.opensource.org/licenses/MIT
 */
@session_start();
require( $_SERVER['DOCUMENT_ROOT'].'/../phplib/upload.class.php' );
$upload_handler = new UploadHandler();
