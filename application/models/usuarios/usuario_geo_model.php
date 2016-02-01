<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_USUARIO_GEO);



class usuario_geo_model extends CI_Model {

    /**
     * @param $geoToken
     * @return mixed
     */
    function getUsuarioGeoByToken($geoToken) {
        $this->db->where('token', $geoToken);
        $query = $this->db->get('usuarios_geo');

        $usuario_geo = $query->row(0, 'usuario_geo');
        return $usuario_geo;
    }

    /**
     * @param $usuario_geo
     * @return bool
     * @throws APIexception
     */
    function saveUsuarioGeo($usuario_geo) {
        $this->load->model("log_model");

        if (!isset($usuario_geo->token)) {
            $usuario_geo->token = getToken();
        }

        $result = $usuario_geo->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on usuario_geo_model->saveVisita. Unable to update usuario_geo.", ERROR_SAVING_DATA, serialize($usuario_geo));
        }
    }



}

?>