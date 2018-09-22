<?php
class mySQL {
//	private $host;
//	private $db_name;
//	private $db_user;
//	private $db_pass;
	public $connect;
	public $result;
	public $error;
	public $sql;
	public $debug_prefix;

	public function __construct($debug_prefix = 'all') {
		if (DB_USE != 'mySQL') return;
		$this->debug_prefix = $debug_prefix;
//		if(!defined('MYSQL_CONNECTION')) {
			$this->connect = mysqli_connect(DB_HOST, DB_USER, DB_PASS);
			if (!$this->connect) { die ('Failed to connect to the server'.br.mysqli_error($this->connect)); }
			mysqli_query ($this->connect,"SET NAMES UTF8");
			mysqli_select_db($this->connect,DB_DATABASE);
			if (mysqli_errno($this->connect) != 0) { die ('Failed to select_db'.br.mysqli_error($this->connect)); }
//			define('MYSQL_CONNECTION', $this->connect);
//		} else $this->connect = MYSQL_CONNECTION;
	}

	public function query() {
		global $LOG, $SQL_COUNTER;
		// debug_print_backtrace(); exit;
		$SQL_COUNTER++;
		$sqlLog = '';
		if (DEBUG == 1) {
			$fp = fopen('debug/sql.txt','a');
			fwrite($fp, $this->debug_prefix.': '.$SQL_COUNTER.rn);

		}
		$args = array();
		if (func_num_args() > 1) $args = func_get_args();
		if (func_num_args() == 1) $args = func_get_arg(0);
		# $args = func_get_args();
		if (is_array($args)) {
			foreach($args as $key => &$arg) {
				if ($key == 0) continue;
				if (gettype ($arg) == 'array' ) { stop($args[0], 0); stop($arg); }
				$LOG->addToLog(array('arg'=>$arg), __LINE__, __METHOD__);
				$arg = mysqli_real_escape_string($this->connect, $arg);
			}
		}
		if (is_array($args))
			$this->sql = call_user_func_array('sprintf', $args);
		else
			$this->sql = call_user_func('sprintf', $args);
		if (DEBUG == 1) {
//			$a = array();
			$a = $args;
			$a0 = $args[0];
			$a = arrayTOhtmlspecialchars($a);
			$a[0] = $a0;
			if (is_array($a))
				$sqlLog = call_user_func_array('sprintf',$a);
			else
				$sqlLog = call_user_func('sprintf',$a);
		}
		if ($this->sql == '') {
			$LOG->addError('Пустой запрос', __LINE__, __Method__);
		}

		$this->result = mysqli_query($this->connect, $this->sql);
        $this->error = mysqli_error($this->connect);

		if (mysqli_errno($this->connect) != 0) {
			$LOG->addError(array('Ошибка MySQL', mysqli_errno($this->connect) => mysqli_error($this->connect) ),__LINE__,__METHOD__);
		}
		$LOG->addToLogSQL(array('SQL' => $sqlLog));
		if (mysqli_errno($this->connect) != 0) {
			$LOG->addError(array('Запрос не выполнен',mysqli_error($this->connect),$this->sql), __LINE__, __Method__);
		}		
		if (DEBUG == 1) {
            if (isset($fp)) {
                fclose($fp);
            }
        }
		return $this->result;
	}

	public function fetchRow() {
		global $LOG;

		if(!($this->result)) #die ('Отсутствет результат запроса'.br.mysql_error().$this->sql);
		{
			$LOG->addToLog(array('sql:'=>$this->sql, 'err'=>'fetchRow Отсутствет результат запроса', 'type'=>gettype($this->result)), __LINE__, __METHOD__);
			return false;
		}
        $return =  mysqli_fetch_row($this->result);
        return ($return == null)?false:$return;
	}
	public function fetchRowA() {
		global $LOG;

		if(!($this->result)) #die ('Отсутствет результат запроса'.br.mysql_error().$this->sql);
		{
			$LOG->addToLog(array('sql:'=>$this->sql, 'err'=>'fetchRowA Отсутствет результат запроса', 'type'=>gettype($this->result)), __LINE__, __METHOD__);
			return false;
		}
        $return =  mysqli_fetch_assoc($this->result);
		return ($return == null)?false:$return;
	}
	/** * возвращает первый (нулевой) элемент из строки запроса */
	public function getOne() {
		if (func_num_args() != 0) {
			$args = func_get_args();
			$this->query($args);
		}
		if(!($this->result)) die ('getOne Отсутствет результат запроса'.br.mysqli_error($this->connect));
		$row = mysqli_fetch_row($this->result);
		return $row[0];
	}

	/** возвращает первую строку по запросу */
	public function fetchOneRow() {
		if (func_num_args() != 0) {
			$args = func_get_args();
			$this->result = call_user_func_array($this.'->query', $args);
		}
		if(!($this->result)) die ('fetchOneRow Отсутствет результат запроса'.br.mysqli_error($this->connect));
        $return =  mysqli_fetch_row($this->result);
        return ($return == null)?false:$return;
	}

	/** возвращает первую строку по запросу */
	public function fetchOneRowA() {
		if (func_num_args() != 0) {
			$args = func_get_args();
			$this->result = call_user_func_array($this.'->query', $args);
		}
		if(!($this->result)) { debug_print_backtrace(); die ('fetchOneRowA Отсутствет результат запроса'.br.mysqli_error($this->connect).br.$this->sql);}
        $return =  mysqli_fetch_assoc($this->result);
        return ($return == null)?false:$return;
	}

	public function numRows() {
		if(($this->result)) return mysqli_num_rows($this->result);
		return 0;
	}

	public function affectedRows() {
		$affectedRows = mysqli_affected_rows($this->connect);
		return $affectedRows;
	}

	public function insertID() {
		return mysqli_insert_id($this->connect);
	}
}