<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_AGRUPACION_MEDIO);
require_once(APPPATH.ENTITY_AGRUPACION_MEDIO_DETALLE);
require_once(APPPATH.GENERIC_MODEL);


/**
 *
 * @Table "agrupacion_medios"
 * @Entity "AgrupacionMedio"
 * @Country true
 * @Autoincrement true;
 *
 */
class agrupacion_model extends generic_Model {

    function getAgrupacionesDisponibles($fecha_ini, $fecha_fin) {

        $query = "
            SELECT agrupacion_medios.* FROM agrupacion_medios
            LEFT JOIN (
                SELECT fk_agruapcion, SUM(slots) AS slots FROM agrupacion_medios_detalle
                JOIN medios ON agrupacion_medios_detalle.fk_medio = medios.pk_medio
                GROUP BY fk_agruapcion
            ) medios ON medios.fk_agruapcion = agrupacion_medios.pk_agrupacion
            LEFT JOIN (
                SELECT fk_agruapcion, COUNT(*) AS slots FROM agrupacion_medios_detalle
                JOIN reserva_medios ON reserva_medios.fk_medio = reserva_medios.fk_medio
                WHERE '$fecha_ini' BETWEEN fecha_inicio AND fecha_fin OR '$fecha_fin' BETWEEN fecha_inicio AND fecha_fin OR  fecha_inicio BETWEEN '$fecha_ini' AND '$fecha_fin'
                GROUP BY fk_agruapcion
            ) reservas ON reservas.fk_agruapcion = agrupacion_medios.pk_agrupacion
            WHERE medios.slots > IFNULL(reservas.slots, 0)
        ";

        $query = $this->db->query($query);

        return $query->result();


    }

    function isDisponible($agrupacionId, $fecha_ini, $fecha_fin) {

        $query = "
            SELECT agrupacion_medios.* FROM agrupacion_medios
            LEFT JOIN (
                SELECT fk_agruapcion, SUM(slots) AS slots FROM agrupacion_medios_detalle
                JOIN medios ON agrupacion_medios_detalle.fk_medio = medios.pk_medio
                WHERE fk_agrupacion = $agrupacionId
                GROUP BY fk_agruapcion
            ) medios ON medios.fk_agruapcion = agrupacion_medios.pk_agrupacion
            LEFT JOIN (
                SELECT fk_agruapcion, COUNT(*) AS slots FROM agrupacion_medios_detalle
                JOIN reserva_medios ON reserva_medios.fk_medio = reserva_medios.fk_medio
                WHERE fk_agrupacion = $agrupacionId AND ('$fecha_ini' BETWEEN fecha_inicio AND fecha_fin OR '$fecha_fin' BETWEEN fecha_inicio AND fecha_fin OR  fecha_inicio BETWEEN '$fecha_ini' AND '$fecha_fin')
                GROUP BY fk_agruapcion
            ) reservas ON reservas.fk_agruapcion = agrupacion_medios.pk_agrupacion
            WHERE medios.slots > IFNULL(reservas.slots, 0)
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