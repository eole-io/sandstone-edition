Sandstone Fullstack
===================

Build a real-time RestApi !

It uses Silex and websockets.


## Technical stack

This fullstack integrates:

 - [Sandstone](https://eole-io.github.io/sandstone/) (Silex with websockets)
 - **Docker** environment to mount the whole application (RestApi, websocket server, MariaDB, PHPMyAdmin)
 - **Doctrine ORM** and Doctrine commands
 - **Symfony web profiler** for debugging RestApi requests and Push events
 - [Silex annotations](https://github.com/danadesrosiers/silex-annotation-provider) for controllers and routing annotations


## Installation

Sandstone requires PHP 5.5+, ZMQ and php-zmq extension.
But the fullstack also has a Docker installation, so you don't need PHP and ZMQ by this way.


### Normal installation

This requires PHP 5.5+, ZMQ and php-zmq extension.
Check [Install ZMQ and php-zmq on Linux](https://eole-io.github.io/sandstone/install-zmq-php-linux.html).

``` bash
composer create-project eole/sanstone-fullstack
cd sandstone-fullstack/
```

Then go to something like `http://localhost/sandstone-fullstack/www/index-dev.php/api/hello`

Access to the **Silex console**:

``` bash
php bin/console
```

### Docker installation

This installation requires **git**, **Docker** and **docker-compose**.

``` bash
# Clone the repo
git clone https://github.com/eole-io/sandstone-fullstack.git
cd sandstone-fullstack/

# Mount the environment
docker-compose up

# Install composer dependencies
docker exec -ti sandstone-php /bin/bash -c "composer update"
```

Then go to `http://localhost:8088/index-dev.php/api/hello`

Docker runs the whole environment, the RestApi, the websocket server and PHPMyAdmin. You now have access to:

 - `http://localhost:8088/index-dev.php/api/hello`: *hello world* route in **dev** mode.
 - `http://localhost:8088/api/hello`: *hello world* route in **prod** mode.
 - `http://localhost:8088/index-dev.php/_profiler`: Symfony web profiler (only dev mode).
 - `http://localhost:8088/websocket-test.html`: A HTML page which connect to the websocket server and says hello on the chat (check your Javacript console).
 - `http://localhost:8090/`: PHPMyAdmin.

You can now start to create your RestApi endpoints and websocket topics.

Access to the **Silex console**:

``` bash
docker exec -ti sandstone-php /bin/bash -c "php bin/console"
```


#### Docker default ports

Once the environment mounted, Docker exposes these ports:

 - `8088:http` Web server for the RestApi (nginx)
 - `8089:ws` Websocket server
 - `8090:http` PHPMyAdmin instance


## Routes

The fullstack provides the RestApi, the websocket server and a web profiler:

 - `/index-dev.php/api/hello`: *hello world* route in **dev** mode.
 - `/api/hello`: *hello world* route in **prod** mode.
 - `/index-dev.php/_profiler`: Symfony web profiler (only dev mode).
 - `/websocket-test.html`: A HTML page which connect to the websocket server and says hello on the chat (check your Javacript console).
 - `http://localhost:25569/`: The websocket server (the port depends on what you set in your config).


## Cookbook

Once you have a running installation of Sandstone,
you can start to build your real-time RestApi.

That means creating API endpoints, websocket topics...


### Creating an API endpoint

As Sandstone extends Silex, just create a controller class and a method, then mount it with Silex.

Also, this fullstack allows to use **annotations** for routing.

In **src/App/Controller**:
``` php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use DDesrosiers\SilexAnnotations\Annotations as SLX;
use Alcalyn\SerializableApiResponse\ApiResponse;

/**
 * @SLX\Controller(prefix="/api")
 */
class HelloController
{
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

        return new ApiResponse($result, Response::HTTP_OK);
    }
}
```

> **Note**: Using `ApiResponse` allows to make your controller return a HTTP-agnostic object,
> and is better when used with serializer
> (see [Github alcalyn/serializable-api-response](https://github.com/alcalyn/serializable-api-response)).
> Sandstone transform the `ApiResponse` to a Symfony `Response` only at the last time (after serialization).

*Related documentation*:

 - [danadesrosiers/Silex annotations](https://github.com/danadesrosiers/silex-annotation-provider)
 - [Silex routing](http://silex.sensiolabs.org/doc/2.0/usage.html#routing)
 - [ApiResponse](https://github.com/alcalyn/serializable-api-response)

### Creating a websocket topic

A websocket topic is like a "category", or a "channel" of communication.
It allows to listen to messages from a same source,
without receive all messages from the websocket server.

Technically, it also separate each topic in classes,
then each topic has its own logic.

Under Sandstone, a topic has a name (i.e `chat/general`) and can be declared like a route.

#### Creating the topic class

In **src/App/Topic/ChatTopic.php**:
``` php
namespace App\Topic;

use Ratchet\Wamp\WampConnection;
use Eole\Sandstone\Websocket\Topic;

class ChatTopic extends Topic
{
    /**
     * Broadcast message to each subscribing client.
     *
     * {@InheritDoc}
     */
    public function onPublish(WampConnection $conn, $topic, $event)
    {
        $this->broadcast([
            'message' => $event,
        ]);
    }
}
```

#### Register the topic

In **src/App/WebsocketApplication**:
``` php
use App\Topic\ChatTopic;

$this->topic('chat/{channel}', function ($topicPattern) {
    return new ChatTopic($topicPattern);
});
```

Then you can now subscribe to `chat/general`, `chat/private`, `chat/whatever`, ...

Sandstone `Topic` class extends `Ratchet\Wamp\Topic`,
which is based on Wamp protocol.

Note that you can use all Silex route configuration like:

``` php
$this
    ->topic('chat/{channel}', function ($topicPattern) {
        return new ChatTopic($topicPattern);
    })
    ->value('channel', 'general')                   // Set a default channel name in case someone subscribes to `chat`
    ->assert('channel', '[a-z]')                    // Add constraint on channel name, only lowercases
    ->convert('channel', function () { /* ... */ }) // Add a converter on channel name
;
```

> **Note** also that you can't use `->method('get')` or `->requireHttps()` for a topic route ;)

#### Retrieve route arguments from topic name

In case your topic name is something like `chat/{channel}`
and you need to pass the `{channel}` argument to your Topic class:

``` php
$this->topic('chat/{channel}', function ($topicPattern, $arguments) {
    $channelName = $arguments['channel'];

    return new ChatTopic($topicPattern, $channelName);
});
```

*Related documentation*:

 - Silex routing: [http://silex.sensiolabs.org/doc/2.0/usage.html](http://silex.sensiolabs.org/doc/2.0/usage.html).
 - Wamp protocol implementation on RatchetPHP: [http://socketo.me/docs/wamp](http://socketo.me/docs/wamp).
 - Wamp protocol: [http://wamp-proto.org/](http://wamp-proto.org/).


### Send a Push event from RestApi to a websocket topic

Sometimes you'll want to notify websocket clients when the RestApi state changes
(a new resource has been PUT or POSTed, or a resource has been PATCHed...).

Then this part is for you.

The logic here is to dispatch an event from the controller behind i.e `postArticle`,
then this event will be forwarded (i.e redisptached) over the `WebsocketApplication`.

Then just listen this event from a topic, and do something like broadcast a message...

#### Dispatch event from controller

In **src/App/Controller/HelloController.php**:
``` php
public function getHello($name)
{
    $this->container['dispatcher']->dispatch(HelloEvent::HELLO, new HelloEvent($name));
}
```

> **Note**: The `container` is passed to your controllers constructors
> if you use the `@SLX\Controller` annotation.

#### Mark the event to be forwarded

In **src/App/RestApiApplication**:
``` php
$app->forwardEventToPushServer(HelloEvent::HELLO);
```

> **Note**: This must be done only in RestApi stack.
> If it's done in websocket stack, the event will be redispatched infinitely to itself!

#### Listen the event from a topic

It will listen and receive the event
that has been serialized/deserialized through the Push server,
from the RestApi thread to the websocket server thread.

In **src/App/Topic/ChatTopic**:
``` php
namespace App\Topic;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Ratchet\Wamp\WampConnection;
use Eole\Sandstone\Websocket\Topic;
use App\Event\HelloEvent;

class ChatTopic extends Topic implements EventSubscriberInterface
{
    /**
     * Subscribe to article.created event.
     *
     * {@InheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            HelloEvent::HELLO => 'onHello',
        ];
    }

    /**
     * Article created listener.
     *
     * @param HelloEvent $event
     */
    public function onHello(HelloEvent $event)
    {
        $this->broadcast([
            'message' => 'Someone called api/hello. Hello '.$event->getName(),
        ]);
    }
}
```

> **Note**: Sandstone automatically subscribes topics (to the EventDispatcher)
> that implement the `Symfony\Component\EventDispatcher\EventSubscriberInterface`.

Up to you to create a `HelloEvent` class, create serialization metadata for it...

*Related documentation*:

 - [Symfony EventSubscriber](http://symfony.com/doc/current/components/event_dispatcher.html#using-event-subscribers)
 - [Sandstone Topic class](https://eole-io.github.io/sandstone/examples/multichannel-chat.html)


### Doctrine

Sandstone fullstack integrates Doctrine:

 - Doctrine DBAL and ORM are installed,
 - you can use entities with annotations or yaml mapping, the `orm:schema-tool`...
 - Doctrine commands are available under `php bin/console`
 - Entities serialization is well handled (fixes relations infinite loops). See [serializer-doctrine-proxies](https://github.com/alcalyn/serializer-doctrine-proxies).

#### Creating an entity

Here using annotations. In **src/App/Entity/Article.php**:

``` php
namespace App\Entity;

/**
 * @Entity
 */
class Article
{
    /**
     * @var int
     *
     * @Id
     * @Column(type="integer")
     * @GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @Column(type="string")
     */
    private $title;

    /**
     * @var \DateTime
     *
     * @Column(type="datetime")
     */
    private $dateCreated;

    // getters and setters...
}
```

#### Serialization metadata

If your entity is meant to be serialized, which happens in any of these cases:

 - rendered in json (or xml, yml...) to the RestApi user
 - sent to the websocket server from the rest api (forwarded)

In **src/App/Resources/serializer/App.Entity.Article.yml**:
``` yml
App\Entity\Article:
    exclusion_policy: NONE
    properties:
        id:
            type: integer
        title:
            type: string
        dateCreated:
            type: DateTime
```

#### Updating the database

Use the Doctrine command:

``` bash
php bin/console orm:schema-tool:update --force
```

*Related documentation*:

 - [Serializer available **Types**](http://jmsyst.com/libs/serializer/master/reference/annotations#type) (`string`, `integer`, ...)
 - [Serializer metadata Yaml reference](http://jmsyst.com/libs/serializer/master/reference/yml_reference)
 - [Doctrine available **Types**](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html)
 - [Doctrine commands](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/tools.html)


### Debugging with Symfony web profiler

[Silex web profiler](https://github.com/silexphp/Silex-WebProfiler) is already integrated in Sandstone fullstack.

It is available under `/index-dev.php/_profiler`.

It allows you to debug RestApi requests: exceptions, Doctrine queries, called listeners...

Sandstone also provides a [Push message debugger](https://github.com/eole-io/sandstone/releases/tag/1.1.0) (since version `1.1`)
to check which messages has been sent to the websocket stack.


### Cross origin

If your front-end application is **not** hosted under the same domain name
(i.e `http://localhost` for the front-end and `http://localhost:8088` for the RestApi),
then you probably get cross origin errors when trying to query your RestApi using Ajax.

This is a server security against XSS attacks.

To fix this issue, you have to configure your RestApi server
to let him send responses to a precise domain name.

The fullstack integrates [jdesrosiers/silex-cors-provider](https://github.com/jdesrosiers/silex-cors-provider),
so you just have to configure it:

In **config/environment.php**, only serve `localhost` (if your front-end application is on `localhost`):
``` php
return [
    'cors' => [
        'cors.allowOrigin' => 'http://localhost',
    ],
];
```

or to serve **All clients**:
``` php
return [
    'cors' => [
        'cors.allowOrigin' => '*',
    ],
];
```


## License

This library is under [MIT License](LICENSE).
