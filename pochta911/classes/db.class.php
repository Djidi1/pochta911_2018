<?php

require_once CORE_ROOT.'classes/mysql.class.php';
require_once CORE_ROOT.'classes/mssql.class.php';

class database {

	protected $MSSQL;
	protected $mySQL;

	protected $sql;
	protected $error;
	protected $result;
	/**
	 * Выбранная База Данных
	 * @var string
	 */
	protected $dbSelected;

	public function __construct($modName = '') {
//		global $LOG,$System, $User;
		
		if (!defined('DB_USE')) stop('Отсутствует параметр [DB_USE]');
		$this->dbSelected = DB_USE;
		
		
		$this->mySQL = new mySQL($modName);
		$this->MSSQL = new MSSQL($modName);
		
	}

	public function query() {
		$args = array();
		$result = false;
		if (func_num_args() > 1) $args = func_get_args();
		if (func_num_args() == 1) $args = func_get_arg(0);
		if (func_num_args() == 0) return false;

		switch ($this->dbSelected) {
			case 'mySQL':
				$result = $this->mySQL->query($args);
				$this->sql = $this->mySQL->sql;
				$this->result = $this->mySQL->result;
				$this->error = $this->mySQL->error;
				break;
			case 'MSSQL':
				$result = $this->MSSQL->query($args);
				$this->sql = $this->MSSQL->sql;
				$this->result = $this->MSSQL->result;
				if (DEBUG == 1) {
					if ($result === false) stop($this->sql,0);
				}
				break;
			default: stop('module_model: Не выбрана СУБД');
		}
		return $result;
	}
	public function fetchRow() {
		switch($this->dbSelected) {
			case 'mySQL': return $this->mySQL->fetchRow(); break;
			case 'MSSQL': return $this->MSSQL->fetchRow(); break;
		}
		return NULL;
	}
	public function fetchRowA() {
		switch($this->dbSelected) {
			case 'mySQL': return $this->mySQL->fetchRowA(); break;
			case 'MSSQL': return $this->MSSQL->fetchRowA(); break;
		}
		return NULL;
	}
	public function getOne() {
		if (func_num_args() != 0) {
			$args = func_get_args();
			// $this->query($args);
			switch($this->dbSelected) {
				case 'mySQL': return $this->mySQL->getOne($args); break;
				case 'MSSQL': return $this->MSSQL->getOne($args); break;
			}
		} else {
			switch($this->dbSelected) {
				case 'mySQL': return $this->mySQL->getOne(); break;
				case 'MSSQL': return $this->MSSQL->getOne(); break;
			}
		}
		return NULL;

	}
	public function fetchOneRow() {
		if (func_num_args() != 0) {
			$args = func_get_args();
			// $this->query($args);
			switch($this->dbSelected) {
				case 'mySQL': return $this->mySQL->fetchOneRow($args); break;
				case 'MSSQL': return $this->MSSQL->fetchOneRow($args); break;
			}
		} else {
			switch($this->dbSelected) {
				case 'mySQL': return $this->mySQL->fetchOneRow(); break;
				case 'MSSQL': return $this->MSSQL->fetchOneRow(); break;
			}
		}
		return NULL;
	}
	public function fetchOneRowA() {
		if (func_num_args() != 0) {
			$args = func_get_args();
			// $this->query($args);
			switch($this->dbSelected) {
				case 'mySQL': return $this->mySQL->fetchOneRowA($args); break;
				case 'MSSQL': return $this->MSSQL->fetchOneRowA($args); break;
			}
		} else {
			switch($this->dbSelected) {
				case 'mySQL': return $this->mySQL->fetchOneRowA(); break;
				case 'MSSQL': return $this->MSSQL->fetchOneRowA(); break;
			}
		}
		return NULL;
	}
	public function numRows() {
		switch($this->dbSelected) {
			case 'mySQL': return $this->mySQL->numRows(); break;
			case 'MSSQL': return $this->MSSQL->numRows(); break;
		}
        return false;
	}
	public function affectedRows() {
		switch($this->dbSelected) {
			case 'mySQL': return $this->mySQL->affectedRows(); break;
			case 'MSSQL': return $this->MSSQL->affectedRows(); break;
		}
        return false;
	}
	public function insertID() {
		switch($this->dbSelected) {
			case 'mySQL': return $this->mySQL->insertID(); break;
			case 'MSSQL': return $this->MSSQL->insertID(); break;
		}
        return false;
	}
}