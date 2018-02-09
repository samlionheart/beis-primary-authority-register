# Drupal

The web application runs on Drupal 8.

# Configure

To configure the application run the following

    npm install
    npm run gulp
    composer install
    sh ./drupal-update.sh /path/to/project/root
    
You must run these commands every time you switch branch or change the applications configuration in any way.

# Coding Standards

To ensure coding standards are met we have two tools for verifying code compliance before issuing a pull request:

## PHP Code Sniffer

To configure php code sniffer you need to add the drupal rules to your code sniffer, we can turn this into a script if required:
    
    /PATH/TO/PROJECT/ROOT/vendor/bin/phpcs --config-set installed_paths /PATH/TO/PROJECT/ROOT/vendor/drupal/coder/coder_sniffer
    /PATH/TO/PROJECT/ROOT/vendor/bin/phpcs --config-set default_standard Drupal

To run code sniffer against any given module run
    
    /PATH/TO/PROJECT/ROOT/vendor/bin/phpcs PATH/TO/CUSTOM/MODULE

More examples of running PHP Code Sniffer can be found in the [Drupal documentation](https://www.drupal.org/node/1419988)

## Test Content and Stubs
To test before we have any content it is probably better to:

First enable the test content `/PATH/TO/PROJECT/ROOT/vendor/bin/drush en par_data_test -y`
Then enable stubs `/PATH/TO/PROJECT/ROOT/vendor/bin/drush config-set par_data.settings stubbed true`

# The Website: How and why it runs the way it runs!!

The PAR site is comprised of a series of user journeys based on the [GDS design patterns](https://www.gov.uk/service-manual/design).

We've represented these journeys as *Flow entities* within the system. So everything is based on flows.

Each new journey (hereon named 'flow') created _should_ be self contained and create:
* the Flow entity
* the routes definitions needed for each step of the journey
* the controllers needed for these route definitions


