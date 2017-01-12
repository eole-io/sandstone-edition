<?php

namespace App\HelloProvider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

class HelloServiceProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        $app->extend('doctrine.mappings', function ($mappings, $app) {
            $mappings []= [
                'type' => 'annotation',
                'namespace' => 'App\\Entity',
                'path' => $app['project.root'].'/src/Entity',
            ];

            return $mappings;
        });
    }
}
