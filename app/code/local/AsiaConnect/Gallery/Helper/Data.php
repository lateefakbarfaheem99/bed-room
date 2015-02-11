<?php

class AsiaConnect_Gallery_Helper_Data extends Mage_Core_Helper_Abstract
{
public function checkEmailDuplicationAjaxUrl()
    {
        return $this->_getUrl('gallery/account/checkemailduplication');
    } 
}