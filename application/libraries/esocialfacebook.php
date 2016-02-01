<?php

class esocialfacebook {
	
	private $facebook; 

	public function __construct()
	{

		$config = array(
            "appId" => FB_APP_ID,
            "secret" => FB_SECRET
        );
	        	
	    require_once 'facebook.php';

	    $this->facebook = new Facebook($config);

	}

	public function getUserByToken($facebookToken) {
		$this->facebook->setAccessToken($facebookToken);        
	    $res = $this->facebook->api("/me", "GET");
	    return $res;
	}

	public function getUserFriendList($facebookToken) {
		$this->facebook->setAccessToken($facebookToken);        
	    $res = $this->facebook->api('/me/friends/?limit=5000&offset=0');
	    return $res;
	}

}

?>