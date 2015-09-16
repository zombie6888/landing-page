<?php
/**
 * Created by Zhukov Sergey.
 * Email: zom688@gmail.com
 * Website: http://websiteprog.ru
 * Date: 03.09.2015, 20:14
 */
namespace wsp;

class Database
{

    protected static $_user = 'root',
                     $_password = 'merlin253536';

    public static function load($user = '', $password = '', $path='', $type = 'sqlite')
    {
        try {

            $user = $user ? $user : self::$_user;
            $password = $password ? $password : self::$_password;
            $path = $path ? $path :  dirname(__FILE__) . "/paypal.db";

            $db = new \PDO("sqlite:" . $path , $user, $password, array(
                \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION));

            return $db;

        } catch (\PDOException $Exception) {
            echo Functions::exception($Exception);
        }
    }
}

Class HtmlHelper {

    public static function getCountriesList($countries)
    {
        $select = '<label for="country">Country:</label><select name="country" id="country">';
        foreach($countries as $code => $country) {
            $select .= "<option value='$code'>$country</option>";
        }
        $select .= "</select>";
        return $select;
    }
}

class Functions {

    public static function exception($e)
    {
        echo $e->getMessage();
        exit;
    }

    public static function getSenders() {
        return array('zom688@gmail.com', 'adept@globalsect.ru');
    }

    public static function getLink() {
        return 'http://globalsect.ru/lp2';
        //return 'http://localhost/lp';
    }

    public static function getProducts()
    {
        $items = array(
            'bundle1' => array('Crystal1', '3CD compilation + booklet + digital release'),
            'bundle2' => array('Crystal2', '3CD compilation + booklet + T-shirt'),
            'bundle3' => array('Crystal3', '3CD compilation + booklet + Backdrop (1x1,5m size)'),
            'bundle4' => array('Crystal4', '3CD compilation + booklet + T-shirt + Backdrop (1x1,5m size)'),
            'bundle5' => array('Crystal5', '3CD compilation + booklet + T-shirt + Backdrop (1,5x2,25m)'),
            'bundle6' => array('Crystal6', '3CD compilation + booklet + T-shirt + Backdrop (2x3m)')
        );
        return $items;
    }

    public static function sendMail($link, $orderid) {

        $db = Database::load();
        try {
            $data = $db->query("SELECT (SELECT response_data FROM operations "
                ."WHERE operation='GetExpressCheckoutDetails' AND token=token) AS data "
                ."FROM orders WHERE id=". (int) $orderid, \PDO::FETCH_COLUMN, 0)->fetch();
        } catch( \PDOException $Exception ) {
            Functions::exception($Exception);
        }
        $data = json_decode($data);
        $address = $data->EMAIL;

        $subject = "New order";
        $body = "Thank you for your order!<br />";
        $body .= "Your order id is: ".  $orderid . "<br />";
        $body .= "Album download link: <a href='$link'>$link</a><br />";
        $body .= "This link will expire on ". date('F,d Y,', time()+86400 * 7)."<br />";
        $body .= "3 downloads to left";


        $header = "From: <adept@globalsect.ru>" . PHP_EOL;
        $header .= "Content-type: text/html; charset=\"utf-8\"";
        mail($address, $subject, $body, $header);
    }

    public static function getLocations()
    {
        $db = Database::load();
        try {
            $stmt = $db->prepare("SELECT c.name as country, c.code as country_code, s.name as state,s.code as state_code
                FROM country AS c
                LEFT JOIN state AS s ON s.country_id = c.id") or die(print_r($db->errorInfo(), true));
            $stmt->execute();
            $list = $stmt->fetchAll(\PDO::FETCH_OBJ);
        } catch( \PDOException $Exception ) {
            Functions::exception($Exception);
        }
        $countries = array();
        $states = array();
        foreach($list as $item)
        {
            $countries[$item->country_code] = $item->country;
            if (!empty( $item->state )) {
                $states[$item->country_code][] = $item->state . ":" . $item->state_code;
            }
        }
        return array('countries' => $countries, 'states'=> $states);
    }
}