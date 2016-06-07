FROM ubuntu:14.04

# https://github.com/dotcloud/docker/issues/1024
RUN dpkg-divert --local --rename --add /sbin/initctl
RUN ln -s /bin/true /sbin/initctl
 
ADD ./docker/setup.sh /setup.sh
RUN sh /setup.sh
 
RUN mkdir /server
 
RUN cd .

WORKDIR /server
ADD . /server
RUN curl -s http://getcomposer.org/installer | php
RUN rm -rf app/cache/*

ADD ./docker/configure-mysql.sh /tmp/configure-mysql.sh
RUN /bin/sh /tmp/configure-mysql.sh

ADD ./docker/configure-ssh.sh /tmp/configure-ssh.sh
RUN /bin/sh /tmp/configure-ssh.sh

RUN php composer.phar update
ADD ./docker/deploy.sh /tmp/deploy.sh
RUN /bin/sh /tmp/deploy.sh

ADD ./docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

EXPOSE 8005 22 9200
 
CMD ["/usr/bin/supervisord"]
