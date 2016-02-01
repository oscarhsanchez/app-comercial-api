<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require(APPPATH.ENTITY_ENTITY);

class entity_model extends CI_Model {

	function getEntityBySecretKey($secretKey) {

		$this->db->where('secret_key', $secretKey);
		$query = $this->db->get('entidades'); 		

		$entity = $query->row(0, 'entity');
		return $entity;

		//$entity = $query->result('entity');

	}

	function getEntityById($entityId) {
		$this->db->where('pk_entidad', $entityId);
		$query = $this->db->get('entidades'); 		

		$entity = $query->row(0, 'entity');
		return $entity;

	}

	function getEntityByNif($nif) {
		$this->db->where('nif', $nif);
		$query = $this->db->get('entidades'); 		

		$entity = $query->row(0, 'entity');
		return $entity;

	}



}

?>