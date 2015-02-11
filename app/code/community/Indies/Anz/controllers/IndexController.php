<?php

class Indies_Anz_IndexController extends Mage_Core_Controller_Front_Action

{
    public function indexAction()
    {
		$Temp = $this->getRequest()->getparams();	
		Mage::log($Temp);
		$result = "Order is not placed successfully due to the <br>";
		
		if($Temp['vpc_TxnResponseCode']=='C')
		{
			switch ($Temp['vpc_TxnResponseCode']) 
			{
				case "C" : $result .= "transaction being cancelled."; break;
				default  : $result .= "Unable to be determined"; 
				Mage::log("Cancelled Order");
			}
			$message = $this->__($result);
			Mage::getSingleton('core/session')->addError($message); 
			$order =  Mage::getModel('sales/order')->loadByIncrementID($Temp['vpc_MerchTxnRef']);
			$order->setState(Mage_Sales_Model_Order::STATE_CANCELED, true)->save();
			$this->_redirect('checkout/cart');
		}
		elseif($Temp['vpc_TxnResponseCode']!=0 || $Temp['vpc_TxnResponseCode'] != '')
		{
			Mage::log("New Order");
			switch ($Temp['vpc_TxnResponseCode']) 
			{
				case "?" : $result .= "Transaction status is unknown"; break;
				case "1" : $result .= "Unknown Error"; break;
				case "2" : $result .= "Bank Declined Transaction"; break;
				case "3" : $result .= "No Reply from Bank"; break;
				case "4" : $result .= "Expired Card"; break;
				case "5" : $result .= "Insufficient funds"; break;
				case "6" : $result .= "Error Communicating with Bank"; break;
				case "7" : $result .= "Payment Server System Error"; break;
				case "8" : $result .= "Transaction Type Not Supported"; break;
				case "9" : $result .= "Bank declined transaction (Do not contact Bank)"; break;
				case "A" : $result .= "Transaction Aborted"; break;
				case "D" : $result .= "Deferred transaction has been received and is awaiting processing"; break;
				case "F" : $result .= "3D Secure Authentication failed"; break;
				case "I" : $result .= "Card Security Code verification failed"; break;
				case "L" : $result .= "Shopping Transaction Locked (Please try the transaction again later)"; break;
				case "N" : $result .= "Cardholder is not enrolled in Authentication scheme"; break;
				case "P" : $result .= "Transaction has been received by the Payment Adaptor and is being processed"; break;
				case "R" : $result .= "Transaction was not processed - Reached limit of retry attempts allowed"; break;
				case "S" : $result .= "Duplicate SessionID (OrderInfo)"; break;
				case "T" : $result .= "Address Verification Failed"; break;
				case "U" : $result .= "Card Security Code Failed"; break;
				case "V" : $result .= "Address Verification and Card Security Code Failed"; break;
				default  : $result .= "Unable to be determined";
			}
			if(isset($Temp['vpc_VerStatus']))
			{
				$result .= " And ";
				switch ($Temp['vpc_VerStatus']) 
				{
					case "Y"  : $result .= "The cardholder was successfully authenticated."; break;
					case "E"  : $result .= "The cardholder is not enrolled."; break;
					case "N"  : $result .= "The cardholder was not verified."; break;
					case "U"  : $result .= "The cardholder's Issuer was unable to authenticate due to some system error at the Issuer."; break;
					case "F"  : $result .= "There was an error in the format of the request from the merchant."; break;
					case "A"  : $result .= "Authentication of your Merchant ID and Password to the ACS Directory Failed."; break;
					case "D"  : $result .= "Error communicating with the Directory Server."; break;
					case "C"  : $result .= "The card type is not supported for authentication."; break;
					case "S"  : $result .= "The signature on the response received from the Issuer could not be validated."; break;
					case "P"  : $result .= "Error parsing input from Issuer."; break;
					case "I"  : $result .= "Internal Payment Server system error."; break;
					default   : $result .= "Unable to be determined"; break;
				}
			}
			/*$message = $this->__($result);
			Mage::getSingleton('core/session')->addError($message); 
			$order =  Mage::getModel('sales/order')->loadByIncrementID($Temp['vpc_MerchTxnRef']);
			$order->setState(Mage_Sales_Model_Order::STATE_NEW, true)->save();
			$this->_redirect('checkout/cart');
			*/
			$order =  Mage::getModel('sales/order')->loadByIncrementID($Temp['vpc_MerchTxnRef']);
			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
			try
			{
				$order->sendNewOrderEmail();
			}catch(Exception $e){}
			$this->_redirect('checkout/onepage/success');	
		}
		else
		{
			Mage::log("Processing Order");
			$order =  Mage::getModel('sales/order')->loadByIncrementID($Temp['vpc_MerchTxnRef']);
			$order->setState(Mage_Sales_Model_Order::STATE_PROCESSING, true)->save();
			try
			{
				$order->sendNewOrderEmail();
			}catch(Exception $e){}
			$this->_redirect('checkout/onepage/success');
		}
    }
}