<?php

if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
    define ('DB_HOST','109.120.131.30');
    define ('DB_DATABASE','db1056464_im');
    define ('DB_USER','u1056464_im');
    define ('DB_PASS','KtKTLK25m');
} else {
    define ('DB_HOST','192.168.137.105');
    define ('DB_DATABASE','db1056464_im');
    define ('DB_USER','u1056464_im');
    define ('DB_PASS','KtKTLK25m');
}

define ('DB_USE','mySQL');

define ('DB_TAB_PREF','');
define ('TAB_PREF','');

define ('TLG_TOKEN','320679954:AAE8u4TLYeJWwfH8hFY4uzKHkits3rlaP_c');

define ('br','<br />');
define ('rn',"\r\n");
define ('bn',"<br />\r\n");

define('RIVC_ROOT', '');
define('SITE_DIR_ROOT', '');
define('SITE_ROOT', 'http://' . $_SERVER["SERVER_NAME"] . '/');
define('DIR_UPLOAD', 'uploads/');

define('CORE_ROOT', __DIR__ . '/');

date_default_timezone_set('Europe/Moscow');