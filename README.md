Sandstone Edition
===================

Build a real-time RestApi !

It uses Silex and websockets.


## Technical stack

This edition integrates:

 - [Sandstone](https://eole-io.github.io/sandstone/) (Silex with websockets)
 - **Docker** environment to mount the whole application (RestApi, websocket server, MariaDB, PHPMyAdmin)
 - **Doctrine ORM** and Doctrine commands
 - **Symfony web profiler** for debugging RestApi requests and Push events
 - [Silex annotations](https://github.com/danadesrosiers/silex-annotation-provider) for controllers and routing annotations


## Installation

Sandstone requires PHP 5.5+, ZMQ and php-zmq extension.

But the edition also has a Docker installation,
so you don't need to install PHP, ZMQ, php-zmq, mysql... using Docker.


### Docker installation

This installation requires **make**, **Docker** and **docker-compose**.

``` bash
# Install a new Sandstone project
curl -L https://github.com/eole-io/sandstone-edition/archive/dev.tar.gz | tar xz
cd sandstone-edition-dev/

# Install and mount environment
make
```

*See [About Makefile](#about-makefile) section to learn more about Makefile commands.*

> **Note**: Sometimes you'll need to do either a
> `chown -R {your_user}:{your_group} .`
> or a
> `chmod -R 777 var/*`
> to make it work.

> ![Raspberry Pi](raspberrypi.png)
> **Note**: There is also an ARMv7 environment
> to mount Sandstone on Raspberry Pi.
>
> Copy docker/docker-compose.arm.yml to docker-compose.override.yml
> to use arm docker images:
>
> `cp docker/docker-compose.arm.yml docker-compose.override.yml`
>
> Or if you already have a docker-compose.override.yml,
> change all images with the ones in `docker/docker-compose.arm.yml`.

Then check your installation by going to the diagnostic page: http://0.0.0.0:8480/hello/world.html

:heavy_check_mark: The installation is done.

Docker runs the whole environment, the RestApi, the websocket server and PHPMyAdmin. You now have access to:

 - http://0.0.0.0:8480/hello/world.html Diagnostic page.
 - http://0.0.0.0:8480/index-dev.php/api/hello *hello world* route in **dev** mode.
 - http://0.0.0.0:8480/api/hello *hello world* route in **prod** mode.
 - http://0.0.0.0:8480/index-dev.php/_profiler/ Symfony web profiler (only dev mode).
 - http://0.0.0.0:8481 PHPMyAdmin (login: `root` / `root`).
 - `ws://0.0.0.0:8482` Websocket server.

You can now start to create your RestApi endpoints and websocket topics.

Access to the **Silex console**:

``` bash
docker exec -ti sandstone-php /bin/bash -c "php bin/console"
```

Open a bash session to PHP Docker container:

``` bash
make bash
```

#### Docker default ports

Once the environment mounted, Docker exposes by default these ports:

 - `8480:http` Web server for the RestApi (nginx)
 - `8481:http` PHPMyAdmin instance
 - `8482:ws` Websocket server


### Normal installation

This requires PHP 5.5+, ZMQ, php-zmq extension, composer, and a database.

You may need to [install ZMQ and php-zmq on Linux](https://eole-io.github.io/sandstone/install-zmq-php-linux.html).

#### Install a new Sandstone project

``` bash
composer create-project eole/sandstone-edition
cd sandstone-edition/
```

 - Create a database for your project.
 - Configure your environment in `config/environment-dev.php`.
 - Run `php bin/console orm:schema-tool:create` to initialize the database schema.

Run the websocket server with:

``` bash
php bin\websocket-server
```

Then go to the diagnostic page: `http://localhost/sandstone-edition/www/hello/world.html`

:heavy_check_mark: The installation is done.

Access to the **Silex console**:

``` bash
php bin/console
```

#### Other links

> I will assume here that your webserver point to your application root with:
>
> `http://localhost/sandstone-edition/www/`

You should also access to:

 - `http://localhost/sandstone-edition/www/index-dev.php/api/hello` *hello world* route in **dev** mode.
 - `http://localhost/sandstone-edition/www/api/hello` *hello world* route in **prod** mode.
 - `http://localhost/sandstone-edition/www/index-dev.php/_profiler/` Symfony web profiler (only dev mode).
 - `ws://localhost:8482` Websocket server.


## Cookbook

Once you have a running installation of Sandstone,
you can start to build your real-time RestApi.

That means creating API endpoints, websocket topics...


### Creating an API endpoint

As Sandstone extends Silex, just create a controller class and a method, then mount it with Silex.

Also, this edition allows to use **annotations** for routing.

In **src/App/Controller/HelloController.php**:
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

> **Note**: Using `ApiResponse` allows to make your controllers return a non-yet-serialized object
> (see [alcalyn/serializable-api-response](https://github.com/alcalyn/serializable-api-response)).
>
> Sandstone transforms the `ApiResponse` to a Symfony `Response` only at the very end, after serialization.

*Related documentation*:

 - [danadesrosiers/Silex annotations](https://github.com/danadesrosiers/silex-annotation-provider)
 - [Silex routing](http://silex.sensiolabs.org/doc/2.0/usage.html#routing)
 - [ApiResponse](https://github.com/alcalyn/serializable-api-response)

### Creating a websocket topic

A websocket topic is like a "category", or a "channel" of communication.
It allows to listen to messages from a same "channel",
without receiving all others messages from the websocket server.

Technically, each topic has its own `Topic` class,
which contains its own logic.

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

In **app/WebsocketApplication.php**:
``` php
use App\Topic\ChatTopic;

private function registerUserProviders()
{
    $this->topic('chat/{channel}', function ($topicPattern) {
        return new ChatTopic($topicPattern);
    });
}
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

> **Note**: You can't use `->method('get')` or `->requireHttps()` for a topic route ;)

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

#### 1. Dispatch event from controller

In **src/App/Controller/HelloController.php**:
``` php
public function getHello($name)
{
    $this->container['dispatcher']->dispatch(HelloEvent::HELLO, new HelloEvent($name));
}
```

> **Note**: The `container` is passed to your controllers constructors
> if you use the `@SLX\Controller` annotation.

#### 2. Mark the event to be forwarded

In **app/RestApiApplication.php**:
``` php
use App\Event\HelloEvent;

private function registerUserProviders()
{
    $app->forwardEventToPushServer(HelloEvent::HELLO);
}
```

> **Note**: This must be done only in RestApi stack.
> If it's done in websocket stack, the event will be redispatched infinitely to itself!

#### 3. Listen the event from a topic

It will listen and receive the event
that has been serialized/deserialized through the Push server,
from the RestApi thread to the websocket server thread.

In **src/App/Topic/ChatTopic.php**:
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

Up to you to create a `HelloEvent` class and **create serialization metadata**.

> **Note**: You need to create serialization metadata for objects that are forwarded.
> It need to be serialized and deserialized around the Push server.

*Related documentation*:

 - [Symfony EventSubscriber](http://symfony.com/doc/current/components/event_dispatcher.html#using-event-subscribers)
 - [Sandstone Topic class](https://eole-io.github.io/sandstone/examples/multichannel-chat.html)
 - [Serializer metadata Yaml reference](http://jmsyst.com/libs/serializer/master/reference/yml_reference)


### Doctrine

Sandstone edition integrates Doctrine:

 - Doctrine DBAL and ORM are installed,
 - you can use entities with annotations or yaml mapping, the `orm:schema-tool`...
 - Doctrine commands are available under `php bin/console`
 - Entities serialization is well handled (fixes relations infinite loops). See [serializer-doctrine-proxies](https://github.com/alcalyn/serializer-doctrine-proxies).

#### Creating an entity

Here using annotations. In **src/App/Entity/Article.php**:

``` php
namespace App\Entity;

use Doctrine\ORM\Mapping\Entity;

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

#### Retrieve Repository from container

``` php
$app['orm.em']->getRepository('App\\Entity\\Article');
```

*Related documentation*:

 - [Serializer available **Types**](http://jmsyst.com/libs/serializer/master/reference/annotations#type) (`string`, `integer`, ...)
 - [Serializer metadata Yaml reference](http://jmsyst.com/libs/serializer/master/reference/yml_reference)
 - [Doctrine available **Types**](http://docs.doctrine-project.org/projects/doctrine-dbal/en/latest/reference/types.html)
 - [Doctrine commands](http://docs.doctrine-project.org/projects/doctrine-orm/en/latest/reference/tools.html)


### Debugging with Symfony web profiler

[Silex web profiler](https://github.com/silexphp/Silex-WebProfiler) is already integrated in Sandstone edition.

It is available under `/index-dev.php/_profiler/`.

It allows you to debug RestApi requests: exceptions, Doctrine queries, called listeners...

Sandstone also provides a [Push message debugger](https://github.com/eole-io/sandstone/releases/tag/1.1.0) (since version `1.1`)
to check which messages has been sent to the websocket stack.


### Cross origin

If your front-end application is **not** hosted under the same domain name
(i.e `http://localhost` for the front-end and `http://localhost:8480` for the RestApi),
then you probably get cross origin errors when trying to query your RestApi using Ajax.

This is a server-side security against XSS attacks.

To fix this issue, you have to configure your RestApi server
to let him send responses to a precise domain name.

The edition integrates [jdesrosiers/silex-cors-provider](https://github.com/jdesrosiers/silex-cors-provider),
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


### About Makefile

The Makefile only works for a Docker installation.

`make`: Used most of the time, install and run the project. Makes containers started.

`make bash`: Open a bash session into php container.

`make update`: Use it to update composer dependencies, rebuild and recreate docker containers.

`make logs`: display container logs.

`make restart_websocket_server`: Should be used after the websocket source code changed,
in example when you develop a websocket topic.

`make optimize_autoloader`: Optimize composer autoloader and reduce autoloader execution time by ~80%.
Only use it in prod. Use `make` to remove optimization.


## License

This library is under [MIT License](LICENSE).
