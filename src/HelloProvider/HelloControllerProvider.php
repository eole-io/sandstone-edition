<?php

namespace App\HelloProvider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use App\Controller\HelloController;
use App\Event\HelloEvent;

class HelloControllerProvider implements ServiceProviderInterface, ControllerProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        $app['app.controllers.hello'] = function (Container $app) {
            return new HelloController($app['dispatcher']);
        };

        $app->forwardEventToPushServer(HelloEvent::HELLO);
    }

    /**
     * @param Application $app
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get('/hello/{name}', 'app.controllers.hello:getHello')->value('name', 'world');

        return $controllers;
    }
}
