<?php
//error_reporting(true);
/**
 * sources
 */
require_once 'setincludepath.php';
require_once 'PaginationType.php';
require_once 'ItemListCustomizationType.php';
require_once 'GetOrdersRequestType.php';
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
class GetOrders extends EbatNs_Environment
{
public $Array_itemsID;

   public function get_orders()
    {
       return $this->dispatchCall($this->Array_itemsID);
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
    public function dispatchCall ($params)
    {
        $req = new GetOrdersRequestType();
        $pag = new PaginationType();
        $pag->setEntriesPerPage(200);
        $pag->setPageNumber( 1 );
        $req->setPagination($pag);
        $req->setNumberOfDays(3);		
        $req->getOrderIDArray();
        $req->setOrderStatus("Completed");
		$req->DetailLevel = "ReturnAll"; 
        $res = $this->proxy->GetOrders($req);
        return $res;
    }

}


?>

