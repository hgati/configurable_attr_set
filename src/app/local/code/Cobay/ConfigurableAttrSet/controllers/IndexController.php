<?php 
class Cobay_ConfigurableAttrSet_IndexController extends Mage_Adminhtml_Controller_Action {

	public function indexAction() {
		$this->loadLayout()->renderLayout();
		return;
		/*
		$this->loadLayout();
        $this->_addContent(
        	$this->getLayout()->createBlock(
				'Mage_Core_Block_Template',
				'cobay_configurableattrset_form',
				array('template'=>'cobay/configurableattrset/form.phtml')
        	)
        );
        $this->renderLayout();
        return;

		$block = $this->getLayout()->createBlock(
			'Mage_Core_Block_Template',
			'cobay_configurableattrset_form',
			array('template'=>'cobay/configurableattrset/form.phtml')
		);
		$this->getResponse()->setBody( $block->toHtml() );
        */
    }

	public function createAction() {
		$pa = $this->getRequest()->getPost();
		//krumo($pa,'r'); exit;
		Mage::getSingleton('core/session')->setData('my_form', $pa);
		
		$attr_set_inherited	= $pa['attr_set_inherited']; $attr_set_inherited = trim($attr_set_inherited);
		if(empty($attr_set_inherited)){
			$this->_getSession()->addError($this->__("Please select the ATTRIBUTE SET inherited!"));
			$this->getResponse()->setRedirect($this->getUrl('*/*/', array()));
			return;		
		}
				
		$attr_set_name	= $pa['attr_set_name']; $attr_set_name = trim($attr_set_name);
		if(empty($attr_set_name)){
			$this->_getSession()->addError($this->__("Please input your own ATTRIBUTE SET NAME!"));
			$this->getResponse()->setRedirect($this->getUrl('*/*/', array()));
			return;
		}
		$entityTypeId = Mage::getModel('eav/entity')->setType('catalog_product')->getTypeId();
		$attributeSetId = Mage::getModel('eav/entity_attribute_set')->getCollection()->setEntityTypeFilter($entityTypeId)
		->addFieldToFilter('attribute_set_name', $attr_set_name)->getFirstItem()->getAttributeSetId();
		if(!empty($attributeSetId)){
			$this->_getSession()->addError($this->__("The attribute set name already exists."));
			$this->getResponse()->setRedirect($this->getUrl('*/*/', array()));
			return;
		}

		$attr_set_group_name	= $pa['attr_set_group_name']; $attr_set_group_name = trim($attr_set_group_name);
		if(empty($attr_set_group_name)){
			$this->_getSession()->addError($this->__("Please select the ATTRIBUTE SET GROUP NAME!"));
			$this->getResponse()->setRedirect($this->getUrl('*/*/', array()));
			return;		
		}		
		
		$attr_code	= $pa['attr_code']; $attr_code = trim($attr_code);
		if(empty($attr_code)){
			$this->_getSession()->addError($this->__("Please input your own ATTRIBUTE CODE!"));
			$this->getResponse()->setRedirect($this->getUrl('*/*/', array()));
			return;		
		}
		$attributeInfo = Mage::getResourceModel('eav/entity_attribute_collection')
		->setCodeFilter($attr_code)->getFirstItem()->getData();
		if(!empty($attributeInfo)){
			$this->_getSession()->addError($this->__("The attribute code already exists."));
			$this->getResponse()->setRedirect($this->getUrl('*/*/', array()));
			return;
		}
		
		$data = $pa['data']; $data = trim($data);
		if(empty($data)){
			$this->_getSession()->addError($this->__("Please input the DATA(Label & Options)!"));
			$this->getResponse()->setRedirect($this->getUrl('*/*/', array()));
			return;		
		}
		
		$header		= array();
		$row_store	= array();
		/*
		$label = array(
			0	=> "컬러 : 사이즈",
			1	=> "Color : Sixze",
			6	=> "Coluar : SSZIA",
			7	=> "xxxx",
			8	=> "ooo",
		);
		*/
		$row_label	= array(); //array(STORE_ID, LABEL)
		$row_option	= array('value'=>array(),'order'=>array());
		$buf_option	= array();
		/*
		$option = array(
			'value' => array(
				'any_option_name1' => array(0=>'apple', 1=>'micro', 6=>'teacher'),
				'any_option_name2' => array(0=>'apple2', 1=>'micro2', 6=>'teacher2'),
			)
		);
		*/
		
		$rows		= explode("\n", $data);
		$rows_len	= count($rows);
		if($rows_len===0){
			$this->_getSession()->addError($this->__("No Rows!"));
			$this->getResponse()->setRedirect($this->getUrl('*/*/', $pa));
			return;
		}
		
		//set store row		
		$row = $header = $rows[0];
		if(empty($row)) return; $row = trim($row);
		$cells = explode("\t", $row);
		foreach ($cells as $idx_cell => $cell):
			$row_store[$idx_cell] = $cell;
		endforeach;
		//krumo($row_store,'r'); exit;
		
		//set lable row
		$row = $rows[1];
		if(empty($row)) return; $row = trim($row);
		$cells = explode("\t", $row);
		foreach ($cells as $idx_cell => $cell):
			$cell = trim($cell); if(empty($cell)) continue;
			$store_id = Mage::app()->getStore( $row_store[$idx_cell] )->getId();
			$row_label[$store_id] = $cell;
		endforeach;
		//krumo($row_label,'r'); exit;
		
		//set option rows
		$cells = explode("\t", $header);
		foreach ($cells as $idx_cell => $cell):
			if(empty($cell)) continue; if(trim($cell)=='') continue;

			$buf_option[$idx_cell] = array();
			$buf_row_set = array(); $row_set_index = 0;
			for($i=2; $i < $rows_len; $i++):
				$row = $rows[$i]; $row = trim($row);
				if(empty($row)){ $row_set_index++; continue; }

				$cells2 = explode("\t", $row);
				$buf_option[$idx_cell][$row_set_index][] = $cells2[$idx_cell];
			endfor;
		endforeach;
		//krumo($buf_option,'r'); exit;
		
		foreach($buf_option as $k => $cell):
			//krumo($this->permutations($cell),'r');
			$arr = $this->permutations($cell);
			foreach($arr as $k2 => $arr2):
				$mixoption = implode(" & ", $arr2);
				/*
				$option = array(
					'value' => array(
						'any_option_name1' => array(0=>'apple', 1=>'micro', 6=>'teacher'),
						'any_option_name2' => array(0=>'apple2', 1=>'micro2', 6=>'teacher2'),
					),
					'order' => array(
						'any_option_name1' => '0',
						'any_option_name2' => '1',
					),
				);
				*/
				$store_id = Mage::app()->getStore( $row_store[$k] )->getId();
				$row_option['value']["any_option_name$k2"][$store_id] = $mixoption;
				$row_option['order']["any_option_name$k2"] = ($k2+1);
				//$row_option[$k] = $mixoption;
			endforeach;
		endforeach;
		//krumo($row_option,'r'); exit;

		//attribute code
		$attr_id = Mage::helper('common/attribute')->createAttributeForConfigurable(
			$attr_code, $row_label, $row_option
		);
		
		//attribute set
		$attr_set_info = Mage::helper('common/attribute')->createAttributeSet($attr_set_name, $attr_set_group_name, $attr_set_inherited);
		
		//assignning attribute to attribute-set
		$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
		$setup->addAttributeToSet($entityTypeId='catalog_product', $attr_set_info['id'], $attr_set_info['group_id'], $attr_id);

		$this->_getSession()->addSuccess($this->__("The Configurable Attributes has been created with attribute_id %s", $attr_id));
		$this->getResponse()->setRedirect($this->getUrl('*/*/', array('_current'=>true)));		
		//$this->getResponse()->setBody( $attr_id );
    }
    
    public function ajaxAction(){
    	$attr_set_id = $this->getRequest()->getParam('attr_set_id');
		
    	$attributeSetCollection = Mage::getModel('eav/entity_attribute_group')->getCollection()
		->addFieldToFilter('attribute_set_id', $attr_set_id)->setOrder('sort_order','asc')->load();
		$result = '<option value=""></option>';
		
		foreach ($attributeSetCollection as $id=>$attributeGroup):
			$result .= '<option value="'.$attributeGroup->getAttributeGroupName().'">'.$attributeGroup->getAttributeGroupName().'</option>';
		endforeach;
		
		echo $result;
    }
    
	private function permutations(array $array){
		switch (count($array)) {
			case 1:
				return $array[0];
				break;
			case 0:
				throw new InvalidArgumentException('Requires at least one array');
				break;
		}
		$a = array_shift($array);
		$b = $this->permutations($array);
	
		$return = array();
		foreach ($a as $v) {
			foreach ($b as $v2) {
				$return[] = array_merge(array($v), (array) $v2);
			}
		}
	
		return $return;
	}
        
}
