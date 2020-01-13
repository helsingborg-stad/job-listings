<?php

namespace JobListings\Admin;

/**
 * Class Settings
 * @package JobListings\Admin
 */
class Settings
{

    /**
     * Settings constructor.
     */
    public function __construct()
    {  
        add_action('acf/init', array($this, 'registerSettingsPage')); 
    }

    /**
     * register settings page
     */
    public function registerSettingsPage()
    {
        //Check if acf pro
        if (!function_exists('acf_add_options_sub_page')) {
            return; 
        }

        //Register page 
        acf_add_options_sub_page(
            array(
                'page_title' 	=> "Import job settings",
                'menu_title'	=> "Import settings",
                'parent_slug'	=> 'edit.php?post_type=job-listing',
            )
        );
    }
}
