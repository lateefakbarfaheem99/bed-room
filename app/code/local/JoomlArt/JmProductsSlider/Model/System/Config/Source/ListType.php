<?php
/**
 * Luxe 
 * MostViewed module
 *
 * @category   Luxe
 * @package    Luxe_MostViewed
 */


class JoomlArt_JmProductsSlider_Model_System_Config_Source_ListType
{
    public function toOptionArray()
    {
        return array(
        	array('value'=>'', 'label'=>Mage::helper('joomlart_jmproductsslider')->__('-- Please select --')),
            array('value'=>'latest', 'label'=>Mage::helper('joomlart_jmproductsslider')->__('Latest')),
            array('value'=>'best_buy', 'label'=>Mage::helper('joomlart_jmproductsslider')->__('Best Buy')),
            array('value'=>'most_viewed', 'label'=>Mage::helper('joomlart_jmproductsslider')->__('Most Viewed')),
            array('value'=>'most_reviewed', 'label'=>Mage::helper('joomlart_jmproductsslider')->__('Most Reviewed')),
            array('value'=>'top_rated', 'label'=>Mage::helper('joomlart_jmproductsslider')->__('Top Rated')),
            array('value'=>'attribute', 'label'=>Mage::helper('joomlart_jmproductsslider')->__('Featured Product')),
        );
    }    
}
