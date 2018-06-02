<?php
// autogenerated file 29.12.2011 15:00
// $Id: $
// $Log: $
//
require_once 'EbatNs_FacetType.php';

/**
 * This code identifies the acknowledgement code types thateBay could use to 
 * communicate the status of processing a(request) message to an application. This 
 * code would be usedas part of a response message that contains 
 * anapplication-level acknowledgement element. 
 *
 * @link http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/AckCodeType.html
 *
 * @property string Success
 * @property string Failure
 * @property string Warning
 * @property string PartialFailure
 * @property string CustomCode
 */
class AckCodeType extends EbatNs_FacetType
{
	const CodeType_Success = 'Success';
	const CodeType_Failure = 'Failure';
	const CodeType_Warning = 'Warning';
	const CodeType_PartialFailure = 'PartialFailure';
	const CodeType_CustomCode = 'CustomCode';

	/**
	 * @return 
	 */
	function __construct()
	{
		parent::__construct('AckCodeType', 'urn:ebay:apis:eBLBaseComponents');

	}
}

$Facet_AckCodeType = new AckCodeType();

?>
