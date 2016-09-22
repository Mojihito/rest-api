#!/usr/bin/env bash
echo "Setting up database"
php app/console doctrine:database:drop --force
php app/console doctrine:database:create

echo "Create schema"
php app/console doctrine:schema:update --force --em=default

echo "Loading the default fixtures"
php app/console doctrine:fixtures:load --em=default

echo "Create user"
php app/console fos:user:create test test@example.com password
php app/console fos:user:promote test ROLE_SUPER_ADMIN

echo "Done deploy"
