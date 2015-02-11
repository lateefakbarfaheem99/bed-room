<?php

class JoomlArt_JmProductsSlider_Model_System_Config_Source_ListDirection{
    public function toOptionArray()
    {
        return array(        	
            array('value'=>'left', 'label'=>Mage::helper('joomlart_jmproductsslider')->__('Left')),
            array('value'=>'right', 'label'=>Mage::helper('joomlart_jmproductsslider')->__('Right')),
        );
    }    
}