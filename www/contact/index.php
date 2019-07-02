<?
    if((!isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&  $_SERVER['HTTP_X_REQUESTED_WITH'] !== 'XMLHttpRequest')){
        require_once ( $_SERVER['DOCUMENT_ROOT'].'/index.php' );
        //return "papo2";
        exit();
    }
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/config.php');
    //require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_auth.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_header_text.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/header/_header_include_base.php');
    require_once ($_SERVER['DOCUMENT_ROOT'] . '/../include/class/class.skillmailer.php');


//exit;
    /**/
    $formArray = createArrayFromPostNV();

    switch (getRequest('action'))
    {
        case 'contact':
            SkillMailer::sendContactEmail(
                array(
                    'senderName' => MySQL::filter($formArray['name']),
                    'senderEmail' => MySQL::filter($formArray['email'])
                ),
                array(
                    'email' => "kbvconsulting@gmail.com",
                    'name' => "SKILLBI.COM"
                ),
                MySQL::filter($formArray['subject']),
                MySQL::filter($formArray['message'])
            );

            $returnArray = array('type'=>'success', 'message'=>'Mail sended successfully');
            break;

        case 'order':
            //printR($formArray);
            SkillMailer::sendOrderEmail(
                array(
                    'senderName' => MySQL::filter($formArray['contact']),
                    'senderEmail' => MySQL::filter($formArray['email']),
                    'phone' => MySQL::filter($formArray['phone']),
                    'company' => MySQL::filter($formArray['company']),
                    'package' => MySQL::filter($formArray['type'])
                ),
                array(
                    'email' => "kbvconsulting@gmail.com",
                    'name' => "SKILLBI.COM"
                )
            );
            $returnArray = array('type'=>'success', 'message'=>'Package order mail sended successfully. We\'ll contact You soon!');
            break;
    }

    printSortResult($returnArray);
?>