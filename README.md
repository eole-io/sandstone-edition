Sandstone Fullstack
===================

Project starter for a Real-time/RestApi oriented project.

It uses Silex and websockets.

Like a Silex skeleton or a Symfony standard edition,
you can install this project and use it as a project starter.

See *Technical stack* below to see what libraries this starter integrates.


## Installation

Using composer:

``` bash
composer create-project eole/sandstone-fullstack
```


## Usage

There is a Docker environment to run the whole application (RestApi, websocket server).

Run your RestApi and websocket server with:

```
cd sandstone-skeleton
docker-composer up
```

Then go to:

 - `http://localhost:8088/index-dev.php/api/hello` to test the *hello world* route in dev mode.
 - `http://localhost:8088/api/hello` to test the *hello world* route in prod mode.
 - `http://localhost:8088/index-dev.php/_profiler` to go to Symfony web profiler and debug your RestApi request (only dev mode).

You can now start to create your RestApi endpoints and websocket topics.


## Technical stack

This starter integrates libraries:

 - [Sandstone](https://eole-io.github.io/sandstone/) (Silex with websockets)
 - Docker environment to mount the whole application (RestApi, websocket server, push server)
 - [AnnotationServiceProvider](https://github.com/danadesrosiers/silex-annotation-provider) to use route annotations
 - PHPUnit for unit testing
 - [Symfony web profiler](https://github.com/silexphp/Silex-WebProfiler) for debugging RestApi request and Push events


## License

This library is under [MIT License](LICENSE).
