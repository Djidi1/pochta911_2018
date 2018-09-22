<?php
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include 'config.php';
header('Content-type: text/html; charset=utf-8');

$SQL_COUNTER = 0;
include CORE_ROOT . 'classes/log.class.php';
$LOG = new TLog();
include CORE_ROOT . 'classes/db.class.php';
include CORE_ROOT . 'classes/values.class.php';
$values = new TValues();
include CORE_ROOT . 'classes/txml.class.php';
include CORE_ROOT . 'classes/modules.class.php';
include CORE_ROOT . 'classes/autch.class.php';
include CORE_ROOT . 'classes/files.class.php';
include CORE_ROOT . 'classes/formgen.class.php';
include CORE_ROOT . 'classes/system.class.php';
include CORE_ROOT . 'classes/sms.ru.php';

$_SESSION['DEBUG'] = 0;

if ($values->isVal('debug', 'GET')) $_SESSION['DEBUG'] = $values->getVal('debug', 'GET', 'integer');
if (!isset($_SESSION['DEBUG'])) $_SESSION['DEBUG'] = 0;
define('DEBUG', $_SESSION['DEBUG']);

if ($values->isVal('fullscreen', 'GET')) $_SESSION['FULLSCREEN'] = $values->getVal('fullscreen', 'GET', 'integer');
if (!isset($_SESSION['FULLSCREEN'])) $_SESSION['FULLSCREEN'] = 0;
define('FULLSCREEN', $_SESSION['FULLSCREEN']);

$System = new TSYSTEM();

// stop($_SERVER["DOCUMENT_ROOT"]);



$User = new TUser();
$User->login();

$System = new TSYSTEM();
$data = false;
$sysBODY = array();

$module = $values->getVal('module', 'GET');
if (intval($User->isAutch()) > 0 and !$module) {
    $group_id = $User->getUserGroup();
    if (in_array($group_id, array('2'))) {
        $module = 'orders';
    } elseif (in_array($group_id, array('1', '3', '4'))) {
        $module = 'orders';
        $values->setValTo('LogistList', '1', 'GET');
    }
}/* else {
    // ???
}
*/
if (!$module) $module = 'title';             # костыль

switch ($module) {
    case 'files':
        $sysMod = new fileProcess('file');
        $sysMod->update('download');
        break;
    case 'image':
        $sysMod = new fileFast();
        $sysMod->update('image');
        break;
}

$values->setValTo('module', $module, 'GET');
if ($module) {
    $LOG->addSysMSG($module, __LINE__, __FILE__);

//  $LOG->addCheckPoint('Preload', __LINE__,__METHOD__);
    /*   * @var modData <string, addonsSet>
     */
    $modData = modulePreload($module);

    if ($modData) {
        if (is_file('modules/' . $modData->module_defModName . '/' . $modData->module_defModName . '.class.php')) {
            $LOG->addWarning(array('Модуль подключен', $module), __LINE__, 'index.php');
        } else {
            $LOG->addWarning(array('подключен виртуальный модуль', $module), __LINE__, 'index.php');
        }
        if (!$modData->module_isSystem && !class_exists($modData->module_processName)) include('modules/' . $modData->module_defModName . '/' . $modData->module_defModName . '.class.php');
        if ($modData->module_isSystem && !class_exists($modData->module_processName)) include(CORE_ROOT . 'classes/' . $modData->module_defModName . '.class.php');
        $autoClass = $modData->module_processName;
        //$values->URLparams($modData->module_defQueryString);
        $sysMod = new $autoClass($modData->module_codename);
        $sysMod->setDefaultAction($modData->module_defAction);
        $sysMod->update();

        $sysBODY[] = array($sysMod->getBody('html'), 'pageContent', 'html');

        //$LOG->addCheckPoint('Загрузка подмодулей', __LINE__,__METHOD__);
        //stop($modData);
        $subModules = $modData->modules->getIterator();
        foreach ($subModules as $sMod) {
            $LOG->addCheckPoint('ADDON:', __LINE__, __METHOD__);
            //stop($sMod);
            if (is_file('modules/' . $sMod->defModName . '/' . $sMod->defModName . '.class.php')) {
                $LOG->addWarning(array('Подключен дополнительный модуль', $sMod->name, 'QueryString' => $sMod->defQueryString), __LINE__, 'index.php');
            } else {
                $LOG->addWarning(array('Подключен виртуальный дополнительный модуль', $sMod->name), __LINE__, 'index.php');
            }
            $autoClass = $sMod->processName;
            $LOG->addToLog(array('QS1' => $sMod->defQueryString), __LINE__, 'index');
            if (!$sMod->isSystem && !class_exists($autoClass)) include('modules/' . $sMod->defModName . '/' . $sMod->defModName . '.class.php');
            if ($sMod->isSystem && !class_exists($autoClass)) include(CORE_ROOT . 'classes/' . $sMod->defModName . '.class.php');

            //include ('modules/'.$sMod->defModName.'/'.$sMod->defModName.'.class.php');
            $values->setModuleParamQS($sMod->codename, $sMod->defQueryString);
            $sysMod = new $autoClass($sMod->codename);
            $sysMod->update($sMod->defAction);
            $sysBODY[] = array($sysMod->getBody('html'), $sMod->codename, 'html');
        }
        $Page = new TXMLPage($modData->module_codename, 'index', 'index', $modData->module_defXSL);
        $Page->title('Балтиклайнс Тур');
        $isAjax = $values->getVal('ajax', 'INDEX', 'integer');
        $without_menu = $values->getVal('without_menu', 'INDEX', 'integer');
        $Page->addToPageAttr('isAjax', intval($isAjax));
        $Page->addToPageAttr('year', date('Y'));
        $Page->addToPageAttr('without_menu', $without_menu);
        $Page->addToPageAttr('fullscreen', FULLSCREEN);
        $Page->addToPageAttr('new_page', $User->get_menu_new(49));
        $Page->addMeta('http-equiv', 'Content-Type', 'text/html; charset=utf-8', '');

        foreach ($sysBODY as $block) {
            $Page->importBlock($block[0], $block[1]);
        }
        $_SESSION['last_page'] = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
        echo $Page->setContentBlock($module);

        $Page->importBlock($User->nView->getBody(), $User->nView->getModName());
        echo $Page->getBody('html');

    } else {
        switch ($module) {
            case 'image':
                break;
            case 'file':
                break;
            default:
                $LOG->addError(array('Модуль не существует!'), __LINE__, __METHOD__, 0);
                $Page = new TXMLPage('index', 'index', 'index', 'page.index.xsl');
                $Page->title('');
                $Page->addMeta('http-equiv', 'Content-Type', 'text/html; charset=utf-8', '');

                $User->nView->viewLogin('', $User->getUserID(), 'login');
                $sysBODY[] = array($User->nView->getBody('html'), 'index', 'html');
                foreach ($sysBODY as $block) {
                    $Page->importBlock($block[0], $block[1]);
                }
                $_SESSION['last_page'] = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
                echo $Page->setContentBlock('CurentUser');

                echo $Page->getBody('html');
                break;
        }
    }
} else {
    $_SESSION['last_page'] = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
    echo 'Error GET MODULE';
}
$_SESSION['last_page'] = $_SERVER["SERVER_NAME"] . $_SERVER["REQUEST_URI"];
$LOG->addSysMSG($_SESSION['last_page'], __LINE__, __METHOD__);
if (DEBUG == 1) echo $LOG->viewLog();

