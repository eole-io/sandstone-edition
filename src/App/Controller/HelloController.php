<?php

namespace App\Controller;

use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Response;
use Alcalyn\SerializableApiResponse\ApiResponse;
use App\Event\HelloEvent;

class HelloController
{
    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param EventDispatcherInterface $dispatcher
     */
    public function __construct(EventDispatcherInterface $dispatcher)
    {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Test endpoint which returns a hello world.
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

        $this->dispatcher->dispatch(HelloEvent::HELLO, new HelloEvent($name));

        return new ApiResponse($result, Response::HTTP_OK);
    }
}
