<?php

class TreeNode {
  private $id;
  private $owner;
  private $parent;
  private $child;
  private $childs; /* array of TreeNode */

  public function __construct ($owner, $parent, $child) {
    $this->id = $owner;
    $this->owner = $owner;
    $this->parent = $parent;
    $this->child = $child;
    $this->childs = array();
  }

}

class TreeNodes extends database {

    private $bodies;

    public function __construct () {;
         parent::__construct();
         $this->bodies = array(false);
    }
  /**
  * $tree - ID ветки предков которой требуется найти
  */
  private function getParents($tree, $current) {
    $sql = 'SELECT `owner`, `parent`, `child` FROM `'.TAB_PREF.'tree_nodes` WHERE `parent` = %1$u AND `owner` <> %2$u group by owner';
    $res = $this->query($sql, $tree, $current);
    $child = array();
    while ($row = mysql_fetch_array($res))
        $child[] = $row;
    return $child;
  }

  private function getChilds($tree, $current) {
    $sql = 'SELECT `owner`, `parent`, `child` FROM `'.TAB_PREF.'tree_nodes` WHERE `child` = %1$u AND `owner` <> %2$u group by owner';
    $res = $this->query($sql, $tree, $current);
    $child = array();
    while ($row = mysql_fetch_array($res))
        $child[] = $row;
    return $child;
  }

  /**
   * 
   * @param Владелец
   * @param Родитель
   * @param Тип отношения к связной таблицы (1-группы)
   * @param Сортировка
   * @return True|False
   */
  public function add ($tree_id, $parent, $type, $position =0 ) {

  	if ($type == 0) return false;
  	if ($position == '') 
  	/*
    $sql = 'INSERT INTO `trees` (`title`, `position`, `type` ) VALUES ( \'%1$s\', %2$u, %4$u)';
    $this->query($sql, $title.'('.$parent.')', $position, $type);
	*/
  	//stop($owner);
  	//exit($owner);
    //$tree_id = $owner;
    
    if($parent == 0) {
      $sql = 'INSERT INTo `'.TAB_PREF.'tree_nodes` (`owner`, `parent`, `child`, `type`) VALUES ('.$tree_id.',0,0, '.$type.')';

      $this->query($sql);
      return true;
    }

    if($parent != 0) {
        $sql = 'INSERT into `'.TAB_PREF.'tree_nodes` (`owner`, `parent`, `child`, `type`) VALUES ('.$parent.',0, '.$tree_id.', '.$type.')';
        $this->query($sql);

        $sql = 'INSERT Into `'.TAB_PREF.'tree_nodes` (`owner`, `parent`, `child`, `type`) VALUES ('.$tree_id.', '.$parent.', 0, '.$type.')';
        $this->query($sql);
    }

    $parents = $this->getChilds($parent,$tree_id);
    if (count($parents) > 0 && $parent != 0) {
            
      $a = false;
      foreach ($parents as $val) {
      	$a = false;
      	$sql = 'insert INTO `'.TAB_PREF.'tree_nodes` (`owner`, `parent`, `child`, `type`) VALUES';
        list($o,$p,$c) = $val;
        if($a) $sql.=',';
        $sql.= '('.$o.', '.$p.', '.$tree_id.', '.$type.')';
        $a=true;
        $this->query($sql);
      }
      
    }

    $parents = $this->getChilds($parent,$tree_id);
    if (count($parents) > 0 && $parent != 0) {      
      
      foreach ($parents as $val) {
      	$sql2 = 'INSERT INTO `'.TAB_PREF.'tree_nodes` (`owner`, `parent`, `child`, `type`) VALUES';
      $a = false;
        list($o,$p,$c) = $val;
        if($a) $sql2.=',';
        $sql2.= '('.$tree_id.', '.$o.', 0, '.$type.')';
        $a=true;
        $this->query($sql2);
      }
      
    }
    return false;
  }

  public function truncateTree () {    
    $sql = 'TRUNCATE TABLE `'.TAB_PREF.'tree_nodes`';
    $this->query($sql);
  }
  
  public function delNode ($nodeID, $type) {}
  
  public function delByType ($type) {
  	$sql ='DELETE FROM `'.TAB_PREF.'tree_nodes` WHERE `type` = %1$u';
  	$this->query($sql,$type);
  	return true;
  }
  
  public function moveTo ($fromID, $toID, $type) {}

  /*
  public function getTree ($tree_id = 0) {
    $sql = 'SELECT t.id, t.title, t.`position`, n.owner, n.`parent`, n.`child` FROM `trees` t
            LEFT JOIN `tree_nodes` n ON t.id = n.`owner`';
    if ($tree_id > 0) $sql.= 'WHERE t.id = %1$u';
    $this->query($sql, $tree_id);    
    $tree = array();
    while ($row = $this->fetchRowA()) {
        $tree[$row['id']][] = $row;
    }
    return $tree;
  }

  public function buildTree ($tree, $body, $stop) {
    if ($stop > 100) die ('Превышен лимит итераций');
    if (!is_array($tree)) die ('Ошибка данных');
    $tmp = '<ul>'.rn;
    foreach ($tree as $key => $val) {
      $tmp.= '<li>'.$val['title'];
      if (isset($tree[$tree['child']]))
      '</li>'.rn;
    }
    $tmp.= '</ul>'.rn;
    return $tmp;
  }

  public function viewTree ($tree) {
    $tmp = '<ul>'."\r\n";
    foreach ($tree as $node0) {

      foreach ( $node0 as  $node)
        $tmp.= "\t".'<li>#'.$node['id'].' - '.$node['title'].'</li>'."\r\n";
    }
    $tmp.='</ul>'."\r\n";
    return $tmp;
  }
  */
  
  /*
    SELECT * FROM `tree_nodes` tn
    inner join trees tr on tn.owner = tr.id
    where tn.parent = 1 or (tn.owner = 1 and tn.child = 0);
*/
  
  
}
?>