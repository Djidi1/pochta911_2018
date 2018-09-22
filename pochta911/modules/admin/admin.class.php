<?php

//include CORE_ROOT . 'classes/tree.class.php';

class adminModel extends module_model {
	public function __construct($modName) {
		parent::__construct ( $modName );
	
		// stop($this->System);
	}

	/*
	 * $row['user_name'] = $from->first_name . " " .
                        (isset($from->last_name) ? $from->last_name : '') .
                        (isset($from->username) ? " [" . $from->username . "]" : '');
                    $row['chat_id'] = $chat->id;
                    $row['date'] = date('d.m.Y H:i', $date);
                    $row['text'] = $text;
	 */
	public function saveTelegramUpdates($items){

	    // проверка на наличие записи в БД
        $upd_ids = array();
        foreach ($items as $key => $item) {
            $upd_ids[] = $item['update_id'];
        }
        $upd_ids = implode("','",$upd_ids);
	    $sql = "SELECT update_id FROM log_telegram WHERE update_id IN ('$upd_ids')";
        $this->query ( $sql );
        $upd_ids = array ();
        while ( ($row = $this->fetchRowA ()) !== false ) {
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
                 ,date
                 ,dk
                )
                VALUES $sql_values
                ;";
            $this->query($sql);
        }

    }

    public function exportToExcel($titles, $items){
        require_once CORE_ROOT . 'classes/PHPExcel.php';
        $objPHPExcel = new PHPExcel();

        $style = array(
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                'wrap' => true
            ),
            'font'  => array(
                'bold'  => true,
                'color' => array('rgb' => '333333'),
                'size'  => 10,
                'name'  => 'Verdana'
            )
        );
        $objPHPExcel->setActiveSheetIndex(0);
// Записываем заголовки со второй строки
        $colCount = 'A';
        foreach ($titles as $col_value) {
            $cell = $colCount.'1';
            $objPHPExcel->getActiveSheet()->SetCellValue($cell, $col_value);
            $objPHPExcel->getActiveSheet()->getStyle($colCount."1")->applyFromArray($style);
            $objPHPExcel->getActiveSheet()->getColumnDimension($colCount)->setAutoSize(true);
            $colCount++;
        }
//        $objPHPExcel->getActiveSheet()->getStyle("A1:".$colCount."1")->applyFromArray($style);
// Записываем данные со второй строки
        $rowCount = 2;
        foreach($items as $item){
            $colCount = 'A';
            foreach ($item as $col_value) {
                $cell = $colCount.$rowCount;
                $objPHPExcel->getActiveSheet()->SetCellValue($cell, $col_value);
//                $objPHPExcel->getActiveSheet()->getColumnDimension($colCount)->setAutoSize(true);
                $colCount++;
            }
            $rowCount++;
        }
        $objPHPExcel->getActiveSheet()->setCellValue("D$rowCount", "=SUM(D2:D".($rowCount-1).")");
        $objPHPExcel->getActiveSheet()->setCellValue("E$rowCount", "=SUM(E2:E".($rowCount-1).")");
        $objPHPExcel->getActiveSheet()->setCellValue("F$rowCount", "=SUM(F2:F".($rowCount-1).")");
        $objPHPExcel->getActiveSheet()->setCellValue("G$rowCount", "=SUM(G2:G".($rowCount-1).")");
        $objPHPExcel->getActiveSheet()->setCellValue("H$rowCount", "=SUM(H2:H".($rowCount-1).")");
        $objPHPExcel->getActiveSheet()->setCellValue("I$rowCount", "=SUM(I2:I".($rowCount-1).")");
        $objPHPExcel->getActiveSheet()->getStyle("D".$rowCount)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle("E".$rowCount)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle("F".$rowCount)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle("G".$rowCount)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle("H".$rowCount)->applyFromArray($style);
        $objPHPExcel->getActiveSheet()->getStyle("I".$rowCount)->applyFromArray($style);
        $colCount--;
        $rowCount--;
        $objPHPExcel->getActiveSheet()->setAutoFilter('A1:'.$colCount.$rowCount);
        $objPHPExcel->getActiveSheet()->freezePane('A2');

        $file_name = 'report_orders'.date('_Y_m_d').'.xlsx';
// Instantiate a Writer to create an OfficeOpenXML Excel .xlsx file
//        $objWriter = new PHPExcel_Writer_Excel2007($objPHPExcel);
// Write the Excel file to filename some_excel_file.xlsx in the current directory
//        $objWriter->save('orders'.date('_Y_m_d').'.xlsx');
        // Redirect output to a client’s web browser (Excel2007)
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="'.$file_name.'"');
        header('Cache-Control: max-age=0');
// If you're serving to IE 9, then the following may be needed
        header('Cache-Control: max-age=1');

// If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0

        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $objWriter->setPreCalculateFormulas(true);
        $objWriter->save('php://output');
        exit;
    }
    function dmy_to_mydate($date) {
        return date ( 'Y-m-d', strtotime (  $date ) );
    }
    public function getMainReport($from, $to){
	    $sql = "
SELECT 
  a.id,
  a.title,
  -- a.name,
  CASE
    WHEN a.category > 1 THEN 'A'
    WHEN a.category IS NULL AND a.count_orders > 0 THEN 'A'
    WHEN a.category > 0.33 THEN 'B'
    WHEN a.category > 0 THEN 'C'
    ELSE 'D'
  END AS cat_value,
--  a.category,
  a.count_orders,
  a.dost,
  (a.dost - a.cour) AS comp_money,
  a.inkass,
  (a.inkass * a.inkass_proc / 100) AS inkas_money,
  (a.dost - a.cour) + (a.inkass * a.inkass_proc / 100) AS sum_money,
--  a.inkass_proc,
  a.max_date,
  a.date_reg
  FROM (
    SELECT 
      u.id,
      u.title, 
      u.name,
      u.date_reg,
      (COUNT(o.id) / datediff(NOW(), u.date_reg)) AS category,
      COUNT(o.id) AS count_orders,
      MAX(o.date) AS max_date,
      SUM(r.cost_route) AS dost,
      SUM(r.cost_car) AS cour,
      SUM(r.cost_tovar) AS inkass,
      u.inkass_proc  
    FROM users u
      LEFT JOIN groups_user gu ON u.id = gu.user_id
      LEFT JOIN orders o ON o.id_user = u.id
      LEFT JOIN orders_routes r ON r.id_order = o.id
    WHERE gu.group_id = 2 AND u.isban = 0  AND (r.id_status = 4 OR r.id_status IS NULL)
    AND o.date BETWEEN '".$this->dmy_to_mydate($from)."' AND '".$this->dmy_to_mydate($to)." 23:59:59'
      GROUP BY u.title, u.name
  ) a
                ";
        $this->query ( $sql );
        $items = array ();
        while ( ($row = $this->fetchRowA ()) !== false ) {
            $items [] = $row;
        }
        return $items;
    }

    public function loadTelegramUpdates(){
	    $sql = "SELECT id, sender, chat_id, update_id, message_id, text, data, date, dk 
                FROM log_telegram 
                ORDER BY dk DESC 
                LIMIT 0,200
                ";
        $this->query ( $sql );
        $items = array ();
        while ( ($row = $this->fetchRowA ()) !== false ) {
            $items [] = $row;
        }
        return $items;
    }

    public function loadViberUpdates(){
	    $sql = "SELECT id, sender, chat_id, update_id, message_id, text, data, date, dk 
                FROM log_viber
                ORDER BY dk DESC 
                LIMIT 0,200
                ";
        $this->query ( $sql );
        $items = array ();
        while ( ($row = $this->fetchRowA ()) !== false ) {
            $items [] = $row;
        }
        return $items;
    }

	public function userInsert($Params) {
		$passi = md5 ( $Params ['pass'] );
		$sql = 'INSERT INTO `users` (`name`,email,login,phone,phone_mess,title,isBan,inkass_proc,fixprice_inside,maxprice_inside,pay_type,pass,send_sms,date_reg,`desc`,work_times,viber_id)
				VALUES (
				    \'%1$s\',
				    \'%2$s\',
				    \'%3$s\',
				    \'%4$s\',
				    \'%5$s\',
				    \'%6$s\',
				    \'%7$u\',
				    \'%8$u\',
				    \'%9$s\',
				    \'%10$s\',
				    \'%11$u\',
				    \'' . $passi . '\',
				    \'%12$u\',
				    NOW(),
				    \'%13$s\',
				    \'%14$s\',
				    \'%15$s\'
				    )';

		$test = $this->query ( $sql, $Params ['username'], $Params ['email'], $Params ['login'], $Params ['phone'],
			$Params ['phone_mess'], $Params ['title'], $Params ['isBan'], $Params ['inkass_proc'],
            $Params ['fixprice_inside'], $Params ['maxprice_inside'], $Params ['pay_type'], $Params ['send_sms'],
            $Params ['desc'], $Params ['work_times'], $Params ['viber_id'] );

//        stop($this->sql);
		$user_id = $this->insertID();
		if ($user_id > 0) {
			$sql = 'INSERT INTO `groups_user` (`group_id`, `user_id`) VALUES (\'%1$u\', \'%2$u\')';
			$this->query($sql, $Params ['group_id'], $user_id);

			$Params ['user_id'] = $user_id;
			$this->updateAddrAndCard($Params, 1);
		}
		return array($test,$Params);
	}

	public function userUpdate($Params) {
		$sql = 'UPDATE `users`
				SET
				    name = \'%1$s\',
				    email = \'%2$s\',
				    login = \'%3$s\',
				    phone = \'%4$s\',
				    phone_mess = \'%5$s\',
				    title = \'%6$s\',
				    isBan = \'%7$u\',
				    inkass_proc = \'%8$s\',
				    fixprice_inside = \'%9$s\',
				    maxprice_inside = \'%10$s\',
				    pay_type = \'%11$u\',
				    send_sms = \'%12$u\',
				    `desc` = \'%13$s\',
				    work_times = \'%14$s\',
				    viber_id = \'%15$s\'
				    ';
        $sql .= ($Params ['pass'] != '') ? ' ,pass = \''.md5 ( $Params ['pass'] ).'\', psw_chgd = 0 ' : '';

		$sql .= ' WHERE `id` = %16$u';
		$test = $this->query ( $sql, $Params ['username'], $Params ['email'], $Params ['login'], $Params ['phone'],
            $Params ['phone_mess'], $Params ['title'], $Params ['isBan'], $Params ['inkass_proc'],
            $Params ['fixprice_inside'],$Params ['maxprice_inside'],$Params ['pay_type'], $Params ['send_sms'],
            $Params ['desc'], $Params ['work_times'], $Params ['viber_id'], $Params ['user_id'] );

//        stop($this->sql);

		$sql = 'UPDATE `groups_user` SET `group_id`  = \'%1$u\' WHERE `user_id` = \'%2$u\'';
		$this->query ( $sql, $Params ['group_id'], $Params ['user_id'] );

		$this->updateAddrAndCard($Params, 1);

		return $test;
	}

    public function updateAddrAndCard($Params, $type_user){
        $sql_arr = array();
        $upd_ids = array();
        $now_adress = $this->getAddress($Params ['user_id']);
        if (isset($Params['addr_id'])) {
            foreach ($Params['addr_id'] as $key => $a_id) {
                if (array_key_exists($a_id, $now_adress)) {
                    $upd_ids[] = $a_id;
                    // Обновляем, адреса которые были
                    $sql_arr[] = "UPDATE users_address SET comment='" . $Params['addr_comment'][$key] . "', address='" . $Params['address'][$key] . "' WHERE id = $a_id";
                }
                if ($a_id == '') {
                    // Добавляем новые адреса
                    $sql_arr[] = 'INSERT INTO users_address (address,comment,user_id) VALUES (\'' . $Params['address'][$key] . '\',\'' . $Params ['addr_comment'][$key] . '\',' . $Params ['user_id'] . ')';
                }
            }
        }
        foreach ($now_adress as $key => $val) {
            if (!in_array($key, $upd_ids)) {
                // Удаляем адреса, которых нет в обновлении
                $sql_arr[] = "DELETE FROM users_address WHERE id = $key";
            }
        }

        foreach ($sql_arr as $sql) {
            $this->query($sql);
        }


/*
        if (is_array($Params ['address'])) {
            $sql = 'DELETE FROM users_address WHERE user_id = '.$Params ['user_id'].';';
            $this->query ( $sql );
            $values = '';
            foreach ($Params ['address'] as $key => $address) {
                $values .= ($key > 0)?', ':'';
                $values .= ' (\''.$address.'\',\''.$Params ['addr_comment'][$key].'\','.$Params ['user_id'].')';
            }
            $sql = 'INSERT INTO users_address (address,comment,user_id) VALUES '.$values;
            $this->query ( $sql );
        }
*/
		if (is_array($Params ['credit_card'])) {
			$sql = 'DELETE FROM users_cards WHERE user_id = '.$Params ['user_id'].' and type_user = '.$type_user.';';
			$this->query ( $sql );
            $values = '';
			foreach ($Params ['credit_card'] as $key => $item) {
                $values .= ($key > 0)?',':'';
                $values .= ' (\''.$item.'\',\''.$Params ['card_comment'][$key].'\','.$Params ['user_id'].','.$type_user.')';
			}
            $sql = 'INSERT INTO users_cards (card_num,comment,user_id,type_user) VALUES '.$values;
			$this->query ( $sql );
		}
	}
	public function carBan($id) {
		$sql = "UPDATE `cars_couriers`
				SET `isBan` = 1
                WHERE `id` = $id";
		return $this->query ( $sql);
	}

	public function userBan($user_id, $full) {
		$type = 1;
		if ($full)
			$type = 2;
		$sql = "UPDATE `users`
				SET `isban` = $type
                WHERE `id` = $user_id";
		$this->query ( $sql );
		return true;
	}
	
	public function groupHide($group_id) {
		$sql = "UPDATE groups
                SET hidden = 1
                WHERE id = $group_id";
		$this->query ( $sql, $group_id );
		return true;
	}
	public function groupCount($group_id) {
		$sql = "SELECT COUNT(group_id) as count
  				FROM groups_user
  				where group_id = $group_id";
		$this->query ( $sql );
//		$count = array ();
		$count = $this->fetchOneRowA ();
		return $count;
	}
	
	public function userUnBan($user_id) {
		$sql = "UPDATE `users`
				SET `isban` = 0
                WHERE `id` = $user_id";
		$this->query ( $sql );
		return true;
	}
	
//	public function user_rights($user_id) {
//		$sql = 'SELECT allow  FROM user_rights  where right_id=1 AND user_id=' . $user_id;
//		$this->query ( $sql );
//		$user_rights = array ();
//		while ( ($row = $this->fetchRowA ()) !== false ) {
//			$user_rights [] = $row;
//		}
//		return $user_rights;
//	}

    public function getAddress($user_id) {
        $sql = 'SELECT id, address, comment, main FROM users_address  WHERE user_id=' . $user_id;
        $this->query ( $sql );
        $items = array ();
        while ( ($row = $this->fetchRowA ()) !== false ) {
            $items [$row['id']] = $row;
        }
        return $items;
    }

    public function getPayTypes() {
        $sql = 'SELECT id, pay_type FROM orders_pay_types';
        $this->query ( $sql );
        $items = array ();
        while ( ($row = $this->fetchRowA ()) !== false ) {
            $items [] = $row;
        }
        return $items;
    }
    public function getCards($user_id, $type_user) {
        $sql = "SELECT card_num, comment, main  FROM users_cards  WHERE user_id=$user_id and type_user = $type_user";
        $this->query ( $sql );
        $items = array ();
        while ( ($row = $this->fetchRowA ()) !== false ) {
            $items [] = $row;
        }
        return $items;
    }

	public function checkUserData($this_user_id,$elem_type,$elem_value)	{
		$sql = "SELECT id FROM users WHERE $elem_type = '$elem_value'";
		$this->query ( $sql );
		$user_data = $this->fetchRowA ();
		$result = (isset($user_data['id']) and $user_data['id'] != $this_user_id )?'1':'0';
		return $result;
	}
	
	public function userGet($user_id) {
		if (! $user_id){
            return false;
        }
		$sql = "SELECT u.id as user_id, u.*,
                    g.id as group_id, 
                    g.name as group_name
				FROM `users` u
				LEFT JOIN `groups_user` gu ON u.id = gu.user_id
				LEFT JOIN `groups` g ON gu.group_id = g.id
				WHERE u.id = $user_id";
		$this->query ( $sql );
		$user = $this->fetchOneRowA ();
		return $user;
	}

	
	public function userList($id_group='', $user_group='') {

		$fsql = '';
		if ($id_group != '') {
			$fsql .= ' AND g.id = \'' . $id_group . '\' ';
		}
        if ($id_group == '0' and $user_group == 1) {
            $fsql = '  AND g.id != 2 ';
        }


		$sql = "SELECT u.id as user_id, u.*,
                    g.id as group_id, 
                    g.name as group_name, 
                    (SELECT count(o.id) FROM orders o WHERE o.id_user = u.id ) as orders
				FROM `users` u
				LEFT JOIN `groups_user` gu ON u.id = gu.user_id
				LEFT JOIN `groups` g ON gu.group_id = g.id
				WHERE u.isBan < 2 $fsql ";
		$this->query ( $sql );
		$users = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$row ['date_reg'] = date ( 'd.m.Y', strtotime ( substr ( $row ['date_reg'], 0, 20 ) ) );
			$users [] = $row;
		}
		return $users;
	}

	public function getCar($car_id) {
		$sql = 'SELECT
				  id,
				  fio,
				  phone,
				  phone2,
				  telegram,
				  email,
				  viber,
				  car_type,
				  car_year,
				  car_firm,
				  car_number,
				  car_value
				FROM cars_couriers cc
				WHERE cc.id='.$car_id.' ';
		$this->query ( $sql );
		$car = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$car = $row;
		}
		return $car;
	}

	public function getCarTypes() {
		$sql = 'SELECT  id,car_type	FROM car_types ct ';
		$this->query ( $sql );
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$items[] = $row;
		}
		return $items;
	}

	public function carsList() {
		$sql = 'SELECT
				  cc.id,
				  fio,
				  phone,
				  phone2,
				  telegram,
				  email,
				  viber,
				  ct.car_type,
				  car_year,
				  car_firm,
				  car_number,
				  car_value
				FROM cars_couriers cc
				  LEFT JOIN car_types ct ON cc.car_type = ct.id
				  WHERE isBan = 0
				ORDER BY cc.fio ';
		$this->query ( $sql );
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
//			$row ['date_reg'] = date ( 'd.m.Y', strtotime ( substr ( $row ['date_reg'], 0, 20 ) ) );
			$items [] = $row;
		}
		return $items;
	}

    public function getRoutesAddPrices()
    {
        $sql = 'SELECT id, type, cost_route, user_id, dk
				FROM routes_add_price';
        $this->query($sql);
        $items = array();
        while (($row = $this->fetchRowA()) !== false) {
            if ($row['type'] == 'kad') {
                $items ['km_kad'] = $row['cost_route'];
            }
            if ($row['type'] == 'neva') {
                $items ['km_neva'] = $row['cost_route'];
            }
            if ($row['type'] == 'geozone') {
                $items ['km_geozone'] = $row['cost_route'];
            }
            if ($row['type'] == 'vsevol') {
                $items ['km_vsevol'] = $row['cost_route'];
            }
            if ($row['type'] == 'target') {
                $items ['km_target'] = $row['cost_route'];
            }
        }
        return $items;
    }

	public function getRoutesPrices() {
		$sql = 'SELECT id, km_from, km_to, user_id, dk, km_cost
				FROM routes_price
				ORDER BY km_from ';
		$this->query ( $sql );
		$items = array ();
        $i = 0;
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$items [] = $row;
            $i++;
		}
//		// Дополняем массив до 10 значений
//		$fake_price = array('id'=>'', 'km_from'=>'', 'km_to'=>'', 'km_cost'=>'');
//		if ($i < 10) {
//		    for ( ; $i <= 10; $i++){
//                $items [] = $fake_price;
//            }
//        }
		return $items;
	}
	public function getTimeCheckList() {
		$sql = 'SELECT id, type, `from`, `to`, period
				FROM time_check_list ';
		$this->query ( $sql );
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$items [$row['type']] = $row;
		}
		return $items;
	}
	public function getGoodsPriceList() {
		$sql = 'SELECT p.*, t.goods_name
				FROM goods_cond_prices p 
				LEFT JOIN goods_types t ON p.goods_id = t.id';
		$this->query ( $sql );
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$items [] = $row;
		}
		return $items;
	}
	public function getGoodsList() {
		$sql = 'SELECT t.id, t.goods_name
				FROM goods_types t';
		$this->query ( $sql );
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$items [] = $row;
		}
		return $items;
	}

    public function saveRoutesPrices($km_from,$km_to,$km_cost,$km_neva,$km_kad,$km_geozone,$km_vsevol,$km_target,$user_id){
        if (is_array($km_from)) {
            $sql = 'TRUNCATE TABLE routes_price';
            $this->query ( $sql );
            $values = '';
            foreach ($km_from as $key => $km_from_item) {
                if ($km_from_item !== ''){
                    $values .= ($key > 0)?',':'';
                    $values .= ' (\''.$km_from_item.'\',\''.$km_to[$key].'\',\''.$km_cost[$key].'\','.$user_id.', NOW())';
                }
            }
            $sql = 'INSERT INTO routes_price (km_from, km_to, km_cost, user_id, dk) VALUES '.$values;
            $this->query ( $sql );
        }
        $sql = " UPDATE routes_add_price SET cost_route = '$km_neva' WHERE type = 'neva';";
        $this->query ( $sql );
        $sql = " UPDATE routes_add_price SET cost_route = '$km_kad' WHERE type = 'kad';";
        $this->query ( $sql );
        $sql = " UPDATE routes_add_price SET cost_route = '$km_geozone' WHERE type = 'geozone';";
        $this->query ( $sql );
        $sql = " UPDATE routes_add_price SET cost_route = '$km_vsevol' WHERE type = 'vsevol';";
        $this->query ( $sql );
        $sql = " UPDATE routes_add_price SET cost_route = '$km_target' WHERE type = 'target';";
        $this->query ( $sql );
    }
    public function saveTimeCheckList($params){
        foreach ($params as $type => $param) {
            $from   = isset($param['from']) ? $param['from'] : '';
            $to     = isset($param['to']) ? $param['to'] : '';
            $period = isset($param['period']) ? $param['period'] : '';
            $sql = "UPDATE time_check_list SET `from` = '$from', `to` = '$to', `period` = '$period' WHERE type = '$type';";
            $this->query($sql);
        }
        return true;
    }
 public function saveGoodsPriceList($params){
        foreach ($params['id'] as $id) {
            $condition = $params['condition'][$id];
            $value = $params['value'][$id];
            $price = $params['price'][$id];
            $fixed = isset($params['fixed'][$id])?$params['fixed'][$id]:'0';
            $mult = $params['mult'][$id];
            $sql = "UPDATE goods_cond_prices SET `condition` = '$condition', `value` = '$value', `price` = '$price', `fixed` = '$fixed', `mult` = '$mult' WHERE id = '$id';";
            $this->query($sql);
        }
        return true;
    }
    public function saveGoodsList($params){
        foreach ($params['value'] as $id => $value) {
            $sql = "UPDATE goods_types SET `goods_name` = '$value' WHERE id = '$id';";
            $this->query($sql);
        }
        return true;
    }
	public function carUpdate($param) {
		$sql = "
		UPDATE cars_couriers 
			SET
			  fio = '".$param['fio']."' -- fio - VARCHAR(255)
			 ,phone = '".$param['phone']."' -- phone - VARCHAR(255)
			 ,phone2 = '".$param['phone2']."' -- phone2 - VARCHAR(255)
			 ,telegram = '".$param['telegram']."' -- phone2 - VARCHAR(255)
			 ,viber = '".$param['viber']."' -- phone2 - VARCHAR(255)
			 ,email = '".$param['email']."' -- phone2 - VARCHAR(255)
			 ,car_type = ".$param['car_type']." -- car_type - INT(11)
			 ,car_year = ".$param['car_year']." -- car_year - INT(4)
			 ,car_firm = '".$param['car_firm']."' -- car_firm - VARCHAR(255)
			 ,car_number = '".$param['car_number']."' -- car_number - VARCHAR(255)
			 ,car_value = '".$param['car_value']."' -- car_value - VARCHAR(255)
			WHERE
			  id = ".$param['car_id']." -- id - INT(11) NOT NULL
  		";
		$result = $this->query ( $sql );
        $this->updateAddrAndCard($param, 2);
		return $result;
	}
	public function carInsert($param) {
		$sql = "
		INSERT INTO cars_couriers
			(
			 fio
			 ,phone
			 ,phone2
			 ,telegram
			 ,email
			 ,viber
			 ,car_type
			 ,car_year
			 ,car_firm
			 ,car_number
			 ,car_value
			)
			VALUES
			(
			 '".$param['fio']."' -- fio - VARCHAR(255)
			 ,'".$param['phone']."' -- phone - VARCHAR(255)
			 ,'".$param['phone2']."' -- phone2 - VARCHAR(255)
			 ,'".$param['telegram']."' -- phone2 - VARCHAR(255)
			 ,'".$param['email']."' -- phone2 - VARCHAR(255)
			 ,'".$param['viber']."' -- phone2 - VARCHAR(255)
			 ,".$param['car_type']." -- car_type - INT(11)
			 ,".$param['car_year']." -- car_year - INT(4)
			 ,'".$param['car_firm']."' -- car_firm - VARCHAR(255)
			 ,'".$param['car_number']."' -- car_number - VARCHAR(255)
			 ,'".$param['car_value']."' -- car_value - VARCHAR(255)
			);
  		";
        $result = $this->query ( $sql );
        $this->updateAddrAndCard($param, 2);
        return $result;
	}
	public function getLogins($page, $limCount) {
		
		$limStart = 0;
		if ($page != 0) {
			/*$count = $this->getOne ();
			if ($page > $count)
				$page = $count;*/
			if ($page < 1)
				$page = 1;
			
		//$col = 10;
			if ($limCount < 1)
				$limCount = 1;
			$limStart = ($page - 1) * $limCount;
			if ($limStart < 0)
				$limStart = 0;
		}
		
		$sql = 'SELECT 
u.name,lu.ip,lu.date,lu.referer,lu.browser,lu.os,g.name as group_name,
(SELECT COUNT(*) FROM logins) as logscount 
  FROM logins lu
  LEFT JOIN users u ON lu.id_user = u.id
  LEFT JOIN groups_user gu ON u.id = gu.user_id
  LEFT JOIN groups g ON gu.group_id = g.id
   LIMIT ' . $limStart . ', ' . ($limStart + $limCount) . ' ';
		
		$this->query ( $sql );
		
		$logins = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$logins [] = $row;
		}
		return $logins;
	}
	
	public function userTest($login_n) {
		$sql = 'SELECT count(id) FROM `users` u  WHERE u.login = \'%1$s\'';
		$this->query ( $sql, $login_n );
		$test = $this->getOne ();
		return $test;
	}
	
//	/**
//	 * Обновить действия только для модуля
//	 * @param $rights
//	 * @return unknown_type
//	 */
//	public function groupRightModuleUpdate($rights, $group_id) {
//
//	}
	/*
	 *
	 * @param $rights array ( mod_id = array( action_id => access));
	 *
	 */
	public function groupRightUpdate($actions, $group_id) {
		//foreach($rights as $mod => $action) {
		foreach ( $actions as $action_id => $access ) {
			$sql = "INSERT INTO `module_access` (`group_id`, `action_id`, `access`) 
                    VALUES ($group_id, $action_id, $access) ON DUPLICATE KEY UPDATE `access` = $access";
			if ($this->query ( $sql, $group_id, $action_id, $access ))
				$this->Log->addToLog ( 'Задано действие', __LINE__, __METHOD__ );
			else
				$this->Log->addError ( array ('Ошибка задания действия', $action_id, $access ), __LINE__, __METHOD__ );
		}
		//}
		return true;
	}
	public function groupReset($group_id, $module_id) {
	
	}
	
	public function groupAdd($group_name, $parent = 0) {
		$sql = "INSERT INTO `groups` (`name`, `admin`, `parent`) VALUES ('$group_name', 1, $parent)";
		$this->query ( $sql );
		$group_id = $this->insertID ();
		
		if ($group_id > 0) {
			$this->System->actionLog ( $this->mod_id, $group_id, 'Создана новая группа: ' . $group_name, date ( 'Y-d-m h-i-s' ), $this->User->getUserID (), 1, 'groupAdd' );
		}
		return $group_id;
	}
	
	public function groupUpdate($group_name, $group_id) {
		if ($group_id == 0)
			return false;
		$sql = "UPDATE groups SET name = '$group_name' WHERE id = $group_id";
		return $this->query ( $sql );
	}
	
	public function getActions($group_id) {
		$sql = 'SELECT ma.*,
                m.name as mod_name,
                mc.access as mcaccess,
                mc.group_id,
                ug.user_id,
                g.name as gname,
                g.name as gname,
                u.name as uname,';
		
		//         $sql.= '       IF (mc.group_id IS NOT NULL, mc.group_id, 0) as group_adm';
		/*
		$sql.= 'mc.group_id as group_adm
            FROM '.TAB_PREF.'module_actions ma
            INNER JOIN '.TAB_PREF.'modules m ON  ma.mod_id = m.id
            LEFT JOIN ('.TAB_PREF.'module_access mc
            INNER JOIN '.TAB_PREF.'groups_user ug ON mc.group_id = ug.group_id
            INNER JOIN '.TAB_PREF.'groups g ON ug.group_id = g.id AND ug.group_id = %1$u
            INNER JOIN '.TAB_PREF.'users u ON ug.user_id = u.id) ON ma.id = mc.action_id
         
            order by m.id';
		*/
		
		$sql .= 'mc.group_id as group_adm
            FROM module_actions ma
            INNER JOIN modules m ON  ma.mod_id = m.id
            LEFT JOIN module_access mc ON ma.id = mc.action_id AND mc.group_id = %1$u
            LEFT JOIN groups_user ug ON mc.group_id = ug.group_id
            LEFT JOIN groups g ON ug.group_id = g.id 
            LEFT JOIN users u ON ug.user_id = u.id 
          /*  group by ma.id*/
            order by m.id';
		
		if (isset ( $_SESSION ['authorization_LDAP'] )) {
			if ($_SESSION ['authorization_LDAP'] == 1) {
				$sql = 'SELECT ma.*,
                m.name as mod_name,
                mc.access as mcaccess,
                mc.group_id ,       
                mc.group_id as group_adm
            FROM module_actions ma            
            INNER JOIN module_access mc ON ma.id = mc.action_id AND mc.group_id  = 10
            INNER JOIN modules m ON  ma.mod_id = m.id';
			}
		}
		
		$this->query ( $sql, $group_id );
		//stop($this->sql);
//		$coll = array ();
		$lastID = 0;
//		$lastGR = '';
		$actionColl = new actionColl ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
			#stop($row);
			$groupColl = new mcColl ();
			if ($lastID != $row ['id']) {
				$Params = array ();
				$Params ['id'] = $row ['id'];
				$Params ['mod_id'] = $row ['mod_id'];
				$Params ['mod_name'] = $row ['mod_name'];
				$Params ['action_name'] = $row ['action_name'];
				$Params ['action_title'] = $row ['action_title'];
				$Params ['access'] = $row ['access'];
				$Params ['group_adm'] = $row ['group_adm'];
				$Params ['groups'] = $groupColl;
				$action = new moduleAction ( $Params );
				$actionColl->add ( $action );
				$lastID = $row ['id'];
			}
			
			$gr = array ();
			if ($row ['group_id'] > 0 && $row ['mcaccess'] > 0) {
				$gr ['id'] = $row ['group_id'];
				$gr ['action_id'] = $row ['id'];
				$gr ['group_id'] = $row ['group_id'];
				$gr ['group_name'] = $row ['gname'];
				$gr ['group_name'] = $row ['gname'];
				$gr ['user_id'] = $row ['user_id'];
				$gr ['access'] = $row ['access'];
				$gr ['module_id'] = $row ['mod_id'];
				$groupColl->addItem ( $gr );
			}
		
		}
		return $actionColl;
	}
	public function getGroupName($group_id) {
		$sql = "SELECT name FROM groups WHERE id = $group_id";
		$this->query ( $sql, $group_id );
		return $this->getOne ();
	}
	
	public function gelLogs() {
		$logs = array ();
		/*
		if ($type == 'few') {
			$sql = 'SELECT s.*, CONVERT (char(10), s.`date`, 105) as date, u.name as username
					FROM `sys_log` s
					INNER JOIN `users` u ON s.user_id = u.id
					ORDER BY s.`date` DESC';
			$this->query ( $sql );
			while ( ($row = $this->fetchOneRowA ()) !== false ) {
				$logs [] = $row;
			}
		}*/
		return $logs;
	}
}

class adminProcess extends module_process {
	public $updated;
	protected $nModel;
	protected $nView;
	
	public function __construct($modName) {
		global $values, $User, $LOG;
		
		//	if ($modName != 'admin') exit('Access denied');
		parent::__construct ( $modName );
		$this->Vals = $values;		
		$this->modName = $modName;
		$this->User = $User;
		$this->Log = $LOG;
		$this->action = false;
		/* actionDefault - должно быбираться из БД!!! */
		
		$this->actionDefault = '';
		
		$this->actionsColl = new actionColl ();
		
		$this->nModel = new adminModel ( $modName );
		$sysMod = $this->nModel->getSysMod ();
		$this->sysMod = $sysMod;
		$this->mod_id = $sysMod->id;
		
		$this->nView = new adminView ( $this->modName, $this->sysMod );
		
		/* Default Process Class actions */
		$this->regAction ( 'useAdmin', 'Использование Админки', ACTION_GROUP );
		$this->regAction ( 'carBan', 'Блокировка машины/курьера', ACTION_GROUP );
		$this->regAction ( 'carEdit', 'Форма добавления/редактирования машины/курьера', ACTION_GROUP );
		$this->regAction ( 'carUpdate', 'Добавление/обновление курьера', ACTION_GROUP );
		$this->regAction ( 'carsList', 'Список машин/курьеров', ACTION_GROUP );
		$this->regAction ( 'newUser', 'Форма создания пользователя', ACTION_GROUP );
		$this->regAction ( 'addUser', 'Вставить пользователя в БД', ACTION_GROUP );
		$this->regAction ( 'userList', 'Список пользователей', ACTION_GROUP );
		$this->regAction ( 'userEdit', 'Редактировать пользователя', ACTION_GROUP );
		$this->regAction ( 'userUpdate', 'Обновить данные пользователя', ACTION_GROUP );
		$this->regAction ( 'userBan', 'Удалить пользователя в корзину', ACTION_GROUP );
		$this->regAction ( 'userUnBan', 'Восстановить пользователя', ACTION_GROUP );
		$this->regAction ( 'checkUser', 'Проверка пользователя', ACTION_PUBLIC );
		$this->regAction ( 'groupNew', 'Диалог создания группы', ACTION_GROUP );
		$this->regAction ( 'groupAdd', 'Добавить группу', ACTION_GROUP );
		$this->regAction ( 'groupEdit', 'Редактировать группу', ACTION_GROUP );
		$this->regAction ( 'groupUpdate', 'Обновить данные группы', ACTION_GROUP );
		$this->regAction ( 'groupHide', 'Скрыть группу', ACTION_GROUP );
		$this->regAction ( 'groupList', 'Список групп', ACTION_GROUP );
		$this->regAction ( 'groupRights', 'Права групп', ACTION_GROUP );
		$this->regAction ( 'groupRightsAdmin', 'Права групп для Администратора', ACTION_GROUP );
		$this->regAction ( 'groupRightsUpdate', 'Обновление прав групп', ACTION_GROUP );
		$this->regAction ( 'LoginsList', 'Журнал входов', ACTION_GROUP );
		$this->regAction ( 'logs', 'Журнал изменений', ACTION_GROUP );
		$this->regAction ( 'price_routes', 'Стоимость за киллометр', ACTION_GROUP );
		$this->regAction ( 'time_check_list', 'Проверка временных рамок', ACTION_GROUP );
		$this->regAction ( 'goods_price_list', 'Настройка стоимости за перевозку товаров', ACTION_GROUP );
//		$this->regAction ( 'mails', 'Рассылка писем', ACTION_GROUP );
		$this->regAction ( 'getTelegramUpdates', 'Обновления телеграмма', ACTION_GROUP );
		$this->regAction ( 'getViberUpdates', 'Обновления Viber', ACTION_GROUP );
		$this->regAction ( 'getReport', 'Отчет', ACTION_GROUP );
		if (DEBUG == 0) {
			$this->registerActions ( 1 );
		}
		if (DEBUG == 1) {
			$this->registerActions ( 0 );
		
		// $this->registerActions(0);
		}
	
	}
	
	public function update($_action = false) {
		$this->updated = false;
		
		if ($_action)
			$this->action = $_action;
//		$action = $this->actionDefault;
		if ($this->action)
			$action = $this->action;
		else
			$action = $this->checkAction ();
		if (! $action) {
			$this->Vals->URLparams ( $this->sysMod->defQueryString );
			$action = $this->actionDefault;
		}

		$user_id = $this->User->getUserID ();
		$user_group = $this->User->getUserGroup();
        $user_right = $this->User->getRight ( $this->modName, $action );

        $user_edit = $this->Vals->getVal ( 'userEdit', 'GET', 'integer' );
        $user_edit = ($user_edit == '')?$this->Vals->getVal ( 'userUpdate', 'GET', 'integer' ):$user_edit;

        if ($user_right == 0 and !$_action and (!in_array($action, array('userEdit','userUpdate')) or $user_edit != $user_id)) {
            $this->User->nView->viewLoginParams ( '', '', $user_id, array (), array () );
            $this->nView->viewMessage ( 'У вас нет прав на работу с этим модулем.','' );
            $this->updated = true;
            return true;
        }

        $this->User->nView->viewLoginParams ( 'FD', '', $user_id, array (), array (), $this->User->getRightModule ( 'admin' ) );

        if ($action == 'getReport') {
            list($from, $to) = $this->get_post_date('all');
            $items = $this->nModel->getMainReport($from, $to);
            $titles = array('ID', 'Компания', 'Категория', 'Количество доставок', 'Стоимость доставок', 'Заработок компании', 'Суммы по инкассации', 'Заработок с инкассации', 'Сумма', 'Дата последнего заказа', 'Дата регистрации');
            $this->nModel->exportToExcel($titles, $items);
            stop($items);
        }
		
		if ($action == 'newUser') {
			$groups = $this->nModel->getGroups ();
			$this->nView->viewNewUser ( $groups );
			$this->updated = true;
		}
		
		if ($action == 'groupHide') {
			$Params ['group_id'] = $this->Vals->getVal ( 'groupHide', 'GET', 'integer' );
			$count = $this->nModel->groupCount ( $Params ['group_id'] );
			
			if ($count ['count'] > 0) {
				$this->nView->viewError ( array ('Ошибка удаления группы. В группе есть пользователи.' ) );
			} else {
				$res = $this->nModel->groupHide ( $Params ['group_id'] );
				if ($res) {
					$this->nView->viewMessage ( 'Группа перемещена в корзину', 'Сообщение' );
				} else {
					$this->nView->viewError ( array ('Ошибка удаления группы' ) );
				}
				header ( "Location:/admin/groupList-1/" );
			}
		}
		
		if ($action == 'userBan') {
			$Params ['user_id'] = $this->Vals->getVal ( 'userBan', 'GET', 'integer' );
			$Params ['full'] = $this->Vals->getVal ( 'full', 'GET', 'integer' );
			$res = $this->nModel->userBan ( $Params ['user_id'], $Params ['full'] );
			if ($res) {
				$this->nView->viewMessage ( 'Пользователь перемещен в корзину', 'Сообщение' );
			} else {
				$this->nView->viewError ( array ('Ошибка удаления пользователя' ) );
			}
			header ( "Location:/admin/userList-1/" );
		}
		
		if ($action == 'userUnBan') {
			$Params ['user_id'] = $this->Vals->getVal ( 'userUnBan', 'GET', 'integer' );
			$res = $this->nModel->userUnBan ( $Params ['user_id'] );
			if ($res) {
				$this->nView->viewMessage ( 'Пользователь восстановлен из корзины', 'Сообщение' );
			} else {
				$this->nView->viewError ( array ('Ошибка восстановления пользователя' ) );
			}
			header ( "Location:/admin/userList-1/" );
		}
		
		if ($action == 'userUpdate') {
			$Params ['user_id'] = $this->Vals->getVal ( 'user_id', 'POST', 'integer' );

			$Params ['username'] = $this->Vals->getVal ( 'username', 'POST', 'string' );
			$Params ['email'] = $this->Vals->getVal ( 'email', 'POST', 'string' );
			$Params ['title'] = $this->Vals->getVal ( 'title', 'POST', 'string' );
			$Params ['login'] = $this->Vals->getVal ( 'login', 'POST', 'string' );
			$Params ['phone'] = $this->Vals->getVal ( 'phone', 'POST', 'string' );
			$Params ['phone_mess'] = $this->Vals->getVal ( 'phone_mess', 'POST', 'string' );
			$Params ['viber_id'] = $this->Vals->getVal ( 'viber_id', 'POST', 'string' );
			$Params ['pass'] = $this->Vals->getVal ( 'pass', 'POST', 'string' );
			$Params ['group_id'] = $this->Vals->getVal ( 'group_id', 'POST', 'integer' );
			$Params ['pay_type'] = $this->Vals->getVal ( 'pay_type', 'POST', 'integer' );
			$Params ['address'] = $this->Vals->getVal ( 'address', 'POST', 'array' );
			$Params ['addr_id'] = $this->Vals->getVal ( 'addr_id', 'POST', 'array' );
			$Params ['addr_comment'] = $this->Vals->getVal ( 'addr_comment', 'POST', 'array' );
			$Params ['credit_card'] = $this->Vals->getVal ( 'credit_card', 'POST', 'array' );
			$Params ['card_comment'] = $this->Vals->getVal ( 'card_comment', 'POST', 'array' );
			$Params ['isAutoPass'] = $this->Vals->getVal ( 'isAutoPass', 'POST', 'integer' );
			$Params ['isBan'] = $this->Vals->getVal ( 'isBan', 'POST', 'integer' );
			$Params ['send_sms'] = $this->Vals->getVal ( 'send_sms', 'POST', 'integer' );
			$Params ['inkass_proc'] = $this->Vals->getVal ( 'inkass_proc', 'POST', 'string' );
			$Params ['fixprice_inside'] = $this->Vals->getVal ( 'fixprice_inside', 'POST', 'string' );
			$Params ['maxprice_inside'] = $this->Vals->getVal ( 'maxprice_inside', 'POST', 'string' );
			$Params ['work_times'] = $this->Vals->getVal ( 'work_times', 'POST', 'string' );
			$Params ['desc'] = $this->Vals->getVal ( 'desc', 'POST', 'string' );

			if ($Params ['isAutoPass'] > 0) {
				$pass = $this->generatePass ( 6 );
				$Params ['pass'] = $pass;
			}
			if ($Params ['user_id'] == 0) {
				list($res, $Params) = $this->nModel->userInsert ( $Params );
				$msg = 'добавлен';
			}else{
				$res = $this->nModel->userUpdate ( $Params );
				$msg = 'обновлен';
			}
			if ($res) {
			    $profile_url = '/admin/userEdit-'.$Params ['user_id'].'/';
				$this->nView->viewMessage ( 'Профиль успешно '.$msg.'.  Вернуться в <a href="'.$profile_url.'">профиль</a>.<script>window.setTimeout(function(){window.location.href = "/admin/userList-1/idg-2/";}, 5000);</script>', 'Сообщение' );
				$message2 = ' Профиль клиента успешно '.$msg.'<br />' . rn . rn;
				$usInfo = '';
				foreach ( $Params as $key => $val ) {
					$usInfo .= $key . ' : ' . (is_array($val)?json_encode($val, JSON_UNESCAPED_UNICODE):$val) . '<br />' . rn;
				}
				$message2 .= $usInfo;

                if ($Params ['user_id'] == 0) {
                    $user_mess = "<p>Благодарим за регистрацию на сайте pochta911.ru</p>
<p>Для входа используйте: </p>
<p><b>Логин:</b> " . $Params ['login'] . "</p>
<p><b>Пароль:</b> " . $Params ['pass'] . "</p>

<p>Если вы забудьте пароль, вы можете восстановить его, введя телефон на который был зарегистрирован аккаунт, в разделе восстановления пароля.</p>";
                    $user_mail = $Params['email'];
                    sendMail('Профиль '.$msg, $user_mess, $user_mail,'Pochta911.ru');
                    sendMail('Профиль '.$msg, $user_mess, 'Manager_pochta911@mail.ru','Pochta911.ru');
                }
				sendMail('Пользователь '.$msg, $message2, 'djidi@mail.ru','Pochta911.ru');
			} else {
				$this->nView->viewError ( array ('Ошибка редактирования профиля' ) );
			}
			$this->updated = true;
		}
		
		if ($action == 'userEdit') {
			$user_id = $this->Vals->getVal ( 'userEdit', 'GET', 'integer' );
			$group_id = $this->Vals->getVal ( 'idg', 'GET', 'integer' );
			$add_data = $this->Vals->getVal ( 'add_data', 'GET', 'integer' );

			$user = ($user_id > 0)?$this->nModel->userGet ( $user_id ):array();
			$paytypes = $this->nModel->getPayTypes ();
			$address = ($user_id > 0)?$this->nModel->getAddress ($user_id):array();
			$cards = ($user_id > 0)?$this->nModel->getCards ($user_id, 1):array();
			$groups = $this->nModel->getGroups ();

			$this->nView->viewUserEdit ( $user, $paytypes, $groups, $address, $cards, $group_id, $add_data );

			$this->updated = true;
		}

		if ($action == 'carBan') {
			$id = $this->Vals->getVal ( 'carBan', 'GET', 'integer' );
			$res = $this->nModel->carBan ( $id );
			if ($res) {
				$this->nView->viewMessage ( 'Курьер заблокирован', 'Сообщение' );
			} else {
				$this->nView->viewError ( array ('Ошибка блокировки' ) );
			}
			header ( "Location:/admin/carsList-1/" );
		}
		
		if ($action == 'userList') {
			$order = $this->Vals->getVal ( 'srt', 'POST', 'string' );
			$id_group = $this->Vals->getVal ( 'idg', 'INDEX', 'string' );
			$isAjax = $this->Vals->getVal ( 'ajax', 'INDEX' );
			$users = $this->nModel->userList ( $id_group, $user_group );
			$groups = $this->nModel->getGroups ();
			$this->nView->viewUserList ( $users, $order, $isAjax, $id_group, $groups );
			$this->updated = true;
		}
		/* * Конец Пользователи * */
//user_id:user_id,elem_type:elem_type,value:elem_val
		if ($action == 'checkUser') {
			$this_user_id = $this->Vals->getVal ( 'user_id', 'POST', 'string' );
			$elem_type = $this->Vals->getVal ( 'elem_type', 'POST', 'string' );
			$elem_value = $this->Vals->getVal ( 'value', 'POST', 'string' );
			$result = $this->nModel->checkUserData($this_user_id,$elem_type,$elem_value);
			echo $result;
			exit();
		}

		if ($action == 'carEdit') {
			$car_id = $this->Vals->getVal ( 'carEdit', 'GET', 'integer' );
			$car = $this->nModel->getCar ( $car_id );
			$types = $this->nModel->getCarTypes ();
            $cards = ($car_id > 0)?$this->nModel->getCards ($car_id, 2):array();
			$this->nView->viewCarEdit ( $car, $types, $cards);
			$this->updated = true;
		}
		if ($action == 'carUpdate') {
			$param['car_id'] = $this->Vals->getVal ( 'car_id', 'POST', 'integer' );
			$param['fio'] = $this->Vals->getVal ( 'fio', 'POST', 'string' );
			$param['phone'] = $this->Vals->getVal ( 'phone', 'POST', 'string' );
			$param['phone2'] = $this->Vals->getVal ( 'phone2', 'POST', 'string' );
			$param['telegram'] = $this->Vals->getVal ( 'telegram', 'POST', 'string' );
			$param['email'] = $this->Vals->getVal ( 'email', 'POST', 'string' );
			$param['viber'] = $this->Vals->getVal ( 'viber', 'POST', 'string' );
			$param['car_firm'] = $this->Vals->getVal ( 'car_firm', 'POST', 'string' );
			$param['car_number'] = $this->Vals->getVal ( 'car_number', 'POST', 'string' );
			$param['car_year'] = $this->Vals->getVal ( 'car_year', 'POST', 'integer' );
			$param['car_value'] = $this->Vals->getVal ( 'car_value', 'POST', 'integer' );
			$param['car_type'] = $this->Vals->getVal ( 'car_type', 'POST', 'integer' );
            $param['credit_card'] = $this->Vals->getVal ( 'credit_card', 'POST', 'array' );
            $param['card_comment'] = $this->Vals->getVal ( 'card_comment', 'POST', 'array' );
            $param['user_id'] = $param['car_id']; // Для сохранения карт оплаты
            $param['address'] = array();
			if ($param['car_id'] > 0) {
				$result = $this->nModel->carUpdate($param);
			}else{
				$result = $this->nModel->carInsert($param);
			}
			if ($result){
				$this->nView->viewMessage('Операция выполнена успешно', '' );
			}else{
				$this->nView->viewError ( array ('Ошибка выполнения' ) );
			}
			$action = 'carsList';
		}
		if ($action == 'carsList') {
			$cars = $this->nModel->carsList ( );
			$this->nView->viewCarsList ( $cars );
			$this->updated = true;
		}
		/* Цены по киллометрам */
		if ($action == 'price_routes') {
		    if ($this->Vals->getVal ( 'sub_action', 'POST', 'string' ) == 'save'){
                $km_from = $this->Vals->getVal ( 'km_from', 'POST', 'array' );
                $km_to = $this->Vals->getVal ( 'km_to', 'POST', 'array' );
                $km_cost = $this->Vals->getVal ( 'km_cost', 'POST', 'array' );
                $km_neva = $this->Vals->getVal ( 'km_neva', 'POST', 'string' );
                $km_kad = $this->Vals->getVal ( 'km_kad', 'POST', 'string' );
                $km_geozone = $this->Vals->getVal ( 'km_geozone', 'POST', 'string' );
                $km_vsevol = $this->Vals->getVal ( 'km_vsevol', 'POST', 'string' );
                $km_target = $this->Vals->getVal ( 'km_target', 'POST', 'string' );
                $this->nModel->saveRoutesPrices($km_from,$km_to,$km_cost,$km_neva,$km_kad,$km_geozone,$km_vsevol,$km_target,$user_id);
            }
		    $prices = $this->nModel->getRoutesPrices();
		    $add_prices = $this->nModel->getRoutesAddPrices();
		    $this->nView->viewRoutesPrices($prices,$add_prices);
            $this->updated = true;
        }
        /* Проверка временных рамок */
        if ($action == 'time_check_list') {
            if ($this->Vals->getVal ( 'sub_action', 'POST', 'string' ) == 'save'){
                $params['period_tomarrow'] = $this->Vals->getVal('period_tomarrow', 'POST', 'array');
                $params['period_today'] = $this->Vals->getVal('period_today', 'POST', 'array');
                $params['period_from'] = $this->Vals->getVal('period_from', 'POST', 'array');
                $params['ready_1'] = $this->Vals->getVal('ready_1', 'POST', 'array');
                $params['ready_2'] = $this->Vals->getVal('ready_2', 'POST', 'array');
                $params['ready_3'] = $this->Vals->getVal('ready_3', 'POST', 'array');
                $params['ready_today'] = $this->Vals->getVal('ready_today', 'POST', 'array');
                $params['period'] = $this->Vals->getVal('period', 'POST', 'array');
                $this->nModel->saveTimeCheckList($params);
            }
            $times = $this->nModel->getTimeCheckList();
            $this->nView->viewTimeCheckList($times);
            $this->updated = true;
        }

        /* Справочник типов товара */
        if ($action == 'goods_price_list') {
            if ($this->Vals->getVal ( 'sub_action', 'POST', 'string' ) == 'save'){
                $params['id'] = $this->Vals->getVal('id', 'POST', 'array');
                $params['condition'] = $this->Vals->getVal('condition', 'POST', 'array');
                $params['value'] = $this->Vals->getVal('value', 'POST', 'array');
                $params['price'] = $this->Vals->getVal('price', 'POST', 'array');
                $params['fixed'] = $this->Vals->getVal('fixed', 'POST', 'array');
                $params['mult'] = $this->Vals->getVal('mult', 'POST', 'array');
                $this->nModel->saveGoodsPriceList($params);
            }
            if ($this->Vals->getVal ( 'sub_action', 'POST', 'string' ) == 'save_goods'){
                $params['value'] = $this->Vals->getVal('value', 'POST', 'array');
                $this->nModel->saveGoodsList($params);
            }
            $prices = $this->nModel->getGoodsPriceList();
            $goods = $this->nModel->getGoodsList();
            $this->nView->viewGoodsPriceList($prices,$goods);
            $this->updated = true;
        }
		/* * Группы * */
		
		if ($action == 'groupNew') {
			$this->nView->viewNewGroup ();
			$this->updated = true;
		}
		
		if ($action == 'groupAdd') {
			$group_name = $this->Vals->getVal ( 'name', 'POST', 'string' );
			if ($group_name != '')
				$this->nModel->groupAdd ( $group_name );
			else
				$this->nView->viewError ( array ('Укажите название группы (rus)' ) );
			$action = 'groupList';
		}
		
		if ($action == 'groupEdit') {
			$group_id = $this->Vals->getVal ( 'groupEdit', 'GET', 'integer' );
			$group_name = $this->nModel->getGroupName ( $group_id );
			$this->nView->viewEditGroup ( $group_name, $group_id );
			$this->updated = true;
		}
		if ($action == 'groupUpdate') {
			$group_id = $this->Vals->getVal ( 'group_id', 'POST', 'integer' );
			$group_name = $this->Vals->getVal ( 'name', 'POST', 'string' );
			if (! $this->nModel->groupUpdate ( $group_name, $group_id ))
				$this->nView->viewError ( array ('Ошибка обновления группы' ) );
			
		//	else $this->System->actionLog($this->mod_id, $group_id, 'Обновлена группа: '.$group_name.'/'.$group_name, dateToDATETIME (date('d-m-Y h-i-s')), $this->User->getUserID(), 1, $action);
			$action = 'groupList';
		}
		
		if ($action == 'groupRightsUpdate') {
			$actions = $this->Vals->getVal ( 'action', 'POST', 'array' );
			$group_id = $this->Vals->getVal ( 'group_id', 'POST', 'integer' );
			if ($group_id < 1) {
				$this->nView->viewError(array('Группа не найдена'));
				return true;
			}
			
		//stop($actions);
			if ($this->nModel->groupRightUpdate ( $actions, $group_id )) {
				$this->nView->viewMessage ( 'Права группы обновлены', 'Сообщение' );
			
		//	$this->System->actionLog($this->mod_id, $group_id, 'Обновлены права группы: '.$group_id, dateToDATETIME (date('d-m-Y h-i-s')), $this->User->getUserID(), 1, $action);
			} else {
				$this->nView->viewError ( array ('Ошибка обновления группы' ) );
			}
		}
		
		if ($action == 'groupList') {
			
			$groups = $this->nModel->getGroups ();
			$this->nView->viewGroups ( $groups );
			$this->updated = true;
		}
		
		if ($action == 'LoginsList') {
			
			$limCount = $this->vals->getVal ( 'count', 'get', 'integer' );
			if (! $limCount)
				$limCount = $this->vals->getModuleVal ( $this->modName, 'count', 'GET' );
			$page = $this->vals->getVal ( 'page', 'GET', 'integer' );
			if ($page <= 0 || $page === NULL) {
				$this->Vals->setValTo ( 'page', '1', 'GET' );
				$page = 1;
			}
			if ($limCount == 0)
				$limCount = 20;
			
			$logins = $this->nModel->getLogins ( $page, $limCount );
			
			$Archive = new archiveStruct ( $this->modName, $logins [0] ['logscount'], $limCount, $page, '' );
			
			$this->nView->viewLogins ( $logins, $Archive );
			$this->updated = true;
		}
		
		if ($action == 'groupRights') {
			$group_id = $this->Vals->getVal ( 'groupRights', 'GET', 'integer' );
			$actions = $this->nModel->getActions ( $group_id );
			$group_name = $this->nModel->getGroupName ( $group_id );
			$this->nView->viewGroupRight ( $actions, $group_name, $group_id );
			$this->updated = true;
		}
		
		if ($action == 'groupRightsAdmin') {
			$group_id = $this->Vals->getVal ( 'groupRightsAdmin', 'GET', 'integer' );
			$actions = $this->nModel->getActions ( $group_id );
			$group_name = $this->nModel->getGroupName ( $group_id );
			$this->nView->viewGroupRightAdmin ( $actions, $group_name, $group_id );
			$this->updated = true;
		}
		/* * Конец Группы * */
		
		if ($action == 'logs') {
			$type = 'few';
			$logs = $this->nModel->gelLogs (  );
			$this->nView->viewLogs ( $logs, $type );
			$this->updated = true;
		}

		if ($action == 'getTelegramUpdates') {
			$users = $this->nModel->userList();
            $items = $this->nModel->loadTelegramUpdates ();
			$this->nView->viewTelegramUpdates ( $items, $users );
			$this->updated = true;
		}

		if ($action == 'getViberUpdates') {
			$users = $this->nModel->userList();
            $items = $this->nModel->loadViberUpdates ();
			$this->nView->viewViberUpdates ( $items, $users );
			$this->updated = true;
		}
		
		if ($this->Vals->isVal ( 'ajax', 'INDEX' )) {
			if ($this->Vals->isVal ( 'xls', 'INDEX' )) {
				$PageAjax = new PageForAjax ( $this->modName, $this->modName, $this->modName, 'page.xls.xsl' );
				$PageAjax->addToPageAttr ( 'xls', '1' );
			} else
				$PageAjax = new PageForAjax ( $this->modName, $this->modName, $this->modName, 'page.ajax.xsl' );
			$isAjax = $this->Vals->getVal ( 'ajax', 'INDEX' );
			$PageAjax->addToPageAttr ( 'isAjax', $isAjax );			
			$html = $PageAjax->getBodyAjax2 ( $this->nView );

			if ($this->Vals->isVal ( 'xls', 'INDEX' )) {
				$reald = date ( "d.m.Y" );
				header ( "Content-Type: application/vnd.ms-excel", true );
				header ( "Content-Disposition: attachment; filename=\"list_" . $reald . ".xls\"" );
				exit ( $html );
			} else
				sendData ( $html );
		
		}
		
		if (! $this->updated) {
			$this->nView->viewMainPage ();
			$this->updated = true;
		}
	    return true;
	}
	
	function generatePass($length = 6) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789_;.";
		$code = "";
		$clean = strlen ( $chars ) - 1;
		while ( strlen ( $code ) < $length ) {
			$code .= $chars [mt_rand ( 0, $clean )];
		}
		return $code;
	}

	public function callApiTlg( $method, $params, $access_token) {
		$url = sprintf(
			"https://api.telegram.org/bot%s/%s",
			$access_token,
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
			CURLOPT_SSL_VERIFYPEER  => FALSE,
		));

		$response = curl_exec($ch);
		if(curl_error($ch))
		{
			stop ('error:' . curl_error($ch));
		}
		return json_decode($response);
	}

    public function get_post_date($type = 'to'){
        if ($type == 'all') {
            $from = $this->Vals->getVal ( 'date_from', 'POST', 'string' );
        }
        $to = $this->Vals->getVal ( 'date_to', 'POST', 'string' );
        if ($type == 'to') {
            $from = $to;
        }
        if ($to == '') {
            if ($type == 'all') {
                $from = (isset($_SESSION['date_from']) and $_SESSION['date_from'] != '') ? $_SESSION['date_from'] : date('01.m.Y');
            }
            $to = (isset($_SESSION['date_to']) and $_SESSION['date_to'] != '') ? $_SESSION['date_to'] : date('d.m.Y');
            if ($type == 'to') {
                $from = $to;
            }
        }else{
            $_SESSION['date_from'] = (isset($from))?$from:$to;
            $_SESSION['date_to'] = $to;
        }
        return array((isset($from))?$from:$to,$to);
    }

}

class adminView extends module_view {
	public function __construct($modName, modsetItem $sysMod) {
		parent::__construct ( $modName, $sysMod );
		$this->pXSL = array ();
	}
	
	public function addXML(bodySet $bodySet, $contName) {
		$this->pXSL = array_merge ( $this->pXSL, $bodySet->getXSL () );
		$Container = $this->newContainer ( $contName );
		parent::mergeXML ( $this->xml, $Container, $bodySet->getXML (), 'xx' );
	}
	
	public function viewNewUser($groups) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/user.new.xsl';
		$Container = $this->newContainer ( 'newuser' );
		$ContainerGroups = $this->addToNode ( $Container, 'groups', '' );
		// stop($groups);
		foreach ( $groups as $item ) {
			$this->arrToXML ( $item, $ContainerGroups, 'item' );
		}
		return true;
	}
	/*
	 *
	 *
	 * @param $users array()
	 * @return boolean
	 */
	public function viewUserList($users, $order, $isAjax, $id_group, $groups) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/user.list.xsl';
		if ($isAjax == 1) {
			$this->pXSL [] = RIVC_ROOT . 'layout/head.page.xsl';
		}
		
		$Container = $this->newContainer ( 'userlist' );
		$Containerusers = $this->addToNode ( $Container, 'users', '' );
		$this->addAttr ( 'order', $order, $Containerusers );
		$this->addAttr ( 'id_group', $id_group, $Containerusers );
		foreach ( $users as $user ) {
			$this->arrToXML ( $user, $Containerusers, 'user' );
		}
		$ContainerGroups = $this->addToNode ( $Container, 'groups', '' );
		foreach ( $groups as $item ) {
			$this->arrToXML ( $item, $ContainerGroups, 'item' );
		}
		return true;
	}

	public function viewCarsList($cars) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/cars.list.xsl';

		$Container = $this->newContainer ( 'carslist' );
		$ContainerCars = $this->addToNode ( $Container, 'cars', '' );
		foreach ( $cars as $user ) {
			$this->arrToXML ( $user, $ContainerCars, 'car' );
		}
		return true;
	}

	public function viewCarEdit($car, $car_types, $cards) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/car.edit.xsl';
		$Container = $this->newContainer ( 'caredit' );
		$this->arrToXML ( $car, $Container, 'car' );
		$ContainerCarTypes = $this->addToNode ( $Container, 'car_types', '' );
		foreach ( $car_types as $item ) {
			$this->arrToXML ( $item, $ContainerCarTypes, 'item' );
		}
        $ContainerCards = $this->addToNode ( $Container, 'cards', '' );
        foreach ( $cards as $item ) {
            $this->arrToXML ( $item, $ContainerCards, 'item' );
        }
		return true;
	}

	public function viewMainPage() {
		$this->pXSL [] = RIVC_ROOT . 'layout/admin/admin.main.xsl';
		$this->newContainer ( 'adminmain' );
		return true;
	}
	
	public function viewUserEdit($user, $paytypes, $groups, $address, $cards, $group_id, $add_data) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/user.edit.xsl';
		$Container = $this->newContainer ( 'useredit' );;
        $this->addAttr ( 'group_id', $group_id, $Container );
        $this->addAttr ( 'add_data', $add_data, $Container );
		$this->arrToXML ( $user, $Container, 'user' );
		$ContainerPayTypes = $this->addToNode ( $Container, 'pay_types', '' );
		foreach ( $paytypes as $item ) {
			$this->arrToXML ( $item, $ContainerPayTypes, 'item' );
		}
		$ContainerGroups = $this->addToNode ( $Container, 'groups', '' );
		foreach ( $groups as $item ) {
			$this->arrToXML ( $item, $ContainerGroups, 'item' );
		}
        $ContainerAddress = $this->addToNode ( $Container, 'address', '' );
        foreach ( $address as $item ) {
            $this->arrToXML ( $item, $ContainerAddress, 'item' );
        }
        $ContainerCards = $this->addToNode ( $Container, 'cards', '' );
        foreach ( $cards as $item ) {
            $this->arrToXML ( $item, $ContainerCards, 'item' );
        }
//		$ContainerRights = $this->addToNode ( $Container, 'user_rights', '' );
//		foreach ( $user_rights as $item ) {
//			$this->arrToXML ( $item, $ContainerRights, 'item' );
//		}
		
		return true;
	}
	
	public function viewGroups($groups) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/group.list.xsl';
		$Container = $this->newContainer ( 'grouplist' );
		$ContainerGroups = $this->addToNode ( $Container, 'groups', '' );
		foreach ( $groups as $item ) {
			$this->arrToXML ( $item, $ContainerGroups, 'item' );
		}
		return true;
	}

	public function viewRoutesPrices($prices,$add_prices) {
		$this->pXSL [] = RIVC_ROOT . 'layout/admin/prices.list.xsl';
		$Container = $this->newContainer ( 'priceslist' );
		$ContainerGroups = $this->addToNode ( $Container, 'prices', '' );
		foreach ( $prices as $item ) {
			$this->arrToXML ( $item, $ContainerGroups, 'item' );
		}
        $this->arrToXML ( $add_prices, $ContainerGroups, 'add_item' );

		return true;
	}
	public function viewTimeCheckList($times) {
		$this->pXSL [] = RIVC_ROOT . 'layout/admin/times.list.xsl';
		$Container = $this->newContainer ( 'timeslist' );
        $this->arrToXML ( $times, $Container, 'times' );
		return true;
	}
	public function viewGoodsPriceList($prices,$goods) {
		$this->pXSL [] = RIVC_ROOT . 'layout/admin/goods.prices.list.xsl';
		$Container = $this->newContainer ( 'priceslist' );
		foreach ( $prices as $item ) {
			$this->arrToXML ( $item, $Container, 'item' );
		}
        $ContainerGoods = $this->addToNode ( $Container, 'goods', '' );
        foreach ( $goods as $item ) {
            $this->arrToXML ( $item, $ContainerGoods, 'item' );
        }
		return true;
	}

	public function viewNewGroup() {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/group.new.xsl';
		$this->newContainer ( 'groupnew' );
		return true;
	}
	
	public function viewEditGroup($group_name, $group_id) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/group.edit.xsl';
		$Container = $this->newContainer ( 'groupedit' );
		$groupC = $this->addToNode ( $Container, 'group', '' );
		$this->addAttr ( 'group_id', $group_id, $groupC );
		$this->addAttr ( 'group_name', $group_name, $groupC );
		return true;
	}
	
	public function viewGroupRight(actionColl $actions, $group_name, $group_id) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/group.rights.xsl';
		$Container = $this->newContainer ( 'grouprights' );
		$actIterator = $actions->getIterator ();
		$ContainerAct = $this->addToNode ( $Container, 'actions', '' );
		$this->addAttr ( 'group_id', $group_id, $ContainerAct );
		$this->addAttr ( 'group_name', $group_name, $ContainerAct );
		/* moduleAction */
		//$action = new moduleAction();
		$lastMod = 0;
		foreach ( $actIterator as $action ) {
			if ($lastMod != $action->mod_id) {
				$modElememt = $this->addToNode ( $ContainerAct, 'module', '' );
				$this->addAttr ( 'mod_name', $action->mod_name, $modElememt );
				$this->addAttr ( 'mod_id', $action->mod_id, $modElememt );
				$lastMod = $action->mod_id;
			}
			$aArray = $action->toArray ();
            if (isset($modElememt)) {
                $actElememt = $this->arrToXML($aArray, $modElememt, 'action');
                if ($action->groups->count() > 0) {
                    $this->addToNode($actElememt, 'inGroup', $action->groups->count());
                } else {
                    $this->addToNode($actElememt, 'inGroup', 0);
                }
            }
		//stop($action->groups->count(), 0);
		}
		return true;
	}
	
	public function viewGroupRightAdmin(actionColl $actions, $group_name, $group_id) {
		$this->pXSL [] = RIVC_ROOT . 'layout/users/group.rights.Admin.xsl';
		$Container = $this->newContainer ( 'grouprights' );
		$actIterator = $actions->getIterator ();
		$ContainerAct = $this->addToNode ( $Container, 'actions', '' );
		$this->addAttr ( 'group_id', $group_id, $ContainerAct );
		$this->addAttr ( 'group_name', $group_name, $ContainerAct );
		/* moduleAction */
		//$action = new moduleAction();
		$lastMod = 0;
		foreach ( $actIterator as $action ) {
			if ($lastMod != $action->mod_id) {
				$modElememt = $this->addToNode ( $ContainerAct, 'module', '' );
				$this->addAttr ( 'mod_name', $action->mod_name, $modElememt );
				$this->addAttr ( 'mod_id', $action->mod_id, $modElememt );
				$lastMod = $action->mod_id;
			}
			$aArray = $action->toArray ();
            if (isset($modElememt)) {
                $actElememt = $this->arrToXML($aArray, $modElememt, 'action');
                if ($action->groups->count() > 0) {
                    $this->addToNode($actElememt, 'inGroup', $action->groups->count());
                } else {
                    $this->addToNode($actElememt, 'inGroup', 0);
                }
            }
		//stop($action->groups->count(), 0);
		}
		return true;
	}
	
	public function viewLogins($logins, archiveStruct $Archive) {
		$this->pXSL [] = RIVC_ROOT . 'layout/admin/admin.logins.xsl';
		$Container = $this->newContainer ( $Archive->module . '/LoginsList-1' );
		$this->addAttr ( 'module', $Archive->module . '/LoginsList-1', $Container );
		$this->addAttr ( 'count', $Archive->count, $Container );
		$this->addAttr ( 'size', $Archive->size, $Container );
		$this->addAttr ( 'curPage', $Archive->curPage, $Container );
		foreach ( $logins as $aArray ) {
            $this->arrToXML($aArray, $Container, 'item');
        }
		return true;
	}
	
	public function viewLogs($logs, $type) {
		$this->pXSL [] = RIVC_ROOT . 'layout/admin/admin.logs.xsl';
		$Container = $this->newContainer ( 'logsfew' );
		
		if ($type == 'few') {
			foreach ( $logs as $aArray ) {
                $this->arrToXML($aArray, $Container, 'item');
            }
		}
		return true;
	}

	public function viewTelegramUpdates($items,$users) {
		$this->pXSL [] = RIVC_ROOT . 'layout/admin/telegram.logs.xsl';
		$Container = $this->newContainer ( 'messages' );
		foreach ( $items as $aArray ) {
			$this->arrToXML($aArray, $Container, 'item');
		}
		$Containerusers = $this->addToNode ( $Container, 'users', '' );
		foreach ( $users as $user ) {
			$this->arrToXML ( $user, $Containerusers, 'user' );
		}
		return true;
	}

	public function viewViberUpdates($items,$users) {
		$this->pXSL [] = RIVC_ROOT . 'layout/admin/viber.logs.xsl';
		$Container = $this->newContainer ( 'messages' );
		foreach ( $items as $aArray ) {
			$this->arrToXML($aArray, $Container, 'item');
		}
		$Containerusers = $this->addToNode ( $Container, 'users', '' );
		foreach ( $users as $user ) {
			$this->arrToXML ( $user, $Containerusers, 'user' );
		}
		return true;
	}



}