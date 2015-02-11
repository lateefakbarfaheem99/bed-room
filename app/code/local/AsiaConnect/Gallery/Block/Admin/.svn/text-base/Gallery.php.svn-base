<?php
class AsiaConnect_Gallery_Block_Admin_Gallery extends Mage_Adminhtml_Block_Widget_Grid_Container
{
  public function __construct()
  {
    $this->_controller = 'admin_gallery';
    $this->_blockGroup = 'gallery';
    $this->_headerText = Mage::helper('gallery')->__('Photo Manager');
    $this->_addButtonLabel = Mage::helper('gallery')->__('Add Photo');
	$this->_addButton('sort', array(
    	'label'     => Mage::helper('gallery')->__('Save order'),
    	'onclick'   => 'saveSort();',
    	'class'     => 'button',
    ));
    parent::__construct();
  }
}