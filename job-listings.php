<?php

/**
 * Plugin Name:       Job Listings
 * Plugin URI:        (#plugin_url#)
 * Description:       
 * Version:           1.0.0
 * Author:            Nikolas Ramstedt
 * Author URI:        
 * License:           MIT
 * License URI:       https://opensource.org/licenses/MIT
 * Text Domain:       job-listings
 * Domain Path:       /languages
 */

 // Protect agains direct file access
if (! defined('WPINC')) {
    die;
}

define('JOBLISTINGS_PATH', plugin_dir_path(__FILE__));
define('JOBLISTINGS_URL', plugins_url('', __FILE__));
define('JOBLISTINGS_TEMPLATE_PATH', JOBLISTINGS_PATH . 'templates/');

load_plugin_textdomain('job-listings', false, plugin_basename(dirname(__FILE__)) . '/languages');

require_once JOBLISTINGS_PATH . 'source/php/Vendor/Psr4ClassLoader.php';
require_once JOBLISTINGS_PATH . 'Public.php';

// Instantiate and register the autoloader
$loader = new JobListings\Vendor\Psr4ClassLoader();
$loader->addPrefix('JobListings', JOBLISTINGS_PATH);
$loader->addPrefix('JobListings', JOBLISTINGS_PATH . 'source/php/');
$loader->register();

// Start application
new JobListings\App();
