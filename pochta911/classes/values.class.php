<?php

class TValues {
  private $post;
  private $get;
  private $cookie;
  private $index;
  private $log;
  /**
   * Переменные для модулей
   * @var array()
   */
  private $moduleVals;
  private $varError;
  
    public function __construct() {
        global $LOG;
        $this->log = $LOG;
        $this->index = array();
        $this->post = array();
        $this->get = array();
        $this->cookie = array();
        $this->moduleVals = array();
        $this->varError = false;

        foreach ($_POST as $name => $val) $this->setValTo($name, $val, 'post');
        foreach ($_GET as $name => $val) $this->setValTo($name, $val, 'get');
        foreach ($_COOKIE as $name => $val) $this->setValTo($name, $val, 'cookie');
        /*
        unset($_POST);
        unset($_GET);
        unset($_COOKIE);
        */
        $_POST = array();
        $_GET = array();
       # $_COOKIE = array();       
    }
    public function setValTo($name, $val, $method) {
      $this->log->addSysMSG(array('Установка переменной', $name => $val, 'method: '.$method));
        switch (strtolower($method)) {
          case 'post': $this->post[$name] = $val; break;
          case 'get': $this->get[$name] = $val; break;
          case 'cookie': 
          		$this->cookie[$name] = $val;
        //  		setcookie($name, $val, time()+60*60*24*30);  
          	break;
          default: break;
        }
        if (!isset($this->index[$name])) $this->index[$name] = $val;
        elseif (!isset($this->index[$name.'-'.$method])) $this->index[$name.'-'.$method] = $val;
    }

    public function setVal($name, $val, $method = 'GET', $safe = false, $prefix = '') {
      if ($safe && isset($this->$name)) {
        if ($prefix) $this->setVal($prefix.' '.$name, $val);
        else return false;
      }
      if (!isset($this->$name)) $this->setValTo($name, $val, $method);
      #$this->$name = $val;
      return true;
    }

    public function setVals($vals) { foreach ($vals as $key=>$val) $this->setVal($key,$val); }

    public function getVal($valName, $method = '', $type = false, $enymethod = false) {
      $result = NULL;
      $this->varError = false;
      switch (strtolower($method)) {
        case 'post': if(isset($this->post[$valName])) $result = $this->post[$valName]; break;
        case 'get': if(isset($this->get[$valName])) $result = $this->get[$valName]; break;
        case 'cookie': if(isset($this->cookie[$valName])) $result = $this->cookie[$valName]; break;
        case 'index': if(isset($this->index[$valName])) $result = $this->index[$valName]; break;
        default:
            if (!isset($this->index[$valName]) && !isset($this->post[$valName]) && !isset($this->get[$valName]) && !isset($this->cookie[$valName]))  return NULL;
            else $this->index[$valName];
            if ($v = $this->getVal($valName, 'POST') !== NULL) {$result = $v;  break;   }
            if ($v = $this->getVal($valName, 'GET') !== NULL) {$result = $v;  break;   }
            if ($v = $this->getVal($valName, 'COOKIE') !== NULL) {$result = $v;  break;   }
             break;
            #$this->index[$name]
            /* addToLog - запрос не существующей переменной */
      }
      if (!isset($result)) {
        $r = NULL;
        switch ($type) {
          case false: $r=''; break;
          case 'int':
          case 'integer':  $r = intval(0); break;
          case 'str':  $r = strval(''); break;
          case 'string':  $r = strval(''); break;
          case 'array':  $r = array(); break;
          default: $r=''; $this->varError = true; break;
        }
      } else {
        $r = $result;
        switch ($type) {
          case false: ; break;
          case 'int':
          case 'integer':  $r = intval($r); break;
          case 'str':  $r = strval($r); break;
          case 'string':  $r = strval($r); break;
          case 'array':  if (!is_array($r)) $r = array($r); break;
          default: $r=''; $this->varError = true; break;
        }

      }
      $this->log->addToLog('Запрос переменной: '.json_encode($valName).' '.json_encode($r).' '.$method, __LINE__, __METHOD__, __CLASS__);
      return $result;
    }   
    /**
    * return all values ARRAY
    */
    public function getVals() { foreach ($this as $key=>$val) $res[$key] = $val; return $res; }
    /* public function getID() {return $this->id; }*/

    public function isVal($valName, $method) {
      $result = false;
      switch (strtolower($method)) {
        case 'post': if(isset($this->post[$valName])) $result = true; break;
        case 'get': if(isset($this->get[$valName])) $result = true; break;
        case 'cookie': if(isset($this->cookie[$valName])) $result = true; break;
        case 'index': if(isset($this->index[$valName])) $result = true; break;
        default: $result = false; break;
      }
      return $result;
    }

    public function isNaN($val) {
      if (preg_match('/[a-zA-Z]/', $val)) return true;
      else return false;
    }

    private function recDebug ($array) {
        $a = '';
        foreach ($this->index as $key=>$val) {
            if (gettype($val) != 'array') $a.= '<br />'.$key.':'.$val;
            else $a.= '&nbsp;&nbsp;'.$this->recDebug($val);
        }
        return $a;
    }

    public function viewDebug() {
        return 'dump: '."\r\n".$this->recDebug($this->index);
    }
    
    public function URLparams($qString) {
    	if (!strpos($qString,'&')) return false;
    	$aString = explode('&', $qString);
    	foreach ($aString as $sub) {
    		if(!$sub) continue;
    		if (strpos($sub,'=') === false && strpos($sub,'-') !== false) {
    			list($vName, $val) = explode('-',$sub);
    		} else {
    			list($vName, $val) = explode('=',$sub);
    		} 
    		$this->setValTo($vName,$val, 'GET');
    	}
    	return true;
    }
    /**
     * 
     * @param $module
     * @param $name
     * @param $value
     * @param $type
     * @return unknown_type
     */
    public function setModuleParam($module, $name, $value, $type) {
    	$this->moduleVals[$module][$name] = $value;
    	settype($this->moduleVals[$module][$name], $type);
    	return true;
    }
    /**
     * 
     * @param $module
     * @param $qString
     * @return unknown_type
     */
    public function setModuleParamQS($module, $qString) {
    	if (!strpos($qString,'&')) return false;
    	$aString = explode('&', $qString);
    	foreach ($aString as $sub) {
    		if(!$sub) continue;
    		if (strpos($sub,'=') === false && strpos($sub,'-') !== false) {
    			list($vName, $val) = explode('-',$sub);
    		} else {
    			list($vName, $val) = explode('=',$sub);
    		}    		
    		$this->log->addSysMSG(array('Установка модульной переменной', $vName => $val, 'module'=>$module));
    		$this->setModuleParam($module, $vName, $val, gettype($val));
    	}
    	return true;
    }
    /**
     * 
     * @param $module
     * @param $vName
     * @param $vType
     * @return unknown_type
     */
    public function getModuleVal ($module, $vName, $vType) {
    	$this->log->addToLog(array('Запрос модульной переменной: ',$module,$vName,$vType), __LINE__, __METHOD__, __CLASS__);
    	$this->varError = false;
    	if (isset($this->moduleVals[$module][$vName])) {
    		$this->log->addToLog(array('Переменная найдена', $this->moduleVals[$module][$vName]), __LINE__, __METHOD__, __CLASS__);
    		return $this->moduleVals[$module][$vName]; 
    	}
    	else { 
    		$this->log->addToLog(array('Переменная отсутствует'), __LINE__, __METHOD__, __CLASS__);
    		$this->varError = true; 
    		return false; 
    	}
    }    
}



?>