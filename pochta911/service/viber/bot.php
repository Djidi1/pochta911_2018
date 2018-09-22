<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once("../vendor/autoload.php");

use Viber\Bot;

$apiKey = '47b928585c67d483-163c35967b6305ec-f92d670f8a72e53d';

include_once ('../../config.php');



$getdata = file_get_contents('php://input');
file_put_contents('viber-log.txt', "\n OK: " . date('d-m-Y H:i:s') ."\n ".urldecode($getdata) ."\n\n" , FILE_APPEND);


$bot = new Bot([ 'token' => $apiKey ]);
$bot
    ->onText('|.*|s', function ($event) use ($bot) {
        // .* - match any symbols (see PCRE)
        $data = json_decode(file_get_contents('php://input'));
        if (isset($data->sender)) {
            $from = $data->sender->name;
            $chat = $data->sender->id;
            $update_id = $data->message_token;
            $date = $data->timestamp;
            $text = $data->message->text;
//            file_put_contents('viber-log.txt', "\n OK: " . date('d-m-Y H:i:s') ."\n ".urldecode($text) ."\n\n" , FILE_APPEND);
            $items = array();
            $row['user_name'] = $from;
            $row['data'] = '';
            $row['chat_id'] = $chat;
            $row['update_id'] = $update_id;
            $row['message_id'] = $update_id;
            $row['date'] = date('Y-m-d H:i:s', round($date/1000));
            $row['text'] = iconv('utf-8','windows-1251',$text);
            $row['user_name'] = iconv('utf-8','windows-1251',$row['user_name']);
            $items[] = $row;

            $re = '/order_accepted_([0-9]*)/';
            $str = $text;
            preg_match_all($re, $str, $matches);
            if (isset($matches[1])){
                $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
                mysqli_select_db($connect,DB_DATABASE);
                $sql = "UPDATE orders SET car_accept = '$chat' WHERE id = '".$matches[1][0]."'";
                mysqli_query($connect, $sql);
                $bot->getClient()->sendMessage(
                    (new \Viber\Api\Message\Text())
                        ->setReceiver($event->getSender()->getId())
                        ->setText("Спасибо за подтверждение!".$matches[1][0])
                );
            }

            saveUpdates ($items);
        }




/*
        $bot->getClient()->sendMessage(
            (new \Viber\Api\Message\Text())
                ->setReceiver($event->getSender()->getId())
                ->setText($text)
        );
*/
    })
    ->run();


function saveUpdates($items){
    $connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
    mysqli_select_db($connect,DB_DATABASE);
    // проверка на наличие записи в БД
    $upd_ids = array();
    foreach ($items as $key => $item) {
        $upd_ids[] = $item['update_id'];
    }
    $upd_ids = implode("','",$upd_ids);
    $sql = "SELECT update_id FROM log_viber WHERE update_id IN ('$upd_ids')";
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
        $sql = "INSERT INTO log_viber
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