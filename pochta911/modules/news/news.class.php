<?php

define('NEWS_NOAUTHOR_ADD', 1);

/*
CREATE TABLE `new_rivc`.`news` (
  `id` INTEGER UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` VARCHAR(255) NOT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `content` TEXT NOT NULL,
  `time` DATETIME NOT NULL,
  `noshow` INTEGER(1) UNSIGNED NOT NULL,
  `prioritet` INTEGER(4) UNSIGNED NOT NULL,
  PRIMARY KEY (`id`)
)
ENGINE = MyISAM
CHARACTER SET utf8 COLLATE utf8_general_ci;
*/

class newsItem extends module_item
{
    public $id;
    public $title;
    public $subject;
    public $content;
    public $language;

    public $time;
    public $noshow;
    public $prioritet;
    public $user_id;
    public $files;

    public function __construct($Params)
    {
//    global $LOG;
        parent::__construct();
        if (isset($Params['id'])) $this->id = $Params['id']; else $this->id = 0;
        if (isset($Params['title'])) $this->title = $Params['title']; else $this->title = 0;
        if (isset($Params['subject'])) $this->subject = $Params['subject']; else $this->subject = 0;
        if (isset($Params['content'])) $this->content = $Params['content']; else $this->content = 0;
        if (isset($Params['language'])) $this->language = $Params['language']; else $this->language = 0;
        if (isset($Params['time'])) $this->time = $Params['time']; else $this->time = 0;
        if (isset($Params['noshow'])) $this->noshow = $Params['noshow']; else $this->noshow = 0;
        if (isset($Params['prioritet'])) $this->prioritet = $Params['prioritet']; else $this->prioritet = 0;
        if (isset($Params['user_id'])) $this->user_id = $Params['user_id']; else $this->user_id = 0;
        if (isset($Params['files'])) $this->files = $Params['files']; else $this->files = new fileColl();

        $this->notInsert['files'] = 1;
    }

    public function toArray()
    {
        $Params['id'] = $this->id;
        $Params['title'] = $this->title;
        $Params['subject'] = $this->subject;
        $Params['content'] = $this->content;
        $Params['language'] = $this->language;
        $Params['time'] = $this->time;
        $Params['noshow'] = $this->noshow;
        $Params['prioritet'] = $this->prioritet;
        $Params['user_id'] = $this->user_id;
        $Params['files'] = $this->files;

        return $Params;
    }
}

class newsColl extends module_collection
{
    public $newsCount;

    public function __construct($news_count = 0)
    {
        $this->newsCount = $news_count;
        parent::__construct();
    }

    public function addItem($params)
    {
        $item = new newsItem($params);
        $this->add($item);
    }
}

class newsModel extends module_model
{
    public function __construct($modName)
    {
        return parent::__construct($modName);
    }

    public function add(newsItem $item)
    {
        //stop($item);
        $res = $item->toInsert();

        $sql = 'INSERT INTO news (' . $res[0] . ') VALUES(' . $res[1] . ')';
        $q = array_merge(array(0 => $sql), $res[2]);
        $this->query($q);
        $id = $this->insertID();
        $item->id = $id;
        return true;
    }

    public function update(newsItem $item)
    {

        $sql = 'UPDATE news SET `title` =  \'%2$s\', `content` = \'%3$s\', language= \'%4$s\',`time` = \'%5$s\',`subject` = \'%6$s\' WHERE id = %1$u';

        $res = $this->query($sql, $item->id, $item->title, $item->content, $item->language, $item->time, $item->subject);

        return $res;
    }

    public function get($id, $noshow = 0, $user_id = 0)
    {
        // stop($id);
        if ($id <= 0) {
            $this->Log->addError(array('Ошибка номера новости'), __LINE__, __METHOD__);
            $news = new newsItem(array());
            return $news;
        }
        //$lang=$_SESSION["lang"];
        $lang = 'ru';
        $sql = 'SELECT n.*, DATE_FORMAT(`time`, \'%%d-%%m-%%Y\') as time,
      			f.`id` as file_id, 
			    	f.`filename`  as file_filename, 
			    	f.`codename` as file_codename, 
			    	f.`date` as file_date, 
			    	f.`folder` as file_folder, 
			    	f.`owner` as file_owner, 
			    	f.`description` as file_description, 
			    	f.`module` as file_module, 
			    	f.`access` as file_access, 
			    	f.`size` as file_size, 
			    	f.`mime` as file_mime, 
			    	f.`inputName` as file_inputName,
			    	fm.`id` as fmid, 
			    	fm.`essence_id` as file_essence_id, 
			    	fm.`module` file_module, 
			    	fm.`essence_module` as file_essence_module
      			FROM news n
      			LEFT JOIN `files_modules` fm ON fm.module = \'%4$s\' AND fm.essence_module = n.id              
              	LEFT JOIN `files` f ON fm.essence_id = f.id
      			WHERE n.id = %1$u and language=\'' . $lang . '\'';
        if ($noshow) $sql .= ' AND n.noshow = %2$u';
        if ($user_id) $sql .= ' AND n.user_id = %3$u';
        $this->query($sql, $id, $noshow, $user_id, $this->modSet->defModName);

        $a = true;
        $news = false;
        if ($this->numRows() == 0) {

            $this->Log->addError(array('Новость не найдена'), __LINE__, __METHOD__);
        } else {
            while ($row = $this->fetchRowA()) {
                if ($a) {
                    $news = new newsItem($row);
                    $a = false;
                }
            }
        }
        return $news;
    }

    public function del($id)
    {
        $sql = 'DELETE FROM news WHERE id=%1$u';
        $this->query($sql, $id);
        $pFile = new fileProcess($this->modSet->defModName, $id);
        $pFile->update('delfile');
    }

    /*
     *
     * @param $noshow
     * @param $limStart
     * @param $limCount
     * @return unknown_type
     */
    public function getList($noshow, $page, $limCount)
    {

        $this->Log->addToLog('Вход', __LINE__, __METHOD__);
        $lang = 'ru';
//        $row = $this->query("SELECT COUNT(*) FROM news WHERE  language='$lang'", 0);


        $limStart = 0;
        if ($page != 0) {
            $count = $this->getOne();

            if ($page > $count) $page = $count;
            if ($page < 1) $page = 1;
            //$col = 10;
            if ($limCount < 1) $limCount = 1;
            $limStart = ($page - 1) * $limCount;
            if ($limStart < 0) $limStart = 0;
        }
        //$script_name =  basename($_SERVER["SCRIPT_NAME"]);

        $sql = 'SELECT n.*, DATE_FORMAT(`time`, \'%%d-%%m-%%Y\') as time,
      			f.`id` as file_id, 
			    	f.`filename`  as file_filename, 
			    	f.`codename` as file_codename, 
			    	f.`date` as file_date, 
			    	f.`folder` as file_folder, 
			    	f.`owner` as file_owner, 
			    	f.`description` as file_description, 
			    	f.`module` as file_module, 
			    	f.`access` as file_access, 
			    	f.`size` as file_size, 
			    	f.`mime` as file_mime, 
			    	f.`inputName` as file_inputName,
			    	fm.`id` as fmid, 
			    	fm.`essence_id` as file_essence_id, 
			    	fm.`module` file_module, 
			    	fm.`essence_module` as file_essence_module,
			    	(SELECT COUNT(*) FROM news WHERE language=\'' . $lang . '\') as news_count
			     FROM news n
      			 LEFT JOIN `files_modules` fm ON fm.module = \'%2$s\' AND fm.essence_module = n.id              
              		LEFT JOIN `files` f ON fm.essence_id = f.id
      				WHERE language=\'' . $lang . '\'';
        if ($noshow) $sql .= ' AND n.noshow = %1$u ';
        $sql .= ' ORDER BY n.`time` DESC';
        if ($limCount > 0) $sql .= ' LIMIT ' . $limStart . ',' . $limCount;

        $this->query($sql, $noshow, $this->modSet->defModName);
        $collect = new newsColl();

        $lastProd = 0;
        $a = true;
        while ($row = $this->fetchRowA()) {

            if ($row['id'] != $lastProd) {
                $a = true;
            }
            $lastProd = $row['id'];
            if ($a) {
                $news = new newsItem($row);
                $collect->add($news);
                $a = false;
            }
            //   	$file = new fileItem($row, 'file_');
            //   	if (preg_match('/image/', $file->mime)) $file->isImage = true;
            //   	$news->files->add($file);
            $collect->newsCount = $row['news_count'];
        }
        /*
        while ($row = $this->fetchRowA()){
          $collect->addItem($row);
          $this->Log->addToLog($row,__LINE__,__METHOD__);
        }
  */

        return $collect;
    }
}

class newsProcess extends module_process
{
//  private $pXSL;
    # protected $xml;
    /**
     * показывает, произошло ли обновление с помощью функции UPDATE или нидина из опций не была выполнена
     */
    public $updated;

    public function __construct($modName)
    {
        parent::__construct($modName);

        $this->nModel = new newsModel($modName);
        $this->nView = new newsView($this->modName, $this->sysMod);

        /* Default Process Class actions */
        $this->regAction('new', 'New', ACTION_GROUP);
        $this->regAction('add', 'Add', ACTION_GROUP);
        $this->regAction('edit', 'Edit', ACTION_GROUP);
        $this->regAction('update', 'Update', ACTION_GROUP);
        $this->regAction('view', 'View', ACTION_GROUP);
        $this->regAction('newsline', 'View', ACTION_PUBLIC);
        $this->regAction('del', 'Del', ACTION_GROUP);
        $this->regAction('newsadmin', 'newsadmin', ACTION_GROUP);

        if (DEBUG == 0) {
            $this->registerActions(1);
        }
        if (DEBUG == 1) {
            $this->registerActions(0);
        }

    }

    public function update($_action = false)
    {
        $this->updated = false;

        $this->Log->addToLog('Новости', __LINE__, __METHOD__);
        if ($_action) $this->action = $_action;
        if ($this->action) $action = $this->action;
        else $action = $this->checkAction();
        if (!$action) {
            $this->Vals->URLparams($this->sysMod->defQueryString);
            $action = $this->actionDefault;
        }
//        $user_id = $this->User->getUserID();
        $user_right = $this->User->getRight($this->modName, $action);

        if ($user_right == 0) {
            $p = array('У Вас нет прав для использования модуля');
            $this->nView->viewMessage($p[0],'', false);
            $this->Log->addError($p, __LINE__, __METHOD__);
            $this->updated = true;
            return;
        }
        // stop($action);
        if ($action == 'new') {
            /* строим XML для создания новости */
            $user_id = $this->User->getUserID();
            if ($user_id > 0 || NEWS_NOAUTHOR_ADD) $this->nView->viewNew($user_id);
            else {
                $p = 'У Вас нет прав для добавления новости';
                $this->nView->viewError($p);
                $this->Log->addError(array('msg' => $p, 'user_id' => $user_id), __LINE__, __METHOD__);
            }
            $this->updated = true;
        }
        if ($action == 'add') {
            /* свтавляем новость в БД */
            $p = array();
            $p['title'] = $this->vals->getVal('title', 'POST');
            $p['content'] = $this->vals->getVal('content', 'POST');
            $p['language'] = $this->vals->getVal('language', 'POST');
            $time = $this->vals->getVal('time', 'POST', 'string');
            $time = dateToTimestamp($time);
            if (!$time) $time = date('Y-m-d H:i:s');
            else $time = date('Y-m-d H:i:s', $time);

            $p['time'] = $time;
            $p['id'] = 0;
// stop($p['time']);
            $user_id = $this->vals->getVal('content', 'POST');
            if ($user_id > 0 || NEWS_NOAUTHOR_ADD) {
                if ($this->isValid($p)) {
                    $item = new newsItem($p);

                    $this->nModel->add($item);
                    #stop($item->id);
                    $pFile = new fileProcess($this->modName, $item->id);

                    $pFile->update('add');

                } else $this->Log->addError(array('msg' => 'Данные не валидны', 'user_id' => $user_id), __LINE__, __METHOD__);
            } else {
                $p = 'У Вас нет прав для добавления новости';
                $this->nView->viewError($p);
                $this->Log->addError(array('msg' => $p, 'user_id' => $user_id), __LINE__, __METHOD__);
            }
            $this->updated = false;
            $action = 'newsadmin';
        }
        if ($action == 'edit') {
            /* выбираем новость, стоим шаблон редактирования */
            $news_id = $this->vals->getVal('edit', 'GET', 'integer');
            if ($news_id > 0) {
                $item = $this->nModel->get($news_id);
                if (!$item) {
                    $p = 'Новость не найдена';
                    $this->nView->viewError($p);
                } else {
                    $this->nView->viewEdit($item, $item->getID());
                }
            }
            $this->updated = true;
            //$action='newsadmin';
        }

        if ($action == 'update') {
            /* сохранить изменения */
            $p = array();
            $p['title'] = $this->vals->getVal('title', 'POST', 'string');
            $p['content'] = $this->vals->getVal('content', 'POST', 'string');
            $p['subject'] = $this->vals->getVal('subject', 'POST', 'string');
            $p['language'] = $this->vals->getVal('language', 'POST', 'string');

            $p['id'] = $this->vals->getVal('update', 'POST', 'integer');
            $time = dateToTimestamp($this->vals->getVal('time', 'POST', 'string'));
            if (!$time) $time = date('Y-m-d H:i:s');
            else $time = date('Y-m-d H:i:s', $time);
            $p['time'] = $time;
            $user_id = 1;
            if ($user_id > 0 && $p['id'] > 0) {
                if ($this->isValid($p)) {
                    $item = new newsItem($p);

                    $this->nModel->update($item);
                    #stop($item->getVal('id'));
                    $pFile = new fileProcess($this->modName, $item->id);
                    $pFile->update('update');
                    $this->nView->viewMessage('Изменения сохранены', 'Сообщение');

                } else $this->Log->addError(array('msg' => 'Данные не валидны', 'user_id' => $user_id), __LINE__, __METHOD__);
            } else {
                $p = 'У Вас нет прав для изменения новости';
                $this->nView->viewError($p);
                $this->Log->addError(array('msg' => $p, 'user_id' => $user_id, 'user_right' => $user_right), __LINE__, __METHOD__);
            }

            $this->updated = true;
            $action = 'newsadmin';
        }

        /* удалить новость */
        if ($action == 'del') {
            $news_id = $this->vals->getVal('news_id', 'get', 'integer');
            $this->nModel->del($news_id);
            if ($news_id == 0) {
                $p = 'Новость не найдена';
                $this->nView->viewError($p);
                //$this->Log->addError(array('msg'=>$p, 'news_id'=>$a), __LINE__, __METHOD__);
            }
            $this->updated = true;
            $action = 'newsadmin';
        }

        /* показать список новостей */
        if ($action == 'newsline') {
            $limCount = $this->vals->getVal('count', 'get', 'integer');
            if (!$limCount) $limCount = $this->vals->getModuleVal($this->modName, 'count', 'GET');
            $page = $this->vals->getVal('page', 'GET', 'integer');

            if ($page <= 0 || $page === NULL) {
                $this->Vals->setValTo('page', '1', 'GET');
                $page = 1;
            }
            //stop($p);
            if ($limCount == 0) $limCount = 12;
            ///stop($limCount,0);
            $collect = $this->nModel->getList(0, $page, $limCount);
            //stop($page);
            /*
                        $count = $collect->count();
                        if ($page > $count) $page = $count;
                        if ($page < 1) $page = 1;
                        $col = 10;
                        $limStart = ($page - 1) * $col;
                        if ($limStart < 0) $limStart = 0;
                        $limcount = $col;
                        $script_name =  basename($_SERVER["SCRIPT_NAME"]);
            */
            $Archive = new archiveStruct($this->modName, $collect->newsCount, $limCount, $page, '');
            $this->nView->viewList($collect, $Archive);

            $this->updated = true;
        }
        if ($action == 'newsadmin') {
            /* показать список новостей */
            $limCount = $this->vals->getModuleVal($this->modName, 'count', 'GET');
            $collect = $this->nModel->getList(0, 0, $limCount);
            $this->nView->viewListAdmin($collect);
            $this->updated = true;
        }
        if ($action == 'view') {
            /* показать новость */
            $a = $this->Vals->getVal('view', 'GET', 'integer');
            if ($a > 0) {
                $news_id = $a;
                $this->Log->addToLog(array('news_id' => $news_id), __LINE__, __METHOD__);
                $item = $this->nModel->get($news_id);
                if (!$item) {
                    $p = 'Новость не найдена';
                    $this->nView->viewError($p);
                } else {
                    $this->vals->setValTo('essence_module', $news_id, 'GET');
                    $this->nView->viewNews($item);
                }
            } else {
                $p = 'Новость не найдена';
                $this->nView->viewError($p);
                $this->Log->addError(array('msg' => $p, 'news_id' => $a), __LINE__, __METHOD__);
            }
            $this->updated = true;
        }

        if (!$this->updated) {
            //$limCount = $this->vals->getVal('count', 'get', 'int');
            $limCount = $this->vals->getVal('count', 'get', 'integer');
            if (!$limCount) $limCount = $this->vals->getModuleVal($this->modName, 'count', 'get');
            $page = $this->vals->getVal('page', 'GET', 'integer');
            if ($page <= 0 || $page == NULL) {
                $this->Vals->setValTo('page', '1', 'GET');
                $page = 1;
            }
            $collect = $this->nModel->getList(0, 0, $limCount);
            $Archive = new archiveStruct($this->modName, $collect->count(), $limCount, $page, '');
            $this->nView->viewList($collect, $Archive);
            $this->updated = true;
        }

    }
    /*
        public function getBody($data_type = 'xml') {
          if ($data_type == 'xml') return $this->nView->getBody();
          if ($data_type == 'html') {
            $this->Log->addToLog('pXSL '.$this->nView->pXSL, __LINE__, __METHOD__);
            $data = '';
            if (!$this->nView->pXSL) {
              $this->Log->addError('Не найден путь к XSL ('. $this->nView->pXSL.')', __LINE__, __METHOD__);
              return false;
            }
            $doc = new DOMDocument();
            $xsl = new XSLTProcessor();
            $doc->load($this->nView->pXSL);
            $xsl->importStyleSheet($doc);
            $res = $xsl->transformToDoc($this->nView->getBody());
            $data = $res->saveHTML();
            return $data;
          }
        }  */
}

class newsView extends module_view
{
    public function __construct($modName, $sysMod)
    {
        parent::__construct($modName, $sysMod);
        $this->pXSL = array();
    }

    /*    public function getBody() { }; */

    public function viewNew($user_id)
    {
        $this->pXSL[] = RIVC_ROOT . 'layout/form.xsl';
        $Container = $this->newContainer('form');
        $form = new CFormGenerator('news', SITE_ROOT . $this->modName . '/add-1/', 'POST', 0);
        $form->addHidden('add', '1', 'add');
        $form->addHidden('user_id', $user_id, 'user_id');
        $form->addButton('button', '     Вернуться', '', 'BackToAdmin');
        // $form->addSelect('language','ru','Язык','','','','','');
        // $form->addOption('en','en','English','language','','','');
        // $form->addOption('ru','ru','Русский','language','','','');
        $form->addHidden('language', 'ru', 'language');
        $form->addText('time', date('d.m.Y'), 'Дата (ДД.ММ.ГГГГ)', 'date', '', 20);
        $form->addText('title', '', 'Название новости', 'title', '', 30);

        $form->addTextArea('content', '', 'Текст на главной', '', 'ckeditor', 80);
        $form->addTextArea('subject', '', 'Подробное описание', '', 'ckeditor', 80);
        // $form->addTextArea('content',$item->content, 'Содержание', 'content_edit', '', 80);

        //$form->addSubmit('submit', 'sddas', '', '');
        //  $pFile = new fileProcess('files');
        //   $fileInputs = $pFile->update('new');
        //  $form->addInputs($fileInputs);

        $form->addSubmit('submit', 'создать', '', '');

        $form->getBody($Container, 'xml'); // $this->xml = $form->getBody('xml');
        #$this->xml->save('form.xml');
        return $form;
    }

    public function viewEdit(newsItem $item, $user_id)
    {
        $this->pXSL[] = RIVC_ROOT . 'layout/form.xsl';
        $Container = $this->newContainer('form');
        $form = new CFormGenerator('news', SITE_ROOT . $this->modName . '/update-' . $item->id . '/', 'POST', 0);
        $form->addHidden('update', $item->id, 'edit');
        $form->addHidden('user_id', $user_id, 'user_id');
        $form->addButton('button', '     Вернуться', '', 'BackToAdmin');
        //  $form->addSelect('language','ru','Язык','','','','','');
        //  $form->addOption('ru','ru','Русский','language','','','');
        $form->addHidden('language', 'ru', 'language');
        $date = $item->time;
        $form->addText('time', $date, 'Дата (ДД.ММ.ГГГГ)', 'date', '', 20);
        $form->addText('title', $item->title, 'Название новости', 'title', '', 30);

//      $form->addTextArea('content', $item->content, 'Содержание', 'content', 'content', 80);
        $form->addTextArea('content', stripcslashes($item->content), 'Текст на главной', '', 'ckeditor', 80);
        $form->addTextArea('subject', stripcslashes($item->subject), 'Подробное описание', '', 'ckeditor', 80);
        //if ($item->time == 0) $date = date('d.m.Y'); else $date = date('d.m.Y',$item->time);
        //    $pFile = new fileProcess($this->modName, $item->id);
        //    $fileInputs = $pFile->update('edit');
        //    $form->addInputs($fileInputs);

        $form->addSubmit('submit', 'сохранить', '', '');
        $form->getBody($Container, 'xml'); // $this->xml = $form->getBody('xml');

        $this->xml->save('form.xml');
        return $form;
    }

    public function viewList(newsColl $collect, archiveStruct $Archive)
    {
        $this->Log->addToLog('Вход', __LINE__, __METHOD__);
        $this->pXSL[] = RIVC_ROOT . 'layout/news/' . $this->sysMod->layoutPref . '.viewlist.xsl';
        $this->pXSL[] = RIVC_ROOT . 'layout/file.view.xsl';
        //$this->sysMod = new modsetItem($p);
        $Container = $this->newContainer($this->modName);
        $this->addAttr('module', $Archive->module, $Container);
        $this->addAttr('count', $Archive->count, $Container);
        $this->addAttr('size', $Archive->size, $Container);
        $this->addAttr('curPage', $Archive->curPage, $Container);
        $iterator = $collect->getIterator();
        //  $pFile = new fileProcess($this->sysMod->defModName);
        foreach ($iterator as $item) {
            //$this->Log->addToLog($item->toArray(), __LINE__, __METHOD__);
//            $iArray = $item->toArray();

            //unset($iArray['files']);
            // $itemConteiner = $this->arrToXML($iArray, $Container, 'item');
            $itemConteiner = $this->addToNode($Container, 'item', '');
            $data = $item->toArray();
            //чтобы отображать ссыдки в виде /news/2013/04/26/69
            $year = substr($data['time'], 6);
            $month = substr($data['time'], 3, 2);
            $day = substr($data['time'], 0, 2);
            $data['year'] = $year;
            $data['month'] = $month;
            $data['day'] = $day;
            //-------------------------------------------------------------
            foreach ($data as $key => $val) {
                if ($key == 'content' || $key == 'title' || $key == 'subject') {
                    $node = $this->addToNode($itemConteiner, $key, '');
                    $CDATA = $this->xml->createCDATASection(rn . stripcslashes($val) . rn);
                    $node->appendChild($CDATA);
                } else $this->addToNode($itemConteiner, $key, $val);
            }
            //    $pFile->dropBody();
            //    $pFile->assignTo = $item->id;
            //  	//$pFile->update('viewIMG');
            //  	$pFile->viewImg($item->files);

            // 	$fileBody = $pFile->getBody();
            // 	$this->mergeXML($this->xml, $itemConteiner, $fileBody->getXML());
            // 	$this->pXSL = array_merge($this->pXSL, $fileBody->getXSL());
        }
        return $Container;
    }

    public function viewNews(newsItem $item)
    {
        $this->pXSL[] = RIVC_ROOT . 'layout/file.view.xsl';
        $this->pXSL[] = RIVC_ROOT . 'layout/news/' . $this->sysMod->layoutPref . '.viewNews.xsl';
        $Container = $this->newContainer($this->sysMod->defModName);

        // $bb  = array('test', 'test2');
        //$any = getTemplate($item, $this->sysMod->defModName, 'test');
        //stop($any);
        //$this->addToNode($itemConteiner, $key, $any);

        //$this->arrToXML($item->toArray(), $Container, 'item');
        $itemConteiner = $this->addToNode($Container, 'item', '');
        $data = $item->toArray();
        foreach ($data as $key => $val) {
            if ($key == 'content' || $key == 'title' || $key == 'subject') {
                $node = $this->addToNode($itemConteiner, $key, '');
                $CDATA = $this->xml->createCDATASection(rn . stripcslashes($val) . rn);
                //$CDATA = $this->xml->createCDATASection(rn.$any.rn);
                // $CDATA = $any;
                $node->appendChild($CDATA);
            } else $this->addToNode($itemConteiner, $key, $val);
        }

        //   $pFile = new fileProcess($this->sysMod->layoutPref, $item->id);
        //   $pFile->update('viewIMG');
        //  $fileBody = $pFile->getBody();
        //  $this->mergeXML($this->xml, $itemConteiner, $fileBody->getXML());
        //  $this->pXSL = array_merge($this->pXSL, $fileBody->getXSL());
    }

    public function viewListAdmin(newsColl $collect)
    {
        $this->Log->addToLog('Вход', __LINE__, __METHOD__);
        $this->pXSL[] = RIVC_ROOT . 'layout/news/' . $this->sysMod->layoutPref . '.viewlistadmin.xsl';
        $this->pXSL[] = RIVC_ROOT . 'layout/file.view.xsl';
        //$this->sysMod = new modsetItem($p);
        $Container = $this->newContainer($this->modName);
        $iterator = $collect->getIterator();
        //  $pFile = new fileProcess($this->sysMod->defModName);
        foreach ($iterator as $item) {
            //$this->Log->addToLog($item->toArray(), __LINE__, __METHOD__);
//            $iArray = $item->toArray();
            //unset($iArray['files']);
            // $itemConteiner = $this->arrToXML($iArray, $Container, 'item');
            $itemConteiner = $this->addToNode($Container, 'item', '');
            $data = $item->toArray();
            foreach ($data as $key => $val) {
                if ($key == 'content' || $key == 'title') {
                    $node = $this->addToNode($itemConteiner, $key, '');
                    $CDATA = $this->xml->createCDATASection(rn . stripcslashes($val) . rn);
                    $node->appendChild($CDATA);
                } else $this->addToNode($itemConteiner, $key, $val);
            }
            //    $pFile->dropBody();
            //    $pFile->assignTo = $item->id;
            //$pFile->update('viewIMG');
            //  	$pFile->viewImg($item->files);

            //  	$fileBody = $pFile->getBody();
            // 	$this->mergeXML($this->xml, $itemConteiner, $fileBody->getXML());
            //  	$this->pXSL = array_merge($this->pXSL, $fileBody->getXSL());
        }
        return true;
    }
}