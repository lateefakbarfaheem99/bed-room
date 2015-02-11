<?php
/**
 * Magento
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so we can send you a copy immediately.
 *
 * @category   Mage
 * @package    Mage_Adminhtml
 * @copyright  Copyright (c) 2004-2007 Irubin Consulting Inc. DBA Varien (http://www.varien.com)
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */


class JoomlArt_JmProductsSlider_Model_System_Config_Backend_JmProductsSlider_checkMode extends Mage_Core_Model_Config_Data
{

    protected function _beforeSave(){
    	$groups = $this->getData('groups');
    	$datas = $groups['joomlart_jmproductsslider'];
    	if($datas['fields']['mode']['value']=='category' && $datas['fields']['catsid']['value']==''){
    		throw new Exception(Mage::helper('joomlart_jmproductsslider')->__('Please enter list of Categories ID.'));
    	}
       	elseif($datas['fields']['mode']['value']=='category' && $datas['fields']['leading_product']['value']=='' && $datas['fields']['intro_product']['value']=='' ){
    		throw new Exception(Mage::helper('joomlart_jmproductsslider')->__('Please enter Leading or Intro number.'));
    	}
        return $this;
    }

}
