<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
/**
 * Telegram Bot access token и URL.
 */
//$access_token = '168745396:AAGYr2sDUWkIZfx_jxeApguifvyp_NNLumM';
$access_token = '320679954:AAE8u4TLYeJWwfH8hFY4uzKHkits3rlaP_c';
$api = 'https://api.telegram.org/bot' . $access_token;

/**
 * Задаём основные переменные.
 */
//$output = json_decode(file_get_contents('php://input'), TRUE);
//file_put_contents('log.txt', "\n OK: " . date('d-m-Y H:i:s') ." ".json_encode($output) , FILE_APPEND);
//$chat_id = $output['message']['chat']['id'];
$chat_id = '243045100';
$message = 'test';
//$first_name = $output['message']['chat']['first_name'];
//$message = $output['message']['text'];
//$message_id = $output['message']['message_id'];


$res = callApi( 'sendMessage', array(
    'chat_id'               => $chat_id,
    'text'                  => "Проверка - ".$message."!",
    //'reply_to_message_id'   => $message_id,
),$access_token);

print_r($res);

//file_put_contents('log.txt', "\n Message: " . date('d-m-Y H:i:s') ." ".$message , FILE_APPEND);


function callApi( $method, $params, $access_token) {
    $proxy = '118.139.178.67:50098';

    $url = sprintf(
        "https://api.telegram.org/bot%s/%s",
        $access_token,
        $method
    );

    $ch = curl_init();
    curl_setopt_array( $ch, array(
        CURLOPT_URL             => $url,
        CURLOPT_POST            => TRUE,
        CURLOPT_RETURNTRANSFER  => TRUE,
        CURLOPT_PROXY           => "socks5://$proxy",
        CURLOPT_FOLLOWLOCATION  => FALSE,
        CURLOPT_HEADER          => FALSE,
        CURLOPT_TIMEOUT         => 10,
        CURLOPT_HTTPHEADER      => array( 'Accept-Language: ru,en-us'),
        CURLOPT_POSTFIELDS      => $params,

    ));
    if(curl_exec($ch) === false)
    {
        echo 'Ошибка curl: ' . curl_error($ch);
    }
    else
    {
        echo 'Операция завершена без каких-либо ошибок';
    }
    return true;
}