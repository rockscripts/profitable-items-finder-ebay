<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(true);
setlocale(LC_MONETARY,"en_GB");
setlocale(LC_ALL, 'en_GB');
require_once 'ebaycalls/FindingAPI/vendor/autoload.php';
require_once 'ebaycalls/GetItemTransactions.php';
require_once 'ebaycalls/GetItem.php';
require_once 'ebaycalls/Additem.php';
/*require_once 'ebaycalls/GetOrders.php';*/

use \DTS\eBaySDK\Constants;
use \DTS\eBaySDK\Finding\Services;
use \DTS\eBaySDK\Finding\Types;

class Profit extends CI_Controller {
    
	private $service;

	public function __construct()
        {
           parent::__construct();
           $this->load->model('Ebay');
           $this->load->model('Importer');
           $this->load->model('Orders_model'); 
           $this->load->model('Profit_model');
           $this->load->model('Item_model'); 
           $this->load->model('Profitable_item_model'); 
           $this->load->helper('form');
           $this->service = new Services\FindingService(array(
            'appId' => 'rockscri-7a27-4c7a-b095-e8cbe5ebb63b',
            'globalId' => Constants\GlobalIds::US
            ));
        }

    public function list_sellers()
    {
        $data["profitable_sellers"]= $this->Importer->get_profitable_sellers(); 
        $this->template->load('profits/display_sellers',$data);	
    }
	public function index($message=null,$msn_type=null)
	{	
	    if($this->input->get("sellerID"))
		$data["userID"] = $userID = $this->input->get("sellerID");	
	    else
        $data["userID"] = $userID = "qualityzoneonline";
        	
	    $data["profitable_items"]= $this->Profit_model->get_profitable_items($userID);
        $data["profitable_seller"]= $this->Importer->get_profitable_seller(array("userID"=>$userID));
		if($message!=null):
		 $data["message"] = $message;
		 $data["message_type"] = $msn_type;
		endif;
	    $this->template->load('profits/display_items',$data);	  	
	}
	
	public function get_import_form()
	{		
	  $data = array();
	  $data["page"] = $this->input->post("page");
	  $data["display_items"] = $this->input->post("display_items");
	  $data["import_form"] = $this->template->ajax_load_view('profits/import_items_by_seller',$data, true); 
	  echo json_encode($data);
	}
	public function get_remove_by_qty_form()
	{		
	  $data = array();
	  $data["display_items"] = $this->input->post("display_items");
	  $data["get_remove_by_qty_form"] = $this->template->ajax_load_view('profits/get_remove_by_qty_form',$data, true); 
	  echo json_encode($data);
	}
	
	public function remove_by_qty()
	{
	 $qty = $this->input->post("qty");	
	 $this->Profit_model->remove_by_qty($qty);
	 $this->index("Items with sold in 30 days less than ".$qty." has beed deleted","success");
	 
	}
	public function remove_profitable_items()
	{		
	 $itemsIDs = $this->input->post("itemsIDs");	
	 
	 if(sizeof($itemsIDs)>0)
	 {
		 foreach($itemsIDs as $itemID)
		 {
			$this->Profit_model->remove_profitable_item($itemID); 
		 }
	 }	 	 
	}
	
	public function update_item_info()
	{
		if($this->input->post("itemID"))
		{
		 $response = $this->get_item($this->input->post("itemID"));
		 $item = $response->Item;
		 $json = array();
	 	 $profitable_item = array("Title"=>$item->Title,"itemID"=>$item->ItemID,"viewItemURL"=>$item->ListingDetails->ViewItemURL,"StartPrice"=>$item->StartPrice->value,"totalQTYPurchased"=> 0, "totalMoneyPurchased" => 0.00, "totalItemsSold" => 0);		   
			   if($this->Importer->profitable_item_exist($item->ItemID))
			   {
				 $profitable_item = $this->add_transaction_details($item->ItemID,$profitable_item);
			     $this->Importer->update_profitable_item($profitable_item);
				 $profitable_item["platform_commision"] = $platform_commision = number_format($profitable_item["StartPrice"]* 0.08,2,'.','');		 
				 /*Payment comission*/
				 $profitable_item["payment_commision"] = $payment_commision = number_format($profitable_item["StartPrice"] * 0.06,2,'.','');;
				 $profitable_item["full_comission"] = $full_comission = number_format($platform_commision + $payment_commision,2,'.','');;
				 $profitable_item["total_per_sale"] = $total_per_sale = number_format($profitable_item["StartPrice"] - $full_comission,2,'.','');;
				 $json["profitable_item"] = $profitable_item;
				 echo json_encode($json);
			   }	
		}		
	}
	
	public function update_pageImported()
	{		
		$this->Importer->update_profitable_seller(array("userID"=>$this->input->post("userID"),"pageImported"=>$this->input->post("page")));
	}
	public function import()
	{
		//echo $this->input->post("display_items");die;
		$pageToImport = $this->input->post("page");
		$userID = $this->input->post("userID")/*"gig-games"*/;//economy-games
		$request = new Types\FindItemsAdvancedRequest();
		$itemFilter = new Types\ItemFilter();
		$itemFilter->name = "Seller";
		$itemFilter->value = array($userID);
		$request->itemFilter= array($itemFilter);
		$request->paginationInput = new Types\PaginationInput();
		$request->paginationInput->entriesPerPage =(int) 200;
		if(!$this->input->post("page"))
			$pageToImport=1;
		$request->paginationInput->pageNumber = (int)$pageToImport;		
		$response = $this->service->findItemsAdvanced($request); 
		if($response->ack=="Failure")
		{
			$this->index($response->errorMessage->error[0]->message,"error");
		}
		if(!$this->Importer->exist_profitable_seller(array("userID"=>$userID)))
		 $this->Importer->import_profitable_seller(array("userID"=>$userID, "totalPages"=>$response->paginationOutput->totalPages,"totalEntries"=>$response->paginationOutput->totalEntries));
			
			for ($i=0;$i<$response->paginationOutput->entriesPerPage;$i++) 
			{
				$item = $response->searchResult->item[$i];
                                if($item->listingInfo->listingType!="Auction" or $item->listingInfo->listingType!="Chinese" or $item->listingInfo->listingType!="PersonalOffer" or $item->listingInfo->listingType!="AdType"):
                                    if($item->itemId!=null)
                                        {
                                            $item_full = $this->get_item($item->itemId);
                                            $ShippingServiceOptions = $this->get_ShippingServiceOptions_encoded($item_full);
                                            $InternationalShippingServiceOption = $this->get_InternationalShippingServiceOption_encoded($item_full);
                                            $ExcludeShipToLocation = $this->get_ExcludeShipToLocation_encoded($item_full);
                                            $ShipToLocations = $this->get_ShipToLocations_encoded($item_full);
                                            $ItemLocation = $this->get_ItemLocation($item_full);
                                            $profitable_item = array("Title"=>$item->title,"itemID"=>$item->itemId,"viewItemURL"=>$item->viewItemURL, "Currency" => $item_full->Item->Currency,"StartPrice"=>$item->sellingStatus->currentPrice->value,"totalQTYPurchased"=> 0, "totalMoneyPurchased" => 0.00, "totalItemsSold" => 0, "Quantity" => $item_full->Item->Quantity,"Location"=>$ItemLocation, "PrimaryCategory" =>$item_full->Item->PrimaryCategory->CategoryName,"ShippingServiceOptions"=>$ShippingServiceOptions,"InternationalShippingServiceOption"=>$InternationalShippingServiceOption,"ExcludeShipToLocation"=>$ExcludeShipToLocation,"ShipToLocations"=>$ShipToLocations, "userID"=>$userID);		   
                                            if(!$this->Importer->profitable_item_exist($item->itemId))
                                            {
                                                  $profitable_item = $this->add_transaction_details($item->itemId,$profitable_item); 
                                                  $this->Importer->import_profitable_item($profitable_item);                                            
                                                  $this->import_profitable_items_pictures($item_full);
                                            }	
                                        }
                                endif;							   		   			   			   
			}	
			 $profitable_seller = $this->Importer->get_profitable_seller(array("userID"=>$userID));
			   if($profitable_seller)
			   $this->Importer->update_profitable_seller(array("userID"=>$userID,"pageImported"=>$pageToImport)); 
       
	   if($this->input->post("display_items")=="true")
		  {
			$this->index();
          }			
       else{
           echo json_encode(array('totalPages'=>$response->paginationOutput->totalPages));
       }
	}
        public function get_ItemLocation($item)
        {
         return $item->Item->Location;   
        }
                
        public function get_ShippingServiceOptions_encoded($item)
        {
         $service_options = array();
         $service_option_single = array();
          
                foreach($item->Item->ShippingDetails->ShippingServiceOptions as $service_option):
                    $service_option_single["ServiceName"] = $service_option->ShippingService;
                    $service_option_single["ServiceCost"] = $service_option->ShippingServiceCost->value;
                    $service_option_single["ServiceAdditionalCost"] = $service_option->ShippingServiceAdditionalCost->value;
                    $service_option_single["ServicePriority"] = (int)$service_option->ShippingServicePriority;
                    $service_option_single["TimeMin"] = (int)$service_option->ShippingTimeMin;
                    $service_option_single["TimeMax"] = (int)$service_option->ShippingTimeMax;
                    $service_option_single["FreeShipping"] = (int)$service_option->FreeShipping;
                    $service_options[] = $service_option_single;
                endforeach;
          return base64_encode(serialize($service_options));
        }
         public function get_InternationalShippingServiceOption_encoded($item)
        {
          if(($item->Item->ShippingDetails->InternationalShippingServiceOption)!= null)
          {
         $international_service_options = array();
         $international_service_option_single = array();
         
                foreach($item->Item->ShippingDetails->InternationalShippingServiceOption as $international_service_option):
                    $international_service_option_single["ServiceName"] = $international_service_option->ShippingService;
                    $international_service_option_single["ServiceCost"] = $international_service_option->ShippingServiceCost->value;                    
                    $international_service_option_single["ServiceAdditionalCost"] = $international_service_option->ShippingServiceAdditionalCost->value;
                    $international_service_option_single["ServicePriority"] = (int)$international_service_option->ShippingServicePriority;
                    $international_service_option_single["ShipToLocation"] = (array)$international_service_option->ShipToLocation;                    
                    $international_service_options[] = $international_service_option_single;
                endforeach;                            
          return base64_encode(serialize($international_service_options));
        }
          else              
         return false;
          }
        public function get_ExcludeShipToLocation_encoded($item)
        { 
            if(($item->Item->ShippingDetails->ExcludeShipToLocation)!=null)
             return base64_encode(serialize($item->Item->ShippingDetails->ExcludeShipToLocation));
         else              
         return false;
         
        }
        public function get_ShipToLocations_encoded($item)
        {
          if(($item->Item->ShipToLocations)!=null)
             return base64_encode(serialize($item->Item->ShipToLocations));
         else              
         return false;  
        }
        public function import_profitable_items_pictures($item)
        {
            $array_pictures = $item->Item->PictureDetails->PictureURL;
            foreach($array_pictures as $picture_url):
                $this->Profitable_item_model->item_picture_import($item->Item->ItemID,$picture_url);
            endforeach;
        }
    
        public function export_to_woocommerce()
        {
       
    $seller_id = "hawk-goods";    
    $items = $this->Profit_model->get_profitable_items($seller_id);
    $handle = fopen('/home/latingan/public_html/mercado-directo.com/test.csv', 'w');
    fputcsv($handle, array(
        'SKU',
        'post_status',
        'post_title',
        'manage_stock', 
        'stock_status',
        'stock',
        'category',
        'regular_price',
        'visibility',
        'featured_image',
        'product_gallery',
        'cf_complex_array'
    ));
    $k=0;
    foreach ($items as $item) {
        if($k/*<2*/):
            $featured_image = "";
        $gallery = "";
        $item_pictures = $this->Profitable_item_model->get_profitable_item_pictures($item->itemID);
        $i=0;
        
        foreach($item_pictures as $item_picture):
            if($i==0)
            {
               $featured_image =  $item_picture->picture_url;
            }
            else
            {
                if($i == sizeof($item_pictures)-1):
                    $gallery .= $item_picture->picture_url;
                    else:
                    $gallery .= $item_picture->picture_url."|";
                endif;                
            }
            $i++;
        endforeach;
        #BEGIN - import variations
        $item_object = $this->get_item($item->itemID);
        $attributes_and_variations = $this->get_attributes_and_variations($item_object);
        #ENDOF
       
        fputcsv($handle, array(
            $item->itemID,
            "publish",
            $item->Title,
            "yes",
            "instock",
            $item->Quantity,
            str_replace(":","->",$item->PrimaryCategory),
            $item->StartPrice,
            "visible",
            $featured_image,
            $gallery,
            "platform->eBay|seller_url->http://www.ebay.com.au/usr/$seller_id|item_url->{$item->viewItemURL}|total_sold->{$item->totalItemsSold}|total_last_30_days_sold->{$item->totalQTYPurchased}|location->{$item->Location}|ShippingServiceOptions->{$item->ShippingServiceOptions}|InternationalShippingServiceOption->{$item->InternationalShippingServiceOption}|ExcludeShipToLocation->{$item->ExcludeShipToLocation}|ShipToLocations->{$item->ShipToLocations}|AttributesAndVariations->".json_encode($attributes_and_variations).""            
        ));
        endif;        
        $k++;
    } 

    fclose($handle);
    $csvFile = fopen('/home/latingan/public_html/mercado-directo.com/test.csv','r');
    $csvData = fread($csvFile,'/home/latingan/public_html/mercado-directo.com/test.csv');
    fclose($csvFile);


   header('Content-Type: text/csv');
   header('Content-Disposition: attachment; filename="export.csv"');
   header('Pragma: no-cache');    
   header('Expires: 0');

   echo $csvData;
    exit;
        }
        
        public function get_convert_currency($from, $to, $amount)
{
    if($amount>0):
         return $this->call_google_api_converter($from, $to, 50);
        else:
            return $amount;
        endif;
}
public function get_attributes_and_variations($item)
{ 
    $attributes = array();
    $variations_attrs = array();	
    if($item->Item->Variations!=null):
        $variations = $item->Item->Variations->Variation;
        for($i=0;$i<sizeof($variations);$i++):                   
            $variation = $variations[$i];
			$attributes_pairs = array();
        foreach($variation->VariationSpecifics as $v_specific):  
            /*collect attributes for variations*/
            $attributes_pairs[] = array(
                                        "name"=>   $v_specific->Name,		   
                                        "slug"=>   $v_specific->Name,
                                        "option"=> $v_specific->Value[0]
                                       ); 			
		   /*prepare product attrs*/
		   if(@!in_array($v_specific->Value[0], (array)$attributes [$v_specific->Name]))
		   $attributes [$v_specific->Name][] = $v_specific->Value[0];
        endforeach;
		   /*prepare product variations*/
           $variations_attrs[] = array(
                                        "regular_price"=> $variation->StartPrice->value,		   
                                        "attributes"=> $attributes_pairs,
                                        "managing_stock"=> true,
                                        "stock_quantity"=> $variation->Quantity
		                      );		
       endfor;   
	 return array("attributes"=>$attributes,"variations"=>$variations_attrs);	      
     endif; 
     return false;
    
}
public function call_google_api_converter($from, $to, $amount)
{
 $content = file_get_contents('http://www.google.com/finance/converter?a='.$amount.'&from='.$from.'&to='.$to);
   $doc = new DOMDocument;
   @$doc->loadHTML($content);
   $xpath = new DOMXpath($doc);
   $result = $xpath->query('//*[@id="currency_converter_result"]/span')->item(0)->nodeValue;
   return $result;
}

	public function add_transaction_details($itemID, $profitable_item)
	{
		$totalQTYPurchased = 0;
		$totalMoneyPurchased = 0.00;
	    $GetItemTransactions = new GetItemTransactions();
		$data = array("EntriesPerPage"=>200,"PageNumber"=>1,"NumberOfDays"=>30);
		$response = $GetItemTransactions->execute($itemID,$data);
		$profitable_item["totalItemsSold"] = $response->Item->SellingStatus->QuantitySold;
		$TotalNumberOfPages = $response->PaginationResult->TotalNumberOfPages;
		for($i=1;$i<=$response->PaginationResult->TotalNumberOfPages;$i++):
			if($i>1)
			{
				$data["PageNumber"] = $i;
				$response = $GetItemTransactions->execute($itemID,$data);
			}		
		     $calculus = $this->get_transaction_calculus($response);
			 $totalQTYPurchased +=  $calculus["totalQTYPurchased"];
			 $totalMoneyPurchased += $calculus["totalMoneyPurchased"];
		endfor;
		
		$profitable_item["totalQTYPurchased"] = $totalQTYPurchased;
		$profitable_item["totalMoneyPurchased"] = number_format($totalMoneyPurchased, 2, '.', '');;
		return  $profitable_item;
	}
	public function get_transaction_calculus($response)
	{
		$calculus = array();
		$totalQTYPurchased = 0;
		$totalMoneyPurchased = 0.00;
		for($i=0; $i<sizeof($response->TransactionArray);$i++):
		 $transaction = $response->TransactionArray[$i];
		 $totalQTYPurchased += $transaction->QuantityPurchased;
		 $totalMoneyPurchased += $transaction->TransactionPrice->value;
		endfor;	
		$calculus["totalQTYPurchased"] += $totalQTYPurchased;
		$calculus["totalMoneyPurchased"] += $totalMoneyPurchased;
		return $calculus;
	}
	public function import_profitable_item_transactions($itemID)
	{
		$GetItemTransactions = new GetItemTransactions();
		$data = array("EntriesPerPage"=>200,"PageNumber"=>1,"NumberOfDays"=>30);
		$response = $GetItemTransactions->execute($itemID,$data);
		if( $response->PaginationResult->TotalNumberOfPages>1)
			die($itemID);
		
		for($i=0; $i<sizeof($response->TransactionArray);$i++):
		 $transaction = $response->TransactionArray[$i];
		 $order_transaction = $this->build_order_transaction_data($itemID, $transaction);
		 $this->Importer->import_profitable_item_transaction($order_transaction);
		endfor;	
	}
	 public function build_order_transaction_data($itemID, $transaction)
    {
		$CreatedDate = date_create($transaction->CreatedDate);
        $CreatedDate =  date_format($CreatedDate,"Y-m-d");
        $data = array(
            'OrderID' => "".$transaction->OrderLineItemID."",
            'ItemID'=> utf8_encode ($itemID),
            'QuantityPurchased' => $transaction->QuantityPurchased,
            'TransactionPrice' => $transaction->TransactionPrice->value,
			'CreatedDate'=> $CreatedDate   
            );
       return $data;
        
    }
	
	public function item_import_single(/*$item_id*/)
    {  
          $item_id = 111572615387;	
          if($item_id != ""):
          $item = $this->get_item($item_id);          
          $data = $this->build_items_data($item);
          $result = $this->Item_model->import_item($data);          
          $this->import_item_pictures($item); 
     endif;
    }
	/**
     * Ebaycontroller::import_item_pictures()     
     * Import pictures to database
     * @param item objects
     */
    public function import_item_pictures($item)
    {
        $array_pictures = $item->Item->PictureDetails->PictureURL;
        foreach($array_pictures as $picture_url):
            $this->Item_model->item_picture_import($item->Item->ItemID,$picture_url);
        endforeach;
    }
   
	/**
     * Ebaycontroller::get_item()     
     * Get items collection to import
     * @param item id
     * @return item object
     */
    public function get_item($itemID)
    {        
	
        $items = array('ItemID' => $itemID);        
        $GetItem = new GetItem();      
        $GetItem->Array_itemsID = $items;          
        $item_object = $GetItem->get_item();
        return $item_object;   /*get items list*/
        
    }
	/**
     * Ebaycontroller::build_items_data()     
     * Get data to insert or update an item
     * @param item object
     * @return data array
     */
    public function build_items_data($item)
    {
        $data = array(
						'ItemID' => "{$item->Item->ItemID}",
						'Title'=> utf8_encode ($item->Item->Title),
						'Description' => utf8_encode($item->Item->Description),
						'StartPrice' => $item->Item->StartPrice->value,
						'ConvertedBuyItNowPriceValue' => $item->Item->ListingDetails->ConvertedBuyItNowPrice->value,
						'ConvertedStartPriceValue' => $item->Item->ListingDetails->ConvertedStartPrice->value,
						'ConvertedReversePriceValue' => $item->Item->ListingDetails->ConvertedReservePrice->value,
						'ViewItemURL' => $item->Item->ListingDetails->ViewItemURL,
						'ViewItemURLForNaturalSearch' => $item->Item->ListingDetails->ViewItemURLForNaturalSearch,
						'LayoutID' => $item->Item->ListingDesigner->LayoutID,
						'ThemeID' => $item->Item->ListingDesigner->ThemeID,
						'ListingDuration' => $item->Item->ListingDuration,
						'ListingType' => $item->Item->ListingType,
						'Location' => $item->Item->Location,
						'PrimaryCategoryID' => $item->Item->PrimaryCategory->CategoryID,
						'Quantity' => $item->Item->Quantity,
						'Currency' => $item->Item->Currency,
						'StartTime1' => $item->Item->ListingDetails->StartTime,
						'EndTime1' => $item->Item->ListingDetails->EndTime,
						);
       return $data;        
    }
	
	public function add_item()
    {    
		$item_information = $this->Item_model->get_item(272030545952);
		$data = $this->build_add_items_data($item_information);
		$addItem = new Additem();
		$addItem->add_item($data);

     //   $item = $this->Item_model->get_item (272030545952);
		
    }
	 public function build_add_items_data($item)
    {
        $data = array(
						'ConditionID'=> 1000,												
						'PrimaryCategoryID' => $item->PrimaryCategoryID,
						'BestOfferEnabled'=> 0,
						'Title'=> utf8_encode ($item->Title."test"),
						'Description' => utf8_encode($item->Description),
						'Site'=>"ES",
						'Country'=>"CO",
						'Currency' => "USD",
						'DispatchTimeMax' => 0,
						'LayoutID' => $item->ItemLayoutID,
						'ThemeID' => $item->Item->ListingDesigner->ThemeID,
						'ListingDuration' => "GTC",
						'ListingType' => "FixedPriceItem",
						'Location' => "Armenia, Quindio",						
						'Quantity' => 5,						
						'PaymentMethods' => "PayPal",
						'PaymenteMail' => "ventas@mercado-directo.com",
						'StartPrice' => $item->ConvertedStartPriceValue,
						'ShippingTermsInDescription' => True,
						
						);
       return $data;        
    }
	
}

