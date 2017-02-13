<?php

namespace Provider\RestApi;

use Doctrine\Common\Cache\FilesystemCache;
use Pimple\ServiceProviderInterface;
use Pimple\Container;
use DDesrosiers\SilexAnnotations\AnnotationServiceProvider;

class ControllerAnnotationsProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        $app->register(new AnnotationServiceProvider(), [
            'annot.cache' => new FilesystemCache($app['project.root'].'/var/cache/annotations'),
            'annot.controllerDir' => $app['project.root'].'/src/App/Controller',
            'annot.controllerNamespace' => 'App\\Controller\\',
        ]);
    }
}
