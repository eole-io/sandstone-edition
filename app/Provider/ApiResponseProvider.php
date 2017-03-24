<?php

namespace Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Symfony\Component\HttpKernel\KernelEvents;
use Alcalyn\SerializableApiResponse\ApiResponseFilter;

class ApiResponseProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        // Register reponse filter as a service
        $app['acme.listener.api_response_filter'] = function () use ($app) {
            $serializer = $app['serializer'];

            return new ApiResponseFilter($serializer);
        };

        // Listen Kernel response to convert ApiResponse with raw object to Symfony Response with serialized data
        $app->on(KernelEvents::VIEW, function ($event) use ($app) {
            $app['acme.listener.api_response_filter']->onKernelView($event);
        });
    }
}
