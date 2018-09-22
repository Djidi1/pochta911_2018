<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once('../config.php');

$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
mysqli_select_db($connect, DB_DATABASE);
mysqli_query ($connect,"SET NAMES UTF8");


$search_type = $_POST['type'];
$search_city = $_POST['city'];
$search_str = $_POST['street'];

$items = array();
//$search_str = iconv('utf-8','windows-1251',$search_str);
if ($search_type == 'street' and strlen($search_str) > 3) {

    $items = array();
    $i = 0;
    $sql = "SELECT 780 region, k.NAME, k.SOCR
            FROM kladr_kladr_78 k
            WHERE k.NAME = '$search_str' OR k.NAME LIKE '$search_str%'
            ORDER BY NAME;";
    $result = mysqli_query($connect, $sql);
    while ($row = mysqli_fetch_assoc($result) and $i < 100) {
        $res = ''.$row['SOCR'].'. '.$row['NAME'];
        $items[] = $res;
        $i++;
    }
    $sql = "SELECT 780 region, ks.NAME, ks.SOCR, k.NAME c_name, k.SOCR c_sokr FROM kladr_street_78 ks 
            LEFT JOIN kladr_kladr_78 k ON LEFT(ks.CODE,11) = LEFT(k.CODE, 11) AND RIGHT(k.CODE,2)='00'
            WHERE k.NAME = 'Санкт-Петербург' AND ks.NAME LIKE '%$search_str%'
            UNION ALL 
            SELECT 472 region, ks.NAME, ks.SOCR, k.NAME c_name, k.SOCR c_sokr FROM kladr_street_78 ks 
            LEFT JOIN kladr_kladr_78 k ON LEFT(ks.CODE,11) = LEFT(k.CODE, 11) AND RIGHT(k.CODE,2)='00'
            WHERE LEFT(k.CODE, 2) = '78' AND k.NAME <> 'Санкт-Петербург' AND ks.NAME LIKE '%$search_str%'
            UNION ALL 
            SELECT 471 region, ks.NAME, ks.SOCR, k.NAME c_name, k.SOCR c_sokr FROM kladr_street_78 ks 
            LEFT JOIN kladr_kladr_78 k ON LEFT(ks.CODE,11) = LEFT(k.CODE, 11) AND RIGHT(k.CODE,2)='00'
            WHERE LEFT(k.CODE, 2) = '47' AND k.NAME <> 'Санкт-Петербург' AND ks.NAME LIKE '%$search_str%'
			ORDER BY region DESC, c_name, NAME;";
    $result = mysqli_query($connect, $sql);
    while ($row = mysqli_fetch_assoc($result) and $i < 100) {
        $res = ''.$row['c_sokr'].'. '.$row['c_name'].', '.$row['SOCR'].'. '.$row['NAME'];
        $items[] = $res;
        $i++;
    }
//    echo print_r($items);
    echo json_encode($items);
    exit();
}

exit ();