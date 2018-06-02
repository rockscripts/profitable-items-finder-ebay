<?php
// autogenerated file 29.12.2011 15:00
// $Id: $
// $Log: $
//
require_once 'EbatNs_FacetType.php';

/**
 *  
 *
 * @link http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/WarningLevelCodeType.html
 *
 * @property string Low
 * @property string High
 */
class WarningLevelCodeType extends EbatNs_FacetType
{
	const CodeType_Low = 'Low';
	const CodeType_High = 'High';

	/**
	 * @return 
	 */
	function __construct()
	{
		parent::__construct('WarningLevelCodeType', 'urn:ebay:apis:eBLBaseComponents');

	}
}

$Facet_WarningLevelCodeType = new WarningLevelCodeType();

?>
