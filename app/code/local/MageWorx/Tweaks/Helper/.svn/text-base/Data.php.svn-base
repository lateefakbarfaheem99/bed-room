<?php
/**
 * MageWorx
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MageWorx EULA that is bundled with
 * this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.mageworx.com/LICENSE-1.0.html
 *
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageworx.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please refer to http://www.mageworx.com/ for more information
 * or send an email to sales@mageworx.com
 *
 * @category   MageWorx
 * @package    MageWorx_Tweaks
 * @copyright  Copyright (c) 2009 MageWorx (http://www.mageworx.com/)
 * @license    http://www.mageworx.com/LICENSE-1.0.html
 */

/**
 * Tweaks extension
 *
 * @category   MageWorx
 * @package    MageWorx_Tweaks
 * @author     MageWorx Dev Team <dev@mageworx.com>
 */

class MageWorx_Tweaks_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_CHECKOUT_NEWSLETTER_ENABLE              = 'mageworx_tweaks/tweaks/onepage_checkout_newsletter_enable';
    const XML_CATEGORY_SINGLE_PRODUCT_REDIRECT_ENABLE = 'mageworx_tweaks/tweaks/category_single_product_redirect_enable';
    const XML_PRODUCT_DESCRIPTION_TEMPLATES_ENABLE    = 'mageworx_tweaks/tweaks/product_description_templates_enable';
    const XML_ORDER_VIEW_PREVNEXT_BOTTONS_ENABLE      = 'mageworx_tweaks/tweaks/order_view_prevnext_bottons_enable';
    const XML_ORDER_BY_BESTSELLERS_ENABLE			  = 'mageworx_tweaks/tweaks/order_by_bestsellers_enable';
    const XML_ORDER_BY_NEWEST_PRODUCT_ENABLE		  =	'mageworx_tweaks/tweaks/order_by_newest_product_enable';
    const XML_ORDER_BY_PRICE_UP_ENABLE				  =	'mageworx_tweaks/tweaks/order_by_price_up_enable';
    const XML_ORDER_BY_PRICE_DOWN_ENABLE			  =	'mageworx_tweaks/tweaks/order_by_price_down_enable';
    const XML_ORDER_BY_AVERAGE_CUSTOMER_REVIEW_ENABLE =	'mageworx_tweaks/tweaks/order_by_average_customer_review_enable';
    
    const XML_CHECKOUT_NEWSLETTER_CHECKED          = 'mageworx_tweaks/checkout_newsletter/checked';
    const XML_CHECKOUT_NEWSLETTER_VISIBLE_GUEST	   = 'mageworx_tweaks/checkout_newsletter/visible_guest';
    const XML_CHECKOUT_NEWSLETTER_VISIBLE_REGISTER = 'mageworx_tweaks/checkout_newsletter/visible_register';

	public function isCategorySingleProductRedirectEnable()
	{
		 return Mage::getStoreConfigFlag(self::XML_CATEGORY_SINGLE_PRODUCT_REDIRECT_ENABLE);
	}
	

    public function isProductDescriptionTemplatesEnable()
    {
         return Mage::getStoreConfigFlag(self::XML_PRODUCT_DESCRIPTION_TEMPLATES_ENABLE);
    }

	public function isOrderViewPrevnextBottonsEnable()
	{
		return Mage::getStoreConfigFlag(self::XML_ORDER_VIEW_PREVNEXT_BOTTONS_ENABLE);
	}

    public function isOnepageCheckoutNewsletterEnabled()
    {
        return Mage::getStoreConfigFlag(self::XML_CHECKOUT_NEWSLETTER_ENABLE);
    }
    
    public function isOrderByBestsellersEnabled()
    {
    	return Mage::getStoreConfigFlag(self::XML_ORDER_BY_BESTSELLERS_ENABLE);	
    }
    
    public function isOrderByNewestProductEnabled()
    {
    	return Mage::getStoreConfigFlag(self::XML_ORDER_BY_NEWEST_PRODUCT_ENABLE);
    }
    
    public function isOrderByPriceUpEnabled()
    {
    	return Mage::getStoreConfigFlag(self::XML_ORDER_BY_PRICE_UP_ENABLE);
    }
    
    public function isOrderByPriceDownEnabled()
    {
    	return Mage::getStoreConfigFlag(self::XML_ORDER_BY_PRICE_DOWN_ENABLE);
    }
    
    public function isOrderByAverageCustomerReviewEnabled()
    {
    	return Mage::getStoreConfigFlag(self::XML_ORDER_BY_AVERAGE_CUSTOMER_REVIEW_ENABLE);
    }
    
    public function isNewsletterChecked()
    {
        return Mage::getStoreConfigFlag(self::XML_CHECKOUT_NEWSLETTER_CHECKED);
    }

    public function isNewsletterVisibleGuest()
    {
        return Mage::getStoreConfigFlag(self::XML_CHECKOUT_NEWSLETTER_VISIBLE_GUEST);
    }

    public function isNewsletterVisibleRegister()
    {
        return Mage::getStoreConfigFlag(self::XML_CHECKOUT_NEWSLETTER_VISIBLE_REGISTER);
    }
}