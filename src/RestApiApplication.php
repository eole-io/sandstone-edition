<?php

namespace App;

use Silex\Provider;
use Eole\Sandstone\Push\Debug\PushServerProfilerServiceProvider;
use App\Controller\HelloController;
use App\ControllerProvider\HelloControllerProvider;
use App\Event\HelloEvent;

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
        $this->mountControllers();

        if ($this['debug']) {
            $this->registerWebProfiler();
        }

        $this->forwardEventToPushServer(HelloEvent::HELLO);
    }

    private function registerDefaultProviders()
    {
        $this->register(new Provider\ServiceControllerServiceProvider());

        $this->register(new Provider\HttpCacheServiceProvider(), array(
            'http_cache.cache_dir' => $this['project.root'].'/var/cache/http-cache/',
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
    }

    private function mountControllers()
    {
        $this['app.controllers.hello'] = function () {
            return new HelloController($this['dispatcher']);
        };

        $this->mount('api', new HelloControllerProvider());
    }
}
