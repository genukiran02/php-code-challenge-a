<?php

require('vendor/autoload.php');

$adapter = new \Ivory\HttpAdapter\Guzzle6HttpAdapter();
$geocoder = new \Korri\Geocoder\Provider\IpApi($adapter);

print_r($geocoder->geocode('66.130.182.186'));