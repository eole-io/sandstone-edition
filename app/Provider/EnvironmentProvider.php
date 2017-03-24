<?php

namespace Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;

class EnvironmentProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        if (!isset($app['project.root'])) {
            throw new \LogicException('project.root must be defined.');
        }

        if (!isset($app['env'])) {
            throw new \LogicException('env must be defined.');
        }

        $app['environment'] = require $app['project.root'].'/config/environment-'.$app['env'].'.php';
    }
}
