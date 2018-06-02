<?php
require __DIR__ . '/artifacts/dts-finding.phar';

$service = new \DTS\eBaySDK\Finding\Services\FindingService(array(
    'apiVersion' => '123'
));

echo 'Version=' . \DTS\eBaySDK\Finding\Services\FindingService::VERSION;
