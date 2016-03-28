<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_SESSION);

class session_model extends CI_Model {
    /**
     * Crea una nueva Session.
     *
     * @param $userId
     * @param $countryId
     * @param $phoneId
     * @return null|Session
     */
    function createSession($userId, $codigo, $roles, $countryId, $phoneId) {
		$token = sha1($userId.$countryId.time().TOKEN_SALT_KEY);
        $renew_token = sha1($userId.$countryId.time().RENEW_SALT_KEY);

		$sesion = new Session();
        $sesion->fk_user = $userId;
        $sesion->fk_pais = $countryId;
        $sesion->roles = $roles;
        $sesion->phone_id = $phoneId;
        $sesion->codigo = $codigo;
        $sesion->renew_token = $renew_token;
        $sesion->access_token = $token;
        $sesion->ip = $this->input->ip_address();
        $this->db->set('ip', "'".$this->input->ip_address()."'", false);
        $this->db->set('created_at', 'CURRENT_TIMESTAMP', false);
		$this->db->set('updated_at', 'CURRENT_TIMESTAMP', false);
		$this->db->set('expires_at', "CURRENT_TIMESTAMP + INTERVAL ".SESSION_TIMEOUT." SECOND", false);

        unset($sesion->created_at);
        unset($sesion->updated_at);

        if ($sesion->id = $sesion->_save(true, true))
			return $sesion;
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
     * @param $accesToken
     * @param $deviceId
     * @return Session
     */
    function getSessionByAccesToken($accessToken, $deviceId) {
        $this->db->where('access_token', $accessToken);
        $this->db->where('phone_id', $deviceId);
        $query = $this->db->get('session');

        $session = $query->row(0, 'Session');
        return $session;
    }

    /**
     * Renueva una Session a partir del token de renovacion.
     *
     * @param $userId
     * @param $phoneId
     * @param $countryId
     * @param $renewToken
     * @return Session|null
     */
    function renewSession($userId, $roles, $countryId, $phoneId, $renewToken) {
		$q = "SELECT renew_token FROM session WHERE fk_user = '$userId' AND fk_pais = '$countryId' AND phone_id = '$phoneId' ORDER BY created_at DESC";
        $query = $this->db->query($q);

        $result = $query->row();

        if(!$result){
			return null;
		}else{
			if ($result->renew_token == $renewToken) {
                return $this->createSession($userId, $result->codigo, $roles, $countryId, $phoneId);
            } else
                return null;
		}
	}


}

?>