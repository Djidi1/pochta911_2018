<?php

define ('FORM_DEF_CLASSNAME', '');

class CFormItem {
  public $name;
  public $lable;
  public $value;
  public $type;
  public $id;
  public $size;
  public $className;
  public $defaultText;
  public $defaultVal;
  public $checked;

  public function __construct ($label, $name, $value, $type, $defT, $defV, $id, $size, $class) {
    $this->label = $label;
    $this->name = $name;
    if (!$this->name) $this->name = 'unnamed'.time();
    $this->value = $value;
    $this->type = $type;
    $this->defaultText = $defT;
    $this->defaultVal = $defV;
    $this->id = $id;
    $this->size = $size;
    $this->className = $class;
  }

   public function getID() {
     return $this->name;
   }
}

class CFormCollection extends ArrayObject {

  public function __construct() { parent::__construct(); }

  public function add(CFormItem $item) {
    if (!$this->offsetExists($item->getID())) $this[$item->getID()] = $item;
  }

  public function addItem($label, $name, $value, $type, $defT, $defV, $id, $size, $class) {

    $item = new CFormItem($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
    $this->add($item);
  }

  public function del($item_id) {
    $this->offsetUnset($item_id);
  }

  public function count() {
    return parent::count();
  }
}

class CFormGenerator {
  private $xml;
  private $form;
  private $Collection;
  private $Log;

  private $formName;
  private $action;
  private $method;
  private $multiPart;

  public function __construct ($formName, $action, $method, $multiPart) {
    global $LOG;
    $this->Log = $LOG;
    $this->formName = $formName;
    $this->action = $action;
    $this->method = $method;
    $this->multiPart = $multiPart;

#    $this->ReSET ($formName, $action, $method, $multiPart);

    if ($multiPart) {
        $eAttr = $this->xml->createAttribute('method');
        $attr = $this->form->appendChild($eAttr);
        $attr->appendChild($this->xml->createTextNode($method));
    }

    $this->Collection = new CFormCollection();
  }

  private function ReSET (DOMElement $DOMElement, $formName, $action, $method, $multiPart) {

    $this->xml = $DOMElement->ownerDocument;
    $root = $DOMElement;
    $formdata = $this->xml->createElement('formdata');
    $root->appendChild($formdata);
    $this->form = $this->xml->createElement('form');
    $formdata->appendChild($this->form);

    $eAttr = $this->xml->createAttribute('name');
    $attr = $this->form->appendChild($eAttr);
    $attr->appendChild($this->xml->createTextNode($formName));

    $eAttr = $this->xml->createAttribute('action');
    $attr = $this->form->appendChild($eAttr);
    $attr->appendChild($this->xml->createTextNode($action));

    $eAttr = $this->xml->createAttribute('method');
    $attr = $this->form->appendChild($eAttr);
    $attr->appendChild($this->xml->createTextNode($method));

    if ($multiPart) {
        $eAttr = $this->xml->createAttribute('method');
        $attr = $this->form->appendChild($eAttr);
        $attr->appendChild($this->xml->createTextNode($method));
    }

  }

  private function addInput ($label, $name, $value, $type, $defT, $defV, $id, $size, $class) {
    $label = iconv('utf-8', 'utf-8', $label);
    $defT = iconv('utf-8', 'utf-8', $defT);
    $value = iconv('utf-8', 'utf-8', $value);
    $item = new CFormItem ($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
    //stop($item, 0);
    $this->Collection->add($item);
  }

  public function addHidden ($name, $value, $id) {
    $defT = '';
    $defV = '';
    $label = '';
    $class = '';
    $size = '';
    $type = 'hidden';
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
  }

  public function addText ($name, $value, $label, $id, $class, $size) {
    $defT = '';
    $defV = '';
    $type = 'text';
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
  }  
  public function addPass ($name, $value, $label, $id, $class, $size) {
    $defT = '';
    $defV = '';
    $type = 'password';
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
  }

  public function addTextArea ($name, $value, $label, $id, $class, $size){
    $defT = '';
    $defV = '';
    $type = 'textarea';
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
  }

  /**
  * defV - отвечает за CHECKED
  * defT - текст после текст бокса
  */
  public function addBox ($name, $value, $label, $defT, $defV, $id, $class, $size) {
    $type = 'checkbox';
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
  }

  public function addSubmit ($name, $value, $id, $class){
    $label = '';
    $defT = '';
    $defV = '';
    $size = '';
    $type = 'submit';
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
  }

  public function addSelect ($name, $selVal, $label, $defT, $defV, $id, $class, $size) {
    $type = 'select';
    $value = $selVal;
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
  }

  public function addOption ($name, $value, $label, $selectName, $id, $class, $size) {
    $defT = $selectName;
    $defV = '';
    $type = 'option';
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
  }

  public function addFile ($name, $value, $label, $id, $class, $size, $codename) {
    $defT = '';
    $defV = '';
    $type = 'file';
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
    if ($value != '') {
    	$this->addHidden ('file_'.$name, $codename, $id);	
    }
  }
  public function addFileFI ($name, $value, $label, $id, $class, $size, $codename) {
    $defT = '';
    $defV = '';
    $type = 'filefi';
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
    if ($value != '') {
    	$this->addHidden ('file_'.$name, $codename, $id);	
    }
  }
  public function addMessage ($name, $value, $id) {
    $defT = '';
    $defV = '';
    $label = '';
    $class = $id;
    $size = '';
    $type = 'message';
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
  }

    public function addMessageLink ($name, $value, $id, $href, $linkCaption) {
    $defT = $linkCaption;
    $defV = $href;
    $label = '';
    $class = $id;
    $size = '';
    $type = 'messagelink';
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
  }

  public function addLabledMessage ($name, $label, $value, $id) {
    $defT = '';
    $defV = '';
    $class = $id;
    $size = '';
    $type = 'lmessage';
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
  }
  public function addButton($name, $value, $label, $defT) {  	
    $defV = '';
    $id = $defT;
    $class = $id;
    $size = '';
    $type = 'button';
    $this->addInput($label, $name, $value, $type, $defT, $defV, $id, $size, $class);
  }
  
  private function buildBody (DOMElement $DOMElement) {
    $this->ReSET ($DOMElement, $this->formName, $this->action, $this->method, $this->multiPart);
    $cIterator = $this->Collection->getIterator();
    $selectID = array();
    $optionID = array();
    foreach ($cIterator as $item) {
      #$this->Collection->add($item);
     $label = $item->label;
     $name = $item->name;
     $value = $item->value;
     $type = $item->type;
     $defaultText = $item->defaultText;
     $defaultVal = $item->defaultVal;
     $id = $item->id;
     $size = $item->size;
     $className = $item->className;
      switch ($item->type) {
        case 'hidden':
            $input = $this->xml->createElement('item');
            $this->form->appendChild($input);
            $attrName = 'id'; $attrVal = $id; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'name'; $attrVal = $name; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'type'; $attrVal = $type; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));

            $value = $this->xml->createElement('value', $item->value);
            $input->appendChild($value);
        break;
        case 'message':
            $input = $this->xml->createElement('item', $item->value);
            $attrName = 'type'; $attrVal = $type; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'className'; $attrVal = $className; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));

            $this->form->appendChild($input);
        break;
        case 'messagelink':
            $input = $this->xml->createElement('item', $item->value);
            $attrName = 'type'; $attrVal = $type; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'className'; $attrVal = $className; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));

            $this->form->appendChild($input);
            $label = $this->xml->createElement('label', $item->label);
            $input->appendChild($label);
            $value = $this->xml->createElement('value', $item->value);
            $input->appendChild($value);
            $defT = $this->xml->createElement('defT', $item->defaultText);
            $input->appendChild($defT);
            $defV = $this->xml->createElement('defV', $item->defaultVal);
            $input->appendChild($defV);
        break;
        case 'lmessage':
            $input = $this->xml->createElement('item', $item->value);
            $attrName = 'type'; $attrVal = $type; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'className'; $attrVal = $className; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));

            $this->form->appendChild($input);
            $label = $this->xml->createElement('label', $item->label);
            $input->appendChild($label);
            $value = $this->xml->createElement('value', $item->value);
            $input->appendChild($value);
        break;
        case 'password':
        case 'text':
            $input = $this->xml->createElement('item');
            $this->form->appendChild($input);
            $attrName = 'id'; $attrVal = $id; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'name'; $attrVal = $name; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'className'; $attrVal = $className; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'type'; $attrVal = $type; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'size'; $attrVal = $size; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));

            $label = $this->xml->createElement('label', $item->label);
            $input->appendChild($label);
$val = htmlspecialchars($item->value);
            $value = $this->xml->createElement('value', $val);
            $input->appendChild($value);
        break;
		
        case 'filefi': /** !!!! **/
        case 'file':
            $input = $this->xml->createElement('item');
            $this->form->appendChild($input);
            $attrName = 'id'; $attrVal = $id; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'name'; $attrVal = $name; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'className'; $attrVal = $className; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'type'; $attrVal = $type; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'size'; $attrVal = $size; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));

            $label = $this->xml->createElement('label', $item->label);
            $input->appendChild($label);
            if ($value == 0) $value = '';
            $value = $this->xml->createElement('value', $item->value);
            $input->appendChild($value);
        break;
        
        case 'textarea':
            $input = $this->xml->createElement('item');
            $this->form->appendChild($input);
            //stop ($className);
            $attrName = 'id'; $attrVal = $id; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'name'; $attrVal = $name; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'className'; $attrVal = $className; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'type'; $attrVal = $type; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'size'; $attrVal = $size; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));

            $label = $this->xml->createElement('label', $item->label);
            $input->appendChild($label);

            $value = $this->xml->createElement('value');
            $CDATA = $this->xml->createCDATASection($item->value);
            $value->appendChild($CDATA);
            //$this->form->appendChild($input);
            
            
            $input->appendChild($value);

        break;
        case 'checkbox':
            $input = $this->xml->createElement('item');
            $this->form->appendChild($input);
            $attrName = 'id'; $attrVal = $id; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'name'; $attrVal = $name; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'className'; $attrVal = $className; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'type'; $attrVal = $type; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'size'; $attrVal = $size; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));

            $attrName = 'checked'; $attrVal = $defaultVal; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'postlabel'; $attrVal = $defaultText; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));

            $label = $this->xml->createElement('label', $item->label);
            $input->appendChild($label);

            $value = $this->xml->createElement('value', $item->value);
            $input->appendChild($value);
        break;

        case 'select':
            $input = $this->xml->createElement('item');
            $this->form->appendChild($input);
            $selectID[$name] = $input;
            #$input->setIdAttribute  ( $id, 1 );
            $attrName = 'id'; $attrVal = $id; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'name'; $attrVal = $name; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'className'; $attrVal = $className; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'type'; $attrVal = $type; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'size'; $attrVal = $size; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));

            $attrName = 'selval'; $attrVal = $defaultVal; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'defaultText'; $attrVal = $defaultText; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
#            $attrName = 'defaultVal'; $attrVal = $this->defaultVal; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));

            $label = $this->xml->createElement('label', $item->label);
            $input->appendChild($label);

            $value = $this->xml->createElement('value', $item->value);
            $input->appendChild($value);
        break;

        case 'option':
            $select = $selectID[$item->defaultText]; # $this->xml->getElementById();
            if ($select === NULL) $this->Log->addError('Не найден элемент "Выпадающий Список" '.$item->defaultText,__LINE__,__METHOD__);
            if ($select !== NULL) {

                if (!isset($optionID[$item->defaultText])) {
                  $options = $this->xml->createElement('options');
                  $select->appendChild($options);
                  $optionID[$item->defaultText] = $options;
                }  else $options = $optionID[$item->defaultText];

                $input = $this->xml->createElement('option', $item->label);
                $options->appendChild($input);
                $attrName = 'value'; $attrVal = $item->value; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            }
        break;
        case 'submit':
            $input = $this->xml->createElement('item');
            $this->form->appendChild($input);
            $attrName = 'id'; $attrVal = $id; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'name'; $attrVal = $name; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'className'; $attrVal = $className; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));
            $attrName = 'type'; $attrVal = $type; $eAttr = $this->xml->createAttribute($attrName); $attr = $input->appendChild($eAttr); $attr->appendChild($this->xml->createTextNode($attrVal));

            $value = $this->xml->createElement('value', $item->value);
            $input->appendChild($value);

        break;
        default: break;
      }
    }
  }

  public function getBody(DOMElement $DOMElement, $datatype = 'xml'){
    $this->buildBody($DOMElement);
    if ($datatype == 'xml') {
      return true;
    }
    if ($datatype == 'html') {

      $data = '';
      $pXSL = RIVC_ROOT.'layout/form.xsl';;
      $doc = $DOMElement->ownerDocument;
      #$doc = $DOMDocument;
      $xsl = new XSLTProcessor();
      $doc->load($pXSL);
      $xsl->importStyleSheet($doc);
      $res = $xsl->transformToDoc($this->xml);
      $data = $res->saveHTML();
      return $data;
    }
  }

  public function getInputs() {
    return $this->Collection;
  }

  public function addInputs (CFormCollection $Collection) {
    $cIterator = $Collection->getIterator();
    foreach ($cIterator as $item) {
      $this->Collection->add($item);
    }
    return true;
  }

}


?>