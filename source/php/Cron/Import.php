<?php

namespace JobListings\Cron;

class Import
{
    public function __construct()
    {
        add_action('init', array($this, 'importXML')); 
    }

    public function importXml() {

        $postType = "ledigajobb"; 
        $xmlUrl = "https://recruit.visma.com/External/Feeds/AssignmentList.ashx"; 


        $curl = new Helper\Curl();
        $data = $curl->request('GET', $xmlUrl, array('guidGroup' => '67794f6d-af82-43a1-b5dc-bb414fd3eab1')); 
        $data = simplexml_load_string($data); 

        $data = $data->Assignments; 

        foreach($data as $item) {

            var_dump($data); 



        }


        

            die(".-"); 
    }
}
