<?php

namespace JobListings\Admin;

class Settings
{
    public function __construct()
    {  
        add_action('acf/init', array($this, 'registerSettingsPage')); 
    }

    public function registerSettingsPage() {
        //Check if acf pro
        if (!function_exists('acf_add_options_sub_page')) {
            return; 
        }

        //Register page 
        acf_add_options_sub_page(
            array(
                'page_title' 	=> __("Import job settings", 'job-listings'),
                'menu_title'	=> __("Import settings", 'job-listings'),
                'parent_slug'	=> 'edit.php?post_type=job-listing',
            )
        );
    }
}
