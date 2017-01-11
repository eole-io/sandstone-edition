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

You can now start to create your RestApi endpoints and websocket topics.


## Technical stack

This fullstack integrates:

 - [Sandstone](https://eole-io.github.io/sandstone/) (Silex with websockets)
 - Docker environment to mount the whole application (RestApi, websocket server, push server)
 - PHPUnit for unit testing
 - [Symfony web profiler](https://github.com/silexphp/Silex-WebProfiler) for debugging RestApi request and Push events


## License

This library is under [MIT License](LICENSE).
