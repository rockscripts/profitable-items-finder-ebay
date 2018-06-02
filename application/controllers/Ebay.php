<?php
 error_reporting(true);

require_once 'ebaycalls/GetOrders.php';

class Ebay extends CI_Controller 
{
   public $token;     
   
   public function __construct() 
   {
      parent::__construct(); 
   }
   
   public function get_orders()
   {
     $GetOrders = new GetOrders();  
     $orders = $GetOrders->get_orders();
     return $orders;
   }       
           
}
?>