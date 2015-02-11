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
 
/**
 * Data helper
 */
class Indies_Anz_Helper_Data extends Mage_Core_Helper_Abstract
{
	private $temp;
	public function getDomain()
    {
        $domain = $_SERVER['SERVER_NAME'];
        $temp = explode('.', $domain);
        $exceptions = array(
            'co.uk',
            'com.au',
			'com.hk',
			'co.nz',
			'co.in'
            );

            $count = count($temp);
            $last = $temp[($count-2)] . '.' . $temp[($count-1)];
			
            if(in_array($last, $exceptions))	{
                $new_domain = $temp[($count-3)] . '.' . $temp[($count-2)] . '.' . $temp[($count-1)];
            }
            else	{
                $new_domain = $temp[($count-2)] . '.' . $temp[($count-1)];
            }
            return $new_domain;
    }

    public function checkEntry($domain, $serial)
    {
        $key = sha1(base64_decode('b25lc3RlcGNoZWNrb3V0'));
        if(sha1($key.$domain) == $serial)
		{
            return true;
        }
        return false;
    }

    public function canRun()
    {
        $temp = Mage::getStoreConfig('payment/anz_egate/serial_key',Mage::app()->getStore());
		
		$url = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_WEB);
		$parsedUrl = parse_url($url);
		$host = explode('.', $parsedUrl['host']);
		$subdomains = array_slice($host, 0, count($host) - 2 );
		
		if($subdomains[0] == 'test'){
			return true;
		}
		
		$domain = $_SERVER['SERVER_NAME'];
		if($domain == 'localhost' || $domain == '127.0.0.1'){
			return true;
		}
		
        $original = $this->checkEntry($_SERVER['SERVER_NAME'], $temp);
        $wildcard = $this->checkEntry($this->getDomain(), $temp);
		
        if(!$original && !$wildcard){
            return false;
        }
        return true;
    }
} 
