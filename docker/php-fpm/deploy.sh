#!/usr/bin/env bash

echo "Starting deploy"

# Wait for the mysql container to be ready before deploying.
echo "Stalling for Mysql"
while true; do
    nc -q 1 mysql 3306>/dev/null && break
done

echo "Installing vendors"

composer install

echo "Setting up database"

php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create
php bin/console doctrine:schema:update --force

echo "Loading fixtures"

php bin/console doctrine:fixtures:load

echo "Done deploy"
