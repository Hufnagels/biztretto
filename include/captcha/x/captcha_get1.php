<?
function rand_4char_text() {
  for ($i = 0; $i < 5; $i++) {
    $string .= chr(rand(97, 122));
  }
  return $string;
}

function rand_2_words(){
  $word_1 = '';
  $word_2 = '';
  for ($i = 0; $i < 5; $i++) {
    $word_1 .= chr(rand(97, 122));
  }
  for ($i = 0; $i < 3; $i++) {
    $word_2 .= chr(rand(97, 122));
  }
  $string = $word_1.' '.$word_2;
  return $string;
}

function random1(){
  $alphanum = "abcdefghijkmnpqrstuvwxyz23456789";
  $inc = 1;
  while ($inc < 5){
    $alphanum = $alphanum.'abcdefghijkmnpqrstuvwxyz23456789';
    $inc++;
  }  
  $str = substr(str_shuffle($alphanum), 0, rand(8,10));  // 3 Being the minimum amound of letters returned and 10 being the maximum
  return $str;
}

function generatePassword($length=10,$level=3){
  list($usec, $sec) = explode(' ', microtime());
  srand((float) $sec + ((float) $usec * 100000));
  $validchars[1] = "0123456789abcdfghjkmnpqrstvwxyz";
  $validchars[2] = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
  $validchars[3] = "0123456789!@#$%&()+/abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@#$%&()+/";
  $password = "";
  $counter = 0;
  while ($counter < $length) {
    $actChar = substr($validchars[$level], rand(0, strlen($validchars[$level])-1), 1);
    // All character must be different
    if (!strstr($password, $actChar)) {
      $password .= $actChar;
      $counter++;
    }
  }
  return $password;
}
function customError($error_level,$error_message,$error_file,$error_line,$error_context)
{
  echo "<b>Error:</b> [$error_level] $error_message <br>";
  echo "<b>Line:</b> [$error_line] $error_file <br>";
  //echo "<b>Context:</b> [<br>";
  //print_r($error_context);
  //echo "<br>]<br>";
  }

//set error handler
set_error_handler("customError");
if (!isset($_SESSION['li'])) {
  session_start();
}
ob_start();//Hook output buffer
  //require_once('db.php');
  require_once ('dbc.php');
  require_once('class.base.php');
  //require_once('../include/class.json.php');
ob_end_clean();//Clear output buffer
//$mA = new Auth();
//$mA->protectPage($_SESSION['office_nametag'], $_SESSION['u_id']);
require_once( 'hu_HU.php');
require_once( 'fv.php');
unset($_SESSION['random_number']);
$string = '';
//print_r($_GET);
//exit;

if (!function_exists('imagettftext')) {
  $_SESSION['random_number'] = rand_2_words();//rand_4char_text();
  $dir = 'fonts/';
  $image = imagecreatetruecolor(165, 30);
  // random number 1 or 2
  $num = rand(1,4);
  switch ($num) {
    case 1:
      $font = "capture.ttf";
      break;
    case 2:
      $font = "moloto.otf";
      break;
    case 3:
      $font = "walk_rounded.ttf";
      break;
    case 4:
      $font = "walk_black.ttf";
      break;
    case 5:
      $font = "young.ttf";
      break;
    case 6:
      $font = "piaggo.ttf";
      break;
  }
  // random number 1 or 2
  $num2 = rand(1,2);
  if($num2 == 1) {
    $color = imagecolorallocate($image, 113, 193, 217);// color
    } else {
      $color = imagecolorallocate($image, 163, 197, 82);// color
  }
  
  $bg = imagecolorallocate($image, 0, 255, 0); // background color white
  imagefilledrectangle($image,0,0,399,99,$bg);
  imagettftext ($image, 18, 0, 5, 25, $color, $dir.$font, $_SESSION['random_number']);
} else {
    $string = rand_2_words();//generatePassword();
    $_SESSION['random_number'] = rand_2_words();
    $image = imagecreatefrompng("button.png");
    $colour = imagecolorallocate($image, 183, 178, 102);
    imagestring($image, 5, 20,10, $_SESSION['random_number'], $colour );
}
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>