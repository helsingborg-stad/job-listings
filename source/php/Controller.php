<?php

namespace JobListings;

/**
 * Class App
 * @package JobListings
 */
class Controller
{
    private $post = null;
    public $list = [];

    /**
     * App constructor.
     */
    public function __construct()
    {
        add_filter('Municipio/viewData', array($this, 'singleViewData'));
        add_filter('Municipio/Controller/Archive/getItems', array($this, 'prepareDataArchive'));

    }


    /**
     * Get single view data
     * @return array
     */
    public function singleViewData($data)
    {

        //Populate post
        if (is_null($this->post)) {
            global $post;
            $this->post = $post;
        }

        //Check if single ad
        if (!$this->isSingleAd()) {
            return $data;
        }

        $data['applyLink'] = ($this->getSourceSystem() == 'reachmee') ? '#job-listings-modal' : $this->getMeta('external_url');
        $data['preamble'] = $this->getMeta('preamble');
        $data['content'] = apply_filters('the_content', $this->post->post_content);
        $data['legal'] = $this->getMeta('legal_details');
        $data['startDate'] = $this->getMeta('publish_start_date');
        $data['endDate'] = $this->getMeta('publish_end_date');
        $data['referenceId'] = $this->getMeta('ad_reference_nbr');
        $data['projectNr'] = $this->getMeta('uuid');
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
        $data['preparedListData'] = $this->prepareData($data);

        return $data;
    }

    /**
     * Get archive view data
     * @param $data
     * @return array
     */
    public function archiveData($data)
    {

        if (is_null($this->post)) {
            global $post;
            $this->post = $post;
        }

        if (!$this->isArchive()) {
            return false;
        }

        $data['tableData'] = (count($this->list) > 0) ? $this->list : [];
        return $data;
    }

    /**
     * @param $data
     * @return mixed
     */
    public function prepareData($data)
    {

        $prepData['employeList'] = $this->prepareList($data);
        $prepData['contacts'] = $this->prepareContacts($data);

        return $prepData;
    }

    /**
     * Prepare data for application list
     * @param $data
     * @return array
     */
    public function prepareList($data)
    {
        $prepList = [];
        // prepare data for List
        if ($data['isExpired']) {
            array_push($prepList, [
                'label' => '<b>' . __('Deadline for applications:', 'job-listings') . '</b><br />
                    ' . $data['isExpired'] . ' (' . $data['daysLeft'] . ')']);
        }

        if ($data['projectNr']) {
            array_push($prepList, ['label' => '<b>' . __('Reference:', 'job-listings') . '</b> <br />' .
                $data['projectNr']]);
        }

        if ($data['startDate']) {
            array_push($prepList, ['label' => '<b>' . __('Published:', 'job-listings') . '</b><br />' .
                $data['startDate']]);
        }

        if ($data['numberOfPositions']) {
            array_push($prepList, ['label' => '<b>' . __('Number of positions:', 'job-listings') . '</b><br />' .
                $data['numberOfPositions']]);
        }

        if ($data['expreience']) {
            array_push($prepList, ['label' => '<b>' . __('Experience:', 'job-listings') . '</b><br />' .
                $data['expreience']]);
        }

        if ($data['employmentType']) {
            array_push($prepList, ['label' => '<b>' . __('Employment type:', 'job-listings') . '</b> <br />' .
                $data['employmentType']]);
        }

        if ($data['employmentGrade']) {
            array_push($prepList, ['label' => '<b>' . __('Extent:', 'job-listings') . '</b> <br />' .
                $data['employmentGrade']]);
        }

        if ($data['location']) {
            array_push($prepList, ['label' => '<b>' . __('Location:', 'job-listings') . '</b> <br />' .
                $data['location']]);
        }

        if ($data['department']) {
            array_push($prepList, ['label' => '<b>' . __('Company:', 'job-listings') . '</b> <br />' .
                $data['department']]);
        }

        return $prepList;
    }

    /**
     * Prepare data for Contacts
     * @param $data
     * @return mixed
     */
    public function prepareContacts($data)
    {
        $prepContact = [];

        if ($data['contacts']) {

            foreach ($data['contacts'] as $index => $contact) {

                if ($contact->name) {
                    $prepContact[$index]['contactPerson'] = $contact->name;
                }

                if ($contact->position) {
                    $prepContact[$index]['contactPosition'] = $contact->position;
                }

                if ($contact->phone) {
                    $prepContact[$index]['contactPhone'] = '<a href="tel:' . $contact->phone_sanitized . ' ' . $contact->phone . '">' . $contact->phone . '</a>';
                }
            }
        }

        return $prepContact;
    }

    /**
     * Prepare data for archive table
     * @param $archiveItems
     * @return void
     */
    public function prepareDataArchive($archiveItems)
    {

        if(isset($archiveItems) && !empty($archiveItems)){
            foreach ($archiveItems as $item) {

                $postMeta = get_post_meta($item->id);
                $href = $item->permalink ?: '';
                $title = $item->postTitle ?: '';
                $published = $postMeta['publish_start_date'][0] ?: '';
                $endDate = $postMeta['application_end_date'][0] ?: '';
                $category = $postMeta['occupationclassifications'][0] ?: '';

                array_push($this->list, ['href' => $href ?? '', 'columns' => [$title, $published, $endDate, $category]]);
            }

            add_filter('Municipio/viewData', array($this, 'archiveData'));
        }
    }



    /**
     * Get meta key, simplified
     * @return mixed
     */
    public function getMeta($key, $single = true)
    {
        return get_post_meta($this->post->ID, $key, $single);
    }

    /**
     * Check if ad is expired
     * @return boolean
     */
    public function isExpired()
    {
        if ($this->daysLeft() < 0) {
            return true;
        }
        return false;
    }

    /**
     * Calculate days left for ad
     * @return integer
     */
    public function daysLeft()
    {
        $daysLeft = (int)round((
            strtotime('+1 day', strtotime($this->getMeta('publish_end_date')) -
                time()) / 86400)
        );

        return ($daysLeft > 0) ? $daysLeft : 0;
    }

    /**
     * Get source system name
     * @return string/null
     */
    public function getSourceSystem()
    {
        $term = get_the_terms($this->post->ID, 'job-listing-source');

        if (is_array($term)) {
            return array_pop($term)->slug;
        } else {
            return null;
        }
    }

    /**
     * Get contacts
     * @return array
     */
    public function getContacts()
    {
        $contacts = $this->getMeta('contact');

        array_walk($contacts, function (&$contact) {
            $contact = (object)$contact;
        });

        return $contacts;
    }

    /**
     * Check if is single ad page
     * @return void
     */
    public function isSingleAd()
    {
        if ($this->post->post_type == "job-listing" && is_single()) {
            return true;
        }
        return false;
    }

    /**
     * Check if is archive
     * @return void
     */
    public function isArchive()
    {

        if ($this->post->post_type == "job-listing" && is_archive()) {
            return true;
        }

        return false;
    }
}
