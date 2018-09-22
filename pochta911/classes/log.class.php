<?php

# addToLog('', __LINE__, __METHOD__, __CLASS__);
# addToLog('', __LINE__, __FUNCTION__);

#define ('rn', "\r\n");
#define ('bn', "<br />\r\n");

define ('CLASS_LOG', 1);

function stop ($msg, $exit = 1) {
	if(!headers_sent()) {
		header('Content-Type: text/html; charset=UTF-8;');
	}
  if (is_array($msg) || is_object($msg)) {
    echo '<hr /><pre style="color: #FF0000">'.br;
    if ( @get_class($msg) == 'DOMDocument') {
      $xml = new DOMDocument('1.0');
      $xml->formatOutput = true;
      $xml->preserveWhiteSpace = false;
      $s = $msg->saveXML();
      #echo $s;
      $xml->loadXML($s);
      $msg = $xml->saveXML();
      #$msg=str_replace("\r\n", "<br />\r\n",$msg);
      #$msg=htmlspecialchars($msg);

    }
    #$msg = iconv('windows-1251','utf-8', $msg);
    print_r ($msg);
    echo '</pre><hr />'.br;
  } else {
    echo '<hr /><pre style="color: #FF0000">'.br;
    echo gettype($msg).':'.br;
    echo $msg;
    echo '</pre><hr />'.br;
  }
  if ($exit) exit;
}
function stopS ($msg, $exit = 1) {
  if (gettype($msg) == 'string') {

    echo '<p style="color: #FF0000">'.br;
    echo gettype($msg).':'.br;
    echo $msg;
    echo '</p>'.br;
  }
  if ($exit) exit;
}


class TLog {
  private $_log;
  private $messages;
  private $timeStart;
  
  private $checkPoint;

  private function addText ($text, $tab = '') {
    $p = '';
    switch(gettype($text)) {
      case 'array':
        foreach ($text as $k => $v) {
          if (gettype($v) == 'array') $p.= $k.' = {<br /> '.$this->addText($v, $tab."\t&nbsp;&nbsp;").' }<br />';
          else $p.= $tab.$k.' => '.$v."<br />\r\n";          
        }
      break;
      case 'string':
            $p = htmlspecialchars($text);
        break;
      case 'boolean':
      		if ($text == false) $p = 'FALSE';
      		if ($text == true) $p = 'TRUE';
        break;
       case 'integer':
      		$p = intval($p);
        break;
      default: $p = $text;

    }
    return $p;
  }

  public function __construct() {
    $this->_log = array();
    $this->messages = array();
    $this->timeStart = $this->getMicroTime();
    $this->checkPoint = 0;
    $this->addSysMSG('System Start: '.date('d.m.Y h:i').' '.$this->timeStart, __METHOD__ );
    #$this->addToLog('Log create: '.date('d.m.Y h:i'), __LINE__, __METHOD__, __CLASS__ );
  }

  private function getMicroTime() {
    list($usec, $sec) = explode(" ", microtime());
    return ((float)$usec + (float)$sec);
  }

  public function addToLog ($msg, $line = '', $method = '', $class = '') {
    $msg = array('m', $msg, $line, $method,$class);
    #$this->_log[] = 'm: '.' '.$method.' '.$line.' '.bn.$this->addText($msg);
    $this->_log[] = $msg;
  }
  public function addToLogSQL ($msg) {
    $msg = array('sql', $msg, 0, 'SQL');
    #$this->_log[] = 'm: '.' '.$method.' '.$line.' '.bn.$this->addText($msg);
    $this->_log[] = $msg;
  }
  public function addSysMSG ($msg, $line = '', $method = '') {
    $msg = array('s', $msg, $line, $method);
    #$this->_log[] = 'm: '.' '.$method.' '.$line.' '.bn.$this->addText($msg);
    $this->_log[] = $msg;
  }
  public function addWarning ($msg, $line, $method) {
    $msg = array('w', $msg, $line, $method);
    #$this->_log[] = 'w: '.' '.$method.' '.$line.' '.$this->addText($msg);
    $this->_log[] = $msg;
  }
  public function addError ($msg, $line, $method, $stop=0) {
    $msg = array('e', $msg, $line, $method);
    #$this->_log[] = 'e: '.' '.$method.' '.$line.' '.$this->addText($msg);
    $this->_log[] = $msg;
    if ($stop) exit(print_r($this->_log));
  }
  
  public function addCheckPoint ($msg, $line, $method) {
    $msg = array('cp', $msg, $line, $method);
    $this->checkPoint++;
    $this->_log[] = $msg; 	
  }
  
  public function viewLog ($style = true) {

    $this->addSysMSG ('System work time: '.number_format(($this->getMicroTime() - $this->timeStart), 4));
    # <![CDATA[//   ]]>
    $s = '<style>
	    #log_view { background-color: #dadada;  padding:5px; width: 1000px;} 
	    #log_view DIV { background-color: #cacaca; margin-top:5px;} 
	    #log_view P {margin:0; padding:0;} #log_view .log_message{ color: #005500;} 
	    #log_view .log_warning { color: #CC6600;} 
	    #log_view .log_error { color: #FF0000; }
	    #log_view .log_system { color: #3333FF; font-weight: bold; } 
	    #log_view .log_checkPoint { color: #B000B0; font-weight: bold; } 
	    #log_view .log_SQL { color: #003366; font-weight: normal; } 
    </style>';
    if ($style) $a = $s; else $a = '<![CDATA[
    //'.$s.'
    ]]>';
    $a.= '<div id="log_view"><h1>Журнал:</h1>';
    for($i=1; $i <= $this->checkPoint; $i++) {
    	$a.= '<p class="log_checkPoint"><a href="#cp'.$i.'">Check Point #'.$i.'</a></p>'.rn;
    }
    $checkPoint = 1;
    $fp = fopen('debug/log.txt','w');
    foreach ($this->_log as $msg) {
        switch ($msg[0]) {
          case 'm': $class="log_message"; $text = $this->addText($msg[1]); break;
          case 'w': $class="log_warning"; $text = $this->addText($msg[1]); break;
          case 'e': $class="log_error"; $text = $this->addText($msg[1]);   break;
          case 's': $class="log_system"; $text = $this->addText($msg[1]);  break;
          case 'cp': $class="log_checkPoint"; $text = $this->addText($msg[1]);  break;
          case 'sql': $class="log_SQL"; $text = $this->addText($msg[1]);  break;

        }
        fwrite($fp, $text.rn);
        if (strlen($text) > 500 && $msg[0] != 'sql' && $msg[0] != 's' && $msg[0] != 'e') {
        	$text = substr($text,0,300);
        	$text.= '... &gt;&gt;';
        }
        $a.= '<div class="'.$class.'">';
        if ($msg[0] != 's' && $msg[0] != 'cp') { 
        	if ($msg[2]) $a.='<p>Line: '.$msg[2]; 
        	if ($msg[3]) $a.=' module: <b>'.$msg[3];
        	$a.= '</b> ['.date('d.m.Y H:i:s').']</p>';
        }
        if ($msg[0] == 'cp') {
        	$a.= '<p id="cp'.$checkPoint.'">Check Point #'.$checkPoint.'</p>';
        	$checkPoint++;	
        }
        $a.='<p>'.$text.'</p>';
        $a.='</div>';
    }
    $a.='</div>';
    
    fclose($fp);
        return $a;
        
  }
}

?>