<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENTE);
require_once(APPPATH.GENERIC_MODEL);
require_once(APPPATH.ENTITY_PROPUESTA);
require_once(APPPATH.ENTITY_PROPUESTA_DETALLE);
require_once(APPPATH.ENTITY_PROPUESTA_DETALLE_OUTDOOR);

/**
 *
 * @Table "propuestas"
 * @Entity "Propuesta"
 * @Country true
 * @Autoincrement true;
 *
 */
class propuesta_model extends generic_Model {

	function getAll($get_vars, $countryId=0, $offset, $limit, $sort, $pagination) {

        $extended = $get_vars["extended"];
        if ($extended) {
            unset($get_vars["extended"]);
        }


        if ($extended) {

            $this->esdb->select("*", "Propuesta");
            $this->esdb->select("*", "PropuestaDetalle");
            $this->esdb->select("*", "PropuestaDetalleOutdoor");
            $this->esdb->from("propuestas");
            $this->esdb->join("propuestas_detalle", "pk_propuesta = fk_propuesta");
            $this->esdb->join("propuestas_detalle_outdoor", "pk_propuesta_detalle = fk_propuesta_detalle", 'left');

            if (isset($get_vars["updated_at"])) {
                $updated_at = $get_vars["updated_at"];
                unset($get_vars["updated_at"]);

                $updated_at = str_replace("<", "", str_replace(">", "", str_replace("]", "", str_replace("[", "", $updated_at))));

                $this->db->where("(propuestas.updated_at >= '$updated_at' OR propuestas_detalle.updated_at >= '$updated_at' OR propuestas_detalle_outdoor.updated_at >= '$updated_at')");
            }

            return parent::extended($get_vars, $countryId, $offset, $limit, $sort, $pagination);


        } else
            return parent::getAll($get_vars, $countryId, $offset, $limit, $sort, $pagination);

    }

}

?>