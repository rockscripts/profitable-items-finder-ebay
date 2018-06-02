<?php
/**
 * Copyright 2014 David T. Sadler
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace DTS\eBaySDK\Finding\Services;

/**
 * Base class for the Finding service.
 */
class FindingBaseService extends \DTS\eBaySDK\Services\BaseService
{
    /**
     * @var string Current version of the SDK
     */
    const VERSION = '0.2.2';

    /**
     * Constants for the various HTTP headers required by the API.
     */
    const HDR_API_VERSION = 'X-EBAY-SOA-SERVICE-VERSION';
    const HDR_APP_ID = 'X-EBAY-SOA-SECURITY-APPNAME';
    const HDR_CONTENT_TYPE = 'CONTENT-TYPE';
    const HDR_GLOBAL_ID = 'X-EBAY-SOA-GLOBAL-ID';
    const HDR_MESSAGE_ENCODING = 'X-EBAY-SOA-MESSAGE-ENCODING';
    const HDR_MESSAGE_PROTOCOL = 'X-EBAY-SOA-MESSAGE-PROTOCOL';
    const HDR_OPERATION_NAME = 'X-EBAY-SOA-OPERATION-NAME';
    const HDR_REQUEST_FORMAT = 'X-EBAY-SOA-REQUEST-DATA-FORMAT';
    const HDR_RESPONSE_FORMAT = 'X-EBAY-SOA-RESPONSE-DATA-FORMAT';

    /**
     * @param array $config Optional configuration option values.
     * @param \DTS\eBaySDK\Interfaces\HttpClientInterface $httpClient The object that will handle sending requests to the API.
     */
    public function __construct($config = array(), \DTS\eBaySDK\Interfaces\HttpClientInterface $httpClient = null)
    {
        if (!array_key_exists(get_called_class(), self::$configOptions)) {
            self::$configOptions[get_called_class()] = array(
                'apiVersion' => array('required' => false),
                'appId' => array('required' => true),
                'globalId' => array('required' => false)
            );
        }

        parent::__construct('http://svcs.ebay.com/services/search/FindingService/v1', 'http://svcs.sandbox.ebay.com/services/search/FindingService/v1', $config, $httpClient);
    }

    /**
     * Build the needed eBay HTTP headers.
     *
     * @param string $operationName The name of the operation been called.
     *
     * @return array An associative array of eBay http headers.
     */
    protected function getEbayHeaders($operationName)
    {
        $headers = array();

        // Add required headers first.
        $headers[self::HDR_APP_ID] = $this->config('appId');
        $headers[self::HDR_OPERATION_NAME] = $operationName;

        // Add optional headers.
        if ($this->config('apiVersion')) {
            $headers[self::HDR_API_VERSION] = $this->config('apiVersion');
        }

        if ($this->config('globalId')) {
            $headers[self::HDR_GLOBAL_ID] = $this->config('globalId');
        }

        return $headers;
    }
}
