<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_WAYBILL);
require_once(APPPATH.ENTITY_WAYBILL_LINE);


class albaran_model extends CI_Model {

    /**
     * ----------------------------------------
     *            FUNCIONES DE APOYO
     * ----------------------------------------
     */
    private function getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp, $fromDate) {

        //Cogemos los albaranes con estado_picking > 0 => Albaranes que no estan pendientes.
        $q = " SELECT DISTINCT albaranes.pk_albaran, albaranes.fk_entidad, albaranes.fk_cliente, albaranes.fk_usuario_entidad, albaranes.serie, albaranes.anio, albaranes.fk_serie_entidad, albaranes.fk_factura_destino, albaranes.fk_almacen, albaranes.fk_delegacion,
                albaranes.fk_terminal_tpv, albaranes.fk_forma_pago, albaranes.fk_condicion_pago, albaranes.cod_albaran, albaranes.cod_cliente, albaranes.cod_usuario_entidad,
                albaranes.num_serie, albaranes.cod_almacen, albaranes.cod_delegacion, albaranes.cod_terminal_tpv, albaranes.bool_actualiza_numeracion, albaranes.bool_recalcular, albaranes.fecha, albaranes.raz_social, albaranes.nif, albaranes.direccion,
                albaranes.poblacion, albaranes.provincia, albaranes.codpostal, albaranes.base_imponible_tot, albaranes.imp_desc_tot, albaranes.imp_iva_tot, albaranes.token AS tokenAlb, albaranes.imp_re_tot, albaranes.imp_total, albaranes.observaciones, albaranes.cod_forma_pago,
                albaranes.cod_condicion_pago, albaranes.varios1, albaranes.varios2, albaranes.varios3, albaranes.varios4, albaranes.varios5, albaranes.varios6, albaranes.varios7, albaranes.varios8, albaranes.varios9, albaranes.varios10, albaranes.estado AS estadoAlb, albaranes.tipo_pedido,
                albaranes.fk_repartidor, albaranes.fk_repartidor_reasignado, albaranes.picking_fecha, albaranes.picking_hora, albaranes.picking_estado, albaranes.updated_repartidor_at, albaranes.fecha_entrega, albaranes.hora_entrega, albaranes.token_visita, albaranes.token_archivo, albaranes.bool_entregado,

                albaranes_lin.id_albaran_lin, albaranes_lin.fk_albaran_cab, albaranes_lin.fk_usuario, albaranes_lin.fk_articulo, albaranes_lin.fk_tarifa, albaranes_lin.cod_usuario_entidad, albaranes_lin.cod_concepto, albaranes_lin.concepto, albaranes_lin.cantidad, albaranes_lin.precio, albaranes_lin.precio_original, albaranes_lin.base_imponible, albaranes_lin.descuento, albaranes_lin.imp_descuento,
                albaranes_lin.iva, albaranes_lin.imp_iva, albaranes_lin.re, albaranes_lin.imp_re, albaranes_lin.total_lin, albaranes_lin.varios1 AS varios1Lin, albaranes_lin.varios2 AS varios2Lin, albaranes_lin.varios3 AS varios3Lin, albaranes_lin.varios4 AS varios4Lin, albaranes_lin.varios5 AS varios5Lin,
                albaranes_lin.varios6 AS varios6Lin, albaranes_lin.varios7 AS varios7Lin, albaranes_lin.varios8 AS varios8Lin, albaranes_lin.varios9 AS varios9Lin, albaranes_lin.varios10 AS varios10Lin, albaranes_lin.estado AS estadoLin, albaranes_lin.token AS tokenLin, albaranes_lin.modif_stock,
                albaranes_lin.desc_promocion, albaranes_lin.imp_promocion, albaranes_lin.fk_promocion, albaranes_lin.cod_camp, albaranes_lin.precio_punto_verde, albaranes_lin.coste_medio, albaranes_lin.lote, albaranes_lin.bool_precio_neto

             FROM clientes
             LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = ".$entityId." AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
             LEFT JOIN visita ON visita.fk_entidad = ".$entityId." AND clientes.pk_cliente = visita.fk_cliente AND fk_vendedor_reasignado = '".$userPk."' AND visita.estado > 0
             LEFT JOIN albaranes_cab ON albaranes_cab.fk_entidad = ".$entityId." AND clientes.pk_cliente = albaranes_cab.fk_cliente AND (fk_repartidor = '".$userPk."' OR fk_repartidor_reasignado = '".$userPk."') AND albaranes_cab.estado > 0 AND albaranes_cab.fecha_entrega >= CURDATE() AND albaranes_cab.updated_at > '".$lastTimeStamp."'
             JOIN albaranes_cab albaranes ON albaranes_cab.fk_entidad = ".$entityId." AND albaranes.fk_cliente = clientes.pk_cliente AND albaranes.estado >= ".$state."
             JOIN albaranes_lin ON albaranes_lin.fk_albaran_cab = albaranes.pk_albaran AND albaranes_lin.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId."
             AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  OR visita.id IS NOT NULL  OR (albaranes_cab.pk_albaran IS NOT NULL AND bool_asignacion_generica = 0 AND r_usu_cli.pk_usuario_cliente IS NULL) )
             AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR albaranes_cab.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR visita.updated_vendedor_at > '".$lastTimeStamp."' OR albaranes.updated_at > '".$lastTimeStamp."' OR albaranes_lin.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0
             AND albaranes.picking_estado > 0
             AND albaranes.fecha >= '".$fromDate."'";

        return $q;
    }

    private function getEntityQuery($entityId, $state, $lastTimeStamp, $fromDate) {

        //Cogemos los albaranes con estado_picking > 0 => Albaranes que no estan pendientes.
        $q = " SELECT DISTINCT albaranes.pk_albaran, albaranes.fk_entidad, albaranes.fk_cliente, albaranes.fk_usuario_entidad, albaranes.serie, albaranes.anio, albaranes.fk_serie_entidad, albaranes.fk_factura_destino, albaranes.fk_almacen, albaranes.fk_delegacion,
                albaranes.fk_terminal_tpv, albaranes.fk_forma_pago, albaranes.fk_condicion_pago, albaranes.cod_albaran, albaranes.cod_cliente, albaranes.cod_usuario_entidad,
                albaranes.num_serie, albaranes.cod_almacen, albaranes.cod_delegacion, albaranes.cod_terminal_tpv, albaranes.bool_actualiza_numeracion, albaranes.bool_recalcular, albaranes.fecha, albaranes.raz_social, albaranes.nif, albaranes.direccion,
                albaranes.poblacion, albaranes.provincia, albaranes.codpostal, albaranes.base_imponible_tot, albaranes.imp_desc_tot, albaranes.imp_iva_tot, albaranes.token AS tokenAlb, albaranes.imp_re_tot, albaranes.imp_total, albaranes.observaciones, albaranes.cod_forma_pago,
                albaranes.cod_condicion_pago, albaranes.varios1, albaranes.varios2, albaranes.varios3, albaranes.varios4, albaranes.varios5, albaranes.varios6, albaranes.varios7, albaranes.varios8, albaranes.varios9, albaranes.varios10, albaranes.estado AS estadoAlb, albaranes.tipo_pedido,
                albaranes.fk_repartidor, albaranes.fk_repartidor_reasignado, albaranes.picking_fecha, albaranes.picking_hora, albaranes.picking_estado, albaranes.updated_repartidor_at, albaranes.fecha_entrega, albaranes.hora_entrega, albaranes.token_visita, albaranes.token_archivo, albaranes.bool_entregado,

                albaranes_lin.id_albaran_lin, albaranes_lin.fk_albaran_cab, albaranes_lin.fk_usuario, albaranes_lin.fk_articulo, albaranes_lin.fk_tarifa, albaranes_lin.cod_usuario_entidad, albaranes_lin.cod_concepto, albaranes_lin.concepto, albaranes_lin.cantidad, albaranes_lin.precio, albaranes_lin.precio_original, albaranes_lin.base_imponible, albaranes_lin.descuento, albaranes_lin.imp_descuento,
                albaranes_lin.iva, albaranes_lin.imp_iva, albaranes_lin.re, albaranes_lin.imp_re, albaranes_lin.total_lin, albaranes_lin.varios1 AS varios1Lin, albaranes_lin.varios2 AS varios2Lin, albaranes_lin.varios3 AS varios3Lin, albaranes_lin.varios4 AS varios4Lin, albaranes_lin.varios5 AS varios5Lin,
                albaranes_lin.varios6 AS varios6Lin, albaranes_lin.varios7 AS varios7Lin, albaranes_lin.varios8 AS varios8Lin, albaranes_lin.varios9 AS varios9Lin, albaranes_lin.varios10 AS varios10Lin, albaranes_lin.estado AS estadoLin, albaranes_lin.token AS tokenLin, albaranes_lin.modif_stock,
                albaranes_lin.desc_promocion, albaranes_lin.imp_promocion, albaranes_lin.fk_promocion, albaranes_lin.cod_camp, albaranes_lin.precio_punto_verde, albaranes_lin.coste_medio, albaranes_lin.lote, albaranes_lin.bool_precio_neto

             FROM albaranes_cab albaranes
             JOIN albaranes_lin ON albaranes.fk_entidad = albaranes_lin.fk_entidad AND albaranes_lin.fk_albaran_cab = albaranes.pk_albaran AND albaranes_lin.estado >= ".$state."
             WHERE albaranes.fk_entidad = ".$entityId."
             AND (albaranes.updated_at > '".$lastTimeStamp."' OR albaranes_lin.updated_at > '".$lastTimeStamp."' )
             AND albaranes.estado >= ".$state."
             AND albaranes.fecha >= '".$fromDate."'";

        return $q;
    }

    private function getEntityQueryByProveedor($entityId, $state, $lastTimeStamp, $fromDate, $proveedores) {
        $sql_prov = "";
        $firstProv = 1;
        foreach ($proveedores as $value) {

            if ($firstProv) {
                $sql_prov .= "'".$value."'";
                $firstProv = 0;
            } else {
                $sql_prov .= ",'".$value."'";
            }

        }

        $q = " SELECT DISTINCT albaranes.pk_albaran, albaranes.fk_entidad, albaranes.fk_cliente, albaranes.fk_usuario_entidad, albaranes.serie, albaranes.anio, albaranes.fk_serie_entidad, albaranes.fk_factura_destino, albaranes.fk_almacen, albaranes.fk_delegacion,
                albaranes.fk_terminal_tpv, albaranes.fk_forma_pago, albaranes.fk_condicion_pago, albaranes.cod_albaran, albaranes.cod_cliente, albaranes.cod_usuario_entidad,
                albaranes.num_serie, albaranes.cod_almacen, albaranes.cod_delegacion, albaranes.cod_terminal_tpv, albaranes.bool_actualiza_numeracion, albaranes.bool_recalcular, albaranes.fecha, albaranes.raz_social, albaranes.nif, albaranes.direccion,
                albaranes.poblacion, albaranes.provincia, albaranes.codpostal, albaranes.base_imponible_tot, albaranes.imp_desc_tot, albaranes.imp_iva_tot, albaranes.token AS tokenAlb, albaranes.imp_re_tot, albaranes.imp_total, albaranes.observaciones, albaranes.cod_forma_pago,
                albaranes.cod_condicion_pago, albaranes.varios1, albaranes.varios2, albaranes.varios3, albaranes.varios4, albaranes.varios5, albaranes.varios6, albaranes.varios7, albaranes.varios8, albaranes.varios9, albaranes.varios10, albaranes.estado AS estadoAlb, albaranes.tipo_pedido,
                albaranes.fk_repartidor, albaranes.fk_repartidor_reasignado, albaranes.picking_fecha, albaranes.picking_hora, albaranes.picking_estado, albaranes.updated_repartidor_at, albaranes.fecha_entrega, albaranes.hora_entrega, albaranes.token_visita, albaranes.token_archivo, albaranes.bool_entregado,

                albaranes_lin.id_albaran_lin, albaranes_lin.fk_albaran_cab, albaranes_lin.fk_usuario, albaranes_lin.fk_articulo, albaranes_lin.fk_tarifa, albaranes_lin.cod_usuario_entidad, albaranes_lin.cod_concepto, albaranes_lin.concepto, albaranes_lin.cantidad, albaranes_lin.precio, albaranes_lin.precio_original, albaranes_lin.base_imponible, albaranes_lin.descuento, albaranes_lin.imp_descuento,
                albaranes_lin.iva, albaranes_lin.imp_iva, albaranes_lin.re, albaranes_lin.imp_re, albaranes_lin.total_lin, albaranes_lin.varios1 AS varios1Lin, albaranes_lin.varios2 AS varios2Lin, albaranes_lin.varios3 AS varios3Lin, albaranes_lin.varios4 AS varios4Lin, albaranes_lin.varios5 AS varios5Lin,
                albaranes_lin.varios6 AS varios6Lin, albaranes_lin.varios7 AS varios7Lin, albaranes_lin.varios8 AS varios8Lin, albaranes_lin.varios9 AS varios9Lin, albaranes_lin.varios10 AS varios10Lin, albaranes_lin.estado AS estadoLin, albaranes_lin.token AS tokenLin, albaranes_lin.modif_stock,
                albaranes_lin.desc_promocion, albaranes_lin.imp_promocion, albaranes_lin.fk_promocion, albaranes_lin.cod_camp, albaranes_lin.precio_punto_verde, albaranes_lin.coste_medio, albaranes_lin.lote, albaranes_lin.bool_precio_neto,

                r_art_pro.cod_art_prov

             FROM albaranes_cab albaranes
             JOIN albaranes_lin ON albaranes.fk_entidad = albaranes_lin.fk_entidad AND albaranes_lin.fk_albaran_cab = albaranes.pk_albaran AND albaranes_lin.estado >= ".$state."
             JOIN r_art_pro ON r_art_pro.fk_entidad = albaranes.fk_entidad AND r_art_pro.fk_articulo = albaranes_lin.fk_articulo AND r_art_pro.estado >= ".$state."
             WHERE albaranes.fk_entidad = ".$entityId."
             AND (albaranes.updated_at > '".$lastTimeStamp."' OR albaranes_lin.updated_at > '".$lastTimeStamp."' OR r_art_pro.updated_at > '".$lastTimeStamp."' )
             AND albaranes.estado >= ".$state."
             AND albaranes.fecha >= '".$fromDate."'";


        //Si hay proveedores añadimos la condicion
        if (!$firstProv) $q .= " AND r_art_pro.fk_proveedor IN (".$sql_prov.")";
        $q .= " ORDER BY pk_albaran";
        return $q;
    }

    private function getByFechaEntregaQuery($entityId, $fechaEntrega) {

        //Cogemos los albaranes con estado_picking > 0 => Albaranes que no estan pendientes.
        $q = " SELECT DISTINCT albaranes.pk_albaran, albaranes.fk_entidad, albaranes.fk_cliente, albaranes.fk_usuario_entidad, albaranes.serie, albaranes.anio, albaranes.fk_serie_entidad, albaranes.fk_factura_destino, albaranes.fk_almacen, albaranes.fk_delegacion,
                albaranes.fk_terminal_tpv, albaranes.fk_forma_pago, albaranes.fk_condicion_pago, albaranes.cod_albaran, albaranes.cod_cliente, albaranes.cod_usuario_entidad,
                albaranes.num_serie, albaranes.cod_almacen, albaranes.cod_delegacion, albaranes.cod_terminal_tpv, albaranes.bool_actualiza_numeracion, albaranes.bool_recalcular, albaranes.fecha, albaranes.raz_social, albaranes.nif, albaranes.direccion,
                albaranes.poblacion, albaranes.provincia, albaranes.codpostal, albaranes.base_imponible_tot, albaranes.imp_desc_tot, albaranes.imp_iva_tot, albaranes.token AS tokenAlb, albaranes.imp_re_tot, albaranes.imp_total, albaranes.observaciones, albaranes.cod_forma_pago,
                albaranes.cod_condicion_pago, albaranes.varios1, albaranes.varios2, albaranes.varios3, albaranes.varios4, albaranes.varios5, albaranes.varios6, albaranes.varios7, albaranes.varios8, albaranes.varios9, albaranes.varios10, albaranes.estado AS estadoAlb, albaranes.tipo_pedido,
                albaranes.fk_repartidor, albaranes.fk_repartidor_reasignado, albaranes.picking_fecha, albaranes.picking_hora, albaranes.picking_estado, albaranes.updated_repartidor_at, albaranes.fecha_entrega, albaranes.hora_entrega, albaranes.token_visita, albaranes.token_archivo, albaranes.bool_entregado,

                albaranes_lin.id_albaran_lin, albaranes_lin.fk_albaran_cab, albaranes_lin.fk_usuario, albaranes_lin.fk_articulo, albaranes_lin.fk_tarifa, albaranes_lin.cod_usuario_entidad AS cod_usu_lin, albaranes_lin.cod_concepto, albaranes_lin.concepto, albaranes_lin.cantidad, albaranes_lin.precio, albaranes_lin.precio_original, albaranes_lin.base_imponible, albaranes_lin.descuento, albaranes_lin.imp_descuento,
                albaranes_lin.iva, albaranes_lin.imp_iva, albaranes_lin.re, albaranes_lin.imp_re, albaranes_lin.total_lin, albaranes_lin.varios1 AS varios1Lin, albaranes_lin.varios2 AS varios2Lin, albaranes_lin.varios3 AS varios3Lin, albaranes_lin.varios4 AS varios4Lin, albaranes_lin.varios5 AS varios5Lin,
                albaranes_lin.varios6 AS varios6Lin, albaranes_lin.varios7 AS varios7Lin, albaranes_lin.varios8 AS varios8Lin, albaranes_lin.varios9 AS varios9Lin, albaranes_lin.varios10 AS varios10Lin, albaranes_lin.estado AS estadoLin, albaranes_lin.token AS tokenLin, albaranes_lin.modif_stock,
                albaranes_lin.desc_promocion, albaranes_lin.imp_promocion, albaranes_lin.fk_promocion, albaranes_lin.cod_camp, albaranes_lin.precio_punto_verde, albaranes_lin.coste_medio, albaranes_lin.lote, albaranes_lin.bool_precio_neto

             FROM albaranes_cab albaranes
             JOIN albaranes_lin ON albaranes_lin.fk_albaran_cab = albaranes.pk_albaran AND albaranes_lin.estado = 1
             WHERE albaranes.fk_entidad = ".$entityId."
             AND albaranes.fecha_entrega = '".$fechaEntrega."'
             AND albaranes.estado >= 1";

        return $q;
    }

    private function getByFechaPickingQuery($entityId, $fechaPicking) {

        //Cogemos los albaranes con estado_picking > 0 => Albaranes que no estan pendientes.
        $q = " SELECT DISTINCT albaranes.pk_albaran, albaranes.fk_entidad, albaranes.fk_cliente, albaranes.fk_usuario_entidad, albaranes.serie, albaranes.anio, albaranes.fk_serie_entidad, albaranes.fk_factura_destino, albaranes.fk_almacen, albaranes.fk_delegacion,
                albaranes.fk_terminal_tpv, albaranes.fk_forma_pago, albaranes.fk_condicion_pago, albaranes.cod_albaran, albaranes.cod_cliente, albaranes.cod_usuario_entidad,
                albaranes.num_serie, albaranes.cod_almacen, albaranes.cod_delegacion, albaranes.cod_terminal_tpv, albaranes.bool_actualiza_numeracion, albaranes.bool_recalcular, albaranes.fecha, albaranes.raz_social, albaranes.nif, albaranes.direccion,
                albaranes.poblacion, albaranes.provincia, albaranes.codpostal, albaranes.base_imponible_tot, albaranes.imp_desc_tot, albaranes.imp_iva_tot, albaranes.token AS tokenAlb, albaranes.imp_re_tot, albaranes.imp_total, albaranes.observaciones, albaranes.cod_forma_pago,
                albaranes.cod_condicion_pago, albaranes.varios1, albaranes.varios2, albaranes.varios3, albaranes.varios4, albaranes.varios5, albaranes.varios6, albaranes.varios7, albaranes.varios8, albaranes.varios9, albaranes.varios10, albaranes.estado AS estadoAlb,
                albaranes.fk_repartidor, albaranes.fk_repartidor_reasignado, albaranes.picking_fecha, albaranes.picking_hora, albaranes.picking_estado, albaranes.updated_repartidor_at, albaranes.fecha_entrega, albaranes.hora_entrega, albaranes.token_visita, albaranes.token_archivo, albaranes.bool_entregado,

                albaranes_lin.id_albaran_lin, albaranes_lin.fk_albaran_cab, albaranes_lin.fk_usuario, albaranes_lin.fk_articulo, albaranes_lin.fk_tarifa, albaranes_lin.cod_usuario_entidad, albaranes_lin.cod_concepto, albaranes_lin.concepto, albaranes_lin.cantidad, albaranes_lin.precio, albaranes_lin.precio_original, albaranes_lin.base_imponible, albaranes_lin.descuento, albaranes_lin.imp_descuento,
                albaranes_lin.iva, albaranes_lin.imp_iva, albaranes_lin.re, albaranes_lin.imp_re, albaranes_lin.total_lin, albaranes_lin.varios1 AS varios1Lin, albaranes_lin.varios2 AS varios2Lin, albaranes_lin.varios3 AS varios3Lin, albaranes_lin.varios4 AS varios4Lin, albaranes_lin.varios5 AS varios5Lin,
                albaranes_lin.varios6 AS varios6Lin, albaranes_lin.varios7 AS varios7Lin, albaranes_lin.varios8 AS varios8Lin, albaranes_lin.varios9 AS varios9Lin, albaranes_lin.varios10 AS varios10Lin, albaranes_lin.estado AS estadoLin, albaranes_lin.token AS tokenLin, albaranes_lin.modif_stock,
                albaranes_lin.desc_promocion, albaranes_lin.imp_promocion, albaranes_lin.fk_promocion, albaranes_lin.cod_camp, albaranes_lin.precio_punto_verde, albaranes_lin.coste_medio, albaranes_lin.lote, albaranes_lin.bool_precio_neto

             FROM albaranes_cab albaranes
             JOIN albaranes_lin ON albaranes_lin.fk_albaran_cab = albaranes.pk_albaran AND albaranes_lin.estado = 1
             WHERE albaranes.fk_entidad = ".$entityId."
             AND albaranes.picking_fecha = '".$fechaPicking."'
             AND albaranes.estado >= 1";

        return $q;
    }

    private function getByClienteQuery($entityId, $clientePk, $fromDate, $lastTimeStamp, $state) {

        //Cogemos los albaranes con estado_picking > 0 => Albaranes que no estan pendientes.
        $q = " SELECT DISTINCT albaranes.pk_albaran, albaranes.fk_entidad, albaranes.fk_cliente, albaranes.fk_usuario_entidad, albaranes.serie, albaranes.anio, albaranes.fk_serie_entidad, albaranes.fk_factura_destino, albaranes.fk_almacen, albaranes.fk_delegacion,
                albaranes.fk_terminal_tpv, albaranes.fk_forma_pago, albaranes.fk_condicion_pago, albaranes.cod_albaran, albaranes.cod_cliente, albaranes.cod_usuario_entidad,
                albaranes.num_serie, albaranes.cod_almacen, albaranes.cod_delegacion, albaranes.cod_terminal_tpv, albaranes.bool_actualiza_numeracion, albaranes.bool_recalcular, albaranes.fecha, albaranes.raz_social, albaranes.nif, albaranes.direccion,
                albaranes.poblacion, albaranes.provincia, albaranes.codpostal, albaranes.base_imponible_tot, albaranes.imp_desc_tot, albaranes.imp_iva_tot, albaranes.token AS tokenAlb, albaranes.imp_re_tot, albaranes.imp_total, albaranes.observaciones, albaranes.cod_forma_pago,
                albaranes.cod_condicion_pago, albaranes.varios1, albaranes.varios2, albaranes.varios3, albaranes.varios4, albaranes.varios5, albaranes.varios6, albaranes.varios7, albaranes.varios8, albaranes.varios9, albaranes.varios10, albaranes.estado AS estadoAlb, albaranes.tipo_pedido,
                albaranes.fk_repartidor, albaranes.fk_repartidor_reasignado, albaranes.picking_fecha, albaranes.picking_hora, albaranes.picking_estado, albaranes.updated_repartidor_at, albaranes.fecha_entrega, albaranes.hora_entrega, albaranes.token_visita, albaranes.token_archivo, albaranes.bool_entregado,

                albaranes_lin.id_albaran_lin, albaranes_lin.fk_albaran_cab, albaranes_lin.fk_usuario, albaranes_lin.fk_articulo, albaranes_lin.fk_tarifa, albaranes_lin.cod_usuario_entidad AS cod_usu_lin, albaranes_lin.cod_concepto, albaranes_lin.concepto, albaranes_lin.cantidad, albaranes_lin.precio, albaranes_lin.precio_original, albaranes_lin.base_imponible, albaranes_lin.descuento, albaranes_lin.imp_descuento,
                albaranes_lin.iva, albaranes_lin.imp_iva, albaranes_lin.re, albaranes_lin.imp_re, albaranes_lin.total_lin, albaranes_lin.varios1 AS varios1Lin, albaranes_lin.varios2 AS varios2Lin, albaranes_lin.varios3 AS varios3Lin, albaranes_lin.varios4 AS varios4Lin, albaranes_lin.varios5 AS varios5Lin,
                albaranes_lin.varios6 AS varios6Lin, albaranes_lin.varios7 AS varios7Lin, albaranes_lin.varios8 AS varios8Lin, albaranes_lin.varios9 AS varios9Lin, albaranes_lin.varios10 AS varios10Lin, albaranes_lin.estado AS estadoLin, albaranes_lin.token AS tokenLin, albaranes_lin.modif_stock,
                albaranes_lin.desc_promocion, albaranes_lin.imp_promocion, albaranes_lin.fk_promocion, albaranes_lin.cod_camp, albaranes_lin.precio_punto_verde, albaranes_lin.coste_medio, albaranes_lin.lote, albaranes_lin.bool_precio_neto

             FROM albaranes_cab albaranes
             JOIN albaranes_lin ON albaranes_lin.fk_albaran_cab = albaranes.pk_albaran AND albaranes_lin.estado >= $state
             WHERE albaranes.fk_entidad = ".$entityId."
             AND albaranes.fk_cliente = '".$clientePk."'
             AND (albaranes_cab.updated_at > '".$lastTimeStamp."' OR albaranes_lin.updated_at > '".$lastTimeStamp."')
             AND albaranes_cab.fecha >= '$fromDate'
             AND albaranes.estado >= $state";

        return $q;
    }

    private function getListQuery($entityId, $clientePk, $entregado, $offset, $limit, $order, $sort) {
        $q = "SELECT * FROM albaranes_cab WHERE estado > 0 AND fk_entidad = ".$entityId;

        if ($entregado === 0 || $entregado == 1) $q .= " AND bool_entregado = ".$entregado;
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
        $q = "SELECT MONTH(fecha) AS mes, COUNT(*) AS num_reg, ROUND(SUM(base_imponible), 2) AS base_imponible FROM albaranes_cab cab
                JOIN albaranes_lin lin ON lin.fk_entidad = cab.fk_entidad AND fk_albaran_cab = pk_albaran AND lin.estado > 0
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
        $q = "SELECT DAY(fecha) AS dia, COUNT(*) AS num_reg, ROUND(SUM(base_imponible), 2) AS base_imponible FROM albaranes_cab cab
                JOIN albaranes_lin lin ON lin.fk_entidad = cab.fk_entidad AND fk_albaran_cab = pk_albaran AND lin.estado > 0
                WHERE cab.fk_entidad = $entityId AND cab.estado > 0 AND YEAR(fecha) = $year AND MONTH(fecha) = $month
                GROUP BY DAY(fecha)";

        $query = $this->db->query($q);

        $result = $query->result();

        return array("resumen" => $result?$result:array());

        return $q;
    }

    /**
     * Funcion que devuelve un listado de albaranes.
     *
     * @param $entityId
     * @param $clientePk (opcional)
     * @param $offset (opcional)
     * @param $limit (opcional)
     * @param $order (opcional)
     *
     */
    function listAlbaranes($entityId, $clientePk, $entregado, $offset, $limit, $order, $sort) {
        $query = $this->getListQuery($entityId, $clientePk, $entregado, $offset, $limit, $order, $sort);

        $query = $this->db->query($query);

        $albaranes = $query->result('albaran');

        return array("albaranes" => $albaranes?$albaranes:array());

    }

    /**
     * Funcion que devuelve un listado de lineas de un cliente desde una fecha.
     *
     * @param $entityId
     * @param $fromDate
     * @param $entregado (opcional)
     * @param $clientePk (opcional)
     * @param $codigo (opcional)
     * @param $descripcion (opcional)
     * @param $offset (opcional)
     * @param $limit (opcional)
     * @param $order (opcional)
     * @param $sort (opcional)
     *
     */
    function listLines($entityId, $clientePk, $entregado, $fromDate, $codigo, $descripcion, $offset, $limit, $order, $sort) {
        $q = "SELECT lin.*, fecha FROM albaranes_cab cab
                JOIN albaranes_lin lin ON lin.fk_entidad = cab.fk_entidad AND lin.fk_albaran_cab = cab.pk_albaran AND lin.estado > 0
                WHERE cab.estado > 0 AND cab.fk_entidad = $entityId AND fecha > '$fromDate' AND fk_cliente = '$clientePk'";

        if ($entregado === 0 || $entregado == 1) $q .= " AND bool_entregado = ".$entregado;
        if ($codigo) $q .= " AND cod_concepto LIKE '%$codigo%'";
        if ($descripcion) $q .= " AND concepto LIKE '%$descripcion%'";


        if ($order) {
            $q .= " ORDER BY ".$order;
            if ($sort == "DESC") $q .= " DESC ";
            else $q .= " ASC ";
        }

        if ($limit) {
            $q .= " LIMIT ".$limit;
            if ($offset) $q .= " OFFSET ".$offset;
        }

        $query = $this->db->query($q);

        $result = $query->result('albaranLine');

        return array("lines" => $result?$result:array());

    }


    /**
     * Funcion que devuelve un albaran a partir de su Token
     *
     * @param $token
     * @param $entityId
     * @return albaran
     */
    function getAlbaranByToken($token, $entityId) {

        //CABECERA
        $this->db->where('albaranes_cab.token', $token);
        $this->db->where('albaranes_cab.fk_entidad', $entityId);
        $query = $this->db->get('albaranes_cab');

        $albaran = $query->row(0, 'albaran');

        //LINEAS
        if ($albaran) {
            $this->db->where('fk_albaran_cab', $albaran->pk_albaran);
            $query = $this->db->get('albaranes_lin');
            $albaranLines = $query->result('albaranLine');

            $albaran->albaranLines = $albaranLines;
        }

        return $albaran;

    }

    /**
     * Funcion que devuelve una linea de albaran a partir de su Token
     *
     * @param $token
     * @param $entityId
     * @return albaranLinea
     */
    function getLineaAlbaranByToken($token, $entityId) {

        //CABECERA
        $this->db->where('albaranes_lin.token', $token);
        $this->db->where('albaranes_lin.fk_entidad', $entityId);
        $query = $this->db->get('albaranes_lin');

        $linea = $query->row(0, 'albaranLine');


        return $linea;

    }

    /**
     * Funcion que devuelve una linea de albaran a partir de su pk
     *
     * @param $lineaPk
     * @param $entityId
     * @return albaranLinea
     */
    function getLineaAlbaran($lineaPk, $entityId) {

        //CABECERA
        $this->db->where('albaranes_lin.id_albaran_lin', $lineaPk);
        $this->db->where('albaranes_lin.fk_entidad', $entityId);
        $query = $this->db->get('albaranes_lin');

        $linea = $query->row(0, 'albaranLine');


        return $linea;

    }

    /**
     * Funcion que devuelve los albaranes de un cliente
     *
     * @param $entityId
     * @param $clientePk
     * @param $fromDate
     * @param $lastTimeStamp
     * @param $state
     * @return array(albaranes)
     *
     */
    function getByCliente($entityId, $clientePk, $fromDate, $lastTimeStamp, $state) {

        $query = $this->getByClienteQuery($entityId, $clientePk, $fromDate, $lastTimeStamp, $state);

        $query = $this->db->query($query);

        $albs = $query->result();

        $albaranes = array();
        $lins = array();
        $lastAlb = "";
        $alb = null;
        for ($i=0; $i<count($albs); $i++) {
            if ($lastAlb != $albs[$i]->pk_albaran) {
                if ($alb) {
                    $alb->albaranLines = $lins;
                    $albaranes[] = $alb;
                }
                $alb = new albaran();
                $alb->set($albs[$i]);
                //Asignamos el token y estado que lo hemos renombrado en la consulta
                $alb->estado = $albs[$i]->estadoAlb;
                $alb->token = $albs[$i]->tokenAlb;
                $lins = array();

                $lastAlb = $alb->pk_albaran;
            }
            // Linea de Pedido
            $lin = new albaranLine();
            $lin->set($albs[$i]);
            //Asignamos los campos renombrados
            $lin->estado = $albs[$i]->estadoLin;
            $lin->token = $albs[$i]->tokenLin;
            $lin->varios1 = $albs[$i]->varios1Lin;
            $lin->varios2 = $albs[$i]->varios2Lin;
            $lin->varios3 = $albs[$i]->varios3Lin;
            $lin->varios4 = $albs[$i]->varios4Lin;
            $lin->varios5 = $albs[$i]->varios5Lin;
            $lin->varios6 = $albs[$i]->varios6Lin;
            $lin->varios7 = $albs[$i]->varios7Lin;
            $lin->varios8 = $albs[$i]->varios8Lin;
            $lin->varios9 = $albs[$i]->varios9Lin;
            $lin->varios10 = $albs[$i]->varios10Lin;
            $lin->cod_usuario_entidad = $albs[$i]->cod_usu_lin;

            $lins[] = $lin;

        }
        //Metemos el ultimo
        if ($alb) {
            $alb->albaranLines = $lins;
            $albaranes[] = $alb;
        }

        return array("albaranes" => $albaranes?$albaranes:array());

    }


    /**
     * Funcion que devuelve los albaranes de una entidad
     *  a partir de las fechas de entrega.
     *
     * @param $entityId
     * @param $fecha_entrega
     * @return array(albaranes)
     *
     */
    function getByFechaEntrega($entityId, $fecha_entrega) {

        $query = $this->getByFechaEntregaQuery($entityId, $fecha_entrega);

        $query = $this->db->query($query);

        $albs = $query->result();

        $albaranes = array();
        $lins = array();
        $lastAlb = "";
        $alb = null;
        for ($i=0; $i<count($albs); $i++) {
            if ($lastAlb != $albs[$i]->pk_albaran) {
                if ($alb) {
                    $alb->albaranLines = $lins;
                    $albaranes[] = $alb;
                }
                $alb = new albaran();
                $alb->set($albs[$i]);
                //Asignamos el token y estado que lo hemos renombrado en la consulta
                $alb->estado = $albs[$i]->estadoAlb;
                $alb->token = $albs[$i]->tokenAlb;
                $lins = array();

                $lastAlb = $alb->pk_albaran;
            }
            // Linea de Pedido
            $lin = new albaranLine();
            $lin->set($albs[$i]);
            //Asignamos los campos renombrados
            $lin->estado = $albs[$i]->estadoLin;
            $lin->token = $albs[$i]->tokenLin;
            $lin->varios1 = $albs[$i]->varios1Lin;
            $lin->varios2 = $albs[$i]->varios2Lin;
            $lin->varios3 = $albs[$i]->varios3Lin;
            $lin->varios4 = $albs[$i]->varios4Lin;
            $lin->varios5 = $albs[$i]->varios5Lin;
            $lin->varios6 = $albs[$i]->varios6Lin;
            $lin->varios7 = $albs[$i]->varios7Lin;
            $lin->varios8 = $albs[$i]->varios8Lin;
            $lin->varios9 = $albs[$i]->varios9Lin;
            $lin->varios10 = $albs[$i]->varios10Lin;
            $lin->cod_usuario_entidad = $albs[$i]->cod_usu_lin;

            $lins[] = $lin;

        }
        //Metemos el ultimo
        if ($alb) {
            $alb->albaranLines = $lins;
            $albaranes[] = $alb;
        }

        return array("albaranes" => $albaranes?$albaranes:array());

    }

    /**
     * Funcion que devuelve los albaranes de una entidad
     *  a partir de las fechas de picking.
     *
     * @param $entityId
     * @param $fecha_picking
     * @return array(albaranes)
     *
     */
    function getByFechaPicking($entityId, $fecha_picking) {

        $query = $this->getByFechaPickingQuery($entityId, $fecha_picking);

        $query = $this->db->query($query);

        $albs = $query->result();

        $albaranes = array();
        $lins = array();
        $lastAlb = "";
        $alb = null;
        for ($i=0; $i<count($albs); $i++) {
            if ($lastAlb != $albs[$i]->pk_albaran) {
                if ($alb) {
                    $alb->albaranLines = $lins;
                    $albaranes[] = $alb;
                }
                $alb = new albaran();
                $alb->set($albs[$i]);
                //Asignamos el token y estado que lo hemos renombrado en la consulta
                $alb->estado = $albs[$i]->estadoAlb;
                $alb->token = $albs[$i]->tokenAlb;
                $lins = array();

                $lastAlb = $alb->pk_albaran;
            }
            // Linea de Pedido
            $lin = new albaranLine();
            $lin->set($albs[$i]);
            //Asignamos los campos renombrados
            $lin->estado = $albs[$i]->estadoLin;
            $lin->token = $albs[$i]->tokenLin;
            $lin->varios1 = $albs[$i]->varios1Lin;
            $lin->varios2 = $albs[$i]->varios2Lin;
            $lin->varios3 = $albs[$i]->varios3Lin;
            $lin->varios4 = $albs[$i]->varios4Lin;
            $lin->varios5 = $albs[$i]->varios5Lin;
            $lin->varios6 = $albs[$i]->varios6Lin;
            $lin->varios7 = $albs[$i]->varios7Lin;
            $lin->varios8 = $albs[$i]->varios8Lin;
            $lin->varios9 = $albs[$i]->varios9Lin;
            $lin->varios10 = $albs[$i]->varios10Lin;

            $lins[] = $lin;

        }
        //Metemos el ultimo
        if ($alb) {
            $alb->albaranLines = $lins;
            $albaranes[] = $alb;
        }

        return array("albaranes" => $albaranes?$albaranes:array());

    }

    /**
     * Funcion que devuelve los albaranes de una entidad
     *  a partir de una fecha de actualizacion y desde una fecha determinada.
     * El parametro state establece el estado de los albranes a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $entityId
     * @param $fromDate
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array(albaranes)
     *
     */
    function getMultipartCachedAlbaranes($entityId, $pagination, $state, $lastTimeStamp, $fromDate) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $albaranes = unserialize($this->esocialmemcache->get($key));
            if (!$albaranes) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
        } else {
            $query = $this->getEntityQuery($entityId, $state, $lastTimeStamp, $fromDate);

            $query = $this->db->query($query);

            $albs = $query->result();

            $albaranes = array();
            $lins = array();
            $lastAlb = "";
            $alb = null;
            for ($i=0; $i<count($albs); $i++) {
                if ($lastAlb != $albs[$i]->pk_albaran) {
                    if ($alb) {
                        $alb->albaranLines = $lins;
                        $albaranes[] = $alb;
                    }
                    $alb = new albaran();
                    $alb->set($albs[$i]);
                    //Asignamos el token y estado que lo hemos renombrado en la consulta
                    $alb->estado = $albs[$i]->estadoAlb;
                    $alb->token = $albs[$i]->tokenAlb;
                    $lins = array();

                    $lastAlb = $alb->pk_albaran;
                }
                // Linea de Pedido
                $lin = new albaranLine();
                $lin->set($albs[$i]);
                //Asignamos los campos renombrados
                $lin->estado = $albs[$i]->estadoLin;
                $lin->token = $albs[$i]->tokenLin;
                $lin->varios1 = $albs[$i]->varios1Lin;
                $lin->varios2 = $albs[$i]->varios2Lin;
                $lin->varios3 = $albs[$i]->varios3Lin;
                $lin->varios4 = $albs[$i]->varios4Lin;
                $lin->varios5 = $albs[$i]->varios5Lin;
                $lin->varios6 = $albs[$i]->varios6Lin;
                $lin->varios7 = $albs[$i]->varios7Lin;
                $lin->varios8 = $albs[$i]->varios8Lin;
                $lin->varios9 = $albs[$i]->varios9Lin;
                $lin->varios10 = $albs[$i]->varios10Lin;

                $lins[] = $lin;

            }
            //Metemos el ultimo
            if ($alb) {
                $alb->albaranLines = $lins;
                $albaranes[] = $alb;
            }

            $rowcount = sizeof($albaranes);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($albaranes, $pagination->pageSize);

                $albaranes = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "albaranes" => $albaranes?$albaranes:array());

    }


    /**
     * Funcion que devuelve los albaranes de una entidad
     *  a partir de una fecha de actualizacion y desde una fecha determinada.
     * El parametro state establece el estado de los albranes a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $entityId
     * @param $fromDate
     * @param $proveedores
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array(albaranes)
     *
     */
    function getMultipartCachedAlbaranesByProveedor($entityId, $pagination, $state, $lastTimeStamp, $fromDate, $proveedores) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $albaranes = unserialize($this->esocialmemcache->get($key));
            if (!$albaranes) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
        } else {
            $query = $this->getEntityQueryByProveedor($entityId, $state, $lastTimeStamp, $fromDate, $proveedores);

            $query = $this->db->query($query);

            $albs = $query->result();

            $albaranes = array();
            $lins = array();
            $lastAlb = "";
            $alb = null;
            for ($i=0; $i<count($albs); $i++) {
                if ($lastAlb != $albs[$i]->pk_albaran) {
                    if ($alb) {
                        $alb->albaranLines = $lins;
                        $albaranes[] = $alb;
                    }
                    $alb = new albaran();
                    $alb->set($albs[$i]);
                    //Asignamos el token y estado que lo hemos renombrado en la consulta
                    $alb->estado = $albs[$i]->estadoAlb;
                    $alb->token = $albs[$i]->tokenAlb;
                    $lins = array();

                    $lastAlb = $alb->pk_albaran;
                }
                // Linea de Pedido
                $lin = new albaranLine();
                $lin->set($albs[$i]);
                //Asignamos los campos renombrados
                $lin->estado = $albs[$i]->estadoLin;
                $lin->token = $albs[$i]->tokenLin;
                $lin->varios1 = $albs[$i]->varios1Lin;
                $lin->varios2 = $albs[$i]->varios2Lin;
                $lin->varios3 = $albs[$i]->varios3Lin;
                $lin->varios4 = $albs[$i]->varios4Lin;
                $lin->varios5 = $albs[$i]->varios5Lin;
                $lin->varios6 = $albs[$i]->varios6Lin;
                $lin->varios7 = $albs[$i]->varios7Lin;
                $lin->varios8 = $albs[$i]->varios8Lin;
                $lin->varios9 = $albs[$i]->varios9Lin;
                $lin->varios10 = $albs[$i]->varios10Lin;
                //Reemplazamos el codigo del articulo por el codigo de articulo de proveedor
                if ($albs[$i]->cod_art_prov)
                    $lin->cod_concepto = $albs[$i]->cod_art_prov;

                $lins[] = $lin;

            }
            //Metemos el ultimo
            if ($alb) {
                $alb->albaranLines = $lins;
                $albaranes[] = $alb;
            }

            $rowcount = sizeof($albaranes);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($albaranes, $pagination->pageSize);

                $albaranes = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "albaranes" => $albaranes?$albaranes:array());

    }

    /**
     * Funcion que devuelve los albaranes de los clientes asignados a un usuario (Vendedor o repartidor)
     *  a partir de una fecha de actualizacion y desde una fecha determinada.
     * El parametro state establece el estado de los albranes a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $userPk
     * @param $entityId
     * @param $fromDate
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array(albaranes)
     *
     */
    function getMultipartCachedClientesAlbaranes($userPk, $entityId, $pagination, $state, $lastTimeStamp, $fromDate) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $albaranes = unserialize($this->esocialmemcache->get($key));
            if (!$albaranes) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
        } else {
            $query = $this->getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp, $fromDate);

            $query = $this->db->query($query);

            $albs = $query->result();

            $albaranes = array();
            $lins = array();
            $lastAlb = "";
            $alb = null;
            for ($i=0; $i<count($albs); $i++) {
                if ($lastAlb != $albs[$i]->pk_albaran) {
                    if ($alb) {
                        $alb->albaranLines = $lins;
                        $albaranes[] = $alb;
                    }
                    $alb = new albaran();
                    $alb->set($albs[$i]);
                    //Asignamos el token y estado que lo hemos renombrado en la consulta
                    $alb->estado = $albs[$i]->estadoAlb;
                    $alb->token = $albs[$i]->tokenAlb;
                    $lins = array();

                    $lastAlb = $alb->pk_albaran;
                }
                // Linea de Pedido
                $lin = new albaranLine();
                $lin->set($albs[$i]);
                //Asignamos los campos renombrados
                $lin->estado = $albs[$i]->estadoLin;
                $lin->token = $albs[$i]->tokenLin;
                $lin->varios1 = $albs[$i]->varios1Lin;
                $lin->varios2 = $albs[$i]->varios2Lin;
                $lin->varios3 = $albs[$i]->varios3Lin;
                $lin->varios4 = $albs[$i]->varios4Lin;
                $lin->varios5 = $albs[$i]->varios5Lin;
                $lin->varios6 = $albs[$i]->varios6Lin;
                $lin->varios7 = $albs[$i]->varios7Lin;
                $lin->varios8 = $albs[$i]->varios8Lin;
                $lin->varios9 = $albs[$i]->varios9Lin;
                $lin->varios10 = $albs[$i]->varios10Lin;

                $lins[] = $lin;

            }
            //Metemos el ultimo
            if ($alb) {
                $alb->albaranLines = $lins;
                $albaranes[] = $alb;
            }

            $rowcount = sizeof($albaranes);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($albaranes, $pagination->pageSize);

                $albaranes = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "albaranes" => $albaranes?$albaranes:array());

    }

    /**
     * Funcion que se encarga de guardar una linea.
     *
     * @param $linea
     */
    function saveLinea($linea) {
        $res = $linea->_save(false, true);
        if (!$res) throw new APIexception("Error on albaran_model->saveLinea. Unable to update Albaran Line", ERROR_SAVING_DATA, serialize($linea));
    }

    /**
     * Funcion que se encarga de guardar un albaran y sus lineas.
     * Para cada linea comprueba que no existe ya buscando por el token.
     *
     * @param $albaran
     * @return bool
     * @throws APIexception
     */
    function saveAlbaran($albaran) {
        //$this->db->trans_start(); Las transacciones las hacemos en el controlador ya que hay un bug y no funcionan las transacciones anidadas
        $this->load->model("usuario_model");
        $this->load->model("log_model");
        $this->load->model("articulos/articulo_almacen_model");
        if (!isset($albaran->token)) {
            $albaran->token = getToken();
        }

        $result = $albaran->_save(false, false);

        if ($result) {
            if (isset($albaran->albaranLines)) {
                $albaranLines = $albaran->albaranLines;
                foreach ($albaranLines as $line) {
                    $line->fk_albaran_cab = $albaran->pk_albaran;
                    $line->fk_entidad = $albaran->fk_entidad;
                    $albaranLine = null;
                    //Nos aseguramos que los Tokens no existen
                    if ($line->id_albaran_lin == null && isset($line->token)) {
                        $query = new stdClass();
                        $this->db->where('token', $line->token);
                        $this->db->where('fk_entidad', $albaran->fk_entidad);
                        $query = $this->db->get("albaranes_lin");
                        $albaranLine = $query->row();
                        if ($albaranLine) $line->id_albaran_lin = $albaranLine->id_albaran_lin;
                    }
                    //Comprobamos si tenemos que descontar el stock
                    if ($line->modif_stock) {
                        //Verificamos si es una modificacion de la linea
                        if ($albaranLine) {
                            $incStock = $line->cantidad - $albaranLine->cantidad;
                        } else {
                            $incStock = $line->cantidad * (-1);
                        }
                        $pk_articulo = $line->cod_concepto . "_" . $albaran->fk_entidad; //Aplicamos Convencion
                        $res = $this->articulo_almacen_model->addStockByArtAndAlmacen($albaran->fk_entidad, $pk_articulo, $albaran->fk_almacen, "Albaran", $incStock, $line->lote, $albaran->pk_albaran);

                        if (!$res) throw new APIexception("Error on albaran_model->saveAlbaran. Unable to update UPDATE STOCKS", ERROR_SAVING_DATA, serialize($albaran));
                    }

                    if (!isset($line->token)) {
                        $line->token = getToken();
                    }
                    $res = $line->_save(false, true);
                    if (!$res) throw new APIexception("Error on albaran_model->saveAlbaran. Unable to update Albaran Line", ERROR_SAVING_DATA, serialize($albaran));
                }
            }
            //$this->db->trans_complete();
            return true;
        } else {
            throw new APIexception("Error on albaran_model->saveAlbaran. Unable to update Albaran", ERROR_SAVING_DATA, serialize($albaran));
        }

    }

    function updateHistoricoPicking($entityId, $fk_almacen, $fk_albaran, $fk_articulo, $lote, $bool_picking_realizado) {
        $q = new stdClass();
        $q->bool_picking_realizado = $bool_picking_realizado;
        $q->fecha_picking = date("Y-m-d h:i:sa");
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('fk_almacen', $fk_almacen);
        $this->db->where('fk_articulo', $fk_articulo);
        $this->db->where('pk_movimiento', $fk_albaran);
        $this->db->where('lote', $lote);


        return $this->db->update('historico_movimientos', $q);
    }

    /**
     * @param $entityPk
     *
     * Coge el siguiente numero de albaran de la serie predeterminada y actualiza la tabla
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
            $q->num_albaran = $result->num_albaran + 1;
            $this->db->where('fk_entidad', $entityPk);
            $this->db->where('bool_predeterminada', 1);
            $this->db->where('anio = YEAR(NOW())');
            $this->db->where('serie', $result->serie);


            $this->db->update('series', $q);

            $this->db->trans_complete();

            $result->num_albaran =  $result->num_albaran + 1;
            return $result;

        } else {
            return null;
        }

    }

    /*
     * Actualiza albaranes_cab y albaranes_lin en base a una condicion
     *
     * $condicion (campo='valor' AND campo=valor)
     * $data (campo=valor;campo=valor)
     *
     * IMPORTANTE: Usar prefijos para hacer referencia a las tablas (cab, lin)
     */
    function updateByCondition($entityId, $condicion, $data){

        $set = str_replace(";", ",", $data);
        $where = "(cab.fk_entidad='".$entityId."') AND (".$condicion.")";

        $query = "UPDATE albaranes_cab cab
                    JOIN albaranes_lin lin ON lin.fk_entidad = cab.fk_entidad AND fk_albaran_cab = pk_albaran AND lin.estado > 0
                    SET $set
                    WHERE $where
                 ";

        return $query = $this->db->query($query);

    }

    /*
     * Elimina albaranes_cab en base a una condicion
     *
     * $condicion (campo='valor' and campo=valor)
     *
     */
    function delCabByCondition($entityId, $condicion){

        $this->db->set('estado', 0, false);

        $where = "(fk_entidad='".$entityId."') AND (".$condicion.")";
        $this->db->where($where);
        return $this->db->update('albaranes_cab');



        return false;
    }

    /*
     * Actualiza albaranes_cab en base a una condicion
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

        $this->db->update('albaranes_cab');



        return false;
    }

    /*
     * Elimina albaranes_lin en base a una condicion
     *
     * $condicion (campo='valor' and campo=valor)
     *
     */
    function delLineByCondition($entityId, $condicion){

        $this->db->set('estado', 0, false);

        $where = "(fk_entidad='".$entityId."') AND (".$condicion.")";
        $this->db->where($where);
        return $this->db->update('albaranes_lin');



        return false;
    }

    /*
     * Actualiza albaranes_lin en base a una condicion
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

        $this->db->update('albaranes_lin');



        return false;
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
            $return = 'pk_albaran';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('albaranes_cab');
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