<?php
class Cobay_ConfigurableAttrSet_Block_Catalog_Product extends Mage_Adminhtml_Block_Catalog_Product {

    protected function _prepareLayout(){
        //NEW CODE BLOCK START
        $this->addButton('add_new_attr', array(
            'label'  	=> Mage::helper('configurableattrset')->__('Quick AttributeSet for Configurable Product'),
            //'onclick' => "window.open('{$this->getUrl('configurableattrset/index/index')}', 'gu_chat', 'width=700,height=600')",
			'onclick'	=>"showCompare('{$this->getUrl('configurableattrset/index/index')}')",
            'class'   	=> 'add'
        ));
		//NEW CODE BLOCK END
		
		return parent::_prepareLayout();
    }
    
}