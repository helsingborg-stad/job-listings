<?php

namespace JobListings;

/**
 * Class App
 * @package JobListings
 */
class Controller
{
    private $post = null; 

    /**
     * App constructor.
     */
    public function __construct()
    {
      add_filter('Municipio/viewData', array($this, 'singleViewData'));
    }


    /**
     * Remove inactive ads from archive
     * @return void
     */
    public function singleViewData($data)
    {

      //Populate post
      if(is_null($this->post)) {
        global $post; 
        $this->post = $post; 
      }

      //Check if single ad
      if(!$this->isSingleAd()) {
        return $data; 
      }

      $data['applyLink'] = ($this->getSourceSystem() == 'reachmee') ? '#job-listings-modal' : $this->getMeta('external_url'); 
      $data['preamble'] = $this->getMeta('preamble');  
      $data['content'] = apply_filters('the_content', $this->post->post_content); 
      $data['legal'] = $this->getMeta('legal_details'); 
      $data['startDate'] = $this->getMeta('publish_start_date'); 
      $data['endDate'] = $this->getMeta('publish_end_date'); 
      $data['referenceId'] = $this->getMeta('ad_reference_nbr');
      $data['numberOfPositions'] = $this->getMeta('number_of_positions'); 
      $data['expreience'] = $this->getMeta('number_of_positions'); 
      $data['employmentType'] = $this->getMeta('employment_type'); 
      $data['employmentGrade'] = $this->getMeta('employment_grade'); 
      $data['department'] = $this->getMeta('departments'); 
      $data['location'] = $this->getMeta('location_name'); 
      $data['contacts'] = $this->getContacts();
      $data['daysLeft'] = $this->daysLeft(); 
      $data['isExpired'] = $this->isExpired(); 
      $data['sourceSystem'] = $this->getSourceSystem(); 

      return $data; 
    }

    public function getMeta($key, $single = true) {
      return get_post_meta($this->post->ID, $key, $single); 
    } 

    public function isExpired() {
      if($this->daysLeft() < 0) {
        return true; 
      } 
      return false; 
    }

    public function daysLeft() {
      return (int) round((
        strtotime($this->getMeta('publish_end_date')) - 
        time()) / 86400
      );
    }

    public function getSourceSystem() {
      $term = get_the_terms($this->post->ID, 'job-listing-source');
      
      if(is_array($term)) {
          return array_pop($term)->slug; 
      } else {
          return null; 
      }
    }

    public function getContacts() {
      $contacts = $this->getMeta('contact');

      array_walk($contacts, function(&$contact) {
        $contact = (object) $contact; 
      }); 

      return $contacts; 
    }

    public function isSingleAd() {
      
      if($this->post->post_type == "job-listing") {
        return true; 
      }
      return false; 
    }
}
