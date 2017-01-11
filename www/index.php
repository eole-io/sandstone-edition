<?php

ini_set('display_errors', 0);

require_once '../vendor/autoload.php';

use App\RestApiApplication;

$app = new RestApiApplication([
    'project.root' => dirname(__DIR__),
]);

$app['http_cache']->run();
