<?php
class emailModel extends module_model {
	public function __construct($modName) {
		parent::__construct ( $modName );
	}
	

	public function get_emails() {
		$sql = "SELECT * FROM turs_signups";
		$this->query ( $sql );
		$items = array();
		while(($row = $this->fetchRowA())!==false) {
			$items[] = $row;
		}
		return $items;
	}	
	
	public function emailList() {
		$sql = "SELECT signups_id,	
		signup_email_address,	
		IFNULL(signup_username,'') signup_username,
		signup_date,	
		signup_time,	
		location
		FROM " . TAB_PREF . "`turs_signups`";
		$this->query ( $sql );
		$letters = array('ku', 'vp', 'gr');
		$locations   = array('Купчино', 'Веселый поселок', 'Гражданка');
		$items = array ();
		while ( ($row = $this->fetchRowA ()) !== false ) {			
			$row['location']  = str_replace($letters, $locations, $row['location']);
			$items [] = $row;
		}
		return $items;
	}

	public function emailGet($email_id) {
		$sql = "SELECT signups_id,	
		signup_email_address,	
		IFNULL(signup_username,'') signup_username,
		IFNULL(signup_date,'') signup_date,
		IFNULL(signup_time,'') signup_time,	
		location
		FROM " . TAB_PREF . "`turs_signups` WHERE signups_id = '$email_id'";
		$this->query ( $sql );
		$item = $this->fetchRowA ();
		$item = ($item)?$item:Array('signups_id'=>'0');
		return $item;
	}
	
	public function emailDelete($id) {		
		$sql = 'DELETE FROM ' . TAB_PREF . '`turs_signups` WHERE `signups_id` = '.$id.'';
		$this->query ( $sql );
		return true;
	}
	public function emailUpdate($id, $p) {		
		$sql = 'UPDATE ' . TAB_PREF . '`turs_signups` SET 
		`signup_username`  = \''.$p['signup_username'].'\', 
		`signup_email_address`  = \''.$p['signup_email_address'].'\', 
		`location`  = \''.$p['location'].'\' 
		WHERE `signups_id` = '.$id.'';
		$this->query ( $sql );
		return true;
	}
	public function emailInsert($id, $p) {
		$sql = 'INSERT INTO ' . TAB_PREF . '`turs_signups` 
				(`signup_username`,`signup_email_address`,`location`,
				signup_date, signup_time) 
				VALUES 
				(\''.$p['signup_username'].'\',\''.$p['signup_email_address'].'\',\''.$p['location'].'\',\''.date('Y-m-d').'\',\''.date('H:i:s').'\')';
		$this->query ( $sql );
		return $this->affectedRows();
	}
	function mydate_to_dmy($date) {
		return ($date != '0000-00-00')?date ( 'd.m.Y', strtotime ( substr ( $date, 0, 20 ) ) ):'';
	}

	function dmy_to_mydate($date) {
		return date ( 'Y-m-d', strtotime (  $date ) );
	}



}

class emailProcess extends module_process {
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
		$this->nModel = new emailModel ( $modName );
		$sysMod = $this->nModel->getSysMod ();
		$this->sysMod = $sysMod;
		$this->mod_id = $sysMod->id;
		$this->nView = new emailView ( $this->modName, $this->sysMod );
		$this->regAction ( 'view', 'Список рассылок', ACTION_GROUP );
		$this->regAction ( 'send', 'Отправка писем', ACTION_GROUP );
		$this->regAction ( 'viewlist', 'Список адресатов', ACTION_GROUP );
		$this->regAction ( 'emailEdit', 'Редактировать Email', ACTION_GROUP );
		$this->regAction ( 'emailDelete', 'Удалить Email', ACTION_GROUP );
		(DEBUG == 0)?$this->registerActions ( 1 ):$this->registerActions ( 0 );
	}
	
	
	public function update($_action = false) {
		$this->updated = false;
		if ($_action)
			$this->action = $_action;
		$action = $this->actionDefault;
		if ($this->action)
			$action = $this->action;
		else
			$action = $this->checkAction ();
		if (! $action) {
			$this->Vals->URLparams ( $this->sysMod->defQueryString );
			$action = $this->actionDefault;
		}
		$user_id = $this->User->getUserID ();
		$user_tab_no = $this->User->getUserTabNo ();
		$user_right = $this->User->getRight ( $this->modName, $action );
		if ($user_right == 0 && $user_id > 0) {
			$p = array ('У Вас нет прав для использования модуля', '$this->modName' => $this->modName, 'action' => $action, 'user_id' => $user_right, 'user_right' => $user_right );
			$this->nView->viewError ( 'У Вас нет прав на это действие', 'Предупреждение' );
			$this->Log->addError ( $p, __LINE__, __METHOD__ );
			$this->updated = true;
			return;
		}
		
		if ($user_right == 0 && $user_id == 0 && ! $_action) {
			$this->nView->viewLogin ( 'Система БЛТ', '', $user_id, array (), array () );
			$this->updated = true;
			return;
		}
		
		if ($user_id > 0 && ! $_action) {
			$this->User->nView->viewLoginParams ( 'Система БЛТ', '', $user_id, array (), array (), $this->User->getRight ( 'admin', 'view' ) );
		}
		
		
		if ($action == 'send') {
				// Определяем переменные
				$emailer_subj = $this->Vals->getVal ( 'emailer_subj', 'POST', 'string' );
				$emails = $this->Vals->getVal ( 'emailer_mails', 'POST', 'array' );
				$emailer_text = $this->Vals->getVal ( 'emailer_text', 'POST', 'string' );
				$emailer_yourmail = $this->Vals->getVal ( 'emailer_yourmail', 'POST', 'string' );
			
				// Теперь проверяем заполнение всех полей
				if (empty($emailer_subj)) {
					// Если тема пустая...
					$mail_msg='Вы не ввели тему письма';
				} elseif (empty($emails)) {
					// Если адресов нет...
					$mail_msg='Не указано адреса получателей';
				} elseif (empty($emailer_text)) {
					// Если сообщение пустое...
					$mail_msg='Вы не ввели текст письма';
				} else { // Если все поля заполнены верно...
					// Готовим сообщение об успешной отправке... Вдруг у вас какой-то необычный браузер
					$mail_msg='Ваше сообщение отправлено.';
					// Готовим заголовки письма... Будем отправлять письма в формате HTML и кодировке UTF-8
					$headers="MIME-Version: 1.0\r\n";
					$headers.="Content-type: text/html; charset=utf-8\r\n";
					$headers.="From: $emailer_yourmail";
			
					// Обработка письма. Нужно удалить лишние пробелы и проставить переносы.
					$emailer_text=preg_replace("/ +/"," ",$emailer_text); // множественные пробелы заменяются на одинарные
					$emailer_text=preg_replace("/(\r\n){3,}/","\r\n\r\n",$emailer_text); // убираем лишние переносы (больше 1 строки)
					$emailer_text=str_replace("\r\n","<br>",$emailer_text); // ставим переносы
			
					$count_emails = count($emails); // Подсчёт количества адресов
					
					// Запускаем цикл отправки сообщений
					for ($i=0; $i<=$count_emails-1; $i++) {
						// Подставляем адреса получаетелей и обрезаем пробелы с обоих сторон, если таковые имеются
						$email=trim($emails[$i]);
						// Отправляем письмо и готовим отчёт по отправке
						if($emails[$i]!="") { // Проверка на случай попадения в массив пустого значения
							if(mail($email,$emailer_subj,$emailer_text,$headers)) 
								$report.="<li><span style=\"color:green;\">Отправлено: ".$emails[$i]."</span></li>"; 
							else 
								$report.="<li><span style=\"color:red;\">Не отправлено: ".$emails[$i]."<span></li>";
							@unlink("logs/log_online.txt");
							file_put_contents("logs/log_online.txt",'Отправлено '.($i+1).' из '.$count_emails);
							sleep(5); // Делаем тайм-аут в 1 сек
						}
					}
					$mail_msg='Количество отправленных писем: '.$i.'.';
					// Запись отчёта в файл. Файл будет сгенерирован в той же папке, под названием log.txt. Проверьте настройку прав папки.
					$log=fopen("logs/log.txt","w");
					fwrite($log,$report);
					fclose($log);
				}
				$this->nView->viewMessage ( $mail_msg, 'Сообщение' );
			//	$action = 'view';
		}
		
		
		if ($action == 'view') {
			$mails = $this->nModel->get_emails ();
			$this->nView->viewMailForm ($mails);
		}
		
		if ($action == 'viewlist') {
			$items = $this->nModel->emailList ( $email, $name );
			$this->nView->viewEmailList ( $items );
			$this->updated = true;
		}
		if ($action == 'emailDelete') {
			$email_id = $this->Vals->getVal ( 'emailDelete', 'GET', 'integer' );
			$this->nModel->emailDelete ( $email_id );
			header("Location: /email/viewlist-1");
		}
		if ($action == 'emailEdit') {
			$email_id = $this->Vals->getVal ( 'emailEdit', 'GET', 'integer' );
			$signups_id = $this->Vals->getVal ( 'signups_id', 'POST', 'integer' );
			
			// Редактируем/Создаем профиль подписчика
			if ($signups_id != null) {
				$p['signup_username'] = $this->Vals->getVal ( 'signup_username', 'POST', 'string' );
				$p['signup_email_address'] = $this->Vals->getVal ( 'signup_email_address', 'POST', 'string' );
				$p['location'] = $this->Vals->getVal ( 'location', 'POST', 'string' );
				$email = $this->nModel->emailGet ( $signups_id );
				// Редактируем
				if ($email ['signups_id'] > 0){
					$this->nModel->emailUpdate ( $signups_id, $p );
					header("Location: /email/viewlist-1");
				}else{ // Создаем
					$this->nModel->emailInsert ( $signups_id, $p );	
					header("Location: /email/viewlist-1");				
				}
			}
			
			// Открываем профиль подписчика
			$email = $this->nModel->emailGet ( $email_id );					
			$this->nView->viewEmailEdit ( $email );
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


	}
	
}

class emailView extends module_View {
	public function __construct($modName, $sysMod) {
		parent::__construct ( $modName, $sysMod );
		$this->pXSL = array ();
	}
	
	
	public function viewMailForm($mails) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/email.list.xsl';
		$Container = $this->newContainer ( 'mailform' );
		$ContainerItems = $this->addToNode ( $Container, 'items', '' );
		foreach ( $mails as $item ) {
			$this->arrToXML ( $item, $ContainerItems, 'item' );
		}
		return true;
	}
	
	public function viewEmailEdit($email) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/email.edit.xsl';
		$Container = $this->newContainer ( 'emailedit' );
		$this->arrToXML ( $email, $Container, 'email' );		
		return true;
	}
	
	public function viewEmailList($items) {
		$this->pXSL [] = RIVC_ROOT . 'layout/'.$this->sysMod->layoutPref.'/email.viewlist.xsl';
	
		$Container = $this->newContainer ( 'emaillist' );
		$Containerusers = $this->addToNode ( $Container, 'items', '' );
		foreach ( $items as $item ) {
			$this->arrToXML ( $item, $Containerusers, 'item' );
		}
		return true;
	}


}
/**
 * **********************************
 */
?>