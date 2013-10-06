<?php

if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class Tweet extends CI_Controller {

	/* show link to connect to Twiiter */
	public function index() {
		$this->load->library('twconnect');
                $this->load->model('Twitter_model');
		echo '<style>P { text-align: center }</style><body background="http://img1.jurko.net/wall/uploads/wallpaper_8547.jpg">';
		echo '<h2>Twitter App</h2>';
		echo '<p><a href="' . base_url() . 'index.php/tweet/redirect"><img src='. base_url() .'"red-button-hi.png" /></a></p>';
	}

	/* redirect to Twitter for authentication */
	public function redirect() {
		$this->load->library('twconnect');
                 $this->load->model('twitter_model');
		$ok = $this->twconnect->twredirect('tweet/callback');

		if (!$ok) {
			echo 'Could not connect to Twitter. Refresh the page or try again later.';
		}
	}


	/* return point from Twitter */
	public function callback() {
		$this->load->library('twconnect');

		$ok = $this->twconnect->twprocess_callback();

		if ( $ok ) { redirect('tweet/success'); }
			else redirect ('tweet/failure');
	}


	public function success() {
		echo '<body background="http://img1.jurko.net/wall/uploads/wallpaper_8547.jpg">';

		echo '<h3>Tweets</h3><br/>';

		$this->load->library('twconnect');
                 $this->load->model('twitter_model');

		$this->twconnect->twaccount_verify_credentials();

		$result = (array)$this->twconnect->tw_user_info;
		$stream_parameter = array("count" => '10',
		                                  "include_rts" => '1',
                                                  'screen_name'=>$result['screen_name'] );
                $stream_result =(array)( $this->twconnect->tw_get("https://api.twitter.com/1.1/statuses/user_timeline.json",$stream_parameter));
                for( $i = 0; $i < 1; $i++){
                foreach($stream_result as $value) {
                   $entities = (array)$value->entities;
                   $en = (array) $entities['urls'];

                   $user = $value->user->id_str;
                   $name = $value->user->screen_name;
                   $tweet_id = $value->id_str;
                   if(count($en)>0)
                       $has_url = 1;
                   else
                       $has_url = 0;
                   $tweet = $value->text;

                   if($has_url==0){
                          $param = array(
                                               "tweet_id" =>  $tweet_id,
                                               "screen_name" => $name,
                                               "twitter_id" => $user,
                                               "has_url" =>$has_url,
                                               "url" =>  "",
                                               "tweet" => $tweet
                                         );
                          $this->twitter_model->insert_tweet($param);

                   }

                   foreach($en as $url){
                                $uurl = substr($this->expandUrlLongApi($url->url),1,-1);
                                echo "\n";
                                $param = array(
                                               "tweet_id" =>  $tweet_id,
                                               "screen_name" => $name,
                                               "twitter_id" => $user,
                                               "has_url" =>$has_url,
                                               "url" =>  $uurl,
                                               "tweet" => $tweet
                                         );
                                $this->twitter_model->insert_tweet($param);
                                
                                echo '<dl>
                                <dt>@'.$name.'</dt>
                                <dd>-'.$tweet.'</dd>
                                </dl>';
                   }
                   $max_id =  $tweet_id;
                //   echo $max_id."\n";
                }
                 $stream_parameter = array("count" => '10',
		                                  "include_rts" => '1',
                                                  'screen_name'=>$result['screen_name'] ,
                                                  'max_id' => $max_id);
                $stream_result =(array)( $this->twconnect->tw_get("https://api.twitter.com/1.1/statuses/user_timeline.json",$stream_parameter));
             //    print_r( $stream_result);
                }
                echo '<strong>User with most links in tweets - </strong>@';
                print_r($this->twitter_model->mostlinks());
                echo '<br><br><strong>Top links in tweets - </strong><br>';
                foreach($this->twitter_model->toplinks(array('no'=>5)) as $key=>$value){
                       echo $key." appeared ".$value." times"."\n <br>" ;
                 }
                echo '</pre>';
		//session_destroy();
		$this->session->sess_destroy();

	}

        function TextAfterTag($input, $tag)
        {
            $result = '';
            $tagPos = strpos($input, $tag);

            if (!($tagPos === false))
            {
                    $length = strlen($input);
                    $substrLength = $length - $tagPos + 1;
                    $result = substr($input, $tagPos + 1, $substrLength); 
            }
        
            return trim($result);
        }

        function expandUrlLongApi($url)
        {
            $format = 'json';
            $api_query = "http://api.longurl.org/v2/expand?" .
                        "url={$url}&response-code=1&format={$format}";
            $ch = curl_init();
            curl_setopt ($ch, CURLOPT_URL, $api_query );
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, 0);
            curl_setopt($ch, CURLOPT_HEADER, false);
            $fileContents = curl_exec($ch);
            curl_close($ch);
            $s1=str_replace("{"," ","$fileContents");
            $s2=str_replace("}"," ","$s1");
            $s2=trim($s2);
            $s3=array();
            $s3=explode(",",$s2);
            $s4=$this->TextAfterTag($s3[0],(':'));
            $s4=stripslashes($s4);
            return $s4;
            }
	public function failure() {

		echo '<p>Twitter connect failed</p>';
	}


	public function clearsession() {

		$this->session->sess_destroy();

		redirect('/tweet');
	}

}
