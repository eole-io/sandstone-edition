<?php

namespace Provider\RestApi;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Sorien\Provider\DoctrineProfilerServiceProvider;
use Silex\Provider;
use Eole\Sandstone\Push\Debug\PushServerProfilerServiceProvider;

class WebProfilerProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        $app->register(new Provider\HttpFragmentServiceProvider());
        $app->register(new Provider\TwigServiceProvider());

        $app->register(new Provider\WebProfilerServiceProvider(), array(
            'profiler.cache_dir' => $app['project.root'].'/var/cache/profiler',
            'profiler.mount_prefix' => '/_profiler',
        ));

        $app->register(new PushServerProfilerServiceProvider());
        $app->register(new DoctrineProfilerServiceProvider());
    }
}
