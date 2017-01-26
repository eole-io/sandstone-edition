<?php

namespace Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Eole\Sandstone\Push\ServiceProvider as PushServiceProvider;
use Eole\Sandstone\Push\Bridge\ZMQ\ServiceProvider as ZMQServiceProvider;

class PushProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        // Register Push Server and ZMQ bridge extension
        $app->register(new PushServiceProvider(), [
            'sandstone.push.enabled' => $app['environment']['push']['enabled'],
        ]);

        $app->register(new ZMQServiceProvider(), [
            'sandstone.push.server' => $app['environment']['push']['server'],
        ]);
    }
}
