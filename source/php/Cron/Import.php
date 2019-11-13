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
        add_action('import_avalable_job_list' . get_class($this), array($this, 'importXml'));

        //Cron schedule
        add_action('admin_init', array($this, 'scheduleCronJob'));

        //Add manual import button(s)
        add_action('restrict_manage_posts', array($this, 'addImportButton'), 100);
 
    }

    /**
     * Schedule Cron
     */
    public function scheduleCronJob()
    {
        if (!wp_next_scheduled('import_avalable_job_list_' . get_class($this))) {
            wp_schedule_event(time(), 'twicedaily', 'import_avalable_job_list_' . get_class($this));
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
        if ((!is_array($data)) and (!is_object($data))) {
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

    public function isMultidimensionalArray($a) {

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