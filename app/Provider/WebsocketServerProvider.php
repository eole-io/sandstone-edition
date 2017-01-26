<?php

namespace Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Eole\Sandstone\Websocket\ServiceProvider as WebsocketServiceProvider;

class WebsocketServerProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        // Register and configure your websocket server
        $app->register(new WebsocketServiceProvider(), [
            'sandstone.websocket.server' => $app['environment']['websocket']['server'],
        ]);
    }
}
