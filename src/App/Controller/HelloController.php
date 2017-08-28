<?php

namespace App\Controller;

use Pimple\Container;
use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Alcalyn\SerializableApiResponse\ApiResponse;
use App\Event\HelloEvent;

/**
 * @SLX\Controller(prefix="/api")
 */
class HelloController
{
    /**
     * @var Container
     */
    private $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Test endpoint which returns a hello world.
     *
     * @SLX\Route(
     *      @SLX\Request(method="GET", uri="hello/{name}"),
     *      @SLX\Value(variable="name", default="world")
     * )
     *
     * @param string $name
     *
     * @return ApiResponse
     */
    public function getHello($name)
    {
        $result = [
            'hello' => $name,
        ];

        $result['articles'] = $this->container['orm.em']->getRepository('App\\Entity\\Article')->findAll();

        $this->container['dispatcher']->dispatch(HelloEvent::HELLO, new HelloEvent($name));

        return new ApiResponse($result, Response::HTTP_OK);
    }
}
