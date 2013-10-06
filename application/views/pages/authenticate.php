<?php
$this->db->reconnect();
require 'application/libraries/twitterlib/tmhOAuth.php';
require 'application/libraries/twitterlib/tmhUtilities.php';
?>
 <script>
 myfun(){
    window.open('<?php echo $a; ?>');
 }
 </script>

<div class="fbpagewrapper">
<div id="fbpageform" class="pageform">


<?php

$tmhOAuth = new tmhOAuth(array(
  'consumer_key'    => 'qou95V0xU8i9z7HZy54jQ',
  'consumer_secret' => 'lJ0SrBdfJvF4qOQdJCdvkhDvbRljXiEWxpAVYIY0Ik'
));
$a = '';

function welcome() {
  echo <<<EOM
tmhOAuth PHP Out-of-band.
This script runs the OAuth flow in out-of-band mode. You will need access to
a web browser to authorise the application. At the end of this script you will
be presented with the user token and secret needed to authenticate as the user.

EOM;
}

function request_token($tmhOAuth) {
  $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), array(
    'oauth_callback' => 'oob',
  ));

  if ($code == 200) {
    $oauth_creds = $tmhOAuth->extract_params($tmhOAuth->response['response']);

    // update with the temporary token and secret
    $tmhOAuth->config['user_token']  = $oauth_creds['oauth_token'];
    $tmhOAuth->config['user_secret'] = $oauth_creds['oauth_token_secret'];

    $url = $tmhOAuth->url('oauth/authorize', '') . "?oauth_token={$oauth_creds['oauth_token']}";
      $o = $oauth_creds['oauth_token'];
      $s = $oauth_creds['oauth_token_secret'];
  } else {
    echo "There was an error communicating with Twitter. {$tmhOAuth->response['response']}" . PHP_EOL;
    die();
  }
  
  return $url;
}




function access_token($tmhOAuth, $pin) {
           $code = $tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
    'oauth_verifier' => $pin
  ));
      echo $oauth_creds['oauth_token'];

  if ($code == 200) {
    $oauth_creds = $tmhOAuth->extract_params($tmhOAuth->response['response']);

    // print tokens
    echo <<<EOM
Congratulations, below is the user token and secret for {$oauth_creds['screen_name']}.
Use these to make authenticated calls to Twitter using the application with
consumer key: {$tmhOAuth->config['consumer_key']}

User Token: {$oauth_creds['oauth_token']}
User Secret: {$oauth_creds['oauth_token_secret']}

EOM;
  } else {
    echo "There was an error communicating with Twitter. {$tmhOAuth->response['response']}" . PHP_EOL;
  }
  //var_dump($tmhOAuth);
  //die();
}

//welcome();
$a = request_token($tmhOAuth);

$temparray = array("tmp =>".$tmhOAuth);


$this->session->set_userdata($temparray);

//$_SESSION['tmp'] =  $tmhOAuth;
//$pin = '5748926';
//access_token($tmhOAuth, $pin);
?>
<form id="form" name="form" method="post" action="oob1.php">
<p>Get Your Pin Here. <?php echo '<a href="'.$a.'" target="_blank">Click Here</a>';?>
</p>
<label>Pin
<span class="small">provided by twitter</span>
</label>
 <input type="text" name="pin">
	<?php
           echo '<input type="text" name="token" style="visibility:hidden;display:none;" value="'. $tmhOAuth->config['user_token'].'">';
            echo '<input type="text" name="secret" style="visibility:hidden;display:none;" value="'.$tmhOAuth->config['user_secret'].'">';
    ?>

<button type="submit" class="button" id="submit_button">Activate</button>
<div class="spacer"></div>
</form>
</div>
</div>
</body>
</html>
</body>
</html>
