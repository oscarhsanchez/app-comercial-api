<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

//require(APPPATH.ENTITY_USER);

class session_model extends CI_Model {
    /**
     * Crea una nueva sesion.
     *
     * @param $userId
     * @param $deviceId
     * @param $entityId
     * @return null|token
     */
    function createSession($userId, $deviceId, $entityId) {
		$token = sha1($userId.$deviceId.time().TOKEN_SALT_KEY);
		$q = new stdClass();
		$q->id_usuario = $userId;
		$q->fk_entidad = $entityId;
		$q->id_dispositivo = $deviceId;		
		$q->token = $token;
		$q->ip = $this->input->ip_address();
		$this->db->set('created_at', 'CURRENT_TIMESTAMP', false);
		$this->db->set('updated_at', 'CURRENT_TIMESTAMP', false); 
		$this->db->set('expires_at', "CURRENT_TIMESTAMP + INTERVAL ".SESSION_TIMEOUT." MINUTE", false);
		if ($this->db->insert('api_session', $q) && $this->db->affected_rows() > 0)	
			return $token;
		else
			return null;
	}

    /**
     * Devuelve la hora del servidor de la base de datos.
     *
     * @return DateTime
     */
    function getServerDateTime() {
        $q = "SELECT NOW(6) as DateTime";
        $query = $this->db->query($q);
        $result = $query->row();
        return $result->DateTime;

    }

    /**
     * Actualiza la sesion del usuarios
     *
     * @param $token
     * @return Boolean
     */
    function updateSession($token) {
		$this->db->set('expires_at', "CURRENT_TIMESTAMP + INTERVAL ".SESSION_TIMEOUT." MINUTE", false);
		$this->db->where("token", $token);
		return $this->db->update('api_session');
	}

    /**
     * Valida si una sesion esta activa o no
     *
     * @param $userId
     * @param $deviceId
     * @param $entityId
     * @param $token
     * @return token|null
     */
    function validateSession($userId, $deviceId, $entityId, $token) {
		$q = new stdClass();
		$this->db->where("id_usuario = '".$userId."' AND id_dispositivo = '".$deviceId."' AND fk_entidad = ".$entityId." AND token = '".$token."' AND expires_at > CURRENT_TIMESTAMP ");
		$q = $this->db->get("api_session");
		$row = $q->row();
		if(!$row){
			return null;
		}else{
			//Cada Vez que validemos la sesion actualizamos la fecha
			$this->updateSession($token);
			return $row->token;
		}
	}

    /**
     * Comprueba si una session a caducado y ha pasado el margen establecido
     *
     * @param $userId
     */
    function monitorSession($userId) {
        $q = new stdClass();
        $this->db->select("MAX(expires_at) AS expires");
        $this->db->where("id_usuario", $userId);
        $q = $this->db->get("api_session");
        $row = $q->row();
        if($row){
            $expires = new DateTime($row->expires);
            $actual = new DateTime();
            $interval = $expires->diff($actual);
            // Si es negativa y ademas de los 60 min de session ha pasado 5 min, lo consideramos como error
            if (!$interval->invert && $interval->i > 5)
                return false;
            else
                return true;
        }

        return false;
    }

}

?>