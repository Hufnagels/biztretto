<?
//function.text.php
//  ==================================================================================================
//  ==================================================================================================
//  SZOVEG ES KARAKTER KEZELO FUGGVENYEK
//  ==================================================================================================

/*
  Paul's Simple Diff Algorithm v 0.1
  (C) Paul Butler 2007 <http://www.paulbutler.org/>
  May be used and distributed under the zlib/libpng license.
  
  This code is intended for learning purposes; it was written with short
  code taking priority over performance. It could be used in a practical
  application, but there are a few ways it could be optimized.
  
  Given two arrays, the function diff will return an array of the changes.
  I won't describe the format of the array, but it will be obvious
  if you use print_r() on the result of a diff on some test data.
  
  htmlDiff is a wrapper for the diff command, it takes two strings and
  returns the differences in HTML. The tags used are <ins> and <del>,
  which can easily be styled with CSS.  
*/

function diff($old, $new){
  foreach($old as $oindex => $ovalue){
    $nkeys = array_keys($new, $ovalue);
    foreach($nkeys as $nindex){
      $matrix[$oindex][$nindex] = isset($matrix[$oindex - 1][$nindex - 1]) ?
        $matrix[$oindex - 1][$nindex - 1] + 1 : 1;
      if($matrix[$oindex][$nindex] > $maxlen){
        $maxlen = $matrix[$oindex][$nindex];
        $omax = $oindex + 1 - $maxlen;
        $nmax = $nindex + 1 - $maxlen;
      }
    }  
  }
  if($maxlen == 0) return array(array('d'=>$old, 'i'=>$new));
  return array_merge(
    diff(array_slice($old, 0, $omax), array_slice($new, 0, $nmax)),
    array_slice($new, $nmax, $maxlen),
    diff(array_slice($old, $omax + $maxlen), array_slice($new, $nmax + $maxlen)));
}

function htmlDiff($old, $new){
  $diff = diff(explode(' ', $old), explode(' ', $new));
  foreach($diff as $k){
    if(is_array($k))
      $ret .= (!empty($k['d'])?"<del>".implode(' ',$k['d'])."</del> ":'').
        (!empty($k['i'])?"<ins>".implode(' ',$k['i'])."</ins> ":'');
    else $ret .= $k . ' ';
  }
  return $ret;
}

//  ==================================================================================================
//  Replaces special characters 
//  with non-special equivalents
//  ==================================================================================================
function normalize_special_characters( $str )
{
  /*
    # Quotes cleanup
    $str = ereg_replace( chr(ord("`")), "'", $str );        # `
    $str = ereg_replace( chr(ord("´")), "'", $str );        # ´
    $str = ereg_replace( chr(ord("„")), ",", $str );        # „
    $str = ereg_replace( chr(ord("`")), "'", $str );        # `
    $str = ereg_replace( chr(ord("´")), "'", $str );        # ´
    $str = ereg_replace( chr(ord("“")), "\"", $str );        # “
    $str = ereg_replace( chr(ord("”")), "\"", $str );        # ”
    $str = ereg_replace( chr(ord("´")), "'", $str );        # ´
*/
  $unwanted_array = array(    'Š'=>'S', 'š'=>'s', 'Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A', 'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E',
                            'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I', 'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U',
                            'Ú'=>'U', 'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss', 'à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a', 'å'=>'a', 'æ'=>'a', 'ç'=>'c',
                            'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i', 'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o',
                            'ö'=>'o', 'ø'=>'o', 'ù'=>'u', 'ú'=>'u', 'û'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 
                            'ü'=>'u','Ü'=>'u','Ű'=>'u','ű'=>'u','Ő'=>'o','ő'=>'o','ú'=>'u','Ú'=>'u','ú'=>'u','í'=>'i','Í'=>'i');
  $unwanted_array2 = array(
    'Ã¡'=>'á', 'Ã³'=>'ó','Ã¶'=>'ö','Ã¶'=>'ö', 'Ã©' => 'é', 'Ã' => 'í', 'Ãº' => 'ú'
  );
//á-Á-í-Í-é-É-ö-Ö-ü-Ü-ó-Ó-ő-Ő-ú-Ú-ű-Ű
//%C3%A1-%C3%81-%C3%AD-%C3%8D-%C3%A9-%C3%89-%C3%B6-%C3%96-%C3%BC-%C3%9C-%C3%B3-%C3%93-%C5%91-%C5%90-%C3%BA-%C3%9A-
  $unwanted_array3 = array(
  '%2C' => ',', 
  '%C3%A1' => 'á', '%C3%81' => 'Á', 
  '%C3%AD' => 'í', '%C3%8D' => 'Í',
  '%C3%A9' => 'é', '%C3%89' => 'É',
  '%C3%B6' => 'ö', '%C3%96' => 'Ö',
  '%C3%BC' => 'ü', '%C3%9C' => 'Ü',
  '%C3%BC' => 'ó', '%C3%9C' => 'Ó',
  '%C3%B3' => 'ő', '%C3%93' => 'Ő',
  '%C5%91' => 'ú', '%C5%B0' => 'Ú',
  '%C5%B1' => 'ű', '%C5%90' => 'Ű'
  );

$str = strtr( $str, $unwanted_array );
/*
# Bullets, dashes, and trademarks
$str = ereg_replace( chr(149), "&#8226;", $str );    # bullet •
$str = ereg_replace( chr(150), "&ndash;", $str );    # en dash
$str = ereg_replace( chr(151), "&mdash;", $str );    # em dash
$str = ereg_replace( chr(153), "&#8482;", $str );    # trademark
$str = ereg_replace( chr(169), "&copy;", $str );    # copyright mark
$str = ereg_replace( chr(174), "&reg;", $str );        # registration mark
*/
    return $str;
}

//  ==================================================================================================
//  Correct from MS WORD pasted text
//  ==================================================================================================
function strip_word_html($text, $allowed_tags = '<b><i><sup><sub><em><strong><u><br/>')
    {
        mb_regex_encoding('UTF-8');
        //replace MS special characters first
        $search = array('/&lsquo;/u', '/&rsquo;/u', '/&ldquo;/u', '/&rdquo;/u', '/&mdash;/u');
        $replace = array('\'', '\'', '"', '"', '-');
        $text = preg_replace($search, $replace, $text);
        //make sure _all_ html entities are converted to the plain ascii equivalents - it appears
        //in some MS headers, some html entities are encoded and some aren't
        $text = html_entity_decode($text, ENT_QUOTES, 'UTF-8');
        //try to strip out any C style comments first, since these, embedded in html comments, seem to
        //prevent strip_tags from removing html comments (MS Word introduced combination)
        if(mb_stripos($text, '/*') !== FALSE){
            $text = mb_eregi_replace('#/\*.*?\*/#s', '', $text, 'm');
        }
        //introduce a space into any arithmetic expressions that could be caught by strip_tags so that they won't be
        //'<1' becomes '< 1'(note: somewhat application specific)
        $text = preg_replace(array('/<([0-9]+)/'), array('< $1'), $text);
        $text = strip_tags($text, $allowed_tags);
        //eliminate extraneous whitespace from start and end of line, or anywhere there are two or more spaces, convert it to one
        $text = preg_replace(array('/^\s\s+/', '/\s\s+$/', '/\s\s+/u'), array('', '', ' '), $text);
        //strip out inline css and simplify style tags
        $search = array('#<(strong|b)[^>]*>(.*?)</(strong|b)>#isu', '#<(em|i)[^>]*>(.*?)</(em|i)>#isu', '#<u[^>]*>(.*?)</u>#isu');
        $replace = array('<b>$2</b>', '<i>$2</i>', '<u>$1</u>');
        $text = preg_replace($search, $replace, $text);
        //on some of the ?newer MS Word exports, where you get conditionals of the form 'if gte mso 9', etc., it appears
        //that whatever is in one of the html comments prevents strip_tags from eradicating the html comment that contains
        //some MS Style Definitions - this last bit gets rid of any leftover comments */
        $num_matches = preg_match_all("/\<!--/u", $text, $matches);
        if($num_matches){
              $text = preg_replace('/\<!--(.)*--\>/isu', '', $text);
        }
        return $text;
    }

//  ==================================================================================================
//  convert arabian numbers to romanic
//  ==================================================================================================
function romanic_number($integer, $upcase = true)
{
    $table = array('M'=>1000, 'CM'=>900, 'D'=>500, 'CD'=>400, 'C'=>100, 'XC'=>90, 'L'=>50, 'XL'=>40, 'X'=>10, 'IX'=>9, 'V'=>5, 'IV'=>4, 'I'=>1);
    $return = '';
    while($integer > 0)
    {
        foreach($table as $rom=>$arb)
        {
            if($integer >= $arb)
            {
                $integer -= $arb;
                $return .= $rom;
                break;
            }
        }
    }

    return $return;
}

//  ==================================================================================================
//  convert \r\n to <br/> 
//  ==================================================================================================
function nl2br_indent($string, $indent = 0){
  //remove carriage returns
  $string = str_replace("\\r", "", $string);

  //convert indent to whitespaces if it is a integer.
  if (is_int($indent)) {
  //set indent to length of the string
    $indent = str_repeat(' ', (int)$indent);
  }

  //replace newlines with “<br />\n$indent”
  $string = str_replace("\\n", "\n".$indent, $string);
  //add the indent to the first line too
  $string = $indent.$string;

  return $string;
}

//  ==================================================================================================
//  megadott szöveget linkké alakít 
//  ==================================================================================================
function tolink($text) {
  $text = html_entity_decode($text);
  $text = " " . $text;
  $text = preg_replace('`(((f|ht){1}tp://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)`', '<a href="\\1">\\1</a>', $text);
  $text = preg_replace('`(((f|ht){1}tps://)[-a-zA-Z0-9@:%_\+.~#?&//=]+)`', '<a href="\\1">\\1</a>', $text);
  $text = preg_replace('`([[:space:]()[{}])(www.[-a-zA-Z0-9@:%_\+.~#?&//=]+)`', '\\1<a href="http://\\2">\\2</a>', $text);
  $text = preg_replace('`([_\.0-9a-z-]+@([0-9a-z][0-9a-z-]+\.)+[a-z]{2,3})`', '<a href="mailto:\\1">\\1</a>', $text);
  return $text;
}

?>
