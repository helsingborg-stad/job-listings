<?php

namespace JobListings\Cron;

/**
 * Class Import
 * @package JobListings\Cron
 */
class Import
{
    public $postType = "job-listing";
    public $cacheTTL = 60 * 60; //Minutes

    /**
     * Import constructor.
     */
    public function __construct()
    {
        //Manual trigger
        add_action('admin_init', array($this, 'importXmlTrigger'));

        //Cron trigger
        add_action($this->getHookName(), array($this, 'importXml'));

        //Cron schedule
        add_action('admin_init', array($this, 'scheduleCronJob'));

        //Add manual import button(s)
        add_action('restrict_manage_posts', array($this, 'addImportButton'), 100);

        // Filter data from external party
        add_filter(str_replace("\\", "/", get_class($this)) . '/Item', array($this, 'normalize')); 
    }

    /**
     * Generate hook name
     * @param $prefix
     * @return string
     */
    public function getHookName(string $prefix = 'import_avalable_job_list_') : string
    {
        $searchFor = '\\';
        $replaceWith = '_';

        return $prefix . str_replace($searchFor, $replaceWith, get_class($this));
    }

    /**
     * Schedule Cron
     */
    public function scheduleCronJob()
    {
        if (!wp_next_scheduled($this->getHookName())) {
            wp_schedule_event(time(), 'hourly', $this->getHookName());
        }
    }

    /**
     * import XML trigger
     */
    public function importXmlTrigger()
    {
      if (isset($_GET[str_replace("\\", "", get_class($this))])) {
          $this->importXml();
          die("Data has been imported with; " . get_class($this));
      }
    }

    /**
     * Convert Object to array
     * @param $data
     * @return array|bool
     */
    public function objectToArray($data)
    {
        if ((!is_array($data)) && (!is_object($data))) {
            return false;
        }

        $result = array();

        $data = (array)$data;
        foreach ($data as $key => $value) {
            if (is_object($value)) {
                $value = (array)$value;
            }
            if (is_array($value)) {
                $result[$key] = $this->objectToArray($value);
            } else {
                $result[$key] = $value;
            }
        }

        return $result;
    }

    /**
     * Import XML
     * @return bool|null
     */
    public function importXml()
    {

        //Get curl helper
        $curl = new \JobListings\Helper\Curl(true, $this->cacheTTL);

        //Fetch data
        $data = $curl->request(
            (string) $this->curlMethod,
            (string) $this->baseUrl,
            (array) $this->queryParams
        );

        //Decode html entities (reachmee somtimes stores double encoded data)
        /*$data = html_entity_decode(
                    html_entity_decode(
                            html_entity_decode($data)
                        )
                    );*/ 

        //Translate url's & to &amp; 
        $data = str_replace("&", "&amp;", $data); 

        //Create array with simple xml
        try {
            $data = simplexml_load_string($data);
        } catch (Exception $e) {
            if (!strstr($e->getMessage(), 'XML')) {
                throw $e;
            }
        }

        //Get main node
        $data = json_decode(json_encode($data->{$this->baseNode}), false)->{$this->subNode};

        //Check if valid list, update jobs
        if (isset($data) && !empty($data)) {

            foreach ($data as $item) {

                $item = apply_filters(str_replace("\\", "/", get_class($this))."/Item", $item); 

                if ($item) {
                    $this->updateItem($item);
                }
            }
            return true;
        }

        return null; //Unsuccessfull, no new data
    }

    /**
     * Checking how many levels in multidimensional array
     * @param $array
     * @return float|int
     */
    public function getArrayDepth($array)
    {
        $maxIndentation = 1;
        $array_str = print_r($array, true);
        $lines = explode("\n", $array_str);
        foreach ($lines as $line) {
            $indentation = (strlen($line) - strlen(ltrim($line))) / 4;
            if ($indentation > $maxIndentation) {
                $maxIndentation = $indentation;
            }
        }
        return ceil(($maxIndentation - 1) / 2) + 1;
    }
    /**
     *  Get posts
     * @param $search
     * @return mixed|null
     */
    public function getPost($search)
    {
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

        if (!empty($post) && is_array($post)) {
            $post = array_pop($post);
            if(isset($post->ID) && is_numeric($post->ID)) {
              return $post; 
            }
        }

        return null;
    }

    /**
     * Update taxonmy
     * @param $postId
     * @param $termSourceKey
     * @param $termId
     * @param $dataObject
     * @return array|bool|false|\WP_Error
     */
    public function updateTaxonomy($postId, $termSourceKey, $termId, $dataObject)
    {

        if (isset($dataObject[$termSourceKey]) && !empty($dataObject[$termSourceKey])) {

            $dataObject[$termSourceKey] =  ucfirst(str_replace(", ", " - ",
                $dataObject[$termSourceKey]));

            // Checking terms
            $term = term_exists($dataObject[$termSourceKey], $termId);

            if (is_null($term)) {
                // Adding terms
                $term = wp_insert_term(
                    $dataObject[$termSourceKey],
                    $termId,
                    array(
                        'description' => $dataObject[$termSourceKey],
                        'slug' => sanitize_title($dataObject[$termSourceKey])
                    )
                );
            }

            // Remove previous connections
            wp_delete_object_term_relationships($postId, $termId); 

            // Connecting term to post
            return wp_set_post_terms($postId, $dataObject[$termSourceKey], $termId, true);

        }

        return false; 
    }

    /**
     *  Update post meta
     * @param $postId
     * @param $dataObject
     * @return bool
     */
    public function updatePostMeta($postId, $dataObject)
    {
        if (is_array($dataObject) && !empty($dataObject)) {
            foreach ($dataObject as $metaKey => $metaValue) {

                if ($metaKey == "") {
                    continue;
                }

                if ($metaValue != get_post_meta($postId, $metaKey, true)) {
                    update_post_meta($postId, $metaKey, $metaValue);
                }
            }

            return true; 
        }

        return false; 
    }

    public function isMultidimensionalArray($a)
    {

      if(!is_array($a)) {
        return false; 
      }

      $rv = array_filter($a,'is_array');
      if(count($rv)>0) {
        return true;
      }

      return false;
    }
}