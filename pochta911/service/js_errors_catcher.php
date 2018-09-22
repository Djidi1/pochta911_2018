<?php
//header('Content-type: application/json');
$getdata = file_get_contents('php://input');
file_put_contents('js-log.txt', "\n OK: " . date('d-m-Y H:i:s') ."\n ".urldecode($getdata) ."\n\n" , FILE_APPEND);
