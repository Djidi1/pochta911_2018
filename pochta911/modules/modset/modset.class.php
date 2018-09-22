<?php

class modsetModel extends module_model {

  public function __construct ($modName) {
    if (!$modName) {
      header('Content-Type: charset=windows-1251;');
      exit ('Не указано название модуля'.get_class($this));
    }
    parent::__construct($modName);
  }

  public function add(modsetItem $item) {
    $res = $item->toInsert();
    if ($item->name == '' || $item->codename = '') return false;
    $sql = 'INSERT INTO '.TAB_PREF.'modules ('.$res[0].') VALUES('.$res[1].')';
    #stop($sql);
    $q = array_merge(array(0=>$sql),$res[2]);
    $this->query($q);
    $id = $this->insertID();
    $item->setVal('id', $id);
    if ($item->id == 0) {
      $this->Log->addError('Модуль не добавлен, действия не регистрируются', __LINE__, __METHOD__);
      return false;
    } else $this->Log->addToLog('Модуль добавлен '.$id, __LINE__, __METHOD__);

    if ($item->parentMod != 0) {
    	$actionsColl = $this->getActions($item->parentMod);
//    	$i = $actionsColl->getIterator();
    	foreach($actionsColl as $action) {    		
    		$action->mod_id = $item->id;
    		//$action = new moduleAction();
    		$action->group_adm = 0;
    	}
    } else {
	    /* Default Process Class actions */
	    $actionsColl = new actionColl();
	    list($Params['id'], $Params['mod_id'], $Params['action_name'], $Params['action_title'], $Params['access']) =
	    array(0, $id, 'new', 'New', ACTION_GROUP);
	    $actionsColl->addItem($Params);
	
	    list($Params['id'], $Params['mod_id'], $Params['action_name'], $Params['action_title'], $Params['access']) =
	    array(0, $id, 'add', 'Add', ACTION_GROUP);
	    $actionsColl->addItem($Params);
	
	    list($Params['id'], $Params['mod_id'], $Params['action_name'], $Params['action_title'], $Params['access']) =
	    array(0, $id, 'view', 'View', ACTION_GROUP);
	    $actionsColl->addItem($Params);
	
	    list($Params['id'], $Params['mod_id'], $Params['action_name'], $Params['action_title'], $Params['access']) =
	    array(0, $id, 'del', 'Delete', ACTION_GROUP);
	    $actionsColl->addItem($Params);
    }
    $this->registerActions($actionsColl, 0);

    return true;
  }

  public function update(modsetItem $item) {
    $Params = $item->toArray();
    if ($item->name == '' || $item->codename = '') return false;

    $sql ='UPDATE modules SET `name` = \'%2$s\', `codename` = \'%3$s\',  `processName` = \'%4$s\', `xsl` = \'%5$s\', defModName =  \'%6$s\', parentMod = \'%7$u\', layoutPref = \'%8$s\', `isSystem` = \'%9$u\' WHERE `id` = \'%1$u\'';
    $this->query($sql, $Params['id'], $Params['name'], $Params['codename'], $Params['processName'], $Params['xsl'], $Params['defModName'], $Params['parentMod'], $Params['layoutPref'], $Params['isSystem']);
    return true;
  }

  public function get($id) {
    $sql = 'SELECT * FROM modules WHERE id = \'%1$u\' ';
    $this->query($sql, $id);
    $row = $this->fetchOneRowA();
    $item = new modsetItem($row);
    return $item;
  }


  public function del($id) {

  }



public function getModulesFULL($mod_id = 0) {
	$sql = 'SELECT
                m.*,
                ma.`action_name`, ma.`action_title`, ma.`access`, ma.`id` as maid,
                mc.`group_id`, mc.`action_id`, mc.`access` as mcaccess,
                ug.group_id,
                ug.user_id,
                g.name as gname,
                u.name as uname,
                IF (mc.group_id IS NOT NULL, mc.group_id, 0) as group_adm,
                
                mas.query_string as mas_query_string , 
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
			
	            FROM modules m
	            INNER JOIN module_actions ma ON m.id = ma.mod_id
	            LEFT JOIN module_access mc ON ma.id = mc.action_id
	            INNER JOIN groups_user ug ON mc.group_id = ug.group_id
	            INNER JOIN groups g ON ug.group_id = g.id
	            INNER JOIN users u ON ug.user_id = u.id
	            
	            LEFT JOIN module_addons_assign mas ON mas.addon_id = m.addons
	 			LEFT JOIN module_addons mad ON mad.id = mas.addon_id
	 			LEFT JOIN modules m2 ON mas.mod_id = m2.id';
    if ($mod_id > 0) $sql.= ' WHERE m.id = %1$u ';
    $sql.= '        	ORDER BY m.id';
/*
 LEFT JOIN module_addons_assign mas ON mas.addon_id = m.addons
 LEFT JOIN module_addons mad ON mad.id = mas.addon_id
 LEFT JOIN modules m2 ON mas.mod_id = m2.id
*/
    $this->query($sql, $mod_id);
    //stop($this->sql, 0);
    $modules = new modsetColl();

    $lastID = 0;
    $lastActID = 0;
    $lastAddonID = 0;
    $lastAddonModuleID = 0;
    while ($row = $this->fetchRowA()) {
    	$Params = array();
        $actionColl = new actionColl();
        $module = new modsetItem($Params);
    	if ($lastID != $row['id']) {
    		$Params = $row;
    		$Params['actions'] = $actionColl;
    		$addonsItem = new addonsSet($Params);
    		$Params['addonsItem'] = $addonsItem;

    		$modules->add($module);

    	}
    	if ($lastActID != $row['maid'] && $row['maid'] > 0) {
    		$groupColl = new mcColl();

    		$Params = array();
    		$Params['id'] = $row['maid'];
    		$Params['mod_id'] = $row['id'];
    		$Params['mod_name'] = $row['name'];
    		$Params['action_name'] = $row['action_name'];
    		$Params['action_title'] = $row['action_title'];
    		$Params['access'] = $row['access'];
    		$Params['group_adm'] = $row['group_adm'];
    		$Params['groups'] = $groupColl;
    		$action = new moduleAction($Params);
    		$actionColl->add($action);
    //	}
    //	if ($lastGroupID != $row['group_id'] && $row['group_id'] > 0) {
    		$gr = array();
    		$gr['id'] = $row['group_id'];
    		$gr['action_id'] = $row['id'];
    		$gr['group_id'] = $row['group_id'];
    		$gr['group_name'] = $row['gname'];
    		$gr['user_id'] = $row['user_id'];
    		$gr['access'] = $row['access'];
    		$gr['module_id'] = $row['id'];
    		$groupColl->addItem($gr);
    	}
//stop($gr['group_name'],0);
        $modulesAddons = new modsetColl();
    	if($lastAddonID != $row['addon_id'] && $row['addon_id'] > 0) {
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
    		/*
    		 $addonsItem = new addonsSet($Params);
    		 $module->addonsItem = $addonsItem;
    		 */
    		$module->addonsItem->modules = $modulesAddons;
    			
//    		$a = true;
    		//print_r ($module->addonsItem->modules); //exit;
    	}

    	if ($lastAddonModuleID != $row['addon_module_id'] && $row['addon_module_id']  > 0) {
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
    		if($row['mas_query_string'] != '') $dqs = $row['mas_query_string']; else $dqs = $row['addon_module_defQueryString'];
    		$Params['defQueryString'] = $dqs;
    		$Params['isSystem'] = $row['addon_module_isSystem'];
    		$modulesAddons->addItem($Params);
    	}
	      $lastID = $row['id'];
	      $lastActID = $row['maid'];
//	      $lastGroupID = $row['group_id'];
	      $lastAddonID = $row['addon_id'];
	      $lastAddonModuleID = $row['addon_module_id'];
	      //echo $lastAddonID.' '.$lastAddonID.'<br />'.rn;
    }
    //print_r ($modules); exit;
    return $modules;
  }
  
  public function getModules() {
    $sql = 'SELECT
                m.*,
                ma.`action_name`, ma.`action_title`, ma.`access`, ma.`id` as maid,
                mc.`group_id`, mc.`action_id`, mc.`access` as mcaccess,
                ug.group_id,
                ug.user_id,
                g.name as gname,
                u.name as uname,
                IF (mc.group_id IS NOT NULL, mc.group_id, 0) as group_adm
            FROM modules m
            INNER JOIN module_actions ma ON m.id = ma.mod_id
            LEFT JOIN module_access mc ON ma.id = mc.action_id
            INNER JOIN groups_user ug ON mc.group_id = ug.group_id
            INNER JOIN groups g ON ug.group_id = g.id
            INNER JOIN users u ON ug.user_id = u.id
            ORDER BY m.id';
/*
 LEFT JOIN module_addons_assign mas ON mas.addon_id = m.addons
 LEFT JOIN module_addons mad ON mad.id = mas.addon_id
 LEFT JOIN modules m2 ON mas.mod_id = m2.id
 */
    $this->query($sql);
    #stop($this->sql);
    $modules = new modsetColl();

    $lastID = 0;
    $lastActID = 0;
    while ($row = $this->fetchRowA()) {
        $actionColl = new actionColl();
      if ($lastID != $row['id']) {
        $Params = $row;
        $Params['actions'] = $actionColl;

        $module = new modsetItem($Params);
        $modules->add($module);
      }
        $groupColl = new mcColl();
      if ($lastActID != $row['maid']) {
        $Params = array();
        $Params['id'] = $row['maid'];
        $Params['mod_id'] = $row['id'];
        $Params['mod_name'] = $row['name'];
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
      $gr['module_id'] = $row['id'];
      $groupColl->addItem($gr);
      $lastID = $row['id'];
      $lastActID = $row['maid'];
    }
    return $modules;
  }

  public function modMenu() {
    $sql = 'SELECT * FROM modules ORDER BY `parentMod`';
    $this->query($sql);
    $modColl = new modsetColl();
    while($row = $this->fetchRowA()) {
      $item = new modsetItem($row);
      $modColl->add($item);
    }
    return $modColl;
  }
  /**
   * Изменяет группу у действия, при отсутствии действия в группе вставляет новое
   * @param $action_id
   * @param $group_id
   * @param $access
   * @return boolean
   */
  public function chActionGroup($action_id, $group_id, $access) {
  	if ($action_id == 0 || $access == 0 || $group_id == 0) return false;
  	$sql = 'UPDATE module_access SET access = \'%3$u\' WHERE action_id = \'%1$u\' AND group_id = \'%2$u\' ';
  	$this->query($sql, $action_id, $group_id, $access);
  	if ($this->affectedRows() == 0) {
  		$sql = 'INSERT INTO module_access VALUES (\'%1$u\', \'%2$u\', \'%3$u\')';
  		if ($this->query($sql, $action_id, $group_id, $access)) return true;
  	} else return true;
  	return false;
  }
  /**
   * Изменяет тип доступа
   * @param $action_id
   * @param $access
   * @return boolean
   */
  public function chActionAccess($action_id, $access) {
  	if ($action_id == 0 || $access == 0) return false; 
  	$sql = 'UPDATE module_actions SET access = \'%2$u\' WHERE id = \'%1$u\' ';
  	$this->query($sql, $action_id, $access);
  	if ($this->affectedRows() == 1) return true;
  	return false;
  }  
}

class modsetProcess extends module_process {

  public function __construct ($modName) {
    parent::__construct($modName);

    $this->nModel = new modsetModel($modName);
    $this->nView = new modsetView($this->modName,$this->sysMod);

    /* Default Process Class actions */
    $this->regAction('new', 'New', ACTION_GROUP);
    $this->regAction('add', 'Add', ACTION_GROUP);
    $this->regAction('edit', 'Edit', ACTION_GROUP);
    $this->regAction('update', 'Update', ACTION_GROUP);
    $this->regAction('view', 'View', ACTION_GROUP);
    $this->regAction('del', 'Del', ACTION_GROUP);
    $this->regAction('newChild', 'Новый потомок', ACTION_GROUP);
    $this->regAction('addChild', 'Вставить нового потомка', ACTION_GROUP);
    $this->regAction('usemodule', 'Загрузить и выполнить модуль', ACTION_GROUP);
    $this->regAction('action', 'Операции с Действиями (del, add, toGroup, delFromGroup)', ACTION_GROUP);    
    $this->registerActions(0);
  }

  public function update($_action = false) {
    $this->updated = false;
    $this->Log->addToLog('Модули', __LINE__, __METHOD__);
    
  if ($_action) $this->action = $_action;
//	$action = $this->actionDefault;
	if ($this->action) $action = $this->action;
	else $action = $this->checkAction();
	if (!$action) {
		$this->Vals->URLparams($this->sysMod->defQueryString);
		$action = $this->actionDefault;
	}

	$user_id = $this->User->getUserID();
	$user_right = $this->User->getRight($this->modName, $action);
	 
	if ($user_right == 0) {
		$p = array('У Вас нет прав для использования модуля!');
		//$this->nView->viewError($p, false);
		$this->nView->viewLogin($p[0],$user_id);
		$this->Log->addError($p, __LINE__, __METHOD__);
		$this->updated = true;
		return;
	}
	
    $this->updated = true;    
    
	/**
	 * ******************************************************************************************************
	 */
    if ($action == 'action') {
    	$a = $this->Vals->getVal('action','GET','string');
    	switch ($a) {
    		case 'new': 
    			$module_id = $this->Vals->getVal('module_id', 'GET','integer');
    			if($module_id > 0) {
	    			$moduleItem = $this->nModel->get($module_id);
	    			$groups = $this->nModel->getGroups();
	    			$this->nView->viewNewAction($moduleItem, $groups);
	    			$this->updated = true;
    			} else {
    				$this->nView->viewError('Не задан модуль');
    			}
    			break;
    		case 'add': 
    			//$Params['id'] = $this->Vals->getVal('action_id');
    			$Params['action_name'] = $this->Vals->getVal('action_name','POST', 'string');
    			$Params['action_title'] = $this->Vals->getVal('action_title','POST', 'string');
    			$Params['mod_id'] = $this->Vals->getVal('module_id','POST', 'integer');
    			$Params['access'] = $this->Vals->getVal('access','POST', 'integer');
    			$Params['group'] = $this->Vals->getVal('group','POST', 'integer');
    			$actionItem = new moduleAction($Params);
    			$res = $this->nModel->addAction($actionItem, $Params['group'], 1);
    			if (!$res) $this->nView->viewError('Действие не добавлено',0);
    			$action = 'view';
    			break;
    		case 'del': 
    			$action_id = $this->Vals->getVal('action_id','GET','integer');
    			$actionItem = $this->nModel->getActionByID($action_id);
    			$this->nModel->delAction($actionItem);
    			$action = 'view';
    			break;
    		case 'togroup': break;
    		case 'delfromgroup': break;
    		case 'chaccess': 
    			$action_id = $this->Vals->getVal('action_id','GET','integer');
    			$access = $this->Vals->getVal('access','GET','integer');
    			if(!$this->nModel->chActionAccess($action_id, $access)) {
    				$this->nView->viewError('Ошибка',0);
    			}
    			$action = 'view';
    			break;
    		default: break;
    	}
    }
  if ($action == 'add') {
      if ($user_right == true) {

        $Params['name'] = $this->vals->getVal('name', 'POST', 'string');
        $Params['codename'] = $this->vals->getVal('codename', 'POST', 'string');
        $Params['processName'] = $this->vals->getVal('processName', 'POST', 'string');
        $Params['xsl'] = $this->vals->getVal('xsl', 'POST', 'string');
		
        $Params['defModName'] = $this->vals->getVal('defModName', 'POST', 'string');
        $Params['parentMod'] = $this->vals->getVal('parentMod', 'POST', 'integer');
        $Params['layoutPref'] = $this->vals->getVal('layoutPref', 'POST', 'string');
        $Params['defAction'] = $this->vals->getVal('defAction', 'POST', 'string');
        $Params['defQueryString'] = $this->vals->getVal('defQueryString', 'POST', 'string');
        $Params['parentMod'] = 0;
                
        $add = true;
        $res = false;
//        foreach ($Params as $val) if (!$val) $add = false;
        $Params['id'] = $this->vals->getVal('id', 'POST', 'integer');
        $item = new modsetItem($Params);
        if ($add) $res = $this->nModel->add($item);
        if ($res !== false) $this->nView->viewMessage('Создан новый модуль', 'Сообщение');
        if ($res === false && !$add) $this->nView->viewError('Не возможно добавить модуль');
        
        $this->updated = true;
      }
      else {
        echo $p = 'У Вас нет прав для создания модуля';
          $this->nView->viewError($p);
          $this->Log->addError(array('msg'=>$p, 'user_id'=>$user_id), __LINE__, __METHOD__);
      }
      $this->updated = true;
    }
    
  if ($action == 'addChild') {
      if ($user_right == true) {

        $Params['name'] = $this->vals->getVal('name', 'POST', 'string');
        $Params['codename'] = $this->vals->getVal('codename', 'POST', 'string');
        $Params['processName'] = $this->vals->getVal('processName', 'POST', 'string');
        $Params['xsl'] = $this->vals->getVal('xsl', 'POST', 'string');
		
        $Params['defModName'] = $this->vals->getVal('defModName', 'POST', 'string');
        $Params['parentMod'] = $this->vals->getVal('parentMod', 'POST', 'integer');
        $Params['layoutPref'] = $this->vals->getVal('layoutPref', 'POST', 'string');
        $Params['defAction'] = $this->vals->getVal('defAction', 'POST', 'string');
        $Params['defQueryString'] = $this->vals->getVal('defQueryString', 'POST', 'string');
        $Params['parentMod'] = $this->vals->getVal('id', 'POST', 'integer');
                
        $add = true;
        $res = false;
//        foreach ($Params as $val) if (!$val) $add = false;
        $Params['id'] = $this->vals->getVal('id', 'POST', 'integer');
        $item = new modsetItem($Params);
        if ($add) $res = $this->nModel->add($item);
        if ($res !== false) $this->nView->viewMessage('Создан новый модуль', 'Сообщение');
        if ($res === false && !$add) $this->nView->viewError('Не возможно добавить модуль');
        
        $this->updated = true;
      }
      else {
        echo $p = 'У Вас нет прав для создания модуля';
          $this->nView->viewError($p);
          $this->Log->addError(array('msg'=>$p, 'user_id'=>$user_id), __LINE__, __METHOD__);
      }
      $this->updated = true;
    }	
   if ($action == 'update') {

      if ($user_right > 0) {
        $Params['id'] = $this->vals->getVal('id', 'POST', 'integer');
        $Params['name'] = $this->vals->getVal('name', 'POST', 'string');
        $Params['codename'] = $this->vals->getVal('codename', 'POST', 'string');
        $Params['processName'] = $this->vals->getVal('processName', 'POST', 'string');
        $Params['xsl'] = $this->vals->getVal('xsl', 'POST', 'string');

        $Params['defModName'] = $this->vals->getVal('defModName', 'POST', 'string');
        $Params['parentMod'] = $this->vals->getVal('parentMod', 'POST', 'integer');
        $Params['layoutPref'] = $this->vals->getVal('layoutPref', 'POST', 'string');
        $Params['defAction'] = $this->vals->getVal('defAction', 'POST', 'string');
        $Params['defQueryString'] = $this->vals->getVal('defQueryString', 'POST', 'string');       
        $Params['parentMod'] = 0;
        
        $item = new modsetItem($Params);
        $this->nModel->update($item);
        $this->nView->viewMessage('Модуль изменен', 'Сообщение');
      }
      else {
        $p = 'У Вас нет прав для изменения модуля';
          $this->nView->viewError($p);
          $this->Log->addError(array('msg'=>$p, 'user_id'=>$user_id), __LINE__, __METHOD__);
      }
      $this->updated = true;
    }

    if ($action == 'del') {
      /* удалить */
      $this->updated = true;
    }
    if ($action == 'line') {
      /* показать список */
      if ($user_right > 0) {
//        $coll = $this->nModel->getModules();
//        $this->nView->viewList($coll);
      }
      $this->updated = true;
    }
	/**
	 * ******************************************************************************************************
	 */
	
//    $modColl = $this->nModel->modMenu();
//    $this->nView->modMenu($modColl);
	$this->Log->addCheckPoint(array('Действие'=>$action),__LINE__, __METHOD__);
	/**
	 * ******************************************************************************************************
	 */	
	if ($action == 'new') {
      if ($user_right > 0) {
        $this->nView->viewNew($user_id);
      }
      else {
        $p = array('У Вас нет прав для создания модуля','user_id' => $user_id);
          $this->nView->viewError($p);
          $this->Log->addError(array('msg'=>$p, 'user_id'=>$user_id), __LINE__, __METHOD__);
      }
      $this->updated = true;
    }

    if ($action == 'newChild') {
      if ($user_right > 0) {
      	$mod_id = $this->vals->getVal('newChild', 'GET', 'integer');
      	$module = $this->nModel->get($mod_id);
        $this->nView->viewNewChild($module, $user_id);
      }
      else {
        $p = array('У Вас нет прав для создания модуля','user_id' => $user_id);
          $this->nView->viewError($p);
          $this->Log->addError(array('msg'=>$p, 'user_id'=>$user_id), __LINE__, __METHOD__);
      }
      $this->updated = true;
    }
    
    
    
    if ($action == 'edit') {
      $modID = $this->vals->getVal('edit', 'GET', 'integer');
      if ($modID > 0 && $user_right > 0) {
        $item = $this->nModel->get($modID);
        $this->nView->viewEdit($item, $user_id);
      } else {
        $p = 'У Вас нет прав для редактирования модуля';
        $this->nView->viewError($p);
        $this->Log->addError(array('msg'=>$p, 'user_id'=>$user_id), __LINE__, __METHOD__);
      }
       $this->updated = true;
    }

   

    if ($action == 'view' && $user_right > 0) {
      $modules = $this->nModel->getModulesFull();
      $this->nView->viewModulesFull($modules);
      $this->updated = true;
    }
/*
    if ($action == 'view') {
      if ($user_right > 0) {
        $modID = $this->vals->getVal('id', 'POST', 'integer');
        $item = $this->nModel->get($modID, '', '');
        $this->nView->viewItem($item);
      }
      $this->updated = true;
    }
*/
    if ($action == 'usemodule' && $user_right > 0) {
      /*
       * Загружаю модуль из БД
       * Подключаю Модуль
       * Выполняю
       * 
       */
    }
    
    if (!$this->updated) {
      $modules = $this->nModel->getModules();
      $this->nView->viewModules($modules);
      $this->updated = true;
    }
  }
  
  public function getModulesFull($mod_id = 0) {
  	return $this->nModel->getModulesFULL($mod_id);
  }
  
  public function viewModulesFull($mod_id = 0) {
  	$this->nView->viewModulesFull($this->nModel->getModulesFULL($mod_id));
  }
}


class modsetView extends module_view {
	/**
	 * 
	 * @param $modName
	 * @param $sysMod
	 */
  public function __construct ($modName,$sysMod) {
    parent::__construct($modName,$sysMod);
    #$this->pXSL = '';
  }
    public function viewNew($user_id) {
      $this->pXSL[] = RIVC_ROOT.'layout/form.xsl';
       #  phpinfo(); exit;

      $Container = $this->newContainer('form');
      #$xModules = $this->addToNode($Container, 'modules', '');
		
      $form = new CFormGenerator($this->modName, SITE_ROOT.$this->modName.'/add-1/', 'POST', 0);
      $form->addHidden('add', '1', 'add');
      $form->addHidden('user_id', $user_id, 'user_id');
      
      $form->addHidden('parentMod', '','XSL построения страницы');
      
      $form->addText('name', '','Название модуля', 'name', '', 30);
      $form->addText('codename', '','Уникальное имя модуля', 'codename', '', 30);
      $form->addText('processName', '','Class Proccess', 'processName', '', 30);
      $form->addText('xsl', '','XSL построения страницы', 'xsl', '', 30);

      $form->addText('defModName', '','Префикс класса файла', 'defModName', '', 30);
      
      $form->addText('layoutPref', '','Префикс к layout', 'layoutPref', '', 30);
      $form->addText('defAction', '','Действие по умолчанию', 'defAction', '', 30);
      $form->addText('defQueryString', '','Параметры по умолчанию', 'defQueryString', '', 30);
      $form->addBox('isSystem', 0,  'Системный модуль', 'isSystem', '', 30, '','');

      $form->addSubmit('submit', 'создать', '', '');
      $form->getBody($Container,'xml');
      #$this->xml->save('form.xml');
      return $form;
    }

    public function viewEdit(modsetItem $item, $user_id) {
      $this->pXSL[] = RIVC_ROOT.'layout/form.xsl';
$Container = $this->newContainer('form');
      $form = new CFormGenerator($this->modName, SITE_ROOT.$this->modName.'/update-'.$item->id.'/', 'POST', 0);
      $form->addHidden('update', $item->id, 'update');
      $form->addHidden('user_id', $user_id, 'user_id');
      $form->addHidden('parentMod', '','XSL построения страницы');
      $form->addText('name', $item->name,'Название модуля', 'name', '', 30);
      $form->addText('codename', $item->codename,'Уникальное имя модуля', 'codename', '', 30);
      $form->addText('processName', $item->processName,'Class Proccess', 'processName', '', 30);
      $form->addText('xsl', $item->xsl,'XSL построения страницы (должен лежать в ./layuot)', 'xsl', '', 30);

      $form->addText('defModName', $item->defModName,'Префикс класса файла', 'xsl', '', 30);
      
      $form->addText('layoutPref', $item->layoutPref,'Префикс к layout', 'xsl', '', 30);
      $form->addText('defAction', $item->defAction,'Действие по умолчанию', 'xsl', '', 30);
      $form->addText('defQueryString', $item->defQueryString,'Параметры по умолчанию', 'xsl', '', 30);
$form->addBox('isSystem', $item->isSystem,  'Системный модуль', 'isSystem', '', 30, '','');
      $form->addSubmit('submit', 'создать', '', '');
      $form->getBody($Container,'xml');

      return $form;
    }
    
    public function viewNewChild(modsetItem $item, $user_id) {
      $this->pXSL[] = RIVC_ROOT.'layout/form.xsl';
	  $Container = $this->newContainer('form');
      $form = new CFormGenerator($this->modName, SITE_ROOT.$this->modName.'/addChild-'.$item->id.'/', 'POST', 0);
      $form->addHidden('addChild', $item->id, 'add');
      $form->addHidden('id', $item->id, 'add');
      $form->addHidden('user_id', $user_id, 'user_id');
      $form->addHidden('parentMod', '','XSL построения страницы');
      $form->addMessage('parentname', 'Родительский модуль: '.$item->name, 'name');
      $form->addText('name', '','Название модуля', 'name', '', 30);
      $form->addText('codename', 'new_'.$item->codename,'Уникальное имя модуля', 'codename', '', 30);
      $form->addText('processName', $item->processName,'Class Proccess', 'processName', '', 30);
      $form->addText('xsl', $item->xsl,'XSL построения страницы ', 'xsl', '', 30);

      $form->addText('defModName', $item->defModName,'Префикс класса файла', 'xsl', '', 30);
      
      $form->addText('layoutPref', $item->layoutPref,'Префикс к layout', 'xsl', '', 30);
      $form->addText('defAction', $item->defAction,'Действие по умолчанию', 'xsl', '', 30);
      $form->addText('defQueryString', $item->defQueryString,'Параметры по умолчанию', 'xsl', '', 30);
      $form->addBox('isSystem', 0,  'Системный модуль', 'isSystem', '', 30, '','');

      $form->addSubmit('submit', 'создать', '', '');
      $form->getBody($Container,'xml');

      return $form;
    }
    
    public function viewItem (modsetItem $item) {
      $this->pXSL[] = RIVC_ROOT.'layout/'.$this->modName.'.view.xsl';
      $Container = $this->newContainer($this->modName);
      $this->arrToXML($item->toArray(),$Container, 'item');
    }

    public function viewActions(actionColl $collect) {
      $this->pXSL[] = RIVC_ROOT.'layout/'.$this->modName.'.viewActions.xsl';
      $Container = $this->newContainer($this->modName);
      $actions = $this->addToNode($Container, 'actions', '');
      #stop($collect->count());
      $iterator = $collect->getIterator();
      foreach ($iterator as $item) {
        $gr = '';
        $groups = $item->groups->getIterator();
        if (count($groups) > 0) {
          foreach ($groups as $group)
            $gr.= $group->group_id.' ';
        } else $gr = 'нет групп';
        $array = array_merge($item->toArray(), array('groups'=>$gr));
        $this->arrToXML($array, $actions, 'action');
      }

      return true;
    }

    public function viewModules( modsetColl $collect) {
      $this->pXSL[] = RIVC_ROOT.'layout/'.$this->modName.'.viewModules.xsl';
      $Container = $this->newContainer($this->modName);

      $xModules = $this->addToNode($Container, 'modulesList', '');
      $modules = $collect->getIterator();
      foreach ($modules as $module) {
        $xModule = $this->addToNode($xModules, 'module', '');
        $this->addAttr('id', $module->id, $xModule);
        $this->addAttr('name', $module->name, $xModule);
        $this->addAttr('codename', $module->codename, $xModule);
        $this->addAttr('processName', $module->processName, $xModule);
        $this->addAttr('xsl', $module->xsl, $xModule);
        $xActions = $this->addToNode($xModule, 'actions', '');
        $actions = $module->actions->getIterator();
        foreach ($actions as $action) {
          $xAction = $this->addToNode($xActions, 'action', '');
          $this->addAttr('id', $action->id, $xAction);
          $this->addAttr('mod_id', $action->mod_id, $xAction);
          $this->addAttr('action_name', $action->action_name, $xAction);
          $this->addAttr('action_title', $action->action_title, $xAction);
          $this->addAttr('access', $action->access, $xAction);

          $groups = $action->groups->getIterator();
          $aGroups = '';
          $nGroups = '';
          foreach($groups as $group) {
            $aGroups.= ' '.$group->id;
            $nGroups.= ' '.$group->group_name;
          }           #stop($group,0);
          $this->addAttr('groups', $aGroups, $xAction);
          $this->addAttr('groups_names', $nGroups, $xAction);
        }
      }
      return true;
    }

    public function viewModulesFull( modsetColl $collect) {
      $this->pXSL[] = RIVC_ROOT.'layout/'.$this->modName.'.viewModules.xsl';
      $Container = $this->newContainer($this->modName);

      $xModules = $this->addToNode($Container, 'modulesList', '');
      $modules = $collect->getIterator();
      foreach ($modules as $module) {
        $xModule = $this->addToNode($xModules, 'module', '');
        $this->addAttr('id', $module->id, $xModule);
        $this->addAttr('name', $module->name, $xModule);
        $this->addAttr('codename', $module->codename, $xModule);
        $this->addAttr('processName', $module->processName, $xModule);
        $this->addAttr('xsl', $module->xsl, $xModule);
        $xActions = $this->addToNode($xModule, 'actions', '');
        $actions = $module->actions->getIterator();
        foreach ($actions as $action) {
          $xAction = $this->addToNode($xActions, 'action', '');
          $this->addAttr('id', $action->id, $xAction);
          $this->addAttr('mod_id', $action->mod_id, $xAction);
          $this->addAttr('action_name', $action->action_name, $xAction);
          $this->addAttr('action_title', $action->action_title, $xAction);
          $this->addAttr('access', $action->access, $xAction);

          $groups = $action->groups->getIterator();
          $aGroups = '';
          $nGroups = '';
          foreach($groups as $group) {
            $aGroups.= ' '.$group->id;
            $nGroups.= ' '.$group->group_name;
           // stop($group->group_name,0);
          }           
          $this->addAttr('groups', $aGroups, $xAction);
          $this->addAttr('groups_names', $nGroups, $xAction);
        }
        
        $xAddons = $this->addToNode($xModule, 'addons', '');
        //echo (gettype($module->addonsItem->modules));
        //print_r($module->addonsItem->modules);
        $addons = $module->addonsItem->modules->getIterator();
        
        /* $addon is modsetItem */
        foreach ($addons as $addon) {
        	//echo (get_class($addon));
          $xAddon = $this->addToNode($xAddons, 'addon', '');
          $this->addToNode($xAddon, 'id', $addon->id);
          $this->addToNode($xAddon, 'codename', $addon->codename);
          $this->addToNode($xAddon, 'name', $addon->name);
/*
          $subMods = $addon->modules->getIterator();
          $aM = '';
          $nGroups = '';
          foreach($subMods as $subMod) {
            $aM.= ' '.$group->id;
                      }
          $this->addToNode($xAddon,'sub_mods', $aM);     
          */     
        }        
      }
      return true;
    }
    

    
    public function viewNewAction(modsetItem $moduleItem, $groups) {
    	$this->pXSL[] = RIVC_ROOT.'layout/form.xsl';
	  $Container = $this->newContainer('form');
      $form = new CFormGenerator($this->modName, SITE_ROOT.$this->modName.'/action-add/', 'POST', 0);
      $form->addHidden('action_id', 'add', 'add');
      $form->addHidden('module_id', $moduleItem->id, 'add');
            
      $form->addMessage('module', 'Модуль: '.$moduleItem->name, 'name');
      $form->addText('action_name', '','Уникальное имя', 'name', '', 30);
      $form->addText('action_title', '','Название', 'action_title', '', 30);
      $form->addText('access', '','Доступ', 'access', '', 30);
      //$form->addText('group', '','Группа', 'access', '', 30);
      $form->addSelect('group', 1, 'Группа', '', '', 'group', '', 1);
      foreach ($groups as $option) {
      	$form->addOption ('op'.$option['id'], $option['id'], $option['name'], 'group', '', '', '');
      }

      $form->addSubmit('submit', 'добавить', '', '');
      $form->getBody($Container,'xml');

      return $form;   	
    }
}