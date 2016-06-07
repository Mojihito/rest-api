#!/bin/sh

#Mysql - Openssh
DEBIAN_FRONTEND=noninteractive apt-get install -y -q mysql-server openssh-server

# Make sshd run dir
mkdir /var/run/sshd
echo 'root:root' | chpasswd

apt-get update
apt-get upgrade -y

# PHP
apt-get install -y python-software-properties software-properties-common
add-apt-repository ppa:ondrej/php
 
apt-get update

apt-get install -y --force-yes php7.0 php7.0-cli php7.0-mysql php7.0-curl php7.0-sqlite php7.0-intl libicu48 php7.0-gd
apt-get install -y git
apt-get install -y curl
apt-get install -y supervisor

# Set timezone
perl -pi -e "s#;date.timezone =#date.timezone = Europe/Amsterdam#g" /etc/php/7.0/cli/php.ini
