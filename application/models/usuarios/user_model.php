<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_USER);


class user_model extends CI_Model {

	function getUserById($userId) {
		$this->db->where('id', $userId);
		$query = $this->db->get('user');

		$user = $query->row(0, 'user');
		return $user;
	}

    function getUserByUserName($userName) {
        $this->db->where('username', $userName);
        $query = $this->db->get('user');

        $user = $query->row(0, 'user');
        return $user;
    }

    function getUserByUserNameAndPasswork($userName, $pass) {
        $this->db->where('username', $userName);
        $this->db->where('password', $pass);
        $query = $this->db->get('user');

        $user = $query->row(0, 'user');
        return $user;
    }

    function updateLastLogin($userId){
        $q = new stdClass();
        $this->db->set('last_login', 'CURRENT_TIMESTAMP', false);
        $this->db->where('id', $userId);

        return $this->db->update('user', $q);
    }



	//---------------------- ENTITY_USER FUNCTIONS ------------------------------
	
	private function getEntityUserQuery() {
		$this->db->_protect_identifiers = false;
		$this->db->select('usuarios.id_usuario, usuarios.pass, usuarios.salt, usuarios.nombre, usuarios.apellidos, usuarios.tipo, usuarios.mail, usuarios.telefono, usuarios.estado, usuarios.last_login, usuarios.token ', false);
		$this->db->select('entityUser.fk_entidad, entityUser.pk_usuario_entidad, entityUser.cod_usuario_entidad, entityUser.fk_tipo_agente, entityUser.id_seg_rol, entityUser.fk_delegacion, entityUser.fk_almacen_camion AS fk_almacen, entityUser.fk_terminal_tpv, entityUser.fk_canal_venta, entityUser.serie_id, entityUser.serie_anio, entityUser.num_inventario, entityUser.num_captacion, entityUser.num_mov_almacen, entityUser.num_incidencia, entityUser.send_ddbb, entityUser.upd_series, entityUser.estado as estado_entity_user, entityUser.token as token_entity_user, bool_search_server_client,', false);
        $this->db->select('canales_venta.cod_canal_venta, tipo_agente.cod_tipo_agente');
        $this->db->from('usuarios');
		$this->db->join('r_usu_emp AS entityUser', 'usuarios.id_usuario = entityUser.id_usuario');
        $this->db->join('canales_venta', 'canales_venta.pk_canal_venta = entityUser.fk_canal_venta', 'left');
        $this->db->join('tipo_agente', 'tipo_agente.pk_tipo_agente = entityUser.fk_tipo_agente', 'left');
	}

    /**
     * @param $entityId
     * @param $userId
     * @return bool
     *
     * Utilizamos esta funciona para desmarcar la bandera de solicitud de actualizacion de serie.
     */
    function unFlagUpdSerie($entityId, $userId) {
        $q = new stdClass();
        $q->upd_series = 0;
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('id_usuario', $userId);

        return $this->db->update('r_usu_emp', $q);
    }

    /**
     * @param $entityId
     * @param $userId
     * @return bool
     *
     * Utilizamos esta funciona para desmarcar la bandera de solicitud de envio de base de datos.
     */
    function unFlagDatabaseSend($entityId, $userId) {
        $q = new stdClass();
        $q->send_ddbb = 0;
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('id_usuario', $userId);

        return $this->db->update('r_usu_emp', $q);
    }
	

	function getEntityUserById($userId) {
		$this->getEntityUserQuery();

		$this->db->where('usuarios.id_usuario', $userId);
		$query = $this->db->get();
		
		$entityUser = $query->row(0, 'entityUser');
		return $entityUser;
	}

	function getEntityUserByCod($userCod, $entityId) {
		$this->getEntityUserQuery();

		$this->db->where('entityUser.cod_usuario_entidad', $userCod);
		$this->db->where('entityUser.fk_entidad', $entityId);
		$query = $this->db->get();
		
		$entityUser = $query->row(0, 'entityUser');
		return $entityUser;
	}

    function getEntityUsers($entityId) {
        $this->getEntityUserQuery();

        $this->db->where('entityUser.fk_entidad', $entityId);
        $query = $this->db->get();

        $entityUsers = $query->result('entityUser');

        return $entityUsers?$entityUsers:array();
    }

	function getEntityUserByMailOrCod($user, $entityId) {
		$this->getEntityUserQuery();

		$this->db->where("(mail = '".$user."' OR entityUser.cod_usuario_entidad = '".$user."') AND entityUser.fk_entidad =".$entityId);
		$query = $this->db->get();
		
		$entityUser = $query->row(0, 'entityUser');
		return $entityUser;
	}

	function getEntityUserByDevice($pk_terminal_tpv) {
		$this->getEntityUserQuery();

		$this->db->where('entityUser.fk_terminal_tpv', $pk_terminal_tpv);
		$query = $this->db->get();
		
		$entityUser = $query->row(0, 'entityUser');		
		return $entityUser;
	}

    function updateCaptacionNum($userPk, $num) {
        $q = new stdClass();
        $q->num_captacion = $num;
        $this->db->where('pk_usuario_entidad', $userPk);
        //Actualizamos solo si el numero nuevo es superior al actual para tener siempre el mayor
        $this->db->where('num_captacion < ', $num, false);

        return $this->db->update('r_usu_emp', $q);
    }

    function updateInventarioNum($userPk, $num) {
        $q = new stdClass();
        $q->num_inventario = $num;
        $this->db->where('pk_usuario_entidad', $userPk);
        //Actualizamos solo si el numero nuevo es superior al actual para tener siempre el mayor
        $this->db->where('num_inventario < ', $num, false);

        return $this->db->update('r_usu_emp', $q);
    }

    function updateMovAlmacenNum($userPk, $num) {
        $q = new stdClass();
        $q->num_mov_almacen = $num;
        $this->db->where('pk_usuario_entidad', $userPk);
        //Actualizamos solo si el numero nuevo es superior al actual para tener siempre el mayor
        $this->db->where('num_mov_almacen < ', $num, false);

        return $this->db->update('r_usu_emp', $q);
    }

    function updateIncidenciaNum($userPk, $num) {
        $q = new stdClass();
        $q->num_incidencia = $num;
        $this->db->where('pk_usuario_entidad', $userPk);
        //Actualizamos solo si el numero nuevo es superior al actual para tener siempre el mayor
        $this->db->where('num_incidencia < ', $num, false);

        return $this->db->update('r_usu_emp', $q);
    }


			
	function sendMail($to, $cc, $bcc, $subject, $message) {



		$config['protocol'] = MAIL_PROTOCOL;
		$config['smtp_host'] = MAIL_SMTP_HOST;
		$config['smtp_user'] = MAIL_SMTP_USER;
		$config['smtp_pass'] = MAIL_SMTP_PASS;
		$config['smtp_port'] = MAIL_SMTP_PORT;
		$config['mailtype'] = MAIL_TYPE;
		$config['charset'] = 'utf-8';
		$config['validate'] = FALSE;
		$config['bcc_batch_mode'] = FALSE; // TRUE or FALSE (boolean)    Enable BCC Batch Mode.
        $config['bcc_batch_size'] = 200; // Number of emails in each BCC batch.
        $config['crlf'] = '\r\n';      //should be "\r\n"
		$config['newline'] = '\r\n';   //should be "\r\n"
		$config['wrapchars'] = 50000;


		$this->load->library('email', $config);
		$this->email->clear();
		$this->email->set_newline("\r\n");
		
		$this->email->from(MAIL_FROM_MAIL, MAIL_FROM_NAME);
		$this->email->to($to);
		if (isset($cc)) {
			$this->email->cc($cc);
		}	
		if (isset($bcc)) {
			$this->email->bcc($bcc);
		}

		$this->email->subject($subject);
		$this->email->message(htmlwrap($message));

		$this->email->send();

		//echo $this->email->print_debugger();

	}

    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function search($entityId, $field, $query, $return=null, $type='text', $fk_delegacion=null, $fk_canal_venta=null) {
        if (!$return) {
            $return = 'pk_usuario_entidad';
        }

        if ($field == 'usuario') {
            $field = "CONCAT(apellidos, CONCAT(', ', nombre))";
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('r_usu_emp');
        $this->db->join('usuarios','r_usu_emp.id_usuario = usuarios.id_usuario','left');
        $this->db->where('r_usu_emp.fk_entidad', $entityId);

        if ($type == 'text')
            $this->db->like($field, $query);
        else
            $this->db->where($field, $query);

        if ($fk_delegacion)
            $this->db->where("fk_delegacion", $fk_delegacion);

        if ($fk_canal_venta)
            $this->db->where("fk_canal_venta", $fk_canal_venta);



        $this->db->limit(10);
        $query = $this->db->get();

        $result = $query->result();
        return $result?$result:array();
    }

    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function searchByUserType($entityId, $field, $query, $return=null, $type='text', $userType, $fk_delegacion=null, $fk_canal_venta=null) {
        if (!$return) {
            $return = 'pk_usuario_entidad';
        }

        if ($field == 'usuario') {
            $field = "CONCAT(apellidos, CONCAT(', ', nombre))";
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('r_usu_emp');
        $this->db->join('usuarios','r_usu_emp.id_usuario = usuarios.id_usuario','left');
        $this->db->join('tipo_agente','tipo_agente.pk_tipo_agente = r_usu_emp.fk_tipo_agente');
        $this->db->where('r_usu_emp.fk_entidad', $entityId);
        $this->db->where('tipo_agente.cod_tipo_agente', $userType);

        if ($type == 'text')
            $this->db->like($field, $query);
        else
            $this->db->where($field, $query);

        if ($fk_delegacion)
            $this->db->where("fk_delegacion", $fk_delegacion);

        if ($fk_canal_venta)
            $this->db->where("fk_canal_venta", $fk_canal_venta);

        $this->db->limit(10);
        $query = $this->db->get();

        $result = $query->result();
        return $result?$result:array();
    }

    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function searchNotInEntity($entityId, $field, $query, $return=null, $type='text') {
        if (!$return) {
            $return = 'pk_usuario_entidad';
        }

        if ($field == 'usuario') {
            $field = "CONCAT(apellidos, CONCAT(', ', nombre))";
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('usuarios');
        $this->db->join('r_usu_emp','r_usu_emp.id_usuario = usuarios.id_usuario','left');
        $this->db->where('(r_usu_emp.fk_entidad !='.$entityId.' OR r_usu_emp.fk_entidad IS NULL)');

        if ($type == 'text')
            $this->db->like($field, $query);
        else
            $this->db->where($field, $query);

        $this->db->limit(10);
        $query = $this->db->get();

        $result = $query->result();
        return $result?$result:array();
    }

    /**
     * @param $entityId
     * @param $field
     * @param $provPk
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function searchNotInProv($entityId, $provPk, $field, $query, $return=null, $type='text') {
        if (!$return) {
            $return = 'pk_usuario_entidad';
        }

        if ($field == 'usuario') {
            $field = "CONCAT(apellidos, CONCAT(', ', nombre))";
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('r_usu_emp');
        $this->db->join('usuarios','r_usu_emp.id_usuario = usuarios.id_usuario','left');
        $this->db->join('r_usu_prov', 'r_usu_prov.fk_usuario_entidad = r_usu_emp.pk_usuario_entidad', 'left');
        $this->db->where("r_usu_emp.fk_entidad = $entityId AND (r_usu_prov.fk_proveedor IS NULL OR r_usu_prov.fk_proveedor != '$provPk')");

        if ($type == 'text')
            $this->db->like($field, $query);
        else
            $this->db->where($field, $query);

        $this->db->limit(10);
        $query = $this->db->get();

        $result = $query->result();

        return $result?$result:array();
    }



	

}


?>