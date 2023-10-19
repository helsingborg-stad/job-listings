<?php

/**
 * Plugin Name:       Job Listings
 * Plugin URI:        (#plugin_url#)
 * Description:       
 * Version: 4.0.3
 * Author:            Nikolas Ramstedt, Sebastian Thulin & Johan Silvergrund
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
define('JOBLISTINGS_PATH_VIEW_PATH', plugin_dir_path(__FILE__) . 'views');
define('JOBLISTINGS_TEMPLATE_PATH', JOBLISTINGS_PATH . 'templates/');

load_plugin_textdomain('job-listings', false, plugin_basename(dirname(__FILE__)) . '/languages');

if (file_exists(JOBLISTINGS_PATH . 'vendor/autoload.php')) {
    require_once JOBLISTINGS_PATH . 'vendor/autoload.php';
}
require_once JOBLISTINGS_PATH . 'Public.php';

// Start application
new JobListings\App();
new JobListings\Admin\Settings();

// Acf auto import and export
add_action('plugins_loaded', function () {
    $acfExportManager = new \AcfExportManager\AcfExportManager();
    $acfExportManager->setTextdomain('job-listings');
    $acfExportManager->setExportFolder(JOBLISTINGS_PATH . 'source/php/AcfFields/');
    $acfExportManager->autoExport(array(
        'settings'          => 'group_5dd2a034a4f0c'
    ));
    $acfExportManager->import();
});



