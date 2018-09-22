<?php

define('XSLT_TYPE_CONTAINER', 1);
define('XSLT_TYPE_MATCH', 2);
define('XSLT_TYPE_NAME', 3);

class TXSL extends TXML  {
	
	/**
	 * Тип Шаблона, автовызов, шиблон по выбору, шаблон именной
	 * @var int
	 */
	protected $typeXSL;
	/**
	 * Ссылка на главный шаблон
	 * @var DOMNode
	 */
	protected $mainTemplate;
	/**
	 * Имя главного шаблона (на вс. случай)
	 * @var string
	 */
	protected $mainTemplateName;
	/**
	 * Метод Match главного шаблога, только для типа (XSLT_TYPE_MATCH)
	 * @var string
	 */
	protected $mainTemplateMatch;
	
	/**
	 * Конструктор
	 * @param $mainTemplateName - Имя главного шаблона
	 * @param $type
	 * @return unknown_type
	 */	
	public function __construct($mainTemplateName, $mainTemplateMatch, $type) {
		$this->blockName = $mainTemplateName;
		$this->mainTemplateName = $mainTemplateName;
		$this->mainTemplateMatch = $mainTemplateMatch;
		$this->xml = new DOMDocument('1.0', 'utf-8');
		$this->xml->preserveWhiteSpace = false;
		$this->xsl->substituteEntities = false;
		$this->xml->formatOutput   = false;
		
		$this->root = $this->xml->createElement('xsl:stylesheet');
		$this->xml->appendChild($this->root);
		$this->addAttr('version','1.0',$this->root);
		$this->addAttr('xmlns:xsl','"http://www.w3.org/1999/XSL/Transform',$this->root);
		
		$this->mainTemplate = $this->xml->createElement('xsl:template');
		$this->root->appendChild($this->mainTemplate);
		
		switch ($type) {
			case XSLT_TYPE_CONTAINER: 				
				$this->addAttr('match','container[@module = \''.$mainTemplateName.'\']',$this->mainTemplate);
				break; 
			case XSLT_TYPE_MATCH: 
				$this->addAttr('match',''.$this->mainTemplateMatch.'',$this->mainTemplate);
				break; 
			case XSLT_TYPE_NAME: 
				$this->addAttr('name',''.$this->mainTemplateName.'',$this->mainTemplate);
				break;
			default: break; 
		}			
	}
	
	public function addTemplate($name, $type) {}
	public function addCallTemplate($name, $type, $parentNode) {}
	public function addElement($name, $parentNode) {}
	
}
?>