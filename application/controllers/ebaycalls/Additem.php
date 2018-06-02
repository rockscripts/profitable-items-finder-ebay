<?php
//error_reporting(true);
/**
 * sources
 */
require_once 'setincludepath.php';
require_once 'AddItemRequestType.php';
require_once 'EbatNs_Environment.php';
/**

 * sample_GetItem
 * 
 * Sample call for GetItem
 * 
 * @package ebatns
 * @subpackage samples_trading
 * @author johann 
 * @copyright Copyright (c) 2008
 * @version $Id: sample_GetItem.php,v 1.90 2011-12-29 14:03:00 michaelcoslar Exp $
 * @access public 
 */
class Additem extends EbatNs_Environment
{
public $Array_itemsID;

   public function add_item($data)
    {
       return $this->dispatchCall($data);
    }

   /**
     * sample_GetItem::dispatchCall()
     * 
     * Dispatch the call
     *
     * @param array $params array of parameters for the eBay API call
     * 
     * @return boolean success
     */
    public function dispatchCall ($data)
    {
		
        $item = new ItemType();
		/*
		* The item is to be listed on the US site.
		*/
		//echo $data["Site"];
		$item->setSite("US");
		/*
		* The item will be a single quantity auction lasting for 10 days.
		* It will have a start price of $8.99 with a reserve of $9.99.
		* Buyers can use the 'Buy It Now' button to purchase the item for $10.99.
		*/

		$item->setListingType($data["ListingType"]);
		$item->setListingDuration($data["ListingDuration"]);
		$item->setQuantity($data["Quantity"]);
		$item->setStartPrice($this->new_amount_type($data["StartPrice"],$data["Currency"]));
		//$item->setReservePrice($this->new_amount_type(9.99,'USD'));
		//$item->setBuyItNowPrice($this->new_amount_type(10.99,'USD'));
		/*
		* Provide a title and description and other information such as the 
		* item's location.
		*/
		$item->setTitle('EbatNS Example Item 2013-03-29');
		$item->setDescription('<h1>EbatNS Example Item</h1><p>Listed using the EbatNS</p>');
		//$item->setSKU('ABCD-0001');
		$item->setCurrency($data["Currency"]);
		$item->setCountry($data["Country"]);
		$item->setLocation($data["Location"]);
		$item->setPostalCode('630004');
		/*
		* Display a picture with the item.
		*/
		$picture = new PictureDetailsType();
		$picture->setGalleryType('Gallery');
		$picture->setPictureURL('http://i.ebayimg.com/00/s/NTAwWDUwMA==/z/d-8AAOSwEetV-sig/$_1.JPG');
		$item->setPictureDetails($picture);
		/*
		* Item will be listed in the Books > Audiobooks category.
		*/
		$primaryCategory = new CategoryType();
		$primaryCategory->setCategoryID($data["PrimaryCategoryID"]);
		$item->setPrimaryCategory($primaryCategory);
		/*
		* The item's condition is 'Brand New'.
		*/
		$item->setConditionID(1000);
		/* 
		* Setup the item specifics.
		* Because we are listing in the Books > Audiobooks category we will use the following specifics.
		* Subject: Fiction & Literature
		* Topic: Fantasy
		* Format: MP3 CD
		* Length: Unabridged
		* Language: English
		* Country of Manufacture: United States
		*/ 
		$itemSpecifics = new NameValueListArrayType();
		$specific = new NameValueListType();
		$specific->setName('Platform');
		$specific->setValue('PC');
		$itemSpecifics->setNameValueList($specific, 0);
		$specific = new NameValueListType();
		$specific->setName('Topic');
		$specific->setValue('Fantasy');
		$itemSpecifics->setNameValueList($specific, 1);
		$specific = new NameValueListType();
		$specific->setName('Format');
		$specific->setValue('Download');
		$itemSpecifics->setNameValueList($specific, 2);
		$specific = new NameValueListType();
		$specific->setName('Length');
		$specific->setValue('Unabridged');
		$itemSpecifics->setNameValueList($specific, 3);
		$specific = new NameValueListType();
		$specific->setName('Language');
		$specific->setValue('English');
		$itemSpecifics->setNameValueList($specific, 4);
		$specific = new NameValueListType();
		$specific->setName('Country of Manufacture');
		$specific->setValue('United States');
		$itemSpecifics->setNameValueList($specific, 5);
		$item->setItemSpecifics($itemSpecifics);
		/*
		* Buyers can use one of two payment methods when purchasing the item.
		* Visa or Master Card  
		* PayPal
		* The item will be dispatched within 3 business days once payment has cleared.
		*/
		$item->setPaymentMethods('PayPal', 0);
		$item->setPayPalEmailAddress($data["PaymenteMail"]);
		$item->setDispatchTimeMax($data["DispatchTimeMax"]);

		/*
		* Setting up the shipping details.
		* We will use Flat shipping for both domestic and international.
		*/
		$shippingDetails = new ShippingDetailsType();
		$shippingDetails->setShippingType('Flat');
		$shippingDetails->setPaymentInstructions('Praesent sagittis ornare.');
		/*
		* Create our first domestic shipping option.
		* Offer the Economy Shipping (1-10 business days) service with free shipping.
		*/
		$shippingService = new ShippingServiceOptionsType(); 
		$shippingService->setShippingServicePriority(1);
		$shippingService->setShippingService('Other');
		$shippingService->setShippingServiceCost($this->new_amount_type(0.00,'USD'));
		$shippingService->setShippingServiceAdditionalCost($this->new_amount_type(0.00,'USD'));
		$shippingService->setFreeShipping(true);
		$shippingDetails->setShippingServiceOptions($shippingService, 0);
		/*
		* Create our second domestic shipping option.
		* Offer the USPS Parcel Post (2-8 business days) service for $2.00.
		*/
		/*$shippingService = new ShippingServiceOptionsType(); 
		$shippingService->setShippingServicePriority(2);
		$shippingService->setShippingService('USPSParcel');
		$shippingService->setShippingServiceCost(new_amount_type(2.00,'USD'));
		$shippingDetails->setShippingServiceOptions($shippingService, 1);*/
		/* 
		* Create our first international shipping option.
		* Offer the USPS First Class Mail International service for $3.00.
		* The item can be shipped Worldwide with this service.
		*/
		$shippingService = new InternationalShippingServiceOptionsType();
		$shippingService->setShippingServicePriority(1);
		$shippingService->setShippingService('UPSWorldWideExpressPlus');
		$shippingService->setShippingServiceCost($this->new_amount_type(0.00,'USD'));
		
		$shippingService->setShipToLocation('Worldwide');
		$shippingDetails->setInternationalShippingServiceOption($shippingService, 0);
		/* 
		* Create our second international shipping option.
		* Offer the USPS Priority Mail International (6-10 business days) service for $4.00.
		* The item will only be shipped to the following locations with this service.
		* N. and S. America 
		* Canada
		* Australia 
		* Europe 
		* Japan
		*/
		/*$shippingService = new InternationalShippingServiceOptionsType();
		$shippingService->setShippingServicePriority(2);
		$shippingService->setShippingService('USPSPriorityMailInternational');
		$shippingService->setShippingServiceCost(new_amount_type(4.00,'USD'));
		$shippingService->setShipToLocation('Americas');
		$shippingService->setShipToLocation('CA');
		$shippingService->setShipToLocation('AU');
		$shippingService->setShipToLocation('Europe');
		$shippingService->setShipToLocation('JP');
		$shippingDetails->setInternationalShippingServiceOption($shippingService, 1);*/
		/*
		* Regardless of which shipping service the buyer chooses the item will not
		* be shipped to the following locations.
		* Alaska/Hawaii
		* US Protectorates
		* APO/FPO
		* Algeria
		* Azerbaijan Republic 
		*/
		/*shippingDetails->setExcludeShipToLocation('Alaska/Hawaii');
		$shippingDetails->setExcludeShipToLocation('US Protectorates');
		$shippingDetails->setExcludeShipToLocation('APO/FPO');
		$shippingDetails->setExcludeShipToLocation('DZ');
		$shippingDetails->setExcludeShipToLocation('AZ');*/
		/*
		* Sales Tax details.
		* %5 Alabama state. 
		* Will also be applied to any shipping and handling costs.
		*/
		$salesTax = new SalesTaxType();
		//$salesTax->setSalesTaxState('AL');
		$salesTax->setSalesTaxPercent('0');
		$salesTax->setShippingIncludedInTax(true);
		$shippingDetails->setSalesTax($salesTax);
		$item->setShippingDetails($shippingDetails);
		/*
		* ShipToRegistrationCountry must be true to have the ExcludeShipToLocation settings applied to the item.
		*/
		$buyerRequirments = new BuyerRequirementDetailsType();
		$buyerRequirments->setShipToRegistrationCountry(true);
		$item->setBuyerRequirementDetails($buyerRequirements);
		/*
		* The return policy is,
		* A refund will be given as money back.
		* The buyer will have 14 days in which to contact the seller after receiving the item.
		* The buyer will pay the return shipping cost.
		*/
		$returnPolicy = new ReturnPolicyType();
		$returnPolicy->setReturnsAcceptedOption('ReturnsAccepted');
		$returnPolicy->setRefundOption('MoneyBack');
		$returnPolicy->setReturnsWithinOption('Days_14');
		$returnPolicy->setShippingCostPaidByOption('Buyer');
		$returnPolicy->setDescription('Nulla odio diam, varius luctus augue.');
		$item->setReturnPolicy($returnPolicy);
		
		$req = new AddItemRequestType();
$req->Item = $item;
$res = $this->proxy->AddItem($req);
echo "<pre>";
print_r($res);
    }
	function new_amount_type($value, $currency)
		{ 
		$amountType = new AmountType();
		$amountType->setTypeValue($value);
		$amountType->setTypeAttribute('currencyID', $currency);

		return $amountType;
		}

}


?>

