<?php
    require_once($_SERVER['DOCUMENT_ROOT'].'/../include/class/class.phpmailer.php');

    abstract class SkillMailer
    {
        public static function addToMailLog($fromEmail,$fromName,$senderEmail,$toEmail,$toName,$subject,$htmlMessage)
        {
            $data="-------------\r\n";
            $data.="TIME: ".date('Y-m-d H:i:s')."\r\n";
            $data.="FROM EMAIL: ".$fromEmail."\r\n";
            $data.="FROM NAME: ".$fromName."\r\n";
            $data.="SENDER EMAIL: ".$senderEmail."\r\n";
            $data.="SUBJECT: ".$subject."\r\n";
            $data.="TO EMAIL: ".$toEmail."\r\n";
            $data.="TO NAME: ".$toName."\r\n";
            $data.="SUBJECT: ".$subject."\r\n";
            $data.="MESSAGE: ".$htmlMessage."\r\n\r\n";

            @file_put_contents($_SERVER['DOCUMENT_ROOT'].'/../log/phplog/mail_'.date('Ymd').'.txt',$data,FILE_APPEND);
        }

        private static function sendEmailMessage($fromEmail,$fromName,$senderEmail,$toEmail,$toName,$subject,$htmlMessage)
        {
            $mail             = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPAuth   = true;                  // enable SMTP authentication
            $mail->Host       = "localhost";      // sets GMAIL as the SMTP server
            $mail->Port       = 25;                   // set the SMTP port for the GMAIL server
            $mail->ContentType='text/html';

            //sender
            $mail->From       = $fromEmail;
            $mail->FromName   = $fromName;

            //return path
            $mail->Sender     = $senderEmail;

            //level targya
            $mail->Subject    = $subject;

            // optional, comment out and test
            $mail->AltBody    = "To view the message, please use an HTML compatible email viewer!\r\n\r\n".strip_tags(self::br2nl($htmlMessage));
            $mail->WordWrap   = 50; // set word wrap

//            @todo KELL EZ?
//            $body             = preg_replace('#(\\\r|\\\r\\\n|\\\n)#', '<br>', $body);
//            $mail->AddBCC('pp@varsoft.hu','Vark-skill: sendRegisteredEmail function');
//            $mail->AddBCC('kircsi.levente@rektor.hu','skill: sendRegisteredEmail function');

            $mail->MsgHTML($htmlMessage);

            $mail->AddAddress($toEmail,$toName);

            $mail->IsHTML(true); // send as HTML

            if (DEVMODE===false and SKILLMAILER_SENDMAIL===true)
            {
                self::addToMailLog($fromEmail,$fromName,$senderEmail,$toEmail,$toName,$subject,$htmlMessage);

                $mail->IsSendmail();
                $mail->send();
            }
            else
            {
                self::addToMailLog($fromEmail,$fromName,$senderEmail,$toEmail,$toName,$subject,$htmlMessage);
            }
        }

        /**
         * @param $str
         * @return mixed
         */
        private static function br2nl($str)
        {
            return preg_replace('=<br */?>=i', "\n", $str);
        }

        /**
         * @param array $row (email,name,password,encrypted)
         * @param $registrar
         * @param $encrypted
         */
        public static function sendRegisteredEmail(array $row, $registrar, $encrypted)
        {
            $body             = '<p>Tisztelt '.$row['name'].'!</p>';
            $body             .= '<p></p>';
            $body             .= '<p></p>';
            $body             .=  '<p>'.$registrar. ' regisztrálta Önt a BIZTRETTO rendszerében. Az alábbiakban az első belépéshez szükséges adatokat olvashatja. Kérjük, ne felejtse el megváltoztatni jelszavát!</p>';
            $body             .= '<p></p>';
            $body             .= '<p>Login data:</p>';
            $body             .= '<p></p>';
            $body             .= '<p>Login name: '. $row['email'].'</p>';
            $body             .= '<p><p>';
            $body             .= '<p>Password: '. $row['password'].'</p>';
            $body             .= '<p><p>';
            $body             .= '<p>Please, click this following activation link: <a href="'.SITE_URL.'auth.php?key='.$encrypted.'">'.SITE_URL.'auth.php?key='.$encrypted.'</a> to activate Your account and use the system.</p>';
            $body             .= '<p></p>';
            $body             .= '<p>Regards:<p>';
            $body             .= '</br>';
            $body             .= $registrar;

            self::sendEmailMessage("registration@biztretto.com","BIZTRETTO.COM",'registration@biztretto.com',$row['email'],$row['name'],'BIZTRETTO.COM regisztráció',$body);
        }

        /**
         * @param array $users (array(array(name,email),...)
         * @param $sender
         * @param string $subj
         * @param $message
         */
        public static function sendFastmessage(array $users, $sender, $subj = 'BIZTRETTO::sikeres regisztráció', $message)
        {
            foreach($users as $row)
            {
                $body             = '<p>Tisztelt '.$row['name'].'!</p>';
                $body             .= '<p></p>';
                $body             .= '<p></p>';
                $body             .=  $message;
                $body             .= '<p><p>';
                $body             .= '<p>www: http://biztretto.com/ </p>';
                $body             .= '<p></p>';
                $body             .= '<p>Üdvözlettel:<p>';
                $body             .= '</br>';
                $body             .= $sender;
                $body             = preg_replace('#(\\\r|\\\r\\\n|\\\n)#', '<br>', $body);

                self::sendEmailMessage("noreply@biztretto.com","BIZTRETTO.COM - ".$sender,'noreply@biztretto.com',$row['email'],$row['name'],$subj,$body);
            }
        }


        /**
         * @param $to
         * @param $message
         * @param string $subj
         */
        public static function sendTestRegistration($to, $message, $subj = 'BIZTRETTO::ajánlatkérés')
        {
            $senderName = 'BIZTRETTO.COM';
            $senderEmail = 'noreply@biztretto.com';

            $body             = '<p>Tisztelt '.$to['name'].'!</p>';
            $body             .= '<p></p>';
            $body             .= '<p></p>';
            $body             .=  $message;
            $body             .= '<p><p>';
            $body             .= '<p>www: http://biztretto.com/ </p>';
            $body             .= '<p></p>';
            $body             .= '<p>Üdvözlettel:<p>';
            $body             .= '</br>';
            $body             .= $senderName;
            $body             = preg_replace('#(\\\r|\\\r\\\n|\\\n)#', '<br>', $body);

            self::sendEmailMessage($senderEmail,$senderName,$senderEmail,$to['email'],$to['name'],$subj,$body);
        }


        /**
         * @param array $sender (senderName,senderEmail)
         * @param array $to
         * @param string $subj
         * @param $message
         */
        public static function sendFeedbackEmail(array $sender, array $to, $subj = 'BIZTRETTO::Feedback', $message)
        {
            $body             = '<p>Tisztelt BIZTRETTO Team!</p>';
            $body             .= '<p></p>';
            $body             .= '<p></p>';
            $body             .=  $message;
            $body             .= '<p><p>';
            $body             .= '<p>Üdvözlettel:<p>';
            $body             .= '</br>';
            $body             .= '<p>'.$sender['senderName'].'<p>';
            $body             .= '<p>'.$sender['senderEmail'].'<p>';

            self::sendEmailMessage("noreply@biztretto.com","BIZTRETTO.COM - ".$sender['senderName'],'noreply@biztretto.com',$to['email'],$to['name'],$subj,$body);
        }

        /**
         * @param array $sender (senderName,senderEmail)
         * @param array $to
         * @param string $subj
         * @param $message
         */
        public static function sendContactEmail(array $sender, array $to, $subj = 'BIZTRETTO::Contact', $message)
        {
            $body             = '<p>Tisztelt BIZTRETTO Team!</p>';
            $body             .= '<p></p>';
            $body             .= '<p></p>';
            $body             .=  $message;
            $body             .= '<p><p>';
            $body             .= '<p>Üdvözlettel:<p>';
            $body             .= '</br>';
            $body             .= '<p>'.$sender['senderName'].'<p>';
            $body             .= '<p>'.$sender['senderEmail'].'<p>';

            self::sendEmailMessage(
                "noreply@biztretto.com",
                "BIZTRETTO.COM/ContactUs - ".$sender['senderName'],
                'noreply@biztretto.com',
                $to['email'],
                $to['name'],
                $subj,
                $body);
        }

        public static function sendOrderEmail(array $sender, array $to, $subj = 'BIZTRETTO::Order')
        {
            $body             = '<p>Tisztelt BIZTRETTO Team!</p>';
            $body             .= '<p></p>';
            $body             .= '<p></p>';
            //$body             .=  $message;
            $body             .= '<p><p>';
            $body             .= '<p>Adataink:<p>';
            $body             .= '<p>Package type: '.$sender['package'].'<p>';
            $body             .= '<p>Company name: '.$sender['company'].'<p>';
            $body             .= '<p>Phone: '.$sender['phone'].'<p>';
            $body             .= '<p><p>';
            $body             .= '<p>Üdvözlettel:<p>';
            $body             .= '</br>';
            $body             .= '<p>'.$sender['senderName'].'<p>';
            $body             .= '<p>'.$sender['senderEmail'].'<p>';

            self::sendEmailMessage(
                "noreply@biztretto.com",
                "BIZTRETTO.COM/ContactUs - ".$sender['senderName'],
                'noreply@biztretto.com',
                $to['email'],
                $to['name'],
                $subj,
                $body);
        }
        
    }
?>