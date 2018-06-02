<?php
//error_reporting(true);
require 'vendor/autoload.php';
use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Finding\Services;
use \DTS\eBaySDK\Finding\Types;

// Create the service object.
$service = new Services\FindingService(array(
    'appId' => 'rockscri-7a27-4c7a-b095-e8cbe5ebb63b',
    'globalId' => Constants\GlobalIds::ES
));

// Create the request object.
$request = new Types\FindItemsAdvancedRequest();
/*$request->keywords="";
$request->categoryId= array("");*/
$itemFilter = new Types\itemFilter();
$itemFilter->name = "Seller";
$itemFilter->value = array("gig-games");
$request->itemFilter= array($itemFilter);

// Send the request to the service operation.
$response = $service->findItemsAdvanced($request);

// Output the result of calling the service operation.
echo "<pre>";
print_r($response);


foreach ($response->searchResult->item as $item) {
	echo  $item->title."<br>";
   
}

/*
Amazon ItemSearch
Amazon Retrieving Price Information
*/
///usr/local/bin/php -q /home/latingan/public_html/mercado-directo.com/index.php orders import_orders