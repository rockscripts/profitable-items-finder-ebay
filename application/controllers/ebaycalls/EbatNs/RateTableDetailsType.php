<?php
// autogenerated file 29.12.2011 15:00
// $Id: $
// $Log: $
//
//
require_once 'EbatNs_ComplexType.php';

/**
 * Details pertinent APIs to allow sellerswho have already set-up a shipping rate 
 * table tohave the option to use their Domestic Rate Table withtheir Flat service 
 * listings, and/or use their InternationalRate Table as their International 
 * service type. Note that if you aremodifying or relisting an item (using 
 * ReviseItem or RelistItem), you candelete the existing rate table settings 
 * applied to the item by using theempty tags: &lt;RateTableDetails / &gt;. 
 *
 * @link http://developer.ebay.com/DevZone/XML/docs/Reference/eBay/types/RateTableDetailsType.html
 *
 */
class RateTableDetailsType extends EbatNs_ComplexType
{
	/**
	 * @var string
	 */
	protected $DomesticRateTable;
	/**
	 * @var string
	 */
	protected $InternationalRateTable;

	/**
	 * @return string
	 */
	function getDomesticRateTable()
	{
		return $this->DomesticRateTable;
	}
	/**
	 * @return void
	 * @param string $value 
	 */
	function setDomesticRateTable($value)
	{
		$this->DomesticRateTable = $value;
	}
	/**
	 * @return string
	 */
	function getInternationalRateTable()
	{
		return $this->InternationalRateTable;
	}
	/**
	 * @return void
	 * @param string $value 
	 */
	function setInternationalRateTable($value)
	{
		$this->InternationalRateTable = $value;
	}
	/**
	 * @return 
	 */
	function __construct()
	{
		parent::__construct('RateTableDetailsType', 'urn:ebay:apis:eBLBaseComponents');
		if (!isset(self::$_elements[__CLASS__]))
				self::$_elements[__CLASS__] = array_merge(self::$_elements[get_parent_class()],
				array(
					'DomesticRateTable' =>
					array(
						'required' => false,
						'type' => 'string',
						'nsURI' => 'http://www.w3.org/2001/XMLSchema',
						'array' => false,
						'cardinality' => '0..1'
					),
					'InternationalRateTable' =>
					array(
						'required' => false,
						'type' => 'string',
						'nsURI' => 'http://www.w3.org/2001/XMLSchema',
						'array' => false,
						'cardinality' => '0..1'
					)
				));
	}
}
?>
