<?php

require_once '../vendor/autoload.php';

use App\RestApiApplication;

$app = new RestApiApplication([
    'project.root' => dirname(__DIR__),
    'env' => 'docker',
    'debug' => true,
]);

$app->run();
