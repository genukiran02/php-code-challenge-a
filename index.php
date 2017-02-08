<?php

require('vendor/autoload.php');

//We will show the Swagger UI on / for now
if (isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/') {
    readfile(__DIR__ . '/static/swagger-ui/index.html');
    exit;
}

//Quick hasck so that the REST library doesn't remove the last part of the API
$_SERVER['REQUEST_URI'] .= '.json';


//init controller
$adapter = new \Ivory\HttpAdapter\Guzzle6HttpAdapter();
$geolocation = new \Korri\Requester\Geolocation($adapter);

$server = new Jacwright\RestServer\RestServer('debug');
$server->refreshCache(); // uncomment momentarily to clear the cache if classes change in production mode

$server->addClass(new Korri\Controller($geolocation));
$server->handle();