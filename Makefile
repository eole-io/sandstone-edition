all: docker_update

docker_update:
	docker-compose up --no-deps -d php-fpm database

	docker exec -ti sandstone-php sh -c "composer update"

	docker exec -ti sandstone-database sh -c "mysql -u root -proot -e 'create database if not exists sandstone;'"
	docker exec -ti sandstone-php sh -c "bin/console orm:schema-tool:update --dump-sql"
	docker exec -ti sandstone-php sh -c "bin/console orm:schema-tool:update --force"

	docker-compose up -d

bash:
	docker exec -ti sandstone-php bash
