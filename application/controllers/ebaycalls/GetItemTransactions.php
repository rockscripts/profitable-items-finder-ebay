<?php
//error_reporting(true);
/**
 * sources
 */
require_once 'setincludepath.php';
require_once 'PaginationType.php';
require_once 'ItemListCustomizationType.php';
require_once 'GetItemTransactionsRequestType.php';
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
class GetItemTransactions extends EbatNs_Environment
{

   public function execute($itemID,$data)
    {
       return $this->dispatchCall($itemID,$data);
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
    public function dispatchCall ($itemID, $data)
    { 
        $req = new GetItemTransactionsRequestType();
        $pag = new PaginationType();
        $pag->setEntriesPerPage($data["EntriesPerPage"]);
        $pag->setPageNumber( $data["PageNumber"] );
		$req->setNumberOfDays($data["NumberOfDays"]);	
        $req->setPagination($pag);
		$req->setItemID($itemID);
        $res = $this->proxy->GetItemTransactions($req);
        return $res;
    }
}


?>

