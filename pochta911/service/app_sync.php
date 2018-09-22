<?php
header('Content-type: application/json');
$data = [ 'name' => 'test', 'age' => '33' ];

$getdata = json_decode(file_get_contents('php://input'));
file_put_contents('log.txt', "\n OK: " . date('d-m-Y H:i:s') ." ".json_encode($getdata) , FILE_APPEND);

echo json_encode( $data );