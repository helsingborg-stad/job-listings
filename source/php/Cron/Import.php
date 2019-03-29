<?php

namespace JobListings\Cron;

class Import
{

    private $uuid = ""; //The unique identifier of each item in XML
    private $guidGroup = "67794f6d-af82-43a1-b5dc-bb414fd3eab1"; 
    private $baseUrl = "https://recruit.visma.com/External/Feeds/AssignmentList.ashx"; 
    private $postType = "job-listing";
    private $baseNode = "Assignments"; 
    private $subNode = "Assignment"; 

    private $cacheTTL = 60*60; //Minutes 

    public function __construct()
    {
        add_action('admin_init', array($this, 'importXmlTrigger')); 
    }

    public function importXmlTrigger() {
        if(isset($_GET['jobListingImport'])) {
            $this->importXml($this->baseUrl, $this->guidGroup); 
            die("Stuff has been imported.");
        }
    }

    public function importXml($baseUrl, $guidGroup) {

        //Get curl helper
        $curl = new \JobListings\Helper\Curl(true, $this->cacheTTL);

        //Fetch data 
        $data = $curl->request(
            'GET',
            $baseUrl,
            array(
                'guidGroup' => $guidGroup
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

                //Store
                $dataObject[$key] = $val; 

            }

            //Get matching post
            $postId = $this->getPostID(
                array(
                    'key' => 'uuid', 
                    'value' => $dataObject['uuid']
                )
            );

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

    private function metaKeyMap() {
        return array(
            'uuid' => array("@attributes", "AssignmentId"),
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
            'location_name' => array("Localization", "AssignmentLoc", "Municipality", "Name")
        ); 
    }

    private function getPostID($search) {
        
        if (!is_array($search)) {
            die("Must be key -> value pair"); 
        }

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
            return array_pop($post)->ID;
        }

        return null; 
    }
}
