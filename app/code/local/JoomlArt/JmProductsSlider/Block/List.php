
<?php
/**
* Jmtabs Chromes View block
*
* @codepool   Local
* @category   JoomlArt
* @package    JoomlArt_Jmtabs
* @module     Jmtabs
*/

class JoomlArt_JmProductsSlider_Block_List extends Mage_Catalog_Block_Product_List 
{
	var $_config = '';
	
	public function __construct($attributes = array()){
		$helper =  Mage::helper('joomlart_jmproductsslider/data');
		
		if(!$helper->get('show', $attributes)) return;
		
		parent::__construct();
		
		$this->_config['mode'] = $helper->get('mode', $attributes);
		$this->_config['title'] = $helper->get('title', $attributes);
		$this->_config['height'] = $helper->get('height', $attributes);
		$this->_config['width'] = $helper->get('width', $attributes);
		
		//$this->_config['catid'] = $helper->get('catid', $attributes);
		
		$this->_config['qty'] = $helper->get('quanlity', $attributes);
		$this->_config['qty'] = $this->_config['qty']>0?$this->_config['qty']:8;	
		
		$this->_config['number_items'] = $helper->get('number_items', $attributes);
		$this->_config['number_items'] = $this->_config['number_items']>0?$this->_config['number_items']:4;	
			
		$this->_config['show_price'] = $helper->get('show_price', $attributes);
		
		$this->_config['show_cart'] = $helper->get('show_cart', $attributes);
		//$this->_config['direction'] = $helper->get('direction', $attributes);
		
		$this->_config['delaytime'] = $helper->get('delaytime', $attributes);
		$this->_config['autorun'] = $helper->get('autorun', $attributes);
		if(!$this->_config['delaytime']) $this->_config['autorun'] =0;
		$this->_config['animationtime'] = $helper->get('animationtime', $attributes);
		$this->_config['attributename'] = $helper->get('attributename', $attributes);
		$this->_config['attributevalue'] = $helper->get('attributevalue', $attributes);
					
		$this->setProductCollection($this->getCategory());
	}
		
	function _toHtml() {		
		$listall = $this->getListProducts();
		
		$this->assign('listall', $listall);		
		$this->assign('configs', $this->_config);
		
		if(!isset($this->_config['template']) || $this->_config['template']==''){
			$this->_config['template'] = 'joomlart/jmproductsslider/list.phtml';
		}
		
		$this->setTemplate($this->_config['template']);				

        return parent::_toHtml();	
	}			
		
	function getListProducts(){
		$listall = null;
		switch ($this->_config['mode']){
			case 'latest':				
				$listall = $this->getListBestBuyProducts( 'updated_at', 'desc');				
				break;
			case 'feature':
				break;
			case 'best_buy':
				$listall = $this->getListBestBuyProducts();
				break;
			case 'most_viewed':
				$listall = $this->getListMostViewedProducts();
				break;
			case 'most_reviewed':
				$listall = $this->getListTopRatedProducts('reviews_count');
				break;
			case 'top_rated':
				$listall = $this->getListTopRatedProducts();
				break;	
			case 'attribute':			
				$listall = $this->getListFeaturedProduct();
				break;	
			case 'category':
				$listall = $this->getListProductbyCatsID();
				break;				
		}

		return $listall;
	}	
	
	function getListTopRatedProducts($orderfeild='rating_summary', $order='desc', $perPage=NULL, $currentPage=1){
		$list = null;
		if($perPage === NULL) $perPage	= (int) $this->_config['qty'];
		$storeId = Mage::app()->getStore()->getId();

        $entityCondition = '_reviewed_order_table.entity_id = e.entity_id';
		
		$products = Mage::getResourceModel('catalog/product_collection')
					->setStoreId($storeId)
					->addAttributeToSelect('*')
					->addStoreFilter($storeId);
					
		$products->getSelect()->joinLeft(
			                array('_reviewed_order_table'=>$products->getTable('review_entity_summary')),
			                		"_reviewed_order_table.store_id=$storeId AND _reviewed_order_table.entity_pk_value=e.entity_id",
			                array()
			            );
			
		$products->getSelect()->order("_reviewed_order_table.$orderfeild $order");	
        $products->getSelect()->group('e.entity_id');		
        		
		$products->setPageSize($perPage)->setCurPage($currentPage);
		
		$this->setProductCollection($products);
			
		if (($_products = $this->getProductCollection()) && $_products->getSize()){
			$list = $products;
		}	
		
		return $list;		
	}		
	
	function getListMostViewedProducts($perPage=NULL, $currentPage=1){									
		/* 
			Always set de $perPage, by template or by config 
			if $perPage eq 0 (zero) not limit the list
		*/
		if($perPage === NULL) $perPage	= (int) $this->_config['qty'];		
		/*
			Show all the product list in the current store			
		*/
		$storeId = Mage::app()->getStore()->getStoreId();
        $this->setStoreId($storeId);
       
        $this->_productCollection = Mage::getResourceModel('reports/product_collection');
        
		$this->_productCollection = $this->_productCollection->addViewsCount(); 
               
        $this->_productCollection = $this->_productCollection->addAttributeToSelect('*')
                        ->setStoreId($storeId)
                        ->addStoreFilter($storeId)
                        ->setPageSize($perPage);       
        return $this->_productCollection;	
	}
	
	function getListBestBuyProducts($fieldorder='ordered_qty', $order='desc', $product_ids='', $perPage=NULL, $currentPage=1){							
		$list = null;				
		/* 
			Always set de $perPage, by template or by config 
			if $perPage eq 0 (zero) not limit the list
		*/
		if($perPage === NULL) $perPage	= (int) $this->_config['qty'];
		/*
			Show all the product list in the current store
			order by ordered_qty, showing the bestsellers first
		*/
		$storeId = Mage::app()->getStore()->getId();
		$products = Mage::getResourceModel('catalog/product_collection')
					->setStoreId($storeId)
					->addAttributeToSelect('*')
					->addStoreFilter($storeId)
					->setOrder($fieldorder, $order);	
				
		if ($product_ids) {
			$products->getSelect()->where("e.entity_id in ($product_ids)");
					
		}

		/*
			Filter list of product showing only the active and 
			visible product
		*/
		Mage::getSingleton('catalog/product_visibility')->addVisibleInCatalogFilterToCollection($products);
		Mage::getSingleton('catalog/product_status')->addVisibleFilterToCollection($products);

		$products->setPageSize($perPage)->setCurPage($currentPage);
		
		$this->setProductCollection($products);
			
		if (($_products = $this->getProductCollection()) && $_products->getSize()){
			$list = $_products;
		}	
		
		return $list;
	}

	function getListFeaturedProduct(){

		$list = null;	
// instantiate database connection object
#
        
#
        $resource = Mage::getSingleton('core/resource');
#
        $read = $resource->getConnection('catalog_read');
#
        $categoryProductTable = $resource->getTableName('catalog/category_product');

#
        $productEntityIntTable = (string)Mage::getConfig()->getTablePrefix() . 'catalog_product_entity_int';
#
        $eavAttributeTable = $resource->getTableName('eav/attribute');
#
   
#
        // Query database for featured product
#
        $select = $read->select('cp.product_id')
#
                       ->from(array('cp'=>$categoryProductTable))
#
                       ->join(array('pei'=>$productEntityIntTable), 'pei.entity_id=cp.product_id', array())
#
                       ->joinNatural(array('ea'=>$eavAttributeTable))
#
                       ->where("pei.value='{$this->_config['attributevalue']}'")
#
                       ->where('ea.attribute_code="featured"');       
#
        $rows = $read->fetchAll($select);
#		
		$product_ids = array();
		
		if ($rows) {
			foreach ($rows as $row){
				$product_ids[] = $row['product_id'];
			}
			$product_ids = implode(',', $product_ids);
			$list = $this->getListBestBuyProducts( 'updated_at', 'desc', $product_ids);
		}		
				       
        return $list;
	}
	
	function getListProductbyCatsID($perPage=NULL, $currentPage=1){
		if (!$this->_config['catsid']) return ;
		
		$list = array();
		
		$layer = Mage::getSingleton('catalog/layer');
		
		$categories_id = explode(',', $this->_config['catsid']);
		
		$k = 0;
		foreach ($categories_id as $catid){
			$catid = (int)trim($catid);
			if($catid){
				$category = Mage::getModel('catalog/category')->load($catid);
				
		        if ($category->getId()) {
		            $origCategory = $layer->getCurrentCategory();
		            $layer->setCurrentCategory($category);
		            
		            $product_collection = $layer->getProductCollection();
		            Mage::dispatchEvent('catalog_block_product_list_collection', array(
			            'collection'=>$product_collection,
			        ));
			
			        $product_collection->load();
			        Mage::getModel('review/review')->appendSummary($product_collection);
        
		            $list[$k]['items'] 	= $product_collection;
		            $list[$k]['category']  = $category;
		            $k++;		            
		        }
			}
		}
		
		return $list;
	}
			
	function set($show=1, $mode='', $title='', $height=100, $width=100, $quanlity=9, $number_items=4, $show_price="", $show_cart='', $autorun=1, $delaytime=1000, $animationtime=800, $attributename='', $attributevalue='', $template=''){
		if(!$mode || !$show){
			return ;
		}
		
		if($mode) $this->_config['mode'] = $mode;
		if($title) $this->_config['title'] = $title;
		if($height) $this->_config['height'] = $height;
		if($width) $this->_config['width'] = $width;
		if($template!='') 	$this->_config['template'] = $template;
		if($quanlity)		$this->_config['qty'] = $quanlity;
		if($number_items)		$this->_config['number_items'] = $number_items;
		if($show_price)		$this->_config['show_price'] = $show_price;
		if($show_cart)		$this->_config['show_cart'] = $show_cart;
		if($autorun)		$this->_config['autorun'] = $autorun;
		if($delaytime)		$this->_config['delaytime'] = $delaytime;
		if($animationtime)		$this->_config['animationtime'] = $animationtime;
		if($attributename)		$this->_config['attributename'] = $attributename;
		if($attributevalue)		$this->_config['attributevalue'] = $attributevalue;			
		if($template)		$this->_config['template'] = $template;							
	}	
}