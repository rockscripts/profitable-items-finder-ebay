<?php
//error_reporting(true);
/**
 * sources
 */
require_once 'setincludepath.php';
require_once 'AddMemberMessageAAQToPartnerRequestType.php';

class MessagesAAQToPartner extends EbatNs_Environment
{
	
   public function send_message_to_buyer($params)
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
		$request = new AddMemberMessageAAQToPartnerRequestType();
		$request->setSenderID($params["SenderID"]);		
		$request->setItemID($params["ItemID"]);
		$MemberMessage = new MemberMessageType();
		$MemberMessage->setQuestionType("Shipping");
		$MemberMessage->setSubject($params["Subject"]);
		$MemberMessage->setBody($params["Body"]);
		$MemberMessage->addRecipientID($params["BuyerUserID"]);
		$MemberMessage->setRecipientID($params["BuyerUserID"]);
		$request->setMemberMessage($MemberMessage); 
		
		$res = $this->proxy->addMemberMessageAAQtoPartner($request);
		return $res;
    }

}


?>

