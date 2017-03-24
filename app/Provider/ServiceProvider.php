<?php

namespace Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

class ServiceProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        $app['doctrine.mappings'] = function () {
            return [];
        };
    }
}
