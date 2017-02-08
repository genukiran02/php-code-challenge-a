<?php

require('vendor/autoload.php');

//We will show the Swagger UI on / for now
if(isset($_SERVER['REQUEST_URI']) && $_SERVER['REQUEST_URI'] == '/') {
    readfile(__DIR__ . '/static/swagger-ui/index.html');
}
