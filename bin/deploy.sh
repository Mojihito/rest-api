#!/usr/bin/env bash
echo "Setting up database"
php bin/console doctrine:database:drop --force
php bin/console doctrine:database:create

echo "Create schema"
php bin/console doctrine:schema:update --force --em=default

echo "Loading the default fixtures"
php bin/console doctrine:fixtures:load --em=default

echo "Create user"
php bin/console fos:user:create test test@example.com password --super-admin

echo "Create oauth test client"
php bin/console app:oauth:create test --grant-type="refresh_token" --grant-type="client_credentials" --grant-type="password"

echo "Done deploy"
