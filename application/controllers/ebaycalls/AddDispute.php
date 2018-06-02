<?php
//error_reporting(true);
/**
 * sources
 */
require_once 'setincludepath.php';
require_once 'AddDisputeRequestType.php';
require_once 'EbatNs_Environment.php';

class AddDispute extends EbatNs_Environment
{

   public function add_dispute($params)
    {
       return $this->dispatchCall($params);
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
        $req = new AddDisputeRequestType();
		 $req->setDisputeExplanation($params["DisputeExplanation"]);
		 $req->setDisputeReason($params["DisputeReason"]);
		 $req->setOrderLineItemID($params["OrderLineItemID"]);
        $res = $this->proxy->AddDispute($req);
        return $res;
    }

}


?>

