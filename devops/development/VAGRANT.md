sudo bash

add-apt-repository -y "deb http://apt.postgresql.org/pub/repos/apt/ trusty-pgdg main"
wget --quiet -O - https://www.postgresql.org/media/keys/ACCC4CF8.asc | sudo apt-key add -
LC_ALL=en_US.UTF-8 add-apt-repository -y ppa:ondrej/php

apt-get update -y

apt-get install -y \
        language-pack-en-base \
        postgresql-9.6 \
        build-essential \
        libfreetype6-dev \
        libmcrypt-dev \
        libpng12-dev \
        libpq-dev \
        postgresql-client-9.6 \
        git \
        software-properties-common \
        python-software-properties \
        libnss3 \
        libgconf-2-4 \
        libfontconfig \
        unzip \
        php7.1 apache2 \
        php7.1-xml \
        php7.1-cli \
        php7.1-mbstring \
        php7.1-curl \
        php7.1-pgsql

sh -c 'cd /tmp && git clone https://github.com/tj/n && cd n && make install'
n 7.2.1

apt-key adv --keyserver hkp://keyserver.ubuntu.com:80 --recv-keys EEA14886
sh -c "echo debconf shared/accepted-oracle-license-v1-1 select true | debconf-set-selections"
sh -c "echo debconf shared/accepted-oracle-license-v1-1 seen true | debconf-set-selections"
sh -c "echo \"deb http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main\" | tee /etc/apt/sources.list.d/webupd8team-java.list"
sh -c "echo \"deb-src http://ppa.launchpad.net/webupd8team/java/ubuntu trusty main\" | tee -a /etc/apt/sources.list.d/webupd8team-java.list"
apt-get -y --force-yes install oracle-java8-installer

if [ ! -f ./google-chrome-stable_62.0.3202.62-1_amd64.deb ]; then wget http://dl.google.com/linux/chrome/deb/pool/main/g/google-chrome-stable/google-chrome-stable_62.0.3202.62-1_amd64.deb; fi
apt-get -y update && apt-get install -y gdebi-core
gdebi -n google-chrome-stable_62.0.3202.62-1_amd64.deb
cd /vagrant/tests && /usr/local/n/versions/node/7.2.1/bin/npm install

phpenmod pgsql pdo_pgsql curl xml mbstring

service apache2 restart

sed -i 's/DocumentRoot \/var\/www\/html/DocumentRoot \/var\/www\/html\/web/g' /etc/apache2/sites-available/000-default.conf
sed -i '1s/^/ServerName localhost\n/' /etc/apache2/apache2.conf

su postgres
psql
CREATE DATABASE par;
CREATE ROLE par WITH LOGIN PASSWORD '123456';
GRANT ALL PRIVILEGES ON DATABASE par TO par;
\q
cd /var/www/html/docker
psql par < fresh_drupal_postgres.sql

exit

if [ ! -f /var/www/html/web/sites/settings.local.php ]; then
    cp /var/www/html/web/sites/example.settings.local.php /var/www/html/web/sites/default/settings.local.php
fi

cd /var/www/html && curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer
php /usr/local/bin/composer install
sudo npm install
sudo npm run gulp
cd tests
sudo npm install

cd /var/www/html/web && ../vendor/bin/drush cc drush
cd /var/www/html/web && ../vendor/bin/drush cr
cd /var/www/html/web && ../vendor/bin/drush fsg s3backups drush-dump-production-sanitized-latest.sql.tar.gz /dump.sql.tar.gz
cd / && tar --no-same-owner -zxvf dump.sql.tar.gz
cd /var/www/html/web && ../vendor/bin/drush @dev --root=/var/www/html/web sql-drop -y
cd /var/www/html/web && ../vendor/bin/drush sql-cli @dev --root=/var/www/html/web < /drush-dump-production-sanitized-latest.sql && rm /drush-dump-production-sanitized-latest.sql
cd /var/www/html/web && ../vendor/bin/drush cc drush
cd /var/www/html/web && ../vendor/bin/drush cr
cd /var/www/html && sh drupal-update.sh /var/www/html
cd /var/www/html/web && ../vendor/bin/drush pcw

