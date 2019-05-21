<?php

namespace JobListings\Cron;

/**
 * Class Import
 * @package JobListings\Cron
 */
class Import
{

    private $uuid = ""; //The unique identifier of each item in XML
    //private $guidGroup = "67794f6d-af82-43a1-b5dc-bb414fd3eab1";
    //private $baseUrl = "https://recruit.visma.com/External/Feeds/AssignmentList.ashx";
    private $postType = "job-listing";
    private $baseNode = "Assignments";
    private $subNode = "Assignment";

    private $cacheTTL = 60*60; //Minutes

    /**
     * Import constructor.
     */
    public function __construct()
    {
        //Manual trigger
        add_action('admin_init', array($this, 'importXmlTrigger'));

        //Cron trigger
        add_action('import_avalable_job_list', array($this, 'importXml'));

        //Cron schedule
        add_action('admin_init', array($this, 'scheduleCronJob'));
    }

    /**
     * Schedule Cron
     */
    public function scheduleCronJob() {
        if (!wp_next_scheduled ('import_avalable_job_list')) {
            wp_schedule_event(time(), 'twicedaily', 'import_avalable_job_list');
        }
    }

    /**
     * import XML trigger
     */
    public function importXmlTrigger() {
        if(isset($_GET['jobListingImport'])) {
            $this->importXml();
            //die("Stuff has been imported.");
        }
    }

    /**
     * Import XML
     * @return bool|null
     */
    public function importXml() {

        //Get curl helper
        $curl = new \JobListings\Helper\Curl(true, $this->cacheTTL);

        //Fetch data
        $data = $curl->request(
            'GET',
            get_field('job_listing_xml_api_url', 'option'),
            array(
                'guidGroup' => get_field('job_listing_xml_api_url_id', 'option')
            )
        );

        //Create array with simple xml
        try {
            $data = simplexml_load_string($data);
        } catch(Exception $e) {
            if(!strstr($e->getMessage(), 'XML')) throw $e;
        }

        //Get main node
        $data = json_decode(json_encode($data->{$this->baseNode}), FALSE)->{$this->subNode};

        //Check if valid list, update jobs
        if(isset($data) && is_array($data) && !empty($data)) {
            foreach ($data as $item) {
                $this->updateItem($item);
            }
            return true;
        }

        return null; //Unsuccessfull, no new data
    }

    /**
     * Update Item
     * @param $item
     * @return bool
     */
    private function updateItem($item) {
        if(isset($item) && is_object($item) && !empty($item)) {

            //Create Response object
            $dataObject = array();

            //Gather data
            foreach ($this->metaKeyMap() as $key => $target) {

                if(count($target) == 1) {
                    $val = $item->{$target[0]};
                }

                if(count($target) == 2) {
                    $val = $item->{$target[0]}->{$target[1]};
                }

                if(count($target) == 3) {
                    $val = $item->{$target[0]}->{$target[1]}->{$target[2]};
                }

                if(count($target) == 4) {
                    $val = $item->{$target[0]}->{$target[1]}->{$target[2]}->{$target[3]};
                }

                if(count($target) == 5) {
                    $val = $item->{$target[0]}->{$target[1]}->{$target[2]}->{$target[3]}->{$target[4]};
                }

                $dataObject[$key] = $val;

            }

            //Get matching post
            $postObject = $this->getPost(
                array(
                    'key' => 'uuid',
                    'value' => $dataObject['uuid']
                )
            );
            $postId = $postObject->ID;

            //Not existing, create new
            if (is_null($postId)) {
                $postId = wp_insert_post(
                    array(
                        'post_title' => $dataObject['post_title'],
                        'post_content' => $dataObject['post_content'],
                        'post_type' => $this->postType,
                        'post_status' => 'publish'
                    )
                );
            } else {

                //Create diffable array
                $updateDiff = array(
                    $postObject->post_title,
                    $postObject->post_content,
                    $dataObject['post_title'],
                    $dataObject['post_content']
                );

                //Diff data
                if(count(array_unique($updateDiff)) != count($updateDiff)) {
                    wp_update_post(
                        array(
                            'ID' => $postId,
                            'post_title' => $dataObject['post_title'],
                            'post_content' => $dataObject['post_content']
                        )
                    );
                }
            }

            //Update if there is data
            if(is_array($dataObject) && !empty($dataObject)) {
                foreach($dataObject as $metaKey => $metaValue) {
                    if($metaKey == "") {
                        continue;
                    }
                    if($metaValue != get_post_meta($postId, $metaKey, true)) {
                        update_post_meta($postId, $metaKey, $metaValue);
                    }
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Key map
     * @return array
     */
    private function metaKeyMap() {
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


            //'departments' => array("Localization", "AssignmentLoc", "Departments", "Department", "Name")

        );
    }

    /**
     *  Get posts
     * @param $search
     * @return mixed|null
     */
    private function getPost($search) {

        $post = get_posts(
            array(
                'meta_query' => array(
                    array(
                        'key' => $search['key'],
                        'value' => $search['value']
                    )
                ),
                'post_type' => $this->postType,
                'posts_per_page' => 1,
                'post_status' => 'all'
            )
        );

        if(!empty($post) && is_array($post)) {
            return array_pop($post);
        }

        return null;
    }
}