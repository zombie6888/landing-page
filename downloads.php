<?php
/**
 * Created by Zhukov Sergey.
 * Email: zom688@gmail.com
 * Website: http://websiteprog.ru
 * Date: 09.09.2015, 23:50
 */
include_once dirname(__FILE__) . '/payment_plugins/helper.php';

$expiration = 86400 * 7; //7 дней
$file =  wsp\Functions::getLink() . '/hidden/album.zip';

$timeout = time() - $expiration;

$transactionid = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING) or die('Invalid parameter!');
$db = wsp\Database::load();
try {
    $stmt = $db->prepare("SELECT id, time, downloads FROM downloads WHERE transaction_id = :transactionid");
    $stmt->bindParam(':transactionid', $transactionid);
    $stmt->execute();
    $row = $stmt->fetch();
} catch( PDOException $Exception ) {
    wsp\Functions::exception($Exception);
}

if ($row['time'] < $timeout)
{
    echo '<h2>Sorry, your download period was expired!</h2>';
    exit();
}

if ($row['downloads'] > 0)
{
    $downloads = $row['downloads'] - 1;
    $db->exec("UPDATE downloads SET downloads=" . $downloads ." WHERE id=" . $row['id']);

    header("Content-Disposition: attachment; filename='" . basename($file) . "';");
    echo file_get_contents($file);
}
else
{
    echo '<h2>Sorry, you had only 3 times to download!</h2>';
    exit();
}





 