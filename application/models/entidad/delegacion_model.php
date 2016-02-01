<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_DELEGACION);
require_once(APPPATH.ENTITY_VERSION_CONTROL);


class delegacion_model extends CI_Model {

	function getDelegacionByPk($delegacionPk, $entityId) {
		$this->db->where('pk_delegacion', $delegacionPk);
		$this->db->where('fk_entidad', $entityId);
        $this->db->where('estado > ', 0);
		$query = $this->db->get('delegaciones'); 		

		$delegacion = $query->row(0, 'delegacion');
		return $delegacion;
	}

	function getDelegacionByCod($delegacionCod, $entityId) {
		$this->db->where('cod_delegacion', $delegacionCod);
		$this->db->where('fk_entidad', $entityId);
		$query = $this->db->get('delegaciones'); 		

		$delegacion = $query->row(0, 'delegacion');
		return $delegacion;
	}

    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function search($entityId, $field, $query, $return=null, $type='text') {
        if (!$return) {
            $return = 'pk_delegacion';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('delegaciones');
        $this->db->where('fk_entidad', $entityId);

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