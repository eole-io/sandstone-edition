<?php

namespace Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Silex\Provider\DoctrineServiceProvider;
use Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider;

class DoctrineProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        $app->register(new DoctrineServiceProvider(), [
            'db.options' => $app['environment']['database']['connection'],
        ]);

        $app->register(new DoctrineOrmServiceProvider(), [
            'orm.proxies_dir' => $app['project.root'].'/var/cache/doctrine-proxies',
            'orm.auto_generate_proxies' => $app['environment']['database']['orm']['auto_generate_proxies'],
            'orm.em.options' => [
                'mappings' => $app['doctrine.mappings'],
            ],
        ]);
    }
}
