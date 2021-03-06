<?php
// autogenerated file 29.12.2011 15:00
// $Id: $
// $Log: $
//
//
require_once 'MemberMessageType.php';
require_once 'AbstractRequestType.php';
require_once 'ItemIDType.php';


/**
 * Enables a buyer and seller in an order relationship tosend messages to each 
 * other's My Messages Inboxes. 
 *
 * @link http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/AddMemberMessageAAQToPartnerRequestType.html
 *
 */
class AddMemberMessageAAQToPartnerRequestType extends AbstractRequestType
{
	/**
	 * @var ItemIDType
	 */
	protected $ItemID;
	/**
	 * @var MemberMessageType
	 */
	protected $MemberMessage;
	/**
	 * @var MemberMessageType
	 */
	protected $Subject;
	/**
	 * @var MemberMessageType
	 */
	protected $SenderID;
	/**
	 * @var MemberMessageType
	 */
	protected $Body;
	
	protected $RecipientID;
    protected $QuestionType;


	function getQuestionType()
	{
		return $this->QuestionType;
	}
	/**
	 * @return void
	 * @param QuestionTypeCodeType $value 
	 */
	function setQuestionType($value)
	{
		$this->QuestionType = $value;
	}
	function setRecipientID($value, $index = null)
	{
		if ($index !== null) {
			$this->RecipientID[$index] = $value;
		} else {
			$this->RecipientID = $value;
		}
	}
	/**
	 * @return void
	 * @param string $value 
	 */
	function addRecipientID($value)
	{
		$this->RecipientID[] = $value;
	}
	function getBody()
	{
		return $this->Body;
	}
	/**
	 * @return void
	 * @param string $value 
	 */
	function setBody($value)
	{
		$this->Body = $value;
	}
	function getSenderID()
	{
		return $this->SenderID;
	}
	function setSenderID($value)
	{
		$this->SenderID = $value;
	}

	function getSubject()
	{
		return $this->Subject;
	}
	function setSubject($value)
	{
		$this->Subject = $value;
	}
	/**
	 * @return ItemIDType
	 */
	function getItemID()
	{
		return $this->ItemID;
	}
	/**
	 * @return void
	 * @param ItemIDType $value 
	 */
	function setItemID($value)
	{
		$this->ItemID = $value;
	}
	/**
	 * @return MemberMessageType
	 */
	function getMemberMessage()
	{
		return $this->MemberMessage;
	}
	/**
	 * @return void
	 * @param MemberMessageType $value 
	 */
	function setMemberMessage($value)
	{
		$this->MemberMessage = $value;
	}
	/**
	 * @return 
	 */
	function __construct()
	{
		parent::__construct('AddMemberMessageAAQToPartnerRequestType', 'urn:ebay:apis:eBLBaseComponents');
		if (!isset(self::$_elements[__CLASS__]))
				self::$_elements[__CLASS__] = array_merge(self::$_elements[get_parent_class()],
				array(
					'ItemID' =>
					array(
						'required' => false,
						'type' => 'ItemIDType',
						'nsURI' => 'urn:ebay:apis:eBLBaseComponents',
						'array' => false,
						'cardinality' => '0..1'
					),
					'MemberMessage' =>
					array(
						'required' => false,
						'type' => 'MemberMessageType',
						'nsURI' => 'urn:ebay:apis:eBLBaseComponents',
						'array' => false,
						'cardinality' => '0..1'
					)
				));
	}
}
?>
