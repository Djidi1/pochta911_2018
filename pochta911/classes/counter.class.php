<?php
# VAR
class counterItem extends module_item {
	public $id;
	public $IP;
	public $module;
	public $module_params;
	public $URL;
	public $screen_size;
	public $os;
	public $browser;
	public $datetime;
	public $unique;
	public $unique_code;
	public $Referer;

	# constructor
	public function __construct($Params, $prefix = '') {
		parent::__construct();
		
		if ($prefix != '') $prefix.='_';		
		if (isset($Params[$prefix.'id'])) $this->id = $Params[$prefix.'id']; else $this->id = 0;
		if (isset($Params[$prefix.'IP'])) $this->IP = $Params[$prefix.'IP']; else $this->IP = 'undefined';
		if (isset($Params[$prefix.'module'])) $this->module = $Params[$prefix.'module']; else $this->module = 0;
		if (isset($Params[$prefix.'module_params'])) $this->module_params = $Params[$prefix.'module_params']; else $this->module_params = '';
		if (isset($Params[$prefix.'URL'])) $this->URL = $Params[$prefix.'URL']; else $this->URL = 0;
		if (isset($Params[$prefix.'screen_size'])) $this->screen_size = $Params[$prefix.'screen_size']; else $this->screen_size = '';
		if (isset($Params[$prefix.'os'])) $this->os = $Params[$prefix.'os']; else $this->os = '';
		if (isset($Params[$prefix.'browser'])) $this->browser = $Params[$prefix.'browser']; else $this->browser = '';
		if (isset($Params[$prefix.'datetime'])) $this->datetime = $Params[$prefix.'datetime']; else $this->datetime = '';
		if (isset($Params[$prefix.'unique'])) $this->unique = $Params[$prefix.'unique']; else $this->unique = -1;
		if (isset($Params[$prefix.'unique_code'])) $this->unique_code = $Params[$prefix.'unique_code']; else $this->unique_code = '';
		if (isset($Params[$prefix.'Referer'])) $this->Referer = $Params[$prefix.'Referer']; else $this->Referer = '';

		$this->notInsert['id'] = 1;
	}
	# toArray
	public function toArray() {
		$Params['id'] = $this->id;
		$Params['IP'] = $this->IP;
		$Params['module'] = $this->module;
		$Params['module_params'] = $this->module_params;
		$Params['URL'] = $this->URL;
		$Params['screen_size'] = $this->screen_size;
		$Params['os'] = $this->os;
		$Params['browser'] = $this->browser;
		$Params['datetime'] = $this->datetime;
		$Params['unique'] = $this->unique;
		$Params['unique_code'] = $this->unique_code;
		$Params['Referer'] = $this->Referer;
		return $Params;
	}
	# fromSQL
	/*
	 $Params['id'] = $row['ctr_id'];
	 $Params['IP'] = $row['ctr_IP'];
	 $Params['module'] = $row['ctr_module'];
	 $Params['module_params'] = $row['ctr_module_params'];
	 $Params['URL'] = $row['ctr_URL'];
	 $Params['screen_size'] = $row['ctr_screen_size'];
	 $Params['os'] = $row['ctr_os'];
	 $Params['browser'] = $row['ctr_browser'];
	 $Params['datetime'] = $row['ctr_datetime'];
	 $Params['unique'] = $row['ctr_unique'];
	 $Params['unique_code'] = $row['ctr_unique_code'];

	 */
	# to New
	/*
	 $Params['id'] = $this->Vals->getVal('id', 'POST', 'string');
	 $Params['IP'] = $this->Vals->getVal('IP', 'POST', 'string');
	 $Params['module'] = $this->Vals->getVal('module', 'POST', 'string');
	 $Params['module_params'] = $this->Vals->getVal('module_params', 'POST', 'string');
	 $Params['URL'] = $this->Vals->getVal('URL', 'POST', 'string');
	 $Params['screen_size'] = $this->Vals->getVal('screen_size', 'POST', 'string');
	 $Params['os'] = $this->Vals->getVal('os', 'POST', 'string');
	 $Params['browser'] = $this->Vals->getVal('browser', 'POST', 'string');
	 $Params['datetime'] = $this->Vals->getVal('datetime', 'POST', 'string');
	 $Params['unique'] = $this->Vals->getVal('unique', 'POST', 'string');
	 $Params['unique_code'] = $this->Vals->getVal('unique_code', 'POST', 'string');


	 # UPDATE
	 `id` = \'%1$s\', `IP` = \'%2$s\', `module` = \'%3$s\', `module_params` = \'%4$s\', `URL` = \'%5$s\', `screen_size` = \'%6$s\', `os` = \'%7$s\', `browser` = \'%8$s\', `datetime` = \'%9$s\', `unique` = \'%10$s\', `unique_code` = \'%11$s\'
	 # UPDATE_VALS
	 $Params['id'], $Params['IP'], $Params['module'], $Params['module_params'], $Params['URL'], $Params['screen_size'], $Params['os'], $Params['browser'], $Params['datetime'], $Params['unique'], $Params['unique_code']
	 SELECT # SELECT
	 `ctr`.`id` AS `ctr_id`, `ctr`.`IP` AS `ctr_IP`, `ctr`.`module` AS `ctr_module`, `ctr`.`module_params` AS `ctr_module_params`, `ctr`.`URL` AS `ctr_URL`, `ctr`.`screen_size` AS `ctr_screen_size`, `ctr`.`os` AS `ctr_os`, `ctr`.`browser` AS `ctr_browser`, `ctr`.`datetime` AS `ctr_datetime`, `ctr`.`unique` AS `ctr_unique`, `ctr`.`unique_code` AS `ctr_unique_code` FROM TABLE_NAME

	 }SELECT_pref
	 # SELECT_pref
	 `'.$pref.'`.`id` AS `'.$pref.'_id`, `'.$pref.'`.`IP` AS `'.$pref.'_IP`, `'.$pref.'`.`module` AS `'.$pref.'_module`, `'.$pref.'`.`module_params` AS `'.$pref.'_module_params`, `'.$pref.'`.`URL` AS `'.$pref.'_URL`, `'.$pref.'`.`screen_size` AS `'.$pref.'_screen_size`, `'.$pref.'`.`os` AS `'.$pref.'_os`, `'.$pref.'`.`browser` AS `'.$pref.'_browser`, `'.$pref.'`.`datetime` AS `'.$pref.'_datetime`, `'.$pref.'`.`unique` AS `'.$pref.'_unique`, `'.$pref.'`.`unique_code` AS `'.$pref.'_unique_code`
	 */
}

class counterCollection extends module_collection {
	
	public $Browsers;
	public $OSs;
	public $Screens;
	public $Unique;
	protected $isCountable;	
	
	/**
	 * 
	 * @param $isCountable Авто подсчет
	 * @return unknown_type
	 */
	public function __construct($isCountable = false) {
		parent::__construct();
		$this->Browsers = array();
		$this->OSs = array();
		$this->Screens = array();
		$this->Unique = 0;
		$this->isCountable = $isCountable;
	}
	
	public function getCountable() { return $this->isCountable; }
	public function add(counterItem $item) {
		parent::add($item);
		if ($this->isCountable) {
			if (!isset($this->Browsers[$item->browser])) $this->Browsers[$item->browser] = 1; else $this->Browsers[$item->browser]++;
			if (!isset($this->OSs[$item->os])) $this->OSs[$item->os] = 1; else $this->OSs[$item->os]++;
			if (!isset($this->Screens[$item->screen_size])) $this->Screens[$item->screen_size] = 1; else $this->Screens[$item->screen_size]++;
			if ($item->unique == 1) $this->Unique++;
		}
	}
	
	public function addItem($Params) {
		$item = new counterItem($Params);
		$this->add($item);
	}
	
	public function countsByField ($fieldName) {
		$counts = array();
		foreach($this as $item) {
			if (!isset($item->$fieldName)) throw new Exception('fullCountSubdivision: Отсутствует поле ['.$fieldName.'] '); 
			if (!isset($counts[$item->$fieldName])) $counts[$item->$fieldName] = 1;
			else $counts[$wsd->$fieldName]++;
		}
		return $counts;
	}
}


# model
class counterModel extends module_model {

	public function __construct ($modName) { parent::__construct($modName); }
	public function add(counterItem $item) {
		$res = array();
		$res = $item->toInsert();
		$sql = 'INSERT INTO '.TAB_PREF.'counter ('.$res[0].') VALUES('.$res[1].')';
		$q = array_merge(array(0=>$sql),$res[2]);
		if(!$this->query($q)) { exit($this->sql); };
		$id = $this->insertID();
		$item->id = $id;
		return true;
	}

	public function get($id) {
		if ($id <= 0) return false;
	}
	public function update(counterItem $item) {
		if ($item->id <= 0) return false;
	}
	
	public function getList($limCount, $page, $filters, $orderField = 'id', $orderType = 'DESC') {

	}
	/**
	 * 
	 * @param $prWith MUST BE SQL DATETIME
	 * @param $prToOn MUST BE SQL DATETIME
	 * @param $filters
	 * @param $orderField
	 * @param $orderType
	 * @return counterCollection
	 */
	public function getPeriod($prWith,$prToOn, $filters, $orderField= 'id', $orderType= 'DESC') {
		if($prWith == '' || $prToOn == '') return new counterCollection();
		$sql = 'SELECT `ctr`.`id` AS `ctr_id`, `ctr`.`IP` AS `ctr_IP`, `ctr`.`module` AS `ctr_module`, `ctr`.`module_params` AS `ctr_module_params`, `ctr`.`URL` AS `ctr_URL`, `ctr`.`screen_size` AS `ctr_screen_size`, `ctr`.`os` AS `ctr_os`, `ctr`.`browser` AS `ctr_browser`, `ctr`.`datetime` AS `ctr_datetime`, `ctr`.`unique` AS `ctr_unique`, `ctr`.`unique_code` AS `ctr_unique_code`, `ctr`.`Referer` AS `ctr_Referer` 
				FROM `counter` as `ctr`
				WHERE ctr.`datetime` >= \'%1$s\' AND ctr.`datetime` <= \'%2$s\'
				';		
		$this->query($sql, $prWith, $prToOn);
		//stop($this->sql);
		$counterCollection = new counterCollection(true);
		while($row = $this->fetchRowA()) {
			$counterItem = new counterItem($row,'ctr');
			$counterCollection->add($counterItem);			
		}
		return $counterCollection;
	}
	
	public function del(counterItem $item) {
		if ($item->id <= 0) return false;
	}
	public function clear($type) {
		
	}
}

# Process
class counterProcess extends module_process {
	private $updated;
	public function __construct ($modName) {
		
		global $values, $User, $LOG,$System;
		$this->Vals = $values;
		$this->System = $System;
		$this->modName = $modName;
		$this->User = $User;
		$this->Log = $LOG;
		$this->action = false;
		/* actionDefault - должно быбираться из БД!!! */ $this->actionDefault = '';
		$this->actionsColl = new actionColl();
		$this->nModel = new counterModel($modName);
		$sysMod = $this->nModel->getSysMod();
		$this->sysMod = $sysMod;
		$this->mod_id = $sysMod->id;
		$this->nView = new counterView($this->modName, $this->sysMod);
		
		// $this->regAction('line', 'Список', ACTION_GROUP);
		$this->regAction('new', 'Форма создания', ACTION_GROUP);
		$this->regAction('add', 'Вставка в БД', ACTION_GROUP);
		$this->regAction('edit', 'Форма редактирования', ACTION_GROUP);
		$this->regAction('update', 'Обновить', ACTION_GROUP);
		$this->regAction('view', 'Просмотр', ACTION_GROUP);
		$this->regAction('viewLine', 'Список', ACTION_GROUP);
		$this->regAction('del', 'Удаление', ACTION_GROUP);
		$this->regAction('clear', 'Очистка таблиц', ACTION_GROUP);
		$this->regAction('admin', 'Список для админки', ACTION_GROUP);
		$this->regAction('viewStat', 'Список для админки', ACTION_GROUP);
		$this->registerActions();
	}
	public function update($_action = false) {
		$this->updated = false;
		
		if ($_action) $this->action = $_action;
		$action = $this->actionDefault;
		if ($this->action) $action = $this->action;
		else $action = $this->checkAction();
		if (!$action) {
			$this->Vals->URLparams($this->sysMod->defQueryString);
			$action = $this->actionDefault;
		}
		$user_id = $this->User->getUserID();
		$user_right = $this->User->getRight($this->modName, $action);
		if ($user_right == 0 && $user_id > 0) {
			
			$p = array('У Вас нет прав для использования модуля', '$this->modName'=>$this->modName, 'action'=>$action, 'user_id'=>$user_right, 'user_right'=>$user_right);
			$this->nView->viewError('У Вас нет прав на это действие', 'Предупреждение');
			$this->Log->addError($p, __LINE__, __METHOD__);
			$this->updated = true;
			return;
		}
		if ($user_right == 0 && $user_id == 0) {
			//stop($user_right.' '.$user_id);
			//$this->nView->viewLogin('Заголовок','',$user_id, array(),array());
			$this->updated = true;
			return;
		}
		if ($user_id > 0) {
			$this->User->nView->viewLoginParams('Заголовок','',$user_id, array(),array());
		}
		if ($action == 'new') {



		}
		
		if ($action == 'add') {
				
			if ($this->Vals->isVal('URL','GET')) {
				$Params['IP'] = $_SERVER["REMOTE_ADDR"];

				$Params['URL'] = $this->Vals->getVal('URL', 'GET', 'string');
				$Params['screen_size'] = $this->Vals->getVal('screen_size', 'GET', 'string');
				$Params['os'] = $this->Vals->getVal('os', 'GET', 'string');
				$Params['browser'] = $this->Vals->getVal('browser', 'GET', 'string');
				$Params['Referer'] = $this->Vals->getVal('Referer', 'GET', 'string');
				$Params['datetime'] = date('Y-m-d H:i:s');

				//[HTTP_HOST]
				//$this->Vals->setValTo('unique_code', '', 'COOKIE');
				if ($this->Vals->isVal('unique_code','COOKIE') && $this->Vals->getVal('unique_code', 'COOKIE', 'string') != '') {
					$Params['unique'] = 0;
					$Params['unique_code'] = $this->Vals->getVal('unique_code', 'COOKIE', 'string');
				} else {
					$Params['unique'] = 1;
					$Params['unique_code'] = '';
					// $unique_code = '';
					// $this->Vals->setVal('');
				}
				$t = str_replace('http://','',$Params['URL']);
				$url_s = split('/', $t);
				//stop($url_s);
				if (count($url_s) > 0) {
					$module_index = 0;
					if ($url_s[0] == $_SERVER['HTTP_HOST'] || $url_s[0] == '') $module_index = 1;
					if (isset($url_s[$module_index])) {
						$Params['module'] = $url_s[$module_index];
					} else {
						$Params['module'] = 'index';
					}
				} else  {
					$Params['module'] = 'index';
				}
				$Params['module_params'] = join('&',array_slice($url_s, 1));

				// stop($Params);
				
				$counterItem = new counterItem($Params);
				//stop($counterItem);
				if($this->nModel->add($counterItem)) {
					$unique_code = $counterItem->id;
					if($Params['unique_code'] == '') $this->Vals->setValTo('unique_code', $unique_code, 'COOKIE');
				} else exit('error SQL');
				exit('ok');
			} else {
				exit('error');
			}

		}
			
		if ($action == 'edit') {

			

		}
		if ($action == 'update') {



		}
		if ($action == 'del') {



		}
		if ($action == 'view') {

			$item_id = $this->Vals->getVal('view','GET','integer');
			$item = $this->nModel->get($item_id);
			if ($item) {
				$this->nView->view($item);
			}
			else {
				$this->nView->viewError(array('Запись не найдена или имеет ошибки!'),0);
			}
			$this->updated = true;


		}
		
		
		if ($action == 'viewStat') {
			$pr_with = $this->Vals->getVal('period_with','POST','string');
			$pr_toOn = $this->Vals->getVal('period_toOn','POST','string');
				
			if ($pr_with != '' && $pr_toOn != '') {
				$collection = $this->nModel->getPeriod(dateToDATETIME($pr_with), dateToDATETIME($pr_toOn),array());
				$this->nView->viewPeriod($collection, $pr_with, $pr_toOn);
				$this->updated = true;
			} else {
				$d = (6 + date('N'));
				$tw = time() - (60*60*24*$d);
				$to = $tw + (60*60*24*(4+7));
				$pr_with = date('d.m.Y', $tw);
				$pr_toOn = date('d.m.Y', $to);
				$this->nView->viewGetPeriod($pr_with, $pr_toOn);
				$this->updated = true; 
			}

		}
	if ($action == 'viewLine' || $this->updated == false) {



		}
		if ($action == 'clear') {



		}
		if ($action == 'admin') {



		}
	}
		
}

# View
class counterView extends module_View {
	public function __construct ($modName, $sysMod) {
		parent::__construct($modName, $sysMod);
		$this->pXSL = array();
	}
	public function viewNew(counterItem $item) {

		$Container = $this->newContainer('counter');
		$itemConteiner = $this->addToNode($Container, 'item','');
		$this->pXSL[] = RIVC_ROOT.'layout/'.$this->modName.'/'.$this->modName.'.viewNew.xsl';
		
		$js = '';

		try {
			$this->arrToXML($item->toArray(), $itemConteiner, 'items', array());
		} catch (Exception $e) {
			stop ('Caught exception: '.$e->getMessage(), "\n");
		}
		//screen.height, screen.width
	}
	
	public function viewGetPeriod($pr_with, $pr_toon) {
		$this->pXSL[] = RIVC_ROOT.'layout/form.xsl';
		$Container = $this->newContainer('form');
		$form = new CFormGenerator('wReport', SITE_ROOT.$this->modName.'/viewStat-1/', 'POST', 0);
		$form->addHidden('viewStat', '1', 'viewStat');
		//$form->addHidden('user_id', $user_id, 'user_id');
		$form->addMessage ('msg1', 'Период', 'msg1');
		$form->addText('period_with', $pr_with,'С: ', 'period_with', '', 30);
		$form->addText('period_toOn', $pr_toon,'По: ', 'period_toOn', '', 30);
		$form->addSubmit('submit', 'далее', 'submit_counter', 'submit_counter');
		$form->getBody($Container,'xml');
		return $form;
	}
	
	public function viewPeriod(counterCollection $counterCollection, $pr_with, $pr_toOn) {
		$Container = $this->newContainer('counter');
		$itemConteiner = $this->addToNode($Container, 'item','');
		$this->pXSL[] = RIVC_ROOT.'layout/'.$this->modName.'/viewPreriod.xsl';

		$js = '';
		$Params['periodWith'] = $pr_with;
		$Params['periodToOn'] = $pr_toOn;
		$Params['Browsers'] = $counterCollection->Browsers;
		$Params['OSs'] = $counterCollection->OSs;
		$Params['Screens'] = $counterCollection->Screens;
		$Params['Unique'] = $counterCollection->Unique;
		$Params['Count'] = $counterCollection->count();

		try {
			$this->arrToXML($Params, $itemConteiner, 'stats', array());
			foreach($counterCollection as $item) {
				try {
					$this->arrToXML($item->toArray(), $itemConteiner, 'items', array('URL','module_params'));
				} catch (Exception $e) {
					stop ('Caught exception: '.$e->getMessage(), "\n");
				}
			}
		} catch (Exception $e) {
			stop ('Caught exception: '.$e->getMessage(), "\n");
		}
	}

}
/*
 <p>ctr_id: <input name="ctr_id" id="ctr_id" value="" /><p>
 <p>ctr_IP: <input name="ctr_IP" id="ctr_IP" value="" /><p>
 <p>ctr_module: <input name="ctr_module" id="ctr_module" value="" /><p>
 <p>ctr_module_params: <input name="ctr_module_params" id="ctr_module_params" value="" /><p>
 <p>ctr_URL: <input name="ctr_URL" id="ctr_URL" value="" /><p>
 <p>ctr_screen_size: <input name="ctr_screen_size" id="ctr_screen_size" value="" /><p>
 <p>ctr_os: <input name="ctr_os" id="ctr_os" value="" /><p>
 <p>ctr_browser: <input name="ctr_browser" id="ctr_browser" value="" /><p>
 <p>ctr_datetime: <input name="ctr_datetime" id="ctr_datetime" value="" /><p>
 <p>ctr_unique: <input name="ctr_unique" id="ctr_unique" value="" /><p>
 <p>ctr_unique_code: <input name="ctr_unique_code" id="ctr_unique_code" value="" /><p>

 */
?>