<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_CONTACTO);


class cliente_linea_mercado_model extends CI_Model {


    /**
     * Funcion que establece una linea de mercado para un cliente
     *
     * @param $LineaMercadoPk
     * @param $ClientePk
     * @return bool
     * @throws APIexception
     */
    function set($LineaMercadoPk, $ClientePk) {
        $cliente = new stdClass();
        $cliente->fk_linea_mercado = $LineaMercadoPk;

        $this->db->where('pk_cliente', $ClientePk);
        $this->db->update('clientes', $cliente);

       return true;
    }
    
    /**
     *
     * @param $LineaMercadoPk
     * @return the LineaMercado
     */
    function getLineaMercadoByPk($LineaMercadoPk) {
    	$this->db->where('pk_LineaMercado', $LineaMercadoPk);
    	$query = $this->db->get('lineas_mercado');
    
    	$LineaMercado = $query->row(0, 'linea_mercado');
    	return $LineaMercado;
    }
    
    /**
     *
     * @param $token
     * @return the LineaMercado
     */
    function getLineaMercadoByToken($token) {
    	$this->db->where('token', $token);
    	$query = $this->db->get('lineas_mercado');
    
    	$LineaMercado = $query->row(0, 'linea_mercado');
    	return $LineaMercado;
    }
    
    function saveLineaMercado($lineaMercado, $omittedFields=null) {
    	$this->load->model("log_model");
    
    	if (!isset($lineaMercado->token)) {
    		$lineaMercado->token = getToken();
    	}
    
    	$result = $lineaMercado->_save(false, false, $omittedFields);
    
    	if ($result) {
    		return true;
    	} else {
    		throw new APIexception("Error on cliente_linea_mercado_model->saveLineaMercado. Unable to update lineaMercado", ERROR_SAVING_DATA, serialize($lineaMercado));
    	}
    }

}

?>