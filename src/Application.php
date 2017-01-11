<?php

namespace App;

use Symfony\Component\HttpKernel\KernelEvents;
use Alcalyn\SerializableApiResponse\ApiResponseFilter;
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

        $this->loadEnvironment();
        $this->registerSerializer();
        $this->registerWebsocketServer();
        $this->registerPushServer();
        $this->registerApiResponse();
    }

    private function loadEnvironment()
    {
        if (!isset($this['project.root'])) {
            throw new \LogicException('project.root must be defined.');
        }

        if (!isset($this['env'])) {
            throw new \LogicException('env must be defined.');
        }

        $this['environment'] = require $this['project.root'].'/config/environment-'.$this['env'].'.php';
    }

    private function registerSerializer()
    {
        // Sandstone requires JMS serializer
        $this->register(new \Eole\Sandstone\Serializer\ServiceProvider());

        // Register serializer metadata
        $this['serializer.builder']
            ->addMetadataDir($this['project.root'].'/src/Serializer')
            ->setCacheDir($this['project.root'].'/var/cache/serializer')
        ;
    }

    private function registerWebsocketServer()
    {
        // Register and configure your websocket server
        $this->register(new \Eole\Sandstone\Websocket\ServiceProvider(), [
            'sandstone.websocket.server' => [
                'bind' => $this['environment']['websocket']['server']['bind'],
                'port' => $this['environment']['websocket']['server']['port'],
            ],
        ]);
    }

    private function registerPushServer()
    {
        // Register Push Server and ZMQ bridge extension
        $this->register(new \Eole\Sandstone\Push\ServiceProvider(), [
            'sandstone.push.enabled' => $this['environment']['push']['enabled'],
        ]);

        $this->register(new \Eole\Sandstone\Push\Bridge\ZMQ\ServiceProvider(), [
            'sandstone.push.server' => [
                'bind' => $this['environment']['push']['server']['bind'],
                'host' => $this['environment']['push']['server']['host'],
                'port' => $this['environment']['push']['server']['port'],
            ],
        ]);
    }

    private function registerApiResponse()
    {
        // Register reponse filter as a service
        $this['acme.listener.api_response_filter'] = function () {
            $serializer = $this['serializer'];

            return new ApiResponseFilter($serializer);
        };

        // Listen Kernel response to convert ApiResponse with raw object to Symfony Response with serialized data
        $this->on(KernelEvents::VIEW, function ($event) {
            $this['acme.listener.api_response_filter']->onKernelView($event);
        });
    }
}
