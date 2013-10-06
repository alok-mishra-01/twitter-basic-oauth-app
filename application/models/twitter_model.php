<?php

class Twitter_model extends CI_Model {

    function __construct()
    {
        // Call the Model constructor
        parent::__construct();
        $this->load->database();
    }


    function insert_tweet($param)
    {
        if(isset($param))
        {
          $id = $param['tweet_id'];
          $twitter_id = $param['twitter_id'];
          $has_url = $param['has_url'];
          $url = $param['url'];
          $screenname = $param['screen_name'];
          $tweet = $param['tweet'];

          $sql = "INSERT IGNORE INTO `tweets`(`tweet`, `tweet_id`, `twitter_id`, `has_url`, `url`, `screen_name`) VALUES ('".addslashes ($tweet)."','".$id."','".$twitter_id."','".$has_url."','".$url."','".$screenname."')";
          $this->db->query($sql);
        }
        else
        {
          echo "Error";
        }
    }
    
    function mostlinks()
    {
       $query = $this->db->query('SELECT * FROM tweets WHERE has_url=1');
       $hashmap = array();
       foreach ($query->result() as $value){
           if (array_key_exists($value->screen_name, $hashmap))
           {
             $count = $hashmap[$value->screen_name];
             $count++;
             $hashmap[$value->screen_name] = $count;
           }
           else
           {
             $count = 0;
             $count++;
             $hashmap[$value->screen_name] = $count;
           }
       }
       asort($hashmap);
       foreach($hashmap as $key=>$value){
       }
       //$last = current(array_slice($hashmap, -1));

       return $key;
    }
    function toplinks($param)
    {
       $query = $this->db->query('SELECT * FROM tweets WHERE has_url=1');
       $hashmap = array();
       foreach ($query->result() as $value){
           $comp = parse_url($value->url);
           $host = $comp['host'];
           if (array_key_exists($host, $hashmap))
           {
             $count = $hashmap[$host];
             $count++;
             $hashmap[$host] = $count;
           }
           else
           {
             $count = 0;
             $count++;
             $hashmap[$host] = $count;
           }
       }
       arsort($hashmap);


       
       //$last = current(array_slice($hashmap, -1));

       return array_slice($hashmap,0,$param['no']);;

    }


}