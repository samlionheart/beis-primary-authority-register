sudo bash
apt-get update
apt-get install -y build-essential

add-apt-repository "deb http://apt.postgresql.org/pub/repos/apt/ trusty-pgdg main"
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
apt-get update
apt-get -y install postgresql-9.6

apt-add-repository -y ppa:ondrej/php
apt-get -y update

apt-get install -y \
        libfreetype6-dev \
        libmcrypt-dev \
        libpng12-dev \
        libpq-dev \
        php5-cli \
        postgresql-client-9.6 \
        git \
        software-properties-common \
        python-software-properties \
        libnss3 \
        libgconf-2-4 \
        libfontconfig \
        unzip

sh -c 'cd /tmp && git clone https://github.com/tj/n && cd n && make install'
n 7.2.1

apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys EEA14886
 sh -c "echo debconf shared/accepted-oracle-license-v1-1 select true | debconf-set-selections"
sh -c "echo debconf shared/accepted-oracle-license-v1-1 seen true | debconf-set-selections"
sh -c "echo \"deb http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main\" | tee /etc/apt/sources.list.d/webupd8team-java.list"
sh -c "echo \"deb-src http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main\" | tee -a /etc/apt/sources.list.d/webupd8team-java.list"
apt-get -y install oracle-java8-installer

apt-get update
apt-get install php7.1

if [ ! -f ./google-chrome-stable_62.0.3202.62-1_amd64.deb ]; then wget http://dl.google.com/linux/chrome/deb/pool/main/g/google-chrome-stable/google-chrome-stable_62.0.3202.62-1_amd64.deb; fi
apt-get -y update && apt-get install -y gdebi-core
gdebi -n google-chrome-stable_62.0.3202.62-1_amd64.deb
cd /vagrant/tests && /usr/local/n/versions/node/7.2.1/bin/npm install

apt-get install -y language-pack-en-base
LC_ALL=en_US.UTF-8 add-apt-repository ppa:ondrej/php

apt-get update
apt-get install -y php7.1
apt-get install apache2

apt-get install php7.1-pgsql
phpenmod pgsql
phpenmod pdo_pgsql
service apache2 restart

sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/web/g' /etc/apache2/sites-available/000-default.conf
sed -i '1s/^/ServerName localhost\n/' /etc/apache2/apache2.conf

su postgres
psql
create database;
CREATE ROLE par WITH LOGIN PASSWORD '123456';
GRANT ALL PRIVILEGES ON DATABASE par TO par;
\q
cd /var/www/html/docker
psql par < fresh_drupal_postgres.sql




