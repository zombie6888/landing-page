<?php
/**
 * Created by Zhukov Sergey.
 * Email: zom688@gmail.com
 * Website: http://websiteprog.ru
 * Date: 28.08.2015, 18:20
 */
include_once dirname(__FILE__). '/payment_plugins/paypal.php';
include_once dirname(__FILE__) . '/payment_plugins/helper.php';
use wsp\Functions as myfuncs;

$senders = myfuncs::getSenders();

$buyername = filter_input(INPUT_POST, 'buyername', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$country = filter_input(INPUT_POST, 'country', FILTER_VALIDATE_REGEXP, array(
    "options"=>array("regexp"=>"/[A-Za-z]{2}/")
));
$state = filter_input(INPUT_POST, 'state', FILTER_SANITIZE_STRING);
$countrytext = filter_input(INPUT_POST, 'country-text', FILTER_SANITIZE_STRING);
$statetext = filter_input(INPUT_POST, 'state-text', FILTER_SANITIZE_STRING);
$postcode = filter_input(INPUT_POST, 'postcode', FILTER_SANITIZE_STRING);
$address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_STRING);
$comment = filter_input(INPUT_POST, 'comment', FILTER_SANITIZE_STRING);
$itemname = filter_input(INPUT_POST, 'itemname', FILTER_SANITIZE_STRING);
$desc = filter_input(INPUT_POST, 'itemdesc', FILTER_SANITIZE_STRING);
$city = filter_input(INPUT_POST, 'city', FILTER_SANITIZE_STRING);
$price = (int) filter_input(INPUT_POST, 'price', FILTER_SANITIZE_STRING);


if( !empty($_SERVER['HTTP_X_FORWARDED_FOR']) ){
    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
}elseif( !empty($_SERVER['HTTP_CLIENT_IP']) ){
    $ip = $_SERVER['HTTP_CLIENT_IP'];
}elseif(!empty($_SERVER['REMOTE_ADDR']) ){
    $ip = $_SERVER['REMOTE_ADDR'];}

$ip = ip2long($ip);
$currency = 'RUB';//'EUR';//'RUB';//
$time = time();

$requestParams = array(
    'RETURNURL' => myfuncs::getLink() . '/payment_plugins/success.php?plugin=paypal',
    'CANCELURL' => myfuncs::getLink() . '/payment_plugins/cancel.php?plugin=paypal'
);

$orderParams = array(
    'PAYMENTREQUEST_0_AMT' => $price,
    'PAYMENTREQUEST_0_SHIPPINGAMT' => '0',
    'PAYMENTREQUEST_0_CURRENCYCODE' => $currency,
    'PAYMENTREQUEST_0_ITEMAMT' => $price,
    'EMAIL'=> $email,
    'PAYMENTREQUEST_0_NOTETEXT'=> $comment,
);

$item = array(
    'L_PAYMENTREQUEST_0_NAME0' => $itemname,
    'L_PAYMENTREQUEST_0_DESC0' => $desc,
    'L_PAYMENTREQUEST_0_AMT0' => $price,
    'L_PAYMENTREQUEST_0_QTY0' => '1'
);

$shipment = array(
    'PAYMENTREQUEST_0_SHIPTOZIP'=> $postcode,
    'PAYMENTREQUEST_0_SHIPTOCOUNTRYCODE'=> $country,
    'PAYMENTREQUEST_0_SHIPTOCITY'=> $city,
    'PAYMENTREQUEST_0_SHIPTOSTREET'=> $address,
    'PAYMENTREQUEST_0_SHIPTOSTATE'=> $state,
    'PAYMENTREQUEST_0_SHIPTONAME'=> $buyername
);

$plugin = new Paypal();
$response = $plugin -> request('SetExpressCheckout', $requestParams + $orderParams + $item + $shipment);
$token = $response['TOKEN'];
$status = $response['ACK'];

$db = wsp\Database::load();

//сохараняем запрос в бд
try {
    $stmt = $db->prepare("INSERT INTO operations (operation, token, response_data)"
        ." VALUES ('SetExpressCheckout', :token, :response)");
    $stmt->bindParam(':token', $token);
    $resp = json_encode($response);
    $stmt->bindParam(':response', $resp);
    $stmt->execute();

    $operation_id = $db->lastInsertId();

    $stmt = $db->prepare("INSERT INTO orders (token, buyer, address, state, city, country, postcode, item, description,"
            ."comment, currency, price, userip, email, last_operation_status, last_operation, last_operation_id, time)"
            ." VALUES (:token, :buyer, :address, :state, :city, :country, :postcode, :item, :description,"
            .":comment, :currency, :price, :userip, :email, :last_operation_status, 'SetExpressCheckout', :last_operation_id, :time)");

    $country = $countrytext ? $countrytext :  $country;
    $state = $statetext ? $statetext :  $state;

    $stmt->bindParam(':token', $token);
    $stmt->bindParam(':buyer', $buyername);
    $stmt->bindParam(':address', $address);
    $stmt->bindParam(':state', $state);
    $stmt->bindParam(':city', $city);
    $stmt->bindParam(':country', $country);
    $stmt->bindParam(':postcode', $postcode);
    $stmt->bindParam(':item', $itemname);
    $stmt->bindParam(':description', $desc);
    $stmt->bindParam(':comment', $comment);
    $stmt->bindParam(':currency', $currency);
    $stmt->bindParam(':price', $price);
    $stmt->bindParam(':userip', $ip);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':last_operation_status', $status);
    $stmt->bindParam(':last_operation_id', $operation_id);
    $stmt->bindParam(':time', $time);

    $stmt->execute();
}
catch( PDOException $Exception ) {
    myfuncs::exception($Exception);
}

if(is_array($response) && $status == 'Success') { // Запрос был успешно принят
    $url = $plugin->getUrl();
    header( 'Location: '. $url . '/webscr?cmd=_express-checkout&token=' . urlencode($token) );
} else {
    //отправляем уведомление в случае провала операции
    $subject = "Ошибка при оформлении заказа";
    $body .= "Ошибка операции SetExpressCheckout: ". PHP_EOL;
    $body .= "Код ошибки: ".  $response['L_ERRORCODE0'] . PHP_EOL;
    $body .= "Краткое описание: ".  $response['L_SHORTMESSAGE0'] . PHP_EOL;
    $body .= "Полное описание: ".  $response['L_LONGMESSAGE0'] . PHP_EOL;
    $body .= "Идентификатор заказа: ".  $db->lastInsertId() . PHP_EOL;
    $body .= "Идентификатор операции: ".  $operation_id . PHP_EOL;
    $header = "From: <adept@globalsect.ru>" . PHP_EOL;
    $header .= "Content-type: text/plain; charset=\"utf-8\"";
    mail(implode(',' , $senders), $subject, $body, $header);
}