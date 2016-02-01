<?php

require_once(APPPATH.ENTITY_EXCEPTION);
require_once("esocialrestclient.php");

class esocialsolr {
	
	private $solrServer;  
	private $restClient;

	public function __construct()
	{

		$this->solrServer = SOLR_SERVER; 
		$this->restClient = new esocialrestclient($this->solrServer, false);

	}
	
	public function insertDocs($collection, $docs, $softCommit=true) {
		$docList = array();

		foreach ($docs as $doc) {
			$solrDoc = $this->mergeClasses($doc->uniqueId, $doc->fields);
			$docList[] = $solrDoc;
		}

		$uri = "/update";

		if ($softCommit) {
			$uri = $uri . "?commit=true&softcommit=true";
		}

		$response = $this->restClient->post($collection.$uri, json_encode($docList), "application/json");

		if ($response["header"]['http_code'] == 200) {
			return true;
		} else {
			return false;
		}

	}

	public function deleteDocs($collection, $uniqueIds, $softCommit=true) {
		$del = "{";

		foreach ($uniqueIds as $uid) {			
			$del = $del . '"delete"' . ":" . json_encode($uid) . ",";			
		}
		$del = substr($del, 0, strlen($del)-1);

		$del = $del . "}";

		echo $del;

		$uri = "/update";

		if ($softCommit) {
			$uri = $uri . "?commit=true&softcommit=true";
		}

		$response = $this->restClient->post($collection.$uri, $del, "application/json");

		if ($response["header"]['http_code'] == 200) {
			return true;
		} else {
			return false;
		}

	}

	public function updateDocsFields($collection, $docs, $softCommit=true) {
		$docList = array();

		foreach ($docs as $doc) {
			foreach ($doc->fields as $property => $value)
		    {
		    	$field = new stdClass();
		    	$field->set = $value;
		        $doc->fields->$property = $field;
		    }

			$solrDoc = $this->mergeClasses($doc->uniqueId, $doc->fields);
			$docList[] = $solrDoc;
		}

		$uri = "/update";

		if ($softCommit) {
			$uri = $uri . "?commit=true&softcommit=true";
		}

		$response = $this->restClient->post($collection.$uri, json_encode($docList), "application/json");

		if ($response["header"]['http_code'] == 200) {
			return true;
		} else {
			return false;
		}
	}
	
	public function incrementField($collection, $docs, $softCommit=true) {
		$docList = array();

		foreach ($docs as $doc) {
			foreach ($doc->fields as $property => $value)
		    {
		    	$field = new stdClass();
		    	$field->inc = $value;
		        $doc->fields->$property = $field;
		    }

			$solrDoc = $this->mergeClasses($doc->uniqueId, $doc->fields);
			$docList[] = $solrDoc;
		}

		$uri = "/update";

		if ($softCommit) {
			$uri = $uri . "?commit=true&softcommit=true";
		}

		$response = $this->restClient->post($collection.$uri, json_encode($docList), "application/json");

		if ($response["header"]['http_code'] == 200) {
			return true;
		} else {
			return false;
		}
	}

	public function deleteField($collection, $docs, $softCommit=true) {
		$docList = array();

		$fields = new stdClass();

		foreach ($docs as $doc) {
			foreach ($doc->fields as $property)
		    {
		    	$field = new stdClass();
		    	$field->set = null;
		        $fields->$property = $field;
		    }

			$solrDoc = $this->mergeClasses($doc->uniqueId, $fields);
			$docList[] = $solrDoc;
		}

		$uri = "/update";

		if ($softCommit) {
			$uri = $uri . "?commit=true&softcommit=true";
		}

		$response = $this->restClient->post($collection.$uri, json_encode($docList), "application/json");

		if ($response["header"]['http_code'] == 200) {
			return true;
		} else {
			return false;
		}
	}

	public function addToMultiValue($collection, $docs, $softCommit=true) {
		$docList = array();

		foreach ($docs as $doc) {
			foreach ($doc->fields as $property => $value)
		    {
		    	$field = new stdClass();
		    	$field->add = $value;
		    	print_r($field);
		        $doc->fields->$property = $field;
		    }

			$solrDoc = $this->mergeClasses($doc->uniqueId, $doc->fields);
			$docList[] = $solrDoc;
		}

		$uri = "/update";

		if ($softCommit) {
			$uri = $uri . "?commit=true&softcommit=true";
		}

		$response = $this->restClient->post($collection.$uri, json_encode($docList), "application/json");

		if ($response["header"]['http_code'] == 200) {
			return true;
		} else {
			return false;
		}
	}

	
	private function mergeClasses($class1, $class2) {

		foreach ($class2 as $property => $value)
	    {
	        $class1->$property = $value;
	    }

	    return $class1;

	}

	public function prueba() {
		print_r($this->restClient->get("/solr/collection1/select?q=jaime&wt=json"));

		$solrSchema = new stdClass();
		$solrSchema->prueba = "kaoa";
		$solrSchema->dos = 1232;

		$field = new stdClass();
		$field->set = $solrSchema->prueba;

		$solrSchema->prueba = $field;

		print_r(json_encode($solrSchema));


	}

	

	

}

?>