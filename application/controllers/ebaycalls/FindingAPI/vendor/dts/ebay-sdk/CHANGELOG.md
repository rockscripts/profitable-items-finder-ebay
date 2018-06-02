# CHANGELOG

## 0.1.6 - 2015-08-05

### Feature

Assigning values when instatiating an object has been improved. It is now possible to simply pass an associative array of property names and values. When a property expects an object as its value you can just pass another associative array instead. For example,

```
$variation = new Types\VariationType(array(
    'SKU' => 'TS-W-S',
    'Quantity' => 5,
    'StartPrice' => ['value' => 10.99],
    'VariationSpecifics' => [[
        'NameValueList' => [
            ['Name' => 'Color', 'Value' => ['White']],
            ['Name' => "Size (Men's)", 'Value' => ['S']]
        ]
    ]]
));
```

This feature is compatiable with the existing method and so you can mix and match as in the example below. 

```
$variation = new Types\VariationType(array(
    'SKU' => 'TS-W-S',
    'Quantity' => 5,
    'StartPrice' => new Types\AmountType(['value' => 10.99]),
    'VariationSpecifics' => [[
        'NameValueList' => [
            new Types\NameValueListType(['Name' => 'Color', 'Value' => ['White']]),
            ['Name' => "Size (Men's)", 'Value' => ['S']]
        ]
    ]]
));
```

## 0.1.5 - 2015-08-02

### END OF LIFE NOTICE

This repository is now deprecated and will reach its end of life on the 6th December 2015. It is only maintained for bug fixes. A new version of the SDK has been released and is available at [https://github.com/davidtsadler/ebay-sdk-php](https://github.com/davidtsadler/ebay-sdk-php). A guide can be found at [http://devbay.net/sdk/guides/migration/](http://devbay.net/sdk/guides/migration/) to help migrate existing projects to the new repository.

## 0.1.4 - 2015-06-28

### Features

* Allow client code to access the HttpClient object used to send requests.

  You can now call the `httpClient` method on a service object. This will
  return an object that implments the \DTS\eBaySDK\Interfaces\HttpClientInterface. 
  This object is responsible for the HTTP request to the API. By default 
  the SDK will use a \DTS\eBaySDK\HttpClient\HttpClient object.

  ```php  
  $service = new Services\TradingService(array(
    'apiVersion' => 925,
    'siteId' => Constants\SiteIds::US
  ));

  $httpClient = $service->httpClient();
  ```

  If you provide your own HTTP client then calling `httpClient` will return
  the same instance.

  ```php
  class MockClient implements \DTS\eBaySDK\Interfaces\HttpClientInterface
  {
    public function post($url, $headers, $body)
    {
        print_r($headers);
        print($body);
    }
  }

  $mock = new MockClient();
  
  $service = new Services\TradingService(array(
    'apiVersion' => 925,
    'siteId' => Constants\SiteIds::US
  ), $mock);

  assert('$mock === $service->httpClient()');
  ```

* Allow access to the `Guzzle` object used by the default HTTP client instance.

  You can now call the `guzzle` method on any instances of \DTS\eBaySDK\HttpClient\HttpClient 
  This will return a Guzzle 3 \Guzzle\Http\Client instance.

  ```php
  $service = new Services\TradingService(array(
    'apiVersion' => 925,
    'siteId' => Constants\SiteIds::US
  ));

  $guzzle = $service->httpClient()->guzzle();

  $guzzle->getConfig()->setPath('curl.options', array(
      'CURLOPT_VERBOSE' => 1
  ));
  ```

## 0.1.3 - 2015-06-20

### Features

* Add Makefile to help with various tasks.
* Allow object properties to be returned as an associative array.

  ```php
  use \DTS\eBaySDK\Trading\Types;
  use \DTS\eBaySDK\Trading\Enums;

  $item = new Types\ItemType();
  $item->Title = 'An Example';
  $item->Quantity = 99;
  $item->StartPrice = new Types\AmountType(array('value' => 19.99));
  $item->PaymentMethods = array(
      'VisaMC',
      'PayPal'
  );
  $item->ShippingDetails = new Types\ShippingDetailsType();
  $item->ShippingDetails->ShippingType = Enums\ShippingTypeCodeType::C_FLAT;
  $item->ShippingDetails->ShippingServiceOptions[] = new Types\ShippingServiceOptionsType(array(
      'ShippingServicePriority' => 1,
      'ShippingService' => 'Other',
      'ShippingServiceCost' => new Types\AmountType(array('value' => 2.00)),
      'ShippingServiceAdditionalCost' => new Types\AmountType(array('value' => 1.00))
  ));
  $item->ShippingDetails->ShippingServiceOptions[] = new Types\ShippingServiceOptionsType(array(
      'ShippingServicePriority' => 1,
      'ShippingService' => 'USPSParcel',
      'ShippingServiceCost' => new Types\AmountType(array('value' => 3.00)),
      'ShippingServiceAdditionalCost' => new Types\AmountType(array('value' => 2.00))
  ));

  print_r($item->toArray());

  /**
  Array
  (
      [PaymentMethods] => Array
          (
              [0] => VisaMC
              [1] => PayPal
          )

      [Quantity] => 99
      [ShippingDetails] => Array
          (
              [ShippingServiceOptions] => Array
                  (
                      [0] => Array
                          (
                              [ShippingService] => Other
                              [ShippingServiceAdditionalCost] => Array
                                  (
                                      [value] => 1
                                  )

                              [ShippingServiceCost] => Array
                                  (
                                      [value] => 2
                                  )

                              [ShippingServicePriority] => 1
                          )

                      [1] => Array
                          (
                              [ShippingService] => USPSParcel
                              [ShippingServiceAdditionalCost] => Array
                                  (
                                      [value] => 2
                                  )

                              [ShippingServiceCost] => Array
                                  (
                                      [value] => 3
                                  )

                              [ShippingServicePriority] => 1
                          )

                  )

              [ShippingType] => Flat
          )

      [StartPrice] => Array
          (
              [value] => 19.99
          )

      [Title] => An Example
  )
  */
  ```

### Tests

* Add PHP 5.6 and HHVM to travis settings.

## 0.1.2 - 2014-08-25

### Features

* Allow attachments to be sent and received. ([94288e3](https://github.com/davidtsadler/ebay-sdk/commit/94288e3a460d0d52a9cc2b6f2aca0a86130369ec) [David T. Sadler]

  The SDK now allows attachments to be sent as part of the request.
  Likewise attachments are handled if they appear in the response.

  To add an attachment to the request object simply call the `attachment`
  method passing in the binary data of the attachment as the first
  parameter. Note that you do not have to base64 encode the data!

  ```php
  $request->attachment(file_get_contents(__DIR__.'/picture.jpg'));
  ```

  To get the attachment from a response simply call the same method with
  no parmaters. The method will return an associative array with two keys.
  The key 'data' is the binary data of the attachment while the key
  'mimeType' returns the mime type.

  ```php
  $response = $service->downloadFile($request);

  $attachment = $response->attachment();

  $fp = fopen('attachment', 'wb');
  fwrite($fp, $attachment['data']);
  fclose($fp);
  ```

### Documentation

* Update requirements to recommend 64 bit systems. ([150abfa](https://github.com/davidtsadler/ebay-sdk/commit/150abfae02699875f86806fbb274d4ae98089e7f) [David T. Sadler]

## 0.1.1 - 2014-08-14

### Fixes

* Memory leak in XmlParser class. ([8bbd4ff](https://github.com/davidtsadler/ebay-sdk/commit/8bbd4ffde833f13936f1d1607ef559609e706a71), [#5](https://github.com/davidtsadler/ebay-sdk/issues/5)) [David T. Sadler]

## 0.1.0 - 2014-07-05

### Breaking changes

* Change callOperation to accept a request object. ([34c44ba](https://github.com/davidtsadler/ebay-sdk/commit/34c44ba166fc9fcac0656073ed6a68b7c5f97eea)) [David T. Sadler]

  This is a breaking change as the paramters of the method
  BaseService::callOperation have changed. Code calling this method must
  now pass an instance of the BaseType class as the second parameter. The
  method will now construct the XML request within itself by calling the
  BaseType::toRequestXml method on the passed request object.

* Change visibility of method BaseType::toXml. ([999e6f3](https://github.com/davidtsadler/ebay-sdk/commit/999e6f3877fdb4d6cd04e9615772e63b5dd53931)) [David T. Sadler]

  This is a breaking change as the visibility of the method BaseType::toXml has been
  changed from `public` to `private`. Client code should now call the new public method
  BaseType::toRequestXml instead.

  The class property `$requestXmlRootElementNames` has also been added to
  the BaseType class. This is a breaking change as classes derived from
  BaseType may have to assign a value to this property in their
  constructor.

  ```php
  if (!array_key_exists(__CLASS__, self::$requestXmlRootElementNames)) {
      self::$requestXmlRootElementNames[__CLASS__] = '<ELEMENT NAME>';
  }
  ```

  Classes are only required to do this if instances of the class are used
  as request objects. The value assigned to `$requestXmlRootElementNames`
  will be used as the name of the root element in the request XML.

### Documentation

* Correct stated minimum PHP version. ([e5b9a6a](https://github.com/davidtsadler/ebay-sdk/commit/e5b9a6ab3a4eb4a5435be9116c69c797e68d4faf)) [David T. Sadler]

### Tests

* Update travis settings. ([541304c](https://github.com/davidtsadler/ebay-sdk/commit/541304ca8a50d6ea7328967c0d3ab145d8384627)) [David T. Sadler]
* Add phpunit configuration file. ([f95a253](https://github.com/davidtsadler/ebay-sdk/commit/f95a2538b4ca89553f3beda4e1fe1ae3f030a05c)) [David T. Sadler]

## 0.0.7 - 2014-06-25

### Refactoring

*  Make Guzzle client a return string. ([3f4be5b](https://github.com/davidtsadler/ebay-sdk/commit/3f4be5b78230af5db521ef7fc87da86c17f31b22)) [David T. Sadler]
