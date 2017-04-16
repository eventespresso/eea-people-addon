#!/usr/bin/env bash

## if there is a BUILD_BRANCH build environment variable then we use that for what branch of
## ee core to checkout.  otherwise master.
if [ -n "$BUILD_BRANCH" ]; then
    core_branch=$BUILD_BRANCH
else
    core_branch="master"
fi

# commands taking care of WordPress setup
function wpCoreSetup {
    git clone git://develop.git.wordpress.org/ $WP_CORE_DIR
    cd $WP_CORE_DIR
    cp wp-tests-config-sample.php wp-tests-config.php
    sed -i "s/youremptytestdbnamehere/wordpress_test/" wp-tests-config.php
    sed -i "s/yourusernamehere/root/" wp-tests-config.php
    sed -i "s/yourpasswordhere//" wp-tests-config.php
}

# commands taking care of ee core setup
# receives an argument indicating what branch to checkout.
function eeCoreSetup {
    local BRANCH=$1
    git clone git@github.com:eventespresso/event-espresso-core.git $event_espresso_core_dir
    git checkout $BRANCH
}

# commands taking care of addon setup
function addOnSetup {
    mv $plugin_loc $plugin_dir
}

# commands taking care of creating the WordPress test db.
function createDB {
    mysql -e 'CREATE DATABASE wordpress_test;' -uroot;
}

# commands taking care of setting up phpunit
function setupPhpUnit {
    wget --no-check-certificate https://phar.phpunit.de/phpunit-old.phar
    chmod +x phpunit-old.phar
    mv phpunit-old.phar /home/ubuntu/.phpenv/shims/phpunit
}

wpCoreSetup
eeCoreSetup $core_branch
addOnSetup
createDB
setupPhpUnit