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

class MageWorx_Tweaks_Helper_Output extends Mage_Core_Helper_Abstract
{
    public function productAttribute($method, $result, $params)
    {
        if ($params['attribute'] == 'description'){
            $processor = Mage::getModel('core/email_template_filter');
            $result = $processor->filter($result);
        }
        return $result;
    }
}