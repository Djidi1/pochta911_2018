<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
// https://api.telegram.org/bot320679954:AAE8u4TLYeJWwfH8hFY4uzKHkits3rlaP_c/setWebhook?url=https://fd.pochta911.ru/service/telegram_sync.php
include_once ('../config.php');

$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
mysqli_select_db($connect,DB_DATABASE);

/**
 * Задаём основные переменные.
 */
//file_put_contents('log.txt', "\n ENTER: " . date('d-m-Y H:i:s')  , FILE_APPEND);
$data = json_decode(file_get_contents('php://input'));
//file_put_contents('log.txt', "\n OK: " . date('d-m-Y H:i:s') ." ".json_encode($data) , FILE_APPEND);
//file_put_contents('log.txt', "\n EXIT: " . date('d-m-Y H:i:s') ." ".$data->update_id , FILE_APPEND);


$items = array();

if (isset($data->callback_query)){
    $from = $data->callback_query->from;
    $chat = $data->callback_query->message->chat;
    $date = $data->callback_query->message->date;
    $text = $data->callback_query->message->text;

    $row['user_name'] = $from->first_name . " " .
        (isset($from->last_name) ? $from->last_name : '') .
        (isset($from->username) ? " [" . $from->username . "]" : '');
    $row['data'] = $data->callback_query->data;
    $row['chat_id'] = $chat->id;
    $row['update_id'] = isset($data->update_id)?$data->update_id:0;
    $row['message_id'] = $data->callback_query->message->message_id;
    $row['date'] = date('Y-m-d H:i:s', $date);
    $row['text'] = iconv('utf-8','windows-1251',$text);
    $row['user_name'] = iconv('utf-8','windows-1251',$row['user_name']);
    $items[] = $row;

    $re = '/order_accepted_([0-9]*)/';
    $str = $row['data'];
    preg_match_all($re, $str, $matches);
    if (isset($matches[1])){
        $sql = "UPDATE orders SET car_accept = '$chat->id' WHERE id = '".$matches[1][0]."'";
        //file_put_contents('log.txt', "\n SQL: " . date('d-m-Y H:i:s') ." ".$sql." " .json_encode($matches), FILE_APPEND);
        mysqli_query($connect, $sql);
    }

    callApi( 'sendMessage', array(
        'chat_id'               => $row['chat_id'],
        'text'                  => "Спасибо за подтверждение!",
        'reply_to_message_id'   => $row['message_id'],
    ));
}

if (isset($data->message)) {
    $from = $data->message->from;
    $chat = $data->message->chat;
    $date = $data->message->date;
    $text = $data->message->text;

    $row['user_name'] = $from->first_name . " " .
        (isset($from->last_name) ? $from->last_name : '') .
        (isset($from->username) ? " [" . $from->username . "]" : '');
    $row['data'] = '';
    $row['chat_id'] = $chat->id;
    $row['update_id'] = isset($data->update_id)?$data->update_id:0;
    $row['message_id'] = $data->message->message_id;
    $row['date'] = date('Y-m-d H:i:s', $date);
    $row['text'] = iconv('utf-8','windows-1251',$text);
    $row['user_name'] = iconv('utf-8','windows-1251',$row['user_name']);
    $items[] = $row;
}

saveTelegramUpdates ($items,$connect);


function saveTelegramUpdates($items,$connect){
    // проверка на наличие записи в БД
    $upd_ids = array();
    foreach ($items as $key => $item) {
        $upd_ids[] = $item['update_id'];
    }
    $upd_ids = implode("','",$upd_ids);
    $sql = "SELECT update_id FROM log_telegram WHERE update_id IN ('$upd_ids')";
    $result = mysqli_query($connect, $sql);
    $upd_ids = array ();
    while ( $row = mysqli_fetch_assoc($result) ) {
        $upd_ids [] = $row['update_id'];
    }

    // Подготовка данных для записи
    $sql_values = '';
    foreach ($items as $key => $item) {
        if (!in_array($item['update_id'],$upd_ids)) {
            $sql_values .= ($key > 0) ? ',' : '';
            $sql_values .= "
            (
                 '" . $item['user_name'] . "'
                 ,'" . $item['chat_id'] . "'
                 ,'" . $item['update_id'] . "'
                 ,'" . $item['message_id'] . "'
                 ,'" . $item['text'] . "'
                 ,'" . $item['data'] . "'
                 ,'" . $item['date'] . "'
                 ,NOW()
                )";
        }
    }
    if ($sql_values != '') {
        $sql = "INSERT INTO log_telegram
                (
                  sender
                 ,chat_id
                 ,update_id
                 ,message_id
                 ,text
                 ,data
                 ,date
                 ,dk
                )
                VALUES $sql_values
                ;";
        mysqli_query($connect, $sql);
    }
}
/*
callApi( 'sendMessage', array(
    'chat_id'               => $chat_id,
    'text'                  => "Пришло сообщение: ".$message."",
    'reply_to_message_id'   => $message_id,
));
*/
function callApi( $method, $params) {
    $url = sprintf(
        "https://api.telegram.org/bot%s/%s",
        TLG_TOKEN,
        $method
    );

    $ch = curl_init();
    curl_setopt_array( $ch, array(
        CURLOPT_URL             => $url,
        CURLOPT_POST            => TRUE,
        CURLOPT_RETURNTRANSFER  => TRUE,
        CURLOPT_FOLLOWLOCATION  => FALSE,
        CURLOPT_HEADER          => FALSE,
        CURLOPT_TIMEOUT         => 10,
        CURLOPT_HTTPHEADER      => array( 'Accept-Language: ru,en-us'),
        CURLOPT_POSTFIELDS      => $params,

    ));

    $response = curl_exec($ch);
    return json_decode($response);
}