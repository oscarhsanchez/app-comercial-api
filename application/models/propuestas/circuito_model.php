<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENTE);
require_once(APPPATH.GENERIC_MODEL);
require_once(APPPATH.ENTITY_PROPUESTA);
require_once(APPPATH.ENTITY_PROPUESTA_DETALLE);
require_once(APPPATH.ENTITY_PROPUESTA_DETALLE_OUTDOOR);
require_once(APPPATH.ENTITY_UBICACION);

/**
 *
 * @Table "propuestas"
 * @Entity "Propuesta"
 * @Country true
 * @Autoincrement true;
 *
 */
class circuito_model extends generic_Model {

    function getAll($get_vars, $countryId, $limit) {

        $this->db->where("estatus", "INSTALADO");

        $this->db->order_by("pk_ubicacion", "random");
        $this->db->limit(15);
        $query = $this->db->get("ubicaciones");
        $result = $query->result("ubicacion");

        return array("result" => $result?$result:array());

    }

}

?>