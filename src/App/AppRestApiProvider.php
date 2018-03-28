<?php

namespace App;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use App\Event\HelloEvent;

class AppRestApiProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        $app->forwardEventToPushServer(HelloEvent::HELLO);
    }
}
