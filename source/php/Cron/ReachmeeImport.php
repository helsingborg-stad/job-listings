<?php

namespace JobListings\Cron;

/**
 * Class Import
 * @package JobListings\Cron
 */
class ReachmeeImport extends Import
{
    public $uuid = ""; //The unique identifier of each item in XML
    //private $guidGroup = "67794f6d-af82-43a1-b5dc-bb414fd3eab1";

    public $curlMethod = "GET"; 
    public $baseUrl = "https://site106.reachmee.com/Public/rssfeed/external.ashx";
    public $queryParams = array(
        'id' => '9',
        'InstallationID' => 'I017',
        'CustomerName' => 'helsingborg',
        'lang' => 'SE'
    ); 

    public $baseNode = "channel";
    public $subNode = "item";

    /**
     * Import constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Add manual import button
     * @return bool|null
     */

    public function addImportButton() {
        global $wp;
        $queryArgs = array_merge($wp->query_vars, array(__CLASS__ => 'true')); 
        echo '<a href="' . add_query_arg($queryArgs, home_url($wp->request)) . '" class="button-primary extraspace" style="float: right;">'. __("Start Reachmee Import") .'</a>'; 
    }

    /**
     * Update Item
     * @param $item
     * @return bool
     */
    public function updateItem($item)
    {
        if (isset($item) && is_object($item) && !empty($item)) {

            //Create Response object
            $dataObject = array();

            //Gather data
            foreach ($this->metaKeyMap() as $key => $target) {

                //Check if is default val
                if(!is_array($target)) {
                    
                    //Assign default val
                    $dataObject[$key] = $target; 

                    //Skip to next
                    continue; 
                }

                //Check if multiple targets (concat these then)
                if($this->isMultidimensionalArray($target)) {

                    //Declare
                    $result = "";
                    $i = 0;

                    //Get multiple values and create array
                    foreach($target as $subkey => $subtarget) {
                        $result .= $this->metaImplodeLimiters($key)[$i] . $item->{$target[$subkey][0]};
                        $i++;
                    }

                    //Concat values
                    $dataObject[$key] = $result; 

                    //Skip to next
                    continue; 
                }

                //If not multi
                $dataObject[$key] = $item->{$target[0]}; 
            }

            //Get matching post
            $postObject = $this->getPost(
                array(
                    'key' => 'uuid',
                    'value' => $dataObject['uuid']
                )
            );

            //Not existing, create new
            if (!isset($postObject->ID)) {
                
                $postId = wp_insert_post(
                    array(
                        'post_title' => $dataObject['post_title'],
                        'post_content' => $dataObject['post_content'],
                        'post_type' => $this->postType,
                        'post_status' => 'publish'
                    )
                );

            } else {

                //Get post object id
                $postId = $postObject->ID;

                //Create diffable array
                $updateDiff = array(
                    $postObject->post_title,
                    $postObject->post_content,
                    $dataObject['post_title'],
                    $dataObject['post_content']
                );

                //Diff data
                if (count(array_unique($updateDiff)) != count($updateDiff)) {
                    wp_update_post(
                        array(
                            'ID' => $postId,
                            'post_title' => $dataObject['post_title'],
                            'post_content' => $dataObject['post_content']
                        )
                    );
                }
            }

            // Taxonomies - Work categories
            if (isset($dataObject['occupationclassifications']) && !empty($dataObject['occupationclassifications'])) {

                // Checking terms
                $term = term_exists($dataObject['occupationclassifications'], 'job-listing-category');

                if (0 === $term || null === $term) {
                    // Adding terms
                    $term = wp_insert_term(
                        $dataObject['occupationclassifications'],
                        'job-listing-category',
                        array(
                            'description' => $dataObject['occupationclassifications'],
                            'slug' => sanitize_title($dataObject['occupationclassifications'])
                        )
                    );
                } else {
                    $term = $dataObject['occupationclassifications'];
                }

                // Connecting term to post
                wp_set_post_terms($postId, $term, 'job-listing-category', true);

            }

            //Update if there is data
            if (is_array($dataObject) && !empty($dataObject)) {
                foreach ($dataObject as $metaKey => $metaValue) {

                    if ($metaKey == "") {
                        continue;
                    }

                    if ($metaValue != get_post_meta($postId, $metaKey, true)) {
                        update_post_meta($postId, $metaKey, $metaValue);
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Mapping meta keys
     * @return array
     */
    private function metaKeyMap()
    {
        return array(
            'uuid' => array("projectNr"), //Required!
            'guid' => array("title"),
            'post_title' => array("title"),
            'preamble' => array("prefixtext"),
            'post_content' => array("description"),
            'legal_details' => array("suffixtext"),
            'publish_start_date' => array("pubDate"),
            'publish_end_date' => array("pubDateTo"),
            'application_end_date' => array("pubDateTo"),
            'employment_start_date' => array("pubDateTo"),
            'employment_end_date' => array("pubDateTo"),
            'ad_created' => array("pubDate"),
            'ad_modified' => array("pubDate"),
            'ad_reference_nbr' => array("projectName"),
            'number_of_positions' => "1",
            'external_url' => array("link"),
            'is_internal' => array("hideApplyButton"),
            'location_name' => array(array("Area1"), array("Area2")),
            'work_experience' => array("Befattning"),
            'employment_type' => array("occupationDegree"),
            'employment_grade' => array("employmentLevel"),
            'departments' => array(array("Org2"), array("Org3")),
            'occupationclassifications' => array("occupationArea"),
            'contact_person_name' => array("contactPersonFullName"),
            'contact_person_phone' => array("contactPersonTelephone"),
            'contact_person_position' => array("contactPersonPosition")
        );
    }

    /**
     * Mapping meta keys
     * @return array
     */
    private function metaImplodeLimiters($key)
    {
        $delimiters =  array(
            'post_content' => array( PHP_EOL . PHP_EOL . "  <!--more-->". PHP_EOL . PHP_EOL , " "),
            'location_name' => array(", "),
            'departments' => array(" - ")
        );

        return array_merge(array(""), $delimiters[$key]); 
    }
}