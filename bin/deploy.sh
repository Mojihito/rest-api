#! /bin/bash

composer install

docker-composer build
docker-composer up -d

php app/console doctrine:database:drop --force
php app/console doctrine:database:create
php app/console doctrine:schema:update --force

php app/console doctrine:fixtures:load
