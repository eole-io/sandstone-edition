Sandstone Fullstack
===================

Build a real-time RestApi !

It uses Silex and websockets.

See *Technical stack* below to see what libraries are integrated in this stack.


## Installation

The installation and development requires **Docker** and **docker-compose**.

``` bash
# Clone the repo
git clone https://github.com/eole-io/sandstone-fullstack.git
cd sandstone-fullstack/

# Install dependencies
docker exec -ti sandstone-php /bin/bash -c "composer update"
```


## Usage

Docker will run your whole environment, the RestApi and the websocket server:

```
docker-composer up
```

Then go to:

 - `http://localhost:8088/index-dev.php/api/hello`: *hello world* route in **dev** mode.
 - `http://localhost:8088/api/hello`: *hello world* route in **prod** mode.
 - `http://localhost:8088/index-dev.php/_profiler`: Symfony web profiler (only dev mode).
 - `http://localhost:8088/websocket-test.html`: An HTML page which connect to the websocket server and says hello on the chat (open your Javacript console).
 - `http://localhost:8090/`: PHPMyAdmin

You can now start to create your RestApi endpoints and websocket topics.

### Docker default ports

The Docker images, once mounted, provides these ports:

 - `8088:http` Web server for the RestApi (nginx)
 - `8089:ws` Websocket server
 - `8090:http` PHPMyAdmin instance


## Technical stack

This fullstack integrates:

 - [Sandstone](https://eole-io.github.io/sandstone/) (Silex with websockets)
 - Docker environment to mount the whole application (RestApi, websocket server, PHPMyAdmin)
 - Doctrine ORM and Doctrine commands
 - MariaDB server with a PHPMyAdmin instance
 - PHPUnit for unit testing
 - [Symfony web profiler](https://github.com/silexphp/Silex-WebProfiler) for debugging RestApi requests and Push events


## License

This library is under [MIT License](LICENSE).
