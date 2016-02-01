<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_INCIDENCIA);
require_once(APPPATH.ENTITY_REGISTRO_INCIDENCIA);


class incidencia_model extends CI_Model {


    /**
     * Devuelve una incidencia a partir de su token
     *
     * @param $token
     * @param $entityId
     * @return mixed
     */
    function getIncidenciaByToken($token, $entityId) {


        $this->db->where('incidencia.token', $token);
        $this->db->where('incidencia.fk_entidad', $entityId);
        $query = $this->db->get('incidencia');

        $incidencia = $query->row(0, 'incidencia');

        return $incidencia;

    }

    /**
     * Devuelve un registro a partir de su token
     *
     * @param $token
     * @return mixed
     */
    function getRegistroByToken($token) {


        $this->db->where('registro_incidencia.token', $token);
        $query = $this->db->get('registro_incidencia');

        $registro = $query->row(0, 'registro_incidencia');

        return $registro;

    }

    /**
     * Funcion que guardar la incidencia en la bbdd
     *
     * @param $incidencia
     * @return bool
     * @throws APIexception
     */
    function saveIncidencia($incidencia) {
        $this->load->model("log_model");

        if (!isset($incidencia->token)) {
            $incidencia->token = getToken();
        }

        if (!$incidencia->fecha_limite) {
            $date = date('Y-m-d', time());
            $incidencia->fecha_limite = date('Y-m-d', strtotime($date. ' + 7 days'));;
        }
        $result = $incidencia->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on incidencia_model->saveIncidencia. Unable to update incidencia.", ERROR_SAVING_DATA, serialize($incidencia));
        }
    }

    /**
     * Funcion que guardar el registro en la bbdd
     *
     * @param $registro
     * @return bool
     * @throws APIexception
     */
    function saveRegistro($registro) {
        $this->load->model("log_model");

        if (!isset($registro->token)) {
            $registro->token = getToken();
        }

        $result = $registro->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on incidencia_model->saveRegistro. Unable to update registro.", ERROR_SAVING_DATA, serialize($registro));
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
            $return = 'id';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('incidencia');
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