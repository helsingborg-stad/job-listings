<?php

namespace JobListings\Cron;

/**
 * Class Import
 * @package JobListings\Cron
 */
class ReachmeeImport extends Import
{
    public $curlMethod = "GET";
    public $baseUrl = "";
    public $queryParams = array();
    public $settings;

    public $baseNode = "channel";
    public $subNode = "item";

    /**
     * Import constructor.
     */
    public function __construct($baseUrl, $queryParams, $settings = array())
    {
        //Assign parameters
        $this->baseUrl = $baseUrl;
        $this->queryParams = $queryParams;
        $this->settings = $settings;

        //Construct parent class
        parent::__construct();
    }

    /**
     * Normalize
     * @return $item array
     */
    public function normalize($item)
    {

        //Str replace magic to get contacts as a structured array
        $contacts = $this->getContactArray($item); 

        //Get description as string
        if (!is_string($item->description)) {
            $item->description = $item->description->asXML();
        }

        //Convert to object
        $item = (object) json_decode(json_encode($item), true);

        //Dates
        $item->pubDate = date("Y-m-d", strtotime($item->pubDate));
        $item->pubDateTo = date("Y-m-d", strtotime($item->pubDateTo));
        $item->hasExpired = strtotime('+1 day', strtotime($item->pubDateTo)) >= time() ? '0' : '1';
        $item->numberOfDaysLeft = date_diff(
            date_create(date("Y-m-d", time())),
            date_create(date("Y-m-d", strtotime('+1 day', strtotime($item->pubDateTo))))
        )->days;

        //Url
        $item->link = str_replace("rmpage=job", "rmpage=apply", $item->link);

        //Sanitize descriptions
        $item->description = html_entity_decode(
            html_entity_decode($item->description)
        );

        //Format contacts
        $item->contact = $contacts;

        return $item;
    }

    /**
     * Hacky solution  to get contacts
     *
     * @param XMLObject $item
     * @return array
     */
    public function getContactArray($item) {

        //Hacky parse due to bad markup structure
        $parsed = $item->asXML();
        $parsed = str_replace('<contactPerson>', '</contact><contact><contactPerson>', $parsed); 
        $parsed = str_replace('</item>', '</contact></item>', $parsed); 
        $parsed = preg_replace('/<\/contact>/','',$parsed, 1);

        // Create array with simple xml
        try {
            $parsed = simplexml_load_string($parsed);
        } catch (Exception $e) {
            if (!strstr($e->getMessage(), 'XML')) {
                throw $e;
            }
        }

        //Get contacts
        $parsed = $parsed->xpath("contact");

        //Rename keys
        $contacts = []; 
        if(is_array($parsed) && !empty($parsed)) {
            foreach($parsed as $item) {
               $contacts[] = [
                    'name' => (string) $item->xpath("contactPersonFullName")[0],
                    'phone' => (string) $item->xpath("contactPersonTelephone")[0],
                    'phone_sanitized' => preg_replace('/\D/', '', (string) $item->xpath("contactPersonTelephone")[0]),
                    'position' => (string) $item->xpath("contactPersonPosition")[0],
                    'email' => strtolower((string) $item->xpath("contactPersonEmail")[0])
                ];
            }
        }

        return $contacts;
    }

    /**
     * Add manual import button
     * @return bool|null
     */
    public function addImportButton()
    {
        global $wp;
        if(isset(get_current_screen()->post_type) && get_current_screen()->post_type == "job-listing") {
            $queryArgs = array_merge($wp->query_vars, array(str_replace("\\", "", __CLASS__) => 'true'));
            echo '<a href="' . add_query_arg($queryArgs, home_url($wp->request)) . '" class="button-primary extraspace" style="float: right; margin-right: 10px;">'. __("Start Reachmee Import") .'</a>';
        }
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

                        if(is_string($item->{$target[$subkey][0]})) {
                            $result .= $this->metaImplodeLimiters($key)[$i] . $item->{$target[$subkey][0]};
                        }

                        $i++;
                    }

                    //Concat values
                    $dataObject[$key] = $result;

                    //Skip to next
                    continue;
                }

                //If not multi
                if (isset($item->{$target[0]})) {
                    $dataObject[$key] = $item->{$target[0]};
                } else {
                    if (!isset($dataObject[$key])) {
                        $dataObject[$key] = "";
                    }
                }
            }

            // Store for later comparison.
            $this->importedUuids[] = $dataObject['uuid'];

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
                    if(!empty($dataObject['post_title']) && !empty($dataObject['post_content'])) {
                        wp_update_post(
                            array(
                                'ID' => $postId,
                                'post_title' => $dataObject['post_title'],
                                'post_content' => $dataObject['post_content']
                            )
                        );
                    }
                }
            }

            // Taxonomies - Work categories
            $this->updateTaxonomy($postId, 'occupationclassifications', 'job-listing-category', $dataObject);

            // Taxonomys source system
            $this->updateTaxonomy($postId, 'source_system', 'job-listing-source', $dataObject);

            //Update post with meta
            $this->updatePostMeta($postId, $dataObject);

            //Done
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
            'uuid' => array("CommAdSeqNo"), //Required!
            'project_number' => array("projectNr"), //Required!
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
            //'number_of_positions' => "1",
            'external_url' => array("link"),
            'is_internal' => array("hideApplyButton"),
            'location_name' => array(array("Area1"), array("Area2")),
            //'work_experience' => array("Befattning"),
            //'employment_type' => array(array("occupationDegree"), array("Befattning")),
            'employment_grade' => array("occupationDegree"),
            'departments' => array(array("Org2"), array("Org3")),
            'occupationclassifications' => array("occupationArea"),

            'contact_person' => array("contactPerson"),
            'contact_person_name' => array("contactPersonFullName"),
            'contact_person_phone' => array("contactPersonTelephone"),
            'contact_person_position' => array("contactPersonPosition"),

            'contact_person_union' => array("contactPersonUnion"),
            'contact_person_union_name' => array("contactPersonUnionFullName"),
            'contact_person_union_phone' => array("contactPersonUnionTelephone"),
            'contact_person_union_position' => array("contactPersonUnionPosition"),

            'source_system' => 'ReachMee',

            'has_expired' => array("hasExpired"),
            'number_of_days_left' => array("numberOfDaysLeft"),
            'contact' => array("contact")
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
            'departments' => array(" - "),
            'employment_type' => array(" - ")
        );

        return array_merge(array(""), $delimiters[$key]);
    }
}
