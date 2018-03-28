all: install run book

install:
	docker-compose up --no-deps -d php-fpm database

	docker exec -ti sandstone-php sh -c "composer install"

	docker exec -ti sandstone-database sh -c "mysql -u root -proot -e 'create database if not exists sandstone;'"
	docker exec -ti sandstone-php sh -c "bin/console orm:schema-tool:update --dump-sql"
	docker exec -ti sandstone-php sh -c "bin/console orm:schema-tool:update --force"

update:
	docker-compose up --build --force-recreate --no-deps -d php-fpm database

	docker exec -ti sandstone-php sh -c "composer update"

	docker exec -ti sandstone-database sh -c "mysql -u root -proot -e 'create database if not exists sandstone;'"
	docker exec -ti sandstone-php sh -c "bin/console orm:schema-tool:update --dump-sql"
	docker exec -ti sandstone-php sh -c "bin/console orm:schema-tool:update --force"

	docker-compose up --build --force-recreate -d

run:
	docker-compose up -d

logs:
	docker-compose logs -ft

optimize_autoloader:
	docker exec -ti sandstone-php sh -c "composer install --optimize-autoloader"

bash:
	docker-compose up --no-deps -d php-fpm
	docker exec -ti sandstone-php bash

restart_websocket_server:
	docker restart sandstone-ws

stop:
	docker-compose stop

book:
	#
	# Default urls:
	#  http://0.0.0.0:8480/hello/world.html          Diagnostic page.
	#  http://0.0.0.0:8480/index-dev.php/api/hello   *hello world* route in **dev** mode.
	#  http://0.0.0.0:8480/api/hello                 *hello world* route in **prod** mode.
	#  http://0.0.0.0:8480/index-dev.php/_profiler/  Symfony web profiler (only dev mode).
	#  http://0.0.0.0:8481                           PHPMyAdmin (login: `root` / `root`).
	#  ws://0.0.0.0:8482                             Websocket server.
	#
	# Make commands:
	#  make                             Install application and run it
	#  make run                         Run application
	#  make bash                        Enter in php container
	#  make logs                        Display containers logs and errors
	#  make update                      rebuild containers, update composer dependencies...
	#  make restart_websocket_server    Reload websocket stack, i.e when code is updated
        #  make optimize_autoloader         Enhance composer autoload performances
	#  make book                        Display this help
	#
	# See Sandstone edition cookbook:
	#  https://github.com/eole-io/sandstone-edition
	#
	# See Sandstone documentation:
	#  https://eole-io.github.io/sandstone
	#
