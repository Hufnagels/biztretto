<?
include ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_header_text.php');
include ($_SERVER['DOCUMENT_ROOT'].'/include/header/_header_include_base.php');
//printR($_POST);
function filter($data) {
    $data = trim(htmlentities(strip_tags($data), ENT_QUOTES, 'UTF-8'));
    return $data;
}

if (isset($_POST) && isset($_POST['name']) && $_POST['name'] != '' && isset($_POST['email']) && $_POST['email'] != '') {
    include($_SERVER['DOCUMENT_ROOT'].'/include/class/class.phpmailer.php');
    //$sm = new mysql();
    $name  = html_entity_decode(filter($_POST['name']), ENT_QUOTES, 'UTF-8');//$sm->filter($_POST['name']);
    $email = filter($_POST['email']);//$sm->filter($_POST['email']);
    $phone = filter($_POST['phone']);//$sm->filter($_POST['phone']);
    
    $mail             = new PHPMailer();

    $mail->IsSMTP();
    $mail->SMTPAuth   = true;                  // enable SMTP authentication
    //$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
    $mail->Host       = "localhost";      // sets GMAIL as the SMTP server
    $mail->Port       = 25;                   // set the SMTP port for the GMAIL server
    //$mail->Username   = "pisti@varsoft.hu";  // GMAIL username
    //$mail->Password   = "mancika";            // GMAIL password
    //$mail->AddReplyTo("yourusername@gmail.com","First Last");
    
    $mail->From       = "registration@skillbuilder.co"; // global emilcim pl.: noreply@
    $mail->FromName   = "Skillbuilder";
    //return path
    $mail->Sender     = 'registration@skillbuilder.co';

  //a regisztralonak kuldott level
    $mail->Subject    = 'Skillbuilder: Regisztrációs válasz';

    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->WordWrap   = 50; // set word wrap


    $body             = 'Tisztelt '. $name.'!'; // a hirdetes linket is generalni kell
    $body             .= '</br>';
    $body             .= '</br>'; 
    $body             .= 'Köszönjük, hogy regisztrált!'; 
    $body             .= '</br>'; 
    $body             .= 'Hamarosan felvesszük Önnel a kapcsolatot a demó felületre való belépés részleteivel.'; 
    $body             .= '</br>'; 
    $body             .= '</br>'; 
    $body             .= 'Üdvözlettel:'; 
    $body             .= '</br>'; 
    $body             .= 'Kircsi Levente igazgató'; 
    $body             = preg_replace('#(\\\r|\\\r\\\n|\\\n)#', '<br>', $body);
    $mail->MsgHTML($body);

    //Kinek megy
    $mail->AddAddress($email, $name);  
    //$mail->AddAttachment("images/phpmailer.gif");             // attachment
    $mail->IsHTML(true); // send as HTML
    $mail->IsSendmail();
    $mail->send();
      $response = array('status' => 'sended', 'message' => 'Köszönjük. A regisztráció sikeres volt! Hamarosan megkeressük a részletekkel!');
    
/* -------------------------- */

    $mail             = new PHPMailer();
    $mail->IsSMTP();
    $mail->SMTPAuth   = true;                  // enable SMTP authentication
    //$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
    $mail->Host       = "localhost";      // sets GMAIL as the SMTP server
    $mail->Port       = 25;                   // set the SMTP port for the GMAIL server
    //$mail->Username   = "pisti@varsoft.hu";  // GMAIL username
    //$mail->Password   = "mancika";            // GMAIL password
    //$mail->AddReplyTo("yourusername@gmail.com","First Last");
    
    $mail->From       = "registration@skillbuilder.co"; // global emilcim pl.: noreply@
    $mail->FromName   = "Skillbuilder";
    //return path
    $mail->Sender     = 'registration@skillbuilder.co';

  //levinek kuldott level
    $mail->Subject    = 'Skillbuilder: Regisztrálói adatok: '.$name;

    $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!"; // optional, comment out and test
    $mail->WordWrap   = 50; // set word wrap


    $body             = 'Regisztráló: '. $name;
    $body             .= '</br>';
    $body             .= 'E-mail: '.$email; 
    $body             .= '</br>'; 
    $body             .= 'Telefon: '.$phone; 
    $body             .= '</br>'; 
    $body             .= 'Regisztráció időpontja: '.date("Y-m-d H:i:s"); 
    $body             .= '</br>'; 
    $body             .= '...a rendszer...'; 
    $body             = preg_replace('#(\\\r|\\\r\\\n|\\\n)#', '<br>', $body);
    $mail->MsgHTML($body);

    //Kinek megy
    $mail->AddAddress('kircsi.levente@rektor.hu', 'Kircsi Levente');  
    $mail->AddBCC('pisti@varsoft.hu','Pisti');
    //$mail->AddAddress('pisti@varsoft.hu','Pisti');  
    //$mail->AddAttachment("images/phpmailer.gif");             // attachment
    $mail->IsHTML(true); // send as HTML
    $mail->IsSendmail();
    $mail->send();
} else {

}

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
$resF['result'] = $response;
$json = json_encode($response, true);
$json = str_replace("\u", "\\u", $json);
echo $json;
?>