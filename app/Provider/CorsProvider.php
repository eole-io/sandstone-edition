<?php

namespace Provider;

use Pimple\ServiceProviderInterface;
use Pimple\Container;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use JDesrosiers\Silex\Provider\CorsServiceProvider;

class CorsProvider implements ServiceProviderInterface
{
    /**
     * {@InheritDoc}
     */
    public function register(Container $app)
    {
        $app->register(new CorsServiceProvider(), $app['environment']['cors']);
        $app->after($app['cors']);

        $app->on(KernelEvents::REQUEST, function (GetResponseEvent $event) use ($app) {
            $request = $event->getRequest();

            if ($this->isPreflightRequest($request)) {
                $response = new Response();

                $response->setStatusCode(Response::HTTP_OK);
                $response->headers->add([
                    'Access-Control-Allow-Headers' => $request->headers->get('Access-Control-Request-Headers'),
                    'Access-Control-Allow-Methods' => !is_null($app['cors.allowMethods']) ? $app['cors.allowMethods'] : $request->headers->get('Access-Control-Request-Method'),
                    'Access-Control-Max-Age' => $app['cors.maxAge'],
                    'Access-Control-Allow-Origin' => $app['cors.allowOrigin'],
                    'Access-Control-Allow-Credentials' => true === $app['cors.allowCredentials'] ? 'true' : null,
                ]);

                $event->setResponse($response);
            }
        }, 128);
    }

    /**
     * Detect if a request is a preflight request (cors).
     *
     * @param Request $request
     *
     * @return bool
     */
    private function isPreflightRequest(Request $request)
    {
        return $request->isMethod(Request::METHOD_OPTIONS) && $request->headers->has('Access-Control-Request-Method');
    }
}
