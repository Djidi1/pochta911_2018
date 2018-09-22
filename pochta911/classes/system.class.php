<?php

/**
 * Выбрать ключ (по первому вхождению) из многомерного массива
 * @param $keyName
 * @param $array
 * @param $rStop рекурсия
 * @return Ключ любого типа или null
 */
function get_key($keyName, $array, $rStop = 0) {
	$result = null;
	if ($rStop > 100) return $result;
	foreach($array as $key => $val) {
		// echo '<b>'.$key.'</b>'.$keyName.' => '.$val.'<br />';
		if (!is_array($val)) {
			if(strval($key) == strval($keyName)) {
				// echo '<b>'.$key.'</b>'.$keyName.' => '.$val.'<br />';
				// echo '<b>2</b><br /> ';
				//$result = iconv('windows-1251','utf-8',$val);
				//$result = iconv('utf-8','windows-1251',$val);
				//$result = iconv('ISO-8859-1','windows-1251',$val);
				$result = win_utf8(trim($val));
				return $result;
			}
		}
		else {
			//echo '<b>Вход в массив</b><br /> ';
			if(strval($key) == strval($keyName)) {
				//echo '<b>Вход в массив</b><br /> ';
				if (isset($val[0])) {
					//$result = iconv('windows-1251','utf-8',$val[0]);
					//$result = iconv('utf-8','windows-1251',$val[0]);
					//$result = trim($val[0]);
					//$result = iconv('ISO-8859-1','windows-1251',$val[0]);
					$result = win_utf8(trim($val[0]));
					return $result;
				} else {
					return null;
				}
			} else {
			$r = get_key($keyName, $val, ($rStop+1));			
				if ($r != null) { return $r; }
			}
		}
	}
	return $result;
}

function arrayTOhtmlspecialchars($array) {
	if (gettype($array) != 'array') return $array;
	foreach($array as &$line) {
		$line = htmlspecialchars($line);
	}
	return $array;
}

class TSYSTEM {
	private $database;
	private $moduleList;

	public function __construct() {
		$this->moduleList = array(array());
		$this->database = new database('TSYSTEM');

		$this->moduleList = $this->moduleList();
		#stop($this->moduleList);
	}

	private function moduleList() {
		$sql = 'SELECT * FROM '.TAB_PREF.'modules ORDER BY `id`, `parentMod` DESC';
		$this->database->query($sql);
		$modList = array();
		while ($row = $this->database->fetchOneRowA()) {
			$modList[$row['id']] = $row;
		}
		return $modList;
	}

	public function getModule($id, $codename = '') {
		if ($id) {
			if (isset($this->moduleList[$id])) return $this->moduleList[$id];
		}
		if (!$id && $codename) {
			foreach($this->moduleList as $mID => $mod) {
				if ($mod['codename'] == $codename) return $mod;
			}
		}
		return false;
	}

	public function getModules() {
		return $this->moduleList;
	}
	/**
	 * Загрузка справочников
	 * @return true
	 */
	public function loadClassifier() {
		global $LOG;

		$this->refCategories = array();
		$this->refProjects = array();
		$this->refTarget = array();
		$this->refTarget = array();
		$this->refCompany = array();
		$this->refWork = array();
		$this->refsystem = array();

		$sql = 'SELECT * FROM `'.TAB_PREF.'refcategories` WHERE enabled = 1 ORDER BY `type` ASC,`prioritet` DESC';
		$this->database->query($sql);
		while ($row = $this->database->fetchOneRowA()) {
			$this->refCategories[$row['id']] = $row;
		}

		$sql = 'SELECT * FROM `'.TAB_PREF.'refprojects` WHERE enabled = 1 ORDER BY `prioritet` DESC';
		$this->database->query($sql);
		while ($row = $this->database->fetchOneRowA()) {
			$this->refProjects[$row['id']] = $row;
		}
		$sql = 'SELECT * FROM `'.TAB_PREF.'reftarget` WHERE enabled = 1  ORDER BY `prioritet` DESC';
		$this->database->query($sql);
		while ($row = $this->database->fetchOneRowA()) {
			$this->refTarget[$row['id']] = $row;
		}
		$sql = 'SELECT * FROM `'.TAB_PREF.'refcompany` WHERE enabled = 1 ORDER BY `id` ASC';
		$this->database->query($sql);
		while ($row = $this->database->fetchOneRowA()) {
			$this->refCompany[$row['id']] = $row;
		}
		$sql = 'SELECT * FROM `'.TAB_PREF.'refwork` WHERE enabled = 1   ORDER BY `prioritet` DESC';
		$this->database->query($sql);
		while ($row = $this->database->fetchOneRowA()) {
			$this->refWork[$row['id']] = $row;
		}
		// $this->setManualCache('refWork',$this->refWork);
		$this->setManualCache('refprojects',$this->refProjects);

		$sql = 'SELECT * FROM `'.TAB_PREF.'refsystem` WHERE enabled = 1 ORDER BY `prioritet` DESC';
		$this->database->query($sql);
		while ($row = $this->database->fetchOneRowA()) {
			$this->refsystem[$row['id']] = $row;
		}
		// $this->setManualCache('refWork',$this->refWork);
		if($this->setManualCache('refsystem',$this->refsystem)) {
			$LOG->addToLog ('refsystem ok', __LINE__, __METHOD__);
		} else {
			$LOG->addError ('refsystem false', __LINE__, __METHOD__)   ;
		}


		return true;
	}

	public function getRefCategories() { return $this->refCategories; }
	public function getRefProjects() { return $this->refProjects;}
	public function getRefTarget() { return $this->refTarget; }
	public function getRefCompany() { return $this->refCompany; }
	public function getRefWork() { return $this->refWork; }
	public function getrefsystem() { return $this->refsystem; }

	public function actionLog($module, $essence_id, $text, $date, $user_id, $type, $action) {
		if (!$date) $date = dateToDATETIME (date('d.m.Y H:i:s'));
		$IP = $_SERVER["REMOTE_ADDR"];

		$sql = 'INSERT INTO `'.TAB_PREF.'sys_log`
				(`module`, `essence_id`, `text`, `date`, `user_id`, `ip`, `type`, `action`)
				VALUES 
				(%1$u, %2$u, \'%3$s\', \'%4$s\', %5$u, \'%6$s\', %7$u, \'%8$s\')';
		$this->database->query($sql,$module, $essence_id, $text, $date, $user_id, $IP, $type, $action);
	}

	public function getManualCache($cacheKey) {
		if (file_exists('cache/manual/'.$cacheKey.'.ch') && $cacheKey != '') {
			return unserialize(file_get_contents('cache/manual/'.$cacheKey.'.ch'));
		}	else return '';
	}

	public function setManualCache($cacheKey, $data) {
		if ($cacheKey != '') {
			return file_put_contents('cache/manual/'.$cacheKey.'.ch', serialize($data));
		}
		else return 0;
	}
}

class OptionItem extends module_item  {
	public $id;
	public $name;

	public function __construct($Params, $prefix = '') {
		parent::__construct();
		if ($prefix != '') $prefix.='_';
		if (isset($Params[$prefix.'id'])) $this->id = $Params[$prefix.'id'];
		else $this->id = 0;
		if (isset($Params[$prefix.'name'])) $this->name = $Params[$prefix.'name'];
		else $this->name = '';
		$this->notInsert['id'] = 1;
	}
	public function toArray() {
		$Params['id'] = $this->id;
		$Params['name'] = $this->name;
		return $Params;
	}
}

class OptionCollection extends module_collection {

	public function __construct() {
		parent::__construct();
	}

	public function addItem($Params) {
		$item = new OptionItem($Params);
		$this->add($item);
	}
}


/**
 * Drop Down List, Выпадающий список (input Select)
 * $input_name, $table, $fileld_title, $field_val, $def_title, $def_val, $selectedBY , $SQL_ADDON = ''
 * @author poltavcev
 *
 */
class DDList {
	private $database;

	public $name;
	public $table;
	public $fileld_title;
	public $field_val;
	public $def_title;
	public $def_val;
	public $selectedBY;
	public $SQL_ADDON;
	public $onChangeJS; // функция JS onChange

	/*
	 * Кеш опшинов (OptionCollection)
	 */
	protected $options;

	public function __construct($name, $table, $fileld_title, $field_val, $def_title, $def_val, $selectedBY, $SQL_ADDON = '') {
		$this->name = $name;
		$this->table = $table;
		$this->fileld_title = $fileld_title;
		$this->field_val = $field_val;
		$this->def_title = $def_title;
		$this->def_val = $def_val;
		$this->selectedBY = $selectedBY;
		$this->SQL_ADDON = $SQL_ADDON;
		$this->onChangeJS = '';

		$this->options = new OptionCollection();
		$this->database = new database();
		if ($table != '') $this->loadOptions();
	}

	public function setOptions(OptionCollection $Options) { $this->options = $Options; }

	public function onChangeJS($js) { $this->onChangeJS = $js; }

	private function loadOptions() {
		$sql = 'SELECT '.$this->fileld_title.', '.$this->field_val.' FROM '.TAB_PREF.''.$this->table.' '.$this->SQL_ADDON;
		if ($this->def_title != '') {
			$this->options->addItem(array('id'=>$this->def_val, 'name'=>$this->def_title));
		}
		$this->database->query($sql);
		while($row = $this->database->fetchRowA()) {
			//stop($row,0);
			$this->options->addItem($row);
		}
	}

	public function getOptions() { return $this->options; }

	/**
	 *
	 * @param $nameMulti Поставить пустые квадратные скобки (массив)
	 * @param $nameIndex Поставить в скобках номер!
	 * @return unknown_type
	 */
	public function getOptionsHTML($nameMulti = false, $nameIndex = -1) {
		$data = '';
		if ($this->options->count() > 0) {
			$data.= '<select ';
			if ($this->onChangeJS != '') {
				$data.= ' onChange="'.$this->onChangeJS.'" ';
			}
			if ($nameMulti && $nameIndex < 0) $data.= ' name="'.$this->name.'[]" id="select_'.$this->name.'">';
			elseif ($nameMulti && $nameIndex >= 0) $data.= ' name="'.$this->name.'['.$nameIndex.']" id="select_'.$this->name.'_'.$nameIndex.'">';
			elseif (!$nameMulti) $data.= ' name="'.$this->name.'" id="select_'.$this->name.'">';
			$iterator = $this->options->getIterator();
			foreach($iterator as $option) {
				if ($option->id == $this->selectedBY) $data.= '<option value="'.$option->id.'" selected="selected">'.$option->name.'</option>';
				if ($option->id != $this->selectedBY) $data.= '<option value="'.$option->id.'">'.$option->name.'</option>';
			}
			$data.= '</select>';
		}
		return $data;
	}
}


/*
 *
 * @param $date строка с датой по маске (DD.MM.YY[YY] HH.MM.SS)
 * @return Возвращает временную метку - timestamp
 */
function dateToTimestamp($date) {
	global $LOG;
	if (strlen($date) < 6) return false;
	preg_match_all('/(\d{1,4})/',$date, $dataArr);
	$hour = (isset($dataArr[1][3]) ? $dataArr[1][3] : 0);
	$minute = (isset($dataArr[1][4]) ? $dataArr[1][4] : 0);
	$second = (isset($dataArr[1][5]) ? $dataArr[1][5] : 0);
	$month = (isset($dataArr[1][1]) ? $dataArr[1][1] : 0);
	$day = (isset($dataArr[1][0]) ? $dataArr[1][0] : 0);
	$year = (isset($dataArr[1][2]) ? $dataArr[1][2] : 0);
	# d.m.Y H:i
	$time = mktime  ($hour, $minute, $second, $month, $day, $year);
	$LOG->addToLog(array('dateString'=>$date,
			'hour'=>$hour,
			'min'=>$minute,
			'sec'=>$second,
			'month'=>$month,
			'day'=>$day,
			'year'=>$year,
			'timestamp',$time
	)
	,__LINE__,'dateToTimestamp');
	if (!$time) $time = time();
	return $time;
}

function dateMyToTimestamp($date) {
	global $LOG;
	if (strlen($date) < 6) return false;
	preg_match_all('/(\d{1,4})/',$date, $dataArr);
	$hour = (isset($dataArr[1][3]) ? $dataArr[1][3] : 0);
	$minute = (isset($dataArr[1][4]) ? $dataArr[1][4] : 0);
	$second = (isset($dataArr[1][5]) ? $dataArr[1][5] : 0);
	$month = (isset($dataArr[1][1]) ? $dataArr[1][1] : 0);
	$day =  (isset($dataArr[1][2]) ? $dataArr[1][2] : 0);
	$year = (isset($dataArr[1][0]) ? $dataArr[1][0] : 0);
	# d.m.Y H:i
	$time = mktime  ($hour, $minute, $second, $month, $day, $year);
	$LOG->addToLog(array('dateString'=>$date,
			'hour'=>$hour,
			'min'=>$minute,
			'sec'=>$second,
			'month'=>$month,
			'day'=>$day,
			'year'=>$year,
			'timestamp',$time
	)
	,__LINE__,'dateToTimestamp');
	if (!$time) $time = time();
	return $time;
}

/**
 * Превращает строку с датой в тип MySQL DATETIME
 * @param $date Строка с датой (по маске d.m.Y h:i:s)
 * @return MYSQL DATETIME format
 */
function dateToDATETIME ($date, $addDay = 0) {
	$timestamp = dateToTimestamp($date);
	if($addDay > 0) $timestamp+= (60*60*24)-1;
	$mySQLDateTime = date('Y-m-d H:i:s',$timestamp);
	return $mySQLDateTime;
}

function MydateToDATETIME ($date, $addDay = 0) {
	$timestamp = dateMyToTimestamp($date);
	if($addDay > 0) $timestamp+= (60*60*24)-1;
	$mySQLDateTime = date('Y-m-d H:i:s',$timestamp);
	return $mySQLDateTime;
}
function dateSQLtoDate($date, $format = 'd.m.Y') {
	global $LOG;
	if (strlen($date) < 6) return false;
	preg_match_all('/(\d{1,4})/',$date, $dataArr);
	$hour = (isset($dataArr[1][3]) ? $dataArr[1][3] : 0);
	$minute = (isset($dataArr[1][4]) ? $dataArr[1][4] : 0);
	$second = (isset($dataArr[1][5]) ? $dataArr[1][5] : 0);
	$month = (isset($dataArr[1][1]) ? $dataArr[1][1] : 0);
	$day =  (isset($dataArr[1][2]) ? $dataArr[1][2] : 0);
	$year = (isset($dataArr[1][0]) ? $dataArr[1][0] : 0);
	# d.m.Y H:i
	$time = mktime  ($hour, $minute, $second, $month, $day, $year);
	if (!$time) $time = time();
	return date($format, $time);
}
class archiveStruct {
	public $module;
	public $count;
	public $size;
	public $curPage;
	public $addrParam;

	public function __construct ($module, $count, $size, $curPage, $addrParam) {
		$this->module =$module;
		$this->count = $count;
		$this->size = $size;
		$this->curPage = $curPage;
		$this->addrParam =	$addrParam;
	}
}

function sendMail($subject, $message, $to, $from, $ContentType = 'text/html') {
	$headers = 'Content-type: '.$ContentType.'; charset=utf-8;'.rn
	."From: $from <office@pochta911.ru>"."\r\n";

	return @mail($to,$subject,$message, $headers);
}

function win_utf8($data){

	if (is_array($data))
	{
		$d = array();
		foreach ($data as $k => &$v) $d[win_utf8($k)] = win_utf8($v);
		return $d;
	}
	if (is_string($data))
	{
		if (function_exists('iconv')) return iconv('windows-1251', 'utf-8', $data);
		//  if (! function_exists('cp1259_to_utf8')) include_once 'cp1259_to_utf8.php';
		//  return cp1259_to_utf8($data);
	}
	if (is_scalar($data) or is_null($data)) return $data;
	#throw warning, if the $data is resource or object:
	trigger_error('An array, scalar or null type expected, ' . gettype($data) . ' given!', E_USER_WARNING);
	return $data;

}

function utf8_win($data){

	if (is_array($data))
	{
		$d = array();
		foreach ($data as $k => &$v) $d[utf8_win($k)] = utf8_win($v);
		return $d;
	}
	if (is_string($data))
	{
		if (function_exists('iconv')) return iconv('utf-8', 'windows-1251', $data);
		//   if (! function_exists('cp1259_to_utf8')) include_once 'cp1259_to_utf8.php';
		//   return cp1259_to_utf8($data);
	}
	if (is_scalar($data) or is_null($data)) return $data;
	#throw warning, if the $data is resource or object:
	trigger_error('An array, scalar or null type expected, ' . gettype($data) . ' given!', E_USER_WARNING);
	return $data;

}
function sendData($data) {
	if (is_string($data)) {	
		exit($data);
	} else {
		exit(strval($data));
	}
}
?>