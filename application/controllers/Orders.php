<?php
//$P$BOR1eRzMebs8rvF5SUrrecir2HJ20e/
//http://technocreates.net/sample-php-code-for-paypal-refundtransaction-using-paypal-nvp-api/
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(true);
require_once 'ebaycalls/GetOrders.php';
require_once 'ebaycalls/GetItem.php';
require_once 'ebaycalls/MessagesAAQToPartner.php';
require_once 'ebaycalls/AddDispute.php';
require_once 'PDFInvoiceGenerator/invoice.php';
require "smsapi/Services/Twilio.php";

class Orders extends CI_Controller {
	public function __construct()
        {
           parent::__construct();
           $this->load->model('Ebay');
           $this->load->model('Importer');
           $this->load->model('Orders_model');
		   $this->load->model('Payments');
           //$this->load->model('Invoice'); 
           $this->load->helper('form');
        }
		
	public function import_orders()
	{
		
          $orders = $this->Ebay->get_orders();
		/* echo "<pre>";
		  print_r($orders);die;*/
          for($i=0; $i<sizeof($orders->OrderArray);$i++):
              $order_data = $this->build_order_data($orders->OrderArray[$i]);
              $existed = $this->Importer->import_order($order_data);
              if(!$existed):
                 for($j=0;$j<sizeof($orders->OrderArray[$i]->TransactionArray);$j++):
				  $order = $orders->OrderArray[$i]->TransactionArray[$j];                                  
				  $data = $this->get_item_specifics($order->Item->ItemID);
				  if(is_array($data)):
				  $order_transaction_data = $this->build_order_transaction_data($order ,$orders->OrderArray[$i]->OrderID);
				  $transactionID = $this->Importer->import_order_transaction($order_transaction_data);
				  $this->Importer->update_order_transaction_delivery_id($transactionID, $data);
				  endif;				 
              endfor; 
              endif; 
              /*paypal transaction*/
				  $PaymentInfo = $this->Payments->Get_transaction_details($orders->OrderArray[$i]->ExternalTransaction[0]->ExternalTransactionID);
				  $PaymentInfoData = $this->build_payment_data($orders->OrderArray[$i]->OrderID,$PaymentInfo);
				  $this->Importer->import_order_payment_info($PaymentInfoData);		  
          endfor;
		  if($this->input->get("loc"))
		  {
			  redirect(base_url()."index.php/administrator/orders");
		  }
	}
	 /**
     * Ebaycontroller::get_item()     
     * Get items collection to import
     * @param item id
     * @return item object
     */
    public function get_item_specifics($ItemID)
    {        
        /*get items list*/
        $items = array('ItemID' => $ItemID);        
        $GetItem = new GetItem();      
        $GetItem->Array_itemsID = $items;          
        $item_object = $GetItem->get_item();
		$data = $this->fetch_item_specifics($item_object);		
        return $data;
    }
	/*data to know if needs delivery with this app*/
	function testa()
	{
		$this->get_item_specifics(151816974652);
	}
	public function fetch_item_specifics($item)
    {    

		$data = array();
             if(is_array($item->Item->ItemSpecifics)):
                 foreach($item->Item->ItemSpecifics as $item_specific):
				 
				 if($item_specific->Name == "DELID"):
				   $data["delivery_id"] = $item_specific->Value[0];
				 endif;                                  
                 endforeach;
             endif;          
       return $data;        
    }
    public function build_order_data($order)
    {
        
        $data = array(
            'OrderID' => "{$order->OrderID}",
            'AmountPaidCurrency'=> utf8_encode ($order->AmountPaid->attributeValues["currencyID"]),
            'AmountPaidValue' => $order->AmountPaid->value,
            'PaymentMethod' => $order->CheckoutStatus->PaymentMethod,
            'PaymentStatus' => $order->CheckoutStatus->Status,
            'ShippingAddressName' => $order->ShippingAddress->Name,
            'ShippingAddressStreet' => $order->ShippingAddress->Street1,
            'ShippingAddressCityName' =>$order->ShippingAddress->CityName,
            'ShippingAddressStateOrProvince' => $order->ShippingAddress->StateOrProvince,
            'ShippingAddressCountry' => $order->ShippingAddress->Country,
            'ShippingAddressCountryName' => $order->ShippingAddress->CountryName,
            'ShippingAddressPhone' => str_replace(' ', '', $order->ShippingAddress->Phone),
            'ShippingAddressPostalCode' => $order->ShippingAddress->PostalCode,
            'BuyerUserID' => $order->BuyerUserID,
            'PaidTime' => $order->PaidTime,
            'ShippingServiceSelectedValue' => $order->ShippingServiceSelected->ShippingServiceCost->value,
			'PaymentTransactionID' => $order->ExternalTransaction[0]->ExternalTransactionID
			
            );
       return $data;        
    }
	
	public function build_payment_data($OrderID,$Payment)
    {
        
        $data = array(
            'OrderID' => "{$OrderID}",
            'ShipToEmail'=>$Payment["EMAIL"],
            'PayerStatus' => $Payment["PAYERSTATUS"],
            'ShipToCountryCode' => $Payment["SHIPTOCOUNTRYCODE"],
            'AddressStatus' => $Payment["ADDRESSSTATUS"]		
            );
       return $data;        
    }
	
     public function build_order_transaction_data($order,$OrderID)
    {
        $data = array(
            'OrderID' => "{$OrderID}",
            'ItemID'=> utf8_encode ($order->Item->ItemID),
            'Title' => $order->Item->Title,
            'QuantityPurchased' => $order->QuantityPurchased,
            'TransactionPrice' => $order->TransactionPrice->value
            );
       return $data;
        
    }
    public function notify_shipping_details_to_buyer()
	{
	 $orders_unshipped = $this->Orders_model->get_orders_without_delivery_message_sent();
	 foreach($orders_unshipped as $order):
	 $orderID = $order->OrderID;
	 $order = $this->Orders_model->get_order($orderID);
	 $order_transactions = $this->Orders_model->get_orders_transactions($orderID);	
	 /*BG Message body*/
	 $shipping_link = $this->generate_link($orderID);
	 $message_shipping_notify = $this->Orders_model->get_app_config("MESSAGE_SHIPPING_NOTIFY")->value;
	 $order_transaction = $this->Orders_model->get_orders_transactions($orderID);
     
	 /*EOF Message body*/
	 /**/
        
	 $MessagesAAQToPartner = new MessagesAAQToPartner();
	 foreach($order_transactions as $order_transaction):
	 $message_shipping_notify = str_replace("USER_FULL_NAME", $order->ShippingAddressName, $message_shipping_notify);
	 $message_shipping_notify = str_replace("SHIPPING_LINK", $shipping_link, $message_shipping_notify);
	 $message_shipping_notify = str_replace("ITEM_TITLE", $order_transaction->Title, $message_shipping_notify);
		$params = array(
		 "BuyerUserID"=> $order->BuyerUserID,
		 "ItemID"=> $order_transaction->ItemID,
		 "Subject"=> "Shipping Link",
		 "Body"=> $message_shipping_notify,
		 "SenderID"=> "mercado-directo"
		);
		$MessagesAAQToPartner->send_message_to_buyer($params);
	    $this->Orders_model->update_delivery_message_sent($orderID, "true");
	 endforeach;		
	
	 
	 endforeach;
	 
	}
	function test1()
	{
		$this->generate_link("654313131");
	}
    public function generate_link($orderID)
    {
      return  "www.mercado-directo.com/orders/delivery/?id=".$orderID;  
    }
    public function delivery()
    {
       $error = array();
       $data = array();
      if($this->input->get('id')):
      $data["id"] = $this->input->get('id');
      $data["id_encrypted"] = $this->input->get('id');
      else:
      $error[] = "Kindly, make sure you copy and paste link sent to you ebay message application.";
      endif;
      if(sizeof($error)==0):
       $order = $this->Orders_model->get_order($data["id"]);
	   if($order->AmountPaidValue<5 and $order->app_verified=="false"):
	     $this->mark_as_shipped($data["id"]);
		 die;
	   endif;
      if($order):
           if($order->app_verified=="false"):
		       $payment_info = $this->Orders_model->get_payment_info($data["id"]);
		       $buyer_validation_result = $this->buyer_validation($order->ShippingAddressCountry,$payment_info->ShipToCountryCode, $payment_info->PayerStatus, $order->PaymentMethod,$order->ShippingAddressPhone);
			   if(sizeof($buyer_validation_result)>0):
			    $message = "Sorry, we cannot ship your order because you do not meet the following requirements to complete the verification process:<br>";
				foreach($buyer_validation_result as $single_error):
				 $message .= "<b>*</b> ".$single_error."<br>";
				endforeach;
				$data["message"] = $message;
				
				if($order->refunded=="false"):
				 /*BG Refun Money*/
					/*Platform commision*/
					 $platform_commision = $order->AmountPaidValue * 0.08;		 
					 /*Payment comission*/
					 $payment_commision = $order->AmountPaidValue * 0.06;	 
                     /*Payment comission*/
					 $refund_commision = $order->AmountPaidValue * 0.03;						 
					 /*Own commision*/  
					 $full_comission = 	$platform_commision + $payment_commision + $refund_commision + 1;
					 $ammount = $order->AmountPaidValue - $full_comission; // win 1€
				 	 $this->Payments->Refund_transaction($order->PaymentTransactionID, "Partial", $ammount);
					 $this->Orders_model->update_is_refunded($data["id"], "true");	
				endif;
				    
				     $this->Orders_model->update_is_refunded($data["id"], "true");					 
					 /*EOF Refund Money*/
					 $data["was_refunded"] = $order->refunded;
					 $data["refunded_date"] = $order->refunded_date;
                	 $this->template->load('buyer_is_scammer',$data);		
                     die;					
			   else:
			    $data["country"] = $this->Orders_model->get_country_by_code($order->ShippingAddressCountry);               
               if($this->has_country_code_included($order->ShippingAddressPhone)):
                  $data["phone"] = $this->remove_countrycode($order->ShippingAddressPhone, $data["country"]->phonecode);
                  $data["phone_code_removed"] = "true";
               else:
                   $data["phone"] = $order->ShippingAddressPhone; 
                   $data["phone_code_removed"] = "false";
               endif;
               $this->template->load('phone_verify',$data);	              		   
			   endif;               
              else:
               //logic for already shipped item.
			   $order_transactions = $this->Orders_model->get_orders_transactions($data["id"]);	
               $output_transactions = array();	
               $index=0;				
				foreach($order_transactions as $order_transaction):				 
				    $output_transactions[$index]["ItemID"] =  $order_transaction->ItemID;
					$output_transactions[$index]["Title"]  =  $order_transaction->Title;
					$output_transactions[$index]["Keys"]   = $this->Orders_model->get_orders_transaction_keys($order_transaction->transactionID, $order_transaction->delivery_id);	
				 $index++;
				endforeach;				
				$data["orders_transaction_keys"] = $output_transactions;
                $this->template->load('display_orders_transactions_keys',$data);
          endif;
         else:
          //not found
             $error[] = "Order not found.";
      endif;       
      endif;   
		echo "<pre>";
		print_r($error);	  
    }
	
	public function refund()
	{
		$response = array();
	 	if($this->input->post('id_order')):
		 $order = $this->Orders_model->get_order($this->input->post('id_order'));
		 $this->Orders_model->update_is_refunded($this->input->post('id_order'), "true");
         if($order->refunded=="false"):
				 /*BG Refun Money*/
					/*Platform commision*/
					 $platform_commision = $order->AmountPaidValue * 0.08;		 
					 /*Payment comission*/
					 $payment_commision = $order->AmountPaidValue * 0.06;	 
                     /*Payment comission*/
					 $refund_commision = $order->AmountPaidValue * 0.03;						 
					 /*Own commision*/  
					 $full_comission = 	$platform_commision + $payment_commision + $refund_commision + 1;
					 $ammount = $order->AmountPaidValue - $full_comission; // win 1€
					 
				 	 $result = $this->Payments->Refund_transaction($order->PaymentTransactionID, "Partial", $ammount);
					 if($result):
					 $this->Orders_model->update_is_refunded($this->input->post('id_order'), "true");	
					 endif;
					 else:
					 $result = false;
				endif;
				    
				     $this->Orders_model->update_is_refunded($this->input->post('id_order'), "true");					 
					 /*EOF Refund Money*/
		 if($result)
		 {
			$response["message"] = "Payment has been refunded"; 			
		 }
		 
	     else
		 {
			 // errors from paypal api
			 $response["message"] = "Already refunded on ".$order->refunded;
			 $response["error"] = "true";
		 }
		endif;
		echo json_encode($response);
	}
	
	/*
	 @platform_country_code - ebay
	 @payment_country_code - paypal
	 @Payment_method - PayPal, Skrill....
	*/
	
	public function buyer_validation($platform_country_code,$payment_country_code, $payment_account_status, $Payment_method, $phone)
	{
     $errors = array();
	 $live_country_code = $this->ip_info($this->input->ip_address(), "Country Code"); 
	 if($live_country_code!=$platform_country_code):
	  $errors [] = "Your eBay account was registered in <b>".$this->Orders_model->get_country_by_code($platform_country_code)->nicename."</b>, but you are trying to receive this order from <b>".$this->Orders_model->get_country_by_code($live_country_code)->nicename."</b>.";
	 endif;
	 if($live_country_code!=$payment_country_code):
	  $errors [] = "Your ".$Payment_method." account was registered in <b>".$this->Orders_model->get_country_by_code($payment_country_code)->nicename."</b>, but you are trying to recive this order from <b>".$this->Orders_model->get_country_by_code($live_country_code)->nicename."</b>.";
	 endif;
	/*  if($payment_account_status!="verified"):
	  $errors [] = "Your ".$Payment_method." account is <b>unverified</b>.";
	 endif;
	 if($phone=="" or $phone==null):
	 $errors [] = "Your phone number on your eBay account is <b>empty</b>, <b>outdated</b> or we are not authorized to see this information. ";
	 endif;*/
	 return $errors;
	}
	
	function ip_info($ip = NULL, $purpose = "location", $deep_detect = TRUE) {
    $output = NULL;
    if (filter_var($ip, FILTER_VALIDATE_IP) === FALSE) {
        $ip = $_SERVER["REMOTE_ADDR"];
        if ($deep_detect) {
            if (filter_var(@$_SERVER['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if (filter_var(@$_SERVER['HTTP_CLIENT_IP'], FILTER_VALIDATE_IP))
                $ip = $_SERVER['HTTP_CLIENT_IP'];
        }
    }
    $purpose    = str_replace(array("name", "\n", "\t", " ", "-", "_"), NULL, strtolower(trim($purpose)));
    $support    = array("country", "countrycode", "state", "region", "city", "location", "address");
    $continents = array(
        "AF" => "Africa",
        "AN" => "Antarctica",
        "AS" => "Asia",
        "EU" => "Europe",
        "OC" => "Australia (Oceania)",
        "NA" => "North America",
        "SA" => "South America"
    );
    if (filter_var($ip, FILTER_VALIDATE_IP) && in_array($purpose, $support)) {
        $ipdat = @json_decode(file_get_contents("http://www.geoplugin.net/json.gp?ip=" . $ip));
        if (@strlen(trim($ipdat->geoplugin_countryCode)) == 2) {
            switch ($purpose) {
                case "location":
                    $output = array(
                        "city"           => @$ipdat->geoplugin_city,
                        "state"          => @$ipdat->geoplugin_regionName,
                        "country"        => @$ipdat->geoplugin_countryName,
                        "country_code"   => @$ipdat->geoplugin_countryCode,
                        "continent"      => @$continents[strtoupper($ipdat->geoplugin_continentCode)],
                        "continent_code" => @$ipdat->geoplugin_continentCode
                    );
                    break;
                case "address":
                    $address = array($ipdat->geoplugin_countryName);
                    if (@strlen($ipdat->geoplugin_regionName) >= 1)
                        $address[] = $ipdat->geoplugin_regionName;
                    if (@strlen($ipdat->geoplugin_city) >= 1)
                        $address[] = $ipdat->geoplugin_city;
                    $output = implode(", ", array_reverse($address));
                    break;
                case "city":
                    $output = @$ipdat->geoplugin_city;
                    break;
                case "state":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "region":
                    $output = @$ipdat->geoplugin_regionName;
                    break;
                case "country":
                    $output = @$ipdat->geoplugin_countryName;
                    break;
                case "countrycode":
                    $output = @$ipdat->geoplugin_countryCode;
                    break;
            }
        }
    }
    return $output;
}
    public function check_verification_code()
    {
         $data = array();
        if($this->input->post("id_encrypted")):
            $data["id"] = $this->input->post("id_encrypted");
            $data["id_encrypted"] = $this->input->post('id_encrypted');
            $order = $this->Orders_model->get_order($data["id"]);
            $data["country"] = $this->Orders_model->get_country_by_code($order->ShippingAddressCountry);
            if($this->has_country_code_included($order->ShippingAddressPhone)):
                  $data["phone"] = $this->remove_countrycode($order->ShippingAddressPhone, $data["country"]->phonecode);
                  $data["phone_code_removed"] = "true";
               else:
                   $data["phone"] = $order->ShippingAddressPhone;
                   $data["phone_code_removed"] = "false";
               endif;
            if($this->input->post("code")!=""):

              $result = $this->Orders_model->check_code_typed( $data["id"], $this->input->post("code"));
            if($result):
                //valid 
                $output_transactions = array();	
                $index = 0;				
                //$this->Orders_model->update_shipped_status($data["id"]);
				$this->Orders_model->update_app_verified_status($data["id"]);
				$data["message"] = "Thank you. Order has been verified successfully."; 
				$order_transactions = $this->Orders_model->get_orders_transactions($data["id"]); 
				$no_in_stock = array();
				$j = 0;
				foreach($order_transactions as $order_transaction):
				  if($order_transaction->shipped=="false")//prevent shipp new keys if user reload this function				  
				   $result = $this->Orders_model->update_digital_item_cd_keys_status($order_transaction->delivery_id, $order_transaction->transactionID, "true", $order_transaction->QuantityPurchased);			   
				  if($result==false):
				   /*Notify to admin - There is not enought keys available*/	
                    $no_in_stock[$j]["Title"] = $order_transaction->Title;
                    $j++;				
				 endif;
				    $output_transactions[$index]["ItemID"] =  $order_transaction->ItemID;
					$output_transactions[$index]["Title"]  =  $order_transaction->Title;
					$output_transactions[$index]["Keys"]   = $this->Orders_model->get_orders_transaction_keys($order_transaction->transactionID, $order_transaction->delivery_id);	
				 $index++;
				endforeach;
				if(sizeof($no_in_stock)>0):
				 $this->notify_to_admin_out_stock($data["id"], $no_in_stock);  
				 else:
				 /*send keys and invoice to buyer email*/
				 $this->send_invoice_keys_to_buyer($data["id"]);
				 /*Notify to admin - order shipped successfully*/
				 $this->notify_to_admin_order_shipped($data["id"]);
				 
				 $this->Orders_model->update_shipped_status($data["id"]);
				endif;
				$data["orders_transaction_keys"] = $output_transactions;
                $this->template->load('display_orders_transactions_keys',$data);
            else:
                $this->Orders_model->increase_times_allowed($data["id"]);
               $data["message"] = "Code does not match. Kindly, try again."; 
               $data["message_type"] = "error";

               $this->template->load('phone_verification',$data);
            endif;                
              else:
              $data["message"] = "Please, type code sent."; 
              $data["message_type"] = "error";
              $this->template->load('phone_verification',$data);
            endif;                       
            else:
              die("Kindly, make sure you copy and paste link sent to you ebay message application. If you are doing all fine and it is not working. Please contact us.");
        endif; 
    }
	public function mark_as_shipped($id_order)
	{
		$output_transactions = array();	
		$data["id"] = $id_order; 
                $index = 0;				
				$this->Orders_model->update_app_verified_status($id_order);
				$data["message"] = "Thank you. Order has been verified successfully."; 
				$order_transactions = $this->Orders_model->get_orders_transactions($id_order); 
				$no_in_stock = array();
				$j = 0;
				foreach($order_transactions as $order_transaction):
				  if($order_transaction->shipped=="false")//prevent shipp new keys if user reload this function				  
				   $result = $this->Orders_model->update_digital_item_cd_keys_status($order_transaction->delivery_id, $order_transaction->transactionID, "true", $order_transaction->QuantityPurchased);			   
				  if($result==false):
				   /*Notify to admin - There is not enought keys available*/	
                    $no_in_stock[$j]["Title"] = $order_transaction->Title;
                    $j++;				
				 endif;
				    $output_transactions[$index]["ItemID"] =  $order_transaction->ItemID;
					$output_transactions[$index]["Title"]  =  $order_transaction->Title;
					$output_transactions[$index]["Keys"]   = $this->Orders_model->get_orders_transaction_keys($order_transaction->transactionID, $order_transaction->delivery_id);	
				 $index++;
				endforeach;
				if(sizeof($no_in_stock)>0):
				 $this->notify_to_admin_out_stock($id_order, $no_in_stock);  
				 else:
				 /*send keys and invoice to buyer email*/
				 $this->send_invoice_keys_to_buyer($id_order);
				 /*Notify to admin - order shipped successfully*/
				 $this->notify_to_admin_order_shipped($id_order);
				 
				 $this->Orders_model->update_shipped_status($id_order);
				endif;
				$data["orders_transaction_keys"] = $output_transactions;
                $this->template->load('display_orders_transactions_keys',$data);
	}
    public function check_phone() 
    {
       $error = array();
       $data = array();
        if($this->input->post("id_encrypted")):
            $data["id"] = $this->input->post("id_encrypted");
            $data["id_encrypted"] = $this->input->post('id_encrypted');
            $order = $this->Orders_model->get_order($data["id"]);
            $data["country"] = $this->Orders_model->get_country_by_code($order->ShippingAddressCountry);
            if($this->has_country_code_included($order->ShippingAddressPhone)):
                  $data["phone"] = $this->remove_countrycode($order->ShippingAddressPhone, $data["country"]->phonecode);
                  $data["phone_code_removed"] = "true";
               else:
                   $data["phone"] = $order->ShippingAddressPhone;
                   $data["phone_code_removed"] = "false";
               endif;
            if($this->input->post("phone_typed")!=""):
              $result = $this->Orders_model->check_phone_typed( $data["id"], $this->input->post("phone_typed"),$this->input->post("phone_code_removed"),$data["country"]->phonecode);
            if($result):
                //valid
                $this->send_verification_code($order);
                $this->template->load('phone_verification',$data);
            else:
               $data["message"] = "Phone does not match. Kindly, try again."; 
               $data["message_type"] = "error";
               $this->template->load('phone_verify',$data);
            endif;
                
              else:
              $data["message"] = "Please, type you phone number."; 
               $data["message_type"] = "error";
               $this->template->load('phone_verify',$data);
            endif;            
            
            else:
              die("Kindly, make sure you copy and paste link sent to you ebay message application. If you are doing all fine and it is not working. Please contact us.");
        endif;     
    }
    public function test()
    {
	  echo	$this->generate_link("271927115517-1512609710017");die;
      $order = $this->Orders_model->get_order("271788603799-1441841425017");
      $this->send_verification_code($order);
    }
    public function send_verification_code($order)
    {
      $file = fopen("sms.txt", "w"); 
      $code = rand(100000, 999999);
      if(!$this->has_country_code_included("$order->ShippingAddressPhone")):
         $data["country"] = $this->Orders_model->get_country_by_code($order->ShippingAddressCountry);
         $toNumber = "+".$data["country"]->phonecode.$order->ShippingAddressPhone;
         else:
         $toNumber = $order->ShippingAddressPhone;
      endif;
      $toNumber = str_replace(" ","",$toNumber);
      $toNumber = str_replace(".","",$toNumber);
      fwrite($file, "StoryTeller Verify $code $toNumber" );
      $AccountSid = "AC3d1f3a68847f5e319bf64eaa8e716e17";
      $AuthToken  = "2d9daf05e425278c5d0e1e6be29ddedf";
      $fromNumber = "+16055938495";
    //  $toNumber= $order->ShippingAddressPhone;
      $client = new Services_Twilio($AccountSid, $AuthToken);
        try
        {
            $sms = $client->account->messages->sendMessage($fromNumber, $toNumber, $code);
            if($sms):
              $verification["phone_no"] = $toNumber;
              $verification["code"] = $code;
              $verification["OrderID"] = $order->OrderID;
              $this->Orders_model->add_verification($verification);
              $data["message"] = "Kindly, check you sms inbox and search code sent."; 
              $data["message_type"] = "ok"; 
              $data["result"] = "ok"; 
              else:
               $data["message"] = "We are sorry, We could not delivery SMS. Kindly, contact us."; 
               $data["message_type"] = "error"; 
               $data["result"] = "failed"; 
            endif;
            return $data;
        } 
        catch (Exception $e)
        {
			
	}
    }
	
	
	public function send_invoice_keys_to_buyer($id_order=null)
	{
		if($this->input->post("id_order")):
		$id_order =$this->input->post("id_order");
		$json = true;
		else:
		$json = false;
		endif;
		$order = $this->Orders_model->get_order($id_order); 
		if($order->shipped == "false"):
		$response = array();
		$response["error"] = true;
		$response["message"] = "Please, try when order is full shipped.";
		if($this->input->post("is_json")):
		
		echo json_encode($response);	
		die;
		endif;
		
		else:
		
		 $payment_info = $this->Orders_model->get_payment_info($id_order);
		 $shipToeMail = $payment_info->ShipToEmail;
		 $order_transactions = $this->Orders_model->get_orders_transactions($id_order);	
               $output_transactions = array();	
               $index=0;				
				foreach($order_transactions as $order_transaction):				 
				    $output_transactions[$index]["ItemID"] =  $order_transaction->ItemID;
					$output_transactions[$index]["Title"]  =  $order_transaction->Title;
					$output_transactions[$index]["Keys"]   = $this->Orders_model->get_orders_transaction_keys($order_transaction->transactionID, $order_transaction->delivery_id);	
				 $index++;
				endforeach;	
          $data["orders_transaction_keys"] = $output_transactions;		
          $data["shipping_link"] =  $this->generate_link($id_order);          	
          $data["date_placed"] = $order->PaidTime;		
		  $eMail_template =  $this->template->ajax_load_view('emails/send_invoice_keys',$data, true);
		  $this->sendEmail_invoice_keys_to_buyer($id_order, $eMail_template, $shipToeMail,$json);
        endif;		
		 
	}
	
	public function notify_to_admin_out_stock($id_order, $no_in_stock)
	{
	  	$this->load->library('email');
		 $this->load->helper('path');
			 		    $config=array(
      'protocol'=>'smtp',
      'smtp_host'=>'ssl://smtp.googlemail.com',
      'smtp_port'=>465,
      'smtp_user'=>'ventas@mercado-directo',
      'smtp_pass'=>'Rock123',
	  
    );
              
	  $config['allowed_types'] = '*';
	  $config['max_size'] = '100000';
	  $config['max_width']  = '1024';
	  $config['max_height']  = '768';
	  $config['mailtype']  = 'html';			  	
 
    $this->load->library("email",$config);
    $this->email->set_newline("\r\n");
    $this->email->from("ventas@mercado-directo","Mercado Directo");
    $this->email->to("alex.rivera.ws@gmail.com");
    $this->email->subject("Out Stock - ".$id_order);
	$body  = "Order ID: ".$id_order."<br>";
	$body .= "Items out stock <br>";
	for($i=0; $i<sizeof($no_in_stock); $i++):
	 $body .= "- ".$no_in_stock[$i]["Title"]."<br>";
	endfor;	
    $this->email->message($body);
    $this->email->set_mailtype("html");
	$errors = "";
	 if($this->email->send())
		{      
		} 
	}
	public function notify_to_admin_order_shipped($id_order)
	{
	  	$this->load->library('email');
		 $this->load->helper('path');
			 		    $config=array(
      'protocol'=>'smtp',
      'smtp_host'=>'ssl://smtp.googlemail.com',
      'smtp_port'=>465,
      'smtp_user'=>'ventas@mercado-directo',
      'smtp_pass'=>'Rock123',
	  
    );
              
	  $config['allowed_types'] = '*';
	  $config['max_size'] = '100000';
	  $config['max_width']  = '1024';
	  $config['max_height']  = '768';
	  $config['mailtype']  = 'html';			  	
 
    $this->load->library("email",$config);
    $this->email->set_newline("\r\n");
    $this->email->from("ventas@mercado-directo","Mercado Directo");
    $this->email->to("alex.rivera.ws@gmail.com");
    $this->email->subject("Order Shipped - ".$id_order);
	$body  = "Order ID: ".$id_order."<br>";
	$body .= "Order Shipped successfully <br>";;	
    $this->email->message($body);
    $this->email->set_mailtype("html");
	$errors = "";
	 if($this->email->send())
		{      
		} 
	}
	public function sendEmail_invoice_keys_to_buyer($id_order, $body, $shipToeMail ,$json=null)
 {
    $order = $this->Orders_model->get_order($id_order);
	/*update invoice before...*/
	$transactions = $this->Orders_model->get_orders_transactions($id_order);
	/*echo "<pre>";
	print_r($order);
	print_r($transactions);*/
	$this->generate_invoice($order, $transactions, $shipToeMail);
	
		 $this->load->library('email');
		 $this->load->helper('path');
			 		    $config=array(
      'protocol'=>'smtp',
      'smtp_host'=>'ssl://smtp.googlemail.com',
      'smtp_port'=>465,
      'smtp_user'=>'ventas@mercado-directo',
      'smtp_pass'=>'Rock123',
	  
    );
              $config['upload_path'] = $_SERVER["DOCUMENT_ROOT"]."/invoices/".$id_order.".pdf";
              $config['allowed_types'] = '*';
              $config['max_size'] = '100000';
              $config['max_width']  = '1024';
              $config['max_height']  = '768';
			   $config['mailtype']  = 'html';
			  
	
 
    $this->load->library("email",$config);
    $this->email->set_newline("\r\n");
    $this->email->from("ventas@mercado-directo","Mercado Directo");
    $this->email->to($shipToeMail);
    $this->email->subject("Order Details - ".$id_order);
	
    $this->email->message($body);
    $this->email->set_mailtype("html");
   // $this->email->attach($config['upload_path']);
	$response = array();
	$errors = "";
	 if($this->email->send())
    {
       $response["message"] = "Message sent to your PayPal eMail successfully.";
    }
 
    else
    {
		
      // Loop through the debugger messages.
    foreach ( $this->email->get_debugger_messages() as $debugger_message )
      $errors .= "- ".$debugger_message."<br>";
    // Remove the debugger messages as they're not necessary for the next attempt.
    $this->email->clear_debugger_messages();
    }
	if($errors!="")
	$response["message"] = "Sorry, there was an error to process your request:<br>".$errors;
    if($json!=false)
    echo json_encode($response);
   
 }

    public function ship_orders()
    {
        
       $orders_unshipped = $this->Orders_model->get_orders_unshipped();
       foreach($orders_unshipped as $order):
           $transactions = $this->Orders_model->get_orders_transactions($order->OrderID);
           $this->generate_invoice($order, $transactions);
       endforeach;
       //echo $orders_unshipped[0]->OrderID;
      // echo "<pre>";
    //   print_r($orders_unshipped);
    }
    
    public function generate_invoice($order, $transactions)
    {
        //////////////////////// START GENERATE PDF INVOCE ////////////////////
        $invoice = new Invoice();
        //Add Font (create manuality previament)
        $invoice->AddFont('UniFont','','DejaVuSans.ttf',true);
        //Pages count
        $invoice->AliasNbPages();
        //generate product with transactions data
        
        $data = array();
        $data = $this->build_invoice_additional_info($order);
        $result = $this->build_invoice_products($order->OrderID, $transactions, $data["tax"],$data["shipping"]);
        $data["products"] = $result["products"];
        $data["base"] = $result["base"];
        $data["customer"] = $this->build_invoice_customer($order, $transactions);
        $data["company"] = $this->build_invoice_company($order->OrderID, $transactions);
        $data['company_data'] = $data["company"];
        $data['customer_data'] = $data["customer"];
        //Color
        $data['color']=array('black'=>133,'blue'=>185,'blue'=>9);
        // Translator options
        $data['text']=array(
                        'phone' 		  =>'Phone:',
                        'fax' 			  =>'Fax:',
                        'document_id' 	  =>'DOC. ID:',
                        'email' 		  =>'Email:',
                        'customer'	   	  =>'Customer:',
                        'invoice_num' 	  =>'Invoice Number:',
                        'date' 			  =>'Created:',
                        'customer_num' 	  =>'Customer ID:',
                        'page' 			  =>'Page',
                        'of' 			  =>'of',
                        'type' 			  =>'Type',
                        'desc' 			  =>'Description',
                        'price' 		  =>'Unit. Price',
                        'quantity' 		  =>'Qty.',
                        'sum_price' 	  =>'Sum Price',
                        'sum_tax' 		  =>'Tax',
                        'pro_total'		  =>'Total',
                        'sub_total'		  =>'Subtotal',
                        'tax_rate'		  =>'Tax rate %',
                        'shipping'		  =>'Shipping',
                        'total'			  =>'Total',
                        'continued'		  =>'Continued on page ',
                        'simbol_left' 	  =>'  $    ',
                        'simbol_right' 	  =>'',
                        );
        //Description left
        $data['description_left']= '';
       
        
//echo "<pre>";
//print_r($data);die; 
      //  echo "<pre>";
/*print_r($products);
die("***");*/

        if(count($data["products"])>=18){

                //separate and paked in array of 17 products
                $pack_products = array_chunk($data["products"],17);
                $limit=count($pack_products);
                $i=1;

                foreach($pack_products as $list_products){
                        $invoice->AddPage();
                        $invoice->Head($data);
                        $invoice->Products($list_products);
                        $invoice->THead($data['text'],$data['color']);
                        if($i==$limit){
                                $invoice->Base($data);
                                $invoice->Payment($data['payment_m']);
                                $invoice->Total($data);
                        }else{
                                $invoice->Base($data,false);
                                $invoice->NextIvoice($data['text']['continued']);
                        }
                        $invoice->RotatedText(8,236,$data['description_left'],90);
                        $i++;
                }
        }else{

                //Load normality products < 18
                $invoice->AddPage();
                $invoice->Head($data);
                $invoice->Products($data["products"]);
                $invoice->THead($data['text'],$data['color']);
                $invoice->Base($data);
                $invoice->Payment($data['payment_m']);
                $invoice->Total($data);
                $invoice->RotatedText(8,236,$data['description_left'],90);	
        }

        // Generate PDF --->OUT
        $dirout='invoices/';

        if(!is_dir($dirout)){
                mkdir($dirout,0700);
        }
        $invoice->Output($dirout.$data['invoce_num'].'.pdf');
       // $invoice->Output();
       // die("created");
            }
            
    public function build_invoice_products($orderID, $transactions, $tax,$shipping_cost)
        {
        
        $data_products = array();
        $data = array();
        $index = 0; 
        $sum=0;
          foreach($transactions as $transaction):
              $data_products[$index]["type"] = "P";
              $data_products[$index]["description"] = str_replace("?","-",$transaction->Title);
              $data_products[$index]["price"] = $transaction->TransactionPrice;
              $data_products[$index]["quantity"] = $transaction->QuantityPurchased;
              $price = $this->format_number_mult($data_products[$index]["price"]);
	      $quantity = (int)$data_products[$index]["quantity"];
	      $taxformat = $this->format_number_mult('1.'.$tax);
              $data_products[$index]["sum_price"] = $this->format_number($price* (int)$quantity);
              $data_products[$index]["sum_tax"] = $this->format_number((($price*$taxformat)- $price)*$quantity);
              $data_products[$index]["total"] = $this->format_number(($price*$quantity)* $taxformat);
              $sum += ($sum+($price*$quantity));
              $index++;
          endforeach; 
          //Generate base and total
          $base = array(
            'subtotal'		=> $this->format_number_mult($sum),
            'sum_tax'	=> $this->format_number(($sum*$this->format_number_mult('1.'.$tax))-$sum),
            'total' 	=> $this->format_number(($sum*$this->format_number_mult('1.'.$tax))+$this->format_number_mult($shipping_cost)),
            );
          $data["products"] = $data_products;
          $data["base"] = $base;
          return $data;
        }
    public function build_invoice_customer($order, $transactions)
        {
        $data = array();
          foreach($transactions as $transaction):
              $data["num"] = $order->BuyerUserID;
              $data["name"] = $order->ShippingAddressName;
              $data["address"] = $order->ShippingAddressStreet;
              $data["postal_code"] = $order->ShippingAddressPostalCode;
              $data["city"] = $order->ShippingAddressCityName;
              $data["country"] = $order->ShippingAddressCountryName;
              $data["ident"] = "NY-5484EN";
          endforeach; 
          return $data;
        }
        public function build_invoice_company()
        {
        $data = array();
              $data["name"] = "Mercado Directo";
              $data["address"] = "Calle Santo domingo";
              $data["postal_code"] = "09400";
              $data["city"] = "Segovia";
              $data["phone"] = "65878541";
              $data["fax"] = "65878541";
              $data["ident"] = "NY-5484EN";
              $data["email"] = "rockscripts@gmail.com";
              $data["web"] = "profits.com";
          return $data;
        }
        public function build_invoice_additional_info($order)
        {
        $data = array();
              $data["invoce_num"] = $order->OrderID;
              $data["tax"] = "0";
              $data["date"] = $order->PaidTime;
              $data["shipping"] = $order->ShippingServiceSelectedValue;
              $data["payment_m"] = $order->PaymentMethod;
          return $data;
        }
        public function build_base_and_total($products)
        {
        
        }
      //Funtion convert in number format
function format_number($num){
	$num=str_replace(',','.',$num);
	$num = number_format($num,2,decimal_symbol,thousand_symbol);
	return $num;
}

function format_number_mult($num){
	$num = number_format($num,2,'.','');
	return $num;
}    

public function remove_countrycode($order_phone, $country_code)
{
    return str_replace("+".$country_code,"",$order_phone);
}
public function has_country_code_included($order_phone)
{
 $pos = strrpos($order_phone, "+");
  if ($pos === false) { // nota: tres signos de igual
    return false;
} 
else
    return true;
}
function decrypt($string, $key) {
$result = '';
$string = base64_decode($string);
for($i=0; $i<strlen($string); $i++) {
$char = substr($string, $i, 1);
$keychar = substr($key, ($i % strlen($key))-1, 1);
$char = chr(ord($char)-ord($keychar));
$result.=$char;
}
return $result;
}

function encrypt($string, $key) {
$result = '';
for($i=0; $i<strlen($string); $i++) {
$char = substr($string, $i, 1);
$keychar = substr($key, ($i % strlen($key))-1, 1);
$char = chr(ord($char)+ord($keychar));
$result.=$char;
}
return base64_encode($result);
}
   
public function add_dispute()
	{
		$params=array(
		               "DisputeExplanation"=>"UnableToResolveTerms",
					   "DisputeReason"=>"TransactionMutuallyCanceled",
					   "OrderLineItemID"=>"271990988190-1535462050017"
				     );
		$AddDispute = new AddDispute();
		$response = $AddDispute->add_dispute($params);
		echo "<pre>";
		print_r($response);
	}
   
}
