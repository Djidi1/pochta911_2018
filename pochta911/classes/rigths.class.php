<?php

class groupItem extends module_item  {
	public $id;
	public $name;
	public $admin;
	public $parent;

	public $parent_link;
	# constructor

	public function __construct($Params, $prefix = '') {

		parent::__construct();

		if ($prefix != '') $prefix.='_';

		if (isset($Params[$prefix.'id'])) $this->id = $Params[$prefix.'id']; else $this->id = 0;
		if (isset($Params[$prefix.'name'])) $this->name = $Params[$prefix.'name']; else $this->name = "";
		if (isset($Params[$prefix.'admin'])) $this->admin = $Params[$prefix.'admin']; else $this->admin = 0;
		if (isset($Params[$prefix.'parent'])) $this->parent = $Params[$prefix.'parent']; else $this->parent = 0;
		if (isset($Params[$prefix.'parent_link'])) $this->parent_link = $Params[$prefix.'parent_link']; else $this->parent_link = null;
		$this->notInsert['id'] = 1;
		$this->notInsert['parent_link'] = 1;
	}

	# toArray

	public function toArray() {
		$Params['id'] = $this->id;
		$Params['name'] = $this->name;
		$Params['admin'] = $this->admin;
		$Params['parent'] = $this->parent;
		$Params['parent_link'] = $this->parent_link;
		return $Params;
	}
	# UPDATE
	public function UPDATE() {
		$result = '
   `name` = \'%2$s\' `admin` = \'%3$s\' `parent` = \'%4$s\' `parent_link` = \'%5$s\'';
		return $result;
	}
	/*SELECT_pref
	 */ # SELECT_pref
	public function SELECT($pref = '') {
		if ($pref != '') $pref.='_';
		$result = '
  `'.$pref.'.id` AS `'.$pref.'_id`, `'.$pref.'.name` AS `'.$pref.'_name`, `'.$pref.'.admin` AS `'.$pref.'_admin`, `'.$pref.'.parent` AS `'.$pref.'_parent`, `'.$pref.'.parent_link` AS `'.$pref.'_parent_link`';
		return $result;
	}
}
class groupCollection extends module_collection {

	public function __construct() {
		parent::__construct();
	}

	public function addItem($Params) {
		$item = new groupItem($Params);
		$this->add($item);
	}
}

# model
class groupsModel extends module_model {
	public function __construct ($modName) {
		parent::__construct($modName);
	}
	public function getList($limCount, $page, $filters, $orderField = 'id', $orderType = 'DESC') {
	}
	public function del(groupsItem $item) {
		if ($item->id <= 0) return false;
	}
	public function clear($type) {}
	
	public function addgroup(groupItem $item) {
		$res = $item->toInsert();
		$sql = 'INSERT INTO '.TAB_PREF.'group ('.$res[0].') VALUES('.$res[1].')';
		$q = array_merge(array(0=>$sql),$res[2]);
		$this->query($q);
		$id = $this->insertID();
		$item->id = $id;
		return true;
	}
	
	public function getgroup($id) {
		if ($id <= 0) return false;
		$sql = 'SELECT * FROM '.TAB_PREF.'group WHERE id = %1$u';
		$this->query($sql, $id);
		$row = $this->fetchOneRowA();
		$item = new groupItem($row);
		return $item;
	}
	
	public function updategroup(groupItem $item) {
		if ($item->id <= 0) return false;

		$sql = 'UPDATE '.TAB_PREF.'group SET '.$item->UPDATE();
		$q = array_merge(array(0=>$sql),$res[2]);
		if (!$this->query($q)) return false;
		return true;
	}
	
	public function delgroup(groupItem $item) {

		if ($item->id <= 0) return false;


		$sql = 'DELETE FROM '.TAB_PREF.'group WHERE `id` = %1$u';
		$this->query($sql,$id);
	}
	
	public function getGroups($parent = 0) {
		$sql = 'SELECT * FROM '.TAB_PREF.'group ORDER BY `parent` ASC, `name` ASC';
		$ZeroGroup = new groupItem(array('id'=>0,'name'=>'Общая Группа', `parent`=>0));
		$this->query($sql);
		while($row = $this->fetchOneRowA()) {
			$group = new groupItem($row);
		}
	}
}
?>