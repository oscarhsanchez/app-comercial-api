<?php

class esocialmemcache {
	
	private $memcache; 

	public function __construct()
	{

		$this->memcache = new Memcache; // instantiating memcache extension class
        $this->memcache->connect(MEMCACHE_SERVER,11211);

	}

	public function add($key, $value, $compress, $expires) {
        if ($key && $value)
		    $this->memcache->add($key, $value, $compress, $expires);
	}

    public function set($key, $value, $compress, $expires) {
        if ($key && $value)
            $this->memcache->set($key, $value, $compress, $expires);
    }

	public function get($key) {
		return $this->memcache->get($key);
	}

    public function delete($key) {
        return $this->memcache->delete($key);
    }

	public function getServerVersion() {
		return $this->memcache->getVersion();
	}

	

}

?>