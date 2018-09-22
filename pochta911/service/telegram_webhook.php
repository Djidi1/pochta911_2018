<?php
error_reporting(E_ALL);
ini_set('display_errors',1);
// https://api.telegram.org/bot320679954:AAE8u4TLYeJWwfH8hFY4uzKHkits3rlaP_c/setWebhook?url=https://fd.pochta911.ru/service/telegram_sync.php
include_once ('../config.php');

	function regHandler($cert, $murl)
    {
        $url = "https://api.telegram.org/bot" . TLG_TOKEN . "/setWebhook";
        $ch = curl_init();
        $optArray = array(
            CURLOPT_URL => $url,
            CURLOPT_POST => true,
            CURLOPT_SAFE_UPLOAD => false,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POSTFIELDS => array(
                'url' => $murl,
                'certificate' => new CurlFile(realpath($cert))
            )
        );
        curl_setopt_array($ch, $optArray);

        $result = curl_exec($ch);
        echo "<pre>";
        print_r(realpath($cert));
        print_r($result);
        echo "</pre>";
        curl_close($ch);
    }


	$path = '../../public.pem';
	$handlerurl = 'https://fd.pochta911.ru/service/telegram_sync.php'; // ИЗМЕНИТЕ ССЫЛКУ

	regHandler($path, $handlerurl);