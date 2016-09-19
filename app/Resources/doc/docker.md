Rest Api
========

Install `docker`, `docker-compose` and we propose to install also `docker-machine`

# Docker

Update docker-compose:
```
$ sudo pip install --upgrade docker-compose
```

Build your containers:
```
$ docker-compose build
```

Run containers:
```
$ docker-compose up -d
```

stop containers:
```
$ docker-compose stop
```


Remove build earlier containers:
```
$ docker-compose rm -vf
```

Get the IP address of one or more machines.
```
$ docker-machine ip dev
192.168.99.104
```
```
$ docker-machine ip dev dev2
192.168.99.104
192.168.99.105
```
