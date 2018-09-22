<?php

define('DEFAULT_UPLOAD_DIR', 'uploads/');

class fileItem extends module_item {
  public $id;
  public $filename;
  public $codename;
  public $date;
  public $folder;
  public $owner;
  public $description;
  public $module;
  public $access;
  public $size;
  public $mime;
  public $inputName;

  public $isImage;

  public function __construct ($Params, $prefix = '') {
//    global $LOG;
    parent::__construct();
    if (isset($Params[$prefix.'id'])) $this->id = $Params[$prefix.'id']; else $this->id = 0;
    if (isset($Params[$prefix.'filename'])) $this->filename = $Params[$prefix.'filename']; else $this->filename = '';
    if (isset($Params[$prefix.'codename'])) $this->codename = $Params[$prefix.'codename']; else $this->codename = '';
    if (isset($Params[$prefix.'date'])) $this->date = $Params[$prefix.'date']; else $this->date = time();
    if (isset($Params[$prefix.'folder'])) $this->folder = $Params[$prefix.'folder']; else $this->folder = '';
    if (isset($Params[$prefix.'owner'])) $this->owner = $Params[$prefix.'owner']; else $this->owner = 0;
    if (isset($Params[$prefix.'description'])) $this->description = $Params[$prefix.'description']; else $this->description = '';
    if (isset($Params[$prefix.'module'])) $this->module = $Params[$prefix.'module']; else $this->module = 0;
    if (isset($Params[$prefix.'access'])) $this->access = $Params[$prefix.'access']; else $this->access = 0;
    if (isset($Params[$prefix.'size'])) $this->size = $Params[$prefix.'size']; else $this->size = 0;
    if (isset($Params[$prefix.'mime'])) $this->mime = $Params[$prefix.'mime']; else $this->mime = '';
    if (isset($Params[$prefix.'inputName'])) $this->inputName = $Params[$prefix.'inputName']; else $this->inputName = '';

    if (isset($Params[$prefix.'isImage'])) $this->isImage = $Params[$prefix.'isImage']; else $this->isImage = false;

    $this->notInsert['isImage'] = 1;
    /*
    if (!$nullFile) {
      if ($this->id == NULL || !$this->filename || !$this->codename || !$this->folder || !$this->module) {
        $LOG->addError('Невозможно создать экземпляр класса', __LINE__, __METHOD__, 0);
        return NULL;
      }
    } else {
      $this->id = 0;
      if ($this->module == '') $this->module = 'global';
      $this->owner = 0;
    }
    */
    return $this;
  }

  public function toArray() {
    $Params['id'] = $this->id;
    $Params['filename'] = $this->filename;
    $Params['codename'] = $this->codename;
    $Params['date'] = $this->date;
    $Params['folder'] = $this->folder;
    $Params['owner'] = $this->owner;
    $Params['description'] = $this->description;
    $Params['module'] = $this->module;
    $Params['access'] = $this->access;
    $Params['size'] = $this->size;
    $Params['mime'] = $this->mime;
    $Params['inputName'] = $this->inputName;
    $Params['isImage'] = $this->isImage;
    return $Params;
  }
}

class fileColl extends module_collection {
  public function __construct () {
    parent::__construct();
  }

  public function addItem($params) {
    $item = new fileItem($params);
    $this->add($item);
  }
  public function getFileByInputName($inputName) {
  	global $LOG;
  	$i = $this->getIterator();
  	$LOG->addtoLog(array('inputName'=>$inputName), __LINE__, __METHOD__);
  	foreach($i as $item) {
  		if ($item->inputName == $inputName) return $item;
  	}
  	return false;
  }
}

class fileInput extends module_item {
	public $inputName;
	public $label;
	/**
	 * Использовать описание для файла
	 * @var boolean
	 */
	public $isDescr;
	/**
	 * Использовать защищенный режим (показать БОКС приватности)
	 * @var boolean
	 */
	public $isAccess;
	/**
	 * File Item
	 * @var fileItem
	 */
	public $file;
	public function __construct ($Params) {
		parent::__construct();
		if (isset($Params['inputName'])) $this->inputName = $Params['inputName']; else $this->inputName = '';
		$this->id = $this->inputName;		
		if (isset($Params['label'])) $this->label = $Params['label']; else $this->label = '';		
		if (isset($Params['isDescr'])) $this->isDescr = $Params['isDescr']; else $this->isDescr = false;		
		if (isset($Params['isAccess'])) $this->isAccess = $Params['isAccess']; else $this->isAccess = false;		
		if (isset($Params['file'])) $this->file = $Params['file']; else $this->file = new fileItem(array());
		$this->notInsert['file'] = 1;		
	}
	
	public function toArray() {
		$Params['inputName'] = $this->inputName;
		$Params['label'] = $this->label;
		$Params['isDescr'] = $this->isDescr;
		$Params['isAccess'] = $this->isAccess;
		$Params['file'] = $this->file;
		return $Params;
  }	
}

class fileInputColl extends module_collection {
  public function __construct () {
    parent::__construct();
  }

  public function addItem($params) {
    $item = new fileInput($params);
    $this->add($item);
  }
  
  public function getFileByInputName($inputName) {
  	$i = $this->getIterator();
  	foreach($i as $fileInput) {
  		$item = $fileInput->file;
  		if ($item->inputName == $inputName) return $item;
  	}
  	return false;
  }
}

class fileModel extends module_model {
  public function __construct ($modName) {
    parent::__construct($modName);
  }

    public function add(fileItem $item, $assign) {
      $res = $item->toInsert();
      $sql = 'INSERT INTO files ('.$res[0].') VALUES('.$res[1].')';
      $q = array_merge(array(0=>$sql),$res[2]);
      if ($this->query($q)) {
        $id = $this->insertID();
        $item->setVal('id', $id);
//        Дописать связь файлов и модулей
        $sql = 'INSERT INTO files_modules (`essence_id`, `module`, `essence_module`) VALUES( %1$u, \'%2$s\', %3$u)';
        $this->query($sql, $id, $item->module, $assign);
        #exit($id);
        return true;
      }
      return false;
    }

    public function update($module, $essence_module) {
      global $vals;
//      $files = $this->getModule($module, $essence_module);
      foreach ($_FILES as $fname => $uFile) {
        $fpost = $vals->getVal($fname, 'POST', 'integer');
        if ($fpost > 0) {
          $r1 = $this->del($fpost, $essence_module);
          $r2 = $this->unlink($fpost);
          if ($r1 && $r2) $this->addToLog('Файл ['.$module.']::['.$fpost.'] удален', __LINE__, __METHOD__);
          else $this->addError(array('Ошибка удаления файла ',$fpost, $module, '', $essence_module), __LINE__, __METHOD__);
        } else {
          $r1 = $this->add($fpost);
          $r2 = $this->upload ($fpost, $uFile['tmp_name']);
          if ($r1 && $r2) $this->addToLog('Файл ['.$module.']::['.$fpost.'] загружен', __LINE__, __METHOD__);
          else $this->addError(array('Ошибка загрузки файла ',$fpost, $module, $item->id, $essence_module), __LINE__, __METHOD__);
        }
      }
      return true;
    }

    public function get($id) {
      $sql = 'SELECT * FROM `files` WHERE id = '.$id;
      $this->query($sql);
    if ($this->numRows() == 1) {
        #$coll = new fileColl();
        $file = $this->fetchRowA();
        $item = new fileItem($file);
        //$this->Log->addWarning($file, __LINE__, __METHOD__);
       # $collect->add($item);
        return $item;
      }
      $this->Log->addError(array('Файл не найден в БД ',$id), __LINE__, __METHOD__);
      return false;

    }
    public function getByCode($codename)  {
      $sql = 'SELECT * FROM `files` WHERE codename = \''.$codename.'\'';
      $this->query($sql);
      if ($this->numRows() == 1) {
        #$coll = new fileColl();
        $file = $this->fetchRowA();
        $item = new fileItem($file);
       # $collect->add($item);
        return $item;
      }
      return false;

    }
	/*
	 * 
	 * @param $moduleAssign
	 * @param $essence_module (id in moduleAssign)
	 * @return fileColl
	 */
    public function getModule($module, $essence_module) {
      $sql = 'SELECT f.`id`, f.`filename`, f.`codename`, f.`date`, f.`folder`, f.`owner`, f.`description`, f.`module`, f.`access`, f.`size`, f.`mime`, f.`inputName`,
                     fm.`id` as fmid, fm.`essence_id`, fm.`module`, fm.`essence_module`
              FROM `files_modules` fm
              INNER JOIN `files` f ON fm.essence_id = f.id
              WHERE fm.module = \'%1$s\' AND fm.essence_module = \'%2$s\' ';
      $this->query($sql, $module, $essence_module);
      $coll = new fileColl();
      if ($this->numRows() > 0) {
        while($file = $this->fetchRowA()) {
          $item = new fileItem($file);
          $coll->add($item);
          if (preg_match('/image/', $item->mime)) $item->isImage = true;
        }
      }
      return $coll;
    }

    public function getList($module, $owner, $all, $is_admin = false) {
      $sql = 'SELECT * FROM `files` ';
      $a = false;
      if ($module || $owner || $is_admin) $sql.= ' WHERE ';
      if ($module) { $sql.= ' `module` = \''.$module.'\' '; }
      if ($owner && !$is_admin) {
        if ($all && $a) $sql.= ' AND ';
        if (!$all && $a) $sql.= ' OR ';
        if ($all && !$a) $sql.= '';
        if (!$all && !$a) $sql.= '';
        $sql.= ' `owner` = \''.$owner.'\' ';
      }
      $this->query($sql);
      $collect = new fileColl();
      if ($this->numRows() > 0) {
        while($file = $this->fetchRowA()) {
            $item = new fileItem($file);
            if (preg_match('/image/', $item->mime)) $item->isImage = true;
            $collect->add($item);            
        }
      }
      return $collect ;
    }

/*
 * 
 * @param fileItem $item
 * @param $essence_module ID записи
 * @return boolean
 
    public function del(fileItem $item, $essence_module) {
    	if (!$item->id) {$this->Log->addError('Невозможно удалить файл, ID = 0'); return false;}
      $sql = 'DELETE FROM `files` WHERE id=%1$u';
      $res1 = $this->query($sql, $item->id);
      $sql = 'DELETE FROM `files_modules` WHERE `module` = \'%1$s\' AND essence_module = %2$u AND essence_id = %3$u';
      $res2 = $this->query($sql, $item->module, $essence_module, $item->id);
      $this->unlink($item);
      return ($res2 & $res1);
    }
*/
    
    /*
     * 
     * @param fileItem $item
     * @param $essence_module
     * @return $res2 & $res1
     */
    public function delFile (fileItem $item, $essence_module) {
    	if (!$item->id) {$this->Log->addError('Невозможно удалить файл, ID = 0','',''); return false;}
      $sql = 'DELETE FROM `files` WHERE id=%1$u';
      $res1 = $this->query($sql, $item->id);
      $sql = 'DELETE FROM `files_modules` WHERE `module` = \'%1$s\' AND essence_module = %2$u AND essence_id = %3$u';
      $res2 = $this->query($sql, $item->module, $essence_module, $item->id);
      
      $this->unlink($item);
      return ($res2 & $res1);
#      if ($res1 && $res2) $this->addToLog('Файл ['.$item->module.']::['.$item->filename.'] удален', __LINE__, __METHOD__);
#      else $this->addError(array('Ошибка удаления файла ',$item->filename, $item->module, $item->id, $essence_module), __LINE__, __METHOD__);
    }
	/**
	 * 
	 * @param fileItem $item
	 * @return boolean
	 */
    public function unlink(fileItem $item) {
    	if (!is_file(DIR_UPLOAD.$item->folder.'/'.$item->codename) && DEBUG == 1) {
    		$fp = fopen('debug/unlink.txt','a');
    		
      		$str = '['.date('d:m:Y H:i:s').'] Ошибка файл: '.DIR_UPLOAD.'\\'.join(', ',(array)$item).rn;
    		fwrite($fp,$str);
    		fclose($fp);
    		return false;
    	}
       $res3 = @unlink(DIR_UPLOAD.$item->folder.'/'.$item->codename);
       $fp = fopen('logs/unlink.txt','a');
       if ($res3) {          		
       	$str = 'ok ['.date('d:m:Y H:i:s').'] Удален файл: '.DIR_UPLOAD.'\\'.join(', ',(array)$item).rn;
       } else {
       	$str = 'er ['.date('d:m:Y H:i:s').'] Ошибка удаления файла: '.DIR_UPLOAD.'\\'.join(', ',(array)$item).rn;
       }
       fwrite($fp,$str);
       fclose($fp);
       
       return $res3;
    }

    public function link(fileItem $item) {

    }

    /*
    *   upload - используется в добавлении файла
    */
    public function upload(fileItem $item, $tmp_name) {
      if (is_uploaded_file ($tmp_name) ) {
        if (!is_dir(DIR_UPLOAD.$item->folder)) mkdir(DIR_UPLOAD.$item->folder, 0777, true);
//        $ext = explode('.',basename($item->filename));
        #$item->setVal('codename', $item->getVal('codename').'.'.$ext[1]);
        if(copy($tmp_name, DIR_UPLOAD.$item->folder.'/'.$item->codename)) {
#          $item->setVal('mime', mime_content_type(DIR_UPLOAD.$item->getVal('folder').'/'.$item->getVal('codename')));
          $this->Log->addError('Файл скопирован ->'.DIR_UPLOAD.$item->folder.'/'.$item->codename, __LINE__, __METHOD__);
          return true;
        } else $this->Log->addError('Не удалось скопировать файл ->'.DIR_UPLOAD.$item->folder.'/'.$item->codename, __LINE__, __METHOD__);
      } else $this->Log->addError('Файл не является загружаемым файлом', __LINE__, __METHOD__);
      return false;
#      $sql = 'DELETE FROM file WHERE id=%1$u';
#      $this->query($sql, $id);
    }

    public function download($codename) {
      if ($item = $this->getByCode($codename)) {
        header('Content-Type: '.$item->mime);
        #header('Content-Disposition: attachment; filename='.$item->getVal('filename').';');
        #header('Content-Transfer-Encoding: '.$item->getVal('mime'));
        #header('Content-Length: '.filesize($item->folder.'/'.$item->codename));
        @readfile(DIR_UPLOAD.$item->folder.'/'.$item->codename);
      } else exit('файл не доступен');
    }
    
    public function image($codename) {
      if ($item = $this->getByCode($codename)) {
        // открываем файл в бинарном режиме
        $name = trim($_SERVER["DOCUMENT_ROOT"].'/'.DIR_UPLOAD.$item->folder.'/'.$item->codename);
        if (!is_file($name)) exit ('Не файл:'. $name);
        $fp = fopen($name, 'rb');

        // отправляем нужные заголовки
        header('Content-Type: '.$item->mime.'; charset=utf-8');
        //header('Content-Type: '.$item->mime.'; charset=windows-1251');
        header("Content-Length: " . filesize($name));
        fpassthru($fp);
        fclose($fp);
        exit;
      } else exit('файл не доступен'.$codename);
    }
        
    public function mkDir($dir, $mode) {
        if (!is_dir($dir)) {
          mkdir($dir, $mode);
          return true;
        }
        return false;
    }

    public function mkDirPath($fullPath, $mode) {
      $dirs = explode('/', $fullPath);
      $path = '';
      foreach($dirs as $dir) {
        $path.=$dir;
        $this->mkDir($path, $mode);
        $path.='/';
      }
      if ($path == $fullPath) return true;
      return false;
    }
        
}

class fileFast extends mySQL {
	
	public function __construct() {
		parent::__construct('fileFast');	
	}
	
	public function update($action) {
		global $values;
		if ($action == 'image') {
			$codename = $values->getVal('image','GET','string');
			//stop($codename);
			$folder = $values->getVal('fd','GET','string');
			$mime = $values->getVal('mime','GET','string');
			$mime = str_replace('_','/',$mime);
			$this->fastimage2($codename, $folder, $mime);
		}	
	}
	
//	private function fastimage($codename) {
//		$sql = 'SELECT * FROM `files` WHERE codename = \''.$codename.'\'';
//    	$this->query($sql);
//    	if ($this->numRows() == 1) {
//    		#$coll = new fileColl();
//    		$file = $this->fetchRowA();
//    		$item = new fileItem($file);
//    		// открываем файл в бинарном режиме
//    		$name = trim($_SERVER["DOCUMENT_ROOT"].'/'.DIR_UPLOAD.$item->folder.'/'.$item->codename);
//    		if (!is_file($name)) exit ('Не файл:'. $name);
//    		$fp = fopen($name, 'rb');
//
//    		// отправляем нужные заголовки
//    		header('Content-Type: '.$item->mime.'; charset=utf-8');
//    		//header('Content-Type: '.$item->mime.'; charset=windows-1251');
//    		header("Content-Length: " . filesize($name));
//    		fpassthru($fp);
//    		fclose($fp);
//    		exit;
//    	}
//    }
    
    private function fastimage2($codename, $folder, $mime) {
    	global $LOG; 
    	list($fname, ) = explode('.',$codename);
		$name = trim($_SERVER["DOCUMENT_ROOT"].'/'.DIR_UPLOAD.$folder.'/'.$fname);
		//stop($name.','.$mime.', '.$codename.', '.$fname);
    	if ($codename != '') {    		    		
    		if (!is_file($name)) exit ('Не файл:'. $name);
    		$fp = fopen($name, 'rb');
    		// отправляем нужные заголовки
    		header('Content-Type: '.$mime.'');
    		//header('Content-Type: '.$item->mime.'; charset=windows-1251');
    		header("Content-Length: " . filesize($name));
    		fpassthru($fp);
    		fclose($fp);
    		$LOG->viewLog();
    		exit;
    	}           
    }	
}

class fileProcess extends module_process {
  protected $nModel;
  protected $nView;
//  private $pXSL;
  public $action;
  public $assignTo;
  public $assignModule;
  public $fileInputs;
  public $files;
  # protected $xml;
  /**
  * показывает, произошло ли обновление с помощью функции UPDATE или нидина из опций не была выполнена
  */
    public $updated;

  public function __construct ($modName, $assign = 0) {  	
  	
  	$this->assignModule = $modName;
  	$modName = 'files';
    parent::__construct($modName);
    
    $this->Log->addToLog(array('Модуль файлов', 'Связный модуль' => $this->assignModule), __LINE__, __METHOD__);
    
    $this->action = '';
    $this->assignTo = $assign;
    $this->fileInputs = new fileInputColl();
    $this->files = new fileColl();

    $this->nModel = new fileModel($modName);
    $this->nView = new fileView($this->modName, $this->sysMod);

    /* Default Process Class actions */
    $this->regAction('new', 'New', ACTION_PUBLIC);
    $this->regAction('add', 'Add', ACTION_PUBLIC);
    $this->regAction('edit', 'Edit', ACTION_PUBLIC);
    $this->regAction('update', 'Update', ACTION_PUBLIC);
    $this->regAction('view', 'View', ACTION_PUBLIC);
    $this->regAction('del', 'Delete', ACTION_PUBLIC);
    $this->regAction('delfile', 'Delete file', ACTION_PUBLIC);
    $this->regAction('viewIMG', 'View image', ACTION_PUBLIC);
    $this->regAction('download', 'View image', ACTION_PUBLIC);
    $this->regAction('image', 'View image', ACTION_PUBLIC);
    $this->regAction('file', 'download file', ACTION_PUBLIC);
    $this->regAction('upload', 'Upload file', ACTION_PUBLIC);
    $this->regAction('link', 'Lint to file', ACTION_PUBLIC);
    $this->regAction('newfi', 'New file with FileInputs scructure', ACTION_PUBLIC);
    $this->registerActions();
  }

  public function update($_action = false) {
      $this->updated = false;
      
      if ($_action) $this->action = $_action;
       # return 1;
       $action = false;
       if ($this->action) $action = $this->action;
       elseif ($a = $this->vals->getVal('new', 'GET') !== NULL) $action = 'new';
       elseif ($a = $this->vals->getVal('add', 'GET') !== NULL) $action = 'add';
       elseif ($a = $this->vals->getVal('edit', 'GET') !== NULL) $action = 'edit';
       elseif ($a = $this->vals->getVal('del', 'GET') !== NULL) $action = 'del';
       elseif ($a = $this->vals->getVal('view', 'GET') !== NULL) $action = 'view';
       elseif ($a = $this->vals->getVal('viewIMG', 'GET') !== NULL) $action = 'viewIMG';
       elseif ($a = $this->vals->getVal('download', 'GET') !== NULL) $action = 'download';
       elseif ($a = $this->vals->getVal('image', 'GET') !== NULL) $action = 'image';
       elseif ($a = $this->vals->getVal('file', 'GET') !== NULL) $action = 'file';
       elseif ($a = $this->vals->getVal('upload', 'GET') !== NULL) $action = 'upload';
       elseif ($a = $this->vals->getVal('link', 'GET') !== NULL) $action = 'link';
       elseif ($a = $this->vals->getVal('update', 'GET') !== NULL) $action = 'update';
       elseif ($a = $this->vals->getVal('newfi', 'GET') !== NULL) $action = 'newfi';
       
       $this->Log->addToLog('Обработка файлов ', __LINE__, __METHOD__);
       
  if (!$action) {
    	return false;
      }

      if ($action == 'new') {
      	$count = $this->vals->getVal('fileCount', 'GET', 'integer');
      	$Inputs = $this->nView->viewNew($count);
      	$this->updated = true;
      	return $Inputs;
      }
      
  	if ($action == 'newfi') {
//      	$count = $this->vals->getVal('fileCount', 'GET', 'integer');
      	
      	$fileInputs = $this->fileInputs;      	
      	$Inputs = $this->nView->viewNewFI($fileInputs);
      	$this->updated = true;
      	return $Inputs;
      }
      
      if ($action == 'add') {
      	if (($this->assignTo != 0 && $this->modName != '')) {
      		$p = array();
      		$collect = new fileColl();

      		foreach($_FILES as $postName => $filetmp) {
      			$p['id'] = 0;
      			$p['filename'] = $filetmp['name'];
      			$p['codename'] = $this->generateCode(15);
      			$p['date'] = time();
      			$p['folder'] = $this->assignModule;
      			$p['owner'] = $this->User->getUserID();
      			$p['description'] = $this->vals->getVal('filename1_description','POST', 'string');
      			$p['module'] = $this->assignModule;
      			# $p['module_essence'] = $this->modName;
      			$p['access'] = 1;
      			$p['size'] = 0;
      			$p['mime'] = $filetmp['type'];
      			$p['inputName'] = $this->vals->getVal('inputName','POST', 'string');
      			$this->Log->addWarning($p, __LINE__, __METHOD__);
      			$file = new fileItem($p);
      			$this->Log->addWarning($file->filename, __LINE__, __METHOD__);
      			$up = $this->nModel->upload($file, $filetmp['tmp_name']);
      			if ($up) {
      				$this->nModel->add($file, $this->assignTo);
      				$this->Log->addToLog('Загружен файл: '.$file->filename.' ID: '.$file->id, __LINE__, __METHOD__);
      			} else {
          			$this->Log->addError('Ошибка загрузки файла : '.$file->filename, __LINE__, __METHOD__);
      			}
      			$collect->add($file);
      		}
      		$this->updated = true;
      	} else $this->Log->addError('Невозможно добавить файл - отсутствует связь с модулем', __LINE__, __METHOD__);
      }
      
	  if ($action == 'addfi') {
	      	if (($this->assignTo != 0 && $this->modName != '')) {
	      		$p = array();
	      		$collect = new fileColl();
	
	      		foreach($_FILES as $postName => $filetmp) {
	      			$p['id'] = 0;
	      			$p['filename'] = $filetmp['name'];
	      			$p['codename'] = $this->generateCode(15);
	      			$p['date'] = time();
	      			$p['folder'] = $this->assignModule;
	      			$p['owner'] = $this->User->getUserID();
	      			$p['description'] = $this->vals->getVal('filename1_description','POST', 'string');
	      			$p['module'] = $this->assignModule;
	      			# $p['module_essence'] = $this->modName;
	      			$p['access'] = 1;
	      			$p['size'] = 0;
	      			$p['mime'] = $filetmp['type'];
	      			$p['inputName'] = $this->vals->getVal('inputName','POST', 'string');
	      			$this->Log->addWarning($p, __LINE__, __METHOD__);
	      			$file = new fileItem($p);
	      			$this->Log->addWarning($file->filename, __LINE__, __METHOD__);
	      			$up = $this->nModel->upload($file, $filetmp['tmp_name']);
	      			if ($up) {
	      				$this->nModel->add($file, $this->assignTo);
	      				$this->Log->addToLog('Загружен файл: '.$file->filename.' ID: '.$file->id, __LINE__, __METHOD__);
	      			} else {
	          			$this->Log->addError('Ошибка загрузки файла : '.$file->filename, __LINE__, __METHOD__);
	      			}
	      			$collect->add($file);
	      		}
	      		$this->updated = true;
	      	} else $this->Log->addError('Невозможно добавить файл - отсутствует связь с модулем', __LINE__, __METHOD__);
	      }
      
      if ($action == 'update') {
      	$p = array();
//      	$collect = new fileColl();
      	//$this->Log->addToLog($_FILES, __LINE__, __METHOD__);
      	foreach($_FILES as $postName => $filetmp) {
      		$p['id'] = 0;
      		$p['filename'] = $filetmp['name'];
      		$p['codename'] = $this->generateCode(15);
      		$p['date'] = time();
      		$p['folder'] = $this->assignModule;
      		$p['owner'] = $this->User->getUserID();
      		$p['description'] = $this->vals->getVal('filedescription_'.$postName,'POST', 'string');
      		$p['module'] = $this->assignModule;
      		# $p['module_essence'] = $this->modName;
      		$p['access'] = 1;
      		$p['size'] = 0;
      		$p['mime'] = $filetmp['type'];
      		$p['inputName'] = $this->vals->getVal('inputName_'.$postName,'POST', 'string');
      		$file = new fileItem($p);
      		$up = $this->nModel->upload($file, $filetmp['tmp_name']);
      		if ($up) {
      			$this->nModel->add($file, $this->assignTo);
      		}
//      		$oldFile = $this->Vals->getVal('file_'.$postName, 'POST', 'string');
      		$oldFileID = $this->Vals->getVal('file_'.$postName, 'POST', 'string');
      		//if (preg_match('/([0-9]*)$/',$postName, $tmp)) $oldFileID = $tmp[1]; else $oldFile = 0;
      		
      		//$oldFile = $this->Vals->getVal($_FILES, 'POST', 'string');
      		//$this->Log->addToLog(array('oldFile'=>$oldFile, '$postName'=>$postName, 'oldFileID'=>$oldFileID), __LINE__, __METHOD__);
      		if ($oldFileID && $up) {
      			$oFile = $this->nModel->getByCode($oldFileID);
      			if (gettype($oFile) == 'object') {
      				$r1 = $this->nModel->delFile($oFile, $this->assignTo);
      				if($r1) $r2 = $this->nModel->unlink($oFile); else $r2 = false;
      				if (!$r1) $this->Log->addError('Файл не удален из БД', __LINE__, __METHOD__);
      				if (!$r2) $this->Log->addError('Файл не стерт с диска', __LINE__, __METHOD__);
      			} else $this->Log->addError('Ошибка загрузки файла', __LINE__, __METHOD__);
      		} else $this->Log->addToLog(array('Нет файла для удаления'=>$oldFileID), __LINE__, __METHOD__);
      	}
      }
      
      if ($action == 'edit') {
      	$this->Log->addToLog(array($this->assignModule, $this->assignTo), __LINE__, __METHOD__);
      	$collect = $this->nModel->getModule($this->assignModule, $this->assignTo);
      	$form = $this->nView->viewEdit($collect, 1);
      	return $form;
      }
      
      if ($action == 'editfi') {
      	$this->Log->addToLog(array($this->assignModule, $this->assignTo), __LINE__, __METHOD__);
      	$collect = $this->nModel->getModule($this->assignModule, $this->assignTo);
      	
      	$fileInputs = $this->fileInputs;
      	$i = $fileInputs->getIterator();
      	foreach ($i as $fileInput) {
      		$file = $collect->getFileByInputName($fileInput->inputName);
      		if ($file) $fileInput->file = $file; 
      	}
      	$form = $this->nView->viewEditFI($fileInputs);
      	return $form;
      }
            
      if ($action == 'del') { $this->updated = true; }
      if ($action == 'delfile') {
      	$this->delFilesModule($this->assignTo); 
      	$this->updated = true; 
      }

      /** view - xml файлов в виде тегов картинок со ссылкой на download */
      if ($action == 'viewIMG') {
      	$essence_module = $this->vals->getVal('essence_module', 'INDEX', 'integer');
      	if (!$essence_module) $essence_module = $this->assignTo;

      	$coll = $this->nModel->getModule($this->assignModule, $essence_module);
      	if ($coll->count()) {
      		$this->nView->getImgTags ($coll);
      	} else {
      		$this->Log->addWarning(array('Пустая коллекция ', $essence_module, $this->modName), __LINE__, __METHOD__);
      		$fake = new fileItem(array());
      		$fake->isImage = false;
      		$coll->add($fake);
      		$this->nView->getImgTags ($coll);
      	}
      	$this->updated = true;
      	#stop($this->nView->getBody(),0);
      }
      /** link ссылка на файл, со всеми параметрами */
      if ($action == 'link') { $this->updated = true; }
      /** download - отправка файла клиенту */
      if ($action == 'download') {
      	$this->nModel->download($this->vals->getVal('file','GET','string'));
      	$this->updated = true;
      }
  
      if ($action == 'file') {
      	$this->nModel->download($this->vals->getVal('file','GET','string'));
      	$this->updated = true;
      }
      
      if ($action == 'image') {
      	$this->nModel->image($this->vals->getVal('image','GET','string'));
      	$this->updated = true;
      }
//  	if ($action == 'fastimage') {
//      	$this->nModel->fastimage($this->vals->getVal('image','GET','string'));
//      	$this->updated = true;
//      }
//
//      if (!$this->updated) {
//
//      }
//
      return $this->updated;
    }

    function generateCode($length=6)
    {
       $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPRQSTUVWXYZ0123456789";
       $code = "";
       $clen = strlen($chars) - 1;
       while (strlen($code) < $length)
          {
            $code .= $chars[mt_rand(0,$clen)];
          }
       return $code;
    }

    public function mkDirPath ($path, $mode) {
      $path = trim($path);
      return $this->nModel->mkDirPath($path, $mode);
    }
    public function dropBody() {
    	$this->nView->dropBody();
    }
    
    /**
     * Удаляет все файлы сущности модуля
     * Модуль и сущность задаеются в конструкторе  
     * @param $essence_id
     * @return true
     */
    public function delFilesModule($essence_id) {
    	$items = $this->nModel->getModule($this->assignModule, $essence_id);
    	$i = $items->getIterator();
    	foreach($i as $item) {
    		$this->nModel->delFile($item, $essence_id);
    	}
    	return true;	
    }

    public function delFile ($file_id) {
    	$item = $this->nModel->get($file_id);
    	if ($item->id == $file_id) {
    		$this->nModel->delFile($item, $this->assignTo);
    	}
    }
    
//    public function loadModule($modName, $essences) {
//    	$this->nModel->getModuleList($modName, array());
//    }
    
    public function viewImg(fileColl $coll) {      	
      	if ($coll->count()) {
      		$this->nView->getImgTags ($coll);
      		$this->Log->addToLog(array('xxxx Обработка колл. файлов ', $coll->count()), __LINE__, __METHOD__);
      	} else {
      		$this->Log->addWarning(array('Пустая коллекция ', '', $this->modName), __LINE__, __METHOD__);
      		$fake = new fileItem(array());
      		$fake->isImage = false;
      		$coll->add($fake);
      		$this->nView->getImgTags ($coll);
      	}
    }
}

class fileView extends module_view {
  public function __construct ($modName, $sysMod) {
    $modName = 'file';
    parent::__construct($modName, $sysMod);
    $this->pXSL = array();
  }

  public function viewNew($user_id, $count = 1) {
    #$this->pXSL = RIVC_ROOT.'layout/'.$this->modName.'.new.xsl';
    $this->pXSL[] = RIVC_ROOT.'layout/form.xsl';
    #$form = new CFormGenerator('file', SITE_ROOT.$this->modName.'/update-'.$item->getID().'/', 'POST', 0);
    $form = new CFormGenerator('file', SITE_ROOT.$this->modName.'/add-1/', 'POST', 0);
    #$form->addHidden('add', '1', 'add');
    $form->addHidden('user_id', $user_id, 'user_id');
    for($i=1;$i<=$count;$i++) {
      $form->addFile('filename'.$i, '', 'Файл', 'filename'.$i, '', '','filename');
    #($name, $value, $label, $id, $class, $size)
      $form->addBox('access'.$i, '', 'Доступ', 'Публичный файл', 0, 'access'.$i, '', '');
    }
  #                   ($name, $value, $label, $defT, $defV, $id, $class, $size)
    return $form->getInputs();
  }
  
  public function viewNewFI(fileInputColl $fileInputs) {   
    $this->pXSL[] = RIVC_ROOT.'layout/form.xsl';    
    $form = new CFormGenerator('file', SITE_ROOT.$this->modName.'/add-1/', 'POST', 0);
    $i = $fileInputs->getIterator();
    foreach($i as $fileInput) {    	
      $form->addFileFI($fileInput->inputName, '', $fileInput->label, $fileInput->inputName, '', '',$fileInput->inputName);
      $this->Log->addToLog(array('isDescr'=>$fileInput->isDescr), __LINE__, __METHOD__);    
      if ($fileInput->isDescr) $form->addTextArea('filedescription_'.$fileInput->inputName, '', 'Описание',$fileInput->inputName,'',45);
      if ($fileInput->isAccess) $form->addBox('access_'.$fileInput->inputName, '', 'Доступ', 'Публичный файл', 1, $fileInput->inputName, '', '');
    }
  #                   ($name, $value, $label, $defT, $defV, $id, $class, $size)
    return $form->getInputs();
  }

  public function viewEdit(fileColl $collect, $count = 1) {
//    global $User;
    $this->pXSL[] = RIVC_ROOT.'layout/'.$this->modName.'.new.xsl';

    $form = new CFormGenerator('file', SITE_ROOT.$this->modName.'/update-0/', 'POST', 0);

    $iterator = $collect->getIterator();
    $index = 1;
    foreach ($iterator as $item) {
      $form->addFile('filename'.$index, $item->filename, 'Файл', 'filename'.$index, '', '', $item->codename);
      //$form->addHidden('filename_id'.$item->id, $item->id, 'update');
      // $form->addBox('access'.$item->getID(), $item->access, 'Доступ', 'Публичный файл', 0, 'access'.$item->getID(), '', '');
      $index++;
    }
    for($i=$index;$i<=$count;$i++) {
      $form->addFile('filename'.$i, '', 'Файл', 'filename'.$i, '', '', '');
      //$form->addBox('access'.$i, '', 'Доступ', 'Публичный файл', 0, 'access'.$i, '', '');

    }
    return $form->getInputs();
  }
  
  public function viewEditFI(fileInputColl $fileInputs) {
//    global $User;
    $this->pXSL[] = RIVC_ROOT.'layout/'.$this->modName.'.new.xsl';

    $form = new CFormGenerator('file', SITE_ROOT.$this->modName.'/update-0/', 'POST', 0);
//stop($fileInputs->count());
    $iterator = $fileInputs->getIterator();
//    $index = 1;
    foreach ($iterator as $fileInput) {
    	$item = $fileInputs->getFileByInputName($fileInput->inputName);
    	if (!$item) $item = new fileItem(array());
    	//stop($fileInput->inputName.' '.$item->codename.' '.$item->id.' '.$item->filename.' '.gettype($item), 0);
      $form->addFileFI($fileInput->inputName, $item->filename, $fileInput->label, $fileInput->inputName, '', '',$fileInput->inputName);    
      if ($fileInput->isDescr) $form->addTextArea($fileInput->inputName, $item->description, 'Описание',$fileInput->inputName,'','');
      if ($fileInput->isAccess) $form->addBox($fileInput->inputName, $item->access, 'Доступ', 'Публичный файл', 1, $fileInput->inputName, '', '');
    }
    return $form->getInputs();
  }

  public function viewList(fileColl $collect) {
    #$this->Log->addToLog('Вход', __LINE__, __METHOD__);
    $this->pXSL[] = RIVC_ROOT.'layout/'.$this->modName.'.viewlist.xsl';
    #$files = $this->addToNode($this->items, 'files','');
    $iterator = $collect->getIterator();
    foreach ($iterator as $item) {
      #$this->arrToXML($item->getVals(), $this->items, '');
      $file = $this->addToNode($this->items, 'file',$item->filename);
      $this->addAttr('code', $item->codename, $file);
      $this->addAttr('id', $item->id, $file);
      $this->addAttr('description', $item->description, $file);
    }
    return true;
  }

    public function link(fileItem $item) {
      $files = $this->addToNode($this->items, 'files','');
      $file = $this->addToNode($files, 'file',$item->filename);
      $this->addAttr('code', $item->codename, $file);
      $this->addAttr('id', $item->codename, $file);
/*      $link = 'files.php?file='.$item->filename.'
#      $sql = 'DELETE FROM file WHERE id=%1$u';
#      $this->query($sql, $id);
*/
    }

    public function getImgTags(fileColl $collect) {
      $this->pXSL[] = RIVC_ROOT.'layout/file.view.xsl';
      $images = $this->newContainer('images');
      #$images = $this->addToNode($this->items, 'images');
      $iterator = $collect->getIterator();
      $this->addAttr('count', $collect->count(), $images);
      
      foreach ($iterator as $item) {
      	$item->mime = str_replace('/','_',$item->mime);
      	$data = $item->toArray();
      	$data['ext'] = substr($item->filename,-3);      	     
        $this->arrToXML($data, $images, 'image');
//        $this->addAttr('inp', $item->inputName, $img);
        $this->Log->addToLog($item->toArray(), __LINE__, __METHOD__);
      }
      #stop($this->getBody(),0);
      return true;
    }

    public function links(fileColl $collect) {
      $files = $this->addToNode($this->items, 'files','');
      $iterator = $collect->getIterator();
      foreach ($iterator as $item) {
        $file = $this->addToNode($files, 'file',$item->filename);
        $this->addAttr('code', $item->codename, $file);
        $this->addAttr('id', $item->codename, $file);
      }
      return true;
    }
   public function dropBody() {
   	parent::dropBody();
   }
   
   public function fileGallery(fileColl $collect) {
   	$this->pXSL[] = RIVC_ROOT.'layout/file.view.xsl';	
   	$gallery = $this->newContainer('fileGallery');
   	$iterator = $collect->getIterator();
    foreach ($iterator as $item) {
        $this->arrToXML($item->toArray(), $item, 'file');
//        $this->addAttr('inp', $item->inputName, $img);
        //$this->Log->addToLog($item->toArray(), __LINE__, __METHOD__);
      }
   }
}
