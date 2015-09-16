<?php
/**
 * Created by Zhukov Sergey.
 * Email: zom688@gmail.com
 * Website: http://websiteprog.ru
 * Date: 10.09.2015, 3:22
 */
error_reporting(E_ALL);
ini_set("mail.log", "mail.log");
ini_set("mail.add_x_header", TRUE);
$address = 'zom688@gmail.com';
$link = "www.websiteprog.ru";
$subject = "New order";
$body = "Thank you for your order!<br />";
//$body .= "Album download link: <a href='$link''>$link</a>";
$header = "From: <adept@globalsect.ru>" . PHP_EOL;
$header .= "Reply-To: adept@globalsect.ru" . PHP_EOL;
//$header .= "X-Mailer: PHP/" . PHP_VERSION . PHP_EOL;
$header .= "Content-type: text/html; charset=\"utf-8\"";
echo mail($address, $subject, $body, $header);

