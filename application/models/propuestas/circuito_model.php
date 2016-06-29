<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENTE);
require_once(APPPATH.GENERIC_MODEL);
require_once(APPPATH.ENTITY_PROPUESTA);
require_once(APPPATH.ENTITY_PROPUESTA_DETALLE);
require_once(APPPATH.ENTITY_PROPUESTA_DETALLE_OUTDOOR);
require_once(APPPATH.ENTITY_UBICACION);
require_once(APPPATH.ENTITY_MEDIO);


/**
 *
 * @Table "propuestas"
 * @Entity "Propuesta"
 * @Country true
 * @Autoincrement true;
 *
 */
class circuito_model extends generic_Model {

    function __construct() {
        parent::__construct();
        $this->load->model('medios/medio_model');
        $this->load->model('agrupaciones_medios/agrupacion_model');
    }

    function getMediosDisponibles($fecha_inicio, $fecha_fin, $score_ini, $score_fin) {
        $query = "
            SELECT medios.* FROM medios
            LEFT JOIN (
                SELECT pk_medio, COUNT(*) AS slots FROM medios
                JOIN ubicaciones ON ubicaciones.pk_ubicacion = medios.fk_ubicacion
                JOIN reserva_medios ON reserva_medios.fk_medio = medios.pk_medio
                WHERE estatus = 'INSTALADO' AND estatus_inventario = 'DISPONIBLE' AND ('$fecha_inicio' BETWEEN fecha_inicio AND fecha_fin OR '$fecha_fin' BETWEEN fecha_inicio AND fecha_fin OR fecha_inicio BETWEEN '$fecha_inicio' AND '$fecha_fin' )
                GROUP BY pk_medio
            ) slots ON slots.pk_medio = medios.pk_medio
            WHERE medios.slots > IFNULL(slots.slots, 0) AND medios.score > $score_ini AND medios.score <= $score_fin
            ORDER BY RAND()
        ";

        $query = $this->db->query($query);

        return $query->result();
    }

    function getAll($get_vars, $countryId, $limit) {

        $resultMedios = array();

        $maxDiscountAllowed = 10;
        $operationMargin = array();
        $operationMargin["low"] = 10;
        $operationMargin["medium"] = 20;
        $operationMargin["high"] = 30;

        $groupsPrecentage = 0.30;

        $highPrecentage = 0.25;
        $mediumPrecentage = 0.25;
        $lowPrecentage = 0.20;

        $highRange = array(7.5, 10);
        $mediumRange = array(4.5, 7.5);
        $lowRange = array(0, 4.5);

        $clientBudget = $get_vars["budged"];
        $currentBudget = 0;

        $fecha_inicio = $get_vars["fecha_inicio"];
        $fecha_fin = $get_vars["fecha_fin"];

        if (!$clientBudget || $clientBudget == 0)
            throw new Exception('Invalid Budget Exception');

        $agrupaciones = array();
        //Obtenemos las agrupaciones de medios disponibles
        $agrupacionesDisponibles = $this->agrupacion_model->getAgrupacionesDisponibles($fecha_inicio, $fecha_fin);

        $limitAgrupaciones = $clientBudget*$groupsPrecentage;
        foreach($agrupacionesDisponibles as $agrupacion) {
            $currentBudget += $agrupacion->coste;
            $agrupaciones[] = $agrupacion;

            if ($currentBudget > $limitAgrupaciones)
                break;

        }

        if ($currentBudget == 0)
            $groupsPrecentage = 1;

        //Oobtenemos los medios disponibles con puntuacion alta, comprobando restricciones hasta cumplir el % de presupuesto establecido
        $medios = $this->getMediosDisponibles($fecha_inicio, $fecha_fin, $highRange[0], $highRange[1]);


        $limitHigh = $limitAgrupaciones + $clientBudget*$highPrecentage;
        $totalHigh = 0;
        foreach ($medios AS $medio) {
            $currentBudget += $medio->coste;
            $resultMedios[] = $medio;
            $totalHigh++;

            if ($currentBudget > $limitHigh)
                break;

        }

        //Oobtenemos los medios disponibles con puntuacion Media, comprobando restricciones hasta cumplir el % de presupuesto establecido
        $medios = $this->getMediosDisponibles($fecha_inicio, $fecha_fin, $mediumRange[0], $mediumRange[1]);
        $limitMedium = $limitHigh + ($clientBudget*$mediumPrecentage);
        $totalMedium = 0;
        foreach ($medios AS $medio) {
            $currentBudget += $medio->coste;
            $resultMedios[] = $medio;
            $totalMedium++;
            if ($currentBudget > $limitMedium)
                break;

        }

        //Oobtenemos los medios disponibles con puntuacion Baja, comprobando restricciones hasta cumplir el % de presupuesto establecido
        $medios = $this->getMediosDisponibles($fecha_inicio, $fecha_fin, $lowRange[0], $lowRange[1]);
        $limitLow = $limitMedium + $clientBudget*$lowPrecentage;
        $totalLow = 0;
        foreach ($medios AS $medio) {
            $currentBudget += $medio->coste;
            $resultMedios[] = $medio;
            $totalLow++;
            if ($currentBudget > $limitLow)
                break;

        }

        //echo "TOTAL: " . $currentBudget . " HIGH: " . $totalHigh . " MEDIUM: " . $totalMedium . " LOW: " . $totalLow;

        $quality_range = array();
        $quality_range["high"] = $highRange;
        $quality_range["medium"] = $mediumRange;
        $quality_range["low"] = $lowRange;

        $parameters = array();
        $parameters["max_discount"] = $maxDiscountAllowed;
        $parameters["quality_range"] = $quality_range;
        $parameters["margin"] = $operationMargin;


        return array("parameters" => $parameters, "agrupaciones" => $agrupaciones, "medios" => $resultMedios?$resultMedios:array());

    }

}

?>