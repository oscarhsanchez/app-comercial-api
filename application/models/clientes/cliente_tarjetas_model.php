<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');



class cliente_tarjetas_model extends CI_Model {



    /**
     * Devuelve las tarjetas de un cliente
     *
     * @param $entityId
     * @param $clientePk
     * @param $state
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     * @return array
     */
    function getAll($entityId, $clientePk,  $state, $offset, $limit) {
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('fk_cliente', $clientePk);
        $this->db->where('estado >=', $state);

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $this->db->limit($limit, $offset);

        $this->db->order_by('created_at', 'DESC');

        $query = $this->db->get('cliente_tarjeta');

        $result = $query->result('tarjeta');

        return array("tarjetas" => $result?$result:array());

    }

    /**
     * @param $entityId
     * @param $token
     * @return the tarjeta
     */
    function getTarjetaByToken($entityId, $token) {
        $this->db->where('token', $token);
        $this->db->where('fk_entidad', $entityId);
        $query = $this->db->get('cliente_tarjeta');

        $result = $query->row(0, 'tarjeta');
        return $result;
    }

    /**
     * Guarda la tarjeta en la base de datos
     *
     * @param $tarjeta
     * @return bool
     * @throws APIexception
     */
    function save($tarjeta) {
        $this->load->model("log_model");

        if (!isset($tarjeta->token)) {
            $tarjeta->token = getToken();
        }

        $result = $tarjeta->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on clientes_tarjetas_model->save. Unable to update tarjeta.", ERROR_SAVING_DATA, serialize($tarjeta));
        }
    }


    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param $type
     * @return Array(Value, Description)
     */
    function search($entityId, $field, $query, $return=null, $type='text') {
        if (!$return) {
            $return = 'pk_cliente';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('cliente_tarjeta');
        $this->db->join('clientes', 'cliente_tarjeta.fk_entidad = clientes.fk_entidad AND cliente_tarjeta.fk_cliente = clientes.pk_cliente');
        $this->db->where('clientes.fk_entidad', $entityId);
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