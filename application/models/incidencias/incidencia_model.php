<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_INCIDENCIA);
require_once(APPPATH.GENERIC_MODEL);


/**
 *
 * @Table "incidencias"
 * @Entity "Incidencia"
 * @Country true
 * @Autoincrement true;
 *
 */
class incidencia_model extends generic_Model {

    function __construct() {
        parent::__construct();
        $this->load->model('ubicaciones/ubicacion_model');
        $this->load->model('medios/medio_model');
        $this->load->model('medios/subtipo_medio_model');

    }

    function getAllIncidencias($get_vars, $countryId, $offset, $limit, $sort, $pagination) {

        $extended = null;
        if (isset($get_vars["extended"])) {
            $extended = $get_vars["extended"];
            unset($get_vars["extended"]);
        }

        if (isset($get_vars["ubicacion"])) {
            $this->db->join("medios", "fk_medio = pk_medio", "left");
            $this->db->join("ubicaciones", "fk_ubicacion = pk_ubicacion", "left");
            $this->db->like("ubicaciones.ubicacion", $get_vars["ubicacion"]);
            unset($get_vars["ubicacion"]);
        }

        if ($extended) {

            $result = $this->getAll($get_vars, $countryId, $offset, $limit, $sort, $pagination);

            $incidenciasArr = array();
            foreach ($result["result"] as $incidencia) {
                if ($incidencia->fk_medio) {

                    $medio = $this->medio_model->getBy("pk_medio", $incidencia->fk_medio, $countryId);
                    if ($medio) {
                        $subtipo = $this->subtipo_medio_model->getBy("pk_subtipo", $medio->fk_subtipo, $countryId);
                        $medio->subtipo = $subtipo;

                        $ubicacion = $this->ubicacion_model->getBy("pk_ubicacion", $medio->fk_ubicacion, $countryId);
                        $ubicacion->medio = $medio;
                    }

                    $incidencia->ubicacion = $ubicacion;

                }
                $incidenciasArr[] = $incidencia;
            }

            $result["result"] = $incidenciasArr;

            return $result;

        } else {
            return $this->getAll($get_vars, $countryId, $offset, $limit, $sort, $pagination);
        }

    }
	
}

?>