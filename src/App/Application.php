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
        $this->registerControllerAnnotations();
        $this->registerServices();
        $this->registerDoctrineMappings();
        $this->registerDoctrine();
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
            ->addMetadataDir($this['project.root'].'/src/App/Resources/serializer')
            ->setCacheDir($this['project.root'].'/var/cache/serializer')
        ;
    }

    private function registerWebsocketServer()
    {
        // Register and configure your websocket server
        $this->register(new \Eole\Sandstone\Websocket\ServiceProvider(), [
            'sandstone.websocket.server' => $this['environment']['websocket']['server'],
        ]);
    }

    private function registerPushServer()
    {
        // Register Push Server and ZMQ bridge extension
        $this->register(new \Eole\Sandstone\Push\ServiceProvider(), [
            'sandstone.push.enabled' => $this['environment']['push']['enabled'],
        ]);

        $this->register(new \Eole\Sandstone\Push\Bridge\ZMQ\ServiceProvider(), [
            'sandstone.push.server' => $this['environment']['push']['server'],
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

    private function registerControllerAnnotations()
    {
        $this->register(new \DDesrosiers\SilexAnnotations\AnnotationServiceProvider(), [
            'annot.cache' => new \Doctrine\Common\Cache\FilesystemCache($this['project.root'].'/var/cache/annotations'),
            'annot.controllerDir' => $this['project.root'].'/src/App/Controller',
            'annot.controllerNamespace' => 'App\\Controller\\',
        ]);
    }

    private function registerServices()
    {
        $this['doctrine.mappings'] = function () {
            return [];
        };
    }

    private function registerDoctrineMappings()
    {
        $this->extend('doctrine.mappings', function ($mappings, $app) {
            $mappings []= [
                'type' => 'annotation',
                'namespace' => 'App\\Entity',
                'path' => $app['project.root'].'/src/App/Entity',
            ];

            return $mappings;
        });
    }

    private function registerDoctrine()
    {
        $this->register(new \Silex\Provider\DoctrineServiceProvider(), [
            'db.options' => $this['environment']['database']['connection'],
        ]);

        $this->register(new \Dflydev\Provider\DoctrineOrm\DoctrineOrmServiceProvider(), [
            'orm.proxies_dir' => $this['project.root'].'/var/cache/doctrine-proxies',
            'orm.auto_generate_proxies' => $this['environment']['database']['orm']['auto_generate_proxies'],
            'orm.em.options' => [
                'mappings' => $this['doctrine.mappings'],
            ],
        ]);
    }
}
