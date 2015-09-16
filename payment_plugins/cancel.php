<?php
/**
* Created by Zhukov Sergey.
* Email: zom688@gmail.com
* Website: http://websiteprog.ru
* Date: 29.08.2015, 2:48
*/
include_once dirname(__FILE__) . '/helper.php';
$db = wsp\Database::load();

$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);
try {
    /*$stmt = $db->prepare("DELETE FROM orders WHERE token=:token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();*/
    $db->exec("UPDATE orders SET last_operation_status=last_operation_status || '(cancelled)' WHERE token=".$db->quote($token));
    /*$stmt = $db->prepare("DELETE FROM operations WHERE token=:token");
    $stmt->bindParam(':token', $token);
    $stmt->execute();*/
}
catch( PDOException $Exception ) {
    wsp\Functions::exception($Exception);
}

header( 'Location: '. wsp\Functions::getLink() . '?canceled=1' );

