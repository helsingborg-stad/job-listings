<?php

// Get around direct access blockers.
if (!defined('ABSPATH')) {
    define('ABSPATH', sys_get_temp_dir());
}

if (!defined('PLUGIN_ABSPATH')) {
    define('PLUGIN_ABSPATH', sys_get_temp_dir() . '/wp-content/plugins/my-plugin/');
}

require_once __DIR__ . '/../../../source/php/Vendor/Psr4ClassLoader.php';

// Instantiate and register the autoloader
$loader = new JobListings\Vendor\Psr4ClassLoader();
$loader->addPrefix('JobListings', __DIR__ . '/../../../');
$loader->addPrefix('JobListings', __DIR__ . '/../../../source/php/');
$loader->register();

require_once __DIR__ . '/../../../vendor/autoload.php';

require_once __DIR__ . '/PluginTestCase.php';
