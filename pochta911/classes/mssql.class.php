<?php
class MSSQL {
	private $host;
	private $db_name;
	private $db_user;
	private $db_pass;
	public $connect;
	public $result;
	public $sql;
	public $debug_prefix;

	public function __construct($debug_prefix = 'all') {
		if (DB_USE != 'MSSQL') return;
		$this->debug_prefix = $debug_prefix;
		//stop(DB_HOST.' '.DB_USER. DB_PASS,0);
		//$this->connect = mssql_connect(DB_HOST, DB_USER, DB_PASS);
		$this->connect = mssql_connect(DB_HOST, DB_USER, DB_PASS);
		if (!$this->connect) { die ('Failed to connect to the server: '.br.mssql_get_last_message()); }
		//mssql_query ("SET NAMES UTF8");
	  //	mssql_select_db(DB_DATABASE,$this->connect);
	//	mssql_select_db(DB_DATABASE);
	   //	if (mssql_get_last_message() != '') { die ('Failed to select_db'.br.mssql_get_last_message()); }
	   //	define('MSSQL_CONNECTION', '');
	}

	public function query() {
		global $LOG, $SQL_COUNTER;
		$SQL_COUNTER++;
		$sqlLog = '';
		if (DEBUG == 1) {
			$fp = fopen('debug/sql.txt','a');
			fwrite($fp, $this->debug_prefix.': '.$SQL_COUNTER.rn);			 
		}
		if (func_num_args() > 1) $args = func_get_args();
		if (func_num_args() == 1) $args = func_get_arg(0);
		# $args = func_get_args();
		if (is_array($args)) {
			foreach($args as $key => &$arg) {
				if ($key == 0) continue;
				if (gettype ($arg) == 'array' ) { stop($args[0], 0); stop($arg); }
				$LOG->addToLog(array('arg'=>$arg), __LINE__, __METHOD__);
				// $arg = mssql_real_escape_string($arg, $this->connect);
			}
		}
		$this->sql = call_user_func_array('sprintf', $args);
		if (DEBUG == 1) {
			$a = array();
			$a = $args;
			$a0 = $args[0];
			$a = arrayTOhtmlspecialchars($a);
			$a[0] = $a0;
			$sqlLog = call_user_func_array('sprintf',$a);
		}
		if ($this->sql == '') {

			$LOG->addError(array('Пустой запрос')+$arg, __LINE__, __Method__);
		}
        if (is_resource($this->result))		mssql_free_result($this->result);
        $this->sql=str_replace('`','',$this->sql);
        $this->sql=utf8_win($this->sql);
        $sqlLog = str_replace('`','',$sqlLog);
        $sqlLog = utf8_win($sqlLog);
	  	$this->result = mssql_query($this->sql, $this->connect);

		if (mssql_get_last_message() != '') {
			$LOG->addError(array('Запрос не выполнен', 'last_message' => win_utf8(mssql_get_last_message()), 'SQL: '=>$this->sql), __LINE__, __Method__);
		} else {
			$LOG->addToLogSQL(array('MSSQL' => $sqlLog));
		}
		if (DEBUG == 1) {   fclose($fp); }
		return $this->result;
	}

	public function fetchRow() {
		global $LOG;

		if(!is_resource($this->result)) #die ('Отсутствет результат запроса'.br.mssql_error().$this->sql);
		{
			$LOG->addToLog(array('sql:'=>$this->sql, 'err'=>'fetchRow Отсутствет результат запроса', 'type'=>gettype($this->result)), __LINE__, __METHOD__);
			return false;
		}
	   //	return mssql_fetch_row($this->result);
               $temp = mssql_fetch_row($this->result);
		return win_utf8($temp);
	}
	public function fetchRowA() {
		global $LOG;

		if(!is_resource($this->result)) #die ('Отсутствет результат запроса'.br.mssql_error().$this->sql);
		{
			$LOG->addToLog(array('sql:'=>$this->sql, 'err'=>'fetchRowA Отсутствет результат запроса', 'type'=>gettype($this->result)), __LINE__, __METHOD__);
			return false;
		}
        $temp = mssql_fetch_assoc($this->result);
		return win_utf8($temp);
	}
	/** * возвращает первый (нулевой) элемент из строки запроса */
	public function getOne() {
		if (func_num_args() != 0) {
			$args = func_get_args();
			$this->query($args);
		}
		if(!is_resource($this->result)) die ('getOne Отсутствет результат запроса'.br.mssql_error());
		$row = mssql_fetch_row($this->result);
		mssql_free_result($this->result);
		return win_utf8($row[0]);
	}

	/** возвращает первую строку по запросу */
	public function fetchOneRow() {
		if (func_num_args() != 0) {
			$args = func_get_args();
			$this->result = call_user_func_array($this.'->query', $args);
		}
		if(!is_resource($this->result)) die ('fetchOneRow Отсутствет результат запроса'.br.mssql_error());
	   //	return mssql_fetch_row($this->result);

        $temp = mssql_fetch_row($this->result);
		return win_utf8($temp);

	}

	/** возвращает первую строку по запросу */
	public function fetchOneRowA() {
		if (func_num_args() != 0) {
			$args = func_get_args();
			$this->result = call_user_func_array($this.'->query', $args);
		}
		if(!is_resource($this->result)) die ('fetchOneRowA Отсутствет результат запроса'.br.mssql_get_last_message().br.$this->sql);
	  //	return mssql_fetch_assoc($this->result);

        $temp = mssql_fetch_assoc($this->result);
		return win_utf8($temp);
	}



	public function numRows() {
		/*$numRows = mssql_num_rows($this->result);
		mssql_free_result($this->result);
		if(is_resource($this->result)) return $numRows;
		return 0; */
        $this->result = mssql_query("select @@rowcount as rows", $this->connect);
        return mssql_result($this->result, 0, "rows");
	}

	public function affectedRows() {
		$affectedRows = @mssql_rows_affected();
		@mssql_free_result($this->result);
		return $affectedRows;
	}

	public function insertID() {
		$sql = 'SELECT @@IDENTITY AS ID';
		$id = $this->getOne($sql);
   //     stop($this->getOne($sql));
    //	mssql_free_result($this->result);
		return $id;
	}
}


?>