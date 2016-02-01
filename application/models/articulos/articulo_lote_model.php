<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_ARTICULO_LOTE);


class articulo_lote_model extends CI_Model {


    function getLoteByPk($entityId, $lotePk) {
        $this->db->where('pk_lote', $lotePk);
        $this->db->where('fk_entidad', $entityId);
        $query = $this->db->get('articulos_lotes');

        $result = $query->row(0, 'articuloLote');
        return $result;
    }

	function getLoteByToken($entityId, $loteToken) {
		$this->db->where('token', $loteToken);
        $this->db->where('fk_entidad', $entityId);
		$query = $this->db->get('articulos_lotes');

        $result = $query->row(0, 'articuloLote');
		return $result;
	}

    /**
     * Devuelve los lote de un articulo para un almacen
     *
     * @param $entityId
     * @param $state
     * @param $articuloPk
     * @param $almacenPk
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     *
     * @return array
     */
    function getByArticuloAndAlmacen($entityId, $articuloPk, $almacenPk, $state, $offset, $limit) {
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('estado >=', $state);
        $this->db->where('fk_articulo', $articuloPk);
        $this->db->where('fk_almacen', $almacenPk);

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $this->db->limit($limit, $offset);

        $this->db->order_by("fecha_fabricacion", "desc");

        $query = $this->db->get('articulos_lotes');

        $result = $query->result('articuloLote');

        return array("lotes" => $result?$result:array());

    }

    /**
     * Devuelve el lote de un articulo para un almacen
     *
     * @param $entityId
     * @param $articuloPk
     * @param $almacenPk
     * @param $lote
     *
     * @return array
     */
    function getByArticuloAndAlmacenAndLote($entityId, $articuloPk, $almacenPk, $lote) {
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('estado', 1);
        $this->db->where('fk_articulo', $articuloPk);
        $this->db->where('fk_almacen', $almacenPk);
        $this->db->where('lote', $lote);

        $query = $this->db->get('articulos_lotes');

        $result = $query->row(0, 'articuloLote');

        return $result;

    }



    function saveLote($lote, $omittedFields=null) {
		$this->load->model("log_model");

		if (!isset($lote->token)) {
			$lote->token = getToken();
		}

		if (!isset($lote->pk_articulo)) {
			$lote->setPk();
		}

		$result = $lote->_save(false, false, $omittedFields);

		if ($result) {
			return true;
		} else {
			throw new APIexception("Error on articulo_model_model->saveLote. Unable to update Lote.", ERROR_SAVING_DATA, serialize($lote));
		}
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
            $return = 'pk_lote';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('articulos_lotes');
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