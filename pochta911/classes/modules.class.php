<?php

define ('ACTION_PUBLIC', 1);
define ('ACTION_AUTCH' , 2);
/**
 *
 * @var Доступ по группе
 */
define ('ACTION_GROUP' , 3);
/**
 *
 * @param $modName
 * @return addonsSet
 */

function modulePreload($modName) {
	global $LOG;
	$database = new database();
	$sql = 'SELECT
			m.*, 
			mas.query_string, 
			mad.codename as addon_codename, mad.title as addon_title, mad.id as addon_id,
			m2.id as addon_module_id, 
			m2.codename as addon_module_codename, 
			m2.name as addon_module_name,
			m2.processName as addon_module_processName,
			m2.`xsl` as addon_module_xsl,
			m.`layoutPref` as addon_module_layoutPref,			
			m2.defModName as addon_module_defModName,
			m2.defAction as addon_module_defAction,
			m2.defQueryString as  addon_module_defQueryString,
			m2.isSystem as addon_module_isSystem			
			FROM '.TAB_PREF.'modules m
			LEFT JOIN '.TAB_PREF.'module_addons_assign mas ON mas.addon_id = m.addons
			LEFT JOIN '.TAB_PREF.'module_addons mad ON mad.id = mas.addon_id
			LEFT JOIN '.TAB_PREF.'modules m2 ON mas.mod_id = m2.id
			WHERE m.`codename` = \'%1$s\'';
	$database->query($sql, $modName);
  //	if ($database->numRows() == 0) return false;
	$modules = array();
	$a = false;
	
	$addonsItem = false;
	
	while($row = $database->fetchRowA()) {
		if(!$a) {
			$Params['addon_id'] = $row['addon_id'];
			$Params['addon_codename'] = $row['addon_codename'];
			$Params['addon_title'] = $row['addon_title'];
			//$Params['addon_QueryString'] = $row['query_string'];
			$Params['module_id'] = $row['id'];
			$Params['module_codename'] = $row['codename'];
			$Params['module_defModName'] = $row['defModName'];
			$Params['module_processName'] = $row['processName'];
			$Params['module_defAction'] = $row['defAction'];
			$Params['module_defQueryString'] = $row['defQueryString'];
			$Params['module_defXSL'] = $row['xsl'];
			$Params['module_isSystem'] = $row['isSystem'];
			$addonsItem = new addonsSet($Params);
			$modules = new modsetColl();
			$addonsItem->modules = $modules;
			$a = true;
		}

		if ($row['addon_module_id'] > 0) {
			//$LOG->addToLog(array('загрузка модуля', $row['addon_module_codename'], $row['addon_module_id']), __LINE__, 'preload');
			$Params['id'] = $row['addon_module_id'];
			$Params['name'] = $row['addon_module_name'];
			$Params['codename'] = $row['addon_module_codename'];
			$Params['processName'] = $row['addon_module_processName'];
			$Params['xsl'] = $row['addon_module_xsl'];
			$Params['defModName'] = $row['addon_module_defModName'];
			//		$Params['parentMod'] = $row['addon_module_parentMod'];
			$Params['layoutPref'] = $row['addon_module_layoutPref'];
			//		$Params['addons'] = $row['addon_module_addons'];
			$Params['defAction'] = $row['addon_module_defAction'];
			if($row['query_string'] != '') $dqs = $row['query_string']; else $dqs = $row['addon_module_defQueryString'];
			$Params['defQueryString'] = $dqs;
			$Params['isSystem'] = $row['addon_module_isSystem'];
			$modules->addItem($Params);
		}
	}
	//$LOG->addToLog($addonsItem->modules->count(), __LINE__, 'preload');
	return $addonsItem ;
}



abstract class module_item {
	protected $id;
	protected $notInsert;  /* ключи которые не учавствуют в стандартной функции вставки. Создается в конструкторе */

	public function __construct() {
		#if ($this->id === NULL) exit ('Ошибка при создании модели ITEM');
		$this->notInsert = array();
		$this->notInsert['notInsert'] = 1;
		$this->notInsert['id'] = 1;
	}

	public function setVal($name, $val) { $this->$name = $val; }
	public function setVals($vals) { foreach ($vals as $key=>$val) $this->setVal($key,$val); }
	#  public function getVal($val) { if(isset($this->$val)) return $this->$val; else return NULL; }

	/**
	 * Подготовка к вставке moduleItem
	 * @return array($i, $v, $p)
	 */
	public function toInsert() {
		$a = true;
		$i = '';
		$v = '';
		$p = array();
		//stop(DB_USE);
		if(DB_USE == 'mySQL') {
			$sql_e1 = '`';
			$sql_e2 = '`';
		} elseif (DB_USE == 'MSSQL') {
			$sql_e1 = '[';
			$sql_e2 = ']';
		}
		$index = 1;
		$array_keys = $this->toArray();
		#stop($this->notInsert,0);
		foreach ($array_keys as $key => $val) {
			if (array_key_exists($key, $this->notInsert)) continue;
			if (!$a) { $i.=', '; $v.= ','; $a = true; }
			$a = false;
			$i.= $sql_e1.$key.$sql_e2.' ';
			$p[$index] = $val;
			switch (gettype($val)) {
				case 'string': $v.= '\'%'.$index.'$s\''; break;
				case 'integer': $v.= '%'.$index.'$u'; break;
				default: $v.= '\'%'.$index.'$s\'';
			}
			$index++;
		}
		return array($i, $v, $p);
	}
	public function getID() {return $this->id; }
	public function toXML($node, $conName) {
		/*$xml = new DOMDocument('1.0', 'utf-8');*/
//		$root = $xml->appendChild($xml->createElement('itm'));
//		$item = $root->appendChild($xml->createElement('item'));
	}
	/**
	 * Возвращает Класс в виде Массива
	 * @return unknown_type
	 */
	abstract public function toArray();		

	/**
	 * 
	 * @param $thisTable Таблица(1) для соединения
	 * @param $pref Префикс таблицы(1)
	 * @param $pref2 Префикс таблицы(2)
	 * @param $type Тип Соединения (INNER LEFT RIGHT)
	 * @param $thisField Поле таблицы(1) для соединения
	 * @param $field2 Поле таблицы (2)
	 * @param $addr Дополнительные параметры (в виде SQL)
	 * @return sql
	 */
	public function toJoin($thisTable, $pref, $pref2, $type, $thisField, $field2, $addr = '') {
		$sql = $type.' JOIN '.$thisTable.' as '.$pref.' ON '.$pref.'.'.$thisField.' = '.$pref2.'.'.$field2.' '.$addr.rn;		
		$sql.= rn;
		return $sql;
	}
	
	public function addonJoin() {
		$sql = ''.rn;
		return $sql; 
	}
}

class moduleAccess extends module_item {
	public $id;
	public $action_id;
	public $group_id;
	public $group_name;
	public $user_id;
	public $access;
	public $module_id;

	public function __construct ($Params) {
		global $LOG;
		parent::__construct();
		if (isset($Params['group_id'])) $this->group_id = $Params['group_id']; else $this->group_id = 0;
		if (isset($Params['group_name'])) $this->group_name = $Params['group_name']; else $this->group_name = 0;
		if (isset($Params['id'])) $this->id = $Params['id']; else $this->id = $this->group_id;
		if (isset($Params['action_id'])) $this->action_id = $Params['action_id']; else $this->action_id = 0;
		if (isset($Params['user_id'])) $this->user_id = $Params['user_id']; else $this->user_id = 0;
		if (isset($Params['access'])) $this->access = $Params['access']; else $this->access = 0;
		if (isset($Params['module_id'])) $this->module_id = $Params['module_id']; else $this->module_id = 0;
		$this->notInsert['group_name'] = 1;
	}

	public function toArray() {
		$Params['id'] = $this->id;
		$Params['group_id'] = $this->group_id;
		$Params['group_name'] = $this->group_name;
		$Params['action_id'] = $this->action_id;
		$Params['user_id'] = $this->user_id;
		$Params['access'] = $this->access;
		$Params['module_id'] = $this->module_id;
		return $Params;
	}
}

class mcColl extends module_collection {
	public function __construct () {
		parent::__construct();
	}

	public function addItem($params) {
		$item = new moduleAccess($params);
		$this->add($item);
	}
}

# VAR
/**
 * 
 * @author poltavcev
 * prefix mREX
 */
class moduleRightsExItem extends module_item {
	public $id;
	public $mod_id;
	public $mod_name;
	public $right_name;
	public $right_title;
	public $access;

	# constructor
	public function __coustruct($Params, $prefix = '') {
		parent::__construct();
		if ($prefix != '') $prefix.='_';
		if (isset($Params[$prefix.'id'])) $this->id = $Params[$prefix.'id']; else $this->id = 0;
		if (isset($Params[$prefix.'mod_id'])) $this->mod_id = $Params[$prefix.'mod_id']; else $this->mod_id = 0;
		if (isset($Params[$prefix.'mod_name'])) $this->mod_name = $Params[$prefix.'mod_name']; else $this->mod_name = 0;
		if (isset($Params[$prefix.'right_name'])) $this->right_name = $Params[$prefix.'right_name']; else $this->right_name = 0;
		if (isset($Params[$prefix.'right_title'])) $this->right_title = $Params[$prefix.'right_title']; else $this->right_title = 0;
		if (isset($Params[$prefix.'access'])) $this->access = $Params[$prefix.'access']; else $this->access = 0;

		//$this->notInsert['id'] = 1;
	}
	# toArray
	public function toArray() {
		$Params['id'] = $this->id;
		$Params['mod_id'] = $this->mod_id;
		$Params['mod_name'] = $this->mod_name;
		$Params['right_name'] = $this->right_name;
		$Params['right_title'] = $this->right_title;
		$Params['access'] = $this->access;
		return $Params;
	}
	
	public function toSelect($pref) { // DATE_FORMAT(`'.$pref.'`.`id`,\'%%d.%%m.%%Y\')
		$sql = '`'.$pref.'`.`id` AS `'.$pref.'_id`, `'.$pref.'`.`mod_id` AS `'.$pref.'_mod_id`, `'.$pref.'`.`mod_name` AS `'.$pref.'_mod_name`, `'.$pref.'`.`right_name` AS `'.$pref.'_right_name`, `'.$pref.'`.`right_title` AS `'.$pref.'_right_title`, `'.$pref.'`.`access` AS `'.$pref.'_access`';
		$sql.= rn;
		return $sql;
	}

	# fromSQL
	/*
	 $Params['id'] = $row['mREX_id'];
	 $Params['mod_id'] = $row['mREX_mod_id'];
	 $Params['mod_name'] = $row['mREX_mod_name'];
	 $Params['right_name'] = $row['mREX_right_name'];
	 $Params['right_title'] = $row['mREX_right_title'];
	 $Params['access'] = $row['mREX_access'];

	 */
	# to New
	/*
	 $Params['id'] = $this->vals->getVal('id', 'POST', 'string');
	 $Params['mod_id'] = $this->vals->getVal('mod_id', 'POST', 'string');
	 $Params['mod_name'] = $this->vals->getVal('mod_name', 'POST', 'string');
	 $Params['right_name'] = $this->vals->getVal('right_name', 'POST', 'string');
	 $Params['right_title'] = $this->vals->getVal('right_title', 'POST', 'string');
	 $Params['access'] = $this->vals->getVal('access', 'POST', 'string');


	 # UPDATE
	 `id` = \'%1$s\', `mod_id` = \'%2$s\', `mod_name` = \'%3$s\', `right_name` = \'%4$s\', `right_title` = \'%5$s\', `access` = \'%6$s\'
	 # UPDATE_VALS
	 $Params['id'], $Params['mod_id'], $Params['mod_name'], $Params['right_name'], $Params['right_title'], $Params['access']
	 SELECT # SELECT
	 `mREX`.`id` AS `mREX_id`, `mREX`.`mod_id` AS `mREX_mod_id`, `mREX`.`mod_name` AS `mREX_mod_name`, `mREX`.`right_name` AS `mREX_right_name`, `mREX`.`right_title` AS `mREX_right_title`, `mREX`.`access` AS `mREX_access` FROM TABLE_NAME

	 }SELECT_pref
	 # SELECT_pref
	 `'.$pref.'`.`id` AS `'.$pref.'_id`, `'.$pref.'`.`mod_id` AS `'.$pref.'_mod_id`, `'.$pref.'`.`mod_name` AS `'.$pref.'_mod_name`, `'.$pref.'`.`right_name` AS `'.$pref.'_right_name`, `'.$pref.'`.`right_title` AS `'.$pref.'_right_title`, `'.$pref.'`.`access` AS `'.$pref.'_access`
	 */
}

class moduleRightsExCollection extends module_collection {
	public function __construct() {
		parent::__construct();

	}
	public function addItem($Params) {
		$item = new moduleRightsExItem($Params);
		$this->add($item);
	}
}

class moduleAction extends module_item {
	public $id;
	public $mod_id;
	public $mod_name;
	public $action_name;
	public $action_title;
	public $access;  /* доступ */
	/** преднадлежность действия к группе администратьоров */
	public $group_adm;

	/** коллекция групп */
	public $groups;

	public function __construct ($Params) {
		global $LOG;
		parent::__construct();
		if (isset($Params['id'])) $this->id = $Params['id']; else $this->id = 0;
		#if (!$this->id) $this->id = time();
		if (isset($Params['mod_id'])) $this->mod_id = $Params['mod_id']; else $this->mod_id = 0;
		if (isset($Params['action_name'])) $this->action_name = $Params['action_name']; else $this->action_name = 0;
		if (isset($Params['action_title'])) $this->action_title = $Params['action_title']; else $this->action_title = 0;
		if (isset($Params['access'])) $this->access = $Params['access']; else $this->access = 0;

		if (isset($Params['group_adm'])) $this->group_adm = $Params['group_adm']; else $this->group_adm = 0;
		if (isset($Params['groups'])) $this->groups = $Params['groups']; else $this->groups = new mcColl();
		if (isset($Params['mod_name'])) $this->mod_name = $Params['mod_name']; else $this->mod_name = 0;

		$this->notInsert['group_adm'] = 1;
		$this->notInsert['groups'] = 1;
		$this->notInsert['mod_name'] = 1;
	}

	public function toArray() {
		$Params['id'] = $this->id;
		$Params['mod_id'] = $this->mod_id;
		$Params['action_name'] = $this->action_name;
		$Params['action_title'] = $this->action_title;
		$Params['access'] = $this->access;
		$Params['mod_name'] = $this->mod_name;

		$Params['group_adm'] = $this->group_adm;
		$Params['groups'] = $this->groups;
		return $Params;
	}
}

class actionColl extends module_collection {
	public function __construct () {
		parent::__construct();
	}

	public function addItem($params) {
		$item = new moduleAction($params);
		$this->add($item);
	}
}


abstract class module_collection extends ArrayObject {

	public $curItem;
	protected $double;

	public function __construct() {
		parent::__construct();
	}

	public function add(module_item $item) {
		global $LOG;
		$this->double = false;
		if(!$item->getID()) $index = $this->count(); else $index = $item->getID();
		//if (!$item->getID()) exit(debug_print_backtrace());
		if ($this->offsetExists($index)) $this->double=true; 
		$this->offsetSet($index, $item);
		$this->curItem = $this->offsetGet($index);
		if ($index != $item->getID()) {
			//    	$LOG->addError(array('Индексы объекта коллекции не совпадают ', 'index'=>$index,'item->id'=>$item->getID(), 'class'=>get_class($item)),__LINE__,__METHOD__);
			return false;
		}
		return true;
	}

	public function addItem($params) {
//		$item = new module_item($params, $auto_id);
//		$this->add($item);
	}

	public function count() {
		return parent::count();
	}

	public function del($item_id) {
		$this->offsetUnset($item_id);
	}

	public function get($item_id) {
		if (!$item_id) exit(debug_print_backtrace());
		if ($this->offsetExists($item_id)) {
			$this->curItem = $this[$item_id];
			return $this->curItem;
		}
		return false;
	}

	public function getListID () {
		$idList = array();
		foreach($this as $item) {
			$idList[] = $item->getID();
		}
	}
	/**
	 * Получить первый элемент, т.к. первый элемент не обязательно "0"
	 */
	public function getFirst() {
		reset($this);
		list($key, $module_item) = each($this);
		return $module_item;
	}
}

class addonsSet extends module_item {
	public $addon_id;
	public $addon_codename;
	public $addon_title;
	//public $addon_QueryString;
	public $module_id;
	public $module_codename;
	public $module_defModName;
	public $module_processName;
	public $module_defAction;
	public $module_defQueryString;
	public $module_defXSL;
	public $module_isSystem;
	/**
	 * Список модулей
	 * @var array
	 *
	 */
	public $modules;

	public function __construct ($Params) {
		global $LOG;
		if (!is_array($Params) || count($Params) == 0) { return false;}
		parent::__construct();
		if (isset($Params['addon_id'])) $this->addon_id = $Params['addon_id']; else $this->addon_id = -1;
		if (isset($Params['addon_codename'])) $this->addon_codename = $Params['addon_codename']; else $this->addon_codename = -1;
		if (isset($Params['addon_title'])) $this->addon_title = $Params['addon_title']; else $this->addon_title = -1;
		//if (isset($Params['addon_QueryString'])) $this->addon_QueryString = $Params['addon_QueryString']; else $this->addon_QueryString = '';
		if (isset($Params['module_id'])) $this->module_id = $Params['module_id']; else $this->module_id = -1;
		if (isset($Params['module_codename'])) $this->module_codename = $Params['module_codename']; else $this->module_codename = -1;
		if (isset($Params['modules'])) $this->modules = $Params['modules']; else $this->modules = new modsetColl();
		//if (gettype($this->modules) != 'object') exit ('addonsSet->modules Не объект'); else echo 'Ok';
		if (isset($Params['module_defModName'])) $this->module_defModName = $Params['module_defModName']; else $this->module_defModName = -1;
		if (isset($Params['module_processName'])) $this->module_processName = $Params['module_processName']; else $this->module_processName = -1;
		if (isset($Params['module_defAction'])) $this->module_defAction = $Params['module_defAction']; else $this->module_defAction = '';
		if (isset($Params['module_defQueryString'])) $this->module_defQueryString = $Params['module_defQueryString']; else $this->module_defQueryString = -1;
		if (isset($Params['module_defXSL'])) $this->module_defXSL = $Params['module_defXSL']; else $this->module_defXSL = 'page.default.xsl';
		if (isset($Params['module_isSystem'])) $this->module_isSystem = $Params['module_isSystem']; else $this->module_isSystem = 0;
	}

	public function toArray() {
		$Params['addon_id'] = $this->addon_id;
		$Params['addon_codename'] = $this->addon_codename;
		$Params['addon_title'] = $this->addon_title;
		//$Params['addon_QueryString'] = $this->addon_QueryString;
		$Params['module_id'] = $this->module_id;
		$Params['module_codename'] = $this->module_codename;
		$Params['modules'] = $this->modules;
		$Params['module_defModName'] = $this->module_defModName;
		$Params['module_processName'] = $this->module_processName;
		$Params['module_defAction'] = $this->module_defAction;
		$Params['module_defQueryString'] = $this->module_defQueryString;
		$Params['module_defXSL'] = $this->module_defXSL;
		$Params['module_isSystem'] = $this->module_isSystem;

		return $Params;
	}
}

class modsetItem extends module_item {
	public $id; # ID
	public $name;   # Имя модуля
	public $codename; # Уникальное имя модуля
	public $processName; # Класс обработчик Процесс
	public $xsl; # пусть к главной странице
	public $actions; # Коллекция действий

	public $defModName; # Уникальное имя модуля по умолчанию
	public $parentMod;  # родительский модуль
	public $layoutPref; # префик шаблонов

	public $defAction; # действие по умолчанию
	public $defQueryString; # параметры для действия в виде  url параметров a=1&b=xxx&c...
	public $isSystem; # системный модуль

	public $addons; # приссоединяемые модули
	public $addons_name; # название адона
	public $addonsItem; # class addonsItem

	public function __construct ($Params) {
		global $LOG;
		if (!is_array($Params) || count($Params) == 0) { return false;}
		parent::__construct();
		if (isset($Params['id'])) $this->id = $Params['id']; else $this->id = -1;
		if (isset($Params['name'])) $this->name = $Params['name']; else $this->name = 0;
		if (isset($Params['codename'])) $this->codename = $Params['codename']; else $this->codename = 0;
		if (isset($Params['processName'])) $this->processName = $Params['processName']; else $this->processName = 0;
		if (isset($Params['xsl'])) $this->xsl = $Params['xsl']; else $this->xsl = 0;
		if (isset($Params['actions'])) $this->actions = $Params['actions']; else $this->actions = new actionColl();

		if (isset($Params['defModName'])) $this->defModName = $Params['defModName']; else $this->defModName = $this->codename;
		if (isset($Params['parentMod'])) $this->parentMod = $Params['parentMod']; else $this->parentMod = 0;
		if (isset($Params['layoutPref'])) $this->layoutPref = $Params['layoutPref']; else $this->layoutPref = 0;
		if (isset($Params['defAction'])) $this->defAction = $Params['defAction']; else $this->defAction = 0;
		if (isset($Params['defQueryString'])) $this->defQueryString = $Params['defQueryString']; else $this->defQueryString = 0;
		if (isset($Params['isSystem'])) $this->isSystem = $Params['isSystem']; else $this->isSystem = 0;

		if (isset($Params['addons'])) $this->addons = $Params['addons']; else $this->addons = 0;
		if (isset($Params['addons_name'])) $this->addons_name = $Params['addons_name']; else $this->addons_name = 0;
		if (isset($Params['addonsItem'])) $this->addonsItem = $Params['addonsItem']; else $this->addonsItem = new addonsSet(array());

		$this->notInsert['actions'] = 1;
		$this->notInsert['addons_name'] = 1;
		$this->notInsert['addonsItem'] = 1;
		foreach ($Params as $key => $val) {
			if (!$val && $key != 'actions') {
				return false;
			}
		}

	}

	public function toArray() {
		$Params['id'] = $this->id;
		$Params['name'] = $this->name;
		$Params['codename'] = $this->codename;
		$Params['processName'] = $this->processName;
		$Params['xsl'] = $this->xsl;
		$Params['actions'] = $this->actions;

		$Params['defModName'] = $this->defModName;
		$Params['parentMod'] = $this->parentMod;
		$Params['layoutPref'] = $this->layoutPref;
		$Params['defAction'] = $this->defAction;
		$Params['defQueryString'] = $this->defQueryString;
		$Params['isSystem'] = $this->isSystem;
		$Params['addons'] = $this->addons;
		$Params['addons_name'] = $this->addons_name;
		$Params['addonsItem'] = $this->addons_name;
		return $Params;
	}
}

class modsetColl extends module_collection {
	public function __construct () {
		parent::__construct();
	}

	public function addItem($params) {
		$item = new modsetItem($params);
		$this->add($item);
	}
}

class groupItem extends module_item {
	public $id;
	public $name;
	public $parent;
	public $admin;

	// public $parentLink;
	public $childs;
	public $rootName;

public function __construct($Params, $prefix = '') {
		parent::__construct();
		if ($prefix != '') $prefix.='_';
		if (isset($Params[$prefix.'id'])) $this->id = $Params[$prefix.'id'];
		else $this->id = 0;
		if (isset($Params[$prefix.'name'])) $this->name = $Params[$prefix.'name'];
		else $this->name = '';
		if (isset($Params[$prefix.'parent'])) $this->parent = $Params[$prefix.'parent'];
		else $this->parent = 0;
		if (isset($Params[$prefix.'admin'])) $this->admin = $Params[$prefix.'admin'];
		else $this->admin = 0;
		//if (isset($Params[$prefix.'parentLink'])) $this->parentLink = $Params[$prefix.'parentLink'];
		//else $this->parentLink = new groupTree();
		if (isset($Params[$prefix.'childs'])) $this->childs = $Params[$prefix.'childs'];
		else $this->childs = array();
		if (isset($Params[$prefix.'rootName'])) $this->rootName = $Params[$prefix.'rootName'];
		else $this->rootName = '';
		$this->notInsert['id'] = 1;
		$this->notInsert['parentLink'] = 1;
	}
	public function toArray() {
		$Params['id'] = $this->id;
		$Params['name'] = $this->name;
		$Params['parent'] = $this->parent;
		$Params['admin'] = $this->admin;
//		$Params['parentLink'] = $this->parentLink;
		$Params['childs'] = $this->childs;
		$Params['rootName'] = $this->rootName;
		return $Params;
	}
	public function hasChild() {
		if (count($this->childs) > 0) return true;
		else return false;
	}

}

class groupTree extends module_collection {
  public function __construct () {
    parent::__construct();
  }

  public function addItem($params) {
    $item = new groupItem($params);
    $this->add($item);
    if ($item->parent != 0) {
    	
    	if (isset($this[$item->parent])) {
    		//stop($item->parent,0);     	
    	
    	//if ($this->offsetExists($item->parent)) {    		
    		//$item->parentLink = //$this->offsetGet($item->parent);
///    		$parentItem = $this->offsetGet($item->parent);
    		$parentItem = $this[$item->parent];
    		//$item->parentLink->childs->addItem($item->toArray());
    		$parentItem->childs[] = $item->id;
    		
    	} else {
    		    		
    	}
    }
    return true;
  }
}

class module_model extends database {
	protected $Log;
	protected $mod_id;
	protected $modSet;
	protected $System;
	protected $User;

	public function __construct($modName) {
		global $LOG,$System, $User;
		
		parent::__construct($modName);
		
		$this->System = $System;
		$this->User = $User;
		$this->Log = $LOG;

		$sql = 'SELECT * FROM '.TAB_PREF.'modules WHERE codename = \'%1$s\'';
//        exit;
//        stop($this->dbSelected);
		$this->query($sql, $modName);
		$mod = $this->fetchOneRowA();
		
		#stop($mod);		
		$modset = new modsetItem($mod);
		if ($modset->id == 0) $this->Log->addError(array('Модуль не зарегестрирован!', $modName), __LINE__, __METHOD__);
		$this->mod_id = $modset->id;
		$this->modSet = $modset;
	}

	public function getModID() {
		return $this->mod_id;
	}
	public function getSysMod() {
		return $this->modSet;
	}

	/**
	 * Добавить действие, по умолчанию в группу Администраторов
	 * @param $action (moduleAction)
	 * @param [group_id = 1]
	 * @param [$access = 1] Доступ у группы (разрешен, запрещен)
	 * @return true or false
	 */
	public function addAction(moduleAction $action, $group_id = 1, $group_access = 1) {
		$res = $action->toInsert();
		# var_dump($action->mod_id); exit;
		if ($action->mod_id == 0 || $action->action_name == '') return false;
		$sql = 'INSERT INTO '.TAB_PREF.'module_actions ('.$res[0].') VALUES('.$res[1].')';
		$q = array_merge(array(0=>$sql),$res[2]);
		$this->query($q);
		$id = $this->insertID();
		if ($action->group_adm == 0 && $id > 0) {
			$sql = 'INSERT INTO '.TAB_PREF.'module_access (`action_id`, `group_id`, `access`) VALUES( %1$u, %2$u, %3$u)';
			$this->query($sql, $id, $group_id, $group_access);
		} else $this->Log->addError(array('Действие не связано',$action->group_adm, $id),__line__,__method__);
		$action->setVal('id', $id);
		if ($action->id == 0) return false;
		return true;
	}

	/**
	 * Удалить действие
	 * @param $action
	 * @return true
	 */
	public function delAction (moduleAction $action) {
		$sql = 'DELETE FROM '.TAB_PREF.'module_access WHERE `action_id` = %1$u';
		$this->query($sql, $action->id);
		$sql = 'DELETE FROM '.TAB_PREF.'module_actions WHERE id = %1$u';
		$this->query($sql, $action->id);
		return true;
	}
	/*
	 * Добавить действие группе
	 * @param $action_id
	 * @param $group_id
	 * @param $access
	 * @return true
	 */
	public function addActionToGroup($action_id, $group_id, $access) {
		$sql = 'INSERT INTO '.TAB_PREF.'module_access (`action_id`, `group_id`, `access`) VALUES( %1$u, %2$u, %3$u)';
		$this->query($sql, $action_id, $group_id, $access);
		return true;
	}

	public function delActionFromGroup ($action_id, $group_id) {
		$sql = 'DELETE FROM '.TAB_PREF.'module_access WHERE `action_id` = %1$u AND `group_id` = %2$u';
		$this->query($sql, $action_id, $group_id);
		return true;
	}

	public function getAction ($module_id, $action_name) {
	  /*
		$sql = 'SELECT ma.*, IF (mc.group_id IS NOT NULL, mc.group_id, 0) as group_adm, m.name as mod_name
            FROM '.TAB_PREF.'module_actions ma
            INNER JOIN '.TAB_PREF.'modules m ON ma.mod_id = m.id
            LEFT JOIN '.TAB_PREF.'module_access mc ON ma.id = mc.action_id AND mc.group_id = 1
            WHERE mod_id = %1$u AND action_name = \'%2$s\'';
            */
            $sql = 'SELECT ma.*, mc.group_id as group_adm, m.name as mod_name
            FROM '.TAB_PREF.'module_actions ma
            INNER JOIN '.TAB_PREF.'modules m ON ma.mod_id = m.id
            LEFT JOIN '.TAB_PREF.'module_access mc ON ma.id = mc.action_id AND mc.group_id = 1
            WHERE mod_id = %1$u AND action_name = \'%2$s\'';
		$this->query($sql, $module_id, $action_name);
		$row = $this->fetchOneRowA();
		$action = new moduleAction($row);
		return $action;
	}

	public function getActionByID($action_id) {
		if ($action_id > 0) {
           /*
			$sql = 'SELECT ma.*, IF (mc.group_id IS NOT NULL, mc.group_id, 0) as group_adm, m.name as mod_name
            FROM '.TAB_PREF.'module_actions ma
            INNER JOIN '.TAB_PREF.'modules m ON ma.mod_id = m.id
            LEFT JOIN '.TAB_PREF.'module_access mc ON ma.id = mc.action_id AND mc.group_id = 1
            WHERE ma.id = %1$u';
            */

            $sql = 'SELECT ma.*, mc.group_id as group_adm, m.name as mod_name
            FROM '.TAB_PREF.'module_actions ma
            INNER JOIN '.TAB_PREF.'modules m ON ma.mod_id = m.id
            LEFT JOIN '.TAB_PREF.'module_access mc ON ma.id = mc.action_id AND mc.group_id = 1
            WHERE ma.id = %1$u';

			$this->query($sql, $action_id);
			$row = $this->fetchOneRowA();
			$action = new moduleAction($row);
			return $action;
		}
		return false;
	}
	/**
	 *
	 * @param $module_id
	 * @return class actionColl()
	 */
	public function getActions($module_id) {
          /*
		$sql = 'SELECT ma.*, IF (mc.group_id IS NOT NULL, mc.group_id, 0) as group_adm, m.name as mod_name
            FROM '.TAB_PREF.'module_actions ma
            INNER JOIN '.TAB_PREF.'modules m ON ma.mod_id = m.id
            LEFT JOIN '.TAB_PREF.'module_access mc ON ma.id = mc.action_id AND mc.group_id = 1
            WHERE ma.mod_id = %1$u';
            */
       		$sql = 'SELECT ma.*, mc.group_id as group_adm, m.name as mod_name
            FROM '.TAB_PREF.'module_actions ma
            INNER JOIN '.TAB_PREF.'modules m ON ma.mod_id = m.id
            LEFT JOIN '.TAB_PREF.'module_access mc ON ma.id = mc.action_id AND mc.group_id = 1
            WHERE ma.mod_id = %1$u';
		$this->query($sql, $module_id);
		$coll = new actionColl();
		while ($row = $this->fetchRowA()) {
			$coll->addItem($row);
		}
		return $coll;
	}

	public function getActionsFull($module_id, $user_id) {

    /*
		$sql = 'SELECT ma.*,
                m.name as mod_name,
                mc.access as mcaccess,
                ug.group_id,
                ug.user_id,
                g.name as gname,
                u.name as uname,
                IF (mc.group_id IS NOT NULL, mc.group_id, 0) as group_adm
            FROM '.TAB_PREF.'module_actions ma
            INNER JOIN '.TAB_PREF.'modules m ON  ma.mod_id = m.id
            LEFT JOIN '.TAB_PREF.'module_access mc ON ma.id = mc.action_id
            INNER JOIN '.TAB_PREF.'groups_user ug ON mc.group_id = ug.group_id
            INNER JOIN '.TAB_PREF.'groups g ON ug.group_id = g.id
            INNER JOIN '.TAB_PREF.'users u ON ug.user_id = u.id
            WHERE ma.mod_id = %1$u';
         */
        		$sql = 'SELECT ma.*,
                m.name as mod_name,
                mc.access as mcaccess,
                ug.group_id,
                ug.user_id,
                g.name as gname,
                u.name as uname,
               mc.group_id as group_adm
            FROM '.TAB_PREF.'module_actions ma
            INNER JOIN '.TAB_PREF.'modules m ON  ma.mod_id = m.id
            LEFT JOIN '.TAB_PREF.'module_access mc ON ma.id = mc.action_id
            INNER JOIN '.TAB_PREF.'groups_user ug ON mc.group_id = ug.group_id
            INNER JOIN '.TAB_PREF.'groups g ON ug.group_id = g.id
            INNER JOIN '.TAB_PREF.'users u ON ug.user_id = u.id
            WHERE ma.mod_id = %1$u';
		if ($user_id > 0) $sql.= ' AND ug.user_id = %2$u';

		$this->query($sql, $module_id, $user_id);
		$coll = array();
		$lastID = 0;
		$lastGR = '';
		$actionColl = new actionColl();
		while ($row = $this->fetchRowA()) {
			#stop($row);
			if ($lastID != $row['id']) {
				$groupColl = new mcColl();

				$Params = array();
				$Params['id'] = $row['id'];
				$Params['mod_id'] = $row['mod_id'];
				$Params['mod_name'] = $row['mod_name'];
				$Params['action_name'] = $row['action_name'];
				$Params['action_title'] = $row['action_title'];
				$Params['access'] = $row['access'];
				$Params['group_adm'] = $row['group_adm'];
				$Params['groups'] = $groupColl;
				$action = new moduleAction($Params);
				$actionColl->add($action);
			}

			$gr = array();
			$gr['id'] = $row['group_id'];
			$gr['action_id'] = $row['id'];
			$gr['group_id'] = $row['group_id'];
			$gr['group_name'] = $row['gname'];
			$gr['user_id'] = $row['user_id'];
			$gr['access'] = $row['access'];
			$gr['module_id'] = $row['mod_id'];
			$groupColl->addItem($gr);
			$lastID = $row['id'];
		}
		return $actionColl;
	}

	public function registerActions(actionColl $actions, $disable = 1) {
		if ($disable == 1) {
			$this->Log->addSysMSG(array('Регистрацция действий ОТКЛЮЧЕНА!'), __LINE__, __METHOD__);
			/*** *** ****/
			return true;
			/*** *** ****/
		}
		$Iterator = $actions->getIterator();
		//stop ($actions->count(),0);
		foreach ($Iterator as $rAction) {
			if ($rAction->mod_id && $rAction->action_name) {
				$a = $this->getAction($rAction->mod_id, $rAction->action_name);
				if (!$a->id && $rAction->action_name != '' && $rAction->mod_id != 0) {
					$this->Log->addSysMSG(array('Регистрацция действия', $rAction->action_name), __LINE__, __METHOD__);
					$this->addAction($rAction);
				} else $this->Log->addWarning(array('Регистрацция действия', $rAction->action_name, $a->id, $rAction->mod_id), __LINE__, __METHOD__);
			}
		}
		return true;
	}

	public function getGroups() {
		$sql = 'SELECT * FROM `groups` WHERE hidden != 1';
		$this->query($sql);
		$groups = array();		
		while($row = $this->fetchRowA()) {			
			$groups[] = $row;
		}
		return $groups;
	}

	public function getGroupsTree() {
		/*
		 * SELECT g.id, g.name, g.parent FROM `tree_nodes` tn
		 inner join groups g on g.id = tn.owner
		 where tn.parent = 0
		 group by tn.owner
		 ;
		 */
		// $sql = 'SELECT * FROM '.TAB_PREF.'`groups`';
		$sql = 'SELECT g.id, g.name, g.parent, g.admin FROM `tree_nodes` tn
					INNER JOIN groups g ON g.id = tn.owner
					WHERE tn.parent = 0
					GROUP BY tn.owner';
		$this->query($sql);
		$groups = new groupTree();

		while($row = $this->fetchRowA()) {
			$groups->addItem($row);
		}
		//stop($groups);
		return $groups;
	}
	public function getGroupsTree2() {

		$sql = 'SELECT g.id, g.name, g.parent, g.admin FROM `tree_nodes` tn
					INNER JOIN groups g ON g.id = tn.owner
					WHERE tn.parent = 0
					GROUP BY tn.owner';
		$this->query($sql);
		$groups = new groupTree();

		while($row = $this->fetchRowA()) {
			$groups->addItem($row);
		}
		//stop($groups);
		return $groups;
	}
	
}

abstract class module_process {
	/**
	 * Ссылка на глобальный класс Values
	 * @var unknown_type
	 */
	protected $Vals;
	protected $vals;
	protected $User;
	protected $Log;
	protected $System;
	protected $modName;
	protected $mod_id;
	protected $action;
	/**
	 * Действие по умолчанию
	 * @access protected
	 * @var string
	 */
	protected $actionDefault;
	protected $actionsColl;

	protected $nModel;
	protected $nView;
	private $pXSL;
	public $updated;

	protected $sysMod ;
	/*
	 * Конструктор, не забываем регистрировать Действия и Перенести в Конструктор экземпляры Модели и Представления
	 * @param $modName Только
	 * @return unknown_type
	 */
	public function __construct($modName) {
		global $values, $User, $LOG, $System;
		$this->vals = $values;
		$this->Vals = $values;
		$this->System = $System;
		$this->modName = $modName;
		$this->User = $User;
		$this->Log = $LOG;
		$this->action = false;
		/* actionDefault - должно быбираться из БД!!! */
		$this->actionDefault = '';

		$this->actionsColl = new actionColl();
		if (!$this->nModel)  $this->nModel = new module_model($modName);
		$sysMod = $this->nModel->getSysMod();
		$this->sysMod = $sysMod;
		$this->mod_id = $sysMod->id;

		if (!$this->mod_id) {
			#      $view = new module_view ($this->modName);
			#      $view->viewError('Модуль не зарегистрирован');
			# $bodySet = $view->getBody('html');
			# exit( $body->saveHTML());
		}
		$this->nView = new module_view ($this->modName, $sysMod);


	}
	/**
	 * проверка на валидность для вставки. Массив из ключей не являющихся числом и Значения не явл. NULL
	 */
	protected function isValid($val) {
		$valid = true;
		if (is_array($val)) {
			foreach ($val as $k => $v ) {
				#if (!is_nan((float)$k) || $v === NULL) $valid = false;
				if (preg_match('/^([a-z-A-Z]*)/', $k) == 0 || $v === NULL) $valid = false;
				$this->Log->addToLog($k.' ['.$v.'] '.intval($valid), __LINE__, __METHOD__);
			}
		} elseif ($val === NULL) $valid = false;
		return $valid;
	}
	/**
	 * Выполнение действия модулем, не обязательный параметр Action, не забыть проверить права доступа и обработать Сообщения
	 * @param $action
	 * @return mixed
	 */
	abstract public function update($action = false);

	#  abstract public function getBody($data_type = 'xml');

	public function createTable ($table, $rows) {
		$sql = 'CREATE TABLE '.$table.'(`id` INT NOT NULL AUTO_INCREMENT';
		//    if ($rows['id']['def'] != '') $sql.= ' NOT NULL '; else $sql.= ' NULL ';
		//next($rows);
		foreach ($rows as $key => $val) {
			if ($key == 'id' || $key == 'link') continue;
			$sql.= rn.', `'.$key.'` ';

			switch (gettype($val)) {
				case 'string': $type = 'TEXT'; break;
				case 'text': $type = 'TEXT'; break;
				case 'file': ;
				case 'fileimg': ;
				case 'filedoc': ;
				case 'pass': ;
				case 'edit': $type = 'VARCHAR (250)'; break;
				case 'box': $type = 'INT (4)'; break;
				case 'num': ;
				case 'option': ;
				case 'integer': ;
				case 'time': $type = 'INT'; break;
				case 'link': /*Поле данного типа не входит в состав таблицы!*/ break;
				default: break;
			}

			$sql.=  $type;
			# if ($val['def'] != '') $sql.= ' NOT NULL DEFAULT \''.$val['def'].'\' '; else $sql.= ' NULL ';
			$sql.= ' NULL ';
		}
		$sql.= ' , PRIMARY KEY ( `id` ) );';
		return $sql;
	}

	/**
	 *
	 * @param $data_type формат возвращаемого документа (xsl, html), dafault - xml;
	 * @return Возвращает посроенный блок модуля (DOMDocument)
	 */
	public function getBody($data_type = 'xml') {
		if (!$this->mod_id) {
			//$this->nView->pXSL[] = 'file.view.xsl';
			//$view = new module_view ('error', new modsetItem(array()));
			$this->nView->addPXSL('layout/file.view.xsl');
			$this->nView->viewError(array($this->modName,'Модуль не зарегистрирован'), true);
			return $this->nView->getBody();
		}
		if ($data_type == 'xml') return $this->nView->getBody();
		if ($data_type == 'html') {
			$data = $this->nView->getBody($data_type);
			return $data;
		}
	}

	public function registerActions($disable = 1) {
		$this->nModel->registerActions($this->actionsColl, $disable);
		return true;
	}

	public function regAction($name, $title, $access) {
		list($Params['id'], $Params['mod_id'], $Params['action_name'], $Params['action_title'], $Params['access']) =
		array(0, $this->mod_id, $name, $title, $access);
		$this->actionsColl->addItem($Params);
		//$this->Log->addToLog(array('regActionCount'=>$this->actionsColl->count(), $Params['action_name']),__line__,__method__);
	}

	public function resetXML($blockName = false) {
		$this->nView->resetXML($blockName);
	}

	public function setDefaultAction($actionDefault) {
		$this->actionDefault = $actionDefault;
	}

	public function checkAction() {
		$Iterator = $this->actionsColl->getIterator();
		$action = false;
		foreach ($Iterator as $act) {
			//$this->Log->addToLog($act->action_name, __line__, __method__);
			if (($a = $this->Vals->getVal($act->action_name, 'GET')) !== NULL) {
				$action = $act->action_name;
				return $action;
			}
		}
		return false;
	}
}

class bodySet {
	private $xml;
	private $pXSL;
	private $Errors;

	public function __construct(DOMDocument $xml, $pXSL, $Errors) {
		$this->xml = $xml;
		if (gettype($pXSL) != 'array') $pXSL = array($pXSL);
		$this->pXSL = $pXSL;
		$this->Errors = $Errors;
	}
	public function getXML() { return $this->xml;  }
	public function getXSL() { return $this->pXSL; }
	public function getErrors() { return $this->Errors; }
}

/**
 *
 * @author poltavcev
 *
 */
class module_view extends TXML {
	protected $modName;
	protected $Log;
	protected $pXSL;
	protected $Errors;
	protected $sysMod;

	/**
	 * подмодули, тела которых должны быть вне текущего модуля
	 * @var array of module_view();
	 */
	protected $subModules;

	/*
	 *
	 * @param $modName
	 * @param $sysMod
	 * @return boolean
	 */
	public function __construct($modName, modsetItem $sysMod) {
		global $LOG;
		$this->modName = $modName;
		parent:: __construct($modName);
		$this->Log = $LOG;
		$this->pXSL = array();
		$this->Errors = false;
		$this->sysMod = $sysMod;
		$this->subModules = array();

		return true;

	}
	public function addPXSL($pXSL) {
		$this->pXSL[] = $pXSL;
	}
	public function getPXSL() { return $this->pXSL; }

	final public function getBody() {
		global $glob_inc;

		if (DEBUG == 1) file_put_contents('debug/getBody_'.$this->modName.'_'.$glob_inc.'.xml', $this->xml->saveXML());
		$glob_inc++;
		#$this->Log->addToLog(array('Получаем bodySet ', join (',',$this->pXSL))  , __LINE__, __METHOD__);
		$bodySet = new bodySet($this->xml, $this->pXSL, $this->Errors);
		return $bodySet;
	}

	final public function addSubBody(DOMDocument $xml) {
		$this->subModules[] = new bodySet($xml, $this->pXSL, $this->Errors);
	}

	final public function getSubBody() { return $this->subModules; }

	public function viewError($message, $critical = true) {
		if (gettype($message) == 'string') $message = array($message);
		$Container = $this->newContainer('errors');
		foreach ($message as $cap => $msg) {
			if (gettype($msg) == 'boolean') if ($msg) $msg = 'true'; else $msg = 'false';
			$node = $this->addToNode($Container, 'error', $msg);
			if(!preg_match('/[a-zA-Z]/', $cap)) $cap = '';
			$this->addAttr('caption', $cap, $node);
		}
		$this->Log->addError($message, __LINE__, __METHOD__);
		$this->pXSL[] = RIVC_ROOT.'layout/viewErrors.xsl';
		# Устанавливаем критическую ошибку, только если нет критической ошибки, иначе можно потерять ошибку как критическую
		if (!$this->Errors) $this->Errors = $critical;
	}

	public function viewMessage($message, $title, $nolink=1) {
		if (gettype($message) == 'string') $message = array($message);
		$Container = $this->newContainer('messages');
		foreach ($message as $cap => $msg) {
			$node = $this->addToNode($Container, 'message', $msg);
			if(!preg_match('/[a-zA-Z]/', $cap)) $cap = '';
			$this->addAttr('caption', $cap, $node);
		}
		$this->addToNode($Container, 'nolink', $nolink);
		$this->Log->addError($message, __LINE__, __METHOD__);
		$this->pXSL[] = RIVC_ROOT.'layout/viewMessage.xsl';
	}
	/**
	 * Сообщение со ссылкой
	 * @param $message
	 * @param $title
	 * @param $linkTtle
	 * @param $linkSrc
	 * @return unknown_type
	 */
	public function viewMessageLink($message, $title, $linkTtle, $linkSrc) {
		if (gettype($message) == 'string') $message = array($message);
		$Container = $this->newContainer('messageslink');
		foreach ($message as $cap => $msg) {
			$node = $this->addToNode($Container, 'message', $msg);
			if(!preg_match('/[a-zA-Z]/', $cap)) $cap = '';
			$this->addAttr('caption', $cap, $node);
			$this->addAttr('linkTitle', $linkTtle, $node);
			$this->addAttr('linkSrc', $linkSrc, $node);
		}
		$this->Log->addError($message, __LINE__, __METHOD__);
		$this->pXSL[] = RIVC_ROOT.'layout/viewMessage.xsl';
	}

	/**
	 * Простая форма ввода логина и пароля
	 * @param $message
	 * @param $user_id
	 * @return unknown_type
	 */
	public function viewLogin($message, $user_id, $ContainerName = 'login') {
		global $_SESSION, $User, $loginErrors;
		$Container = $this->newContainer($ContainerName);
		$form = new CFormGenerator('login', SITE_ROOT.ltrim($_SERVER["REQUEST_URI"],'/'), 'POST', 0);
		$form->addHidden('referer',  SITE_ROOT.$_SERVER["REQUEST_URI"], 'referer');
		$form->addHidden('login', '1', 'login');
		$form->addHidden('user_id', $user_id, 'user_id');
        if (gettype($message) != 'array') $message = array(0 => $message);
		$form->addMessage('subject', $message[0], 'msg-login');

        if ($user_id == 0 && $User->getUserName() == '' && $loginErrors[$User->lastError()]=='Вы вошли как: %s') {
          $form->addMessageLink('sLogin', 'Вам необходимо авторизоваться локально', 's-login', '?logout', 'выйти');
          }
          else {

		$form->addMessage('error', sprintf($loginErrors[$User->lastError()], $User->getUserName()), 'error');
          }

		if ($User->lastError() != 1) {
			$form->addText('username', '','Имя пользователя', 'title', '', 30);
			$form->addPass('userpass', '', 'Пароль', 'userpass', '', 30);
			
			$LDAP = false;
			if (defined('LDAP_ENABLE')) {
				if (LDAP_ENABLE == 1) {
					$form->addSelect('domain', 0 , 'Домен', '', '', 'domain', '', 1);
					$form->addOption ('local', 'local', 'local', 'domain', '', '', '');		
					$form->addOption ('RIVC_DOMAIN', 'RIVC_DOMAIN', 'RIVC_DOMAIN', 'domain', '', '', '');
					$LDAP = true;		
				}
 			}
 			if (!$LDAP) {
 				$form->addHidden('domain', 'local', 'domain');
 			}
			$form->addSubmit('submit', 'войти', '', 'btn btn-primary');
		}
		$form->getBody($Container,'xml');

		$this->pXSL[] = RIVC_ROOT.'layout/form.xsl';
	}

	public function resetXML($blockName = false) {
		if ($blockName != false) $this->blockName = $blockName;
        $sysMod = '';
		$this->__construct($this->blockName,$sysMod);
	}

	public function Archive(DOMElement $Container, $page, $limCount, $moduleName, $addParam) {
		$archive = $this->addToNode($Container,'archive','');
		$this->addAttr('count',$limCount,$archive);
		$this->addAttr('page',$page,$archive);
		$this->addAttr('module',$moduleName,$archive);
		$this->addAttr('addParam',$addParam,$archive);
		 
	}
}

$glob_inc = 1;

?>