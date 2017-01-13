Sandstone Fullstack
===================

Build a real-time RestApi !

It uses Silex and websockets.

See *Technical stack* below to see what libraries are integrated in this stack.


## Installation

The installation and development requires **git**, **Docker** and **docker-compose**.

``` bash
# Clone the repo
git clone https://github.com/eole-io/sandstone-fullstack.git
cd sandstone-fullstack/

# Mount the environment
docker-compose up

# Install composer dependencies
docker exec -ti sandstone-php /bin/bash -c "composer update"
```


## Usage

Docker runs the whole environment, the RestApi, the websocket server and PHPMyAdmin. You now have access to:

 - `http://localhost:8088/index-dev.php/api/hello`: *hello world* route in **dev** mode.
 - `http://localhost:8088/api/hello`: *hello world* route in **prod** mode.
 - `http://localhost:8088/index-dev.php/_profiler`: Symfony web profiler (only dev mode).
 - `http://localhost:8088/websocket-test.html`: A HTML page which connect to the websocket server and says hello on the chat (check your Javacript console).
 - `http://localhost:8090/`: PHPMyAdmin.

You can now start to create your RestApi endpoints and websocket topics.

Access to the **Silex console**:

``` bash
docker exec -ti sandstone-php /bin/bash -c "bin/console"
```


### Docker default ports

Once the environment mounted, Docker exposes these ports:

 - `8088:http` Web server for the RestApi (nginx)
 - `8089:ws` Websocket server
 - `8090:http` PHPMyAdmin instance


## Technical stack

This fullstack integrates:

 - [Sandstone](https://eole-io.github.io/sandstone/) (Silex with websockets)
 - **Docker** environment to mount the whole application (RestApi, websocket server, PHPMyAdmin)
 - **Doctrine ORM** and Doctrine commands
 - **MariaDB** server with a PHPMyAdmin instance
 - **Symfony web profiler** for debugging RestApi requests and Push events


## License

This library is under [MIT License](LICENSE).
