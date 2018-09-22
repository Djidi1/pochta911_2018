<?php

class ordersModel extends module_model {
	public function __construct($modName) {
		parent::__construct ( $modName );
	}

	public function get_assoc_array($sql){
		$this->query ( $sql );
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {
            $vowels = array(
                'г Санкт-Петербург,',
                'г. Санкт-Петербург,',
                'г Санкт-Петербург,',
                'Г. Санкт-Петербург,',
                'город Санкт-Петербург,',
                'Санкт-Петербург,',
                'Россия',
            );
			if (isset($row['to'])) $row['to'] = str_replace($vowels,'',$row['to']);
			if (isset($row['address'])) $row['address'] = str_replace($vowels,'',$row['address']);
			if (isset($row['from'])) $row['from'] = str_replace($vowels,'',$row['from']);
            if (isset($row['to'])) $row['to'] = trim($row['to'],', ');
            if (isset($row['address'])) $row['address'] = trim($row['address'],', ');
            if (isset($row['from'])) $row['from'] = trim($row['from'],',');

			if (isset($row['ready'])) $row['ready'] = substr($row['ready'],0,5);
			if (isset($row['to_time'])) $row['to_time'] = substr($row['to_time'],0,5);
			if (isset($row['to_time_end'])) $row['to_time_end'] = substr($row['to_time_end'],0,5);
			if (isset($row['to_time_ready'])) $row['to_time_ready'] = substr($row['to_time_ready'],0,5);
			if (isset($row['to_time_ready_end'])) $row['to_time_ready_end'] = substr($row['to_time_ready_end'],0,5);
            if (isset($row['date'])) $row['date'] = $this->dateToRuFormat($row['date']);
//            if (isset($row['from_phone'])) $row['from_phone'] = $this->formatPhoneNumber($row['from_phone']);
//            if (isset($row['to_phone'])) $row['to_phone'] = $this->formatPhoneNumber($row['to_phone']);
//            if (isset($row['phone'])) $row['phone'] = $this->formatPhoneNumber($row['phone']).' - '.$row['phone'];
			$items[] = $row;
		}
		return $items;
	}

    public function formatPhoneNumber($phone){
        $phone = preg_replace("/[^0-9]/", "", ($phone) );
        return '+7'.substr($phone,1,10);
    }

    public function formatPhoneNumber8($phone){
        $phone = preg_replace("/[^0-9]/", "", ($phone) );
        return '8'.substr($phone,1,10);
    }

    public function getUserId($phone){
        $sql = "SELECT id FROM users WHERE phone = '$phone'";
        $this->query($sql);
        return $this->getOne();
    }

	public function exportToExcel($titles,$orders){
        require_once CORE_ROOT . 'classes/PHPExcel.php';
        // Instantiate a new PHPExcel object
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
        foreach($orders as $order){
            $colCount = 'A';
            foreach ($order as $col_value) {
                $cell = $colCount.$rowCount;
                $objPHPExcel->getActiveSheet()->SetCellValue($cell, $col_value);
//                $objPHPExcel->getActiveSheet()->getColumnDimension($colCount)->setAutoSize(true);
                $colCount++;
            }
            $rowCount++;
        }
        $file_name = 'orders'.date('_Y_m_d').'.xlsx';
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
        $objWriter->save('php://output');
        exit;
    }


    public function getClientData($user_id, $address_id){
		$sql = "SELECT
				  u.title, u.name, u.phone, ua.address, ua.comment
				FROM users u 
				LEFT JOIN users_address ua ON ua.user_id = u.id
				WHERE u.id = $user_id and ua.id = $address_id";
		return $this->get_assoc_array($sql);
	}
    public function getClientTitle($user_id){
		$sql = 'SELECT
				  title
				FROM users
				WHERE id = '.$user_id;
		return $this->get_assoc_array($sql);
	}

	public function getStores($user_id) {
		$sql = 'SELECT
				  id,
				  address
				FROM users_address
				WHERE user_id = '.$user_id;
		return $this->get_assoc_array($sql);
	}
	public function getStatuses() {
		$sql = 'SELECT
				  id,
				  status
				FROM orders_status ';
		return $this->get_assoc_array($sql);
	}
	public function getCarCouriers() {
		$sql = 'SELECT
				  id,
				  fio, car_number
				FROM cars_couriers WHERE isBan != 1
				ORDER BY fio';
		return $this->get_assoc_array($sql);
	}
	public function getCouriers() {
		$sql = 'SELECT
				  id,
				  fio
				FROM couriers WHERE isBan != 1
				ORDER BY fio';
		return $this->get_assoc_array($sql);
	}

	public function getRoutes($order_id) {
		$sql = 'SELECT r.id id_route, `to`,to_region,to_AOGUID,to_house,to_corpus,to_appart,
					  to_fio,to_phone,to_coord,from_coord,lenght,cost_route,cost_tovar,cost_car,
					  `to_time`,`to_time_end`,r.`comment`, s.status, s.id status_id, r.pay_type as pay_type_id, p.pay_type,
					  r.to_time_ready, r.to_time_ready_end, r.goods_type, r.goods_val, g.goods_name
				FROM orders_routes r
				LEFT JOIN orders_status s ON s.id = r.id_status
				LEFT JOIN goods_types g ON g.id = r.goods_type
				LEFT JOIN orders_pay_types p ON p.id = r.pay_type
				WHERE id_order = '.$order_id;
		return $this->get_assoc_array($sql);
	}
    public function getUsers($uid) {
        $sql = "SELECT u.id, u.name, u.phone, u.title, u.pay_type, a.`from`, a.from_appart, a.from_comment
                FROM users u
                LEFT JOIN groups_user g ON u.id = g.user_id
                LEFT JOIN  
                  (SELECT id_user, `from`, from_appart, from_comment, COUNT(id)
                  FROM orders
                  WHERE `from` IS NOT NULL
                  GROUP BY id_user, `from`, from_appart
                  ORDER BY COUNT(id) DESC
                  LIMIT 1) AS a ON a.id_user = u.id
                WHERE u.isban < 1 and (g.group_id = 2 or u.id = $uid)
                 AND (name <> '' OR phone <> '')
                 ORDER BY u.title";
        return $this->get_assoc_array($sql);
    }
    public function getUserParams($uid) {
        $sql = "SELECT pay_type, fixprice_inside, maxprice_inside FROM users u WHERE u.id = $uid";
        $this->query($sql);
        $result = $this->fetchRowA ();
        return array(
            'pay_type' => $result['pay_type'],
            'fixprice' => $result['fixprice_inside'],
            'maxprice' => $result['maxprice_inside']
        );
    }
    public function getPrices() {
        $sql = 'SELECT id, km_from, km_to, km_cost FROM routes_price r';
        return $this->get_assoc_array($sql);
    }
    public function getPayTypes() {
        $sql = 'SELECT id, pay_type FROM orders_pay_types opt';
        return $this->get_assoc_array($sql);
    }
    public function getAddPrices() {
        $sql = 'SELECT id, type, cost_route FROM routes_add_price r';
        return $this->get_assoc_array($sql);
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
        $goods = array ();
        while ( ($row = $this->fetchRowA ()) !== false ) {
            $items [] = $row;
        }
        foreach ($items as $item){
            $goods[$item['goods_id']] = $item;
        }
        return array($items,$goods);
    }
	public function getSpbStreets(){
		$sql = 'SELECT id, street_name name FROM spb_streets';
		return $this->get_assoc_array($sql);
	}

	public function getOrderRoute($order_route_id) {
	    $sql = "SELECT id id_route, id_order, `to`, to_house, to_corpus, to_appart, to_fio, to_phone, to_coord, 
                      from_coord, lenght, cost_route,cost_tovar,cost_car, to_date, to_time, comment, id_status, dk 
                FROM orders_routes
                WHERE id = $order_route_id";
	    $row = $this->get_assoc_array($sql);
	    return $row[0];
    }

    public function checkUserCard($user_id){
        $not_all = false;
	    $sql = "SELECT id, name, email, login, pass, date_reg, isban, prior, title, phone, phone_mess, inkass_proc 
                FROM users WHERE id = $user_id";
	    $this->query($sql);
	    $user = $this->fetchOneRowA();
        if ($user['name'] == ''){
            $not_all = true;
        }
        if ($user['email'] == ''){
            $not_all = true;
        }
        if ($user['title'] == ''){
            $not_all = true;
        }
        if ($user['phone'] == ''){
            $not_all = true;
        }
        return $not_all;
    }

    public function checkUserPswChgd($user_id){
        $PswChgd = false;
        $sql = "SELECT psw_chgd FROM users WHERE id = $user_id";
        $this->query($sql);
        $user = $this->fetchOneRowA();
        if ($user['psw_chgd'] == '1'){
            $PswChgd = true;
        }
        return $PswChgd;
    }

    public function getOrderInfo($order_id) {
        $sql = 'SELECT o.id,
                      (CASE 
					        WHEN o.id_address = 0 THEN o.from
					        ELSE ua.address
					    END) AS `from`,
					   (CASE 
					        WHEN o.id_address = 0 THEN o.from_comment
					        ELSE ua.comment
					    END) AS from_comment,
					   o.from_fio, 
					   o.from_phone,
                       u.inkass_proc,
                       o.id_car,
					   o.ready,
					   o.date
				FROM orders o
				LEFT JOIN users_address ua ON ua.id = o.id_address
				LEFT JOIN users u ON u.id = o.id_user
				WHERE o.id = '.$order_id;
        $items = $this->get_assoc_array($sql);
        return isset($items[0])?$items[0]:array();
    }

    public function getOrderRoutesInfo($order_id) {
        $sql = 'SELECT r.id,
                       r.to_time,
                       r.to_time_end,
                       r.to_time_ready,
                       r.to_time_ready_end,
                       CONCAT(r.`to`,/*\', д.\',r.`to_house`,\', корп.\',r.`to_corpus`,*/\', \',r.`to_appart`) to_addr,
                       r.to_fio,
                       r.to_phone,
                       r.pay_type,
					   r.cost_route,
					   r.cost_tovar,
					   r.id_status,
					   r.comment,
					   s.status
				FROM orders o
				LEFT JOIN orders_routes r ON o.id = r.id_order
				LEFT JOIN orders_status s ON r.id_status = s.id
				WHERE o.id = '.$order_id;
        $items = $this->get_assoc_array($sql);
        return $items;
    }

	public function getOrder($order_id) {
		$sql = 'SELECT o.id,
					   o.id_user,
					   o.id_address,
					   o.address_new,
					   o.id_car,
					   o.id_courier,
					   o.ready,
					   o.target,
					   o.date,
					   o.cost,
					   o.comment,
					   o.dk,
					   o.`from`,
					   o.from_appart,
					   o.from_coord,
					   o.from_fio,
					   o.from_phone,
					   o.from_comment,
					   u.name,
					   u.title,
					   u.phone,
					   cc.fio fio_car,
					   cc.car_number,
					   c.fio fio_courier
				FROM orders o
				LEFT JOIN cars_couriers cc ON cc.id = o.id_car
				LEFT JOIN couriers c ON c.id = o.id_car
				LEFT JOIN users u ON o.id_user = u.id
				WHERE o.id = '.$order_id;
		$this->query ( $sql );
		$items = array ();
		// один заказ
		while ( ($row = $this->fetchRowA ()) !== false ) {
			$row['date'] = $this->dateToRuFormat($row['date']);
			$items = $row;
		}
		return $items;
	}

	public function getChatIdByOrderRoute($order_route_id) {
		$sql = "SELECT 
                    u.phone_mess,
                    case u.send_sms
                        when '1' then u.phone
                        when '0' then ''
                    end as phone,
                    u.email,
                    u.viber_id
				FROM orders o
				LEFT JOIN orders_routes r ON o.id = r.id_order
				LEFT JOIN users u ON o.id_user = u.id
				WHERE r.id = ".$order_route_id;
		$row = $this->get_assoc_array($sql);
		return array($row[0]['phone_mess'],$row[0]['phone'],$row[0]['email'],$row[0]['viber_id']);
	}

    public function getChatIdByOrder($order_id) {
        $sql = "SELECT 
                    u.phone_mess,
                    case u.send_sms
                        when '1' then u.phone
                        when '0' then ''
                    end as phone,
                    u.email,
                    u.viber_id
				FROM orders o
				LEFT JOIN users u ON o.id_user = u.id
				WHERE o.id = ".$order_id;
        $row = $this->get_assoc_array($sql);
        return array($row[0]['phone_mess'],$row[0]['phone'],$row[0]['email'],$row[0]['viber_id']);
    }

	public function getChatIdByCourier($courier_id) {
		$sql = "SELECT c.telegram
				FROM couriers c
				WHERE c.id = $courier_id";
		$row = $this->get_assoc_array($sql);
		return $row[0]['telegram'];
	}

    public function getChatIdByCarCourier($courier_id) {
        $sql = "SELECT c.telegram, c.email, c.viber
				FROM cars_couriers c
				WHERE c.id = $courier_id";
        $row = $this->get_assoc_array($sql);
        return array($row[0]['telegram'], $row[0]['email'], $row[0]['viber']);
    }

    public function getStatusName($status_id) {
        $sql = 'SELECT s.status
				FROM orders_status s
				WHERE s.id = '.$status_id;
        $row = $this->get_assoc_array($sql);
        return $row[0]['status'];
    }

    public function getOrdersListExcel($from, $to, $user_id = 0) {
        $sql = 'SELECT o.id, 
                       o.date, ';
		if ($user_id == 0) {
			$sql .= '   r.to_time_ready, ';
			$sql .= '   r.to_time_ready_end, ';
		}
	    $sql .= '      r.to_time,
                       r.to_time_end,
                       (CASE 
					        WHEN o.id_address = 0 THEN o.address_new
					        ELSE a.address
					    END) AS `from`,
                       u.title,
                       concat(r.`to`, \', \', r.to_house, \'-\',/* r.to_corpus, \'-\',*/ r.to_appart) `to`,
                       r.to_phone,
                       r.to_fio,
                       s.status,
					   cc.fio fio_car,
					   r.cost_route,
					  /* r.cost_tovar,*/
					   (CASE 
					        WHEN r.pay_type = 1 THEN r.cost_route /* Если стоит полата доставки в магазине , то стоимость доставки - сумму которую забрал курьер в магазине  должна быть в выгрузке в графе инкассация!  */
					      /*  WHEN r.pay_type = 1 and r.cost_tovar = 0 THEN r.cost_route */
					        WHEN r.pay_type = 2 THEN r.cost_route + r.cost_tovar
					        ELSE r.cost_tovar
					    END) AS inkass,
					/*   (r.cost_route + r.cost_tovar + (r.cost_tovar)*(u.inkass_proc/100)) inkass,*/
					   ((r.cost_tovar)*(u.inkass_proc/100)) inkas_proc,';
		if ($user_id == 0) {
			$sql .= '   r.cost_car money_car,
				    (r.cost_route - r.cost_car) money_comp,';
		}
	    $sql .= '   o.comment
                  FROM orders o
                LEFT JOIN orders_routes r ON r.id_order = o.id
				LEFT JOIN orders_status s ON s.id = r.id_status
                LEFT JOIN users_address a ON a.id = o.id_address
				LEFT JOIN cars_couriers cc ON cc.id = o.id_car
				LEFT JOIN couriers c ON c.id = o.id_car
                LEFT JOIN users u ON u.id = o.id_user
                  WHERE o.date BETWEEN \''.$this->dmy_to_mydate($from).'\' AND \''.$this->dmy_to_mydate($to).' 23:59:59\'
                  AND (o.id_user = \''.$user_id.'\' or \''.$user_id.'\' = 0)
                  and o.isBan = 0
                ORDER BY o.date, r.to_time_ready, o.id desc';
        $orders = $this->get_assoc_array($sql);
        $result_orders = array();
        foreach ($orders as $key => $order) {
            $order['to_time'] = $order['to_time'].'-'.$order['to_time_end'];
            if (isset($order['to_time_ready'])) {
                $order['to_time_ready'] = $order['to_time_ready'] . '-' . $order['to_time_ready_end'];
                unset($order['to_time_ready_end']);
            }
            unset($order['to_time_end']);
            $result_orders[] = $order;
        }
        return $result_orders;
    }
    /*
	public function getOrdersList($from, $to) {
		$sql = 'SELECT o.id, o.comment, o.cost, o.ready, a.address `from`, a.comment addr_comment,
					   u.name, u.title, o.dk, o.id_user,
					   o.id_courier, o.id_car,
					   cc.fio fio_car,
					   cc.car_number,
					   c.fio fio_courier
                  FROM orders o
                LEFT JOIN users_address a ON a.id = o.id_address
				LEFT JOIN cars_couriers cc ON cc.id = o.id_car
				LEFT JOIN couriers c ON c.id = o.id_car
                LEFT JOIN users u ON u.id = o.id_user
                  WHERE o.date BETWEEN \''.$this->dmy_to_mydate($from).'\' AND \''.$this->dmy_to_mydate($to).' 23:59:59\'
                  and o.isBan = 0
                ORDER BY o.id desc
                LIMIT 0,1000';
		$orders = $this->get_assoc_array($sql);
		foreach ($orders as $key => $order) {
			$route = $this->getRoutes($order['id']);
			$orders[$key]['route'] = $route;
		}
		return $orders;
	}
*/
	public function getLogistList($from, $to, $user_id = 0) {
		$sql = 'SELECT o.id, 
                      (CASE 
					        WHEN o.id_address = 0 THEN o.`from`
					        ELSE ua.address
					    END) AS address,
                       ua.comment addr_comment, 
                       u.name,
                       u.title, 
                       o.ready,
                       o.date, 
                       o.comment, 
                       u.inkass_proc, 
                       o.id_car,
					   cc.fio fio_car,
					   cc.car_number,
					   c.fio fio_courier,
					   o.car_accept
			  FROM orders o
			  LEFT JOIN users_address ua ON o.id_address = ua.id
			  LEFT JOIN cars_couriers cc ON cc.id = o.id_car
			  LEFT JOIN couriers c ON c.id = o.id_car
			  LEFT JOIN users u ON u.id = o.id_user
                  WHERE o.date BETWEEN \''.$this->dmy_to_mydate($from).'\' AND \''.$this->dmy_to_mydate($to).' 23:59:59\'
                  AND (o.id_user = \''.$user_id.'\' or \''.$user_id.'\' = 0)
                ';
		$orders = $this->get_assoc_array($sql);
		foreach ($orders as $key => $order) {
			$route = $this->getRoutes($order['id']);
			$orders[$key]['route'] = $route;
		}
		return $orders;
	}
	
	function dateToRuFormat($date) {
		$date = $this->mydate_to_dmy( $date );
		setlocale(LC_ALL, 'ru_RU.CP1251', 'rus_RUS.CP1251', 'Russian_Russia.1251');
//		$date = strftime("%a, %d.%m.%Y", strtotime($date));
		$date = strftime("%d.%m.%Y", strtotime($date));
		return iconv('windows-1251','UTF-8', $date);
	}
	


	public function orderInsert($id_user, $params) {
		$sql = "
		INSERT INTO orders (id_user, ready, target, `date`, comment, id_address, address_new, dk,
		`from`,from_coord,from_appart,from_fio,from_phone,from_comment)
		VALUES ($id_user,'".$params['ready']."','".$params['target']."','".$this->dmy_to_mydate($params['date'])."','".$params['order_comment']."','".$params['store_id']."','".$params['store_new']."',NOW(),
		'".$params['from'][0]."','".$params['from_coord'][0]."','".$params['from_appart'][0]."',
		'".$params['from_fio'][0]."','".$params['from_phone'][0]."','".$params['from_comment'][0]."');
		";
		$this->query($sql);

		$order_id = $this->insertID();
		$this->update_routes($order_id,$params);

		return $order_id;
	}

	public function orderUpdate($params) {
		$sql = "
		UPDATE orders SET 
		`id_user` = '".$params['id_user']."',
		`ready` = '".$params['ready']."',
		`target` = '".$params['target']."',
		`date` = '".$this->dmy_to_mydate($params['date'])."',
		`id_address` = '".$params['store_id']."',
		`address_new` = '".$params['store_new']."',
		`comment` = '".$params['order_comment']."',
		`id_car` = '".$params['car_courier']."',
		`from` = '".$params['from'][0]."',
		`from_coord` = '".$params['from_coord'][0]."',
		`from_appart` = '".$params['from_appart'][0]."',
		`from_fio` = '".$params['from_fio'][0]."',
		`from_phone` = '".$params['from_phone'][0]."',
		`from_comment` = '".$params['from_comment'][0]."',
		`dk` = NOW()
		WHERE id = ".$params['order_id']."
		";
		$this->query($sql);

		$this->update_routes($params['order_id'],$params);

		return $params['order_id'];
	}

	public function updOrderStatus($user_id, $order_route_id, $new_status, $stat_comment){
		$sql = "UPDATE orders_routes SET id_status = $new_status, `dk` = NOW()	WHERE id = $order_route_id ";
		$this->query($sql);

		$sql = "INSERT INTO order_status_history (user_id, order_route_id, new_status, comment, dk)
				VALUES ($user_id, $order_route_id, $new_status, '$stat_comment', NOW())";
		$this->query($sql);
	}

	public function updOrderRouteCourier($user_id, $order_id, $new_courier, $new_car_courier, $courier_comment){
//		$sql = "UPDATE orders SET id_courier = $new_courier, id_car = $new_car_courier, `dk` = NOW() WHERE id = ".$order_id." ";
//		$this->query($sql);

		$sql = "INSERT INTO order_courier_history (user_id, order_route_id, new_courier, new_car, comment, dk)
				VALUES ($user_id, $order_id, $new_courier, $new_car_courier, '$courier_comment', NOW())";
		$this->query($sql);

        // При назначении курьера, статус заказа должен меняться на исполняется
        $sql = "SELECT id FROM orders_routes WHERE id_order = '$order_id'";
        $routes = $this->get_assoc_array($sql);

        foreach ($routes as $route){
            $this->updOrderStatus($user_id, $route['id'], '3', 'Назначен курьер');
        }

	}

	public function update_routes($order_id,$params){
		if (is_array($params ['to'])) {
			$sql = 'DELETE FROM orders_routes WHERE id_order = '.$order_id.';';
			$this->query ( $sql );
            $sql_values = '';

			foreach ($params ['to'] as $key => $item) {
			    // Если к точному времени, то время ПО = времени С
                if ($params['target'] == 1){
                    $params ['to_time_end'][$key] = $params ['to_time'][$key];
                }
                $params ['status'][$key] = $params ['status'][$key] == 0 ? 1 : $params ['status'][$key];
                // Если сбросили курьера, но не отмена
                $params ['status'][$key] = ($params['car_courier'] == 0 and $params ['status'][$key] != 5) ? 1 : $params ['status'][$key];
				$sql_values .= ($key > 0)?',':'';
                $sql_values .= ' (\''.$order_id.'\',\''.$params ['to'][$key].'\',\''.$params ['to_region'][$key].'\',\''.$params ['to_AOGUID'][$key].'\',
                            \''.$params ['to_coord'][$key].'\',\''.$params ['to_house'][$key].'\',\''.$params ['to_corpus'][$key].'\',
							\''.$params ['to_appart'][$key].'\',\''.$params ['to_fio'][$key].'\',\''.$params ['to_phone'][$key].'\',
							\''.$params ['cost_route'][$key].'\',\''.$params ['cost_tovar'][$key].'\',\''.$params ['cost_car'][$key].'\',
							\''.$params ['to_time'][$key].'\',\''.$params ['to_time_end'][$key].'\',\''.$params ['comment'][$key].'\',
							\''.$params ['to_time_ready'][$key].'\',\''.$params ['to_time_ready_end'][$key].'\',\''.$params ['pay_type'][$key].'\',
							\''.$params ['status'][$key].'\',\''.$params ['goods_type'][$key].'\',\''.$params ['goods_val'][$key].'\'	)';
			}
			if ($sql_values != '') {
                $sql = "INSERT INTO orders_routes (id_order,`to`,`to_region`,`to_AOGUID`,`to_coord`,`to_house`,`to_corpus`,`to_appart`,`to_fio`,`to_phone`,`cost_route`,`cost_tovar`,`cost_car`,`to_time`,`to_time_end`,`comment`, `to_time_ready`, `to_time_ready_end`, `pay_type`, `id_status`, goods_type, goods_val) VALUES $sql_values";
                $this->query($sql);
            }
		}
	}

	public function orderBan($id) {
		$sql = "UPDATE `orders`
				SET `isBan` = 1
                WHERE `id` = $id";
		return $this->query ( $sql);
	}

	public function saveSMSlog($phone, $sms_id, $sms_status_code, $sms_status_text, $sms_json){
	    $sql = "INSERT INTO log_sms_send (sms_phone, sms_id, status_text, status_code, desc_json, dk, sms_type) VALUES ('$phone', '$sms_id','$sms_status_code','$sms_status_text','$sms_json',NOW(), 'mes')";
	    $this->query($sql);
    }

    public function createUser($name, $phone, $desc){
        $user_id = 0;
        $sql = "INSERT INTO users (name, email, login, pass, date_reg, isban, prior, title, phone, phone_mess, fixprice_inside, inkass_proc, pay_type, sms_id, `desc`, send_sms) 
                VALUES ('$name','','$phone','',NOW(),'0','0','$name','$phone','','','','','','$desc', 1)";
        $test = $this->query($sql);
        if ($test) {
            $user_id = $this->insertID();
            $sql = "INSERT INTO `groups_user` (`group_id`, `user_id`) VALUES ('2', '$user_id')";
            $this->query($sql);
        }
        return $user_id;
    }

function mydate_to_dmy($date) {
	return date ( 'd.m.Y', strtotime ( substr ( $date, 0, 20 ) ) );
}

function dmy_to_mydate($date) {
	return date ( 'Y-m-d', strtotime (  $date ) );
}

public function getNewsList($limCount) {
      $page = 1;
      $limStart = ($page - 1) * $limCount;      
      $sql = 'SELECT n.*, DATE_FORMAT(`time`, \'%%d.%%m.%%Y\') as time, 
			    	(SELECT COUNT(*) FROM news) as news_count
			     FROM news n
				ORDER BY n.`time` DESC';
      if ($limCount > 0) $sql.= ' LIMIT '.$limStart.','.$limCount;
      $this->query($sql);
      $collect = Array();
      while($row = $this->fetchRowA()) {      	
      	$collect[]=$row;
      }      
      return $collect;
    }

function GetInTranslit($string) {
	$replace=array(
			"'"=>"",
			"`"=>"",
			"а"=>"a","А"=>"a",
			"б"=>"b","Б"=>"b",
			"в"=>"v","В"=>"v",
			"г"=>"g","Г"=>"g",
			"д"=>"d","Д"=>"d",
			"е"=>"e","Е"=>"e",
			"ж"=>"zh","Ж"=>"zh",
			"з"=>"z","З"=>"z",
			"и"=>"i","И"=>"i",
			"й"=>"y","Й"=>"y",
			"к"=>"k","К"=>"k",
			"л"=>"l","Л"=>"l",
			"м"=>"m","М"=>"m",
			"н"=>"n","Н"=>"n",
			"о"=>"o","О"=>"o",
			"п"=>"p","П"=>"p",
			"р"=>"r","Р"=>"r",
			"с"=>"s","С"=>"s",
			"т"=>"t","Т"=>"t",
			"у"=>"u","У"=>"u",
			"ф"=>"f","Ф"=>"f",
			"х"=>"kh","Х"=>"kh",
			"ц"=>"tc","Ц"=>"tc",
			"ч"=>"ch","Ч"=>"ch",
			"ш"=>"sh","Ш"=>"sh",
			"щ"=>"shch","Щ"=>"shch",
			"ъ"=>"","Ъ"=>"",
			"ы"=>"y","Ы"=>"y",
			"ь"=>"","Ь"=>"",
			"э"=>"e","Э"=>"e",
			"ю"=>"iu","Ю"=>"iu",
			"я"=>"ia","Я"=>"ia",
			"і"=>"i","І"=>"i",
			"ї"=>"yi","Ї"=>"yi",
			"є"=>"e","Є"=>"e"
	);
	return $str=iconv("UTF-8","UTF-8//IGNORE",strtr($string,$replace));
}

}

class ordersProcess extends module_process {
	public function __construct($modName) {
		global $values, $User, $LOG, $System;
		parent::__construct ( $modName );
		$this->Vals = $values;
		$this->System = $System;
		$this->modName = $modName;
		$this->User = $User;
		$this->Log = $LOG;
		$this->action = false;
		/*
		 * actionDefault - Действие по умолчанию. Должно браться из БД!!!
		 */
		$this->actionDefault = '';
		$this->actionsColl = new actionColl ();
		$this->nModel = new ordersModel ( $modName );
		$sysMod = $this->nModel->getSysMod ();
		$this->sysMod = $sysMod;
		$this->mod_id = $sysMod->id;
		$this->nView = new ordersView ( $this->modName, $this->sysMod );
		$this->regAction ( 'view', 'Главная страница заказов', ACTION_GROUP );
		$this->regAction ( 'LogistList', 'Для логистов', ACTION_GROUP );
		$this->regAction ( 'order', 'Заявка', ACTION_GROUP );
		$this->regAction ( 'naklad', 'Накладная', ACTION_GROUP );
		$this->regAction ( 'orderUpdate', 'Редактирование заявки', ACTION_GROUP );
		$this->regAction ( 'orderBan', 'Удаление заявки', ACTION_GROUP );
		$this->regAction ( 'chg_status', 'Изменение статуса заявки', ACTION_GROUP );
		$this->regAction ( 'chg_courier', 'Изменение курьера', ACTION_GROUP );
		$this->regAction ( 'get_data', 'Получение интерактивных данных', ACTION_GROUP );
		$this->regAction ( 'excel', 'Экспорт в Excel', ACTION_GROUP );

		if (DEBUG == 0) {
			$this->registerActions ( 1 );
		}
		if (DEBUG == 1) {
			$this->registerActions ( 0 );
		}
	}
	
	
	public function update($_action = false) {
		if ($_action)
			$this->action = $_action;
		if ($this->action)
			$action = $this->action;
		else
			$action = $this->checkAction ();
		if (! $action) {
			$this->Vals->URLparams ( $this->sysMod->defQueryString );
			$action = $this->actionDefault;
		}
		$user_id = $this->User->getUserID ();
        $group_id = $this->User->getUserGroup();

		if ($user_id > 0) {
			$this->User->nView->viewLoginParams ( 'Доставка', '', $user_id, array (), array (), $this->User->getRight ( 'admin', 'view' ) );
		}

        $user_right = $this->User->getRight ( $this->modName, $action );
        if ($user_right == 0 && ! $_action) {
            $this->User->nView->viewLoginParams ( '', '', $user_id, array (), array () );
            $this->nView->viewMessage ( 'У вас нет прав на работу с этим модулем.','' );
            $this->updated = true;
            return;
        }


		if ($action == 'get_data'){
			$type_data = $this->Vals->getVal ( 'get_data', 'GET', 'string' );
			if ($type_data == 'spbStreets'){
				$items = $this->nModel->getSpbStreets();
				echo json_encode($items);
			}
            if ($type_data == 'userStores'){
                $user_id = $this->Vals->getVal ( 'user_id', 'POST', 'string' );
                $stores = $this->nModel->getStores($user_id);
                $opt = '';
                foreach ($stores as $store){
                    $opt .= '<option value="'.$store['id'].'">'.$store['address'].'</option>';
                }
                $opt .= '<option value="0" style="color:maroon;"></option>';
                $items = $this->nModel->getUserParams($user_id);
                $items['opts'] = $opt;
                echo json_encode($items);
            }
            exit();
		}

        if ($action == 'excel'){
            $sub_action = $this->Vals->getVal ( 'sub_action', 'POST', 'string' );
            $logist = $this->Vals->getVal ( 'logist', 'GET', 'string' );
            list($from, $to) = $this->get_post_date('all');
            if ($sub_action == 'excel') {
                if ($logist == 1) {
                    $titles = array('номер заказа', 'дата', 'время готовности', 'время доставки', 'адрес приема', 'компания', 'адрес доставки', 'телефон', 'ФИО получателя', 'статус заказа', 'курьер', 'стоимость доставки', /*'стоимость цветов',*/ 'инкассация', '% инкас.', 'заработок курьера', 'заработок компании', 'примечания');
                    $orders = $this->nModel->getOrdersListExcel($from, $to);
                }else{
                    $titles = array('номер заказа', 'дата', 'время доставки', 'адрес приема', 'компания', 'адрес доставки', 'телефон', 'ФИО получателя', 'статус заказа', 'курьер', 'стоимость доставки', /*'стоимость цветов',*/ 'инкассация', '% инкас.', 'примечания');
                    $orders = $this->nModel->getOrdersListExcel($from, $to, $user_id);
                }
                $this->nModel->exportToExcel($titles,$orders);
            }
            $this->nView->viewExcelDialog($from, $to, $logist);
        }
		
		if ($action == 'order') {
			$order_id = $this->Vals->getVal ( 'order', 'GET', 'integer' );
			$is_single = $this->Vals->getVal ( 'single', 'GET', 'integer' );
			$without_menu = $this->Vals->getVal ( 'without_menu', 'GET', 'integer' );
			$order = $this->nModel->getOrder($order_id);
            $uid = isset($order['id_user'])?$order['id_user']:$user_id;
			$routes = $this->nModel->getRoutes($order_id);
			$pay_types = $this->nModel->getPayTypes();
            $statuses = $this->nModel->getStatuses();
            $car_couriers = $this->nModel->getCarCouriers();
			$users = $this->nModel->getUsers($uid);
            $userData = $this->nModel->getUserParams($uid);
            $user_pay_type = $userData['pay_type'];
            $user_fix_price = $userData['fixprice'];
            $user_max_price = $userData['maxprice'];
			$prices = $this->nModel->getPrices();
			$timer = $this->getTimeForSelect();
            $add_prices = $this->nModel->getAddPrices();
            $times = $this->nModel->getTimeCheckList();
            list($g_price, $goods) = $this->nModel->getGoodsPriceList();
			$stores = $this->nModel->getStores($uid);
			$client_title = $this->nModel->getClientTitle($uid);
			$this->nView->viewOrderEdit ( $user_id, $order, $users, $stores, $routes, $pay_types, $statuses,
                $car_couriers, $timer, $prices, $add_prices, $client_title, $without_menu, $is_single,
                $user_pay_type, $user_fix_price, $user_max_price, $times, $g_price, $goods );
		}
		if ($action == 'naklad') {
            $this->Vals->setVals(array('without_menu' => 1));
			$order_id = $this->Vals->getVal ( 'naklad', 'GET', 'integer' );
			$order = $this->nModel->getOrder($order_id);
            $uid = isset($order['id_user'])?$order['id_user']:$user_id;
			$routes = $this->nModel->getRoutes($order_id);
			$client = $this->nModel->getClientData($uid, $order['id_address']);
			$this->nView->viewNaklad ( $order, isset($client[0])?$client[0]:array(), $routes );

			$PageAjax = new PageForAjax ( $this->modName, $this->modName, $this->modName, 'page.print.xsl' );
            $isAjax = $this->Vals->getVal ( 'ajax', 'INDEX' );
            $PageAjax->addToPageAttr ( 'isAjax', $isAjax );
            $html = $PageAjax->getBodyAjax2 ( $this->nView );
            sendData ( $html );
		}

		if ($action == 'orderBan') {
			$order_id = $this->Vals->getVal ( 'orderBan', 'GET', 'integer' );
			$this->nModel->orderBan($order_id);
			$this->nView->viewMessage('Заказ успешно удален.', 'Сообщение');
			header ( "Location:/orders/" );
		}

		if ($action == 'orderUpdate') {
			$params['order_id'] = $this->Vals->getVal ( 'order_id', 'POST', 'integer' );
			$params['id_user'] = $this->Vals->getVal ( 'id_user', 'POST', 'integer' );
			$params['store_id'] = $this->Vals->getVal ( 'store_id', 'POST', 'integer' );
			$params['store_new'] = $this->Vals->getVal ( 'store_new', 'POST', 'integer' );
			$params['date'] = $this->Vals->getVal ( 'date', 'POST', 'string' );
			$params['ready'] = $this->Vals->getVal ( 'ready', 'POST', 'string' );
			$params['target'] = $this->Vals->getVal ( 'target', 'POST', 'string' );
			$params['order_comment'] = $this->Vals->getVal ( 'order_comment', 'POST', 'string' );

            $user_name = $this->Vals->getVal('user_name', 'POST', 'string');
            $user_phone = $this->Vals->getVal('user_phone', 'POST', 'string');

            $params['from'] = $this->Vals->getVal('from', 'POST', 'array');
            $params['from_coord'] = $this->Vals->getVal('from_coord', 'POST', 'array');
            $params['from_appart'] = $this->Vals->getVal('from_appart', 'POST', 'array');
            $params['from_fio'] = $this->Vals->getVal('from_fio', 'POST', 'array');
            $params['from_phone'] = $this->Vals->getVal('from_phone', 'POST', 'array');
            $params['from_comment'] = $this->Vals->getVal('from_comment', 'POST', 'array');

			$params['to'] = $this->Vals->getVal ( 'to', 'POST', 'array' );
			$params['to_region'] = $this->Vals->getVal ( 'to_region', 'POST', 'array' );
			$params['to_coord'] = $this->Vals->getVal ( 'to_coord', 'POST', 'array' );
			$params['to_AOGUID'] = $this->Vals->getVal ( 'to_AOGUID', 'POST', 'array' );
			$params['to_time_ready'] = $this->Vals->getVal ( 'to_time_ready', 'POST', 'array' );
			$params['to_time_ready_end'] = $this->Vals->getVal ( 'to_time_ready_end', 'POST', 'array' );
			$params['to_house'] = $this->Vals->getVal ( 'to_house', 'POST', 'array' );
			$params['to_corpus'] = $this->Vals->getVal ( 'to_corpus', 'POST', 'array' );
			$params['to_appart'] = $this->Vals->getVal ( 'to_appart', 'POST', 'array' );
			$params['to_fio'] = $this->Vals->getVal ( 'to_fio', 'POST', 'array' );
			$params['to_phone'] = $this->Vals->getVal ( 'to_phone', 'POST', 'array' );
			$params['to_time'] = $this->Vals->getVal ( 'to_time', 'POST', 'array' );
			$params['to_time_end'] = $this->Vals->getVal ( 'to_time_end', 'POST', 'array' );

			$params['cost_route'] = $this->Vals->getVal ( 'cost_route', 'POST', 'array' );
			$params['cost_tovar'] = $this->Vals->getVal ( 'cost_tovar', 'POST', 'array' );
			$params['cost_car'] = $this->Vals->getVal ( 'cost_car', 'POST', 'array' );
			$params['goods_type'] = $this->Vals->getVal ( 'goods_type', 'POST', 'array' );
			$params['goods_val'] = $this->Vals->getVal ( 'goods_val', 'POST', 'array' );
			$params['pay_type'] = $this->Vals->getVal ( 'pay_type', 'POST', 'array' );
			$params['comment'] = $this->Vals->getVal ( 'comment', 'POST', 'array' );
			$params['status'] = $this->Vals->getVal ( 'status', 'POST', 'array' );
			$params['car_courier'] = $this->Vals->getVal ( 'car_courier', 'POST', 'integer' );

            $dontsend_message = false;
            $send_message_to_client = false;
            $message_add_text = "";

            // Проверка, что пришли из заказа (с набором данных)
            if ($params['date'] != '') {
                if ($params['order_id'] > 0) {
                    if ($group_id != 2) {
                        $user_id = $this->Vals->getVal('new_user_id', 'POST', 'integer');
                        if ($user_id > 0) {
                            $params['id_user'] = $user_id;
                        }
                    }
                    $order_info = $this->nModel->getOrderInfo($params['order_id']);
                    $order_routes_info = $this->nModel->getOrderRoutesInfo($params['order_id']);
                    foreach ($params['status'] as $key => $route_statuses) {
                        $now_status = $order_routes_info[$key]['id_status'];
                        // Не отправляем сообщений, если новый статус равен предыдущему
                        if ($now_status == $route_statuses) {
                            $dontsend_message = true;
                        }
                    }
                    $order_id = $this->nModel->orderUpdate($params);
                    $send_message_to_client = false;
                } else {
                    if ($group_id != 2) {
                        $new_user_id =$this->Vals->getVal('new_user_id', 'POST', 'integer');
                        if ($user_name != '' and $user_phone != ''){
                            $user_phone = $this->nModel->formatPhoneNumber8($user_phone);
                            if (!($this->nModel->getUserId($user_phone) > 0)) {
                                $this->nModel->createUser($user_name, $user_phone, 'Регистрация менеджером через новый заказ');
                            }
                            /*
                            $user_id = $this->nModel->getUserId($user_phone);
                            $user_id = ($user_id > 0) ? $user_id : $this->nModel->createUser($user_name, $user_phone, 'Регистрация менеджером через новый заказ');
                            */
//                        }else {
                        }
                        $user_id = $new_user_id > 0 ? $new_user_id : $user_id;
                    }
                    if ($user_id > 0) {
                        $order_id = $this->nModel->orderInsert($user_id, $params);
                        $order_info = $this->nModel->getOrderInfo($order_id);
                        $message_add_text = "Заказ принят, ожидайте курьера.";
                        $send_message_to_client = true;
                    }
                }


                // Если статус больше статуса в исполнении
                if (isset($params['status']) and is_array($params['status'])) {
                    foreach ($params['status'] as $route_statuses) {
                        if ($route_statuses > 3) {
                            $send_message_to_client = true;
                        }
                    }
                }

                if ($send_message_to_client and !$dontsend_message and isset($order_id)) {
                    $message = $this->getOrderTextInfo($order_id);
                    $message .= $message_add_text;

                    $this->telegram($message, '243045100'); // Отправка нового заказа Админу
                    $this->telegram($message, '196962258');
                    $this->telegram($message, '379575863');

                    $this->viber($message, 'df+c6B5JkuIet0qr9uTnrg==');
                    $this->viber($message, 'QxA+XapnH/sgMDfOki5emA==');
                    $this->viber($message, '8fZTnZ52qp+odd7YUtMdVg==');


                    list($chat_id, $phone, $email, $viber) = $this->nModel->getChatIdByOrder($order_id);
                    if (isset($chat_id) and $chat_id != '') {
                        $this->telegram($message, $chat_id);
                    }
                    if (isset($viber) and $viber != '') {
                        $this->viber($message, $viber);
                    }
                    if (isset($phone) and $phone != '') {
                        $this->send_sms($phone, $this->getOrderTextSMS($order_id, $params['status'][0]));
                    }
                    if (isset($email) and $email != '') {
                        $this->send_email($email, $message);
                    }
                }

                //отправка сообщения курьеру
                if (isset($order_info) and isset($order_id) and $params['car_courier'] > 0 and $order_info['id_car'] != $params['car_courier']) {
                    $this->saveCourier($user_id, $order_id, $params['car_courier']);
                }

                $this->nView->viewMessage('Заказ успешно сохранен.' . (isset($order_id) ? ' Номер для отслеживания: ' . $order_id : ''), 'Сообщение');
            }else{
                $this->nView->viewMessage('Пожалуйста подождите...', 'Сообщение');
            }
            $this->updated = true;
		}

		if ($action == 'chg_status'){
            $order_id = $this->Vals->getVal ( 'order_id', 'POST', 'integer' );
            $order_route_id = $this->Vals->getVal ( 'order_route_id', 'POST', 'integer' );
			$new_status = $this->Vals->getVal ( 'new_status', 'POST', 'integer' );
			$stat_comment = $this->Vals->getVal ( 'stat_comment', 'POST', 'string' );
			$order_info_message = $this->Vals->getVal ( 'order_info_message', 'POST', 'string' );
			if ($order_info_message == '') {
                $order_info_message = $this->getOrderTextInfo($order_id);
            }
            if ($order_route_id == ''){
                $order_routes = $this->nModel->getOrderRoutesInfo($order_id);
                $order_route_id = $order_routes[0]['id'];
            }
            /*if ($new_status == 5){
                $this->nModel->updOrderStatus($user_id, $order_route_id, $new_status, $stat_comment);
            }else*/
            if ($new_status > 0){
				$this->nModel->updOrderStatus($user_id, $order_route_id, $new_status, $stat_comment);
				$result = 'Статус успешно изменен. ';
				$status_name = $this->nModel->getStatusName($new_status);

                $message = $order_info_message."\r\n";
                $message .= '<b>Статус вашего заказа:</b> '.$status_name.''."\r\n";
                if (trim($stat_comment) != '') {
                    $message .= '<b>Сообщение с сайта:</b> ' . $stat_comment . '';
                }

                list($chat_id, $phone, $email, $viber) = $this->nModel->getChatIdByOrderRoute($order_route_id);
                if (isset($chat_id) and $chat_id != '') {
                    $result .= ' Сообщение в Телеграм клиенту отправлено.';
                    $this->telegram($message, $chat_id);
                }
                if (isset($viber) and $viber != '') {
                    $result .= ' Сообщение в Viber клиенту отправлено.';
                    $this->viber($message, $viber);
                }
                if (isset($phone) and $phone != '') {
                    $this->send_sms($phone, $this->getOrderTextSMS($order_id, $new_status));
                }
                if (isset($email) and $email != '') {
                    $this->send_email($email, $message);
                }
				echo $result;
			}else {
				$order_route = $this->nModel->getOrderRoute($order_route_id);
				$statuses = $this->nModel->getStatuses();
				$select = "<select class='form-control' name='new_status' >";
				foreach ($statuses as $status) {
					$selected = ($order_route['id_status'] == $status['id']) ? 'selected=""' : '';
					$select .= "<option value='" . $status['id'] . "' $selected>" . $status['status'] . "</option>";
				}
				$select .= "</select>";
				$comment = "<textarea class='form-control' name='comment_status' placeholder='Комментарий для клиента'></textarea>";
				$info = "<div class='alert alert-success'>".str_replace("\r\n","<br/>",$order_info_message)."</div>";
				$info .= "<div class='alert alert-info'>Выберите новый статус и введите комментарий для клиента.</div>
						<input type='hidden' name='order_route_id' value='$order_route_id' />
						<input type='hidden' name='order_info_message' value='$order_info_message' />";
				echo $info . "<br/>" . $select . "<br/>" . $comment;
			}
			exit();
		}

		if ($action == 'chg_courier'){
			$order_id = $this->Vals->getVal ( 'order_id', 'POST', 'integer' );
			$new_courier = $this->Vals->getVal ( 'new_courier', 'POST', 'integer' );
			$new_car_courier = $this->Vals->getVal ( 'new_car_courier', 'POST', 'integer' );
            $courier_comment = $this->Vals->getVal ( 'courier_comment', 'POST', 'string' );
            $order_info_message = $this->Vals->getVal ( 'order_info_message', 'POST', 'string' );
            if ($order_info_message == '') {
                $order_info_message = $this->getOrderTextInfo($order_id);
            }
			if ($new_courier > 0 and $new_car_courier > 0){
				$this->nModel->updOrderRouteCourier($user_id, $order_id, $new_courier, $new_car_courier, $courier_comment);
				$result = 'Курьер успешно назначен.';
//                $chat_id= ($new_courier == 1)?$this->nModel->getChatIdByCarCourier($new_car_courier):$this->nModel->getChatIdByCourier($new_courier);
                list($chat_id, $email, $viber)= $this->nModel->getChatIdByCarCourier($new_car_courier);
                if (isset($chat_id) and $chat_id != '') {
                    $result .= ' Сообщение курьеру отправлено.';
                    $message = '<i>Вы назначены на заказ</i>'."\r\n";
                    $message .= $order_info_message."\r\n";
                    if (trim($courier_comment) != '') {
                        $message .= 'Сообщение с сайта: ' . $courier_comment . '';
                    }
                    $menu = array('inline_keyboard' => array(
                        array(
                            array(
                                'text' => 'принять заказ', 'callback_data' => '/order_accepted_'.$order_id,
                            ),
                        ),
                    ),
                    );
                    $this->telegram($message, $chat_id, $menu);
                }
                if (isset($email) and $email != '') {
                    $result .= ' Сообщение на почту курьеру отправлено.';
                    $message = '<i>Вы назначены на заказ</i>'."\r\n";
                    $message .= $order_info_message."\r\n";
                    if (trim($courier_comment) != '') {
                        $message .= 'Сообщение с сайта: ' . $courier_comment . '';
                    }
                    $this->send_email($email, $message, 'Уведомление курьеру Pochta911.ru');
                }
                if (isset($viber) and $viber != '') {
                    $result .= ' Сообщение в Viber курьеру отправлено.';
                    $message = '<i>Вы назначены на заказ</i>'."\r\n";
                    $message .= $order_info_message."\r\n";
                    if (trim($courier_comment) != '') {
                        $message .= 'Сообщение с сайта: ' . $courier_comment . '';
                    }
                    $this->viber($message, $viber, $order_id);
                }
				echo $result;
			}else {
				$order = $this->nModel->getOrder($order_id);
				$car_couriers = $this->nModel->getCarCouriers();
                $select = '<div class="input-group"><span class="input-group-addon">Курьер:</span>';
				$select .= "<select class='form-control' name='new_car_courier' >";
				foreach ($car_couriers as $car) {
					$selected = ($order['id_car'] == $car['id']) ? 'selected=""' : '';
					$select .= "<option value='" . $car['id'] . "' $selected>" . $car['fio'] . " (" . $car['car_number'] . ")</option>";
				}
                $select .= "</select></div>";
               /* $couriers = $this->nModel->getCouriers();
                $select2 = '<div class="input-group"><span class="input-group-addon">За рулем:</span>';
                $select2 .= "<select class='form-control' name='new_courier' >";
                foreach ($couriers as $courier) {
                    $selected = ($order['id_car'] == $courier['id']) ? 'selected=""' : '';
                    $select2 .= "<option value='" . $courier['id'] . "' $selected>" . $courier['fio'] . "</option>";
                }
				$select2 .= "</select></div>";*/
                $input2 = "<input type='hidden' name='new_courier' value='1'/>";
                $comment = "<textarea class='form-control' name='courier_comment' placeholder='Комментарий для курьера'></textarea>";
                $info = "<div class='alert alert-success' style='display: none;'>".str_replace("\r\n","<br/>",$order_info_message)."</div>";
				$info .= "<div class='alert alert-info'>Выберите курьера для данного заказа.</div>
						<input type='hidden' name='order_id' value='$order_id' />
						<input type='hidden' name='order_info_message' value='$order_info_message' />";
				echo $info . "<br/>" . $select . $input2 . $comment;
			}
			exit();
		}

/** Поиск своего заказа */		/*
		if ($action == 'search_order') {
			$order = $this->Vals->getVal ( 'order_number', 'POST', 'integer' );
//			$items = $this->nModel->SearchOrder ( $order);
			$this->nView->viewSearchOrder (  );
			
		}
*/
		if ($action == 'view') {
            if ($this->nModel->checkUserPswChgd($user_id)){
                header('location: /admin/userEdit-'.$user_id.'/add_data-2/');
                exit();
            }
		    if ($this->nModel->checkUserCard($user_id)){
		        header('location: /admin/userEdit-'.$user_id.'/add_data-1/');
                exit();
            }
            list($from, $to) = $this->get_post_date();
            $statuses = $this->nModel->getStatuses();
			$orders = $this->nModel->getLogistList($from, $to, $user_id);
			$this->nView->viewOrders ($from, $to, $orders, $statuses);
		}

		if ($action == 'LogistList') {
            list($from, $to) = $this->get_post_date();
            $statuses = $this->nModel->getStatuses();
			$orders = $this->nModel->getLogistList($from, $to);
			$this->nView->viewLogistList ($from, $to, $orders, $statuses);
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


	}

	public function getTimeForSelect(){
        $time_arr = array();
        for ($h = 7; $h <= 23; $h++) {
            if ($h < 23){
                for ($i= 0; $i <= 5; $i++){
                    $time_arr[] = substr('0'.$h,-2).':'.substr('0'.($i*10),-2);
                }
            }else{
                $time_arr[] = $h.':00';
            }
        }
        return $time_arr;
    }

    public function saveCourier($user_id,$order_id,$new_car_courier){
        $order_info_message = $this->getOrderTextInfo($order_id);
        $this->nModel->updOrderRouteCourier($user_id, $order_id, '0', $new_car_courier, 'Изменен из заказа');
        list($chat_id, $email, $viber) = $this->nModel->getChatIdByCarCourier($new_car_courier);
        if (isset($chat_id) and $chat_id != '') {
            $message = '<i>Вы назначены на заказ</i>'."\r\n\r\n";
            $message .= $order_info_message."\r\n";
            $menu = array('inline_keyboard' => array(
                array(
                    array(
                        'text' => 'принять заказ', 'callback_data' => '/order_accepted_'.$order_id,
                    ),
                ),
            ),
            );
            $this->telegram($message, $chat_id, $menu);
        }
        if (isset($email) and $email != '') {
            $message = '<i>Вы назначены на заказ:</i>'."\r\n\r\n";
            $message .= $order_info_message."\r\n";
            $this->send_email($email, $message, 'Уведомление курьеру Pochta911.ru');
        }
        if (isset($viber) and $viber != '') {
            $message = '<i>Вы назначены на заказ</i>'."\r\n";
            $message .= $order_info_message."\r\n";
            $this->viber($message, $viber, $order_id);
        }
    }

	public function getOrderTextInfo($order_id){
        $order_info = $this->nModel->getOrderInfo($order_id);
        $order_routes_info = $this->nModel->getOrderRoutesInfo($order_id);
        $order_info_message = "<b>Заказ </b> " . $order_id . "\r\n";
        $order_info_message .= "<b>Дата:</b>\r\n " . $order_info['date'] . "\r\n";
        $order_info_message .= "<b>Откуда:</b>\r\n " . $order_info['from'] . "\r\n";
        if (trim($order_info['from_phone']) != '') {
            $order_info_message .= "<b>Отправитель:</b>\r\n " . $order_info['from_fio'] . " [" . $order_info['from_phone'] . "]\r\n";
        }
        $order_info_message .= ($order_info['from_comment'] != '')?"<i>" . $order_info['from_comment'] . "</i>\r\n\r\n":'';
        $i = 0;
        foreach ($order_routes_info as $order_route_info) {
            $i++;
            if (count($order_routes_info) > 1){
                $order_info_message .= "<b>Участок № $i:</b>\r\n";
            }

            $order_info_message .= "<b>Адрес доставки:</b>\r\n " . $order_route_info['to_addr'] . "\r\n";
            $order_info_message .= ($order_route_info['comment'] != '')?" <b>Комментарий:</b> " . $order_route_info['comment'] . "\r\n":"";
            $order_info_message .= "<b>Период забора:</b>\r\n " . $order_route_info['to_time_ready'] . " - " . $order_route_info['to_time_ready_end'] . "\r\n";
            $order_info_message .= "<b>Период получения:</b>\r\n " . $order_route_info['to_time'] . " - " . $order_route_info['to_time_end'] . "\r\n";
            $order_info_message .= "<b>Получатель:</b>\r\n " . $order_route_info['to_fio'] . " [" . $order_route_info['to_phone'] . "]\r\n";
	        if ($order_route_info['pay_type'] == 1) {
		        $order_info_message .= "<b>Взять в магазине:</b>\r\n " . ($order_route_info['cost_route']) . " руб.\r\n";
	        }elseif ($order_route_info['pay_type'] == 2) {
		        $order_info_message .= "<b>Наличные у клиента:</b>\r\n " . ($order_route_info['cost_route'] + $order_route_info['cost_tovar']) . " руб.\r\n";
	        }elseif ($order_route_info['pay_type'] == 3 or $order_route_info['pay_type'] == 4 ) {
		        $order_info_message .= "<b>Наличные у клиента:</b>\r\n " . $order_route_info['cost_tovar'] . " руб. \r\n";
	        }
            if ($order_route_info['id_status'] == 4) {
                $order_info_message .= "<b>Ваш заказ доставлен получателю</b>\r\n";
            }elseif ($order_route_info['id_status'] > 1) {
                $order_info_message .= "<b>Статус:</b>\r\n " . $order_route_info['status'] . "\r\n";
            }
//            $order_info_message .= " <b>Стоимость заказа:</b> " . (+$order_route_info['cost_route'] + $order_route_info['cost_tovar']) . "\r\n ";
        }
        return $order_info_message;
    }

    public function getOrderTextSMS($order_id, $status){
        $order_info = $this->nModel->getOrderInfo($order_id);
        $order_routes_info = $this->nModel->getOrderRoutesInfo($order_id);
        $order_info_message = "Заказ № " . $order_id . "\r\n";
        if ($status == 1) {
            $order_info_message .= "Дата: " . $order_info['date'] . "\r\n";
            $order_info_message .= "Откуда: " . $order_info['from_addr'] . "\r\n";
        }
        $i = 0;
        foreach ($order_routes_info as $order_route_info) {
            $i++;
            if (count($order_routes_info) > 1){
                $order_info_message .= "Сегмент №$i:\r\n";
            }

            if ($status == 1/* or $status == 4*/) {
                $order_info_message .= "Куда: " . $order_route_info['to_addr'] . "\r\n";
            }
            if ($order_route_info['id_status'] == 1) {
                $order_info_message .= "Заказ принят, ожидайте курьера\r\n";
            } elseif ($order_route_info['id_status'] == 4) {
                $order_info_message .= "Доставлен получателю\r\n";
            } elseif ($order_route_info['id_status'] == 5) {
                $order_info_message .= "Отменен\r\n";
            } else {
                $order_info_message .= "Статус: " . $order_route_info['status'] . "\r\n";
            }
        }
        return $order_info_message;
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

    function send_sms($phone, $message){
        $smsru = new SMSRU('69da81b5-ee1e-d004-a1aa-ac83d2687954'); // Ваш уникальный программный ключ, который можно получить на главной странице

        $data = new stdClass();
        $data->to = $phone;
        $data->text = $message; // Текст сообщения
        $data->from = 'pochta911ru'; // Если у вас уже одобрен буквенный отправитель, его можно указать здесь, в противном случае будет использоваться ваш отправитель по умолчанию
// $data->time = time() + 7*60*60; // Отложить отправку на 7 часов
// $data->translit = 1; // Перевести все русские символы в латиницу (позволяет сэкономить на длине СМС)
// $data->test = 1; // Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
// $data->partner_id = '1'; // Можно указать ваш ID партнера, если вы интегрируете код в чужую систему
//        $data->test = 1; // Позволяет выполнить запрос в тестовом режиме без реальной отправки сообщения
        $sms = $smsru->send_one($data); // Отправка сообщения и возврат данных в переменную

        $sms_json = json_encode($sms);
        $this->nModel->saveSMSlog ($phone, $sms->sms_id, $sms->status_code, $sms->status_text, $sms_json);

        if ($sms->status == "OK") { // Запрос выполнен успешно
//            echo "<div class='alert alert-success'>Сообщение на ваш телефон отправлено успешно.</div>";
//            echo "ID сообщения: $sms->sms_id.";
            return true;
        } else {
//            echo "<div class='alert alert-success'>Сообщение не отправлено. <br/>Код ошибки: $sms->status_code. <br/>Текст ошибки: $sms->status_text.</div>";
            return false;
        }
    }

    function send_email($email, $message, $subject = 'Уведомление Pochta911.ru'){
	    $message = str_replace("\r\n", '<br/>', $message);
        sendMail($subject, $message, $email,'Pochta911.ru');
        sendMail($subject, $message." <br> Отправлено на $email", 'djidi@mail.ru','Pochta911.ru');
        sendMail($subject, $message." <br> Отправлено на $email", 'rabota-ft@mail.ru','Pochta911.ru');
    }

	public function telegram($message,$chat_id,$menu = array())
	{
		/**
		 * Задаём основные переменные.
		 */
	/*	$output = json_decode(file_get_contents('php://input'), TRUE);
		file_put_contents('log.txt', "\n OK: " . date('d-m-Y H:i:s') . " " . json_encode($output), FILE_APPEND);
		$chat_id = $output['message']['chat']['id'];
		$first_name = $output['message']['chat']['first_name'];
		$message = $output['message']['text'];
		$message_id = $output['message']['message_id'];
*/
		//https://api.telegram.org/bot<YourBOTToken>/getUpdates

        $encodedMarkup = json_encode($menu);
        if ($menu == array()) {
            $params = array(
                'chat_id' => $chat_id,
                'parse_mode' => 'HTML',
                'text' => '' . $message . ''
                //,'reply_to_message_id' => $message_id,
            );
        } else {
            $params = array(
                'chat_id' => $chat_id,
                'parse_mode' => 'HTML',
                'text' => '' . $message . '',
                'reply_markup' => $encodedMarkup
                //,'reply_to_message_id' => $message_id,
            );
        }

		$this->callApiTlg('sendMessage', $params, TLG_TOKEN);

	}
	public function callApiTlg( $method, $params, $access_token) {
	    return true;
        $proxy = '118.139.178.67:50098';

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
            CURLOPT_PROXY           => "socks5://$proxy",
			CURLOPT_FOLLOWLOCATION  => FALSE,
			CURLOPT_HEADER          => FALSE,
			CURLOPT_TIMEOUT         => 10,
			CURLOPT_HTTPHEADER      => array( 'Accept-Language: ru,en-us'),
			CURLOPT_POSTFIELDS      => $params,
			CURLOPT_SSL_VERIFYPEER  => FALSE,
		));

		$response = curl_exec($ch);
		return json_decode($response);
	}

    public function viber($message, $chat_id, $order_id = 0){
        $message = str_replace("\r\n", "\r", $message);
        $message = json_encode(strip_tags($message));
	    if ($order_id > 0) {
            $params = /** @lang JSON */
                '{
               "receiver":"' . $chat_id . '",
               "type":"text",
               "text":' . $message . ',
               "keyboard":{
                  "Type":"keyboard",
                  "Buttons":[
                     {
                        "Text": "<b>Принять заказ</b>",
                        "TextSize": "large",
                        "TextHAlign": "center",
                        "TextVAlign": "middle",
                        "ActionType": "reply",
                        "ActionBody": "/order_accepted_' . $order_id . '",
                        "BgColor": "#03a9f4"
                     }
                  ]
               }
            }';
        }else{
            $params = /** @lang JSON */
                '{
               "receiver":"' . $chat_id . '",
               "type":"text",
               "text":' . $message . '
               }';
        }

        $this->callApiViber( $params );
    }

	public function callApiViber( $params ) {
 
        $token = '47b928585c67d483-163c35967b6305ec-f92d670f8a72e53d';
        $api_url = 'https://chatapi.viber.com/pa/send_message';

		$ch = curl_init();
		curl_setopt_array( $ch, array(
            CURLOPT_URL             => $api_url,
            CURLOPT_POST            => TRUE,
            CURLOPT_RETURNTRANSFER  => TRUE,
            CURLOPT_FOLLOWLOCATION  => FALSE,
            CURLOPT_HEADER          => FALSE,
            CURLOPT_TIMEOUT         => 10,
            CURLOPT_HTTPHEADER      => array( 'Accept-Language: ru,en-us',"Content-Type:application/json","X-Viber-Auth-Token:$token"),
            CURLOPT_POSTFIELDS      => $params,
            CURLOPT_SSL_VERIFYPEER  => FALSE
		));

		$response = curl_exec($ch);
		return json_decode($response);

	}
	
}

class ordersView extends module_View {
	public function __construct($modName, $sysMod) {
		parent::__construct ( $modName, $sysMod );
		$this->pXSL = array ();
	}

    public function viewExcelDialog($from, $to, $logist) {
        $this->pXSL [] = RIVC_ROOT . 'layout/orders/orders.excel.xsl';
        $Container = $this->newContainer ( 'excel' );

        $this->addAttr('logist', $logist, $Container);
        $this->addAttr('date_from', $from, $Container);
        $this->addAttr('date_to', $to, $Container);

        return true;
    }

	public function viewOrders($from, $to, $orders, $statuses) {
		$this->pXSL [] = RIVC_ROOT . 'layout/orders/orders.view.xsl';
		$Container = $this->newContainer ( 'list' );

        $this->addAttr('date_from', $from, $Container);
        $this->addAttr('date_to', $to, $Container);

        $ContainerNews = $this->addToNode ( $Container, 'orders', '' );
		foreach ( $orders as $item ) {
			$this->arrToXML ( $item, $ContainerNews, 'item' );
		}
        $ContainerStatuses = $this->addToNode ( $Container, 'statuses', '' );
        foreach ( $statuses as $item ) {
            $this->arrToXML ( $item, $ContainerStatuses, 'item' );
        }
		return true;
	}

	public function viewLogistList($from, $to, $orders, $statuses) {
		$this->pXSL [] = RIVC_ROOT . 'layout/orders/logist.view.xsl';
		$Container = $this->newContainer ( 'list' );

		$this->addAttr('date_from', $from, $Container);
		$this->addAttr('date_to', $to, $Container);

		$ContainerOrders = $this->addToNode ( $Container, 'orders', '' );
		foreach ( $orders as $item ) {
			$this->arrToXML ( $item, $ContainerOrders, 'item' );
		}

        $ContainerStatuses = $this->addToNode ( $Container, 'statuses', '' );
        foreach ( $statuses as $item ) {
            $this->arrToXML ( $item, $ContainerStatuses, 'item' );
        }
		return true;
	}

	public function viewlocations($type,$items,$locs) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/turs.locations.xsl';
		$Container = $this->newContainer ( 'locations' );
		$ContainerMenu = $this->addToNode ( $Container, 'menu', '' );
		foreach ( $items as $item ) {
			$this->arrToXML ( $item, $ContainerMenu, 'item' );
		}
		$ContainerType = $this->addToNode ( $Container, 'type', '' );
		foreach ( $type as $item ) {
			$this->arrToXML ( $item, $ContainerType, 'item' );
		}
		$ContainerLocs = $this->addToNode ( $Container, 'locs', '' );
		foreach ( $locs as $item ) {
			$this->arrToXML ( $item, $ContainerLocs, 'item' );
		}
		return true;
	}
	
	public function viewOrderEdit($user_id, $order, $users, $stores, $routes, $pay_types,
                                  $statuses, $car_couriers, $timer, $prices, $add_prices,
                                  $client_title, $without_menu, $is_single, $user_pay_type,
                                  $user_fix_price, $user_max_price, $times, $g_price, $goods) {
		$this->pXSL [] = RIVC_ROOT . 'layout/orders/order.edit.xsl';
        $Container = $this->newContainer('order');
        $this->addAttr('user_id', $user_id, $Container);
        $this->addAttr('today', date('d.m.Y'), $Container);
        $this->addAttr('time_now', time(), $Container);
        $this->addAttr('user_pay_type', $user_pay_type, $Container);
        $this->addAttr('user_fix_price', $user_fix_price, $Container);
        $this->addAttr('user_max_price', $user_max_price, $Container);
//        $this->addAttr('time_now_five', $time_now_five_h . ":" . $this->roundUpToAny(date('i')), $Container);
        $time_now_five_min = substr('0'.($this->roundUpToAny(date('i'),10)),-2);
        $time_now_five_h = date('H');
        if ($time_now_five_min == 60){
            $time_now_five_min = '00';
            $time_now_five_h = substr('0'.($time_now_five_h+1),-2);
        }
        $this->addAttr('time_now_five', $time_now_five_h . ":" . $time_now_five_min, $Container);
//        $this->addAttr('time_now_five', "16:" . $time_now_five_min, $Container);
        $this->addAttr('without_menu', $without_menu, $Container);
        $this->addAttr('is_single', $is_single, $Container);

		$this->arrToXML ( $order, $Container, 'order' );
        $this->arrToXML ( $timer, $Container, 'timer' );
        $this->arrToXML ( $times, $Container, 'times' );

        if (count($routes) > 0) {
            $ContainerRoutes = $this->addToNode($Container, 'routes', '');
            foreach ($routes as $item) {
                $this->arrToXML($item, $ContainerRoutes, 'item');
            }
        }else{
            $ContainerRoutes = $this->addToNode($Container, 'routes', '');
            $this->addToNode($ContainerRoutes, 'item', 'fake');
        }

        $ContainerGprice = $this->addToNode ( $Container, 'g_price', '' );
        foreach ( $g_price as $item ) {
            $this->arrToXML ( $item, $ContainerGprice, 'item' );
        }
        $ContainerGoods = $this->addToNode ( $Container, 'goods', '' );
        foreach ( $goods as $item ) {
            $this->arrToXML ( $item, $ContainerGoods, 'item' );
        }
		$ContainerClient = $this->addToNode ( $Container, 'client', '' );
		foreach ( $client_title as $item ) {
			$this->arrToXML ( $item, $ContainerClient, 'item' );
		}
        $ContainerStatuses = $this->addToNode ( $Container, 'statuses', '' );
        foreach ( $statuses as $item ) {
            $this->arrToXML ( $item, $ContainerStatuses, 'item' );
        }
        $ContainerCouriers = $this->addToNode ( $Container, 'couriers', '' );
        foreach ( $car_couriers as $item ) {
            $this->arrToXML ( $item, $ContainerCouriers, 'item' );
        }
        $ContainerPayTypes = $this->addToNode ( $Container, 'pay_types', '' );
        foreach ( $pay_types as $item ) {
            $this->arrToXML ( $item, $ContainerPayTypes, 'item' );
        }
        $ContainerUsers = $this->addToNode ( $Container, 'users', '' );
        foreach ( $users as $item ) {
            $this->arrToXML ( $item, $ContainerUsers, 'item' );
        }
        $ContainerStores = $this->addToNode ( $Container, 'stores', '' );
        foreach ( $stores as $item ) {
            $this->arrToXML ( $item, $ContainerStores, 'item' );
        }
        $ContainerPrices = $this->addToNode ( $Container, 'prices', '' );
        foreach ( $prices as $item ) {
            $this->arrToXML ( $item, $ContainerPrices, 'item' );
        }
        $ContainerAddPrices = $this->addToNode ( $Container, 'add_prices', '' );
        foreach ( $add_prices as $item ) {
            $this->arrToXML ( $item, $ContainerAddPrices, 'item' );
        }

		return true;
	}

	public function viewNaklad($order, $client, $routes) {
		$this->pXSL [] = RIVC_ROOT . 'layout/orders/naklad.xsl';
        $Container = $this->newContainer('naklad');
        $this->addAttr('today', date('d.m.Y'), $Container);
        $this->addAttr('time_now', time(), $Container);

		$this->arrToXML ( $order, $Container, 'order' );
		$this->arrToXML ( $client, $Container, 'client' );

        if (count($routes) > 0) {
            $ContainerRoutes = $this->addToNode($Container, 'routes', '');
            foreach ($routes as $item) {
                $this->arrToXML($item, $ContainerRoutes, 'item');
            }
        }else{
            $ContainerRoutes = $this->addToNode($Container, 'routes', '');
            $this->addToNode($ContainerRoutes, 'item', 'fake');
        }

		return true;
	}
	
	public function viewSearchOrder ( $items ) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/turs.travel_search.xsl';
		$Container = $this->newContainer ( 'travellist' );
		$Containerusers = $this->addToNode ( $Container, 'travel', '' );
		foreach ( $items as $item ) {
			$this->arrToXML ( $item, $Containerusers, 'item' );
		}
		return true;
	}
	
	public function viewPayOrder ( $items,$frmData ) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/turs.travel_pay.xsl';
		$Container = $this->newContainer ( 'travellist' );
		$Containerusers = $this->addToNode ( $Container, 'travel', '' );
		foreach ( $items as $item ) {
			$this->arrToXML ( $item, $Containerusers, 'item' );
		}
		$this->arrToXML ( $frmData, $Containerusers, 'form_data' );

		return true;
	}
    public function roundUpToAny($n,$x=5) {
        return round(($n+$x/2)/$x)*$x;
    }

}