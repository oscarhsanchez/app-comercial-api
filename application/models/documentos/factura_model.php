<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_INVOICE);
require_once(APPPATH.ENTITY_INVOICE_LINE);
require_once(APPPATH.ENTITY_RECIBO_COBRO);


class factura_model extends CI_Model {

    /**
     * ----------------------------------------
     *            FUNCIONES DE APOYO
     * ----------------------------------------
     */
    private function getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp, $onlyPdtes) {

        // El parametro $onlyPdtes establece estado de cobro de las facturas que se devuelven.
        $q = "SELECT DISTINCT facturas_cab.pk_factura, facturas_cab.fk_entidad, facturas_cab.fk_usuario_entidad, facturas_cab.serie, facturas_cab.anio, facturas_cab.fk_serie_entidad, facturas_cab.fk_cliente, facturas_cab.fk_cliente_facturacion, facturas_cab.fk_delegacion, facturas_cab.fk_terminal_tpv, facturas_cab.fk_fact_anul, facturas_cab.fk_forma_pago,
                facturas_cab.fk_condicion_pago, facturas_cab.fk_almacen, facturas_cab.cod_factura, facturas_cab.cod_usuario_entidad, facturas_cab.num_serie, facturas_cab.cod_cliente, facturas_cab.bool_origen_albaran,
                facturas_cab.cod_delegacion, facturas_cab.cod_terminal_tpv, facturas_cab.bool_actualiza_numeracion, facturas_cab.bool_recalcular, facturas_cab.cod_fact_anul, facturas_cab.fecha, facturas_cab.fecha_vencimiento, facturas_cab.raz_social, facturas_cab.nif, facturas_cab.direccion,
                facturas_cab.poblacion, facturas_cab.provincia, facturas_cab.codpostal, facturas_cab.base_imponible_tot, facturas_cab.imp_desc_tot, facturas_cab.imp_iva_tot, facturas_cab.imp_re_tot, facturas_cab.imp_retencion_tot, facturas_cab.imp_total, facturas_cab.observaciones, facturas_cab.period_tipo_frecuencia,
                facturas_cab.repetir_cada, facturas_cab.period_tipo_mensual, facturas_cab.period_values_mes, facturas_cab.period_dia_1, facturas_cab.period_dia_2, facturas_cab.period_dia_3, facturas_cab.period_dia_4, facturas_cab.period_dia_5, facturas_cab.period_dia_6, facturas_cab.period_dia_7, facturas_cab.envios_mails,
                facturas_cab.cod_forma_pago, facturas_cab.cod_condicion_pago, facturas_cab.varios1, facturas_cab.varios2, facturas_cab.varios3, facturas_cab.varios4, facturas_cab.varios5, facturas_cab.varios6, facturas_cab.varios7, facturas_cab.varios8, facturas_cab.varios9, facturas_cab.varios10, facturas_cab.estado_factura,
                facturas_cab.estado AS estadoFac, facturas_cab.token AS tokenFac, facturas_cab.cod_almacen, facturas_cab.fk_repartidor, facturas_cab.fk_repartidor_reasignado,

                facturas_lin.id_factura_lin, facturas_lin.fk_factura, facturas_lin.fk_usuario, facturas_lin.fk_articulo, facturas_lin.fk_tarifa, facturas_lin.cod_usuario_entidad, facturas_lin.cod_concepto, facturas_lin.concepto, facturas_lin.cantidad, facturas_lin.precio, facturas_lin.precio_original, facturas_lin.base_imponible, facturas_lin.descuento, facturas_lin.imp_descuento,
                facturas_lin.iva, facturas_lin.imp_iva, facturas_lin.re, facturas_lin.imp_re, facturas_lin.retencion, facturas_lin.imp_retencion, facturas_lin.total_lin, facturas_lin.varios1 AS varios1Lin, facturas_lin.varios2 AS varios2Lin, facturas_lin.varios3 AS varios3Lin, facturas_lin.varios4 AS varios4Lin,
                facturas_lin.varios5 AS varios5Lin, facturas_lin.varios6 AS varios6Lin, facturas_lin.varios7 AS varios7Lin, facturas_lin.varios8 AS varios8Lin, facturas_lin.varios9 AS varios9Lin, facturas_lin.varios10 AS varios10Lin, facturas_lin.estado AS estadoLin, facturas_lin.token AS tokenLin, facturas_lin.modif_stock,
                facturas_lin.desc_promocion, facturas_lin.imp_promocion, facturas_lin.fk_promocion, facturas_lin.cod_camp, facturas_lin.precio_punto_verde, facturas_lin.coste_medio, facturas_lin.bool_precio_neto

             FROM clientes
             LEFT JOIN albaranes_cab ON albaranes_cab.fk_entidad = ".$entityId." AND clientes.pk_cliente = albaranes_cab.fk_cliente AND (fk_repartidor = '".$userPk."' OR fk_repartidor_reasignado = '".$userPk."') AND albaranes_cab.estado > 0 AND albaranes_cab.fecha_entrega >= CURDATE() AND albaranes_cab.updated_at > '".$lastTimeStamp."'
             LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = ".$entityId." AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
             LEFT JOIN visita ON visita.fk_entidad = ".$entityId." AND  clientes.pk_cliente = visita.fk_cliente AND fk_vendedor_reasignado = '".$userPk."' AND visita.estado > 0
             JOIN facturas_cab ON facturas_cab.fk_entidad = ".$entityId." AND facturas_cab.fk_cliente = clientes.pk_cliente AND facturas_cab.estado >= ".$state."
             JOIN facturas_lin ON facturas_lin.fk_factura = facturas_cab.pk_factura AND facturas_lin.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId."
             AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  OR visita.id IS NOT NULL  OR (albaranes_cab.pk_albaran IS NOT NULL AND bool_asignacion_generica = 0 AND r_usu_cli.pk_usuario_cliente IS NULL) )
             AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR albaranes_cab.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR visita.updated_vendedor_at > '".$lastTimeStamp."' OR facturas_cab.updated_at > '".$lastTimeStamp."' OR facturas_lin.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0
             AND (".$onlyPdtes." = 0 OR facturas_cab.estado_factura = 0 OR facturas_cab.estado_factura = 1)";

        return $q;
    }

    private function getEntityQuery($entityId, $state, $lastTimeStamp, $onlyPdtes, $fromDate) {

        // El parametro $onlyPdtes establece estado de cobro de las facturas que se devuelven.
        $q = " SELECT DISTINCT facturas_cab.pk_factura, facturas_cab.fk_entidad, facturas_cab.fk_usuario_entidad, facturas_cab.serie, facturas_cab.anio, facturas_cab.fk_serie_entidad, facturas_cab.fk_cliente, facturas_cab.fk_cliente_facturacion, facturas_cab.fk_delegacion, facturas_cab.fk_terminal_tpv, facturas_cab.fk_fact_anul, facturas_cab.fk_forma_pago,
                facturas_cab.fk_condicion_pago, facturas_cab.fk_almacen, facturas_cab.cod_factura, facturas_cab.cod_usuario_entidad, facturas_cab.num_serie, facturas_cab.cod_cliente, facturas_cab.bool_origen_albaran,
                facturas_cab.cod_delegacion, facturas_cab.cod_terminal_tpv, facturas_cab.bool_actualiza_numeracion, facturas_cab.bool_recalcular, facturas_cab.cod_fact_anul, facturas_cab.fecha, facturas_cab.fecha_vencimiento, facturas_cab.raz_social, facturas_cab.nif, facturas_cab.direccion,
                facturas_cab.poblacion, facturas_cab.provincia, facturas_cab.codpostal, facturas_cab.base_imponible_tot, facturas_cab.imp_desc_tot, facturas_cab.imp_iva_tot, facturas_cab.imp_re_tot, facturas_cab.imp_retencion_tot, facturas_cab.imp_total, facturas_cab.observaciones, facturas_cab.period_tipo_frecuencia,
                facturas_cab.repetir_cada, facturas_cab.period_tipo_mensual, facturas_cab.period_values_mes, facturas_cab.period_dia_1, facturas_cab.period_dia_2, facturas_cab.period_dia_3, facturas_cab.period_dia_4, facturas_cab.period_dia_5, facturas_cab.period_dia_6, facturas_cab.period_dia_7, facturas_cab.envios_mails,
                facturas_cab.cod_forma_pago, facturas_cab.cod_condicion_pago, facturas_cab.varios1, facturas_cab.varios2, facturas_cab.varios3, facturas_cab.varios4, facturas_cab.varios5, facturas_cab.varios6, facturas_cab.varios7, facturas_cab.varios8, facturas_cab.varios9, facturas_cab.varios10, facturas_cab.estado_factura,
                facturas_cab.estado AS estadoFac, facturas_cab.token AS tokenFac, facturas_cab.cod_almacen, facturas_cab.fk_repartidor, facturas_cab.fk_repartidor_reasignado,

                facturas_lin.id_factura_lin, facturas_lin.fk_factura, facturas_lin.fk_usuario, facturas_lin.fk_articulo, facturas_lin.fk_tarifa, facturas_lin.cod_usuario_entidad, facturas_lin.cod_concepto, facturas_lin.concepto, facturas_lin.cantidad, facturas_lin.precio, facturas_lin.precio_original, facturas_lin.base_imponible, facturas_lin.descuento, facturas_lin.imp_descuento,
                facturas_lin.iva, facturas_lin.imp_iva, facturas_lin.re, facturas_lin.imp_re, facturas_lin.retencion, facturas_lin.imp_retencion, facturas_lin.total_lin, facturas_lin.varios1 AS varios1Lin, facturas_lin.varios2 AS varios2Lin, facturas_lin.varios3 AS varios3Lin, facturas_lin.varios4 AS varios4Lin,
                facturas_lin.varios5 AS varios5Lin, facturas_lin.varios6 AS varios6Lin, facturas_lin.varios7 AS varios7Lin, facturas_lin.varios8 AS varios8Lin, facturas_lin.varios9 AS varios9Lin, facturas_lin.varios10 AS varios10Lin, facturas_lin.estado AS estadoLin, facturas_lin.token AS tokenLin, facturas_lin.modif_stock,
                facturas_lin.desc_promocion, facturas_lin.imp_promocion, facturas_lin.fk_promocion, facturas_lin.cod_camp, facturas_lin.precio_punto_verde, facturas_lin.coste_medio, facturas_lin.bool_precio_neto

             FROM facturas_cab
             JOIN facturas_lin ON facturas_lin.fk_factura = facturas_cab.pk_factura AND facturas_lin.estado >= ".$state."
             WHERE facturas_cab.fk_entidad = ".$entityId."
             AND (facturas_cab.updated_at > '".$lastTimeStamp."' OR facturas_lin.updated_at > '".$lastTimeStamp."' )
             AND facturas_cab.estado >= ".$state."
             AND (".$onlyPdtes." = 0 OR facturas_cab.estado_factura = 0 OR facturas_cab.estado_factura = 1)
             AND facturas_cab.fecha >= '".$fromDate."'";

        return $q;
    }

    private function getByFechaQuery($entityId, $fecha) {

        // El parametro $onlyPdtes establece estado de cobro de las facturas que se devuelven.
        $q = " SELECT DISTINCT facturas_cab.pk_factura, facturas_cab.fk_entidad, facturas_cab.fk_usuario_entidad, facturas_cab.serie, facturas_cab.anio, facturas_cab.fk_serie_entidad, facturas_cab.fk_cliente, facturas_cab.fk_cliente_facturacion, facturas_cab.fk_delegacion, facturas_cab.fk_terminal_tpv, facturas_cab.fk_fact_anul, facturas_cab.fk_forma_pago,
                facturas_cab.fk_condicion_pago, facturas_cab.fk_almacen, facturas_cab.cod_factura, facturas_cab.cod_usuario_entidad, facturas_cab.num_serie, facturas_cab.cod_cliente, facturas_cab.bool_origen_albaran,
                facturas_cab.cod_delegacion, facturas_cab.cod_terminal_tpv, facturas_cab.bool_actualiza_numeracion, facturas_cab.bool_recalcular, facturas_cab.cod_fact_anul, facturas_cab.fecha, facturas_cab.fecha_vencimiento, facturas_cab.raz_social, facturas_cab.nif, facturas_cab.direccion,
                facturas_cab.poblacion, facturas_cab.provincia, facturas_cab.codpostal, facturas_cab.base_imponible_tot, facturas_cab.imp_desc_tot, facturas_cab.imp_iva_tot, facturas_cab.imp_re_tot, facturas_cab.imp_retencion_tot, facturas_cab.imp_total, facturas_cab.observaciones, facturas_cab.period_tipo_frecuencia,
                facturas_cab.repetir_cada, facturas_cab.period_tipo_mensual, facturas_cab.period_values_mes, facturas_cab.period_dia_1, facturas_cab.period_dia_2, facturas_cab.period_dia_3, facturas_cab.period_dia_4, facturas_cab.period_dia_5, facturas_cab.period_dia_6, facturas_cab.period_dia_7, facturas_cab.envios_mails,
                facturas_cab.cod_forma_pago, facturas_cab.cod_condicion_pago, facturas_cab.varios1, facturas_cab.varios2, facturas_cab.varios3, facturas_cab.varios4, facturas_cab.varios5, facturas_cab.varios6, facturas_cab.varios7, facturas_cab.varios8, facturas_cab.varios9, facturas_cab.varios10, facturas_cab.estado_factura,
                facturas_cab.estado AS estadoFac, facturas_cab.token AS tokenFac, facturas_cab.cod_almacen, facturas_cab.fk_repartidor, facturas_cab.fk_repartidor_reasignado,

                facturas_lin.id_factura_lin, facturas_lin.fk_factura, facturas_lin.fk_usuario, facturas_lin.fk_articulo, facturas_lin.fk_tarifa, facturas_lin.cod_usuario_entidad, facturas_lin.cod_concepto, facturas_lin.concepto, facturas_lin.cantidad, facturas_lin.precio, facturas_lin.precio_original, facturas_lin.base_imponible, facturas_lin.descuento, facturas_lin.imp_descuento,
                facturas_lin.iva, facturas_lin.imp_iva, facturas_lin.re, facturas_lin.imp_re, facturas_lin.retencion, facturas_lin.imp_retencion, facturas_lin.total_lin, facturas_lin.varios1 AS varios1Lin, facturas_lin.varios2 AS varios2Lin, facturas_lin.varios3 AS varios3Lin, facturas_lin.varios4 AS varios4Lin,
                facturas_lin.varios5 AS varios5Lin, facturas_lin.varios6 AS varios6Lin, facturas_lin.varios7 AS varios7Lin, facturas_lin.varios8 AS varios8Lin, facturas_lin.varios9 AS varios9Lin, facturas_lin.varios10 AS varios10Lin, facturas_lin.estado AS estadoLin, facturas_lin.token AS tokenLin, facturas_lin.modif_stock,
                facturas_lin.desc_promocion, facturas_lin.imp_promocion, facturas_lin.fk_promocion, facturas_lin.cod_camp, facturas_lin.precio_punto_verde, facturas_lin.coste_medio, facturas_lin.bool_precio_neto

             FROM facturas_cab
             JOIN facturas_lin ON facturas_lin.fk_factura = facturas_cab.pk_factura AND facturas_lin.estado > 0
             WHERE facturas_cab.fk_entidad = ".$entityId."  AND facturas_cab.estado > 0 AND facturas_cab.fecha >= '".$fecha."'";


        return $q;
    }

    private function getByClienteQuery($entityId, $clientePk, $fromDate, $lastTimeStamp, $state) {

        // El parametro $onlyPdtes establece estado de cobro de las facturas que se devuelven.
        $q = " SELECT DISTINCT facturas_cab.pk_factura, facturas_cab.fk_entidad, facturas_cab.fk_usuario_entidad, facturas_cab.serie, facturas_cab.anio, facturas_cab.fk_serie_entidad, facturas_cab.fk_cliente, facturas_cab.fk_cliente_facturacion, facturas_cab.fk_delegacion, facturas_cab.fk_terminal_tpv, facturas_cab.fk_fact_anul, facturas_cab.fk_forma_pago,
                facturas_cab.fk_condicion_pago, facturas_cab.fk_almacen, facturas_cab.cod_factura, facturas_cab.cod_usuario_entidad, facturas_cab.num_serie, facturas_cab.cod_cliente, facturas_cab.bool_origen_albaran,
                facturas_cab.cod_delegacion, facturas_cab.cod_terminal_tpv, facturas_cab.bool_actualiza_numeracion, facturas_cab.bool_recalcular, facturas_cab.cod_fact_anul, facturas_cab.fecha, facturas_cab.fecha_vencimiento, facturas_cab.raz_social, facturas_cab.nif, facturas_cab.direccion,
                facturas_cab.poblacion, facturas_cab.provincia, facturas_cab.codpostal, facturas_cab.base_imponible_tot, facturas_cab.imp_desc_tot, facturas_cab.imp_iva_tot, facturas_cab.imp_re_tot, facturas_cab.imp_retencion_tot, facturas_cab.imp_total, facturas_cab.observaciones, facturas_cab.period_tipo_frecuencia,
                facturas_cab.repetir_cada, facturas_cab.period_tipo_mensual, facturas_cab.period_values_mes, facturas_cab.period_dia_1, facturas_cab.period_dia_2, facturas_cab.period_dia_3, facturas_cab.period_dia_4, facturas_cab.period_dia_5, facturas_cab.period_dia_6, facturas_cab.period_dia_7, facturas_cab.envios_mails,
                facturas_cab.cod_forma_pago, facturas_cab.cod_condicion_pago, facturas_cab.varios1, facturas_cab.varios2, facturas_cab.varios3, facturas_cab.varios4, facturas_cab.varios5, facturas_cab.varios6, facturas_cab.varios7, facturas_cab.varios8, facturas_cab.varios9, facturas_cab.varios10, facturas_cab.estado_factura,
                facturas_cab.estado AS estadoFac, facturas_cab.token AS tokenFac, facturas_cab.cod_almacen, facturas_cab.fk_repartidor, facturas_cab.fk_repartidor_reasignado,

                facturas_lin.id_factura_lin, facturas_lin.fk_factura, facturas_lin.fk_usuario, facturas_lin.fk_articulo, facturas_lin.fk_tarifa, facturas_lin.cod_usuario_entidad, facturas_lin.cod_concepto, facturas_lin.concepto, facturas_lin.cantidad, facturas_lin.precio, facturas_lin.precio_original, facturas_lin.base_imponible, facturas_lin.descuento, facturas_lin.imp_descuento,
                facturas_lin.iva, facturas_lin.imp_iva, facturas_lin.re, facturas_lin.imp_re, facturas_lin.retencion, facturas_lin.imp_retencion, facturas_lin.total_lin, facturas_lin.varios1 AS varios1Lin, facturas_lin.varios2 AS varios2Lin, facturas_lin.varios3 AS varios3Lin, facturas_lin.varios4 AS varios4Lin,
                facturas_lin.varios5 AS varios5Lin, facturas_lin.varios6 AS varios6Lin, facturas_lin.varios7 AS varios7Lin, facturas_lin.varios8 AS varios8Lin, facturas_lin.varios9 AS varios9Lin, facturas_lin.varios10 AS varios10Lin, facturas_lin.estado AS estadoLin, facturas_lin.token AS tokenLin, facturas_lin.modif_stock,
                facturas_lin.desc_promocion, facturas_lin.imp_promocion, facturas_lin.fk_promocion, facturas_lin.cod_camp, facturas_lin.precio_punto_verde, facturas_lin.coste_medio, facturas_lin.bool_precio_neto

             FROM facturas_cab
             JOIN facturas_lin ON facturas_lin.fk_factura = facturas_cab.pk_factura AND facturas_lin.estado >= $state
             WHERE facturas_cab.fk_entidad = ".$entityId."  AND facturas_cab.estado >= $state AND facturas_cab.fk_cliente >= '$clientePk'
                   AND (facturas_cab.updated_at > '".$lastTimeStamp."' OR facturas_lin.updated_at > '".$lastTimeStamp."')
                   AND facturas_cab.fecha >= '$fromDate'
                    ";


        return $q;
    }



    private function getAssignedRecibosQuery($entityId, $userPk, $state, $lastTimeStamp) {

        //Obtenemos los recibos de las facturas
        $q = "SELECT DISTINCT recibos_cobro.*, facturas_cab.fk_cliente
             FROM clientes
             LEFT JOIN albaranes_cab ON albaranes_cab.fk_entidad = ".$entityId." AND clientes.pk_cliente = albaranes_cab.fk_cliente AND (fk_repartidor = '".$userPk."' OR fk_repartidor_reasignado = '".$userPk."') AND albaranes_cab.estado > 0 AND albaranes_cab.fecha_entrega >= CURDATE() AND albaranes_cab.updated_at > '".$lastTimeStamp."'
             LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = ".$entityId." AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
             LEFT JOIN visita ON visita.fk_entidad = ".$entityId." AND clientes.pk_cliente = visita.fk_cliente AND fk_vendedor_reasignado = '".$userPk."' AND visita.estado > 0
             JOIN facturas_cab ON facturas_cab.fk_entidad = ".$entityId." AND facturas_cab.fk_cliente = clientes.pk_cliente AND facturas_cab.estado >= ".$state."
             JOIN recibos_cobro ON recibos_cobro.fk_entidad = ".$entityId." AND recibos_cobro.fk_factura_cliente = facturas_cab.pk_factura AND recibos_cobro.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId." AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  OR visita.id IS NOT NULL  OR albaranes_cab.pk_albaran IS NOT NULL )
             AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR albaranes_cab.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR visita.updated_vendedor_at > '".$lastTimeStamp."' OR facturas_cab.updated_at > '".$lastTimeStamp."' OR recibos_cobro.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0
             AND (facturas_cab.estado_factura = 0 OR facturas_cab.estado_factura = 1)";

        return $q;
    }

    private function getEntityRecibosQuery($entityId, $state, $lastTimeStamp) {

        //Obtenemos los recibos de las facturas
        $q = "SELECT recibos_cobro.*, fk_cliente
             FROM recibos_cobro
             JOIN facturas_cab ON pk_factura = fk_factura_cliente
             WHERE recibos_cobro.fk_entidad = ".$entityId." AND recibos_cobro.updated_at > '".$lastTimeStamp."'";

        return $q;
    }

    private function getRecibosByFechaCobroQuery($entityId, $fechaCobro) {

        //Obtenemos los recibos de las facturas
        $q = "SELECT DISTINCT recibos_cobro.*, facturas_cab.fk_cliente
             FROM facturas_cab
             JOIN recibos_cobro ON recibos_cobro.fk_entidad = ".$entityId." AND recibos_cobro.fk_factura_cliente = facturas_cab.pk_factura AND recibos_cobro.estado > 0
             WHERE facturas_cab.fk_entidad = ".$entityId." AND facturas_cab.estado > 0 AND estado_recibo = 1 AND fecha_cobro = '".$fechaCobro."'";

        return $q;
    }

    private function getRecibosByClienteQuery($entityId, $clientePk, $fromDate, $lastTimeStamp, $state) {

        //Obtenemos los recibos de las facturas
        $q = "SELECT DISTINCT recibos_cobro.*, facturas_cab.fk_cliente
             FROM facturas_cab
             JOIN recibos_cobro ON recibos_cobro.fk_entidad = ".$entityId." AND recibos_cobro.fk_factura_cliente = facturas_cab.pk_factura AND recibos_cobro.estado >= $state
             WHERE facturas_cab.fk_entidad = ".$entityId." AND facturas_cab.estado > 0  AND facturas_cab.fecha >= '$fromDate'
             AND (facturas_cab.updated_at > '".$lastTimeStamp."' OR recibos_cobro.updated_at > '".$lastTimeStamp."')
             AND facturas_cab.fk_cliente >= '$clientePk'
             ";

        return $q;
    }

    private function getListQuery($entityId, $clientePk, $pagada, $offset, $limit, $order, $sort) {
        $q = "SELECT * FROM facturas_cab WHERE estado > 0 AND fk_entidad = ".$entityId;

        if ($pagada == 1) $q .= " AND estado_factura = 2 ";
        else if ($pagada === 0) $q .= " AND estado_factura < 2 ";


        if ($clientePk) $q .= " AND fk_cliente = '".$clientePk."'";
        if ($order) {
            $q .= " ORDER BY ".$order;
            if ($sort == "DESC") $q .= " DESC ";
            else $q .= " ASC ";
        }

        if ($limit) {
            $q .= " LIMIT ".$limit;
            if ($offset) $q .= " OFFSET ".$offset;
        }

        return $q;
    }

    /**
     * Funcion que devuelve un resumen anual agrupado por meses (numero de lineas y base imponible).
     *
     * @param $entityId
     * @param $year
     *
     */
    function getYearSummary($entityId, $year) {
        $q = "SELECT MONTH(fecha) AS mes, COUNT(*) AS num_reg, ROUND(SUM(base_imponible), 2) AS base_imponible FROM facturas_cab cab
                JOIN facturas_lin lin ON lin.fk_entidad = cab.fk_entidad AND fk_factura = pk_factura AND lin.estado > 0
                WHERE cab.fk_entidad = $entityId AND cab.estado > 0 AND YEAR(fecha) = $year
                GROUP BY MONTH(fecha)";

        $query = $this->db->query($q);

        $result = $query->result();

        return array("resumen" => $result?$result:array());

        return $q;
    }

    /**
     * Funcion que devuelve un resumen para un mes agrupado por dias (numero de lineas y base imponible).
     *
     * @param $entityId
     * @param $year
     * @param $month
     *
     */
    function getMonthSummary($entityId, $year, $month) {
        $q = "SELECT DAY(fecha) AS dia, COUNT(*) AS num_reg, ROUND(SUM(base_imponible), 2) AS base_imponible FROM facturas_cab cab
                JOIN facturas_lin lin ON lin.fk_entidad = cab.fk_entidad AND fk_factura = pk_factura AND lin.estado > 0
                WHERE cab.fk_entidad = $entityId AND cab.estado > 0 AND YEAR(fecha) = $year AND MONTH(fecha) = $month
                GROUP BY DAY(fecha)";

        $query = $this->db->query($q);

        $result = $query->result();

        return array("resumen" => $result?$result:array());

        return $q;
    }

    /**
     * Funcion que devuelve un listado de facturas.
     *
     * @param $entityId
     * @param $clientePk (opcional)
     * @param $offset (opcional)
     * @param $limit (opcional)
     * @param $order (opcional)
     *
     */
    function listFacturas($entityId, $clientePk, $pagada, $offset, $limit, $order, $sort) {
        $query = $this->getListQuery($entityId, $clientePk, $pagada, $offset, $limit, $order, $sort);

        $query = $this->db->query($query);

        $facturas = $query->result('factura');

        return array("facturas" => $facturas?$facturas:array());

    }



    /**
     * Funcion que devuelve una factura a partir de su Token
     *
     * @param $token
     * @param $entityId
     * @return factura
     */
    function getFacturaByToken($token, $entityId) {

        //CABECERA
        $this->db->where('facturas_cab.token', $token);
        $this->db->where('facturas_cab.fk_entidad', $entityId);
        $query = $this->db->get('facturas_cab');

        $factura = $query->row(0, 'factura');

        //LINEAS
        if ($factura) {
            $this->db->where('fk_factura', $factura->pk_factura);
            $query = $this->db->get('facturas_lin');
            $facturaLines = $query->result('facturaLine');

            $factura->facturaLines = $facturaLines;
        }

        return $factura;

    }

    /**
     * Funcion que devuelve una factura a partir de su codigo
     *
     * @param $codigo
     * @param $entityId
     * @return factura
     */
    function getFacturaByCodigo($codigo, $entityId) {

        //CABECERA
        $this->db->where('facturas_cab.cod_factura', $codigo);
        $this->db->where('facturas_cab.fk_entidad', $entityId);
        $query = $this->db->get('facturas_cab');

        $factura = $query->row(0, 'factura');

        //LINEAS
        if ($factura) {
            $this->db->where('fk_factura', $factura->pk_factura);
            $query = $this->db->get('facturas_lin');
            $facturaLines = $query->result('facturaLine');

            $factura->facturaLines = $facturaLines;
        }

        return $factura;

    }

    /**
     * Funcion que devuelve la cabecera de una factura a partir de su codigo
     *
     * @param $codigo
     * @param $entityId
     * @return factura
     */
    function getCabeceraByCodigo($codigo, $entityId) {

        //CABECERA
        $this->db->where('facturas_cab.cod_factura', $codigo);
        $this->db->where('facturas_cab.fk_entidad', $entityId);
        $query = $this->db->get('facturas_cab');

        $factura = $query->row(0, 'factura');

        return $factura;

    }

    /**
     * @param $entityId
     * @param $facturaPk
     * @param $tarjeta
     * @param $importe
     *
     * Coge el siguiente numero de pedido de la serie predeterminada y actualiza la tabla
     */
    function updatePagoTarjeta($entityId, $facturaPk, $tarjeta, $importe) {

        $q = new stdClass();
        $q->bool_conf_pago_tarjeta = 1;
        $q->tarjeta = $tarjeta;
        $q->importe = $importe;
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('pk_factura', $facturaPk);

        return $this->db->update('facturas_cab', $q);

    }

    /**
     * Funcion que verifica si una factura esta pagada por tarjeta de credito
     *
     * @param $facturaPk
     * @param $entityId
     * @return $factura->bool_conf_pago_tarjeta
     */
    function verifyPagoTarjeta($entityId, $pedidoPk) {

        $this->db->where('pk_factura', $pedidoPk);
        $this->db->where('fk_entidad', $entityId);
        $query = $this->db->get('facturas_cab');

        $factura = $query->row(0, 'factura');

        if ($factura)
            return $factura->bool_conf_pago_tarjeta;
        else
            return 0;

    }

    /**
     * Funcion que devuelve un Recibo a partir de su Token
     *
     * @param $token
     * @param $entityId
     * @return recibo
     */
    function getReciboByToken($token, $entityId) {


        $this->db->where('recibos_cobro.token', $token);
        $this->db->where('recibos_cobro.fk_entidad', $entityId);
        $query = $this->db->get('recibos_cobro');

        $recibo = $query->row(0, 'recibo_cobro');

        return $recibo;

    }

    /**
     * Funcion que devuelve los recibos de una factura
     *
     * @param $facturaPk
     * @param $entityId
     * @return Array(recibo_cobro)
     */
    function getRecibosByFactura($facturaPk, $entityId) {


        $this->db->where('recibos_cobro.fk_factura_cliente', $facturaPk);
        $this->db->where('recibos_cobro.fk_entidad', $entityId);
        $query = $this->db->get('recibos_cobro');

        $recibos = $query->result('recibo_cobro');

        return $recibos;

    }

    /**
     * Funcion que devuelve los recibos a partir de una fecha de cobro
     *
     * @param $fechaCobro
     * @param $entityId
     * @return Array(recibo_cobro)
     */
    function getRecibosByFechaCobro($entityId, $fechaCobro) {

        $query = $this->getRecibosByFechaCobroQuery($entityId, $fechaCobro);

        $query = $this->db->query($query);

        $recibos = $query->result('recibo_cobro');

        return $recibos;

    }

    /**
     * Funcion que devuelve los recibos de un cliente
     *
     * @param $entityId
     * @param $clientePk
     * @param $state
     * @param $fromDate
     * @param $lastupdate
     *
     * @return Array(recibo_cobro)
     */
    function getRecibosByCliente($entityId, $clientePk, $fromDate, $lastTimeStamp, $state) {

        $query = $this->getRecibosByClienteQuery($entityId, $clientePk, $fromDate, $lastTimeStamp, $state);

        $query = $this->db->query($query);

        $recibos = $query->result('recibo_cobro');

        return $recibos;

    }

    /*
     * Elimina Recibo en base a una condicion
     *
     * $condicion (campo='valor' and campo=valor)
     *
     */
    function delRecibosByCondition($entityId, $condicion){

        $this->db->set('estado', 0, false);

        $where = "(fk_entidad='".$entityId."') AND (".$condicion.")";
        $this->db->where($where);
        return $this->db->update('recibos_cobro');



        return false;
    }

    /*
     * Elimina Recibo en base a una condicion
     *
     * $condicion (campo='valor' AND campo=valor)
     * $data (campo=valor;campo=valor)
     *
     */
    function updateRecibosByCondition($entityId, $condicion, $data){

        $datos = explode(';', $data);

        if (count($datos) == 0) return false;

        foreach($datos as $datoArr) {
            $dato = explode('=', $datoArr);
            if (count($dato) == 2)
                $this->db->set($dato[0], $dato[1], false);
            else
                return false;
        }
        $where = "(fk_entidad='".$entityId."') AND (".$condicion.")";
        $this->db->where($where);

        $this->db->update('recibos_cobro');



        return false;
    }

    /*
     * Actualiza facturas_cab y facturas_lin en base a una condicion
     *
     * $condicion (campo='valor' AND campo=valor)
     * $data (campo=valor;campo=valor)
     *
     * IMPORTANTE: Usar prefijos para hacer referencia a las tablas (cab, lin)
     */
    function updateByCondition($entityId, $condicion, $data){

        $set = str_replace(";", ",", $data);
        $where = "(cab.fk_entidad='".$entityId."') AND (".$condicion.")";

        $query = "UPDATE facturas_cab cab
                    JOIN facturas_lin lin ON lin.fk_entidad = cab.fk_entidad AND fk_factura = pk_factura AND lin.estado > 0
                    SET $set
                    WHERE $where
                 ";

        return $query = $this->db->query($query);

    }

    /*
     * Elimina facturas_cab en base a una condicion
     *
     * $condicion (campo='valor' and campo=valor)
     *
     */
    function delCabByCondition($entityId, $condicion){

        $this->db->set('estado', 0, false);

        $where = "(fk_entidad='".$entityId."') AND (".$condicion.")";
        $this->db->where($where);
        return $this->db->update('facturas_cab');



        return false;
    }

    /*
     * Actualiza facturas_cab en base a una condicion
     *
     * $condicion (campo='valor' AND campo=valor)
     * $data (campo=valor;campo=valor)
     *
     */
    function updateCabByCondition($entityId, $condicion, $data){

        $datos = explode(';', $data);

        if (count($datos) == 0) return false;

        foreach($datos as $datoArr) {
            $dato = explode('=', $datoArr);
            if (count($dato) == 2)
                $this->db->set($dato[0], $dato[1], false);
            else
                return false;
        }
        $where = "(fk_entidad='".$entityId."') AND (".$condicion.")";
        $this->db->where($where);

        $this->db->update('facturas_cab');



        return false;
    }

    /*
     * Elimina facturas_lin en base a una condicion
     *
     * $condicion (campo='valor' and campo=valor)
     *
     */
    function delLineByCondition($entityId, $condicion){

        $this->db->set('estado', 0, false);

        $where = "(fk_entidad='".$entityId."') AND (".$condicion.")";
        $this->db->where($where);
        return $this->db->update('facturas_lin');



        return false;
    }

    /*
     * Actualiza facturas_lin en base a una condicion
     *
     * $condicion (campo='valor' AND campo=valor)
     * $data (campo=valor;campo=valor)
     *
     */
    function updateLineByCondition($entityId, $condicion, $data){

        $datos = explode(';', $data);

        if (count($datos) == 0) return false;

        foreach($datos as $datoArr) {
            $dato = explode('=', $datoArr);
            if (count($dato) == 2)
                $this->db->set($dato[0], $dato[1], false);
            else
                return false;
        }
        $where = "(fk_entidad='".$entityId."') AND (".$condicion.")";
        $this->db->where($where);

        $this->db->update('facturas_lin');



        return false;
    }




    /**
     * Funcion que establece una factura como pagada
     *
     * @param $facturaPk
     * return 1 | 0
     */
    function setFacturaAsPagada($facturaPk) {


        $this->db->set('estado_factura', 2, false);
        $this->db->where("pk_factura", $facturaPk);
        $this->db->where("estado_factura <", 2, false); //Solo cmabiamos si el estado esta en pdte o pdte parcial.
        return $this->db->update('facturas_cab');

    }

    /**
     * Funcion que establece TODAS las facturas  del entidad como pagadas
     *
     * @param $facturaPk
     * return 1 | 0
     */
    function setAllFacturaAsPagada($fk_entidad) {

        $this->db->set('estado_factura', 2, false);
        $this->db->where("fk_entidad", $fk_entidad);
        $this->db->where("estado_factura <", 2, false); //Solo cmabiamos si el estado esta en pdte o pdte parcial.
        return $this->db->update('facturas_cab');

    }

    /**
     * Funcion que establece un recibo como cobrado
     *
     * @param $reciboPk
     * return 1 | 0
     */
    function setReciboAsPagado($reciboPk) {

        $this->db->set('estado_recibo', 1, false);
        $this->db->where("pk_recibo_cobro", $reciboPk);
        return $this->db->update('recibos_cobro');

    }

    /**
     * Funcion que establece TODOS los recibos como cobrados de una entidad
     *
     * @param $reciboPk
     * return 1 | 0
     */
    function setAllReciboAsPagado($fk_entidad) {

        $this->db->set('estado_recibo', 1, false);
        $this->db->where("fk_entidad", $fk_entidad);
        return $this->db->update('recibos_cobro');

    }

    /**
     * Funcion que establece un recibo como cobrado en base a su token
     *
     * @param $reciboToken
     * return 1 | 0
     */
    function setReciboAsPagadoByToken($reciboToken) {

        $this->db->set('estado_recibo', 1, false);
        $this->db->where("token", $reciboToken);
        return $this->db->update('recibos_cobro');

    }

    /**
     * Funcion que devuelve las facturas de un cliente
     *
     * @param $entityId
     * @param $clientePk
     * @param $fromDate
     * @param $lastTimeStamp
     * @param $state
     * @return array(facturas)
     *
     */
    function getByCliente($entityId, $clientePk, $fromDate, $lastTimeStamp, $state) {


        $query = $this->getByClienteQuery($entityId, $clientePk, $fromDate, $lastTimeStamp, $state);

        $query = $this->db->query($query);

        $facs = $query->result();

        $facturas = array();
        $lins = array();
        $lastFac = "";
        $fac = null;
        for ($i=0; $i<count($facs); $i++) {
            if ($lastFac != $facs[$i]->pk_factura) {
                if ($fac) {
                    $fac->facturaLines = $lins;
                    $facturas[] = $fac;
                }
                $fac = new factura();
                $fac->set($facs[$i]);
                //Asignamos el token y estado que lo hemos renombrado en la consulta
                $fac->estado = $facs[$i]->estadoFac;
                $fac->token = $facs[$i]->tokenFac;
                $lins = array();

                $lastFac = $fac->pk_factura;
            }
            // Linea de Factura
            $lin = new facturaLine();
            $lin->set($facs[$i]);
            //Asignamos los campos renombrados
            $lin->estado = $facs[$i]->estadoLin;
            $lin->token = $facs[$i]->tokenLin;
            $lin->varios1 = $facs[$i]->varios1Lin;
            $lin->varios2 = $facs[$i]->varios2Lin;
            $lin->varios3 = $facs[$i]->varios3Lin;
            $lin->varios4 = $facs[$i]->varios4Lin;
            $lin->varios5 = $facs[$i]->varios5Lin;
            $lin->varios6 = $facs[$i]->varios6Lin;
            $lin->varios7 = $facs[$i]->varios7Lin;
            $lin->varios8 = $facs[$i]->varios8Lin;
            $lin->varios9 = $facs[$i]->varios9Lin;
            $lin->varios10 = $facs[$i]->varios10Lin;

            $lins[] = $lin;

        }
        //Metemos el ultimo
        if ($fac) {
            $fac->facturaLines = $lins;
            $facturas[] = $fac;
        }

        $rowcount = sizeof($facturas);


        return array("facturas" => $facturas?$facturas:array());

    }

    /**
     * Funcion que devuelve las facturas de una entidad para una fecha concreta
     *
     * @param $entityId
     * @param $fecha
     *
     * @return $pagination<br/> array(facturas)
     *
     */
    function getByFecha($entityId, $fecha) {


        $query = $this->getByFechaQuery($entityId, $fecha);

        $query = $this->db->query($query);

        $facs = $query->result();

        $facturas = array();
        $lins = array();
        $lastFac = "";
        $fac = null;
        for ($i=0; $i<count($facs); $i++) {
            if ($lastFac != $facs[$i]->pk_factura) {
                if ($fac) {
                    $fac->facturaLines = $lins;
                    $facturas[] = $fac;
                }
                $fac = new factura();
                $fac->set($facs[$i]);
                //Asignamos el token y estado que lo hemos renombrado en la consulta
                $fac->estado = $facs[$i]->estadoFac;
                $fac->token = $facs[$i]->tokenFac;
                $lins = array();

                $lastFac = $fac->pk_factura;
            }
            // Linea de Factura
            $lin = new facturaLine();
            $lin->set($facs[$i]);
            //Asignamos los campos renombrados
            $lin->estado = $facs[$i]->estadoLin;
            $lin->token = $facs[$i]->tokenLin;
            $lin->varios1 = $facs[$i]->varios1Lin;
            $lin->varios2 = $facs[$i]->varios2Lin;
            $lin->varios3 = $facs[$i]->varios3Lin;
            $lin->varios4 = $facs[$i]->varios4Lin;
            $lin->varios5 = $facs[$i]->varios5Lin;
            $lin->varios6 = $facs[$i]->varios6Lin;
            $lin->varios7 = $facs[$i]->varios7Lin;
            $lin->varios8 = $facs[$i]->varios8Lin;
            $lin->varios9 = $facs[$i]->varios9Lin;
            $lin->varios10 = $facs[$i]->varios10Lin;

            $lins[] = $lin;

        }
        //Metemos el ultimo
        if ($fac) {
            $fac->facturaLines = $lins;
            $facturas[] = $fac;
        }

        $rowcount = sizeof($facturas);


        return array("facturas" => $facturas?$facturas:array());

    }


    /**
     * Funcion que devuelve las facturas de una entidad
     *  a partir de una fecha de actualizacion y desde una fecha determinada.
     * El parametro state establece el estado de los albranes a devolver. Estado >= $state
     * El parametro $onlyPdtes establece si tiene que devolver las facturas pendientes de cobro o todas.
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $entityId
     * @param $fromDate
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @param $onlyPdtes --> 0 o 1
     * @return $pagination<br/> array(facturas)
     *
     */
    function getMultipartCachedFacturas($entityId, $pagination, $state, $lastTimeStamp, $onlyPdtes, $fromDate) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $facturas = unserialize($this->esocialmemcache->get($key));
            if (!$facturas) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
            $this->esocialmemcache->delete($key);
        } else {
            $query = $this->getEntityQuery($entityId, $state, $lastTimeStamp, $onlyPdtes, $fromDate);

            $query = $this->db->query($query);

            $facs = $query->result();

            $facturas = array();
            $lins = array();
            $lastFac = "";
            $fac = null;
            for ($i=0; $i<count($facs); $i++) {
                if ($lastFac != $facs[$i]->pk_factura) {
                    if ($fac) {
                        $fac->facturaLines = $lins;
                        $facturas[] = $fac;
                    }
                    $fac = new factura();
                    $fac->set($facs[$i]);
                    //Asignamos el token y estado que lo hemos renombrado en la consulta
                    $fac->estado = $facs[$i]->estadoFac;
                    $fac->token = $facs[$i]->tokenFac;
                    $lins = array();

                    $lastFac = $fac->pk_factura;
                }
                // Linea de Factura
                $lin = new facturaLine();
                $lin->set($facs[$i]);
                //Asignamos los campos renombrados
                $lin->estado = $facs[$i]->estadoLin;
                $lin->token = $facs[$i]->tokenLin;
                $lin->varios1 = $facs[$i]->varios1Lin;
                $lin->varios2 = $facs[$i]->varios2Lin;
                $lin->varios3 = $facs[$i]->varios3Lin;
                $lin->varios4 = $facs[$i]->varios4Lin;
                $lin->varios5 = $facs[$i]->varios5Lin;
                $lin->varios6 = $facs[$i]->varios6Lin;
                $lin->varios7 = $facs[$i]->varios7Lin;
                $lin->varios8 = $facs[$i]->varios8Lin;
                $lin->varios9 = $facs[$i]->varios9Lin;
                $lin->varios10 = $facs[$i]->varios10Lin;

                $lins[] = $lin;

            }
            //Metemos el ultimo
            if ($fac) {
                $fac->facturaLines = $lins;
                $facturas[] = $fac;
            }

            $rowcount = sizeof($facturas);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($facturas, $pagination->pageSize);

                $facturas = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "facturas" => $facturas?$facturas:array());

    }


    /**
     * Funcion que devuelve las facturas de los clientes asignados a un usuario (Vendedor o repartidor)
     *  a partir de una fecha de actualizacion y desde una fecha determinada.
     * El parametro state establece el estado de los albranes a devolver. Estado >= $state
     * El parametro $onlyPdtes establece si tiene que devolver las facturas pendientes de cobro o todas.
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $userPk
     * @param $entityId
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @param $onlyPdtes --> 0 o 1
     * @return $pagination<br/> array(facturas)
     *
     */
    function getMultipartCachedClientesFacturas($userPk, $entityId, $pagination, $state, $lastTimeStamp, $onlyPdtes) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $facturas = unserialize($this->esocialmemcache->get($key));
            if (!$facturas) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
            $this->esocialmemcache->delete($key);
        } else {
            $query = $this->getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp, $onlyPdtes);

            $query = $this->db->query($query);
            $facs = $query->result();
            $facturas = array();
            $lins = array();
            $lastFac = "";
            $fac = null;

            for ($i=0; $i<count($facs); $i++) {
                if ($lastFac != $facs[$i]->pk_factura) {
                    if ($fac) {
                        $fac->facturaLines = $lins;
                        $facturas[] = $fac;
                    }
                    $fac = new factura();
                    $fac->set($facs[$i]);
                    //Asignamos el token y estado que lo hemos renombrado en la consulta
                    $fac->estado = $facs[$i]->estadoFac;
                    $fac->token = $facs[$i]->tokenFac;
                    $lins = array();

                    $lastFac = $fac->pk_factura;
                }
                // Linea de Factura
                $lin = new facturaLine();
                $lin->set($facs[$i]);
                //Asignamos los campos renombrados
                $lin->estado = $facs[$i]->estadoLin;
                $lin->token = $facs[$i]->tokenLin;
                $lin->varios1 = $facs[$i]->varios1Lin;
                $lin->varios2 = $facs[$i]->varios2Lin;
                $lin->varios3 = $facs[$i]->varios3Lin;
                $lin->varios4 = $facs[$i]->varios4Lin;
                $lin->varios5 = $facs[$i]->varios5Lin;
                $lin->varios6 = $facs[$i]->varios6Lin;
                $lin->varios7 = $facs[$i]->varios7Lin;
                $lin->varios8 = $facs[$i]->varios8Lin;
                $lin->varios9 = $facs[$i]->varios9Lin;
                $lin->varios10 = $facs[$i]->varios10Lin;

                $lins[] = $lin;

            }
            //Metemos el ultimo
            if ($fac) {
                $fac->facturaLines = $lins;
                $facturas[] = $fac;
            }

            $rowcount = sizeof($facturas);

            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($facturas, $pagination->pageSize);

                $facturas = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "facturas" => $facturas?$facturas:array());

    }

    /**
     * Funcion que devuelve los recibos de las facturas de una entidad
     * a partir de una fecha de actualizacion y desde una fecha determinada.
     * El parametro state establece el estado de los albranes a devolver. Estado >= $state
     * El parametro $onlyPdtes establece si tiene que devolver las facturas pendientes de cobro o todas.
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $entityId
     * @param $pagination
     * @param $state
     * @param $lastTimeStamp
     * @return array|null
     */
    function getMultipartCachedRecibosFacturasFromEntity($entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $recibos = unserialize($this->esocialmemcache->get($key));
            if (!$recibos) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
            $this->esocialmemcache->delete($key);
        } else {
            $query = $this->getEntityRecibosQuery($entityId, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $recibos = $query->result('recibo_cobro');

            $rowcount = sizeof($recibos);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($recibos, $pagination->pageSize);

                $recibos = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "recibos" => $recibos?$recibos:array());

    }

    /**
     * Funcion que devuelve los recibos de las facturas de los clientes asociados al usuario.
     * a partir de una fecha de actualizacion y desde una fecha determinada.
     * El parametro state establece el estado de los albranes a devolver. Estado >= $state
     * El parametro $onlyPdtes establece si tiene que devolver las facturas pendientes de cobro o todas.
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $userPk
     * @param $entityId
     * @param $pagination
     * @param $state
     * @param $lastTimeStamp
     * @return array|null
     */
    function getMultipartCachedRecibosFacturas($userPk, $entityId, $pagination, $state, $lastTimeStamp) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $recibos = unserialize($this->esocialmemcache->get($key));
            if (!$recibos) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
            $this->esocialmemcache->delete($key);
        } else {
            $query = $this->getAssignedRecibosQuery($entityId, $userPk, $state, $lastTimeStamp);

            $query = $this->db->query($query);

            $recibos = $query->result('recibo_cobro');

            $rowcount = sizeof($recibos);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($recibos, $pagination->pageSize);

                $recibos = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "recibos" => $recibos?$recibos:array());

    }

    /**
     * Funcion que se encarga de guardar una factura y sus lineas.
     * Para cada linea comprueba que no existe ya buscando por el token.
     *
     * @param $factura
     * @return bool
     * @throws APIexception
     */
    function saveFactura($factura) {
        //$this->db->trans_start(); Las transacciones las hacemos en el controlador ya que hay un bug y no funcionan las transacciones anidadas
        $this->load->model("usuario_model");
        $this->load->model("log_model");
        $this->load->model("articulos/articulo_almacen_model");
        if (!isset($factura->token)) {
            $factura->token = getToken();
        }

        $result = $factura->_save(false, false);

        if ($result) {
            if (isset($factura->facturaLines)) {
                $facturaLines = $factura->facturaLines;
                foreach ($facturaLines as $line) {
                    $line->fk_factura = $factura->pk_factura;
                    $line->fk_entidad = $factura->fk_entidad;
                    $facturaLine = 0;
                    //Nos aseguramos que los Tokens no existen
                    if ($line->id_factura_lin == null && isset($line->token)) {
                        $query = new stdClass();
                        $this->db->where('token', $line->token);
                        $this->db->where('fk_entidad', $factura->fk_entidad);
                        $query = $this->db->get("facturas_lin");
                        $facturaLine = $query->row();
                        if ($facturaLine) $line->id_factura_lin = $facturaLine->id_factura_lin;
                    }
                    //Comprobamos si tenemos que descontar el stock
                    if ($line->modif_stock && $factura->bool_origen_albaran == 0) {
                        //Verificamos si es una modificacion de la linea
                        if ($facturaLine) {
                            $incStock = $line->cantidad - $facturaLine->cantidad;
                        } else {
                            $incStock = $line->cantidad * (-1);
                        }
                        $pk_articulo = $line->cod_concepto . "_" . $factura->fk_entidad; //Aplicamos Convencion
                        $res = $this->articulo_almacen_model->addStockByArtAndAlmacen($factura->fk_entidad, $pk_articulo, $factura->fk_almacen, "Factura", $incStock, null, $factura->pk_factura);

                        if (!$res) throw new APIexception("Error on factura_model->saveFactura. Unable to update UPDATE STOCKS", ERROR_SAVING_DATA, serialize($factura));
                    }

                    if (!isset($line->token)) {
                        $line->token = getToken();
                    }
                    $res = $line->_save(false, true);
                    if (!$res) throw new APIexception("Error on factura_model->saveFactura. Unable to update Factura Line", ERROR_SAVING_DATA, serialize($factura));
                }
            }
            //$this->db->trans_complete();			
            return true;
        } else {
            throw new APIexception("Error on factura_model->saveFactura. Unable to update Factura", ERROR_SAVING_DATA, serialize($factura));
        }

    }

    /**
     * Funcion que guarda el recibo en la bbdd
     *
     * @param $recibo_cobro
     * @return bool
     * @throws APIexception
     */
    function saveRecibo($recibo_cobro) {
        $this->load->model("log_model");

        if (!isset($recibo_cobro->token)) {
            $recibo_cobro->token = getToken();
        }

        $result = $recibo_cobro->_save(false, true);

        if ($result) {
            return true;
        } else {
            throw new APIexception("Error on factura_model->saveRecibo. Unable to update recibo.", ERROR_SAVING_DATA, serialize($recibo_cobro));
        }
    }

    /**
     * @param $entityPk
     *
     * Coge el siguiente numero de pedido de la serie predeterminada y actualiza la tabla
     */
    function getSerieForNewCode($entityPk) {

        $this->db->trans_start();

        $this->db->where('fk_entidad', $entityPk);
        $this->db->where('bool_predeterminada', 1);
        $this->db->where('anio = YEAR(NOW())');
        $query = $this->db->get('series');

        $result = $query->row(0, 'serie');

        if ($result) {
            $q = new stdClass();
            $q->num_factura = $result->num_factura + 1;
            $this->db->where('fk_entidad', $entityPk);
            $this->db->where('bool_predeterminada', 1);
            $this->db->where('anio = YEAR(NOW())');
            $this->db->where('serie', $result->serie);


            $this->db->update('series', $q);

            $this->db->trans_complete();

            $result->num_factura =  $result->num_factura + 1;
            return $result;

        } else {
            return null;
        }

    }


    /**
     * @param $entityId
     * @param $field
     * @param $query
     * @param $return
     * @param type
     * @return Array(Value, Description)
     */
    function search($entityId, $field, $query, $return=null, $type='text') {
        if (!$return) {
            $return = 'pk_factura';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('facturas_cab');
        $this->db->where('fk_entidad', $entityId);

        if ($type == 'text')
            $this->db->like($field, $query);
        else
            $this->db->where($field, $query);

        $this->db->limit(10);
        $query = $this->db->get();

        $result = $query->result();
        return $result?$result:array();
    }

}

?>