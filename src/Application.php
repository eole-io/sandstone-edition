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

        if (!isset($this['project.root'])) {
            throw new \LogicException('project.root must be defined.');
        }

        if (!isset($this['project.cache_dir'])) {
            $this['project.cache_dir'] = $this['project.root'].'/var/cache';
        }

        // Sandstone requires JMS serializer
        $this->register(new \Eole\Sandstone\Serializer\ServiceProvider());

        $this['serializer.builder']
            ->addMetadataDir($this['project.root'].'/src/Serializer')
            ->setCacheDir($this['project.cache_dir'].'/serializer')
        ;

        // Register and configure your websocket server
        $this->register(new \Eole\Sandstone\Websocket\ServiceProvider(), [
            'sandstone.websocket.server' => [
                'bind' => '0.0.0.0',
                'port' => '25569',
            ],
        ]);

        // Register Push Server and ZMQ bridge extension
        $this->register(new \Eole\Sandstone\Push\ServiceProvider());
        $this->register(new \Eole\Sandstone\Push\Bridge\ZMQ\ServiceProvider(), [
            'sandstone.push.server' => [
                'bind' => '127.0.0.1',
                'host' => '127.0.0.1',
                'port' => 5555,
            ],
        ]);

        // Register serializer metadata

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
