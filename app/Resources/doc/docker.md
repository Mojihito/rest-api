Rest Api
========

Install `docker`, `docker-compose` and we propose to install also `docker-machine`

### Setup ###

Create a local working directory and clone this repo into it.

make _/app/logs_ and _/app/cache_ folders - this ensures the user is not root when the data volume mounts

Build and run the containers
```
    $ docker-compose up -d --build
``` 


### Setup Symfony ###
1. Update app/config/parameters.yml
```yml
parameters:
    database_host: wldev-mysql # Docker server
    database_port: 3306
    database_name: symfony
    database_user: root
    database_password: root
    
    #...
    
    rabbitmq_connections:
        default:
          host: rabbitmq
    
    #...        
```

2. Composer install & project setup
```bash
$ docker-compose exec wldev-php-fpm bash
```
Or
```bash
$ docker exec -it wldev-php-fpm bash
```
Or if at Windows
```bash
$ winpty docker exec -it wldev-php-fpm bash
```
And then execute following:
```bash
$ composer install
$ sh bin/deploy.sh
```
When on windows remember to change line ending from _CRLF_ to _LF_ for `deploy.sh` before executing

You can login:
```
login:   test 
email:  test@example.com 
pass:    password
```
### Shared folders ###

Remember to toggle your drive to be available! [Shared Drives](https://forums.docker.com/t/volume-mounts-in-windows-does-not-work/10693/99)

On windows go to: `Local Security Policy > Network List Manager Policies` and Double-clicked `unidentified Networks`
then change the location type to `private` and restart Docker.

If this does not help, you may want to try this also [Sharing settings](https://forums.docker.com/t/volume-mounts-in-windows-does-not-work/10693/115)

#### Port info 

| Service | Exposed Ports | Link Hostname |
| ---| --- | --- |
| nginx | 8080 | nginx |
| rabbitmq | 25671:5672, 25672:15672 | rabbitmq |
| mysql | 33306:3306 | mysql |
| phpmyadmin | 8090:80 | phpmyadmin |
| redis | 6379 | solr |
| elasticsearch | 9200:9200 9300:9300 | elasticsearch |
| logstash | 5000:5000 | logstash |
| kibana | 5601:5601 | kibana |

#### Retrieve Containers IP's 
```bash
$ docker inspect $(docker ps -f name=mysql -q) | grep IPAddress
```

#### Remove volumes
```bash
$ docker volume rm dockersymfony_cache
```
