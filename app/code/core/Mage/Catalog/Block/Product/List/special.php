<?php
$storeId = Mage::app()->getStore()->getId();
$resource = Mage::getSingleton('core/resource');
$read = $resource->getConnection('catalog_read');
$categoryProductTable = $resource->getTableName('catalog/category_product');
$productEntityIntTable = (string)Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_int';
$eavAttributeTable = $resource->getTableName('eav/attribute');
// Query database for special product
$select = $read->select()
->distinct()
->from(array('cp'=>$categoryProductTable),'cp.product_id')
->join(array('pei'=>$productEntityIntTable), 'pei.entity_id=cp.product_id', array())
->joinNatural(array('ea'=>$eavAttributeTable))
->where('pei.value=1')
->where('ea.attribute_code="special"');// Write your attribute code instead of Special
$rows = $read->fetchAll($select);
// Save all product Id which have the Special Product Yes
foreach($rows as $row)
{
$product[] =$row['product_id'];
} 

?>