<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//require_once(APPPATH.ENTITY_EXCEPTION);

class log_model extends CI_Model {

	function logWithSession($exception, $userId, $entitySecret, $deviceId, $token) {
		$q = new stdClass();
		$q->id_usuario = $userId;
		$q->entity_secret = $entitySecret;
		$q->id_dispositivo = $deviceId;		
		$q->token = $token;
		$q->ip = $this->input->ip_address();
		$q->code = $exception->getCode();
		$q->message =  $exception->getMessage();
		$q->data =  $exception->getData();		
		return $this->db->insert('log', $q)	;
	}

	function log($exception) {	
		$q = new stdClass();	
		$q->code = $exception->getCode();
		$q->message =  $exception->getMessage();
		$q->data =  $exception->getData();		
		return $this->db->insert('log', $q)	;
	}

	function logParamsWithSession($code, $message, $data, $userId, $entitySecret, $deviceId, $token) {
		if (!$entitySecret)
            $entitySecret = "";

        if (!$deviceId)
            $deviceId = "";

        if (!$token)
            $token = "";

        $q = new stdClass();
		$q->id_usuario = $userId;
		$q->entity_secret = $entitySecret;
		$q->id_dispositivo = $deviceId;		
		$q->token = $token;
		$q->ip = $this->input->ip_address();
		$q->code = $code;
		$q->message = $message;
		$q->data =  $data;		
		return $this->db->insert('log', $q)	;
	}

	function logParams($code, $message, $data) {
		$q = new stdClass();
		$q->ip = $this->input->ip_address();
		$q->code = $code;
		$q->message = $message;
		$q->data =  $data;		
		return $this->db->insert('log', $q)	;
	}

    function logTpv($log) {
        $q = new stdClass();
        $q->log = $log;
        return $this->db->insert('log_tpv', $q)	;
    }

}


?>