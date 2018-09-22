<?php

class pageItem extends module_item {
	public $id;
	public $name; /* уникальное имя */

	public $module;   /* название модуля */
	public $parent_module; /* родительский модуль */

	public $title;
	public $content;
	public $access;

	public $user_id; /* кто написал страницу */
	public $date;
	public $date_change; /* дата изменения */

	public $description;
	public $keywords;
	public $page_title;

	public function __construct ($Params) {
//		global $LOG;
		parent::__construct();
		if (isset($Params['id'])) $this->id = $Params['id']; else $this->id = 0;
		if (isset($Params['name'])) $this->name = $Params['name']; else $this->name = 0;
		if (isset($Params['module'])) $this->module = $Params['module']; else $this->module = 0;
		if (isset($Params['parent_module'])) $this->parent_module = $Params['parent_module']; else $this->parent_module = 0;
		if (isset($Params['title'])) $this->title = $Params['title']; else $this->title = 0;
		if (isset($Params['content'])) $this->content = $Params['content']; else $this->content = 0;
		if (isset($Params['access'])) $this->access = $Params['access']; else $this->access = 0;
		if (isset($Params['user_id'])) $this->user_id = $Params['user_id']; else $this->user_id = 0;
		if (isset($Params['date'])) $this->date = $Params['date']; else $this->date = 0;
		if ($this->date == 0) $this->date = time();
		/*    if (($timestamp = strtotime($str)) === -1) {
		 echo "Строка ($str) недопустима";
		 } else {
		 echo "$str == " . date('l dS of F Y h:i:s A', $timestamp);
		 }  */
		if (isset($Params['date_change'])) $this->date_change = $Params['date_change']; else $this->date_change = 0;
		if (isset($Params['description'])) $this->description = $Params['description']; else $this->description = 0;
		if (isset($Params['keywords'])) $this->keywords = $Params['keywords']; else $this->keywords = 0;
		if (isset($Params['page_title'])) $this->page_title = $Params['page_title']; else $this->page_title = 0;

	}

	public function toArray() {
		$Params = array();
		$Params['id'] = $this->id;
		$Params['name'] = $this->name;
		$Params['module'] = $this->module;
		$Params['parent_module'] = $this->parent_module;
		$Params['title'] = $this->title;
		$Params['content'] = $this->content;
		$Params['access'] = $this->access;
		$Params['user_id'] = $this->user_id;
		$Params['date'] = $this->date;
		$Params['date_change'] = $this->date_change;
		$Params['description'] = $this->description;
		$Params['keywords'] = $this->keywords;
		$Params['page_title'] = $this->page_title;

		return $Params;
	}
}



class pageModel extends module_model {
	public function __construct ($modName) {
		return parent::__construct($modName);
	}


	public function add(pageItem $item) {
		$res = $item->toInsert();
		$sql = 'INSERT INTO '.TAB_PREF.'pages ('.$res[0].') VALUES('.$res[1].')';
		$q = array_merge(array(0=>$sql),$res[2]);
		$this->query($q);
		$id = $this->insertID();
		$item->id = $id;
		return true;
	}

	public function update(pageItem $item) {
		$Params = $item->toArray();
		$sql = 'UPDATE '.TAB_PREF.'pages
    SET '.' `module` = \'%3$u\', `parent_module` = \'%4$u\', `title` = \'%5$s\', `content` = \'%6$s\', `access` = \'%7$u\', `user_id` = \'%8$u\', `date` = \'%9$s\', `date_change` = \'%10$s\', `description` = \'%11$s\', `keywords` = \'%12$s\', `page_title` = \'%13$s\'
    WHERE id = %1$u AND `name` = \'%2$s\'';
		return $this->query($sql, $Params['id'], $Params['name'], $Params['module'], $Params['parent_module'], $Params['title'], $Params['content'], $Params['access'], $Params['user_id'], $Params['date'], $Params['date_change'], $Params['description'], $Params['keywords'], $Params['page_title']);
	}

	public function getPageID($pageName) {
		$sql = 'SELECT id FROM pages WHERE name = \'%1$s\'';
		$this->query($sql, $pageName);
		$id = $this->getOne();
		return $id;
	}
	public function getInfoPages() {
		$sql = 'SELECT * FROM pages WHERE module=13 ORDER BY title';
		$this->query($sql);
	
		$collection = Array();
		while (($row = $this->fetchOneRowA())!==false) {
			$collection[]=$row;
		}
		return $collection;
	}

	public function getAllPages() {
		$sql = 'SELECT id, module, access, title FROM pages ORDER BY module,title';
		$this->query($sql);
		
		$collection = Array();
		while (($row = $this->fetchOneRowA())!==false) {
			$collection[]=$row;
		}
		return $collection;
	}

	public function get($id) {
		$sql = 'SELECT * FROM pages WHERE id = '.$id.' AND (module='.$this->mod_id.' OR `parent_module` = 0)';
		$this->query($sql);
		$row = $this->fetchOneRowA();
		$page = new pageItem($row);
		return $page;
	}

	public function del($id) {
		$sql = "DELETE FROM pages WHERE id = $id";
		$query = $this->query($sql);
		return $query;
	}
	public function copy($id) {
		$sql = 'insert into pages (name,module,parent_module,title,content,access,date,date_change,description,keywords,page_title,user_id)
				select CONCAT(name,\'_\',id),module,parent_module,CONCAT(title,\' copy\'),content,access,date,date_change,description,keywords,page_title,user_id
				FROM pages where id = '.$id;
		$query = $this->query($sql);
		return $query;
	}
/*
	public function sendSupport ($fio, $email, $message) {
		$sql = 'INSERT INTO messages (dateadd, ipadress, uname, umail, letter) VALUES (NOW(), \'%1$s\',\'%2$s\',\'%3$s\',\'%4$s\')';
		$ip = $_SERVER["REMOTE_ADDR"];
		$sr = $this->query($sql,$ip, $fio,$email,$message);
		//stop($this->sql);
		if (!$sr) return false;
//		$date = date('d.m.Y');
		$to = 'support@rivc-pulkovo.ru';
		$title='title';
		// To send HTML mail, the Content-type header must be set
		$headers = 'MIME-Version: 1.0'.rn;
		$headers.= 'Content-type: text/html; charset=windows-1251'.rn;

		$headers.= 'From: '.$_SERVER["SERVER_NAME"].' <mailer@'.$_SERVER["SERVER_NAME"].'>';

		$content = 'Центр сообщений'."\r\n";
		$content.= 'Имя: '.$_REQUEST['name']."\r\n";
		$content.= 'E-mail: '.$_REQUEST['mail']."\r\n";
		$content.= 'Cообщение: '."\r\n".$_REQUEST['msgtxt']."\r\n";
		$ok=mail($to,$title,$content,$headers);
		$res = ($sr & $ok);
		return $res;
	}
*/
}


class pageProcess extends module_process {

	public function __construct ($modName) {
		parent::__construct($modName);
		$this->nModel = new pageModel($modName);
		$this->nView = new pageView($this->modName, $this->sysMod);

		/* Default Process Class actions */
		$this->regAction('new', 'Новая страница', ACTION_GROUP);
		$this->regAction('add', 'Добавить страницу', ACTION_GROUP);
		$this->regAction('edit', 'Редактировать страницу', ACTION_GROUP);
		$this->regAction('update', 'Обновить страницу', ACTION_GROUP);
		$this->regAction('view', 'Открыть страницу', ACTION_GROUP);
		$this->regAction('del', 'Удалить страницу', ACTION_GROUP);
		$this->regAction('copy', 'Копировать страницу', ACTION_GROUP);
		$this->regAction('line', 'Список страниц', ACTION_GROUP);
		$this->regAction('info', 'Информационные страницы', ACTION_GROUP);
		
		if (DEBUG == 0) {
			$this->registerActions ( 1 );
		}
		if (DEBUG == 1) {
			$this->registerActions ( 0 );
		}

	}

	public function update($_action = false) {
		$this->updated = false;

		if ($_action) $this->action = $_action;
		$action = false;


		if ($this->action) $action = $this->action;
		elseif (($a = $this->vals->getVal('line', 'GET')) !== NULL) $action = 'line';
		elseif (($a = $this->vals->getVal('info', 'GET')) !== NULL) $action = 'info';
		elseif (($a = $this->vals->getVal('view', 'GET')) !== NULL) $action = 'view';
		elseif (($a = $this->vals->getVal('new', 'GET')) !== NULL) $action = 'new';
		elseif (($a = $this->vals->getVal('add', 'GET')) !== NULL) $action = 'add';
		elseif (($a = $this->vals->getVal('edit', 'GET')) !== NULL) $action = 'edit';
		elseif (($a = $this->vals->getVal('update', 'GET')) !== NULL) $action = 'update';
		elseif (($a = $this->vals->getVal('del', 'GET')) !== NULL) $action = 'del';
		elseif (($a = $this->vals->getVal('copy', 'GET')) !== NULL) $action = 'copy';
		elseif (($a = $this->vals->getVal('send', 'GET')) !== NULL) $action = 'send';
		if (!$action) {
			$this->Vals->URLparams($this->sysMod->defQueryString);
			$action = $this->actionDefault;
		}
		$this->Log->addToLog(array('Страницы', $action), __LINE__, __METHOD__);

		$user_id = $this->User->getUserID();
		$user_right = $this->User->getRight($this->modName, $action);

		if ($user_right == 0 && $user_id > 0) {
			$p = array('У Вас нет прав для использования модуля', '$this->modName'=>$this->modName, 'action'=>$action, 'user_id'=>$user_right, 'user_right'=>$user_right);
			$this->nView->viewMessage('У Вас нет прав на использования данного модуля.', '');
			$this->Log->addError($p, __LINE__, __METHOD__);
			$this->updated = true;
			return;
		}

        $this->User->nView->viewLoginParams('Цветочное такси','',$user_id, array(),array(), $this->User->getRight('admin','view'));

/*
		if ($user_id > 0 && !$_action) {
			$this->User->nView->viewLoginParams('Балтиклайнс Тур','',$user_id, array(),array(), $this->User->getRight('admin','view'));
		}
*/
		if ($action == 'new') {
			/* строим XML для создания */
			if ($user_id > 0) {

				$this->nView->viewNew($user_id);
			}
			else {
				$p = 'У Вас нет прав для создания страницы';
				$this->nView->viewError($p);
				$this->Log->addError(array('msg'=>$p, 'user_id'=>$user_id), __LINE__, __METHOD__);
			}
			$this->updated = true;
		}
		#'<![CDATA[ '. .' ]]>'
		if ($action == 'add') {
			#$Params['id'] = $this->vals->getVal('id', 'POST', 'string');
			$Params['name'] = $this->vals->getVal('name', 'POST', 'string');
			#$Params['category'] = $this->vals->getVal('category', 'POST', 'string');
			$Params['title'] = $this->vals->getVal('title', 'POST', 'string');
			$Params['content'] = $this->vals->getVal('content', 'POST', 'string');
			$Params['access'] = $this->vals->getVal('access', 'POST', 'ineger');
			$Params['module'] = $this->vals->getVal('module', 'POST', 'ineger');
			$Params['owner_user'] = $user_id;
			$Params['date'] = time();
			$Params['description'] = $this->vals->getVal('description', 'POST', 'string');
			$Params['keywords'] = $this->vals->getVal('keywords', 'POST', 'string');
			$Params['page_title'] = $this->vals->getVal('page_title', 'POST', 'string');
//			$page = new pageItem($Params);

			if ($user_id > 0) {
			//	if ($this->isValid($Params)) {
					$item = new pageItem($Params);
					$this->nModel->add($item);
					$pFile = new fileProcess($this->modName,$item->id);
					$pFile->update('add');
					$this->nView->viewMessage('Новая старница создана', 'Сообщение');
					$action = 'line';
				//	$this->Vals->setValTo('view',$item->id,'GET');
			//	} else $this->Log->addError(array('msg'=>'Данные не валидны', 'user_id'=>$user_id), __LINE__, __METHOD__);
			} else {
				$p = 'У Вас нет прав для добавления '.$this->modName;
				$this->nView->viewError($p);
				$this->Log->addError(array('msg'=>$p, 'user_id'=>$user_id), __LINE__, __METHOD__);
			}
			$this->updated = false;
		}
		if ($action == 'update') {
			/* сохранить изменения */
			$Params['id'] = $this->vals->getVal('update', 'POST', 'string');
			$Params['name'] = $this->vals->getVal('name', 'POST', 'string');
			#$Params['category'] = $this->vals->getVal('category', 'POST', 'string');
			$Params['title'] = $this->vals->getVal('title', 'POST', 'string');
			$Params['content'] = $this->vals->getVal('content', 'POST', 'string');
			$Params['access'] = $this->vals->getVal('access', 'POST', 'ineger');
			$Params['module'] = $this->vals->getVal('module', 'POST', 'ineger');
			$Params['date_cahnge'] = time();
			$Params['description'] = $this->vals->getVal('description', 'POST', 'string');
			$Params['keywords'] = $this->vals->getVal('keywords', 'POST', 'string');
			$Params['page_title'] = $this->vals->getVal('page_title', 'POST', 'string');
			$page = new pageItem($Params);
			//stop($page->toArray());
			if ($page->id > 0) {
				$res = $this->nModel->update($page);
				$pFile = new fileProcess($this->modName,$page->id);
				$pFile->update('update');
				if ($res !== false) $this->nView->viewMessage('Страница изменена', 'Сообщение');
				if ($res === false) $this->nView->viewError('Не возможно сохранить страницу');
				$this->updated = true;
				$action = 'line';
			//	$this->Vals->setValTo('view',$page->id,'GET');
			} else {
				$p = 'Не возможно сохранить страницу '.$this->modName;
				$this->nView->viewMessage($p, 'Сообщение');
				$action = 'line';
			}


		}

		if ($action == 'del') {
			/* удалить */
//			$page_id = $this->vals->getVal('del', 'GET');
//			$res = $this->nModel->del($page_id);
			$res = false;
			if ($res !== false) $this->nView->viewMessage('Страница удалена', 'Сообщение');
			if ($res === false) $this->nView->viewError('Не возможно удалить страницу');
			$action = 'line';
		}
		
		if ($action == 'copy') {
			/* копировать */
			$page_id = $this->vals->getVal('copy', 'GET');
			$res = $this->nModel->copy($page_id);
			if ($res !== false) $this->nView->viewMessage('Страница скопирована', 'Сообщение');
			if ($res === false) $this->nView->viewMessage('Ошибка копирования страницы', 'Сообщение');
			$action = 'line';
		}

		if ($action == 'edit') {
			$page_id = $this->vals->getVal('edit', 'GET');
			if ($this->vals->isNaN($page_id)) $page_id = $this->nModel->getPageID($page_id);
			if ($page_id > 0) {
				$page = $this->nModel->get($page_id);
				if ($page->id > 0) {
					$this->nView->viewEdit($page, $user_id);
					$this->updated = true;
				}
			} else {
				$p = 'У Вас нет прав для редактирования ';//.
				$this->nView->viewError($p);
				$p .= $page_id;
				$this->Log->addError(array('msg'=>$p, 'user_id'=>$user_id, 'page_id'=>$page_id), __LINE__, __METHOD__);
				$this->updated = true;
			}
		}
		
		if ($action == 'info') {
			/* показать список */
			$pages = $this->nModel->getInfoPages();
			$this->nView->viewList($pages);
			$this->updated = true;
		}

		if ($action == 'line') {
			/* показать список */
			$pages = $this->nModel->getAllPages();
			$this->nView->viewList($pages);
			$this->updated = true;
		}

		if ($action == 'view') {
			$page_id = $this->vals->getVal('view', 'GET');
			if ($this->vals->isNaN($page_id)) $page_id = $this->nModel->getPageID($page_id);
			#$page_id = $this->nModel->getPageID('index');
			/*
			if ($page_id == 30)
				$this->nView->viewLogin('Вход для Магазинов','',$user_id);
				
			if ($user_id > 0 and $page_id == 30 ) {
				$this->nView->viewMessage('Вам доступен раздел для Агентов. <br/>
				<ul>
					<li><a href="/tc/agent-1/"><b>Список ваших бронирований.</b></a></li>
					<li><a href="/turs/viewTur-1/"><b>Сделать новое бронирование.</b></a></li>
				</ul><br />
				Все бронирования должны осуществлять после авторизации в разделе "Клиентам"','');
			}
*/
			$page = $this->nModel->get($page_id);
			if ($page->id > 0) {
				$this->nView->viewPage($page);
			} else {
//				$error = '';
				#$this->Log->addError('Страница не найдена', $page_id, 'index',__LINE__, __METHOD__);
				$this->nView->viewError(array('Страница не найдена'));
			}
			$this->updated = true;
		}


		if (!$this->updated || $action == 'index') {
			$page_id = $this->nModel->getPageID('index');
			if ($page_id > 0) {
				$page = $this->nModel->get($page_id);
				$this->nView->viewIndexPage($page);
			} else {
//				$error = '';
				#$this->Log->addError('Страница не найдена', $page_id, 'index',__LINE__, __METHOD__);
				$this->nView->viewError(array('Страница не найдена', 'index', intval($page_id)));
			}
			$this->updated = true;
		}
		/*
		 $ajax = $this->Vals->getVal('ajax','GET','string');
		 if (!$ajax == '') {
		 if ($ajax == 'pages') {
		 $doc = $this->getBody('xml');
		 header('Content-type: text/xml; charset=utf-8;');
		 exit($doc->saveXML());
		 }
		 }
		 */
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


class pageView extends module_view {
	public function __construct ($modName, $sysMod) {
		parent::__construct($modName, $sysMod);
		$this->pXSL = array();
	}

	public function viewNew($user_id) {
		global $System;
		$this->pXSL[] = RIVC_ROOT.'layout/form.xsl';
		$Container = $this->newContainer('form');
		$form = new CFormGenerator('pages', SITE_ROOT.$this->modName.'/add-1/', 'POST', 0);
		$form->addHidden('add', '1', 'add');
		$form->addHidden('user_id', $user_id, 'user_id');
		$form->addText('title', '','Название страницы RUS', 'title', '', 30);
	//	$form->addText('name', '','Название страницы ENG', 'name', '', 30);
		//$form->addText('module', 0,'Модуль', 'module', '', 30);
		$modules = $System->getModules();
		$modID = $System->getModule(0,$this->modName);
		$form->addSelect('module', $modID['id'] , 'Модуль', '', '', 'module', '', 1);
		foreach ($modules as $option) {
			//stop($option);
			if ($option['codename'] == 'modset' || $option['codename'] == 'files') continue;
			$form->addOption ('op'.$option['id'], $option['id'], $option['name'], 'module', '', '', '');
		}
		$form->addTextArea('content', '', 'Содержание', 'edit_content', '', 80);

		#      $form->addText('content', '', 'Содержание', 'content', '', 30);
	//	$form->addText('page_title', '','Заголовок страницы', 'name', '', 30);
	//	$form->addTextArea('description', '','Описание страницы', 'name', '', 80);
	//	$form->addText('keywords', '','ключевые слова', 'name', '', 30);

	//	$pFile = new fileProcess('files');
	//	$fileInputs = $pFile->update('new');
	//	$form->addInputs($fileInputs);

		$form->addSubmit('submit', 'сохранить', '', 'btn btn-success btn-xs');
	//	$form->addSubmit('submit', 'создать', '', '');
		$form->getBody($Container,'xml');
		return $form;
	}

	public function viewEdit(pageItem $page, $user_id) {
		global $System;
		$this->pXSL[] = RIVC_ROOT.'layout/form.xsl';
		$Container = $this->newContainer('form');
		$form = new CFormGenerator('pages', SITE_ROOT.$this->modName.'/update-'.$page->id.'/', 'POST', 0);
		$form->addHidden('update', $page->id, 'update');
		$form->addHidden('user_id', $user_id, 'user_id');
//		$form->addBox('access', '1','Пометить NEW', '1', $page->access, '', '', '');
		$form->addHidden('name', $page->name,'name');
		$form->addText('title', $page->title,'Название страницы RUS', 'title', '', 30);
	//	$form->addLabledMessage('name2', 'Название страницы ENG', $page->name, 'name2');

		//$form->addText('module', $page->module,'Модуль', 'module', '', 30);
		$modules = $System->getModules();
//		$modID = $System->getModule(0,$this->modName);
		//stop($page->module,0);
		$form->addSelect('module', $page->module , 'Модуль', $page->module, $page->module, 'module', '', 1);
		foreach ($modules as $option) {
			//stop($option);
			if ($option['codename'] == 'modset' || $option['codename'] == 'files' || $option['codename'] == 'menu') continue;
			$form->addOption ('op'.$option['id'], $option['id'], $option['name'], 'module', '', '', '');
		}
		$form->addTextArea('content', stripslashes($page->content), 'Содержание', 'edit_content', 'editing', 80);

		#      $form->addText('content', '', 'Содержание', 'content', '', 30);
	//	$form->addText('page_title', $page->page_title,'Заголовок страницы', 'page_title', '', 30);
	//	$form->addTextArea('description', $page->description,'Описание страницы', 'description', '', 80);
	//	$form->addText('keywords', $page->keywords,'ключевые слова', 'keywords', '', 30);

	//	$pFile = new fileProcess($this->modName);
	//	$fileBody = $pFile->update('edit', $page->id);
	//	$form->addInputs($fileBody);

		$form->addSubmit('submit', 'сохранить', '', 'btn btn-success btn-xs');
		$form->getBody($Container,'xml'); // $this->xml = $form->getBody('xml');
		#$this->xml->save('form.xml');
		return $form;
	}

	public function viewList($collect) {
		#$this->Log->addToLog('Вход', __LINE__, __METHOD__);
		$this->pXSL[] = RIVC_ROOT.'layout/'.$this->modName.'/'.$this->modName.'.viewlist.xsl';
		$Container = $this->newContainer($this->modName);
		$itemConteiner = $this->addToNode($Container, 'items','');
		foreach ($collect as $item) {
			$this->arrToXML($item, $itemConteiner, 'item');
			//$this->addAttr('name', $item['name'], $pageElement);
		}
		return true;
	}

	public function viewPage (pageItem $item) {
		$this->pXSL[] = RIVC_ROOT.'layout/'.$this->sysMod->layoutPref.'/'.$this->sysMod->defModName.'.view.xsl';
		$Container = $this->newContainer($this->modName);
		$itemConteiner = $this->addToNode($Container, 'item','');
		$data = $item->toArray();
		foreach ($data as $key => $val) {
				$this->addToNode($itemConteiner, $key, stripslashes($val));
		}
	}
	public function viewIndexPage (pageItem $item) {
		$this->pXSL[] = RIVC_ROOT.'layout/'.$this->sysMod->layoutPref.'/'.$this->sysMod->layoutPref.'.view.xsl';
		$Container = $this->newContainer('index');
		$itemConteiner = $this->addToNode($Container, 'item','');
		#$item->content.= '<![CDATA'.rn.$item->content.rn.']]>';
		#$this->arrToXML($item->toArray(), $Container, 'item');
		$data = $item->toArray();
		foreach ($data as $key => $val) {
			if ($key == 'content') {
				$node = $this->addToNode($itemConteiner, $key,'');
				$CDATA = $this->xml->createCDATASection(rn.$val.rn);
				$node->appendChild($CDATA);
			} else $this->addToNode($itemConteiner, $key, $val);
		}

	}

}
