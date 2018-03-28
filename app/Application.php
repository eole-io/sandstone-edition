<?php

use Eole\Sandstone\Application as BaseApplication;

class Application extends BaseApplication
{
    /**
     * Initialize Sandstone application.
     *
     * {@Inheritdoc}
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->register(new Provider\EnvironmentProvider());
        $this->register(new Provider\SerializerProvider());
        $this->register(new Provider\ApiResponseProvider());
        $this->register(new Provider\WebsocketServerProvider());
        $this->register(new Provider\PushProvider());
        $this->register(new Provider\ServiceProvider());

        $this->registerUserProviders();

        $this->register(new Provider\DoctrineProvider());
    }

    /**
     * Here is user application providers for both RestApi and websocket containers.
     * Register services, doctrine mappings...
     */
    private function registerUserProviders()
    {
        $this->register(new App\AppProvider());
    }
}
