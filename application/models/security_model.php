<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');


class security_model extends CI_Model {

	function hasPermissionForAction($entityId, $userId, $controller, $action) {
		$this->db->from('seg_acciones');
		$this->db->join('r_usu_emp_accion AS usuAcc', 'seg_acciones.id_accion = usuAcc.id_accion');
		$this->db->where('controlador', $controller);
		$this->db->where('accion', $action);
		$this->db->where('id_usuario', $userId);
		$this->db->where('fk_entidad', $entityId);	

		$query = $this->db->get();		

		$user = $query->row();

		return $user;
	}

}

?>