<?php

namespace App\ControllerProvider;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class HelloControllerProvider implements ControllerProviderInterface
{
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
