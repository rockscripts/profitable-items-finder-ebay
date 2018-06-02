<?php
//error_reporting(true);
/**
 * sources
 */
require_once 'setincludepath.php';
require_once 'GetItemRequestType.php';
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
class GetItem extends EbatNs_Environment
{
public $Array_itemsID;

   public function get_item()
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
        $req = new GetItemRequestType();
        $req->setItemID($params['ItemID']);
        $req->setIncludeItemSpecifics(true);
        $req->DetailLevel = "ReturnAll";        
        $res = $this->proxy->GetItem($req);
        if ($this->testValid($res))
        return ($res);
        else 
            return (false);
    }

}


?>

