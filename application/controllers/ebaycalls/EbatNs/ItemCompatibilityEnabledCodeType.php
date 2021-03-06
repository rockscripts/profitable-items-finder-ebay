<?php
// autogenerated file 29.12.2011 15:00
// $Id: $
// $Log: $
//
require_once 'EbatNs_FacetType.php';

/**
 * Used to indicate whether the parts compatibility feature is enabled for a 
 * category.<br><br> Parts Compatibility is supported in limited Parts & 
 * Accessoriescategories for the eBay US Motors (site ID 100) and eBay Germany 
 * (site ID 77) sites only. 
 *
 * @link http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/ItemCompatibilityEnabledCodeType.html
 *
 * @property string Disabled
 * @property string ByApplication
 * @property string BySpecification
 * @property string CustomCode
 */
class ItemCompatibilityEnabledCodeType extends EbatNs_FacetType
{
	const CodeType_Disabled = 'Disabled';
	const CodeType_ByApplication = 'ByApplication';
	const CodeType_BySpecification = 'BySpecification';
	const CodeType_CustomCode = 'CustomCode';

	/**
	 * @return 
	 */
	function __construct()
	{
		parent::__construct('ItemCompatibilityEnabledCodeType', 'urn:ebay:apis:eBLBaseComponents');

	}
}

$Facet_ItemCompatibilityEnabledCodeType = new ItemCompatibilityEnabledCodeType();

?>
