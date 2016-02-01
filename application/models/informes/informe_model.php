<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_ALMACEN);


class informe_model extends CI_Model {


    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function familiaSearch($field, $query, $return=null, $type='text') {
        if (!$return) {
            $return = 'id';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('inf_familia');

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
     * @param $query
     * @param $return
     * @param $type
     * @param $familiaId
     * @return Array(Value, Description)
     */
    function subfamiliaSearch($field, $query, $return=null, $type='text', $familiaId=null) {
        if (!$return) {
            $return = 'id';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('inf_subfamilia');

        if ($familiaId) {
            $this->db->where('id_familia', $familiaId);
        }

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