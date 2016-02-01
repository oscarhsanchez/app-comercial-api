<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_VISITA);

class visita_model extends CI_Model {

    private function getSqlVisitasVendedores($str_fecha) {

        $sql = "SELECT VIS_TODAS.fk_entidad, VIS_TODAS.fk_cliente, VIS_TODAS.fecha_visita, VIS_TODAS.fecha_visita AS fecha_calculada, VIS_TODAS.fk_usuario_vendedor AS fk_vendedor, VIS_TODAS.fk_canal_venta, tipo_visita, visita.token, fk_vendedor_reasignado, IFNULL(IFNULL(hora_visita, r_usu_cli.hora),'00:00:00') AS hora_visita, hora_ejecucion, clientes.nombre_comercial AS 'nombre_cliente', clientes.cod_cliente, 'VISITA_VENTA' AS tipo_visita, 0 AS bool_resultado, 0 AS bool_visitada, 1 AS estado
             FROM (
                 SELECT VIS_FUTURO.fk_entidad, VIS_FUTURO.fk_cliente, VIS_FUTURO.fk_canal_venta, VIS_FUTURO.fk_usuario_vendedor as fk_usuario_vendedor, IFNULL(visita.fecha_visita, VIS_FUTURO.fecha_calculada) AS fecha_visita FROM
                        (
                        SELECT fk_entidad, fk_cliente, fk_canal_venta, fk_usuario_vendedor, CAST('".$str_fecha."'AS DATE) AS fecha_calculada
                        FROM r_usu_cli
                        WHERE r_usu_cli.estado > 0 AND
                        #FRECUENCIA SEMANAL - LUNES
                        ( tipo_frecuencia = 0 AND dia_1 = 1 AND WEEKDAY( '".$str_fecha."') = 0 AND TRUNCATE((DATEDIFF( '".$str_fecha."', fecha_inicio) / 7), 0) % repetir_cada = 0 )
                        #FRECUENCIA SEMANAL - MARTES
                        OR ( tipo_frecuencia = 0 AND dia_2 = 1 AND WEEKDAY( '".$str_fecha."') = 1 AND TRUNCATE((DATEDIFF( '".$str_fecha."', fecha_inicio) / 7), 0) % repetir_cada = 0 )
                        #FRECUENCIA SEMANAL - MIERCOLES
                        OR ( tipo_frecuencia = 0 AND dia_3 = 1 AND WEEKDAY( '".$str_fecha."') = 2 AND TRUNCATE((DATEDIFF( '".$str_fecha."', fecha_inicio) / 7), 0) % repetir_cada = 0 )
                        #FRECUENCIA SEMANAL - JUEVES
                        OR ( tipo_frecuencia = 0 AND dia_4 = 1 AND WEEKDAY( '".$str_fecha."') = 3 AND TRUNCATE((DATEDIFF( '".$str_fecha."', fecha_inicio) / 7), 0) % repetir_cada = 0 )
                        #FRECUENCIA SEMANAL - VIERNES
                        OR ( tipo_frecuencia = 0 AND dia_5 = 1 AND WEEKDAY( '".$str_fecha."') = 4 AND TRUNCATE((DATEDIFF( '".$str_fecha."', fecha_inicio) / 7), 0) % repetir_cada = 0 )
                        #FRECUENCIA SEMANAL - SABADO
                        OR ( tipo_frecuencia = 0 AND dia_6 = 1 AND WEEKDAY( '".$str_fecha."') = 5 AND TRUNCATE((DATEDIFF( '".$str_fecha."', fecha_inicio) / 7), 0) % repetir_cada = 0 )
                        #FRECUENCIA SEMANAL - DOMINGO
                        OR ( tipo_frecuencia = 0 AND dia_7 = 1 AND WEEKDAY( '".$str_fecha."') = 6 AND TRUNCATE((DATEDIFF( '".$str_fecha."', fecha_inicio) / 7), 0) % repetir_cada = 0 )
                        # --------------------------------------------------------------------------
                        # FRECUENCIA MENSUAL --> DIAS DEL MES
                        OR ( tipo_frecuencia = 1 AND tipo_mensual = 0 AND TIMESTAMPDIFF(MONTH, DATE_FORMAT(fecha_inicio ,'%Y-%m-01'), DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01')) % repetir_cada = 0 AND FIND_IN_SET(DAY( '".$str_fecha."'), values_mes) )
                        # --------------------------------------------------------------------------
                        # FRECUENCIA MENSUAL --> DIAS DE LA SEMANA (PRIMERO, SEGUNDO, ...) --> LUNES
                        OR ( tipo_frecuencia = 1 AND tipo_mensual = 1 AND TIMESTAMPDIFF(MONTH, DATE_FORMAT(fecha_inicio ,'%Y-%m-01'), DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01')) % repetir_cada = 0 AND dia_1 = 1 AND WEEKDAY( '".$str_fecha."') = 0 AND (
                                # Diferenciamos entre Primero, Segundo, Tercero o Cuarto y el ultimo
                            (CAST(values_mes AS UNSIGNED) BETWEEN 0 AND 4 AND '".$str_fecha."' = DATE_ADD(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'),INTERVAL((14 - WEEKDAY(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'))) % 7) + (7 * CAST(values_mes AS UNSIGNED)) DAY ))
                                OR (CAST(values_mes AS UNSIGNED) = 5 AND STR_TO_DATE(LAST_DAY( '".$str_fecha."') - ((7 + WEEKDAY(LAST_DAY( '".$str_fecha."')) - 0) % 7),'%Y%m%d')) ) )
                            # FRECUENCIA MENSUAL --> DIAS DE LA SEMANA (PRIMERO, SEGUNDO, ...) --> MARTES
                                OR ( tipo_frecuencia = 1 AND tipo_mensual = 1 AND TIMESTAMPDIFF(MONTH, DATE_FORMAT(fecha_inicio ,'%Y-%m-01'), DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01')) % repetir_cada = 0 AND dia_2 = 1 AND WEEKDAY( '".$str_fecha."') = 1 AND (
                                # Diferenciamos entre Primero, Segundo, Tercero o Cuarto y el ultimo
                            ( '".$str_fecha."' = DATE_ADD(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'),INTERVAL((15 - WEEKDAY(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'))) % 7) + (7 * CAST(values_mes AS UNSIGNED)) DAY )) OR (CAST(values_mes AS UNSIGNED) = 5 AND STR_TO_DATE(LAST_DAY( '".$str_fecha."') - ((7 + WEEKDAY(LAST_DAY( '".$str_fecha."')) - 1) % 7),'%Y%m%d')) ) )
                            # FRECUENCIA MENSUAL --> DIAS DE LA SEMANA (PRIMERO, SEGUNDO, ...) --> MIERCOLES
                                OR ( tipo_frecuencia = 1 AND tipo_mensual = 1 AND TIMESTAMPDIFF(MONTH, DATE_FORMAT(fecha_inicio ,'%Y-%m-01'), DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01')) % repetir_cada = 0 AND dia_3 = 1 AND WEEKDAY( '".$str_fecha."') = 2 AND (
                                # Diferenciamos entre Primero, Segundo, Tercero o Cuarto y el ultimo
                            ( '".$str_fecha."' = DATE_ADD(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'),INTERVAL((16 - WEEKDAY(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'))) % 7) + (7 * CAST(values_mes AS UNSIGNED)) DAY )) OR (CAST(values_mes AS UNSIGNED) = 5 AND STR_TO_DATE(LAST_DAY( '".$str_fecha."') - ((7 + WEEKDAY(LAST_DAY( '".$str_fecha."')) - 2) % 7),'%Y%m%d')) ) )
                            # FRECUENCIA MENSUAL --> DIAS DE LA SEMANA (PRIMERO, SEGUNDO, ...) --> JUEVES
                                OR ( tipo_frecuencia = 1 AND tipo_mensual = 1 AND TIMESTAMPDIFF(MONTH, DATE_FORMAT(fecha_inicio ,'%Y-%m-01'), DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01')) % repetir_cada = 0 AND dia_4 = 1 AND WEEKDAY( '".$str_fecha."') = 3 AND (
                                # Diferenciamos entre Primero, Segundo, Tercero o Cuarto y el ultimo
                            ( '".$str_fecha."' = DATE_ADD(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'),INTERVAL((17 - WEEKDAY(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'))) % 7) + (7 * CAST(values_mes AS UNSIGNED)) DAY )) OR (CAST(values_mes AS UNSIGNED) = 5 AND STR_TO_DATE(LAST_DAY( '".$str_fecha."') - ((7 + WEEKDAY(LAST_DAY( '".$str_fecha."')) - 3) % 7),'%Y%m%d')) ) )
                            # FRECUENCIA MENSUAL --> DIAS DE LA SEMANA (PRIMERO, SEGUNDO, ...) --> VIERNES
                                OR ( tipo_frecuencia = 1 AND tipo_mensual = 1 AND TIMESTAMPDIFF(MONTH, DATE_FORMAT(fecha_inicio ,'%Y-%m-01'), DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01')) % repetir_cada = 0 AND dia_5 = 1 AND WEEKDAY( '".$str_fecha."') = 4 AND (
                                # Diferenciamos entre Primero, Segundo, Tercero o Cuarto y el ultimo
                            ( '".$str_fecha."' = DATE_ADD(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'),INTERVAL((18 - WEEKDAY(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'))) % 7) + (7 * CAST(values_mes AS UNSIGNED)) DAY )) OR (CAST(values_mes AS UNSIGNED) = 5 AND STR_TO_DATE(LAST_DAY( '".$str_fecha."') - ((7 + WEEKDAY(LAST_DAY( '".$str_fecha."')) - 4) % 7),'%Y%m%d')) ) )
                            # FRECUENCIA MENSUAL --> DIAS DE LA SEMANA (PRIMERO, SEGUNDO, ...) --> SABADO
                                OR ( tipo_frecuencia = 1 AND tipo_mensual = 1 AND TIMESTAMPDIFF(MONTH, DATE_FORMAT(fecha_inicio ,'%Y-%m-01'), DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01')) % repetir_cada = 0 AND dia_6 = 1 AND WEEKDAY( '".$str_fecha."') = 5 AND (
                                # Diferenciamos entre Primero, Segundo, Tercero o Cuarto y el ultimo
                            ( '".$str_fecha."' = DATE_ADD(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'),INTERVAL((19 - WEEKDAY(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'))) % 7) + (7 * CAST(values_mes AS UNSIGNED)) DAY )) OR (CAST(values_mes AS UNSIGNED) = 5 AND STR_TO_DATE(LAST_DAY( '".$str_fecha."') - ((7 + WEEKDAY(LAST_DAY( '".$str_fecha."')) - 5) % 7),'%Y%m%d')) ) )
                            # FRECUENCIA MENSUAL --> DIAS DE LA SEMANA (PRIMERO, SEGUNDO, ...) --> DOMINGO
                                OR ( tipo_frecuencia = 1 AND tipo_mensual = 1 AND TIMESTAMPDIFF(MONTH, DATE_FORMAT(fecha_inicio ,'%Y-%m-01'), DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01')) % repetir_cada = 0 AND dia_7 = 1 AND WEEKDAY( '".$str_fecha."') = 6 AND (
                                # Diferenciamos entre Primero, Segundo, Tercero o Cuarto y el ultimo
                            ( '".$str_fecha."' = DATE_ADD(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'),INTERVAL((20 - WEEKDAY(DATE_FORMAT( '".$str_fecha."' ,'%Y-%m-01'))) % 7) + (7 * CAST(values_mes AS UNSIGNED)) DAY )) OR (CAST(values_mes AS UNSIGNED) = 5 AND STR_TO_DATE(LAST_DAY( '".$str_fecha."') - ((7 + WEEKDAY(LAST_DAY( '".$str_fecha."')) - 6) % 7),'%Y%m%d')) ) )

                ) AS VIS_FUTURO
                LEFT JOIN visita ON visita.fk_entidad = VIS_FUTURO.fk_entidad AND visita.fk_cliente = VIS_FUTURO.fk_cliente AND visita.fk_canal_venta = VIS_FUTURO.fk_canal_venta AND visita.fk_vendedor = VIS_FUTURO.fk_usuario_vendedor AND visita.fecha_calculada = VIS_FUTURO.fecha_calculada
                WHERE visita.fecha_visita IS NULL OR visita.fecha_visita = '".$str_fecha."'
                # OBTENEMOS LAS VISITAS EXISTENTES PARA EL DIA EN CUESTION
                UNION
                SELECT fk_entidad, fk_cliente, fk_canal_venta, fk_vendedor, fecha_visita
                FROM visita
                WHERE visita.estado > 0 AND fecha_visita = '".$str_fecha."'
            ) AS VIS_TODAS
            LEFT JOIN visita ON visita.estado > 0 AND visita.fk_entidad = VIS_TODAS.fk_entidad AND visita.fk_cliente = VIS_TODAS.fk_cliente AND visita.fk_canal_venta = VIS_TODAS.fk_canal_venta AND visita.fk_vendedor = VIS_TODAS.fk_usuario_vendedor AND visita.fecha_visita = VIS_TODAS.fecha_visita
            LEFT JOIN r_usu_cli ON r_usu_cli.estado > 0 AND r_usu_cli.fk_entidad = VIS_TODAS.fk_entidad AND r_usu_cli.fk_cliente = VIS_TODAS.fk_cliente AND r_usu_cli.fk_canal_venta = VIS_TODAS.fk_canal_venta AND r_usu_cli.fk_usuario_vendedor = VIS_TODAS.fk_usuario_vendedor
            LEFT JOIN clientes ON clientes.pk_cliente = VIS_TODAS.fk_cliente
            WHERE (
            (clientes.estacionalidad_periodo1_desde IS NOT NULL AND clientes.estacionalidad_periodo1_hasta IS NOT NULL AND (clientes.estacionalidad_periodo2_desde IS NULL OR clientes.estacionalidad_periodo2_hasta IS NULL) AND '".$str_fecha."' BETWEEN CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(estacionalidad_periodo1_desde, '%m-%d')) AND CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(estacionalidad_periodo1_hasta, '%m-%d')))
            OR (clientes.estacionalidad_periodo2_desde IS NOT NULL AND clientes.estacionalidad_periodo2_hasta IS NOT NULL AND (clientes.estacionalidad_periodo1_desde IS NULL OR clientes.estacionalidad_periodo1_hasta IS NULL) AND '".$str_fecha."' BETWEEN CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(estacionalidad_periodo2_desde, '%m-%d')) AND CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(estacionalidad_periodo2_hasta, '%m-%d')))
            OR (clientes.estacionalidad_periodo2_desde IS NOT NULL AND clientes.estacionalidad_periodo2_hasta IS NOT NULL AND clientes.estacionalidad_periodo1_desde IS NOT NULL AND clientes.estacionalidad_periodo1_hasta IS NOT NULL AND ('".$str_fecha."' BETWEEN CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(estacionalidad_periodo2_desde, '%m-%d')) AND CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(estacionalidad_periodo2_hasta, '%m-%d')) OR '".$str_fecha."' BETWEEN CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(estacionalidad_periodo1_desde, '%m-%d')) AND CONCAT(YEAR(CURDATE()), '-', DATE_FORMAT(estacionalidad_periodo1_hasta, '%m-%d'))))
            OR ((clientes.estacionalidad_periodo2_desde IS NULL OR clientes.estacionalidad_periodo2_hasta IS NULL) AND (clientes.estacionalidad_periodo1_desde IS NULL OR clientes.estacionalidad_periodo1_hasta IS NULL) )
            )
            AND visita.id IS NULL
            AND VIS_TODAS.fecha_visita = '".$str_fecha."'
            AND VIS_TODAS.fk_usuario_vendedor IS NOT NULL"
        ;

        return $sql;


    }


    /**
     * Genera las visitas pendientes que no se han generado en el terminal. Las marcamos como negativas.
     */
    function genVisitasPendientesVendedores() {

        $hoy = date("Y-m-d", strtotime("-1 days"));

        $query = $this->getSqlVisitasVendedores($hoy);

        $query = $this->db->query($query);

        $visitas = $query->result('visita');

        for ($i=0; $i<count($visitas); $i++) {
            $this->saveVisita( $visitas[$i]);

        }

    }

    function saveVisita($visita) {
        $this->load->model("log_model");

        if (!isset($visita->token)) {
            $visita->token = getToken();
        }

        $result = $visita->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on visita_model->saveVisita. Unable to save visita.", ERROR_SAVING_DATA, serialize($visita));
        }
    }

}

?>