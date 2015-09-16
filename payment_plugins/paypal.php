<?php
/**
 * Created by Zhukov Sergey.
 * Email: zom688@gmail.com
 * Website: http://websiteprog.ru
 * Date: 28.08.2015, 17:43
 */

class Paypal {
    /**
     * Последние сообщения об ошибках
     * @var array
     */
    protected $_errors = array();

    protected $_credentials = array(
        'USER' => 'zom688-facilitator_api1.gmail.com',
        'PWD' => 'KUYWDX34M4EUP9PP',
        'SIGNATURE' => 'AbYAuOdNax4i5uP2ju0koltyd2SSAeHsb4Xoi.MhxYirbt3eYcHVuNn1',
    );

    /**
     * Указываем, куда будет отправляться запрос
     * Реальные условия - https://api-3t.paypal.com/nvp
     * Песочница - https://api-3t.sandbox.paypal.com/nvp
     * @var string
     */
    protected $_endPoint = 'https://api-3t.sandbox.paypal.com/nvp';

    /**
     * Версия API
     * @var string
     */
    protected $_version = '124.0';


    /**
     * Конструктор
     *
     * @param string $user
     * @param string $password
     * @param string $signature - цифровая подпись
     * @param string $mode - Режим: sandbox - песочница, live - реальные условия
     */
    function __construct($mode='sandbox', $user='', $password='', $signature='')
    {
        $this->setCredentials($user, $password, $signature);
        $this->setMode($mode);
    }

    /**
     * Сформировываем запрос
     *
     * @param string $method Данные о вызываемом методе перевода
     * @param array $params Дополнительные параметры
     * @return array / boolean Response array / boolean false on failure
     */
    public function request($method,$params = array()) {
        $this -> _errors = array();
        if( empty($method) ) { // Проверяем, указан ли способ платежа
            $this -> _errors = array('Не указан метод перевода средств');
            return false;
        }

        // Параметры нашего запроса
        $requestParams = array(
                'METHOD' => $method,
                'VERSION' => $this -> _version
            ) + $this -> _credentials;

        // Сформировываем данные для NVP
        $request = http_build_query($requestParams + $params);

        // Настраиваем cURL
        $curlOptions = array (
            CURLOPT_URL => $this -> _endPoint,
            CURLOPT_VERBOSE => 1,
            CURLOPT_SSL_VERIFYPEER => true,
            CURLOPT_SSL_VERIFYHOST => 2,
            CURLOPT_CAINFO => dirname(__FILE__) . '/cacert.pem', // Файл сертификата
            CURLOPT_RETURNTRANSFER => 1,
            CURLOPT_POST => 1,
            CURLOPT_POSTFIELDS => $request
        );

        $ch = curl_init();
        curl_setopt_array($ch,$curlOptions);

        // Отправляем наш запрос, $response будет содержать ответ от API
        $response = curl_exec($ch);

        // Проверяем, нету ли ошибок в инициализации cURL
        if (curl_errno($ch)) {
            $this -> _errors = curl_error($ch);
            curl_close($ch);
            return false;
        } else  {
            curl_close($ch);
            $responseArray = array();
            parse_str($response,$responseArray); // Разбиваем данные, полученные от NVP в массив
            return $responseArray;
        }
    }

    public function setCredentials($user, $password, $signature)
    {
        if (!empty($user) && !empty($password) && !empty($signature))
        {
            $this->_credentials = array(
                'USER' => $user,
                'PWD' => $password,
                'SIGNATURE' => $signature,
            );
        }
        return $this;
    }

    public function setMode($mode)
    {
        if ($mode == 'live')  {
            $this->_endPoint = 'https://api-3t.paypal.com/nvp';
        }
    }

    public function getUrl()
    {
        if($this->_endPoint == 'https://api-3t.sandbox.paypal.com/nvp') {
            return 'https://www.sandbox.paypal.com';
        } else {
            return 'https://www.paypal.com';
        }
    }

}
 