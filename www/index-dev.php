<?php

require_once '../vendor/autoload.php';

$app = new RestApiApplication([
    'project.root' => dirname(__DIR__),
    'env' => 'docker',
    'debug' => true,
]);

$app->run();
