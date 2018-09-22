<?php
require_once("../vendor/autoload.php");

use Viber\Client;

$apiKey = '47b928585c67d483-163c35967b6305ec-f92d670f8a72e53d'; // from "Edit Details" page
$webhookUrl = 'https://pochta911.ru/service/viber/bot.php'; // for exmaple https://pochta911.ru/service/viber/bot.php

try {
    $client = new Client([ 'token' => $apiKey ]);
    $result = $client->setWebhook($webhookUrl);
    echo "Success!\n";
} catch (Exception $e) {
    echo "Error: ". $e->getMessage() ."\n";
}