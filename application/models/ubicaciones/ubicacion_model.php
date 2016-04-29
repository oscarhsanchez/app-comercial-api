<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_UBICACION);
require_once(APPPATH.GENERIC_MODEL);


/**
 *
 * @Table "ubicaciones"
 * @Entity "Ubicacion"
 * @Country true
 * @Autoincrement false;
 *
 */
class ubicacion_model extends generic_Model {

	function getAll($get_vars, $countryId=0, $offset, $limit, $sort, $pagination) {


        if (isset($get_vars["latitud"]) && isset($get_vars["longitud"])) {
            $latitud = $get_vars["latitud"];
            $longitud = $get_vars["longitud"];
            $this->db->where("(
                              6371 * ACOS (
                              COS ( RADIANS($latitud) )
                              * COS( RADIANS( latitud ) )
                              * COS( RADIANS( longitud ) - RADIANS($longitud) )
                              + SIN ( RADIANS($latitud) )
                              * SIN( RADIANS( latitud ) )
                            ) ) < 0.5");

            unset($get_vars["latitud"]);
            unset($get_vars["longitud"]);
        }

        return parent::getAll($get_vars, $countryId, $offset, $limit, $sort, $pagination);

    }

}

?>