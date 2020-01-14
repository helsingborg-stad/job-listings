<?php

namespace JobListings\Cron;

/**
 * Class Import
 * @package JobListings\Cron
 */
class VismaImport extends Import
{

    public $uuid = ""; //The unique identifier of each item in XML

    public $curlMethod = "GET";
    public $baseUrl = "";
    public $queryParams = array();
    public $baseAdLink = "";
    
    public $detailsUrl = "https://recruit.visma.com/External/Feeds/AssignmentItem.ashx";

    public $settings; 

    public $baseNode = "Assignments";
    public $subNode = "Assignment";

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

        $item->PublishStartDate = date("Y-m-d", strtotime($item->PublishStartDate));
        $item->PublishEndDate = date("Y-m-d", strtotime($item->PublishEndDate));
        $item->ApplicationEndDate = date("Y-m-d", strtotime($item->ApplicationEndDate));
        $item->EmploymentStartDate = date("Y-m-d", strtotime($item->EmploymentStartDate));
        $item->EmploymentEndDate = date("Y-m-d", strtotime($item->EmploymentEndDate));
        $item->EmploymentEndDate = date("Y-m-d", strtotime($item->EmploymentEndDate));
        $item->Modified = date("Y-m-d", strtotime($item->Modified));
        $item->hasExpired = strtotime($item->PublishEndDate) >= time() ? '0' : '1';
        $item->numberOfDaysLeft = date_diff(
            date_create(date("Y-m-d", time())), 
            date_create($item->ApplicationEndDate)
        )->days;
        $item->ReadMoreUrl = $this->settings['apply_base_link'] . $item->Guid; 

        //Get full text
        $details = $this->getDetails($item->Guid);
        $item->Localization->AssignmentLoc->WorkDescr = implode("\r\n", array(
            $details->Assignment->Localization->AssignmentLoc->DepartmentDescr,
            PHP_EOL . PHP_EOL . "  <!--more-->". PHP_EOL . PHP_EOL,
            '<h2>'. __('Work Description', 'job-listings') .'</h2>',
            $details->Assignment->Localization->AssignmentLoc->WorkDescr,
            '<h2>'. __('Qualifications', 'job-listings') .'</h2>',
            $details->Assignment->Localization->AssignmentLoc->Qualifications,
            '<h2>'. __('Other', 'job-listings') .'</h2>',
            $details->Assignment->Localization->AssignmentLoc->AdditionalInfo
        ));

        //Contacts
        $item->contact = array(); 
        if(isset($details->Assignment->Localization->AssignmentLoc->ContactPersons->ContactPerson) && is_object($details->Assignment->Localization->AssignmentLoc->ContactPersons->ContactPerson) && !empty($details->Assignment->Localization->AssignmentLoc->ContactPersons->ContactPerson)) {
            foreach($details->Assignment->Localization->AssignmentLoc->ContactPersons->ContactPerson as $key => $detail) {
                $item->contact[] = array(
                    'name' => isset($detail->ContactName) ? reset($detail->ContactName) : '',
                    'phone' => isset($detail->Telephone) ? (string) $detail->Telephone : '',
                    'phone_sanitized' => isset($detail->Telephone) ? preg_replace('/\D/', '', (string) $detail->Telephone) : '',
                    'position' => isset($detail->Title) ? reset($detail->Title) : ''
                );
            }
        }

        return $item; 
    }

    /**
     * Add manual import button
     * @return bool|null
     */
    public function addImportButton() {
        global $wp;
        if(isset(get_current_screen()->post_type) && get_current_screen()->post_type == "job-listing") {
            $queryArgs = array_merge($wp->query_vars, array(__CLASS__ => 'true')); 
            echo '<a href="' . add_query_arg($queryArgs, home_url($wp->request)) . '" class="button-primary extraspace" style="float: right; margin-right: 10px;">'. __("Start Visma Import") .'</a>'; 
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

                if (count($target) == 1) {
                    $val = $item->{$target[0]};
                }

                if (count($target) == 2) {
                    $val = $item->{$target[0]}->{$target[1]};
                }

                if (count($target) == 3) {
                    $val = $item->{$target[0]}->{$target[1]}->{$target[2]};
                }

                if (count($target) == 4) {
                    $val = $item->{$target[0]}->{$target[1]}->{$target[2]}->{$target[3]};
                }

                $dataObject[$key] = $val;

                if (count($target) == 5) {
                    if ($key === 'occupationclassifications' || $key === 'departments') {
                        $val = $this->objectToArray($item->{$target[0]}->{$target[1]}->{$target[2]}->{$target[3]});
                        if (is_array($val)) {
                            for ($int = 0; $int < count($val); $int++) {
                                if ($this->getArrayDepth($val) > 2) {
                                    $level = $val[$int]['Level'];
                                    if ($key === 'departments') {
                                        if ($level == 2) {
                                            $dataObject[$key] = ($val[$int]['Name'] != '' && $val[$int]['Name'] != null) ? $val[$int]['Name'] : '';
                                        }
                                    } else {
                                        if ($level == 1) {
                                            $dataObject[$key] = $val[$int]['Name'];
                                        }
                                    }

                                } else {
                                    $level = $val['Level'];
                                    if ($key === 'departments') {
                                        if ($level == 2) {
                                            $dataObject[$key] = ($val['Name'] != '' && $val['Name'] != null) ? $val['Name'] : '';
                                        }
                                    } else {
                                        if ($level == 1) {
                                            $dataObject[$key] = $val['Name'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
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
            $this->updateTaxonomy($postId, 'occupationclassifications', 'job-listing-category', $dataObject); 

            // Taxonomys source system
            $this->updateTaxonomy($postId, 'source_system', 'job-listing-source', $dataObject); 

            //Update post with meta
            $this->updatePostMeta($postId, $dataObject); 

            return true;
        }

        return false;
    }

    /**
     * Mapping meta keys
     * @return array
     */
    public function metaKeyMap()
    {
        return array(
            'uuid' => array("@attributes", "AssignmentId"),
            'guid' => array("Guid"),
            'post_title' => array("Localization", "AssignmentLoc", "AssignmentTitle"),
            'post_content' => array("Localization", "AssignmentLoc", "WorkDescr"),
            'publish_start_date' => array("PublishStartDate"),
            'publish_end_date' => array("PublishEndDate"),
            'application_end_date' => array("ApplicationEndDate"),
            'employment_start_date' => array("EmploymentStartDate"),
            'employment_end_date' => array("EmploymentEndDate"),
            'ad_created' => array("Created"),
            'ad_modified' => array("Modified"),
            'ad_reference_nbr' => array("RefNo"),
            'number_of_positions' => array("NumberOfJobs"),
            'external_url' => array("ReadMoreUrl"),
            'is_internal' => array("IsInternal"),
            'location_name' => array("Localization", "AssignmentLoc", "Municipality", "Name"),

            'work_experience' => array("Localization", "AssignmentLoc", "WorkExperiencePrerequisite", "Name"),
            'employment_type' => array("Localization", "AssignmentLoc", "EmploymentType", "Name"),
            'employment_grade' => array("Localization", "AssignmentLoc", "EmploymentGrade", "Name"),
            'departments' => array("Localization", "AssignmentLoc", "Departments", "Department", "Name"),
            'occupationclassifications' => array(
                "Localization",
                "AssignmentLoc",
                "OccupationClassifications",
                "OccupationClassification",
                "Name"
            ),
            'source_system' => 'Visma',
            'has_expired' => array("hasExpired"),
            'number_of_days_left' => array("numberOfDaysLeft"),
            'contact' => array("contact")
        );
    }

    public function getDetails($guid) {

        //Get curl helper
        $curl = new \JobListings\Helper\Curl(true, $this->cacheTTL);

        //Fetch data
        $data = $curl->request(
            (string) $this->curlMethod,
            (string) $this->detailsUrl,
            (array) array(
                'guidGroup' => $this->queryParams['guidGroup'],
                'guidAssignment' => $guid
            )
        );

        //Create array with simple xml
        try {
            $data = simplexml_load_string($data);
        } catch (Exception $e) {
            if (!strstr($e->getMessage(), 'XML')) {
                throw $e;
            }
        }

        return $data; 
    }
}