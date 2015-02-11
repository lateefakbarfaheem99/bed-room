<?php

##########################################################################################
# In order to be able to use this script you need to join the merchant program depending on the country where your store is selling the products
#
# AUSTRALIA - 
# 
##########################################################################################

set_time_limit(0);
ignore_user_abort();
error_reporting(E_ALL^E_NOTICE);
$_SVR = array();

$path_include = "app/Mage.php";

// Include configuration file
if(!file_exists($path_include)) {
	exit('<HTML><HEAD><TITLE>404 Not Found</TITLE></HEAD><BODY><H1>Not Found</H1>Please ensure that this file is in the root directory, or make sure the path to the directory where the configure.php file is located is defined corectly above in $path_include variable</BODY></HTML>');
}
else {
	require_once $path_include;
}

// Get default store code
$default_store = Mage::app()->getStore();
$default_store_code = $default_store->getCode();

if (isset($_GET['show_stores']) && ($_GET['show_stores'] == 'on')) {
	$stores = Mage::app()->getStores();
	
	foreach ($stores as $i) {
		print $i->getCode() . "<br />";
	}
	exit;
}
if (isset($_GET['store']) && ($_GET['store'] != "")) {
	$store = $_GET['store'];
}
else {
	$store = $default_store_code;
}

Mage::app($store);

// Datafeed specific settings
$datafeed_separator = "|"; // Possible options are \t or |

// Define VAT value
$vat_value = 1.19; // 19%

// Description options - possible values on, off
if (isset($_GET['description'])){
	$show_description = ($_GET['description'] == "off") ? "off" : "on";
}
else {
	$show_description = "on";
}

if (isset($_GET['currency'])) {
	$currency_code = $_GET['currency'];
	$convert_price_to_currency = 1;
}

// Image options - possible values on, off
if (isset($_GET['image'])){
	$show_image = ($_GET['image'] == "off") ? "off" : "on";
}
else {
	$show_image = "on";
}

// Add VAT to prices
if (isset($_GET['add_vat'])){
	$add_vat = ($_GET['add_vat'] == "on") ? "on" : "off";
}
else {
	$add_vat = "off";
}

// Get current date
$datetime = date("Y-m-d G:i:s");

try{

	// Get store ID use this to filter products
	$storeId = Mage::app()->getStore()->getId(); 

	// Get shop currency
	$default_currency = Mage::getModel('directory/currency')->getConfigBaseCurrencies();
	$datafeed_default_currency = $default_currency[0];

	if (@$convert_price_to_currency == 1) {
		$currency_value_rate = Mage::getModel('directory/currency')->getCurrencyRates($datafeed_default_currency, $currency_code);
		$datafeed_currency = $currency_code;
	}
	else {
		$datafeed_currency = $datafeed_default_currency;
	}

	$CAT = getCategories();

	$GROUPED = array();

	// Get grouped products
	$grouped_products = Mage::getModel('catalog/product')->getCollection();
	$grouped_products->addAttributeToFilter('status', 1);//enabled
	$grouped_products->addAttributeToFilter('type_id', 'grouped');//catalog, search
	$grouped_products->addAttributeToSelect('*');
	$grouped_products->addStoreFilter($storeId); 
	$grouped_prodIds = $grouped_products->getAllIds();

	$grouped_product = Mage::getModel('catalog/product');
	
	foreach($grouped_prodIds as $grouped_productId) { 


		$grouped_product->load($grouped_productId);

		// Get linked products 
        $LINKED = array();
        
		$grouped_product->getLinkInstance()->useGroupedLinks();

        foreach ($grouped_product->getGroupedLinkCollection() as $_link) {
            $GROUPED[$_link->getLinkedProductId()] = $grouped_productId;
        }
	}

	// Get bundle products
	$grouped_products = Mage::getModel('catalog/product')->getCollection();
	$grouped_products->addAttributeToFilter('status', 1);//enabled
	$grouped_products->addAttributeToFilter('type_id', 'bundle');//catalog, search
	$grouped_products->addAttributeToSelect('*');
	$grouped_products->addStoreFilter($storeId);
	$grouped_prodIds = $grouped_products->getAllIds();
	
	$grouped_product = Mage::getModel('catalog/product');

	foreach($grouped_prodIds as $grouped_productId) { 

		$grouped_product->load($grouped_productId);


		// Get linked products 
        $LINKED = array();
		
		$grouped_product->getLinkInstance()->useGroupedLinks();

		foreach ($grouped_product->getRelatedLinkCollection() as $_link) {
			$GROUPED[$_link->getLinkedProductId()] = $grouped_productId;
		}
	
	}

	// Get configurable products
	$conf_products = Mage::getModel('catalog/product')->getCollection();
	$conf_products->addAttributeToFilter('status', 1);//enabled
	$conf_products->addAttributeToFilter('type_id', 'configurable');//catalog, search
	$conf_products->addAttributeToSelect('*');
	$conf_products->addStoreFilter($storeId);
	$conf_prodIds = $conf_products->getAllIds();

	$conf_product = Mage::getModel('catalog/product');
	
	foreach($conf_prodIds as $conf_productId) { 

		$conf_product->load($conf_productId);
		
		// Get linked products
        $LINKED = array();
        
		$grouped_product->getLinkInstance()->useRelatedLinks();

        foreach ($grouped_product->getRelatedLinkCollection() as $_link) {
            $GROUPED[$_link->getLinkedProductId()] = $conf_productId;
        }

	}
	
	// ******************************************************************************
	// Get the products
	$products = Mage::getModel('catalog/product')->getCollection();
	$products->addAttributeToFilter('status', 1);//enabled
	$products->addAttributeToFilter('visibility', 4);//catalog, search
	$products->addAttributeToFilter('type_id', 'simple');//catalog, search
	$products->addAttributeToSelect('*');
	$products->addStoreFilter($storeId);
	$prodIds = $products->getAllIds();
	
	// Array to check if product is already send
	$already_sent = array();

	foreach($prodIds as $productId) {

		// If we've sent this one, skip the rest - this is to ensure that we do not get duplicate products
		if (@$already_sent[$productId] == 1) continue;

		$PRODUCT = array();
		
		$PRODUCT = function_to_get_product_details($productId);
		
		if (isset($GROUPED[$productId])) {
			$GROUPED_PRODUCT = function_to_get_product_details($GROUPED[$productId]);
			if ($GROUPED_PRODUCT['category_name'] != "") {
				$PRODUCT['category_name'] = $GROUPED_PRODUCT['category_name'];
			}
			if ($GROUPED_PRODUCT['prod_desc'] != "") {
				$PRODUCT['prod_desc'] = $GROUPED_PRODUCT['prod_desc'];
			}
			if ($GROUPED_PRODUCT['prod_url'] != "") {
				$PRODUCT['prod_url'] = $GROUPED_PRODUCT['prod_url'];
			}
			if ($GROUPED_PRODUCT['prod_image'] != "") {
				$PRODUCT['prod_image'] = $GROUPED_PRODUCT['prod_image'];
			}
			unset($GROUPED_PRODUCT);
		}
		
		print $PRODUCT['category_name'] . $datafeed_separator . 
		$PRODUCT['manufacturer'] . $datafeed_separator . 
		$PRODUCT['prod_model'] . $datafeed_separator . 
		$PRODUCT['prod_id'] . $datafeed_separator . 
		$PRODUCT['prod_name'] . $datafeed_separator . 
		$PRODUCT['prod_desc'] . $datafeed_separator . 
		$PRODUCT['prod_url'] . $datafeed_separator . 
		$PRODUCT['prod_image'] . $datafeed_separator . 
		$PRODUCT['prod_price'] . $datafeed_separator . 
		$datafeed_currency . 
		"\n";

		$already_sent[$productId] = 1;
	}
	
}
catch(Exception $e){
	die($e->getMessage());
}

function function_to_get_product_details($product_id) {

	global $Mage, $show_description, $show_image, $add_vat, $datafeed_separator, $CAT;

	$RESULT = array();

	$product = Mage::getModel('catalog/product');

	$product->load($product_id);

	$prod_model = $product->getSku();	
	$prod_id = $product->getId();
	$prod_name = $product->getName();

	if ($show_description == "off"){
		$prod_desc = "";	   
	}
	else {
		$prod_desc = $product->getDescription();
	}

	$prod_attribute_id = $product->getAttributeSetId();

	$prod_url = function_to_get_product_url($product->getProductUrl());

	if ($show_image == "off"){
		$prod_image = "";		
	}
	else {
		$prod_image = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA).'catalog/product'.$product->getImage();
	}
	
	$prod_price = function_to_get_product_price($product);

	// Add VAT to prices
	if ($add_vat == "on") {
		$prod_price = $prod_price * $vat_value;
	}

	if ($product->getResource()->getAttribute('manufacturer')) {
		$manufacturer = $product->getResource()->getAttribute('manufacturer')->getFrontend()->getValue($product);
		if ($manufacturer == "No") {
			$manufacturer = "";
		}
	}
	else {
		$manufacturer = "";
	}

	$cat_ids = $product->getCategoryIds();
	$cat_level = 0;
	$cat_final_id = 0;
	$category_name = "";
	
	foreach ($cat_ids as $cat_id) {
		if ($CAT[$cat_id]['level'] > $cat_level) {
			$cat_level = $CAT[$cat_id]['level'];
			$cat_final_id = $cat_id;
		}
	}
	if ($cat_final_id > 0) {
		$category_name = $CAT[$cat_final_id]['name'];
	}
	else {
		$category_name = "Home";
	}

	$category_name = ereg_replace("Root Catalog", "Home", $category_name);

	// Clean product name (new lines)
	$prod_name = str_replace("\n", "", strip_tags($prod_name));
	$prod_name = str_replace("\r", "", strip_tags($prod_name));
	$prod_name = str_replace("\t", " ", strip_tags($prod_name));

	// Clean product names and descriptions (separators)
	if ($datafeed_separator == "\t") {
		// Continue... tabs were already removed
	}
	elseif ($datafeed_separator == "|") {
		$prod_name = str_replace("|", " ", strip_tags($prod_name));
		$prod_desc = str_replace("|", " ", $prod_desc);
	}

	// Clean product description (Replace new line with <BR>). In order to make sure the code does not contains other HTML code it might be a good ideea to strip_tags()
	$prod_desc = replace_not_in_tags("\n", "<BR />", $prod_desc);
	$prod_desc = str_replace("\n", " ", $prod_desc);		
	$prod_desc = str_replace("\r", "", $prod_desc);
	$prod_desc = str_replace("\t", " ", $prod_desc);

	if (strpos($prod_image, "no_selection")) {
		$prod_image = "";
	}

	$RESULT = array();
	
	$RESULT['category_name'] = $category_name;
	$RESULT['manufacturer'] = $manufacturer;
	$RESULT['prod_model'] = $prod_model;
	$RESULT['prod_id'] = $prod_id;
	$RESULT['prod_name'] = $prod_name;
	$RESULT['prod_desc'] = $prod_desc;
	$RESULT['prod_url'] = $prod_url;
	$RESULT['prod_image'] = $prod_image;
	$RESULT['prod_price'] = $prod_price;

	unset($product);
	
	return $RESULT;
}

// Function to return the Product URL based on your product ID
function function_to_get_product_url($product_url){
	$current_file_name = basename($_SERVER['REQUEST_URI']);
	$product_url = str_replace($current_file_name, "index.php", $product_url);
	$product_url = str_replace("myshopping_magento", "index", $product_url);
	
	// Eliminate id session 
	$pos_SID = strpos( $product_url, "?SID");
	if ($pos_SID) {
		$product_url = substr($product_url, 0, $pos_SID);
	}
	return $product_url;
	
}

function replace_not_in_tags($find_str, $replace_str, $string) {
	
	$find = array($find_str);
	$replace = array($replace_str);	
	preg_match_all('#[^>]+(?=<)|[^>]+$#', $string, $matches, PREG_SET_ORDER);	
	foreach ($matches as $val) {	
		if (trim($val[0]) != "") {
			$string = str_replace($val[0], str_replace($find, $replace, $val[0]), $string);
		}
	}	
	return $string;
}

function function_to_get_product_price($product) {

	$_taxHelper  = Mage::helper('tax');

	if ( $product->getSpecialPrice() && (date("Y-m-d G:i:s") > $product->getSpecialFromDate() || !$product->getSpecialFromDate()) &&  (date("Y-m-d G:i:s") < $product->getSpecialToDate() || !$product->getSpecialToDate())){
		$price = $product->getSpecialPrice();
	} else {
		$price = $product->getPrice();
	}

	$price = $_taxHelper->getPrice($product, $price, true);

	return $price;
}

// Get all categories whith breadcrumbs
function getCategories(){
	$storeId = Mage::app()->getStore()->getId(); 

	$collection = Mage::getModel('catalog/category')->getCollection()
		->setStoreId($storeId)
		->addAttributeToSelect("name");
	$catIds = $collection->getAllIds();

	$cat = Mage::getModel('catalog/category');

	$max_level = 0;

	foreach ($catIds as $catId) {
		$cat_single = $cat->load($catId);
		$level = $cat_single->getLevel();
		if ($level > $max_level) {
			$max_level = $level;
		}

		$CAT_TMP[$level][$catId]['name'] = $cat_single->getName();
		$CAT_TMP[$level][$catId]['childrens'] = $cat_single->getChildren();
	}

	$CAT = array();
	
	for ($k = 0; $k <= $max_level; $k++) {
		if (is_array($CAT_TMP[$k])) {
			foreach ($CAT_TMP[$k] as $i=>$v) {
				if (isset($CAT[$i]['name']) && ($CAT[$i]['name'] != "")) {
					$CAT[$i]['name'] .= " > " . $v['name'];
					$CAT[$i]['level'] = $k;
				}
				else {
					$CAT[$i]['name'] = $v['name'];
					$CAT[$i]['level'] = $k;
				}

				if (($v['name'] != "") && ($v['childrens'] != "")) {
					if (strpos($v['childrens'], ",")) {
						$children_ids = explode(",", $v['childrens']);
						foreach ($children_ids as $children) {
							if (isset($CAT[$children]['name']) && ($CAT[$children]['name'] != "")) {
								$CAT[$children]['name'] = $CAT[$i]['name'];
							}
							else {
								$CAT[$children]['name'] = $CAT[$i]['name'];
							}
						}
					}
					else {
						if (isset($CAT[$v['childrens']]['name']) && ($CAT[$v['childrens']]['name'] != "")) {
							$CAT[$v['childrens']]['name'] = $CAT[$i]['name'];
						}
						else {
							$CAT[$v['childrens']]['name'] = $CAT[$i]['name'];
						}
					}
				}
			}
		}
	}
	unset($collection);
	unset($CAT_TMP);
	return $CAT;
}

function print_array($_ARR) {
	print "<pre>";
	print_r($_ARR);
	print "</pre>";
}



?>