<?php

class WebsocketApplication extends Application
{
    /**
     * Initialize Sandstone application.
     *
     * {@Inheritdoc}
     */
    public function __construct(array $values = [])
    {
        parent::__construct($values);

        $this->registerUserProviders();
    }

    /**
     * Here is user application providers for websocket container.
     * Register topics...
     */
    private function registerUserProviders()
    {
        $this->register(new App\AppWebsocketProvider());
    }
}
