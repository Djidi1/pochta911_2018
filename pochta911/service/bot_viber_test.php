<?php
error_reporting(E_ALL);
ini_set('display_errors',1);

$token = '47b928585c67d483-163c35967b6305ec-f92d670f8a72e53d';
$api_url = 'https://chatapi.viber.com/pa/send_message';

/**
 * Задаём основные переменные.
 */
$chat_id = 'df+c6B5JkuIet0qr9uTnrg==';
$message = '<b>Заказ №</b> 111669  <b>Дата заказа:</b> 21.04.2018  <b>Откуда:</b> Старо-Петергофский проспект  21 Санкт-Петербург   <i>Вход с Курляндской ул</i>     <b>Адрес доставки:</b> г Санкт-Петербург, Московское шоссе, д 10 , 15   <b>Период забора:</b> 16:10 - 19:10   <b>Период получения:</b> 20:00 - 23:00   <b>Получатель:</b> Катя  [89626918955]   <b>Взять в магазине:</b> 400.00 руб.  Заказ принят, ожидайте курьера.';


/*
$res = callApi($api_url, array(
    'receiver' => $chat_id,
    'min_api_version' => '1',
    'tracking_data' => 'tracking data',
    'type' => 'text',
    'text' => "Проверка - " . $message . "!"
), $token);

*/

$params = /** @lang JSON */
    '{
   "receiver":"'.$chat_id.'",
   "type":"text",
   "text": "'.json_encode(strip_tags($message)).'",
   "keyboard":{
      "Type":"keyboard",
      "Buttons":[
         {
            "Text": "<b>принять заказ</b>",
            "TextSize": "large",
            "TextHAlign": "center",
            "TextVAlign": "middle",
            "ActionType": "reply",
            "ActionBody": "/order_accepted_",
            "BgColor": "#f7bb3f"
         }
      ]
   }
}';

$res = callApi($api_url, $params, $token);


function callApi( $api_url, $params, $token) {
    $ch = curl_init();
    curl_setopt_array( $ch, array(
        CURLOPT_URL             => $api_url,
        CURLOPT_POST            => TRUE,
        CURLOPT_RETURNTRANSFER  => TRUE,
        CURLOPT_FOLLOWLOCATION  => FALSE,
        CURLOPT_HEADER          => FALSE,
        CURLOPT_TIMEOUT         => 10,
        CURLOPT_HTTPHEADER      => array( 'Accept-Language: ru,en-us',"Content-Type:application/json","X-Viber-Auth-Token:$token"),
        CURLOPT_POSTFIELDS      => $params,
        CURLOPT_SSL_VERIFYPEER  => FALSE
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