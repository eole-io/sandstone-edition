<?php

use Silex\Provider as SilexProvider;

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

        $this->register(new SilexProvider\ServiceControllerServiceProvider());
        $this->register(new SilexProvider\HttpCacheServiceProvider(), array(
            'http_cache.cache_dir' => $this['project.root'].'/var/cache/http-cache',
        ));

        $this->register(new Provider\RestApi\ControllerAnnotationsProvider());
        $this->register(new Provider\RestApi\CorsProvider());

        if ($this['debug']) {
            $this->register(new Provider\RestApi\WebProfilerProvider());
        }

        $this->registerUserProviders();
    }

    /**
     * Here is user RestApi only providers.
     * Mount routes if you don't use annotations, tag events to forward...
     */
    private function registerUserProviders()
    {
        $this->register(new App\AppRestApiProvider());
    }
}
