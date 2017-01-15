<?php

namespace App;

use Silex\Provider;
use Eole\Sandstone\Push\Debug\PushServerProfilerServiceProvider;
use App\HelloProvider\HelloControllerProvider;

class RestApiApplication extends Application
{
    /**
     * Initialize Sandstone application.
     *
     * {@Inheritdoc}
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->registerDefaultProviders();

        if ($this['debug']) {
            $this->registerWebProfiler();
        }

        $helloControllerProvider = new HelloControllerProvider();

        $this->register($helloControllerProvider);
        $this->mount('api', $helloControllerProvider);
    }

    private function registerDefaultProviders()
    {
        $this->register(new Provider\ServiceControllerServiceProvider());

        $this->register(new Provider\HttpCacheServiceProvider(), array(
            'http_cache.cache_dir' => $this['project.root'].'/var/cache/http-cache',
        ));
    }

    private function registerWebProfiler()
    {
        $this->register(new Provider\HttpFragmentServiceProvider());
        $this->register(new Provider\TwigServiceProvider());

        $this->register(new Provider\WebProfilerServiceProvider(), array(
            'profiler.cache_dir' => $this['project.root'].'/var/cache/profiler',
            'profiler.mount_prefix' => '/_profiler',
        ));

        $this->register(new PushServerProfilerServiceProvider());
        $this->register(new \Sorien\Provider\DoctrineProfilerServiceProvider());
    }
}
