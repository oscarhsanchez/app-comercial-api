<?php

class APIerror{
	
	private $code;
	private $description;
		

	function APIerror($code, $description="") {
		
		$this->code=$code;
		$this->description=$description;

		$descriptions = array (
	    	1000  => "Invalid Number of Params",
	    	1001  => "Invalid Number of Header Params",
	    	2000  => "Error Saving Data",
            2001  => "Invalid Property Name",
	    	3000  => "Invalid Access Token",
	    	4000  => "Param verification Error",
	    	4001  => "FBID verification Error",
	    	4002  => "User Not Registered",
	    	4003  => "Invalid User Name or Pass",
	    	4004  => "Missing User mail",
	    	4005  => "Missing User UserName",
	    	4006  => "User Wikking Id already used",
	    	4007  => "Missing User mail and User UserName already used",
	    	4008  => "Missing User mail and Missing User UserName",
	    	4009  => "User mail already used",
	    	4010  => "Twitter verification Error",
	    	4011  => "Device already exist for indicated phone",
	    	4012  => "Code verification error",
	    	4013  => "Device not registered",
	    	4014  => "Device not Activated",
	    	4015  => "Entity not found",
	    	4016  => "Entity verification failed",
	    	4017  => "Delegacion not found",
            4018  => "No Lines defined for Document",
            4019  => "Error Sending mail.",
	    	5000  => "Access Forbiden",
	    	6000  => "Error getting information requested",
            6001  => "Error getting information from memcache",
            6002  => "Error exceeded max pagination size"

		);

		if ($description == "")
			$this->description = $descriptions[$this->code];		
		
	}
	
	public function getValues(){
		return get_object_vars($this);
	}


	/**
	 * [description here]
	 *
	 * @return [type] [description]
	 */
	public function getCode() {
	    return $this->code;
	}
	
	/**
	 * [Description]
	 *
	 * @param [type] $newcode [description]
	 */
	public function setCode($code) {
	    $this->code = $code;
	
	    return $this;
	}


	/**
	 * [description here]
	 *
	 * @return [type] [description]
	 */
	public function getDescription() {
	    return $this->description;
	}
	
	/**
	 * [Description]
	 *
	 * @param [type] $newdescription [description]
	 */
	public function setDescription($description) {
	    $this->description = $description;
	
	    return $this;
	}

}
?>