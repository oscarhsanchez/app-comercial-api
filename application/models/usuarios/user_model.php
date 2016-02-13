<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_USER);
require_once(APPPATH.GENERIC_MODEL);

/**
 *
 * @Table "user"
 * @Entity "User"
 * @Country false
 * @Autoincrement false;
 *
 */
class user_model extends generic_model {

    function __construct() {
        parent::__construct();
    }


    function getUserById($userId) {
		$this->db->where('id', $userId);
		$query = $this->db->get('user');

		$user = $query->row(0, 'User');
		return $user;
	}

    function getUserByUserName($userName) {
        $this->db->where('username', $userName);
        $query = $this->db->get('user');

        $user = $query->row(0, 'User');
        return $user;
    }

    function getUserByUserNameAndPasswork($userName, $pass) {
        $this->db->where('username', $userName);
        $this->db->where('password', $pass);
        $query = $this->db->get('user');

        $user = $query->row(0, 'User');
        return $user;
    }

    function updateLastLogin($userId){
        $q = new stdClass();
        $this->db->set('last_login', 'CURRENT_TIMESTAMP', false);
        $this->db->where('id', $userId);

        return $this->db->update('user', $q);
    }

}


?>