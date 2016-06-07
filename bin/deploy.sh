#! /bin/bash

composer install

docker-composer build
docker-composer up -d

php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force

php bin/console doctrine:fixtures:load
