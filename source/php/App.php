<?php

namespace JobListings;

class App
{
    public function __construct()
    {
        // add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        // add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));


        new \JobListings\Entity\PostType('Jobs', 'Job', 'lediga-jobb', array(
            'description'          =>   __('Available jobs', 'modularity-resource-booking'),
            'menu_icon'            =>   'dashicons-list-view',
            'public'               =>   true,
            'publicly_queriable'   =>   true,
            'show_ui'              =>   true,
            'show_in_nav_menus'    =>   true,
            'has_archive'          =>   true,
            'hierarchical'          =>  false,
            'exclude_from_search'   =>  false,
            'taxonomies'            =>  array(),
            'supports'              =>  array('title', 'revisions', 'editor')
        ));
    }

    /**
     * Enqueue required style
     * @return void
     */
    public function enqueueStyles()
    {
        wp_register_style('job-listings-css', JOBLISTINGS_URL . '/dist/' . \JobListings\Helper\CacheBust::name('css/job-listings.css'));
    }

    /**
     * Enqueue required scripts
     * @return void
     */
    public function enqueueScripts()
    {
        wp_register_script('job-listings-js', JOBLISTINGS_URL . '/dist/' . \JobListings\Helper\CacheBust::name('js/job-listings.js'));
    }
}
