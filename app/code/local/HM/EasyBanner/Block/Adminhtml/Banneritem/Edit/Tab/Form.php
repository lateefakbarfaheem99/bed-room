<?php

class HM_EasyBanner_Block_Adminhtml_Banneritem_Edit_Tab_Form extends Mage_Adminhtml_Block_Widget_Form
{
  protected function _prepareForm()
  {
      $form = new Varien_Data_Form();
      $this->setForm($form);
      $fieldset = $form->addFieldset('easybanneritem_form', array('legend'=>Mage::helper('easybanner')->__('Banner Item information')));
     
	  $banners = array(''=>'-- Select Banner --');
	  $collection = Mage::getModel('easybanner/banner')->getCollection();
	  foreach ($collection as $banner) {
		 $banners[$banner->getId()] = $banner->getTitle();
	  }

	  $fieldset->addField('banner_id', 'select', array(
          'label'     => Mage::helper('easybanner')->__('Banner'),
          'name'      => 'banner_id',
          'required'  => true,
          'values'    => $banners,
      ));

      $fieldset->addField('title', 'text', array(
          'label'     => Mage::helper('easybanner')->__('Title'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'title',
      ));
      
	  $fieldset->addField('banner_order', 'text', array(
          'label'     => Mage::helper('easybanner')->__('Banner Order'),
          'class'     => 'required-entry',
          'required'  => true,
          'name'      => 'banner_order',
      ));
      
      $fieldset->addField('image', 'image', array(
          'label'     => Mage::helper('easybanner')->__('Banner Image'),
          'required'  => false,
          'name'      => 'image',
	  ));

      $fieldset->addField('image_url', 'text', array(
          'label'     => Mage::helper('easybanner')->__('Image Url'),
          'required'  => false,
          'name'      => 'image_url',
	  ));

      $fieldset->addField('thumb_image', 'image', array(
          'label'     => Mage::helper('easybanner')->__('Thumnail Image'),
          'required'  => false,
          'name'      => 'thumb_image',
	  ));

      $fieldset->addField('thumb_image_url', 'text', array(
          'label'     => Mage::helper('easybanner')->__('Thumnail Url'),
          'required'  => false,
          'name'      => 'thumb_image_url',
	  ));
		
      $fieldset->addField('link_url', 'text', array(
          'label'     => Mage::helper('easybanner')->__('Link Url'),
          'required'  => false,
          'name'      => 'link_url',
	  ));

	  $fieldset->addField('status', 'select', array(
          'label'     => Mage::helper('easybanner')->__('Status'),
          'name'      => 'status',
          'values'    => array(
              array(
                  'value'     => 1,
                  'label'     => Mage::helper('easybanner')->__('Enabled'),
              ),

              array(
                  'value'     => 2,
                  'label'     => Mage::helper('easybanner')->__('Disabled'),
              ),
          ),
      ));
     
      $fieldset->addField('content', 'editor', array(
          'name'      => 'content',
          'label'     => Mage::helper('easybanner')->__('Content'),
          'title'     => Mage::helper('easybanner')->__('Content'),
          'style'     => 'width:600px; height:300px;',
          'wysiwyg'   => false,
          'required'  => false,
      ));
     
      if ( Mage::getSingleton('adminhtml/session')->getEasyBannerItemData() )
      {
          $form->setValues(Mage::getSingleton('adminhtml/session')->getEasyBannerItemData());
          Mage::getSingleton('adminhtml/session')->setEasyBannerItemData(null);
      } elseif ( Mage::registry('easybanneritem_data') ) {
          $form->setValues(Mage::registry('easybanneritem_data')->getData());
      }
      return parent::_prepareForm();
  }
}