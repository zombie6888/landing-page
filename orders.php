<?php
/**
 * Created by Zhukov Sergey.
 * Email: zom688@gmail.com
 * Website: http://websiteprog.ru
 * Date: 04.09.2015, 18:34
 */
include_once dirname(__FILE__) . '/payment_plugins/helper.php';
$db = wsp\Database::load();

try {
    $query = $db->query("SELECT o.id, o.address, o.state, o.city, o.buyer, o.comment, o.item, o.userip, o.postcode, o.time, "
          ."o.country, o.last_operation, o.last_operation_status, o.transaction_id, o.email, os.operation, os.response_data "
          ."FROM orders AS o "
          ."LEFT JOIN operations AS os ON os.token = o.token OR os.id = o.last_operation_id");
    $result = $query->fetchAll(PDO::FETCH_OBJ);
} catch(PDOException $Exception) {
    wsp\Functions::exception($Exception);
}
$orders = array();
$operations = array();
foreach ($result as $item)
{

    if ( empty($orders[$item->id]) )
    {
        $order = array(
            'id' => $item->id,
            //'name' => $item->buyer,
            //'address' => $item->country . ', ' . $item->state . ', ' . $item->city . ', ' . $item->address. ' '. $item->postcode,
            'ip' => long2ip($item->userip),
            //'email' => $item->email,
            'item' => $item->item,
            'comment' => $item->comment,
            'last_operation' => $item->last_operation,
            'status' => $item->last_operation_status,
            'transaction_id' => $item->transaction_id,
            'time' => date('Y-m-d H:i:s', $item->time)
        );
        $orders[$item->id] = $order;
    }

    if(!empty($item->response_data)) {
        $operations[$item->id][$item->operation] = json_decode($item->response_data);
    }
}
$orders = array_values($orders);
echo '<h2>Заказы</h2>';
$html = '<table id="orders"><tr>';
$html .= '<th>id</th><!--<th>Имя</th><<th>Email</th><th>Адрес</th>--><th>IP Адрес</th><th>Товар</th><th>Коментарий</th>';
$html .= '<th>Последняя операция</th><th>Статус</th><th>Идентификатор транзакции</th><th>Дата заказа</th><th>Данные по операциям</th></tr>';

foreach($orders as $order) {

    $html .= '<tr>';

    if($order['status'] == 'Failure' or $order['status'] == 'FailureWithWarning')  {
        $status = 'class="error"';
    } elseif($order['status'] == 'Success') {
        $status = 'class="success"';
    } else {
        $status = 'class="warning"';
    }


    $html .= '<td width="1%">' . $order['id'] .'</td>';
    //$html .= '<td>' . $order['name'] .'</td>';
    //$html .= '<td>' . $order['email'] .'</td>';
    //$html .= '<td>' . $order['address'] .'</td>';
    $html .= '<td>' . $order['ip'] .'</td>';
    $html .= '<td>' . $order['item'] .'</td>';
    $html .= '<td>' . $order['comment'] .'</td>';
    $html .= '<td>' . $order['last_operation'] .'</td>';
    $html .= '<td ' .$status .' >' . $order['status'] .'</td>';
    $html .= '<td>' . $order['transaction_id'] .'</td>';
    $html .= '<td>' . $order['time'] .'</td>';
    $html .= '<td class="show-details"><a class="details" href="#" id="order-' . $order['id'] .'">Показать</a></td>';
    $html .= '</tr>';
}
$html .= '</table>';
echo $html;

/*var_dump($orders, $operations);*/

$operations = json_encode($operations);
?>
<style>
    table {
        width: 100%; /* Ширина таблицы */
        border: 4px double black; /* Рамка вокруг таблицы */
        border-collapse: collapse; /* Отображать только одинарные линии */
    }
    th {
        text-align: left; /* Выравнивание по левому краю */
        background: #ccc; /* Цвет фона ячеек */
        padding: 5px; /* Поля вокруг содержимого ячеек */
        border: 1px solid black; /* Граница вокруг ячеек */
    }
    td {
        padding: 5px; /* Поля вокруг содержимого ячеек */
        border: 1px solid black; /* Граница вокруг ячеек */
    }
    .success , .warning, .error { font-weight: bold; }
    .success {
        color: green;
    }
    .warning {
        color: #cfcf00;
    }
    .error {
        color: red;
    }
    ul {
        display: inline-block;
        list-style: none;
        vertical-align: top;
    }
    a#back {
        font-size: 30px;
        color: green;
        font-weight: bold;
        display: block;
        cursor: pointer;
    }
</style>
<script>
    var operations = <?php echo $operations ?>;
    var details = document.getElementsByClassName('details');
    for(i=0;i< details.length; i++)
    {
        details[i].onclick = function() {
            var listid = this.id.replace('order-', '');
            if(operations[listid] !== 'undefined') {

                var orderinfo = document.createElement('div');
                orderinfo.id = 'info';

                var back = document.createElement('a');
                back.id = 'back';
                back.innerHTML = 'назад';

                orderinfo.appendChild(back)
                for (var operation in operations[listid])
                {
                    operationData = operations[listid][operation];
                    var header = document.createElement('h2');
                    var ul = document.createElement('ul');
                    header.innerHTML = operation;
                    ul.appendChild(header)
                    for (var key in operationData) {
                        var li = document.createElement('li');
                        li.innerHTML = '<b>' +key+'</b>: ' + operationData[key];
                        ul.appendChild(li);
                    }
                    orderinfo.appendChild(ul);
                }
                document.body.appendChild(orderinfo);
                document.getElementById('orders').style.display = "none";

                back.onclick = function() {
                    document.body.removeChild(orderinfo);
                    document.getElementById('orders').style.display = "block";
                }

            }

        }

    }
</script>
