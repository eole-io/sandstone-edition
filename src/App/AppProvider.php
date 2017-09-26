<?php

namespace App;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

class AppProvider implements ServiceProviderInterface
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
                'path' => $app['project.root'].'/src/App/Entity',
            ];

            return $mappings;
        });

        $app['serializer.builder']
            ->addMetadataDir($app['project.root'].'/src/App/Resources/serializer')
        ;
    }
}
