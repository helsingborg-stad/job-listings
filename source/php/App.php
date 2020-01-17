<?php

namespace JobListings;

/**
 * Class App
 * @package JobListings
 */
class App
{
    /**
     * App constructor.
     */
    public function __construct()
    {

        new \JobListings\Entity\PostType(__('Jobs', 'job-listings'), __('Job', 'job-listings'), 'job-listing', array(
            'description' => __('Available jobs', 'job-listings'),
            'menu_icon' => 'dashicons-list-view',
            'public' => true,
            'publicly_queriable' => true,
            'show_ui' => true,
            'show_in_nav_menus' => true,
            'has_archive' => true,
            'hierarchical' => false,
            'exclude_from_search' => false,
            'rewrite' => array(
                'slug' => 'lediga-jobb',
                'with_front' => false
            ),
            'taxonomies' => array(),
            'supports' => array('title', 'revisions', 'editor')
        ));

        add_filter('Municipio/blade/view_paths', array($this, 'includePluginTemplates'), 10);

        new \JobListings\Entity\Taxonomy(
            __('Job categories', 'job-listings'),
            __('Job category', 'job-listings'),
            'job-listing-category',
            'job-listing',
            array(
                'label' => __('Job Categories', 'job-listings'),
                'public' => true,
                'description' => '',
                'show_in_nav_menus' => true,
                'show_admin_column' => true,
                'hierarchical' => false,
                'show_tagcloud' => false,
                'show_ui' => true,
                'query_var' => true,
                'rewrite' => true,
                'show_in_rest' => true,
            )
        );


        new \JobListings\Entity\Taxonomy(
            __('Sources', 'job-listings'),
            __('Source', 'job-listings'),
            'job-listing-source',
            'job-listing',
            array(
                'label' => __('Job Categories', 'job-listings'),
                'public' => true,
                'description' => '',
                'show_in_nav_menus' => true,
                'show_admin_column' => true,
                'hierarchical' => false,
                'show_tagcloud' => false,
                'show_ui' => true,
                'query_var' => true,
                'rewrite' => true,
                'show_in_rest' => true,
            )
        );

        new Controller(); 

        add_action('wp_enqueue_scripts', array($this, 'enqueueStyles'), 14);
        add_action('wp_enqueue_scripts', array($this, 'enqueueScripts'));

        add_action('pre_get_posts', array($this, 'removeInactiveAds'));

        add_action('init', array($this, 'initializeImporters'));
    }

    /** Initialize importers
     * @return array
     */
    public function initializeImporters()
    {
        $importers = get_field('job_listings_importers', 'option'); 
        
        if(is_array($importers) && !empty($importers)) {
            foreach($importers as $importer) {

                //Init visma import
                if(isset($importer['acf_fc_layout']) && $importer['acf_fc_layout'] == "visma") {
                    new \JobListings\Cron\VismaImport(
                        $importer['baseUrl'],
                        array(
                            'guidGroup' => $importer['guidGroup']
                        ),
                        $importer
                    );
                    continue;
                }

                //Init reachmee import
                if(isset($importer['acf_fc_layout']) && $importer['acf_fc_layout'] == "reachmee") {
                    new \JobListings\Cron\ReachmeeImport(
                        $importer['baseUrl'],
                        array(
                            'id' => $importer['id'],
                            'InstallationID' => $importer['InstallationID'],
                            'CustomerName' => $importer['CustomerName'],
                            'lang' => $importer['lang']
                        ),
                        $importer
                    );
                    continue;
                }
            }
        }
    }

    /**
     * @param $paths
     * @return array
     */
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
        wp_enqueue_style('job-listings-css', JOBLISTINGS_URL . '/dist/' . Helper\CacheBust::name('css/job-listings.css'));
    }

    /**
     * Enqueue required scripts
     * @return void
     */
    public function enqueueScripts()
    {
        $postType = get_post_type();

        wp_register_script('job-listings-js',
            JOBLISTINGS_URL . '/dist/' . Helper\CacheBust::name('js/job-listings.js'), array(), false, true);
        if (is_single() && $postType === 'job-listing') {
          wp_enqueue_script('job-listings-js');

            $postMeta = get_post_meta(get_the_ID());
            $applyUrl = $postMeta['external_url'][0] ?? '';
            parse_str(htmlspecialchars_decode($applyUrl), $output);

            wp_localize_script(
              'job-listings-js',
              'jobListings',
              array(
                'jobId' => $output['rmjob'] ?? '',
              )
            );
        }
    }

    /**
     * Remove inactive ads from archive
     * @return void
     */
    public function removeInactiveAds($query)
    {
        if (!is_admin() && $query->is_main_query() && is_post_type_archive('job-listing')) {
            $query->set('meta_query', array(
                array(
                   'key'=>'number_of_days_left',
                   'value'=>'0',
                   'compare'=>'!=',
                ),
            ));
        }
    }
}
