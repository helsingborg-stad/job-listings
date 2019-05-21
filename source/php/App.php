<?php

namespace JobListings;

class App
{
    public function __construct()
    {
        new \JobListings\Entity\PostType(__('Jobs', 'job-listings'), __('Job', 'job-listings'), 'job-listing', array(
            'description'          =>   __('Available jobs', 'job-listings'),
            'menu_icon'            =>   'dashicons-list-view',
            'public'               =>   true,
            'publicly_queriable'   =>   true,
            'show_ui'              =>   true,
            'show_in_nav_menus'    =>   true,
            'has_archive'          =>   true,
            'hierarchical'          =>  false,
            'exclude_from_search'   =>  false,
            'rewrite'              =>   array(
                'slug'       =>   'lediga-jobb',
                'with_front' =>   false
            ),
            'taxonomies'            =>  array(),
            'supports'              =>  array('title', 'revisions', 'editor')
        ));

        add_filter('Municipio/blade/view_paths', array($this, 'includePluginTemplates'), 10);
    }

    public function includePluginTemplates($paths)
    {
        $paths[] = JOBLISTINGS_PATH . 'views';
        return $paths;
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
