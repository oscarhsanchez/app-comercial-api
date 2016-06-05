<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_MEDIO);
require_once(APPPATH.GENERIC_MODEL);


/**
 *
 * @Table "medios"
 * @Entity "Medio"
 * @Country true
 * @Autoincrement false;
 *
 */
class medio_model extends generic_Model {

    function __construct() {
        parent::__construct();
        $this->load->model('medios/subtipo_medio_model');

    }

    function getAll($get_vars, $countryId=0, $offset, $limit, $sort, $pagination) {
        $extended = null;
        if (isset($get_vars["extended"])) {
            $extended = $get_vars["extended"];
            unset($get_vars["extended"]);
        }

        $result = parent::getAll($get_vars, $countryId, $offset, $limit, $sort, $pagination);

        if ($extended) {
            $mediosArr = array();

            foreach ($result["result"] AS $medio) {
                $subtipo = $this->subtipo_medio_model->getBy("pk_subtipo", $medio->fk_subtipo, $countryId);
                $medio->subtipo = $subtipo;

                $mediosArr[] = $medio;
            }

            $result["result"] = $mediosArr;

            return $result;

        } else
            return $result;

    }

    function getDisponibilidad($pk_medio, $date) {

        $query = "
            SELECT medios.* FROM medios
            LEFT JOIN (
                SELECT pk_medio, COUNT(*) AS slots FROM medios
                JOIN reserva_medios ON reserva_medios.fk_medio = medios.pk_medio
                WHERE estatus_inventario = 'DISPONIBLE' AND '$date' BETWEEN fecha_inicio AND fecha_fin AND pk_medio = '$pk_medio'
                GROUP BY pk_medio
            ) slots ON slots.pk_medio = medios.pk_medio
            WHERE medios.slots > IFNULL(slots.slots, 0) AND medios.pk_medio = '$pk_medio'
        ";

        $query = $this->db->query($query);

        $result = $query->result();

        if (count($result) > 0)
            return true;
        else
            return false;



    }


}

?>