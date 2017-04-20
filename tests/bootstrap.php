<?php
/**
 * Bootstrap for eea-people-addon tests
 */

use EETests\bootstrap\AddonLoader;

$core_tests_dir = dirname(dirname(dirname(__FILE__))) . '/event-espresso-core/tests/';
require $core_tests_dir . 'includes/CoreLoader.php';
require $core_tests_dir . 'includes/AddonLoader.php';

define('EEA_PEOPLE_ADDON_PLUGIN_DIR', dirname(dirname(__FILE__)) . '/');
define('EEA_PEOPLE_ADDON_TESTS_DIR', EEA_PEOPLE_ADDON_PLUGIN_DIR . 'tests');


$addon_loader = new AddonLoader(
    EEA_PEOPLE_ADDON_TESTS_DIR,
    EEA_PEOPLE_ADDON_PLUGIN_DIR,
    'eea-people-addon.php'
);
$addon_loader->init();
