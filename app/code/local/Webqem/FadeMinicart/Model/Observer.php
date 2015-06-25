<?php
class Webqem_FadeMinicart_Model_Observer {

    public function catchCartAdd() {
 
        $isAddToCart = 1;
        Mage::getSingleton('core/session')->setIsAddToCart($isAddToCart);

    }
}
?>