<?php
/**

 * Indies ANZ eGate Extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@magentocommerce.com so you can be sent a copy immediately.
 *
 * @category   Indies
 * @package    Indies_Anz
 * @author     Indies Services
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
 
class Indies_Anz_Model_Egate extends Mage_Payment_Model_Method_Abstract
{

    protected $_code  = 'anz_egate';

    protected $_isGateway               = true;
    protected $_canAuthorize            = true;
    protected $_canCapture              = true;
//    protected $_canCapturePartial       = false;
    protected $_canRefund               = false;
  //  protected $_canVoid                 = false;
   protected $_canUseInternal          = true;
    protected $_canUseCheckout          = true;
    protected $_canUseForMultishipping  = true;
    protected $_canSaveCc               = false;
	protected $rd123;

    const STATUS_APPROVED = 'Approved';
	const STATUS_PENDING = 'Pending';
    const TRANS_TYPE_CAPTURE    = 1;
    const TRANS_TYPE_REFUND     = 2;
    const TRANS_TYPE_AUTH       = 3;

    const PAYMENT_ACTION_AUTH_CAPTURE = 'authorize_capture';
    const PAYMENT_ACTION_AUTH = 'authorize';

    public function getGatewayUrl()
	{
        return 'https://migs.mastercard.com.au/vpcpay';
    }
	

    public function getDebug()
	{
        return Mage::getStoreConfig('payment/anz_egate/debug');
    }

    public function getLogPath()
	{
        return Mage::getBaseDir() . '/var/log/anz_egate.log';
    }

    /**
     * Returns the MerchantID as set in the configuration.
     * Note that if test mode is active then "TEST" will be prepended to the ID.
     */
    public function getMerchantId()
	{
        if(Mage::getStoreConfig('payment/anz_egate/test')) {
            return 'TEST' . Mage::getStoreConfig('payment/anz_egate/merchant_id');
        }
        else {
            return Mage::getStoreConfig('payment/anz_egate/merchant_id');
        }
    }
	
	public function getSecurity_Hash()
	{
		return Mage::getStoreConfig('payment/anz_egate/secure_hash');	
	}
	
	public function getSerialKey()
	{
		return Mage::getStoreConfig('payment/anz_egate/serial_key');
	}
	
    public function getAccessCode()
	{
        return Mage::getStoreConfig('payment/anz_egate/access_code');
    }

    public function getUser()
	{
        return Mage::getStoreConfig('payment/anz_egate/username');
    }

    public function getPassword()
	{
        return Mage::getStoreConfig('payment/anz_egate/password');
    }
	
    public function validate()
	{
        parent::validate();
        $paymentInfo = $this->getInfoInstance();
        if ($paymentInfo instanceof Mage_Sales_Model_Order_Payment) {
            $currency_code = $paymentInfo->getOrder()->getBaseCurrencyCode();
        } else {
            $currency_code = $paymentInfo->getQuote()->getBaseCurrencyCode();
        }
        return $this;
    }

    public function authorize(Varien_Object $payment, $amount)
    {
		
    }

    public function refund(Varien_Object $payment, $amount)
    {
        $this->setAmount($amount)->setPayment($payment);

        $result = $this->_call(self::TRANS_TYPE_REFUND, $payment);

        if($result === false) {
            $e = $this->getError();
            if (isset($e['message'])) {
                $message = Mage::helper('anz')->__('There has been an error processing your payment.') . ' ' . $e['message'];
            } else {
                $message = Mage::helper('anz')->__('There has been an error processing your payment. Please try later or contact us for help.');
            }
            Mage::throwException($message);
        }
        else {
            if ($result['vpc_TxnResponseCode'] === '0') {
                $payment->setStatus(self::STATUS_APPROVED)->setLastTransId($result['vpc_TransactionNo']);
            }
            else {
                Mage::throwException("Error code " . $result['vpc_TxnResponseCode'] . ": " . urldecode($result['vpc_Message']));
            }
        }
    }
	
	 public function capture(Varien_Object $payment, $amount)
     {
        // Ensure the transaction ID is always unique by including a time-based element
      
		$client_response='';
		$vpcURL='';
        $result = $this->_call(self::TRANS_TYPE_CAPTURE, $payment,$amount,$client_response,$vpcURL);
		
		if($result==false)
		{
			Mage::throwException($client_response);			
		}
		else
		{	
			$_SESSION['redirectURL']=$vpcURL;
		}
	 }

    protected function _call($type, Varien_Object $payment,$amount='',&$client_response='',&$vpcURL='')
    {
		//declaring a request array 
		$request = array();
		$request['Serial_Key'] = $this->getSerialKey();
		$request['Title'] = "PHP VPC 3-Party";
		$request['vpc_AccessCode'] = $this->getAccessCode();
		$request['vpc_Amount'] =  $amount*100;
		$request['vpc_SecureHash'] = $this->getSecurity_Hash();
		$request['vpc_Command'] = 'pay';
		$request['vpc_Locale'] = 'en';
		$request['vpc_Merchant'] = $this->getMerchantId();
		$request['vpc_MerchTxnRef'] = $payment->getOrder()->getIncrementId();
		$request['vpc_OrderInfo'] = $payment->getOrder()->getIncrementId();
		//$request['vpc_ReturnURL'] = "http://127.0.0.1/magento14/anz";
		$request['vpc_ReturnURL'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB) . "anz";
		//$request['vpc_TicketNo'] = '';
		//$request['vpc_TxSourceSubType'] = '';
		$request['vpc_Version'] = '1';
		
		ksort($request);
		$check = array('vpc_AccessCode','vpc_Command','vpc_SecureHash','vpc_Amount','vpc_OrderInfo','vpc_Merchant','vpc_ReturnURL');
		$flag=true;
		
		foreach($check as $d)
		{
			$checkoutHelper = Mage::helper('anz/data');
			
			if(!$checkoutHelper->canRun())
			{
				$message = base64_decode('VGhlIEFOWiBQYXltZW50IE1vZHVsZSBMaWNlbnNlIGdvdCBWaW9sYXRlZC4gUGxlYXNlIGNvbnRhY3QgdXMgb24gaHR0cDovL3d3dy5pbmRpZXN3ZWJzLmNvbS8=');
				
				$client_response = $message;
				$flag=false;
				return false;
    		}	
			if(empty($request[$d]))
			{
				Mage::log($d);
				$flag=false;
				$client_response = "Sorry We cant make your transaction Bcoz there is a lack of information";
				return false;
			}
		}
		
		if($flag)
		{
			//declare vpcURL
			$vpcURL = $this->getGatewayUrl() . '?';
			$appendAmp = 0;
			$md5HashData = $request['vpc_SecureHash'];
			foreach($request as $key => $value)
			{
				if($key!='vpc_SecureHash')
				{
					if ($appendAmp == 0) {
            			$vpcURL .= urlencode($key) . '=' . urlencode($value);
			            $appendAmp = 1;
			        } else {
			            $vpcURL .= '&' . urlencode($key) . "=" . urlencode($value);
			        }
			        $md5HashData .=   $value;
				}
			}
		    $vpcURL .= "&vpc_SecureHash=" . strtoupper(md5($md5HashData));
			
			return $request;
		}
		return false;
    }
	public function getOrderPlaceRedirectUrl()
	{
		$s = $_SESSION['redirectURL'];
		unset($_SESSION['redirectURL']);
		return $s;
	}
}


?>