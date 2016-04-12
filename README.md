Rest Api
======

Symfony 3.0 REST API project skeleton with crud builder and API doc included

# OAuth
OAuth2 client create:
```
$ php app/console app:oauth:create angular --grant-type="refresh_token" --grant-type="client_credentials" --grant-type="password"
```
# User
Create admin:
```
$ php app/console fos:user:create test test@example.com password --super-admin
```
# ElasticSearch
Elastic search:
```
$ php app/console fos:elastica:populate
```
# Propel
Worth to read [Working with Symfony2 - Introduction](http://propelorm.org/Propel/cookbook/symfony2/working-with-symfony2.html)

You now can run the following command to create the model:
```
$ php app/console propel:build
```

To create SQL, run the command `propel:build --insert-sql` or use migration commands if you have an existing schema in your database.

Run the following command to generate an XML schema from your default database:
```
php app/console propel:reverse
```
You can generate stub classes based on your schema.xml in a given bundle:
```
php app/console propel:crud:generate @AppBundle Book Author
```
Register services:
```
php app/console propel:crud:register @AppBundle Book Author
```
## Migrations
[Migration Workflow](http://propelorm.org/Propel/documentation/10-migrations.html)
```
php app/console propel:migration:generate-diff
```

# Chat
Run chat
```
php app/console chat:run
```

Example usage

```javascript
        <script src="http://autobahn.s3.amazonaws.com/js/autobahn.min.js"></script>
        <script>
            var conn = new ab.Session('ws://localhost:8080',
                    function() {
                        conn.subscribe('current_user_access_token', function(topic, data) {
                            //you have got a message
                            console.log(topic);
                            console.log(data);
                        });
                    },
                    function() {
                        console.warn('WebSocket connection closed');
                    },
                {'skipSubprotocolCheck': true}
            );
        </script>
```
