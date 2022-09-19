<?php
class Cobay_ConfigurableAttrSet_Block_Form extends Mage_Adminhtml_Block_Widget_Container {
	
	public $my_form = array();
	public $gdocs_user;
	public $gdocs_pwd;
	public $gdocs_sampleurl;
	public $gdocs_key;
	
    public function __construct() {
        parent::__construct();

		$this->gdocs_user		= Mage::getStoreConfig('catalog/configurable_attributeset/gdocs_user');
		$this->gdocs_pwd		= Mage::getStoreConfig('catalog/configurable_attributeset/gdocs_pwd');
		$this->gdocs_sampleurl	= Mage::getStoreConfig('catalog/configurable_attributeset/sampleurl');
		// https://docs.google.com/a/hadoos.com/spreadsheet/ccc?key=0AhTb-h2-n0X3dFAyTEk2clhnekIzYkl5RktkeDVoU2c#gid=0
		if(!empty($this->gdocs_sampleurl)):
			$sampleurl = $this->gdocs_sampleurl;
			$querystring = parse_url($sampleurl, PHP_URL_QUERY);
			$arr = explode("&", $querystring);
			foreach($arr as $item):
				$arr2 = explode("=", $item);
				if($arr2[0]!='key') continue;
				$this->gdocs_key = $arr2[1];
			endforeach;
		endif;
		
        $my_form = Mage::getSingleton('core/session')->getData('my_form');
		if(isset($my_form)):
			$this->my_form = $my_form;
		else:
			$this->my_form = array(
				'attr_set_name'			=> '',
				'attr_set_inherited'	=> '',
				'attr_set_group_name'	=> '',
				'attr_code'				=> '',
				'data'					=> '',
			);
		endif;
		
		$this->setTemplate('cobay/configurableattrset/form.phtml');
    }
    
}
