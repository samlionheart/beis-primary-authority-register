#!/usr/bin/env bash

# Destroy dependencies

    cd /vagrant/docker
    sudo sh destroy-dependencies.sh

# Install dependencies

    docker exec -i par_beta_web bash -c 'su - composer -c "cd ../../var/www/html && php composer.phar install"'
    docker exec -i par_beta_web bash -c "cd /var/www/html/tests && rm -rf node_modules/* && ../../../../usr/local/n/versions/node/7.2.1/bin/npm install"
    docker exec -i par_beta_web bash -c "rm -rf node_modules/* && ../../../usr/local/n/versions/node/7.2.1/bin/npm install"
    docker exec -i par_beta_web bash -c "../../../usr/local/n/versions/node/7.2.1/bin/npm run gulp"

# Setup the development settings file:

if [ ! -f ../web/sites/settings.local.php ]; then
    cp ../web/sites/example.settings.local.php ../web/sites/default/settings.local.php
    cat ../web/sites/settings.local.php.docker.append >> ../web/sites/default/settings.local.php
fi

# Load the test data:

    DATAFILE=drush-dump-post-drush-updates-sanitized-201707222304.sql
    
    # Time for the server to boot
    sleep 5

    # Got to load a clean database before "cr" and "fsg" commands can be bootstrapped

    docker exec -i par_beta_web bash -c "vendor/bin/drush @dev --root=/var/www/html/web sql-drop -y"
    docker exec -i par_beta_web bash -c "vendor/bin/drush sql-cli @dev --root=/var/www/html/web < docker/fresh_drupal_postgres.sql"

    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush cc drush"
    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush cr"

    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush fsg s3backups $DATAFILE.tar.gz /dump.sql.tar.gz"
    docker exec -i par_beta_web bash -c "cd / && tar --no-same-owner -zxvf dump.sql.tar.gz"

    # The tar.gz maintains the tar folder structure, so it exports in /home/vcap to maintain consistency.
    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush sql-cli @dev --root=/var/www/html/web < /home/vcap/$DATAFILE && rm $DATAFILE"

    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush cc drush"
    docker exec -i par_beta_web bash -c "cd web && ../vendor/bin/drush cr"

# Update Drupal

    docker exec -i par_beta_web bash -c "sh drupal-update.sh /var/www/html"
