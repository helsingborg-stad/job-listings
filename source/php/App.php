<?php

namespace JobListings;

class App
{
    public function __construct()
    {
        add_action('admin_enqueue_scripts', array($this, 'enqueueStyles'));
        add_action('admin_enqueue_scripts', array($this, 'enqueueScripts'));
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
