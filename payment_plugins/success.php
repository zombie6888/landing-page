<?php
/**
 * Created by Zhukov Sergey.
 * Email: zom688@gmail.com
 * Website: http://websiteprog.ru
 * Date: 29.08.2015, 2:48
 */
error_reporting(E_ALL);
include_once dirname(__FILE__). '/paypal.php';
include_once dirname(__FILE__) . '/helper.php';
use wsp\Functions as myfuncs;

$senders = myfuncs::getSenders();

$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);

if( $token )
{
    // Токен присутствует
    // Получаем детали оплаты, включая информацию о покупателе.
    // Эти данные могут пригодиться в будущем для создания, к примеру, базы постоянных покупателей
    $paypal = new Paypal();
    $db = wsp\Database::load();

    $token = $_GET['token'];
    $checkoutDetails = $paypal->request('GetExpressCheckoutDetails', array('TOKEN' => $token));
    $status = !empty($checkoutDetails['ACK']) ? $checkoutDetails['ACK'] : '';

    //обновляем данные
    try {
        $stmt = $db->prepare("INSERT INTO operations (operation, token, response_data)"
            . " VALUES ('GetExpressCheckoutDetails', :token, :response)");
        $stmt->bindParam(':token', $token);
        $resp = json_encode($checkoutDetails);
        $stmt->bindParam(':response', $resp);
        $stmt->execute();
        $operation_id = $db->lastInsertId();

        if ($operation_id) {
            $db->exec("UPDATE orders SET"
                . " last_operation='GetExpressCheckoutDetails',"
                . " last_operation_id=" . $db->quote($operation_id) .","
                . " last_operation_status=" . $db->quote($status)
                . " WHERE token=" .  $db->quote($token)
            );
        }
    } catch (PDOException $Exception) {
        myfuncs::exception($Exception);
    }

    if($status = 'success' || $status = 'SuccessWithWarning')
    {
       // Завершаем транзакцию
        $requestParams = array(
            'PAYMENTREQUEST_0_PAYMENTACTION' => 'Sale',
            'PAYERID' => $_GET['PayerID'],
            'TOKEN' => $token,
            'PAYMENTREQUEST_0_AMT' => $checkoutDetails['PAYMENTREQUEST_0_AMT'],
            'PAYMENTREQUEST_0_CURRENCYCODE' => $checkoutDetails['PAYMENTREQUEST_0_CURRENCYCODE']
        );

        $response = $paypal->request('DoExpressCheckoutPayment', $requestParams);

        $status = is_array($response) ? $response['ACK'] : '';

        if ($status == 'Success' || $status = 'SuccessWithWarning')
        {
            // Оплата успешно проведена
            // Здесь мы сохраняем ID транзакции, может пригодиться во внутреннем учете
            $transactionId = $response['PAYMENTINFO_0_TRANSACTIONID'];

            //обновляем данные
            try {
                $stmt = $db->prepare("INSERT INTO operations (operation, token, response_data)"
                    . " VALUES ('DoExpressCheckoutPayment', :token, :response)");
                $stmt->bindParam(':token', $token);
                $resp = json_encode($response);
                $stmt->bindParam(':response', $resp);
                $stmt->execute();
                $operation_id = $db->lastInsertId();

                if ($operation_id) {
                    $db->exec("UPDATE orders SET"
                        . " last_operation='DoExpressCheckoutPayment',"
                        . " last_operation_id=" . $db->quote($operation_id) .","
                        . " transaction_id=" . $db->quote($transactionId) .","
                        . " last_operation_status=" . $db->quote($status)
                        . " WHERE token=" .  $db->quote($token)
                    );
                }

                $time = time();
                $db->exec("INSERT INTO downloads (transaction_id,time, downloads) VALUES ('$transactionId','$time','3')");

            } catch (PDOException $Exception) {
                myfuncs::exception($Exception);
            }

            try {
                $id = $db->query("SELECT id FROM orders WHERE token=". $db->quote($response['TOKEN']),PDO::FETCH_COLUMN,0)->fetch();
            } catch (PDOException $Exception) {
                myfuncs::exception($Exception);
            }

            if($status != 'Success') {
                sendnotify('DoExpressCheckoutPayment', $response, $senders, $operation_id, $id);
            }
            header( 'Location: '. myfuncs::getLink() . '?orderid='.$id.'&transactionid='.$transactionId);
        }

    } else {
        if($status != 'Success') {
            try {
                $id = $db->query("SELECT id FROM orders WHERE token=". $db->quote($response['TOKEN']),PDO::FETCH_COLUMN,0)->fetch();
            } catch (PDOException $Exception) {
                myfuncs::exception($Exception);
            }
            sendnotify('GetExpressCheckoutDetails', $checkoutDetails, $senders, $operation_id, $id);
        }
    }
}

function sendnotify($operation, $response, $senders, $oid, $id)
{
    $subject = "Ошибка при оформлении заказа";
    $body = "Ошибка операции $operation: ". PHP_EOL;
    $body .= "Код ошибки: ".  $response['L_ERRORCODE0'] . PHP_EOL;
    $body .= "Краткое описание: ".  $response['L_SHORTMESSAGE0'] . PHP_EOL;
    $body .= "Полное описание: ".  $response['L_LONGMESSAGE0'] . PHP_EOL;
    $body .= "Идентификатор заказа: ".  $id . PHP_EOL;
    $body .= "Идентификатор операции: ".  $oid . PHP_EOL;
    if(!empty($response['PAYMENTINFO_0_TRANSACTIONID']))
    {
        $body .= "Идентификатор транзакции: ".  $response['PAYMENTINFO_0_TRANSACTIONID'] . PHP_EOL;
    }
    $header = "From: <adept@globalsect.ru>" . PHP_EOL;
    $header .= "Content-type: text/plain; charset=\"utf-8\"";
    mail(implode(',' , $senders), $subject, $body, $header);
}