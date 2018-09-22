<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
include_once('../config.php');

$connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
mysqli_select_db($connect, DB_DATABASE);
mysqli_query ($connect,"SET NAMES UTF8");

$search_type = $_POST['type'];

$items = array();
if ($search_type == 'street') {
    $search_city = $_POST['city'];
    $search_str = $_POST['street'];
    $items = array();
    $i = 0;
    // 0. Ищем города
    $sql = "SELECT 47 region, ct.SHORTNAME, ct.OFFNAME city 
            FROM  addrob47 ct
            WHERE (ct.OFFNAME = '$search_str' OR ct.OFFNAME LIKE '$search_str%') 
                AND  ct.LIVESTATUS = 1 AND ct.AOLEVEL IN (4,6)
          UNION ALL 
            SELECT 78 region, ct.SHORTNAME, ct.OFFNAME city 
            FROM  addrob78 ct
            WHERE (ct.OFFNAME = '$search_str' OR ct.OFFNAME LIKE '$search_str%') 
                AND  ct.LIVESTATUS = 1 AND ct.AOLEVEL IN (4,6)
            ";
    $result = mysqli_query($connect, $sql);
    $res = array();
    while ($row = mysqli_fetch_assoc($result) and $i < 100) {
        $res['id'] = '';
        $res['region'] = $row['region'];
        $res['name'] = ''.$row['SHORTNAME'].'. '.$row['city'];
//        $res['name'] = $row['city'];
        $items[] = $res;
        $i++;
    }
    // 1. Ищем улицу в Санкт-Петербурге
    // 2. Ищем улицу в регионе Петербурга
    // 3. Ищем улицу в Ленинградской области
    // -- 7 - улицы // 4,6 - города и нас.пункты
    $sql = "
            SELECT 78 region, st.SHORTNAME, st.OFFNAME street, ct.SHORTNAME shtn, ct.OFFNAME city, st.AOGUID 
            FROM addrob78 st
              LEFT JOIN addrob78 ct ON ct.AOGUID = st.PARENTGUID AND ct.LIVESTATUS = 1 AND ct.AOLEVEL IN (4,6)
            WHERE st.LIVESTATUS = 1 AND st.OFFNAME LIKE '%$search_str%' AND st.AOLEVEL IN (7) 
                AND ct.OFFNAME IS null
          UNION ALL 
            SELECT 78 region, st.SHORTNAME, st.OFFNAME street, ct.SHORTNAME shtn, ct.OFFNAME city, st.AOGUID 
            FROM addrob78 st
              LEFT JOIN addrob78 ct ON ct.AOGUID = st.PARENTGUID AND ct.LIVESTATUS = 1 AND ct.AOLEVEL IN (4,6)
            WHERE st.LIVESTATUS = 1 AND st.OFFNAME LIKE '%$search_str%' AND st.AOLEVEL IN (7)
                AND ct.OFFNAME IS NOT NULL
          UNION ALL 
            SELECT 47 region, st.SHORTNAME, st.OFFNAME street, ct.SHORTNAME shtn, ct.OFFNAME city, st.AOGUID 
            FROM addrob47 st
              LEFT JOIN addrob47 ct ON ct.AOGUID = st.PARENTGUID AND ct.LIVESTATUS = 1 AND ct.AOLEVEL IN (4,6)
            WHERE st.LIVESTATUS = 1 AND st.OFFNAME LIKE '%$search_str%' AND st.AOLEVEL IN (7) 
";
    $result = mysqli_query($connect, $sql);
    $res = array();
    while ($row = mysqli_fetch_assoc($result) and $i < 100) {
        $res['id'] = $row['AOGUID'];
        $res['region'] = $row['region'];
        $res['name'] = ($row['city'] != ''?''.$row['shtn'].'. '.$row['city'].', ':'').$row['SHORTNAME'].'. '.$row['street'];
//        $res['name'] = ($row['city'] != ''?$row['city'].', ':'').$row['SHORTNAME'].'. '.$row['street'];
        $items[] = $res;
        $i++;
    }
//    echo print_r($items);
    echo json_encode($items);
    exit();
}
if ($search_type == 'house') {
    $house = $_POST['house'];
    $AOGUID = $_POST['AOGUID'];

    preg_match('/(\d+)/', $house, $matches, PREG_OFFSET_CAPTURE, 0);
    if (isset($matches[1][0])){
        $house = $matches[1][0];
    }
    $items = array();
    $i = 0;
    // 1. Ищем дома на улице в городе
    $sql = "SELECT DISTINCT HOUSENUM, BUILDNUM
              FROM house78 h 
              WHERE h.AOGUID = '$AOGUID' AND HOUSENUM LIKE '$house%' AND h.ENDDATE > NOW()
              ORDER BY HOUSENUM, BUILDNUM";
    $result = mysqli_query($connect, $sql);
    $res = array();
    while ($row = mysqli_fetch_assoc($result) and $i < 100) {
        $res['name'] = ''.$row['HOUSENUM'].($row['BUILDNUM'] != ''?'к'.$row['BUILDNUM']:'');
        $items[] = $res;
        $i++;
    }
    // 2. Ищем дома на улице в загородом
    $sql = "SELECT DISTINCT HOUSENUM, BUILDNUM
              FROM house47 h 
              WHERE h.AOGUID = '$AOGUID' AND HOUSENUM LIKE '$house%' AND h.ENDDATE > NOW()
              ORDER BY HOUSENUM, BUILDNUM";
    $result = mysqli_query($connect, $sql);
    $res = array();
    while ($row = mysqli_fetch_assoc($result) and $i < 100) {
        $res['name'] = ''.$row['HOUSENUM'].($row['BUILDNUM'] != ''?'к'.$row['BUILDNUM']:'');
        $res['id'] = ''.$row['HOUSENUM'].($row['BUILDNUM'] != ''?'к'.$row['BUILDNUM']:'');
        $res['text'] = ''.$row['HOUSENUM'].($row['BUILDNUM'] != ''?'к'.$row['BUILDNUM']:'');
        $items[] = $res;
        $i++;
    }
    echo '{"items": '.json_encode($items).'}';
    exit();
}

exit ();