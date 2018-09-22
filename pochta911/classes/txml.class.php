<?php

abstract class TXML
{

    protected $xml;
    protected $root;
    protected $blockName;

    public function __construct($blockName = '')
    {
        $this->blockName = $blockName;
        $this->xml = new DOMDocument('1.0', 'utf-8');
        $this->xml->preserveWhiteSpace = false;
        $this->xml->substituteEntities = false;
        $this->xml->formatOutput = false;
        if ($blockName) {
            $this->root = $this->xml->createElement('module');
            $this->xml->appendChild($this->root);
            $this->addAttr('name', $blockName, $this->root);
        } else {
            $this->root = false;
            $this->items = false;
        }
    }

    protected function addNodeToNode(DOMNodeList $NodeList, DOMElement $nodeTo, DOMDocument $DOMDocoment, $firstItem = 1)
    {
        if ($NodeList->length == 0) return false;
        if ($firstItem) {
            $element = $NodeList->item(0);
            $domNode = $DOMDocoment->importNode($element, true);
            $nodeTo->appendChild($domNode);
        } else {
            foreach ($NodeList as $element) {
                $domNode = $DOMDocoment->importNode($element, true);
                $nodeTo->appendChild($domNode);
            }
        }
        return $nodeTo;
    }

    /**
     * слияние XSL документов
     * @param $mainXML DOMDocument Главная XSL
     * @param $mainElement DOMElement, Элемент в который добавляем
     * @param $XML2 DOMDocument, Документ который нужно добавить
     * @param $partName string, Название нового блока (не обязательно)
     */
    protected function mergeXML(DOMDocument $mainXML, DOMElement $mainElement, DOMDocument $XML2, $partName = '')
    {
        global $LOG;
        # stop($mainXML->saveXML());

        $Inserted = $XML2->documentElement;
        if ($partName) {
            $a = $XML2->createAttribute('partition');
            $Inserted->appendChild($a);
            $a->appendChild($XML2->createTextNode('test'));
        }
        $node = $mainXML->importNode($Inserted, true);
        #$mainElement->appendChild($node);
        //stop($node);
        //var_dump($node->tagName);
        //exit;
        $mainElement->appendChild($node);
        $LOG->addToLog('Дбавлен блок: ' . $XML2->documentElement->tagName, __LINE__, __METHOD__);
    }

    protected function addToNode($nodeName, $newNode, $nodeValue = '')
    {
        global $LOG;
        if (gettype($nodeName) == 'object') $node = $nodeName;
        elseif (gettype($nodeName) == 'string') {
            $xquery = $nodeName;
            $xpath = new DOMXPath($this->xml);
            $nodes = $xpath->query($xquery);
            if ($nodes->length != 0) {
                $node = $nodes->item(0);
            } else return false;
        }
        if (gettype($nodeValue) != 'object') {
            if ($nodeValue) {
                $preData = htmlspecialchars($nodeValue);
                $preData = str_replace(array('&lt;![CDATA[', ']]&gt;', '&lt;br/&gt;'), array('<![CDATA[', ']]>', '<br />'), $preData);
                $item = $this->xml->createElement($newNode, $preData);
            } else {
                $item = $this->xml->createElement($newNode);
            }
            if (isset($node)) {
                $node->appendChild($item);
            }
            return $item;
        } else {
            $LOG->addError('Текстовая переменная передана как Объект!', __LINE__, __METHOD__);
        }
        return false;
    }

    protected function addToNodeCDATA($nodeName, $newNode, $nodeValue, $htmlspecialchars = false)
    {
        global $LOG;
        #if (isset($this->$nodeName)) $node = $this->$nodeName;
        if (gettype($nodeName) == 'object') $node = $nodeName;
        elseif (gettype($nodeName) == 'string') {
            $xquery = $nodeName;
            $xpath = new DOMXPath($this->xml);
            $nodes = $xpath->query($xquery);
            if ($nodes->length != 0) {
                $node = $nodes->item(0);
            } else return false;
        }
        //stop(array($nodeValue, $newNode),0);
        if (gettype($nodeValue) != 'object') {
            if ($nodeValue) {
                if ($htmlspecialchars) {
                    $preData = htmlspecialchars($nodeValue);
                } else {
                    $preData = $nodeValue;
                }
                $item = $this->xml->createElement($newNode, '');
                $cd = $this->xml->createCDATASection(rn . $preData . rn);
                $item->appendChild($cd);
            } else {
                $item = $this->xml->createElement($newNode);
            }
            if (isset($node)) {
                $node->appendChild($item);
            }
            return $item;
        } else {
            $LOG->addError('Текстовая переменная передана как Объект!', __LINE__, __METHOD__);
        }
        return false;
    }
    /*
      protected function insertNodes ($nodes, $toNode) {
        return $toNode->appendChild($nodes);
      }
    */
    /**
     *
     * @param string $attrName название атрибута
     * @param string $attrValue значение
     * @param DOMElement $nodeName
     * @return boolean
     */
    protected function addAttr($attrName, $attrValue, DOMElement $nodeName)
    {
        if (gettype($nodeName) != 'object') return false;
        $a = $this->xml->createAttribute($attrName);
        $nodeName->appendChild($a);
        $a->appendChild($this->xml->createTextNode($attrValue));
        return true;
    }

    /**
     *  function converting a simple HTML to XML
     * $html - string line. The line should not contain a HTML "head" and "body" tags.
     * nodeName - DOMNode
     * @param $content
     * @param string $datatype
     * @return DOMDocument
     */
    protected function HTMLtoXML($content, $datatype = 'html')
    {
        global $LOG;

        $html = new DOMDocument('1.0', 'utf-8');
        $xml = new DOMDocument('1.0', 'utf-8');

        $html->formatOutput = true;
        $xml->formatOutput = true;

        $contentXML = $xml->createElement('body');
        $xml->appendChild($contentXML);
        if (gettype($content) == 'object') {
            $content = 'Data ERROR ' . implode(', ', debug_backtrace());
        }

        if ($datatype == 'xml') $html->loadXML('<html><head></head><body>' . $content . '</body></html>');
        if ($datatype == 'html') $html->loadHTML('<html><head></head><body>' . $content . '</body></html>');

        $xpath = new DOMXPath($html);
        $conts = $xpath->query('//html/body');

        if ($conts->length == 0) $LOG->addWarning(array('Не найден HTML', 'content:' => $content), __LINE__, __METHOD__);
        $this->addNodeToNode($conts, $contentXML, $xml);
        return $xml;
    }

    /**
     *
     * @param $data
     * @param $node
     * @param $CDATA
     * @return object
     */
    protected function rowToXML($data, $node, $CDATA = array())
    {
        return $this->arrToXML($$data, $node, 'row', $CDATA);
    }

    /**
     *
     * @param $array - Массив данных
     * @param $node - Имя нового узла
     * @param $conainer_name - Узел контейнер
     * @param array $CDATA - Массив названий полей, которые должны быть преобразованы в CDATA
     * @param int $recursion
     * @return DOMNode - Новый узел
     * @throws Exception
     */
    protected function arrToXML($array, $node, $conainer_name, $CDATA = array(), $recursion = 0)
    {
        $recursion++;

        if ($conainer_name == '' || getType($conainer_name) != 'string') {
            debug_print_backtrace();
            stop(array('arrToXML : Ошибка имени контейнера ', $conainer_name));
        }

        if (!is_array($array)) {
            throw new Exception('Переменная $Array не массив. Тип: ' . get_class($array));
        }

        if ($recursion > 100) {
            stop('arrToXML: Достигнут предел рекурсии ', 0);
            stop(array($array, $node, $conainer_name), 0);
            debug_print_backtrace();
            exit;
        }
        //stop($conainer_name.' '.getType($conainer_name),0);
        $items = $this->xml->createElement($conainer_name, '');
        $node->appendChild($items);
        /**
         * преобразование типов
         */
        foreach ($array as $key => $val) {
            $insert = true;
            switch (gettype($val)) {
                case 'array':
                    # $subArr = $this->arrToXML($val, $items, 'item');
                    // $val = 'isArray';
                    //   if (is_numeric($key)){$key = 'a'.$key;}
                    //	$subItem = $this->xml->createElement($key, '');
                    //	$items->appendChild($subItem);
                    $CN = $this->arrToXML($val, $node, is_numeric($key) ? 'array' : $key, $CDATA, $recursion);
                    $items->appendChild($CN);
                    $this->addAttr('count', count($val), $CN);
                    $insert = false;
                    break;
                case 'boolean':
                    if ($val) $val = 'true'; else $val = 'false';
                    break;
                case 'string':
                    $val = strval($val);
                    break;
                case 'integer':
                    $val = intval($val);
                    if ($val == 0) $val = '0';
                    break;
                case 'float':
                case 'double':
                    $val = floatval($val);
                    if ($val == 0) $val = '0';
                    break;
                case 'object':
                    //if (get_parent_class($val) == 'modile_item');
                    //if(get_parent_class($val) == 'module_collection');
                    //	break;
                    //$val = 'object';
                    //stop($val,0);
                    //var_dump($val);
                    if (get_parent_class($val) == 'module_item') {
                        //stop(array(get_class($val),$key),0);
                        $Params = $val->toArray();
                        if (get_class($val) == 'groupItem') {
                            $insert = false;
                            if ($val->parent == 0) continue;
                        }
                        $this->arrToXML($Params, $node, get_class($val), $CDATA, $recursion);
                        $insert = true;
                    } elseif (get_parent_class($val) == 'module_collection') {
                        $insert = false;
                        if ($val->count() == 0) continue;
                        foreach ($val as $objectItem) {
                            $subItem = $this->xml->createElement(get_class($objectItem), '');
                            $items->appendChild($subItem);
                            //stop($objectItem->toArray());
                            $this->arrToXML($objectItem->toArray(), $subItem, 'item', $CDATA, $recursion);
                        }

                    } else $val = 'unsupported Object [' . get_class($val) . '].[' . get_parent_class($val) . '] ';
                    break;
                case 'NULL':
                    $val = '';
                    break;
                default:
                    $val = 'undefined type: ' . gettype($val);
            }
            /*
             * вставляем Узел
             * $val != '' &&
             */
            if ($insert) {
                /*
                 * Проверка названия узла на корректность
                 */
                if (preg_match('/^[a-z-A-Z_]/', $key) != 0) {
                    /*
                     * Проверка узла на необходимость создать CDATA секцию
                     */
                    if (in_array($key, $CDATA)) {
                        /*
                         * Вставка CDATA
                         */
                        $newElement = $this->xml->createElement($key, '');
                        $cd = $this->xml->createCDATASection(rn . $val . rn);
                        $newElement->appendChild($cd);
                    } else {
                        /*
                         * вставка обычным способом
                         */
                        $newElement = $this->xml->createElement($key, $val);
                    }

                } else {
                    /*
                     * То же самое, только перед $KEY вставляется буква "a"
                     */
                    if (in_array($key, $CDATA)) {
                        $newElement = $this->xml->createElement('element', '');
                        $cd = $this->xml->createCDATASection(rn . $val . rn);
                        $newElement->appendChild($cd);
                        $attr = $this->xml->createAttribute('key');
                        $newElement->appendChild($attr);
                        $text = $this->xml->createTextNode($key);
                        $attr->appendChild($text);
                    } else {
                        $newElement = $this->xml->createElement('element', $val);
                        $attr = $this->xml->createAttribute('key');
                        $newElement->appendChild($attr);
                        $text = $this->xml->createTextNode($key);
                        $attr->appendChild($text);
                    }
                }
                if (!is_object($newElement)) stop($newElement);
                $items->appendChild($newElement);
            }
        }
        return $items;
    }

    protected function getNode($nodePath, $inXML)
    {
        $xpath = new DOMXPath($inXML);
        $list = $xpath->query($nodePath);
        if ($list->length == 1) {
            $item = $list->item(0);
            if (get_class($item) == 'DOMDocument') {
                $element = $item->documentElement;
                $list = $item->getElementsByTagName($element->tagName);
            }
        }
        return $list;
    }

    protected function newContainer($name)
    {
        $container = $this->xml->createElement('container');
        $this->root->appendChild($container);
        $this->addAttr('module', $name, $container);
        return $container;
    }

    protected function newModuleContainer($name, $modName)
    {
        $module = $this->xml->createElement('submodule');
        $this->xml->documentElement->appendChild($module);
        $this->addAttr('name', $name, $module);

        $container = $this->xml->createElement('subcontainer');
        $module->appendChild($container);
        $this->addAttr('module', $name, $container);
        return $container;
        /*
          $xml = new DOMDocument('1.0', 'utf-8');
          $xml->preserveWhiteSpace = false;
          $xml->formatOutput   = false;

          $root = $xml->createElement('module');
          $xml->appendChild($root);

          $a = $xml->createAttribute('name');
          $root->appendChild($a);
          $a->appendChild($xml->createTextNode($name));
        */
    }

    protected function dropBody()
    {
        $this->xml = new DOMDocument('1.0', 'utf-8');
        $this->xml->preserveWhiteSpace = false;
        $this->xsl->formatOutput = false;
        if ($this->blockName) {
            $this->root = $this->xml->createElement('module');
            $this->xml->appendChild($this->root);
            $this->addAttr('name', $this->blockName, $this->root);
        } else {
            $this->root = false;
            $this->items = false;
        }
    }
}

/**
 *
 * @author poltavcev
 *
 */
class TXMLPage extends TXML
{
    /**
     * @param page - root
     */
    protected $pageName;
    protected $pageID;
    protected $pageClass;
    protected $page;
    protected $header;
    protected $body;
    protected $menu;
    protected $head;
    protected $content;
    protected $footer;

    protected $Log;
    protected $pXSL;

    protected $bodyRec;
    protected $hasErrors;
    protected $pageXSL;

    /**
     *
     * @param string $pageName
     * @param $pageID
     * @param $pageClass
     * @param $pageXSL
     */
    public function __construct($pageName, $pageID, $pageClass, $pageXSL)
    {
        global $LOG;

        parent::__construct();
        $this->xml->preserveWhiteSpace = true;
        #$this->xsl->substituteEntities = false;
        $this->xml->formatOutput = false;
        $this->Log = $LOG;
        $this->pXSL = array();
        #$this->pXSL[] = RIVC_ROOT.'layout/page.'.$pageName.'.xsl';
        $this->pageXSL = $pageXSL;
        $this->page = $this->xml->createElement('page');
        $this->xml->appendChild($this->page);

        if ($pageName) $this->addAttr('name', $pageName, $this->page);
        if ($pageID) $this->addAttr('id', $pageID, $this->page);
        if ($pageClass) $this->addAttr('class', $pageClass, $this->page);
        if ($pageClass) $this->addAttr('host', $_SERVER["HTTP_HOST"], $this->page);

        $this->header = $this->addToNode($this->page, 'header');
        $this->body = $this->addToNode($this->page, 'body');
        $this->Log->addToLog('Конструктор ', __LINE__, __METHOD__);

        $this->bodyRec = new ArrayObject();
        $this->pageName = $pageName;
        $this->pageID = $pageID;
        $this->pageClass = $pageClass;

        $this->hasErrors = false;
    }

    protected function addToPage($nodeName, $nodeValue)
    {
        return $this->page->appendChild($this->xml->createElement($nodeName, $nodeValue));
    }

    public function addToPageAttr($attrName, $attrValue)
    {
        $this->addAttr($attrName, $attrValue, $this->page);
    }

    public function importBlock(bodySet $block, $partName)
    {
        #stop($block->getXML());
        $this->mergeXML($this->xml, $this->body, $block->getXML());
        $this->pXSL = array_merge($this->pXSL, $block->getXSL());
        $this->Log->addToLog(array('partName' => $partName, join(', ', $this->pXSL)), __LINE__, __METHOD__);
        if ($block->getErrors()) $this->hasErrors = true;
    }

    public function title($title)
    {
        $this->addToNode($this->header, 'title', $title);
    }

    public function addMeta($datatype, $type, $content, $rel)
    {
        $node = $this->addToNode($this->header, 'meta', '');
        $this->addAttr('datatype', $datatype, $node);
        $this->addAttr('content', $content, $node);
        $this->addAttr('type', $type, $node);
        $this->addAttr('rel', $rel, $node);
        #$this->addAttr ('href', $href, $node);
    }

    private function addToMenu($id, $title, $href, $parentNode = false)
    {
        if (!$parentNode) {
            $node = $this->menu;
        } else {
            $node = $parentNode;
        }
        $owner = $this->addToNode($node, 'item', $title);
        $this->addAttr('href', $href, $node);
        $this->addAttr('onMouseOver', '', $node);
        $this->addAttr('onMouseOut', '', $node);
        $this->addAttr('onClick', '', $node);
#    if ($parentNode) $this->addAttr ('isChild', 1, $node);
        return $owner;
    }

    /**
     * $items[parent][] - array(id, title, href, parent)
     *               [] - array(id, title, href, parent)
     */
    private function recMenu($tree, $parent = 0)
    {
        foreach ($tree[$parent] as $arr) {
            #foreach ($item as $key => $arr) {
            if ($arr['parent'] == $parent) $a = $this->addToMenu($arr['id'], $arr['title'], $arr['href'], $arr['parent']);
            if ($arr['parent'] != $parent) {
                $a = $this->recMenu($arr['id'], $arr['title'], $arr['href'], $arr['parent']);
                $this->buildMenu($a, $arr['parent']);
            }
            #}
        }
    }

    public function buildMenu($menuItems)
    {
        $this->recMenu($menuItems);
    }

    final public function getBody($data_type)
    {
        global $glob_inc;
        if (DEBUG == 1) file_put_contents('debug/getBody_PAGE_' . $this->blockName . '.xml', $this->xml->saveXML());
        if ($data_type == 'xml') {
            return $this->xml;
        }
        if ($data_type == 'xmls') {
            $this->xml->preserveWhiteSpace = false;
            $this->xsl->formatOutput = true;
            return $this->xml->saveXML();
        }
        if ($data_type == 'html') {
            $this->Log->addToLog('pXSL ' . join(', ', $this->pXSL), __LINE__, __METHOD__);
            $data = '';
            if (count($this->pXSL) == 0) {
                $this->Log->addError('Не найден путь к XSL (' . join(', ', $this->pXSL) . ')', __LINE__, __METHOD__);
                //return false;
            }

            if (gettype($this->pXSL) != 'array') $pXSL = array($this->pXSL); else $pXSL = $this->pXSL;
            $xslt = new XSLTProcessor();
            arsort($pXSL); /* Переворачивем XSLки, что бы последеней была XSL страницы */
            if (DEBUG == 1) $fp = fopen('debug/pXSL.txt', 'a');
            $debug0 = array();
            $pRoot = RIVC_ROOT . 'layout/' . $this->pageXSL;
            if (DEBUG == 1) fwrite($fp, 'Root XSL page: ' . $pRoot . rn);
            $xslns = "http://www.w3.org/1999/XSL/Transform";

            $xsl = new DOMDocument('1.0', 'utf-8');
            // stop('Критическая ошибка. Не найден главный шаблон '.$pRoot);
            if (!file_exists($pRoot) || is_dir($pRoot)) {
                if (DEBUG == 1) {
                    header('Content-Type: text/html; charset=utf-8');
                    debug_print_backtrace();
                    echo join('<br /> ', $pXSL);
                    global $LOG;
                    echo $LOG->viewLog();
                }

                stop('Критическая ошибка. Не найден главный шаблон ');

            }
            $xsl->load($pRoot);
            $Element = $xsl->documentElement;
            $Exists = array();
            foreach ($pXSL as $pKey => $XSLPath) {
                if (!in_array($XSLPath, $Exists)) {
                    $Exists[] = $XSLPath;
                    $debug0[] = $XSLPath;
                    if (DEBUG == 1) fwrite($fp, $glob_inc . ':' . $pKey . '-' . $XSLPath . rn);
                    $imp = $xsl->createElementNS($xslns, 'xsl:include');
                    $Element->appendChild($imp);
                    $attr = $xsl->createAttribute('href');
                    $imp->appendChild($attr);
                    $text = $xsl->createTextNode('../' . $XSLPath);
                    $attr->appendChild($text);
                }
            }
            if (DEBUG == 1) {
                fwrite($fp, rn . join(', ', $pXSL) . rn);
                fclose($fp);
                file_put_contents('debug/getBody_PAGE_merged_' . $this->blockName . '_' . $glob_inc . '.xsl', $xsl->saveXML());
                file_put_contents('debug/getBody_PAGE_pXSL.txt', join("\r\n ", $debug0));
            }
            @$xslt->importStyleSheet($xsl);
            $res = @$xslt->transformToDoc($this->getBody('xml'));
            if (gettype($res) != 'object') {
                header('Content-Type: text/html; charset=utf-8');
                if (DEBUG == 1) {
                    debug_print_backtrace();
                    echo '<hr />';
                    echo join('<br /> ', $pXSL);
                    global $LOG;
                    echo $LOG->viewLog();
                }
                exit;
            }
            $data = $res->saveHTML();
            if (DEBUG == 1) file_put_contents('debug/finish_page.html', $data);
            return $data;
        }
    }

    public function setContentBlock($module)
    {
        $this->addAttr('contentContainer', $module, $this->body);
        if ($this->hasErrors) $this->addAttr('hasErrors', '1', $this->body); else $this->addAttr('hasErrors', '0', $this->body);
    }

    public function getBodyAjax()
    {
        Global $values;
        $ajax = $values->getVal('ajax', 'GET', 'string');
        if (!$ajax == '') {
            if ($ajax == 'pages') {
                $doc = $this->getBody('xml');
                header('Content-type: text/xml; charset=utf-8;');
                exit($doc->saveXML());
            }
        }
    }
}

class PageForAjax extends TXMLPage
{

    public function __construct($pageName, $pageID, $pageClass, $pageXSL)
    {
        parent::__construct($pageName, $pageID, $pageClass, $pageXSL);
    }


    public function getBodyAjax2(module_view $View)
    {
        // $Page = new TXMLPage($this->pageName, 'index', 'index', $mainPageXSL);
        $this->title('SkyLC');
        $this->addMeta('http-equiv', 'Content-Type', 'text/html; charset=utf-8', '');

        $sysBODY[] = array($View->getBody('html'), $this->pageName, 'html');

        foreach ($sysBODY as $block) {
            $this->importBlock($block[0], $block[1]);
        }
        // $_SESSION['last_page'] = $_SERVER["SERVER_NAME"].$_SERVER["REQUEST_URI"];
        $this->setContentBlock($this->pageName);

        return $this->getBody('html');
    }
}