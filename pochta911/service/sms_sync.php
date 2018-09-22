<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
include_once ('../config.php');

$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
mysqli_select_db($connect,DB_DATABASE);



$items = array();
if (isset($_POST["data"])) {
    foreach ($_POST["data"] as $entry) {
        $lines = explode("\n", $entry);
        if (trim($lines[0]) == "sms_status") {
            $row['sms_id'] = $lines[1];
            $row['sms_status'] = $lines[2];
            // "Изменение статуса. Сообщение: $sms_id. Новый статус: $sms_status";
            // Здесь вы можете уже выполнять любые действия над этими данными.
            $items[] = $row;
        }
    }
    echo "100"; /* Важно наличие этого блока, иначе наша система посчитает, что в вашем обработчике сбой */
}else{
    echo "Error!";
}

saveSmsUpdates ($items, $connect);


function saveSmsUpdates($items,$connect){
    // Подготовка данных для записи
    $sql_values = '';
    foreach ($items as $key => $item) {
            $sql_values .= ($key > 0) ? ',' : '';
            $sql_values .= "('" . $item['sms_id'] . "','" . $item['sms_status'] . "',NOW())";
    }
    if ($sql_values != '') {
        $sql = "INSERT INTO log_sms (sms_id,sms_status,dk) VALUES $sql_values ;";
        mysqli_query($connect, $sql);
    }
}
