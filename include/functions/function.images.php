<?
//function.images.php

//  ==================================================================================================
//  HEX to RGB and RGB to HEX
//  ==================================================================================================
  function HexToRGB($hex) {
    $hex = ereg_replace("#", "", $hex);
    $color = array();
    
    if(strlen($hex) == 3) {
      $color['r'] = hexdec(substr($hex, 0, 1) . $r);
      $color['g'] = hexdec(substr($hex, 1, 1) . $g);
      $color['b'] = hexdec(substr($hex, 2, 1) . $b);
    }
    else if(strlen($hex) == 6) {
      $color['r'] = hexdec(substr($hex, 0, 2));
      $color['g'] = hexdec(substr($hex, 2, 2));
      $color['b'] = hexdec(substr($hex, 4, 2));
    }
    
    return $color;
  }
  
  function RGBToHex($r, $g, $b) {
    //String padding bug found and the solution put forth by Pete Williams (http://snipplr.com/users/PeteW)
    $hex = "#";
    $hex.= str_pad(dechex($r), 2, "0", STR_PAD_LEFT);
    $hex.= str_pad(dechex($g), 2, "0", STR_PAD_LEFT);
    $hex.= str_pad(dechex($b), 2, "0", STR_PAD_LEFT);
    
    return $hex;
  }

//  ==================================================================================================
//  a feltoltott kepnek elkesziti a 128 * 96 -os thumbnail-jet
//  form_ingadat_swf-upload.php
//  ==================================================================================================
function createThumbs($imageUrl,$imgName){
  header("Content-type: image/jpg");
  $SourceFile = $imageUrl . $imgName;
  $filename = $imageUrl . 'tn_' . $imgName;
    //letrehozom a thumb kepet
    // Load the image on which watermark is to be applied
    $originalImage = imagecreatefromjpeg($SourceFile);
    // Get original parameters
    list($originalWidth, $originalHeight, $original_type, $original_attr) = getimagesize($SourceFile);
    //eloallitjuk a thumbnailt, aminek a mérete 128*96
    $newWidth = 128;
    $newHeight = 96;
    $blankThumbImage = imagecreatetruecolor($newWidth, $newHeight);
    
    //a feltoltott kep 640*480 vagy 480*640
    //azt feltetelezzuk, hogy mindig ennyi
    //az oldalak aránya 0.75
    //ha 640*480
    if ($originalWidth > $originalHeight) {
      imagecopyresampled($blankThumbImage, $originalImage, 0, 0, 0, 0, $newWidth, $newHeight, $originalWidth, $originalHeight);
    }
    //ha 480*640
    if ($originalWidth < $originalHeight) {
      //levagom a tetejet es az aljat
      $newWidthT = $originalWidth;
      $newHeightT = $originalWidth * 0.75; //360
      $sy = ($originalHeight - $newHeightT) / 2;
      $tempImage = imagecreatetruecolor($newWidthT, $newHeightT);
      imagecopyresized($tempImage, $originalImage, 0, 0, 0, $sy, $newWidthT, $newHeightT , $originalWidth, $newHeightT);
      imagecopyresampled($blankThumbImage, $tempImage, 0, 0, 0, 0, $newWidth, $newHeight, $newWidthT, $newHeightT);
    }
    // elmentem a thumbnailt
    imagejpeg($blankThumbImage, $filename,100); //$SourceFile
    imagedestroy($blankThumbImage);
    
}
//  ==================================================================================================
//  kiszamitja a watermark szoveg befoglalo meretet 
//  form_ingadat_swf-upload.php
//  ==================================================================================================
function calculateTextBox($text,$fontFile,$fontSize,$fontAngle) { 
    /************ 
    simple function that calculates the *exact* bounding box (single pixel precision). 
    The function returns an associative array with these keys: 
    left, top:  coordinates you will pass to imagettftext 
    width, height: dimension of the image you have to create 
    *************/ 
    $rect = imagettfbbox($fontSize,$fontAngle,$fontFile,$text); 
    $minX = min(array($rect[0],$rect[2],$rect[4],$rect[6])); 
    $maxX = max(array($rect[0],$rect[2],$rect[4],$rect[6])); 
    $minY = min(array($rect[1],$rect[3],$rect[5],$rect[7])); 
    $maxY = max(array($rect[1],$rect[3],$rect[5],$rect[7])); 
    
    return array( 
     "left"   => abs($minX) - 1, 
     "top"    => abs($minY) - 1, 
     "width"  => $maxX - $minX, 
     "height" => $maxY - $minY, 
     "box"    => $rect 
    ); 
} 
//  ==================================================================================================
//  watermarkot tesz a feltoltott kepre 
//  form_ingadat_swf-upload.php
//  ==================================================================================================
function watermarkText2Image ($imageUrl,$imgName,$WaterMarkText, $thb = true) {
  header("Content-type: image/jpg");

  //amire kerul a watermark
  if ($thb) {
    $SourceFile = $imageUrl . 'tn_' . $imgName;
  } else {
    $SourceFile = $imageUrl . $imgName;
  }
  list($originalWidth, $originalHeight, $original_type, $original_attr) = getimagesize($SourceFile);
  $sourceImage = imagecreatefromjpeg($SourceFile);
  
  //watermark eloallitasa
  $new_w = (int)($originalWidth);
  $new_h = (int)($originalWidth)*0.15;
  $font = $_SERVER['DOCUMENT_ROOT'].'/include/captcha/fonts/walk_rounded.ttf';
  $fontSize = $new_h/2;
  $angel = 0;
  $the_box = calculateTextBox($WaterMarkText, $font, $fontSize, $angel);
    
  $watermark = imagecreatetruecolor($new_w,$new_h);
  
  
  $white = imagecolorallocate($watermark, 255, 255, 255);
  // Add some shadow to the text
  //$WaterMarkText = 'ww='.$the_box["width"].' nw='.$new_w;
  //imagettftext($watermark, $new_h/2, 0, ($new_w - $the_box["width"])/6, 2*$new_h/3, $white, $font, $WaterMarkText);
  imagettftext($watermark, 
    $fontSize,
    $angel,
    $the_box["left"] + ($new_w / 2) - ($the_box["width"] / 2), 
    $the_box["top"] + ($new_h / 2) - ($the_box["height"] / 2), 
    $white, 
    $font, 
    $WaterMarkText); 
  
  $sx = imagesx($watermark);
  $sy = imagesy($watermark);
  
  imagecopymerge($sourceImage, 
       $watermark,
       0,//imagesx($original_image)/2  , //dest x  - $sx/2
       imagesy($sourceImage)/2- $sy/2, //dest y   
       0,                                   //source origo x
       0,                                   //source origo y
       imagesx($watermark), 
       imagesy($watermark),
       30
  );
  
  // megjelenitem a thumbnailt a watermark szoveggel
  imagejpeg($sourceImage, null, 100); //$SourceFile
  imagedestroy($sourceImage);

}



//  ==================================================================================================
//  display image 
//  display_images.php
//  ==================================================================================================
function displayImage($image) {
  header("Content-type: image/jpg");
  imagejpeg($image);
  imagedestroy($image);
}
//  ==================================================================================================
//  WATERMARK 2
//  display_images.php
//  ==================================================================================================
function watermarkText21Image ($SourceFile,$WaterMarkText) {
  header("Content-type: image/jpg");
  //Using imagecopymerge() to create a translucent watermark
    
  // Load the image on which watermark is to be applied
  $original_image = imagecreatefromjpeg($SourceFile);
  // Get original parameters
  list($original_width, $original_height, $original_type, $original_attr) = getimagesize($original_image); 
  
  // create watermark with orig size
  $watermark = imagecreatetruecolor(200, 60);
  
  $original_image = imagecreatefromjpeg($SourceFile);
  
  // Define text
  $font = $_SERVER['DOCUMENT_ROOT'].'/include/captcha/fonts/moloto.otf';
  $white = imagecolorallocate($watermark, 255, 255, 255);
  // Add some shadow to the text
  imagettftext($watermark, 18, 0, 30, 35, $white, $font, $WaterMarkText);
   
  // Set the margins for the watermark and get the height/width of the watermark image
  $marge_right = 10;
  $marge_bottom = 10;
  $sx = imagesx($watermark);
  $sy = imagesy($watermark);
   
  // Merge the watermark onto our photo with an opacity (transparency) of 50%
  imagecopymerge($original_image, 
       $watermark,
       imagesx($original_image)/2 - $sx/2 , 
       imagesy($original_image) /2 - $sy/2,
       0, 
       0, 
       imagesx($watermark), 
       imagesy($watermark),
       20
  );
   
  // Save the image to file and free memory
  imagejpeg($original_image, null, 100); //$SourceFile
  imagedestroy($original_image);
}

//  ==================================================================================================
//  amennyiben nincs imageRotate fuggveny
//  elforgatja a kepet
//  process-swf-rotate.php
//  ==================================================================================================
function rotateImage0($img, $imgPath, $suffix, $degrees, $quality, $save)
{
    // Open the original image.
    $original = imagecreatefromjpeg("$imgPath/$img") or die("Error Opening original");
    list($width, $height, $type, $attr) = getimagesize("$imgPath/$img");
 
    // Resample the image.
    $tempImg = imagecreatetruecolor($width, $height) or die("Cant create temp image");
    imagecopyresized($tempImg, $original, 0, 0, 0, 0, $width, $height, $width, $height) or die("Cant resize copy");
 
    // Rotate the image.
    $rotate = imagerotate($original, $degrees, 0);
 
    // Save.
    if($save)
    {
        // Create the new file name.
    $newNameE = explode(".", $img);
    $newName = ''. $newNameE[0] .''. $suffix .'.'. $newNameE[1] .'';
 
    // Save the image.
    imagejpeg($rotate, "$imgPath/$newName", $quality) or die("Cant save image");
    }
 
    // Clean up.
    imagedestroy($original);
    imagedestroy($tempImg);
    return true;
}

//  ==================================================================================================
function rotateImage($image) {
  $width = imagesx($image);
  $height = imagesy($image);
  $newImage= imagecreatetruecolor($height, $width);
  imagealphablending($newImage, false);
  imagesavealpha($newImage, true);
  for($w=0; $w<$width; $w++)
    for($h=0; $h<$height; $h++) {
      $ref = imagecolorat($image, $w, $h);
      imagesetpixel($newImage, $h, ($width-1)-$w, $ref);
    }
  return $newImage;
}
//  ==================================================================================================
function rotateImage2 ($image, $angle)
     {
         if ( ($angle < 0) || ($angle > 360) )
         {
             //exit ("Error, angle passed out of range: [0,360]");
             $angle = $angle - 360;
         }
         
        $width    = imagesx ($image);
         $height    = imagesy ($image);
         
        $dstImage = imagecreatetruecolor ($width, $height);
         
        if ( ($angle == 0) || ($angle == 360) )
         {
             // Just copy image to output:
             imagecopy ($dstImage, $image, 0, 0, 0, 0, $width, $height);
         }
         else
         {
             $centerX = floor ($width / 2);
             $centerY = floor ($height / 2);
             
            // Run on all pixels of the destination image and fill them:
             for ($dstImageX = 0; $dstImageX < $width; $dstImageX++)
             {
                 for ($dstImageY = 0; $dstImageY < $height; $dstImageY++)
                 {
                     // Calculate pixel coordinate in coordinate system centered at the image center:
                     $x = $dstImageX - $centerX;
                     $y = $centerY - $dstImageY;
                     
                    if ( ($x == 0) && ($y == 0) )
                     {
                         // We are in the image center, this pixel should be copied as is:
                         $srcImageX = $x;
                         $srcImageY = $y;
                     }
                     else
                     {
                         $r = sqrt ($x * $x + $y * $y); // radius - absolute distance of the current point from image center
 
                        $curAngle = asin ($y / $r); // angle of the current point [rad]
                         
                        if ($x < 0)
                         {
                             $curAngle = pi () - $curAngle;
                         }
                         
                        $newAngle = $curAngle + $angle * pi () / 180; // new angle [rad]
 
                        // Calculate new point coordinates (after rotation) in coordinate system at image center
                         $newXRel = floor ($r * cos ($newAngle));
                         $newYRel = floor ($r * sin ($newAngle));
                         
                        // Convert to image absolute coordinates
                         $srcImageX = $newXRel + $centerX;
                         $srcImageY = $centerY - $newYRel;
                     }
                     
                    $pixelColor = imagecolorat  ($image, $srcImageX, $srcImageY); // get source pixel color
                     
                    imagesetpixel ($dstImage, $dstImageX, $dstImageY, $pixelColor); // write destination pixel
                 }
             }
         }
         
        return $dstImage;
     } 
//  ==================================================================================================

?>
