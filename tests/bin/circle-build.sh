#!/usr/bin/env bash
function wpCoreSetup {
    git clone git://develop.git.wordpress.org/ $WP_CORE_DIR
    cd $WP_CORE_DIR
    cp wp-tests-config-sample.php wp-tests-config.php
    sed -i "s/youremptytestdbnamehere/wordpress_test/" wp-tests-config.php
    sed -i "s/yourusernamehere/root/" wp-tests-config.php
    sed -i "s/yourpasswordhere//" wp-tests-config.php
}

function eeCoreSetup {
    local BRANCH=$1
    git clone git@github.com:eventespresso/event-espresso-core.git $event_espresso_core_dir
    git checkout $BRANCH
}

function addOnSetup {
    mv $plugin_loc $plugin_dir
}

function createDB {
    mysql -e 'CREATE DATABASE wordpress_test;' -uroot;
}

function setupPhpUnit {
    wget --no-check-certificate https://phar.phpunit.de/phpunit-old.phar
    chmod +x phpunit-old.phar
    mv phpunit-old.phar /home/ubuntu/.phpenv/shims/phpunit
}

wpCoreSetup
eeCoreSetup master
addOnSetup
createDB
setupPhpUnit