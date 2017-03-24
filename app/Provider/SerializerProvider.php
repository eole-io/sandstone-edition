<?php

namespace Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Eole\Sandstone\Serializer\ServiceProvider as SerializerServiceProvider;

class SerializerProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        // Sandstone requires JMS serializer
        $app->register(new SerializerServiceProvider());

        // Register serializer metadata
        $app['serializer.builder']
            ->addMetadataDir($app['project.root'].'/src/App/Resources/serializer')
            ->setCacheDir($app['project.root'].'/var/cache/serializer')
        ;
    }
}
