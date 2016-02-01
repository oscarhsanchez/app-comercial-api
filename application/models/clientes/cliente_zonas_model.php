<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_CLIENTE_CONTACTO);


class cliente_zonas_model extends CI_Model {


    /**
     * Funcion que establece una subzona para un cliente
     *
     * @param $SubZonaPk
     * @param $ClientePk
     * @return bool
     * @throws APIexception
     */
    function set($SubZonaPk, $ClientePk) {
        $cliente = new stdClass();
        $cliente->fk_cliente_subzona = $SubZonaPk;

        $this->db->where('pk_cliente', $ClientePk);
        $this->db->update('clientes', $cliente);

        return true;
    }

}

?>