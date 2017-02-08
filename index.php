<?php

require('vendor/autoload.php');

//We will show the Swagger UI on / for now
if (isset($_SERVER['REQUEST_URI'])) {
    if($_SERVER['REQUEST_URI'] == '/') {
        readfile(__DIR__ . '/static/swagger-ui/index.html');
        exit;
    }
    if(substr($_SERVER['REQUEST_URI'], 0, 8) == '/static/' || $_SERVER['REQUEST_URI'] == '/swagger.json') {
        //Serve static files
        return false;
    }
}
//Quick hasck so that the REST library doesn't remove the last part of the API
$_SERVER['REQUEST_URI'] = preg_replace('/\.(\w+)($|\?)/i', '.$1.json$2', $_SERVER['REQUEST_URI']);


//init controller
$adapter = new \Ivory\HttpAdapter\Guzzle6HttpAdapter();
$geolocation = new \Korri\Requester\Geolocation($adapter);
$weather = new \Korri\Requester\Weather($adapter);
$controller = new Korri\Controller($geolocation, $weather);

$server = new Jacwright\RestServer\RestServer('debug');
$server->addClass($controller);
$server->handle();