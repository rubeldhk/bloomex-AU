<?php
//$conn = mysqli_connect("localhost", "chasingp_singh", "chasingpapers@123", "chasingp_fundlr_new"); 

session_start();
require './src/TwitterOAuth.php';

define('CONSUMER_KEY', 'b1cG19N19CvTuGalpQKSuASfa'); // add your app consumer key between single quotes
define('CONSUMER_SECRET', 'CGytGsg2j9ZUTiQUZHp7pDnkmmlmmQWG3npQblOJA8WUNqwvvv'); // add your app consumer secret key between single quotes
define('OAUTH_CALLBACK', 'https://stage1-a.amazon.bloomex.com.au/Go-through-checkout-process.html'); // your app callback URL

if (!isset($_SESSION['access_token'])) {
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET);
	$request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => OAUTH_CALLBACK));
	$_SESSION['oauth_token'] = $request_token['oauth_token'];
	$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	$url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

} else {
	$access_token = $_SESSION['access_token'];
	$connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, $access_token['oauth_token'], $access_token['oauth_token_secret']);
	$user = $connection->get("account/verify_credentials");
    
    echo $user->name;
     echo "<br/>";
    echo $user->location;
     echo "<br/>";
    echo $user->screen_name;
     echo "<br/>";
    echo $user->created_at;
     echo "<br/>";
    echo $user->profile_image_url;
     echo "<br/>"; 

    
    echo "<img src='$user->profile_image_url'>";
    echo "<br/>";
//    $q = "INSERT INTO usr_dtls(name, username) VALUES('$user->name', '$user->screen_name')";
//    mysqli_query($conn, $q);
//    
   
    
    echo "<pre>";
    print_r($user);
    echo "<pre>";
}