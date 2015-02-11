<?php
/**
 * Paytype Newslider Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the PayType EULA (End-user license
 * agreement) that is bundled with this package in the file license.txt.
 * You may not give, sell, distribute, sub-license, rent, lease or lend
 * any portion of the Software or Documentation to anyone. 
 *
 * @category   Paytype
 * @package    Paytype_Newslider
 * @author     M.Rivas
 * @copyright  Copyright (c) 2009 Paytype SL (http://magentomodules.paytype.com)
 * @license    End-user license Agreement
 */
class Paytype_Newslider_Block_Newslider extends Mage_Catalog_Block_Product_List
{
    protected $_productsCount = null;


    protected function _beforeToHtml()
    {
        $todayDate  = Mage::app()->getLocale()->date()->toString(Varien_Date::DATETIME_INTERNAL_FORMAT);
        $collection = $this->_addProductAttributesAndPrices(Mage::getResourceModel('catalog/product_collection'))
            ->addStoreFilter()
            ->addAttributeToFilter('news_from_date', array('date' => true, 'to' => $todayDate))
            ->addAttributeToFilter('news_to_date', array('or'=> array(
                0 => array('date' => true, 'from' => $todayDate),
                1 => array('is' => new Zend_Db_Expr('null')))
            ), 'left')
            ->addAttributeToSort('news_from_date', 'desc')
	    ->setOrder('created_at', 'desc')
            ->setPageSize($this->getProductsCount())
            ->setCurPage(1)
        ;
        $this->setProductCollection($collection);

        return parent::_beforeToHtml();
    }

    public function setProductsCount($count)
    {
        $this->_productsCount = $count;
        return $this;
    }

    public function getProductsCount()
    {
        if (null === $this->_productsCount) {
            $this->_productsCount = intval(Mage::getStoreConfig('admin/newslider/product_count'));
        }
        return $this->_productsCount;
    }
}