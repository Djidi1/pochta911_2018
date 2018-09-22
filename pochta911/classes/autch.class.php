<?php

session_start();

$loginErrors = array(
    0 => 'Введите имя пользователя и пароль',
    1 => ' %s',
    -1 => '',
    -2 => 'Имя пользователя или пароль не верны',
    -3 => 'Сессия устарела',
    -4 => ''

);


class TUser extends database
{

    private $user_name;
//  private $user_login;
    private $user_id;
    private $user_right;   /* array ! */
    /**
     * class TValues
     * @var TValues
     */
    private $Vals;   /*  */
    private $Log;   /*  */

    /* ID сесси в таблице */
    protected $session_id;
    protected $adm;

    protected $login;
    protected $name;
    protected $phone;

    protected $login_error;
    protected $authorization;

    protected $userData;
    public $nView;

    public function __construct()
    {
        global $values, $LOG;

        parent::__construct('auth');

        $this->Log = $LOG;
        $this->Vals = $values;
        $this->user_id = 0;
        $this->user_right = array();
        $this->userData = array('group_name' => '');
        $this->nView = new userView();

//    $mod = 'modset';
        parent::__construct();
    }

    public function getUserTabNo()
    {
        if (isset($this->userData['tab_no'])) return $this->userData['tab_no']; else return 0;
    }

    public function getUserID()
    {
        return $this->user_id;
    }

    public function getUserName()
    {
        if (isset($this->userData['name'])) return $this->userData['name']; else return false;
    }

    public function getUserGroupName()
    {
        if (isset($this->userData['gname'])) return $this->userData['gname']; else return false;
    }

    public function getUserGroup()
    {
        if (isset($this->userData['gid'])) return $this->userData['gid']; else return false;
    }

    public function lastError()
    {
        return $this->login_error;
    }

    /* На какие модули есть админские права - Права определяются по Разрешению(наличию) Дейсвия добавления */
    public function getRightAdmin()
    {
        $right = array();
        foreach ($this->user_right as $module => $action) {
            if (isset($this->user_right[$module]['add'])) {
                if ($this->user_right[$module]['add'] == true) {
                    $right[$module] = true;
                }
            }
        }
        return $right;
    }


    public function getRight($module, $action)
    {
        $right = false;
        if (isset($this->user_right[$module][$action])) $right = $this->user_right[$module][$action];
        $this->Log->addToLog('Запрос прав ' . $module . '.' . $action . ' ' . (($right) ? 'Разрешено' : 'Запрещено'), __LINE__, __METHOD__);
        return $right;
    }

    public function getRightModule($module)
    {
        $right = true;
        if (!isset($this->user_right[$module])) {
            $right = false;
            return $right;
        }
        foreach ($this->user_right[$module] as $a) if ($a == 0) {
            $right = false;
        }
        $this->Log->addToLog('Запрос прав ' . $module . ' ' . $right, __LINE__, __METHOD__);
        return $right;
    }


    /**
     * проверка логина и пароля (если имеются) и текущей сессии
     * @param $username
     * @param $password
     * @return bool
     */
    public function authentication($username, $password)
    {
        if ($username == '' || $password == '') return false;
        $sql = 'SELECT * FROM users WHERE isban = 0 AND pass = \'%2$s\' AND login = \'%1$s\'';
        $this->query($sql, $username, md5($password));

        $row = $this->fetchOneRowA();
        if ($this->numRows() == 1) {

            $this->user_id = $row['id'];
            $this->user_name = $row['name'];

            $sql2 = 'INSERT INTO logins (id_user,ip,referer,browser,screen_size,os)
     VALUES
           (\'' . $row ["id"] . '\', \'' . $_SERVER ["REMOTE_ADDR"] . '\', 
            \'' . $_SERVER ["HTTP_REFERER"] . '\', \'' . $_SERVER ["HTTP_USER_AGENT"] . '\',
            \'\', \'' . PHP_OS . '\')';
            $this->query($sql2);

            return true;
        }

        return false;
    }

    /*
    Получени параметров меню, есть ли установка NEW
    */
    public function get_menu_new($id)
    {
        $sql = "SELECT access FROM pages WHERE id ='$id'";
        $this->query($sql);
        $row = $this->fetchOneRowA();
        return $row['access'];
    }

    /**
     * Получение данных о пользователе и установка прав
     * @param $user_id
     */
    public function get_user_data($user_id)
    {
        if ($user_id > 0) {
            $sql = 'SELECT u.*, g.id AS gid, g.name AS gname FROM users u
            LEFT JOIN (groups_user ug INNER JOIN groups g ON ug.group_id = g.id) ON u.id = ug.user_id
            WHERE u.id = \'%1$u\'';
            $this->query($sql, $user_id);
            $this->userData = $this->fetchRowA();

            $sql = 'SELECT 
	    IF(ma.access IS NOT NULL, ma.access, ma2.access) AS access, 
	    IF(ma.action_name IS NOT NULL, ma.action_name, ma2.action_name) AS action_name, 
	    IF(mc.access IS NOT NULL, mc.access, ma.access) AS mcaccess, 
	    IF(m.name IS NOT NULL, m.codename, m2.codename) AS mcodename, 
	    IF(gu.group_id IS NOT NULL, gu.group_id, 0)  , 
	    IF(tn.owner IS NOT NULL, tn.owner, 0)  ,
	    IF(g.name IS NOT NULL, g.name, \'\') , 
	    IF(ma.id IS NOT NULL, ma.id, ma2.id) AS maid
		FROM tree_nodes tn
		INNER JOIN groups_user gu ON (tn.child = gu.group_id OR tn.owner = gu.group_id) AND gu.user_id = \'%1$u\'
		INNER JOIN module_access mc ON (tn.child = mc.group_id OR tn.owner = mc.group_id)
		INNER JOIN groups g ON mc.group_id = g.id
		INNER JOIN module_actions ma ON mc.action_id = ma.id
		INNER JOIN modules m ON ma.mod_id = m.id

		LEFT JOIN module_actions ma2 ON (ma2.access = 2 AND \'%1$u\' > 0) OR (ma2.access = 1)
		LEFT JOIN modules m2 ON ma2.mod_id = m2.id
		
		GROUP BY maid';


            $user_group = $this->userData['gid'];
            $this->query($sql, $user_id, $user_group);

            while (($row = $this->fetchRowA()) !== false) {
                $a = false;
                if (($row['access'] == 1) || ($row['access'] == 2 && $user_id > 0)) $a = true;
                elseif ($row['mcaccess'] == 1) $a = true;
                $this->Log->addToLog('Установка разрешения Auth ' . $row['mcodename'] . '.' . $row['action_name'] . ' установка в ' . (($a) ? 'Разрешено' : 'Запрещено'), __LINE__, __METHOD__);
                $this->user_right[$row['mcodename']][$row['action_name']] = $a;
            }
            //stop($this->user_right);
        } else {
            # ACTION_PUBLIC
            $this->user_right = array();
            $sql = 'SELECT ma.*, m.name AS mname, m.codename AS mcodename, mc.access AS mcaccess, mc.group_id
	            FROM module_actions ma
	            LEFT JOIN module_access mc ON ma.id = mc.action_id
	            INNER JOIN modules m ON ma.mod_id = m.id
	            WHERE ma.access = \'%2$u\' ';

            $this->query($sql, $user_id, ACTION_PUBLIC);

            while (($row = $this->fetchRowA()) !== false) {
                $a = false;
                if ($row['access'] == 1) $a = true;
                elseif ($row['mcaccess'] == 1) $a = true;
                $this->user_right[$row['mcodename']][$row['action_name']] = $a;
                $this->Log->addToLog('Установка разрешения ' . $row['mcodename'] . '.' . $row['action_name'] . ' установка в ' . (($a) ? 'Разрешено' : 'Запрещено'), __LINE__, __METHOD__);
            }
        }
    }


    /**
     * выход (вывод) из системы, как самостоятельно так и по окончании сессии
     * @param bool $redirect
     */
    public function logout($redirect = false)
    {
        $sql = "DELETE FROM session WHERE session_id = '".session_id()."'";
        $this->query($sql);
        if ($redirect) session_start();
        session_unset();
        session_destroy();
        if ($redirect) header('Location: http://' . $_SERVER["SERVER_NAME"] . '/');
    }


    /* Вход по Базе Данных */
    protected function login_DB()
    {
        $this->Log->addToLog('Авторизация по БД', __LINE__, __METHOD__);
        /**
         * 0 - нет ошибок, вход не выполнен
         * 1 - вошли удачно
         * -2 - логин или пароль не верены
         * -3 - сессия устарела
         */
        if ($this->Vals->isVal('logout', 'GET')) $this->logout(true);
        $login = false;
        /**
         * Проверка и Вход по логину и паролю - аутентификация
         */
        if ($this->Vals->isVal('username', 'POST') && $this->Vals->isVal('userpass', 'POST')) {
            $login = $this->authentication($this->Vals->getVal('username', 'POST', 'string'), $this->Vals->getVal('userpass', 'POST', 'string'));
            if (!$login) {
                $this->login_error = -2;
            }else{
                // Записываем сессию в БД
                $sql = "INSERT INTO session (session_id, user_id, dk) VALUES ('".session_id()."','".$this->user_id."',NOW())";
                $this->query($sql);
            }
        }

        /**
         * Старт сессии и проверка существующей на жизнь
         */

        if (isset($_SESSION['authorization'])) {
//            if (session_id() == $_SESSION['authorization_ses'] && $_SESSION['authorization_user'] != 0) {
                $login = true;
                $this->login_error = 1;
                $this->user_id = $_SESSION['authorization_user'];
//            } else {
//                $login = false;
//                $this->login_error = -3;
//            }
        }else{
            // Если пошло что-то не так, пытаемся прочитать сессию из БД
            $sql = "SELECT user_id FROM session WHERE session_id ='".session_id()."'";
            $this->query($sql);
            $this->user_id = $this->getOne();
            $sql2 = 'INSERT INTO logins (id_user,ip,referer,browser,screen_size,os)  VALUES
           (\'' . $this->user_id . '\', \'' . $_SERVER ["REMOTE_ADDR"] . '\', \'' . (isset($_SERVER ["HTTP_REFERER"])?$_SERVER ["HTTP_REFERER"]:'-') . '\', 
            \'' . $_SERVER ["HTTP_USER_AGENT"] . '\', \'' . $this->user_id . '\', \'' . addslashes($sql) . '\')';
            $this->query($sql2);
            if ($this->user_id > 0){
                $login = true;
                $this->login_error = 1;
            }
        }

        $this->get_user_data($this->user_id);
        /**
         * При удачной аутентификации, авторизируем
         */
        if ($login) {
            $_SESSION['authorization'] = 1;
            $_SESSION['authorization_LDAP'] = 0;
            $_SESSION['authorization_user'] = $this->user_id;
            $_SESSION['authorization_ses'] = session_id();
            $this->authorization = true;
            $this->login_error = 1;
            $this->Log->addToLog('Пользователь авторизован', __LINE__, __METHOD__);
            $this->Log->addToLog($this->user_right, __LINE__, __METHOD__);
            return true;
        } else {
            /*
            $sql2 = 'INSERT INTO logins (id_user,ip,referer,browser,screen_size,os)  VALUES
           (\'' . $this->user_id . '\', \'' . $_SERVER ["REMOTE_ADDR"] . '\', \'' . (isset($_SERVER ["HTTP_REFERER"])?$_SERVER ["HTTP_REFERER"]:'-') . '\', 
            \'' . $_SERVER ["HTTP_USER_AGENT"] . '\', \'session_logout\', \'' . json_encode($_SESSION) . '\')';
            $this->query($sql2);
*/
            $this->login_error = 0;
            $this->Log->addToLog(array('Пользователь не авторизован (Гость)', $this->user_id), __LINE__, __METHOD__);
            $this->logout();
            $this->Log->addToLog($this->user_right, __LINE__, __METHOD__);
            return false;
        }
    }


    /**
     * вход в систему, проверка сессии
     */
    public function login()
    {
        return $this->login_DB();
    }


    public function isAutch()
    {
        if (!isset($_SESSION['authorization']) || !isset($_SESSION['authorization_user'])) return false;
        if ($_SESSION['authorization'] == 1 && $_SESSION['authorization_user'] != 0) return $this->authorization;
        return false;
    }

}

class userView extends module_view
{
    public function __construct()
    {
        $modName = 'CurentUser';
        $sysMod = new modsetItem(array());
        parent::__construct($modName, $sysMod);
        $this->pXSL = array();
    }

    /**
     * Форма регистрации
     * @return boolean
     */
    public function getModName()
    {
        return $this->modName;
    }

    public function viewRegister()
    {

    }

    /**
     * Стандартная информация о пользователе
     * @param $userData -- Массив данных из TUser->userData
     * @return boolean
     */
    public function viewUserInfo($userData)
    {
        return is_array($userData);
    }

    /**
     * Продвинутый блок логина и пароля, включает в себя дополнительные параметры
     * @param $title
     * @param $message
     * @param $user_id
     * @param $Params - параметры отображения окна логин/статус
     * @param $StatusBar - данные для окна статуса
     * @param int $rightsModAdmin
     * @return bool
     */
    public function viewLoginParams($title, $message, $user_id, $Params, $StatusBar, $rightsModAdmin = 0)
    {
        global $_SESSION, $User, $loginErrors;
        /*
         * $Params set array of
         * 	isLogout : {0,1}
         * 	isRegister : {0,1}
         * 	regLink : string
         * 	titleType : {norm,error,warning}
         * 	isRedirect : {0,1}
         */
        $sysMod = new modsetItem(array());
        $sysMod->name = 'login';
        $sysMod->defModName = 'login';
//		$View = new module_view('login', $sysMod);
        if (gettype($message) != 'array') $message = array(0 => $message);
        if (gettype($title) != 'array') $title = array(0 => $title);
        $Container = $this->newContainer('login');

        $this->pXSL[] = RIVC_ROOT . 'layout/viewLoginBar.xsl';
        # sprintf($loginErrors[$User->lastError()], $User->getUserName())
        $this->addToNode($Container, 'login', intval($User->lastError()));
        $this->addToNode($Container, 'referer', SITE_ROOT . $_SERVER["REQUEST_URI"]);
        $this->addToNode($Container, 'user_id', $user_id);
        $this->addToNode($Container, 'group_id', $User->getUserGroup());
        $this->addToNode($Container, 'rightsModAdmin', $rightsModAdmin);
        $this->addToNode($Container, 'subject', $message[0]);
        $this->addToNode($Container, 'title', $title[0]);
        $this->addToNode($Container, 'error', sprintf($loginErrors[$User->lastError()], $User->getUserName()));
        if ($User->lastError() == 1) $this->addToNode($Container, 'user_name', $User->getUserName());
        else $this->addToNode($Container, 'user_name', '');

        $StatusBar[] = array('subject' => 'Группа', 'message' => $User->getUserGroupName());

        $ParamsBlock = $this->addToNode($Container, 'params', '');
        foreach ($Params as $key => $val) {
            $this->arrToXML($val, $ParamsBlock, 'item');
        }
        $StatusBarBlock = $this->addToNode($Container, 'statusbar', '');
        foreach ($StatusBar as $key => $val) {
            $this->arrToXML($val, $StatusBarBlock, 'item');

        }
        return true;
    }
}