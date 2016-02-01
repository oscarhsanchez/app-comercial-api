<?php

class esocialtwitter {
	
	private $cb; 

	public function __construct()
	{
		require_once 'codebird.php';

		Codebird::setConsumerKey(TWITTER_KEY, TWITTER_SECRET);
        $this->cb = Codebird::getInstance();   

	}

	public function getUserByToken($token, $tokenSecret) {
		$this->cb->setToken($token, $tokenSecret);
        return $this->cb->account_verifyCredentials();
	}

	public function getUserFriendList($token, $tokenSecret) {
		$this->cb->setToken($token, $tokenSecret);

		$list = array();

        $result = $this->cb->friends_list();
        $list = $result->users;
        $nextCursor = $result->next_cursor_str;
        if ($nextCursor > 0) {
        	$result = $this->cb->friends_list('cursor=' . $nextCursor);
        	$nextCursor = $result->next_cursor_str;
        	array_merge($list, $result->users);
    	}

    	return $list;
		


	}

}

?>