<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_ORDEN_TRABAJO);
require_once(APPPATH.GENERIC_MODEL);


/**
 *
 * @Table "ordenes_trabajo"
 * @Entity "OrdenTrabajo"
 * @Country true
 * @Autoincrement true;
 *
 */
class orden_trabajo_model extends generic_Model {

    function __construct() {
        parent::__construct();
        $this->load->model('ubicaciones/ubicacion_model');
        $this->load->model('medios/medio_model');
        $this->load->model('medios/subtipo_medio_model');

    }

    function getAllOrdenes($get_vars, $countryId, $offset, $limit, $sort, $pagination) {

        $extended = null;
        if (isset($get_vars["extended"])) {
            $extended = $get_vars["extended"];
            unset($get_vars["extended"]);
        }

        if (isset($get_vars["search"]) || isset($get_vars["ubicacion"]) || (isset($get_vars["latitud"]) && isset($get_vars["longitud"]))) {
            $this->db->join("medios", "fk_medio = pk_medio", "left");
            $this->db->join("ubicaciones", "fk_ubicacion = pk_ubicacion", "left");
        }

        //Busqueda compleja: Direccion y por Codigo de ubicacion
        if (isset($get_vars["search"])) {
            $search = $get_vars["search"];
            $this->db->where("(ubicacion LIKE '%$search%' OR pk_ubicacion LIKE '%$search%')", NULL, FALSE);
            unset($get_vars["search"]);
        }

        // Buscar ordenes por cercania
        if (isset($get_vars["latitud"]) && isset($get_vars["longitud"])) {
            $latitud = $get_vars["latitud"];
            $longitud = $get_vars["longitud"];
            $this->db->where("(
                              6371 * ACOS (
                              COS ( RADIANS($latitud) )
                              * COS( RADIANS( ubicaciones.latitud ) )
                              * COS( RADIANS( ubicaciones.longitud ) - RADIANS($longitud) )
                              + SIN ( RADIANS($latitud) )
                              * SIN( RADIANS( ubicaciones.latitud ) )
                            ) ) < 0.5");

            unset($get_vars["latitud"]);
            unset($get_vars["longitud"]);
        }

        // Para el buscador por texto
        if (isset($get_vars["ubicacion"])) {
            $this->db->like("ubicaciones.ubicacion", $get_vars["ubicacion"]);
            unset($get_vars["ubicacion"]);
        }

        if ($extended) {

            $result = $this->getAll($get_vars, $countryId, $offset, $limit, $sort, $pagination);

            $ordenesArr = array();
            foreach ($result["result"] as $orden) {
                if ($orden->fk_medio) {

                    $medio = $this->medio_model->getBy("pk_medio", $orden->fk_medio, $countryId);
                    if ($medio) {
                        $subtipo = $this->subtipo_medio_model->getBy("pk_subtipo", $medio->fk_subtipo, $countryId);
                        $medio->subtipo = $subtipo;

                        $ubicacion = $this->ubicacion_model->getBy("pk_ubicacion", $medio->fk_ubicacion, $countryId);
                        $ubicacion->medio = $medio;

                    }

                    $orden->ubicacion = $ubicacion;

                }
                $ordenesArr[] = $orden;
            }

            $result["result"] = $ordenesArr;

            return $result;

        } else {
            return $this->getAll($get_vars, $countryId, $offset, $limit, $sort, $pagination);
        }

    }
	
}

?>