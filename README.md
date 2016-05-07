Rest Api
======

Symfony 3.0 REST API project skeleton with crud builder and API doc included

How to work with this skeleton read: [CRUD configuration](https://github.com/Vardius/crud-bundle/blob/master/Resources/doc/configuration.md)

# OAuth
OAuth2 client create:
```
$ php bin/console app:oauth:create angular --grant-type="refresh_token" --grant-type="client_credentials" --grant-type="password"
```
# User
Create admin:
```
$ php bin/console fos:user:create test test@example.com password --super-admin
```
# ElasticSearch
Elastic search:
```
$ php bin/console fos:elastica:populate
```
# Database
## Doctrine
It's also possible to generate the metadata files in XML format by changing the last argument to xml.
Run the following command to generate an YAML schema from your default database:
```
php bin/console doctrine:mapping:import --force AppBundle yml
```
This command line tool asks Doctrine to introspect the database and generate the YAML metadata files under the src/AppBundle/Resources/config/doctrine folder of your bundle. 

Once the metadata files are generated, you can ask Doctrine to build related entity classes by executing the following two commands.
```
php bin/console doctrine:mapping:convert annotation ./src
php bin/console doctrine:generate:entities AppBundle
```
The first command generates entity classes with annotation mappings. But if you want to use YAML or XML mapping instead of annotations, you should execute the second command only.
If you want to use annotations, you must remove the XML (or YAML) files after running these two commands. This is necessary as it is not possible to mix mapping configuration formats

If you want to have a one-to-many relationship, you will need to add it manually into the entity or to the generated XML or YAML files. Add a section on the specific entities for one-to-many defining the inversedBy and the mappedBy pieces.

You can generate stub classes in a given bundle:
```
php bin/console vardius:crud:generate AppBundle Book Author
```

## Propel
Worth to read [Working with Symfony2 - Introduction](http://propelorm.org/Propel/cookbook/symfony2/working-with-symfony2.html)

You now can run the following command to create the model:
```
$ php bin/console propel:build
```
To create SQL, run the command `propel:build --insert-sql` or use migration commands if you have an existing schema in your database.

Run the following command to generate an XML schema from your default database:
```
php bin/console propel:reverse
```
You can generate stub classes based on your schema.xml in a given bundle:
```
php app/console vardius:crud:generate AppBundle Book Author
```
### Migrations
[Migration Workflow](http://propelorm.org/Propel/documentation/10-migrations.html)
```
php bin/console propel:migration:generate-diff
```
