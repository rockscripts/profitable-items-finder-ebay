<?php
defined('BASEPATH') OR exit('No direct script access allowed');
error_reporting(true);

class Orders extends CI_Controller {
	public function __construct()
        {
           parent::__construct();
           $this->load->model('Ebay');
           $this->load->model('Importer');
           $this->load->model('Orders_model');
           $this->load->helper('form');
        }
	public function index()
	{ 
	  $data = array();
	  if($this->input->get("filter")):
	  if($this->input->get("filter")==1):
	  $data["orders"] = $orders = $this->Orders_model->get_orders("verified-pending");//App Status verified and shipping status pending or out of stock.	  	  
	  endif;
	  if($this->input->get("filter")==3):
	  $data["orders"] = $orders = $this->Orders_model->get_orders();
	  endif;
	  if($this->input->get("filter")==2):
	   $data["orders"] = $orders = $this->Orders_model->get_orders("verified-shipped");
	  endif;
	  if($this->input->get("filter")==4):
	   $data["orders"] = $orders = $this->Orders_model->get_orders("refunded");
	  endif;
	  else:
	   $data["orders"] = $orders = $this->Orders_model->get_orders();
	  endif;
	  $this->template->load('administrator/display_orders',$data);
	}	
	public function get_transactions_html()
	{
		$data = array();
		if($this->input->post('id_order')):
		 $data["order_transactions"] = $this->Orders_model->get_orders_transactions($this->input->post('id_order'));
		 $data["currency"] = $this->input->post('currency');
		 $data["shipping_status"] = $this->input->post('shipping_status');
		 //logic for already shipped item.
		   $order_transactions = $this->Orders_model->get_orders_transactions($this->input->post('id_order'));	
		   $output_transactions = array();	
		   $index=0;				
			foreach($order_transactions as $order_transaction):				 
				$output_transactions[$index]["ItemID"] =  $order_transaction->ItemID;
				$output_transactions[$index]["Title"]  =  $order_transaction->Title;
				$output_transactions[$index]["Keys"]   = $this->Orders_model->get_orders_transaction_keys($order_transaction->transactionID, $order_transaction->delivery_id);	
			 $index++;
			endforeach;				
			$data["orders_transaction_keys"] = $output_transactions;
		 $data["orders_transactions_html"] = $this->template->ajax_load_view('display_orders_transactions',$data, true);
         echo json_encode($data);		 
		endif;
	}
	public function delivery ()
	{
		$response = array();
		if($this->input->post("id")):
		/*check if order was shipped*/
		$order = $this->Orders_model->get_order($this->input->post("id"));
		if($order->shipped == "false"):
		$order_transactions = $this->Orders_model->get_orders_transactions($this->input->post("id")); 
		$no_in_stock = array();
		foreach($order_transactions as $order_transaction):
		  if($order_transaction->shipped=="false")://prevent shipp new keys if user reload this function				  
		   $result = $this->Orders_model->update_digital_item_cd_keys_status($order_transaction->delivery_id, $order_transaction->transactionID, "true", $order_transaction->QuantityPurchased);			   
		  if($result==false):
		   /*Notify to admin - There is not enought keys available*/	
			$no_in_stock["Title"] = str_replace("?", "-", $order_transaction->Title);				
		 endif;	
		 endif;
		endforeach;
		if(sizeof($no_in_stock)>0):
				  $message = "There are items out of stock:<br><br>";
				  foreach($no_in_stock as $title):
				  $message .= "- ".$title."<br>";
				  endforeach;				  
				  $response["message"] = $message;
				  $response["error"] = true;
				 else:
				  /**/
				  $response["message"] = "Order has been shipped and completed.";
				  $this->Orders_model->update_shipped_status($this->input->post("id"));
				  $response["action"] = "send_invoice_keys_to_buyer";
				endif;
				else:
				$response["action"] = "send_invoice_keys_to_buyer";
				$response["message"] = "Sending Invoice and keys to yor PayPal eMail...";
				
		endif;
		
		echo json_encode($response);
		endif;		
	}
	
}
 