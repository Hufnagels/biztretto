<?
session_start();
ob_start();//Hook output buffer
  require_once('class.base.php');
ob_end_clean();//Clear output buffer
unset($_SESSION['random_number']);
$string = '';
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
if (function_exists('imagettftext')) {
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
	
	$bg = imagecolorallocate($image, 255, 255, 255); // background color white
	imagefilledrectangle($image,0,0,399,99,$bg);
  imagettftext ($image, 18, 0, 5, 25, $color, $dir.$font, $_SESSION['random_number']);
} else {
  $string = rand_2_words();//generatePassword();
  $_SESSION['random_number'] = rand_2_words();
	$image = imagecreatefrompng("button.png");
	$colour = imagecolorallocate($image, 183, 178, 102);
  imagestring($image, 5, 20,10, $_SESSION['random_number'], $colour );
}
//echo imagettftext ($image, 18, 0, 5, 25, $color, $dir.$font, $_SESSION['random_number']);
//echo 'Dirname ( __FILE__ ): '.dirname( __FILE__ );
//exit;
header("Content-type: image/png");
imagepng($image);
imagedestroy($image);
?>