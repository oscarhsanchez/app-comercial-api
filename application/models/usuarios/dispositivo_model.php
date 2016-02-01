<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_PARAMETER_TPV);
require_once(APPPATH.ENTITY_VERSION_CONTROL);


class dispositivo_model extends CI_Model {

	function getDeviceByUniqueId($uniqueId, $entityId) {
		$this->db->where('id_dispositivo', $uniqueId);
		$this->db->where('fk_entidad', $entityId);
		$query = $this->db->get('terminales_tpv'); 		

		$device = $query->row(0, 'dispositivo');
		return $device;

		//$entity = $query->result('entity');

	}

	function getParameterTpv($entityId) {
		$this->db->where('fk_entidad', $entityId);
		$this->db->where('estado','1');
		$query = $this->db->get('parametro_tpv'); 		


		$parameters = $query->result('parameterTpv');
		
		return $parameters;		
	}

	function getLastVersionOfApp($entityId) {
		$this->db->where('estado','1');
        $this->db->where('fk_entidad',$entityId);
		$this->db->order_by("version", "desc"); 
		$query = $this->db->get('control_versiones');

		$vc = $query->row(0, 'versionControl');
		return $vc;
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
            $return = 'pk_terminal_tpv';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('terminales_tpv');
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

?>