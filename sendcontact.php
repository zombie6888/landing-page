<?php
/**
 * Created by Zhukov Sergey.
 * Email: zom688@gmail.com
 * Website: http://websiteprog.ru
 * Date: 15.09.2015, 4:11
 */
$email = filter_input(INPUT_GET, 'email', FILTER_VALIDATE_EMAIL);
$text = filter_input(INPUT_GET, 'contact-text',  FILTER_SANITIZE_STRING);

if($email && $text)
{
    include_once dirname(__FILE__) . '/payment_plugins/helper.php';
    $senders = wsp\Functions::getSenders();

    $subject = "Новое сообщение от посетителя:";
    $body = "<b>Email посетителя: </b> $email<br />";
    $body .= "<b>Текст сообщения: </b><br />".  $text;

    $header = "From: <adept@globalsect.ru>" . PHP_EOL;
    $header .= "Content-type: text/html; charset=\"utf-8\"";
    if(mail(implode(',', $senders), $subject, $body, $header)) {
        echo json_encode(array('status'=>'ok'));
    }
}
