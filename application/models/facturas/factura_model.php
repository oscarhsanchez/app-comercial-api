<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENTE);
require_once(APPPATH.GENERIC_MODEL);
require_once(APPPATH.ENTITY_FACTURA);
require_once(APPPATH.ENTITY_FACTURA_DETALLE);

/**
 *
 * @Table "facturas"
 * @Entity "Factura"
 * @Country true
 * @Autoincrement false;
 *
 */
class factura_model extends generic_Model {

	function getAll($get_vars, $countryId=0, $offset, $limit, $sort, $pagination) {

        $extended = $get_vars["extended"];
        if ($extended) {
            unset($get_vars["extended"]);
        }


        if ($extended) {

            $this->esdb->select("*", "Factura");
            $this->esdb->select("*", "FacturaDetalle");
            $this->esdb->from("facturas");
            $this->esdb->join("facturas_detalle", "pk_factura = fk_factura");

            if (isset($get_vars["updated_at"])) {
                $updated_at = $get_vars["updated_at"];
                unset($get_vars["updated_at"]);

                $updated_at = str_replace("<", "", str_replace(">", "", str_replace("]", "", str_replace("[", "", $updated_at))));

                $this->db->where("(facturas.updated_at >= '$updated_at' OR facturas_detalle.updated_at >= '$updated_at')");
            }

            return parent::extended($get_vars, $countryId, $offset, $limit, $sort, $pagination);


        } else
            return parent::getAll($get_vars, $countryId, $offset, $limit, $sort, $pagination);

    }

}

?>