<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class valoracion_model extends CI_Model {

    /**
     * Devuelve las valoracion de un  proveedor de una entidad
     *
     * @param $entityId
     * @param $proveedorPk
     * @param $state
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     * @return array
     */
    function getAll($entityId, $proveedorPk,  $state,$offset, $limit) {
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('fk_proveedor', $proveedorPk);
        $this->db->where('estado >=', $state);

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $this->db->limit($limit, $offset);

        $this->db->order_by('created_at', 'DESC');

        $query = $this->db->get('proveedor_valoracion');

        $result = $query->result('proveedorValoracion');

        return array("valoraciones" => $result?$result:array());

    }


    /**
     * 
     * @param $token
     * @return the valoracion
     */
    function getValoracionByToken($entityId, $token) {
    	$this->db->where('token', $token);
        $this->db->where('fk_entidad', $entityId);
    	$query = $this->db->get('proveedor_valoracion');
    
    	$result = $query->row(0, 'proveedorValoracion');
    	return $result;
    }
    
    /**
     * Guarda la valoracion del proveedor en la base de datos
     *
     * @param $valoracion
     * @return bool
     * @throws APIexception
     */
    function save($valoracion) {
    	$this->load->model("log_model");
    
    	if (!isset($valoracion->token)) {
    		$valoracion->token = getToken();
    	}
    
    	$result = $valoracion->_save(false, true);
    
    	if ($result) {
    		return true;
    	} else {
    		throw new APIexception("Error on valoracion_model->save. Unable to update valoracion.", ERROR_SAVING_DATA, serialize($valoracion));
    	}
    }

}

?>