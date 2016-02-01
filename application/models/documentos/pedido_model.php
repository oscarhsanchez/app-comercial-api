<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_CLIENT);
require_once(APPPATH.ENTITY_ORDER);
require_once(APPPATH.ENTITY_ORDER_LINE);


class pedido_model extends CI_Model {

    /**
     * ----------------------------------------
     *            FUNCIONES DE APOYO
     * ----------------------------------------
     */
    private function getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp, $fromDate) {
        $q = " SELECT DISTINCT pedidos_cab.pk_pedido, pedidos_cab.fk_entidad, pedidos_cab.fk_usuario, pedidos_cab.fk_serie_entidad, pedidos_cab.serie, pedidos_cab.anio, pedidos_cab.fk_albaran_destino, pedidos_cab.fk_cliente, pedidos_cab.fk_delegacion, pedidos_cab.fk_terminal_tpv, pedidos_cab.fk_forma_pago, pedidos_cab.fk_condicion_pago, pedidos_cab.fk_almacen,
                pedidos_cab.cod_pedido, pedidos_cab.cod_usuario_entidad, pedidos_cab.num_serie, pedidos_cab.cod_cliente, pedidos_cab.cod_delegacion, pedidos_cab.cod_terminal_tpv, pedidos_cab.bool_actualiza_numeracion, pedidos_cab.bool_recalcular, pedidos_cab.fecha,
                pedidos_cab.raz_social, pedidos_cab.nif, pedidos_cab.direccion, pedidos_cab.poblacion, pedidos_cab.provincia, pedidos_cab.codpostal, pedidos_cab.base_imponible_tot, pedidos_cab.imp_desc_tot, pedidos_cab.imp_iva_tot, pedidos_cab.imp_re_tot, pedidos_cab.imp_total, pedidos_cab.observaciones, pedidos_cab.cod_forma_pago,
                pedidos_cab.cod_condicion_pago, pedidos_cab.varios1, pedidos_cab.varios2, pedidos_cab.varios3, pedidos_cab.varios4, pedidos_cab.varios5, pedidos_cab.varios6, pedidos_cab.varios7, pedidos_cab.varios8, pedidos_cab.varios9, pedidos_cab.varios10, pedidos_cab.estado as estadoPed, pedidos_cab.token AS tokenPed,
                pedidos_cab.fk_repartidor, pedidos_cab.fk_repartidor_reasignado, pedidos_cab.tipo_pedido, pedidos_cab.fecha_entrega, pedidos_cab.hora_entrega, pedidos_cab.token_visita, pedidos_cab.hora,

                pedidos_lin.id_pedido_lin, pedidos_lin.fk_pedido_cab, pedidos_lin.fk_usuario as fk_usuarioLin, pedidos_lin.fk_articulo, pedidos_lin.fk_tarifa, pedidos_lin.cod_usuario_entidad, pedidos_lin.cod_concepto, pedidos_lin.concepto, pedidos_lin.cantidad, pedidos_lin.precio, pedidos_lin.precio_original, pedidos_lin.base_imponible, pedidos_lin.descuento, pedidos_lin.imp_descuento, pedidos_lin.iva, pedidos_lin.imp_iva,
	            pedidos_lin.re, pedidos_lin.imp_re, pedidos_lin.total_lin, pedidos_lin.varios1 AS varios1Lin, pedidos_lin.varios2  AS varios2Lin, pedidos_lin.varios3 AS varios3Lin, pedidos_lin.varios4 AS varios4Lin, pedidos_lin.varios5 AS varios5Lin, pedidos_lin.varios6 AS varios6Lin, pedidos_lin.varios7 AS varios7Lin,
	            pedidos_lin.varios8 AS varios8Lin, pedidos_lin.varios9 AS varios9Lin, pedidos_lin.varios10 AS varios10Lin, pedidos_lin.estado as estadoLin, pedidos_lin.token as tokenLin,  pedidos_lin.desc_promocion, pedidos_lin.imp_promocion, pedidos_lin.fk_promocion, pedidos_lin.cod_camp, pedidos_lin.precio_punto_verde, pedidos_lin.coste_medio, pedidos_lin.lote, pedidos_lin.bool_precio_neto,  pedidos_lin.cantidad_original

             FROM clientes
             LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = ".$entityId." AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '".$userPk."' OR fk_usuario_repartidor = '".$userPk."') AND r_usu_cli.estado >= ".$state."
             LEFT JOIN visita ON visita.fk_entidad = ".$entityId." AND clientes.pk_cliente = visita.fk_cliente AND fk_vendedor_reasignado = '".$userPk."' AND visita.estado > 0
             LEFT JOIN albaranes_cab ON albaranes_cab.fk_entidad = ".$entityId." AND clientes.pk_cliente = albaranes_cab.fk_cliente AND (fk_repartidor = '".$userPk."' OR fk_repartidor_reasignado = '".$userPk."') AND albaranes_cab.estado > 0 AND albaranes_cab.fecha_entrega >= CURDATE() AND albaranes_cab.updated_at > '".$lastTimeStamp."'
             JOIN pedidos_cab ON pedidos_cab.fk_entidad = ".$entityId." AND pedidos_cab.fk_cliente = clientes.pk_cliente AND pedidos_cab.estado >= ".$state."
             JOIN pedidos_lin ON pedidos_lin.fk_pedido_cab = pedidos_cab.pk_pedido AND pedidos_lin.estado >= ".$state."
             WHERE clientes.bool_es_captacion = 0 AND  clientes.fk_entidad = ".$entityId."
             AND (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL  OR visita.id IS NOT NULL  OR (albaranes_cab.pk_albaran IS NOT NULL AND bool_asignacion_generica = 0 AND r_usu_cli.pk_usuario_cliente IS NULL) )
             AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR albaranes_cab.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR visita.updated_vendedor_at > '".$lastTimeStamp."' OR pedidos_cab.updated_at > '".$lastTimeStamp."' OR pedidos_lin.updated_at > '".$lastTimeStamp."' )
             AND clientes.estado > 0
             AND pedidos_cab.fecha >= '".$fromDate."'
             ORDER BY pedidos_cab.pk_pedido";


        return $q;
    }

    private function getEntityQuery($entityId, $state, $lastTimeStamp, $fromDate) {
        $q = " SELECT DISTINCT pedidos_cab.pk_pedido, pedidos_cab.fk_entidad, pedidos_cab.fk_usuario, pedidos_cab.fk_serie_entidad, pedidos_cab.serie, pedidos_cab.anio, pedidos_cab.fk_albaran_destino, pedidos_cab.fk_cliente, pedidos_cab.fk_delegacion, pedidos_cab.fk_terminal_tpv, pedidos_cab.fk_forma_pago, pedidos_cab.fk_condicion_pago, pedidos_cab.fk_almacen,
                pedidos_cab.cod_pedido, pedidos_cab.cod_usuario_entidad, pedidos_cab.num_serie, pedidos_cab.cod_cliente, pedidos_cab.cod_delegacion, pedidos_cab.cod_terminal_tpv, pedidos_cab.bool_actualiza_numeracion, pedidos_cab.bool_recalcular, pedidos_cab.fecha,
                pedidos_cab.raz_social, pedidos_cab.nif, pedidos_cab.direccion, pedidos_cab.poblacion, pedidos_cab.provincia, pedidos_cab.codpostal, pedidos_cab.base_imponible_tot, pedidos_cab.imp_desc_tot, pedidos_cab.imp_iva_tot, pedidos_cab.imp_re_tot, pedidos_cab.imp_total, pedidos_cab.observaciones, pedidos_cab.cod_forma_pago,
                pedidos_cab.cod_condicion_pago, pedidos_cab.varios1, pedidos_cab.varios2, pedidos_cab.varios3, pedidos_cab.varios4, pedidos_cab.varios5, pedidos_cab.varios6, pedidos_cab.varios7, pedidos_cab.varios8, pedidos_cab.varios9, pedidos_cab.varios10, pedidos_cab.estado as estadoPed, pedidos_cab.token AS tokenPed,
                pedidos_cab.fk_repartidor, pedidos_cab.fk_repartidor_reasignado, pedidos_cab.tipo_pedido, pedidos_cab.fecha_entrega, pedidos_cab.hora_entrega, pedidos_cab.token_visita, pedidos_cab.hora,

                pedidos_lin.id_pedido_lin, pedidos_lin.fk_pedido_cab, pedidos_lin.fk_usuario as fk_usuarioLin, pedidos_lin.fk_articulo, pedidos_lin.fk_tarifa, pedidos_lin.cod_usuario_entidad, pedidos_lin.cod_concepto, pedidos_lin.concepto, pedidos_lin.cantidad, pedidos_lin.precio, pedidos_lin.precio_original, pedidos_lin.base_imponible, pedidos_lin.descuento, pedidos_lin.imp_descuento, pedidos_lin.iva, pedidos_lin.imp_iva,
	            pedidos_lin.re, pedidos_lin.imp_re, pedidos_lin.total_lin, pedidos_lin.varios1 AS varios1Lin, pedidos_lin.varios2  AS varios2Lin, pedidos_lin.varios3 AS varios3Lin, pedidos_lin.varios4 AS varios4Lin, pedidos_lin.varios5 AS varios5Lin, pedidos_lin.varios6 AS varios6Lin, pedidos_lin.varios7 AS varios7Lin,
	            pedidos_lin.varios8 AS varios8Lin, pedidos_lin.varios9 AS varios9Lin, pedidos_lin.varios10 AS varios10Lin, pedidos_lin.estado as estadoLin, pedidos_lin.token as tokenLin,  pedidos_lin.desc_promocion, pedidos_lin.imp_promocion, pedidos_lin.fk_promocion, pedidos_lin.cod_camp, pedidos_lin.precio_punto_verde, pedidos_lin.coste_medio, pedidos_lin.lote, pedidos_lin.bool_precio_neto, pedidos_lin.cantidad_original

             FROM pedidos_cab
             JOIN pedidos_lin ON pedidos_lin.fk_entidad = pedidos_cab.fk_entidad AND pedidos_lin.fk_pedido_cab = pedidos_cab.pk_pedido AND pedidos_lin.estado >= ".$state."
             WHERE pedidos_cab.fk_entidad = ".$entityId."
             AND (pedidos_cab.updated_at > '".$lastTimeStamp."' OR pedidos_lin.updated_at > '".$lastTimeStamp."' )
             AND pedidos_cab.estado >= ".$state."
             AND pedidos_cab.fecha >= '".$fromDate."'
             ORDER BY pedidos_cab.pk_pedido";


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

        $q = " SELECT DISTINCT pedidos_cab.pk_pedido, pedidos_cab.fk_entidad, pedidos_cab.fk_usuario, pedidos_cab.fk_serie_entidad, pedidos_cab.serie, pedidos_cab.anio, pedidos_cab.fk_albaran_destino, pedidos_cab.fk_cliente, pedidos_cab.fk_delegacion, pedidos_cab.fk_terminal_tpv, pedidos_cab.fk_forma_pago, pedidos_cab.fk_condicion_pago, pedidos_cab.fk_almacen,
                pedidos_cab.cod_pedido, pedidos_cab.cod_usuario_entidad, pedidos_cab.num_serie, pedidos_cab.cod_cliente, pedidos_cab.cod_delegacion, pedidos_cab.cod_terminal_tpv, pedidos_cab.bool_actualiza_numeracion, pedidos_cab.bool_recalcular, pedidos_cab.fecha,
                pedidos_cab.raz_social, pedidos_cab.nif, pedidos_cab.direccion, pedidos_cab.poblacion, pedidos_cab.provincia, pedidos_cab.codpostal, pedidos_cab.base_imponible_tot, pedidos_cab.imp_desc_tot, pedidos_cab.imp_iva_tot, pedidos_cab.imp_re_tot, pedidos_cab.imp_total, pedidos_cab.observaciones, pedidos_cab.cod_forma_pago,
                pedidos_cab.cod_condicion_pago, pedidos_cab.varios1, pedidos_cab.varios2, pedidos_cab.varios3, pedidos_cab.varios4, pedidos_cab.varios5, pedidos_cab.varios6, pedidos_cab.varios7, pedidos_cab.varios8, pedidos_cab.varios9, pedidos_cab.varios10, pedidos_cab.estado as estadoPed, pedidos_cab.token AS tokenPed,
                pedidos_cab.fk_repartidor, pedidos_cab.fk_repartidor_reasignado, pedidos_cab.tipo_pedido, pedidos_cab.fecha_entrega, pedidos_cab.hora_entrega, pedidos_cab.token_visita, pedidos_cab.hora, pedidos_cab.fk_proveedor,

                pedidos_lin.id_pedido_lin, pedidos_lin.fk_pedido_cab, pedidos_lin.fk_usuario as fk_usuarioLin, pedidos_lin.fk_articulo, pedidos_lin.fk_tarifa, pedidos_lin.cod_usuario_entidad, pedidos_lin.cod_concepto, pedidos_lin.concepto, pedidos_lin.cantidad, pedidos_lin.precio, pedidos_lin.precio_original, pedidos_lin.base_imponible, pedidos_lin.descuento, pedidos_lin.imp_descuento, pedidos_lin.iva, pedidos_lin.imp_iva,
	            pedidos_lin.re, pedidos_lin.imp_re, pedidos_lin.total_lin, pedidos_lin.varios1 AS varios1Lin, pedidos_lin.varios2  AS varios2Lin, pedidos_lin.varios3 AS varios3Lin, pedidos_lin.varios4 AS varios4Lin, pedidos_lin.varios5 AS varios5Lin, pedidos_lin.varios6 AS varios6Lin, pedidos_lin.varios7 AS varios7Lin,
	            pedidos_lin.varios8 AS varios8Lin, pedidos_lin.varios9 AS varios9Lin, pedidos_lin.varios10 AS varios10Lin, pedidos_lin.estado as estadoLin, pedidos_lin.token as tokenLin,  pedidos_lin.desc_promocion, pedidos_lin.imp_promocion, pedidos_lin.fk_promocion, pedidos_lin.cod_camp, pedidos_lin.precio_punto_verde, pedidos_lin.coste_medio, pedidos_lin.lote, pedidos_lin.bool_precio_neto,  pedidos_lin.cantidad_original,

	            r_art_pro.cod_art_prov

             FROM pedidos_cab
             JOIN pedidos_lin ON pedidos_lin.fk_entidad = pedidos_cab.fk_entidad AND pedidos_lin.fk_pedido_cab = pedidos_cab.pk_pedido AND pedidos_lin.estado >= ".$state."
             JOIN r_art_pro ON r_art_pro.fk_entidad = pedidos_cab.fk_entidad AND r_art_pro.fk_articulo = pedidos_lin.fk_articulo AND r_art_pro.estado >= ".$state."
             WHERE pedidos_cab.fk_entidad = ".$entityId."
             AND (pedidos_cab.updated_at > '".$lastTimeStamp."' OR pedidos_lin.updated_at > '".$lastTimeStamp."' OR r_art_pro.updated_at > '".$lastTimeStamp."' )
             AND pedidos_cab.estado >= ".$state."
             AND pedidos_cab.fecha >= '".$fromDate."'";

        //Si hay proveedores añadimos la condicion
        if (!$firstProv) $q .= " AND r_art_pro.fk_proveedor IN (".$sql_prov.")";
        $q .= " ORDER BY pedidos_cab.pk_pedido";
        return $q;
    }

    private function getByFechaQuery($entityId, $fecha) {
        $q = " SELECT DISTINCT pedidos_cab.pk_pedido, pedidos_cab.fk_entidad, pedidos_cab.fk_usuario, pedidos_cab.fk_serie_entidad, pedidos_cab.serie, pedidos_cab.anio, pedidos_cab.fk_albaran_destino, pedidos_cab.fk_cliente, pedidos_cab.fk_delegacion, pedidos_cab.fk_terminal_tpv, pedidos_cab.fk_forma_pago, pedidos_cab.fk_condicion_pago, pedidos_cab.fk_almacen,
                pedidos_cab.cod_pedido, pedidos_cab.cod_usuario_entidad, pedidos_cab.num_serie, pedidos_cab.cod_cliente, pedidos_cab.cod_delegacion, pedidos_cab.cod_terminal_tpv, pedidos_cab.bool_actualiza_numeracion, pedidos_cab.bool_recalcular, pedidos_cab.fecha,
                pedidos_cab.raz_social, pedidos_cab.nif, pedidos_cab.direccion, pedidos_cab.poblacion, pedidos_cab.provincia, pedidos_cab.codpostal, pedidos_cab.base_imponible_tot, pedidos_cab.imp_desc_tot, pedidos_cab.imp_iva_tot, pedidos_cab.imp_re_tot, pedidos_cab.imp_total, pedidos_cab.observaciones, pedidos_cab.cod_forma_pago,
                pedidos_cab.cod_condicion_pago, pedidos_cab.varios1, pedidos_cab.varios2, pedidos_cab.varios3, pedidos_cab.varios4, pedidos_cab.varios5, pedidos_cab.varios6, pedidos_cab.varios7, pedidos_cab.varios8, pedidos_cab.varios9, pedidos_cab.varios10, pedidos_cab.estado as estadoPed, pedidos_cab.token AS tokenPed,
                pedidos_cab.fk_repartidor, pedidos_cab.fk_repartidor_reasignado, pedidos_cab.tipo_pedido, pedidos_cab.fecha_entrega, pedidos_cab.hora_entrega, pedidos_cab.token_visita, pedidos_cab.hora, pedidos_cab.fk_proveedor,

                pedidos_lin.id_pedido_lin, pedidos_lin.fk_pedido_cab, pedidos_lin.fk_usuario as fk_usuarioLin, pedidos_lin.fk_articulo, pedidos_lin.fk_tarifa, pedidos_lin.cod_usuario_entidad, pedidos_lin.cod_concepto, pedidos_lin.concepto, pedidos_lin.cantidad, pedidos_lin.precio, pedidos_lin.precio_original, pedidos_lin.base_imponible, pedidos_lin.descuento, pedidos_lin.imp_descuento, pedidos_lin.iva, pedidos_lin.imp_iva,
	            pedidos_lin.re, pedidos_lin.imp_re, pedidos_lin.total_lin, pedidos_lin.varios1 AS varios1Lin, pedidos_lin.varios2  AS varios2Lin, pedidos_lin.varios3 AS varios3Lin, pedidos_lin.varios4 AS varios4Lin, pedidos_lin.varios5 AS varios5Lin, pedidos_lin.varios6 AS varios6Lin, pedidos_lin.varios7 AS varios7Lin,
	            pedidos_lin.varios8 AS varios8Lin, pedidos_lin.varios9 AS varios9Lin, pedidos_lin.varios10 AS varios10Lin, pedidos_lin.estado as estadoLin, pedidos_lin.token as tokenLin,  pedidos_lin.desc_promocion, pedidos_lin.imp_promocion, pedidos_lin.fk_promocion, pedidos_lin.cod_camp, pedidos_lin.precio_punto_verde, pedidos_lin.coste_medio, pedidos_lin.lote, pedidos_lin.bool_precio_neto, pedidos_lin.cantidad_original

             FROM pedidos_cab
             JOIN pedidos_lin ON pedidos_lin.fk_entidad = pedidos_cab.fk_entidad AND pedidos_lin.fk_pedido_cab = pedidos_cab.pk_pedido AND pedidos_lin.estado >= 1
             WHERE pedidos_cab.fk_entidad = ".$entityId."
             AND pedidos_cab.estado >= 1
             AND pedidos_cab.fecha = '".$fecha."'
             ORDER BY pedidos_cab.pk_pedido";


        return $q;
    }

    private function getByFechaEntregaQuery($entityId, $fecha) {

        $q = " SELECT DISTINCT pedidos_cab.pk_pedido, pedidos_cab.fk_entidad, pedidos_cab.fk_usuario, pedidos_cab.fk_serie_entidad, pedidos_cab.serie, pedidos_cab.anio, pedidos_cab.fk_albaran_destino, pedidos_cab.fk_cliente, pedidos_cab.fk_delegacion, pedidos_cab.fk_terminal_tpv, pedidos_cab.fk_forma_pago, pedidos_cab.fk_condicion_pago, pedidos_cab.fk_almacen,
                pedidos_cab.cod_pedido, pedidos_cab.cod_usuario_entidad, pedidos_cab.num_serie, pedidos_cab.cod_cliente, pedidos_cab.cod_delegacion, pedidos_cab.cod_terminal_tpv, pedidos_cab.bool_actualiza_numeracion, pedidos_cab.bool_recalcular, pedidos_cab.fecha,
                pedidos_cab.raz_social, pedidos_cab.nif, pedidos_cab.direccion, pedidos_cab.poblacion, pedidos_cab.provincia, pedidos_cab.codpostal, pedidos_cab.base_imponible_tot, pedidos_cab.imp_desc_tot, pedidos_cab.imp_iva_tot, pedidos_cab.imp_re_tot, pedidos_cab.imp_total, pedidos_cab.observaciones, pedidos_cab.cod_forma_pago,
                pedidos_cab.cod_condicion_pago, pedidos_cab.varios1, pedidos_cab.varios2, pedidos_cab.varios3, pedidos_cab.varios4, pedidos_cab.varios5, pedidos_cab.varios6, pedidos_cab.varios7, pedidos_cab.varios8, pedidos_cab.varios9, pedidos_cab.varios10, pedidos_cab.estado as estadoPed, pedidos_cab.token AS tokenPed,
                pedidos_cab.fk_repartidor, pedidos_cab.fk_repartidor_reasignado, pedidos_cab.tipo_pedido, pedidos_cab.fecha_entrega, pedidos_cab.hora_entrega, pedidos_cab.token_visita, pedidos_cab.hora, pedidos_cab.fk_proveedor,

                pedidos_lin.id_pedido_lin, pedidos_lin.fk_pedido_cab, pedidos_lin.fk_usuario as fk_usuarioLin, pedidos_lin.fk_articulo, pedidos_lin.fk_tarifa, pedidos_lin.cod_usuario_entidad, pedidos_lin.cod_concepto, pedidos_lin.concepto, pedidos_lin.cantidad, pedidos_lin.precio, pedidos_lin.precio_original, pedidos_lin.base_imponible, pedidos_lin.descuento, pedidos_lin.imp_descuento, pedidos_lin.iva, pedidos_lin.imp_iva,
                pedidos_lin.re, pedidos_lin.imp_re, pedidos_lin.total_lin, pedidos_lin.varios1 AS varios1Lin, pedidos_lin.varios2  AS varios2Lin, pedidos_lin.varios3 AS varios3Lin, pedidos_lin.varios4 AS varios4Lin, pedidos_lin.varios5 AS varios5Lin, pedidos_lin.varios6 AS varios6Lin, pedidos_lin.varios7 AS varios7Lin,
                pedidos_lin.varios8 AS varios8Lin, pedidos_lin.varios9 AS varios9Lin, pedidos_lin.varios10 AS varios10Lin, pedidos_lin.estado as estadoLin, pedidos_lin.token as tokenLin,  pedidos_lin.desc_promocion, pedidos_lin.imp_promocion, pedidos_lin.fk_promocion, pedidos_lin.cod_camp, pedidos_lin.precio_punto_verde, pedidos_lin.coste_medio, pedidos_lin.lote, pedidos_lin.bool_precio_neto, pedidos_lin.cantidad_original

             FROM pedidos_cab
             JOIN pedidos_lin ON pedidos_lin.fk_entidad = pedidos_cab.fk_entidad AND pedidos_lin.fk_pedido_cab = pedidos_cab.pk_pedido AND pedidos_lin.estado >= 1
             WHERE pedidos_cab.fk_entidad = ".$entityId."
             AND pedidos_cab.estado >= 1
             AND pedidos_cab.fecha_entrega = '".$fecha."'
             ORDER BY pedidos_cab.pk_pedido";

        return $q;
    }

    private function getByClienteQuery($entityId, $clientePk, $fromDate, $lastTimeStamp, $state) {

        $q = " SELECT DISTINCT pedidos_cab.pk_pedido, pedidos_cab.fk_entidad, pedidos_cab.fk_usuario, pedidos_cab.fk_serie_entidad, pedidos_cab.serie, pedidos_cab.anio, pedidos_cab.fk_albaran_destino, pedidos_cab.fk_cliente, pedidos_cab.fk_delegacion, pedidos_cab.fk_terminal_tpv, pedidos_cab.fk_forma_pago, pedidos_cab.fk_condicion_pago, pedidos_cab.fk_almacen,
                pedidos_cab.cod_pedido, pedidos_cab.cod_usuario_entidad, pedidos_cab.num_serie, pedidos_cab.cod_cliente, pedidos_cab.cod_delegacion, pedidos_cab.cod_terminal_tpv, pedidos_cab.bool_actualiza_numeracion, pedidos_cab.bool_recalcular, pedidos_cab.fecha,
                pedidos_cab.raz_social, pedidos_cab.nif, pedidos_cab.direccion, pedidos_cab.poblacion, pedidos_cab.provincia, pedidos_cab.codpostal, pedidos_cab.base_imponible_tot, pedidos_cab.imp_desc_tot, pedidos_cab.imp_iva_tot, pedidos_cab.imp_re_tot, pedidos_cab.imp_total, pedidos_cab.observaciones, pedidos_cab.cod_forma_pago,
                pedidos_cab.cod_condicion_pago, pedidos_cab.varios1, pedidos_cab.varios2, pedidos_cab.varios3, pedidos_cab.varios4, pedidos_cab.varios5, pedidos_cab.varios6, pedidos_cab.varios7, pedidos_cab.varios8, pedidos_cab.varios9, pedidos_cab.varios10, pedidos_cab.estado as estadoPed, pedidos_cab.token AS tokenPed,
                pedidos_cab.fk_repartidor, pedidos_cab.fk_repartidor_reasignado, pedidos_cab.tipo_pedido, pedidos_cab.fecha_entrega, pedidos_cab.hora_entrega, pedidos_cab.token_visita, pedidos_cab.hora, pedidos_cab.fk_proveedor,

                pedidos_lin.id_pedido_lin, pedidos_lin.fk_pedido_cab, pedidos_lin.fk_usuario as fk_usuarioLin, pedidos_lin.fk_articulo, pedidos_lin.fk_tarifa, pedidos_lin.cod_usuario_entidad, pedidos_lin.cod_concepto, pedidos_lin.concepto, pedidos_lin.cantidad, pedidos_lin.precio, pedidos_lin.precio_original, pedidos_lin.base_imponible, pedidos_lin.descuento, pedidos_lin.imp_descuento, pedidos_lin.iva, pedidos_lin.imp_iva,
                pedidos_lin.re, pedidos_lin.imp_re, pedidos_lin.total_lin, pedidos_lin.varios1 AS varios1Lin, pedidos_lin.varios2  AS varios2Lin, pedidos_lin.varios3 AS varios3Lin, pedidos_lin.varios4 AS varios4Lin, pedidos_lin.varios5 AS varios5Lin, pedidos_lin.varios6 AS varios6Lin, pedidos_lin.varios7 AS varios7Lin,
                pedidos_lin.varios8 AS varios8Lin, pedidos_lin.varios9 AS varios9Lin, pedidos_lin.varios10 AS varios10Lin, pedidos_lin.estado as estadoLin, pedidos_lin.token as tokenLin,  pedidos_lin.desc_promocion, pedidos_lin.imp_promocion, pedidos_lin.fk_promocion, pedidos_lin.cod_camp, pedidos_lin.precio_punto_verde, pedidos_lin.coste_medio, pedidos_lin.lote, pedidos_lin.bool_precio_neto, pedidos_lin.cantidad_original

             FROM pedidos_cab
             JOIN pedidos_lin ON pedidos_lin.fk_entidad = pedidos_cab.fk_entidad AND pedidos_lin.fk_pedido_cab = pedidos_cab.pk_pedido AND pedidos_lin.estado >= 1
             WHERE pedidos_cab.fk_entidad = $entityId
             AND pedidos_cab.estado >= $state
             AND (pedidos_cab.updated_at > '".$lastTimeStamp."' OR pedidos_lin.updated_at > '".$lastTimeStamp."')
             AND pedidos_cab.fk_cliente = '$clientePk'
             AND pedidos_cab.fecha >= '$fromDate'
             ORDER BY pedidos_cab.pk_pedido";

        return $q;
    }

    private function getSugeridoQuery($entityId, $clientePk, $fromDate, $lastTimeStamp, $offset, $limit) {
        $q = "SELECT fk_articulo, ROUND(AVG(cantidad)) AS cantidad, MAX(fecha) AS ultima_compra, fk_cliente
                FROM pedidos_cab cab
                JOIN pedidos_lin lin ON lin.fk_pedido_cab = cab.pk_pedido AND lin.fk_entidad = cab.fk_entidad AND lin.estado > 0
                WHERE cab.fk_entidad = ".$entityId." AND cab.estado > 0 AND fk_cliente = '".$clientePk."' AND fecha >= '".$fromDate."' ";

        if ($lastTimeStamp) $q .= " AND lin.updated_at > '".$lastTimeStamp."'";

        $q .= " GROUP BY fk_articulo  ";
        $q .= " HAVING cantidad > 0  ORDER BY ultima_compra DESC ";

        if ($offset && $limit) $q .= " LIMIT ".$limit." OFFSET ".$offset;

        return $q;
    }

    private function getListQuery($entityId, $clientePk, $offset, $limit, $order, $sort) {
        $q = "SELECT * FROM pedidos_cab WHERE estado > 0 AND fk_entidad = ".$entityId;

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
     * Funcion que devuelve un listado de pedidos.
     *
     * @param $entityId
     * @param $clientePk (opcional)
     * @param $offset (opcional)
     * @param $limit (opcional)
     * @param $order (opcional)
     *
     */
    function listPedidos($entityId, $clientePk, $offset, $limit, $order, $sort) {
        $query = $this->getListQuery($entityId, $clientePk, $offset, $limit, $order, $sort);

        $query = $this->db->query($query);

        $pedidos = $query->result('pedido');

        return array("pedidos" => $pedidos?$pedidos:array());

    }

    /**
     * Funcion que devuelve un pedido a partir de su Token
     *
     * @param $token
     * @param $entityId
     * @return pedido
     */
    function getPedidoByToken($token, $entityId) {

        //CABECERA
        $this->db->where('pedidos_cab.token', $token);
        $this->db->where('pedidos_cab.fk_entidad', $entityId);
        $query = $this->db->get('pedidos_cab');

        $pedido = $query->row(0, 'pedido');

        //LINEAS
        if ($pedido) {
            $this->db->where('fk_pedido_cab', $pedido->pk_pedido);
            $query = $this->db->get('pedidos_lin');
            $pedidoLines = $query->result('pedidoLine');

            $pedido->pedidoLines = $pedidoLines;
        }

        return $pedido;

    }

    /**
     * Funcion que devuelve un pedido a partir de su codigo
     *
     * @param $codigo
     * @param $entityId
     * @return pedido
     */
    function getPedidoByCodigo($entityId, $codigo) {

        //CABECERA
        $this->db->where('pedidos_cab.codigo_pedido', $codigo);
        $this->db->where('pedidos_cab.fk_entidad', $entityId);
        $query = $this->db->get('pedidos_cab');

        $pedido = $query->row(0, 'pedido');

        //LINEAS
        if ($pedido) {
            $this->db->where('fk_pedido_cab', $pedido->pk_pedido);
            $query = $this->db->get('pedidos_lin');
            $pedidoLines = $query->result('pedidoLine');

            $pedido->pedidoLines = $pedidoLines;
        }

        return $pedido;

    }

    /**
     * Funcion que devuelve un pedido a partir de su codigo_ean
     *
     * @param $ean
     * @param $entityId
     * @return pedido
     */
    function getPedidoByCodigoEan($entityId, $ean, $clientePk) {

        //CABECERA
        $this->db->where('pedidos_cab.codigo_ean', $ean);
        $this->db->where('pedidos_cab.fk_cliente', $clientePk);
        $this->db->where('pedidos_cab.fk_entidad', $entityId);
        $query = $this->db->get('pedidos_cab');

        $pedido = $query->row(0, 'pedido');

        //LINEAS
        if ($pedido) {
            $this->db->where('fk_pedido_cab', $pedido->pk_pedido);
            $query = $this->db->get('pedidos_lin');
            $pedidoLines = $query->result('pedidoLine');

            $pedido->pedidoLines = $pedidoLines;
        }

        return $pedido;

    }

    /**
     * Funcion que devuelve la cabecera de un pedido a partir de su codigo
     *
     * @param $codigo
     * @param $entityId
     * @return pedido
     */
    function getCabeceraByCodigo($entityId, $codigo) {

        //CABECERA
        $this->db->where('pedidos_cab.cod_pedido', $codigo);
        $this->db->where('pedidos_cab.fk_entidad', $entityId);
        $query = $this->db->get('pedidos_cab');

        $pedido = $query->row(0, 'pedido');

        return $pedido;

    }

    /**
     * Funcion que devuelve los articulos sugeridos para un pedido de cliente.
     *
     * @param $entityId
     * @param $clientePk
     * @param $fromDate
     * @param $lastUpdate
     * @param $offset
     * @param $limit
     */
    function getSugerido($entityId, $clientePk, $fromDate, $lastUpdate, $offset, $limit) {
        $query = $this->getSugeridoQuery($entityId, $clientePk, $fromDate, $lastUpdate, $offset, $limit);

        $query = $this->db->query($query);

        $sugerido = $query->result();

        return array("sugerido" => $sugerido?$sugerido:array());

    }

    /**
     * Funcion que devuelve los pedidos de un cliente
     *
     * @param $entityId
     * @param $clientePk
     * @param $fromDate
     * @param $lastTimeStamp
     * @param $state
     * @return array(pedidos)
     *
     */
    function getPedidosByCliente($entityId, $clientePk, $fromDate, $lastTimeStamp, $state) {

        $this->load->library('esocialmemcache');


        $query = $this->getByClienteQuery($entityId, $clientePk, $fromDate, $lastTimeStamp, $state);

        $query = $this->db->query($query);

        $peds = $query->result();

        $pedidos = array();
        $lins = array();
        $lastPed = "";
        $ped = null;
        for ($i=0; $i<count($peds); $i++) {
            if ($lastPed != $peds[$i]->pk_pedido) {
                if ($ped) {
                    $ped->pedidoLines = $lins;
                    $pedidos[] = $ped;
                }
                $ped = new pedido();
                $ped->set($peds[$i]);
                //Asignamos el token y estado que lo hemos renombrado en la consulta
                $ped->estado = $peds[$i]->estadoPed;
                $ped->token = $peds[$i]->tokenPed;
                $lins = array();

                $lastPed = $ped->pk_pedido;
            }
            // Linea de Pedido
            $lin = new pedidoLine();
            $lin->set($peds[$i]);
            //Asignamos los campos renombrados
            $lin->fk_usuario = $peds[$i]->fk_usuarioLin;
            $lin->estado = $peds[$i]->estadoLin;
            $lin->token = $peds[$i]->tokenLin;
            $lin->varios1 = $peds[$i]->varios1Lin;
            $lin->varios2 = $peds[$i]->varios2Lin;
            $lin->varios3 = $peds[$i]->varios3Lin;
            $lin->varios4 = $peds[$i]->varios4Lin;
            $lin->varios5 = $peds[$i]->varios5Lin;
            $lin->varios6 = $peds[$i]->varios6Lin;
            $lin->varios7 = $peds[$i]->varios7Lin;
            $lin->varios8 = $peds[$i]->varios8Lin;
            $lin->varios9 = $peds[$i]->varios9Lin;
            $lin->varios10 = $peds[$i]->varios10Lin;

            $lins[] = $lin;

        }
        //Metemos el ultimo
        if ($ped) {
            $ped->pedidoLines = $lins;
            $pedidos[] = $ped;
        }


        return array("pedidos" => $pedidos?$pedidos:array());

    }

    /**
     * Funcion que devuelve los pedidos de la entidad para una fecha concreta
     *
     * @param $entityId
     * @param $fecha
     * @return $pagination<br/> array(pedidos)
     *
     */
    function getPedidosByFecha($entityId, $fecha) {

        $this->load->library('esocialmemcache');


        $query = $this->getByFechaQuery($entityId, $fecha);

        $query = $this->db->query($query);

        $peds = $query->result();

        $pedidos = array();
        $lins = array();
        $lastPed = "";
        $ped = null;
        for ($i=0; $i<count($peds); $i++) {
            if ($lastPed != $peds[$i]->pk_pedido) {
                if ($ped) {
                    $ped->pedidoLines = $lins;
                    $pedidos[] = $ped;
                }
                $ped = new pedido();
                $ped->set($peds[$i]);
                //Asignamos el token y estado que lo hemos renombrado en la consulta
                $ped->estado = $peds[$i]->estadoPed;
                $ped->token = $peds[$i]->tokenPed;
                $lins = array();

                $lastPed = $ped->pk_pedido;
            }
            // Linea de Pedido
            $lin = new pedidoLine();
            $lin->set($peds[$i]);
            //Asignamos los campos renombrados
            $lin->fk_usuario = $peds[$i]->fk_usuarioLin;
            $lin->estado = $peds[$i]->estadoLin;
            $lin->token = $peds[$i]->tokenLin;
            $lin->varios1 = $peds[$i]->varios1Lin;
            $lin->varios2 = $peds[$i]->varios2Lin;
            $lin->varios3 = $peds[$i]->varios3Lin;
            $lin->varios4 = $peds[$i]->varios4Lin;
            $lin->varios5 = $peds[$i]->varios5Lin;
            $lin->varios6 = $peds[$i]->varios6Lin;
            $lin->varios7 = $peds[$i]->varios7Lin;
            $lin->varios8 = $peds[$i]->varios8Lin;
            $lin->varios9 = $peds[$i]->varios9Lin;
            $lin->varios10 = $peds[$i]->varios10Lin;

            $lins[] = $lin;

        }
        //Metemos el ultimo
        if ($ped) {
            $ped->pedidoLines = $lins;
            $pedidos[] = $ped;
        }


        return array("pedidos" => $pedidos?$pedidos:array());

    }

    /**
     * Funcion que devuelve los pedidos de la entidad para una fecha de entrega concreta
     *
     * @param $entityId
     * @param $fecha
     * @return $pagination<br/> array(pedidos)
     *
     */
    function getPedidosByFechaEntrega($entityId, $fecha) {

        $this->load->library('esocialmemcache');


        $query = $this->getByFechaEntregaQuery($entityId, $fecha);

        $query = $this->db->query($query);

        $peds = $query->result();

        $pedidos = array();
        $lins = array();
        $lastPed = "";
        $ped = null;
        for ($i=0; $i<count($peds); $i++) {
            if ($lastPed != $peds[$i]->pk_pedido) {
                if ($ped) {
                    $ped->pedidoLines = $lins;
                    $pedidos[] = $ped;
                }
                $ped = new pedido();
                $ped->set($peds[$i]);
                //Asignamos el token y estado que lo hemos renombrado en la consulta
                $ped->estado = $peds[$i]->estadoPed;
                $ped->token = $peds[$i]->tokenPed;
                $lins = array();

                $lastPed = $ped->pk_pedido;
            }
            // Linea de Pedido
            $lin = new pedidoLine();
            $lin->set($peds[$i]);
            //Asignamos los campos renombrados
            $lin->fk_usuario = $peds[$i]->fk_usuarioLin;
            $lin->estado = $peds[$i]->estadoLin;
            $lin->token = $peds[$i]->tokenLin;
            $lin->varios1 = $peds[$i]->varios1Lin;
            $lin->varios2 = $peds[$i]->varios2Lin;
            $lin->varios3 = $peds[$i]->varios3Lin;
            $lin->varios4 = $peds[$i]->varios4Lin;
            $lin->varios5 = $peds[$i]->varios5Lin;
            $lin->varios6 = $peds[$i]->varios6Lin;
            $lin->varios7 = $peds[$i]->varios7Lin;
            $lin->varios8 = $peds[$i]->varios8Lin;
            $lin->varios9 = $peds[$i]->varios9Lin;
            $lin->varios10 = $peds[$i]->varios10Lin;

            $lins[] = $lin;

        }
        //Metemos el ultimo
        if ($ped) {
            $ped->pedidoLines = $lins;
            $pedidos[] = $ped;
        }


        return array("pedidos" => $pedidos?$pedidos:array());

    }


    /**
     * Funcion que devuelve los pedidos sugeridos de los clientes asignados a un usuario
     *  a partir de una fecha de actualizacion y desde una fecha determinada.
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $entityId
     * @param $userPk
     * @param $fromDate
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array(pedidos)
     *
     */
    function getMultipartCachedSugerido($entityId, $userPk, $pagination, $lastTimeStamp, $fromDate) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $sugerido = unserialize($this->esocialmemcache->get($key));
            if (!$sugerido) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
        } else {
            $query = "SELECT DISTINCT cab.fk_cliente, fk_articulo, ROUND(AVG(cantidad)) AS cantidad, MAX(fecha) AS ultima_compra
                        FROM clientes
                        LEFT JOIN r_usu_cli ON r_usu_cli.fk_entidad = clientes.fk_entidad AND clientes.pk_cliente = r_usu_cli.fk_cliente AND (fk_usuario_vendedor = '$userPk' OR fk_usuario_repartidor = '$userPk') AND r_usu_cli.estado > 0
                        JOIN pedidos_cab cab ON clientes.fk_entidad = cab.fk_entidad AND clientes.pk_cliente = cab.fk_cliente AND cab.estado > 0
                        JOIN pedidos_lin lin ON lin.fk_pedido_cab = cab.pk_pedido AND lin.fk_entidad = cab.fk_entidad AND lin.estado > 0
                        WHERE clientes.bool_es_captacion = 0 AND  (bool_asignacion_generica = 1 OR r_usu_cli.pk_usuario_cliente IS NOT NULL)
                        AND (r_usu_cli.updated_vendedor_at > '".$lastTimeStamp."' OR r_usu_cli.updated_repartidor_at > '".$lastTimeStamp."' OR clientes.updated_at > '".$lastTimeStamp."' OR cab.updated_at > '".$lastTimeStamp."' OR lin.updated_at > '".$lastTimeStamp."' ) AND
                        clientes.fk_entidad = $entityId AND clientes.estado > 0  AND fecha >= '$fromDate' AND fk_articulo IS NOT NULL
                        GROUP BY cab.fk_cliente, fk_articulo";

            $query = $this->db->query($query);

            $sugerido = $query->result();

            $rowcount = sizeof($sugerido);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($sugerido, $pagination->pageSize);

                $sugerido = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "sugerido" => $sugerido?$sugerido:array());

    }

    /**
     * Funcion que devuelve los pedidos de la entidad
     *  a partir de una fecha de actualizacion y desde una fecha determinada.
     * El parametro state establece el estado de los pedidos a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $entityId
     * @param $fromDate
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array(pedidos)
     *
     */
    function getMultipartCachedPedidos($entityId, $pagination, $state, $lastTimeStamp, $fromDate) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $pedidos = unserialize($this->esocialmemcache->get($key));
            if (!$pedidos) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
        } else {
            $query = $this->getEntityQuery($entityId, $state, $lastTimeStamp, $fromDate);

            $query = $this->db->query($query);

            $peds = $query->result();

            $pedidos = array();
            $lins = array();
            $lastPed = "";
            $ped = null;
            for ($i=0; $i<count($peds); $i++) {
                if ($lastPed != $peds[$i]->pk_pedido) {
                    if ($ped) {
                        $ped->pedidoLines = $lins;
                        $pedidos[] = $ped;
                    }
                    $ped = new pedido();
                    $ped->set($peds[$i]);
                    //Asignamos el token y estado que lo hemos renombrado en la consulta
                    $ped->estado = $peds[$i]->estadoPed;
                    $ped->token = $peds[$i]->tokenPed;
                    $lins = array();

                    $lastPed = $ped->pk_pedido;
                }
                // Linea de Pedido
                $lin = new pedidoLine();
                $lin->set($peds[$i]);
                //Asignamos los campos renombrados
                $lin->fk_usuario = $peds[$i]->fk_usuarioLin;
                $lin->estado = $peds[$i]->estadoLin;
                $lin->token = $peds[$i]->tokenLin;
                $lin->varios1 = $peds[$i]->varios1Lin;
                $lin->varios2 = $peds[$i]->varios2Lin;
                $lin->varios3 = $peds[$i]->varios3Lin;
                $lin->varios4 = $peds[$i]->varios4Lin;
                $lin->varios5 = $peds[$i]->varios5Lin;
                $lin->varios6 = $peds[$i]->varios6Lin;
                $lin->varios7 = $peds[$i]->varios7Lin;
                $lin->varios8 = $peds[$i]->varios8Lin;
                $lin->varios9 = $peds[$i]->varios9Lin;
                $lin->varios10 = $peds[$i]->varios10Lin;

                $lins[] = $lin;

            }
            //Metemos el ultimo
            if ($ped) {
                $ped->pedidoLines = $lins;
                $pedidos[] = $ped;
            }

            $rowcount = sizeof($pedidos);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($pedidos, $pagination->pageSize);

                $pedidos = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "pedidos" => $pedidos?$pedidos:array());

    }

    /**
     * Funcion que devuelve los pedidos de la entidad
     *  a partir de una fecha de actualizacion y desde una fecha determinada.
     * El parametro state establece el estado de los pedidos a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $entityId
     * @param $fromDate
     * @param $proveedores
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array(pedidos)
     *
     */
    function getMultipartCachedPedidosByProveedor($entityId, $pagination, $state, $lastTimeStamp, $fromDate, $proveedores) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $pedidos = unserialize($this->esocialmemcache->get($key));
            if (!$pedidos) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
        } else {
            $query = $this->getEntityQueryByProveedor($entityId, $state, $lastTimeStamp, $fromDate, $proveedores);

            $query = $this->db->query($query);

            $peds = $query->result();

            $pedidos = array();
            $lins = array();
            $lastPed = "";
            $ped = null;
            for ($i=0; $i<count($peds); $i++) {
                if ($lastPed != $peds[$i]->pk_pedido) {
                    if ($ped) {
                        $ped->pedidoLines = $lins;
                        $pedidos[] = $ped;
                    }
                    $ped = new pedido();
                    $ped->set($peds[$i]);
                    //Asignamos el token y estado que lo hemos renombrado en la consulta
                    $ped->estado = $peds[$i]->estadoPed;
                    $ped->token = $peds[$i]->tokenPed;
                    $lins = array();

                    $lastPed = $ped->pk_pedido;
                }
                // Linea de Pedido
                $lin = new pedidoLine();
                $lin->set($peds[$i]);
                //Asignamos los campos renombrados
                $lin->fk_usuario = $peds[$i]->fk_usuarioLin;
                $lin->estado = $peds[$i]->estadoLin;
                $lin->token = $peds[$i]->tokenLin;
                $lin->varios1 = $peds[$i]->varios1Lin;
                $lin->varios2 = $peds[$i]->varios2Lin;
                $lin->varios3 = $peds[$i]->varios3Lin;
                $lin->varios4 = $peds[$i]->varios4Lin;
                $lin->varios5 = $peds[$i]->varios5Lin;
                $lin->varios6 = $peds[$i]->varios6Lin;
                $lin->varios7 = $peds[$i]->varios7Lin;
                $lin->varios8 = $peds[$i]->varios8Lin;
                $lin->varios9 = $peds[$i]->varios9Lin;
                $lin->varios10 = $peds[$i]->varios10Lin;
                //Reemplazamos el codigo del articulo por el codigo de articulo de proveedor
                if ($peds[$i]->cod_art_prov)
                    $lin->cod_concepto = $peds[$i]->cod_art_prov;

                $lins[] = $lin;

            }
            //Metemos el ultimo
            if ($ped) {
                $ped->pedidoLines = $lins;
                $pedidos[] = $ped;
            }

            $rowcount = sizeof($pedidos);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($pedidos, $pagination->pageSize);

                $pedidos = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                    $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "pedidos" => $pedidos?$pedidos:array());

    }


    /**
     * Funcion que devuelve los pedidos de los clientes asignados a un usuario (Vendedor o repartidor)
     *  a partir de una fecha de actualizacion y desde una fecha determinada.
     * El parametro state establece el estado de los pedidos a devolver. Estado >= $state
     * El resultado se trocea y se guarda en memcache esperando las proximas llamadas
     *
     * @param $userPk
     * @param $entityId
     * @param $fromDate
     * @param $pagination --> $pageSize, $page, $totalPages, $pagination
     * @param $cache_token
     * @param $state=0
     * @param $lastTimeStamp=null
     * @return $pagination<br/> array(pedidos)
     *
     */
    function getMultipartCachedClientesPedidos($userPk, $entityId, $pagination, $state, $lastTimeStamp, $fromDate) {

        $this->load->library('esocialmemcache');

        if (isset($pagination->cache_token)) {
            $key = $pagination->cache_token . "-" . $pagination->page;
            $pedidos = unserialize($this->esocialmemcache->get($key));
            if (!$pedidos) throw new APIexception("Error Getting Data From Memcache", ERROR_GETTING_INFO, serialize($pagination));
        } else {
            $query = $this->getAssignedQuery($entityId, $userPk, $state, $lastTimeStamp, $fromDate);

            $query = $this->db->query($query);

            $peds = $query->result();
            
            $pedidos = array();
            $lins = array();
            $lastPed = "";
            $ped = null;
            for ($i=0; $i<count($peds); $i++) {
                if ($lastPed != $peds[$i]->pk_pedido) {
                    if ($ped) {
                        $ped->pedidoLines = $lins;
                        $pedidos[] = $ped;
                    }
                    $ped = new pedido();
                    $ped->set($peds[$i]);
                    //Asignamos el token y estado que lo hemos renombrado en la consulta
                    $ped->estado = $peds[$i]->estadoPed;
                    $ped->token = $peds[$i]->tokenPed;
                    $lins = array();

                    $lastPed = $ped->pk_pedido;
                }
                // Linea de Pedido
                $lin = new pedidoLine();
                $lin->set($peds[$i]);
                //Asignamos los campos renombrados
                $lin->fk_usuario = $peds[$i]->fk_usuarioLin;
                $lin->estado = $peds[$i]->estadoLin;
                $lin->token = $peds[$i]->tokenLin;
                $lin->varios1 = $peds[$i]->varios1Lin;
                $lin->varios2 = $peds[$i]->varios2Lin;
                $lin->varios3 = $peds[$i]->varios3Lin;
                $lin->varios4 = $peds[$i]->varios4Lin;
                $lin->varios5 = $peds[$i]->varios5Lin;
                $lin->varios6 = $peds[$i]->varios6Lin;
                $lin->varios7 = $peds[$i]->varios7Lin;
                $lin->varios8 = $peds[$i]->varios8Lin;
                $lin->varios9 = $peds[$i]->varios9Lin;
                $lin->varios10 = $peds[$i]->varios10Lin;

                $lins[] = $lin;

            }
            //Metemos el ultimo
            if ($ped) {
                $ped->pedidoLines = $lins;
                $pedidos[] = $ped;
            }

            $rowcount = sizeof($pedidos);
            if ($rowcount >= MULTIPART_CACHE_PAGINATION_MAX_SIZE) return null;

            $pagination->totalPages = ceil($rowcount / $pagination->pageSize);
            $pagination->page = 0;

            if ($rowcount > $pagination->pageSize) {
                $chunk_result = array_chunk($pedidos, $pagination->pageSize);

                $pedidos = $chunk_result[0];

                $fecha = new DateTime();
                $pagination->cache_token = base64_encode(rand() . '-' . $fecha->getTimestamp() . '-' . rand());

                for ($i=1; $i < sizeof($chunk_result); $i++) {
                   $this->esocialmemcache->add($pagination->cache_token . "-" . $i, serialize($chunk_result[$i]), false, MULTIPART_CACHE_PAGINATION_EXPIRE_TIME);
                }
            }
        }

        return array("pagination" => $pagination, "pedidos" => $pedidos?$pedidos:array());

    }

    /**
     * Funcion que se encarga de guardar un pedido y sus lineas.
     * Para cada linea comprueba que no existe ya buscando por el token.
     *
     * @param $pedido
     * @return bool
     * @throws APIexception
     */
    function savePedido($pedido) {
        //$this->db->trans_start(); Las transacciones las hacemos en el controlador ya que hay un bug y no funcionan las transacciones anidadas
        $this->load->model("usuario_model");
        $this->load->model("log_model");
        if (!isset($pedido->token)) {
            $pedido->token = getToken();
        }

        $result = $pedido->_save(false, false);

        if ($result) {
            if (isset($pedido->pedidoLines)) {
                $pedidoLines = $pedido->pedidoLines;
                foreach ($pedidoLines as $line) {
                    if ($line->concepto != null) {
                        $line->fk_pedido_cab = $pedido->pk_pedido;
                        $line->fk_entidad = $pedido->fk_entidad;
                        //Nos aseguramos que los Tokens no existen
                        if ($line->id_pedido_lin == null && isset($line->token)) {
                            $query = new stdClass();
                            $this->db->where('token', $line->token);
                            $this->db->where('fk_entidad', $pedido->fk_entidad);
                            $query = $this->db->get("pedidos_lin");
                            $pedidoLine = $query->row();
                            if ($pedidoLine) $line->id_pedido_lin = $pedidoLine->id_pedido_lin;
                        }
                        //Si no tenemos la fk_articulo aplicamos convenio
                        if ($line->fk_articulo == null) {
                            $line->fk_articulo = $line->cod_concepto . '_' . $pedido->fk_entidad;
                        }

                        if (!isset($line->token)) {
                            $line->token = getToken();
                        }
                        if (!$line->cantidad_original)
                            $line->cantidad_original = $line->cantidad;

                        $res = $line->_save(false, true);
                        if (!$res) throw new APIexception("Error on pedido_model->savePedido. Unable to update Pedido Line", ERROR_SAVING_DATA, serialize($pedido));
                    }
                }
            }
            //$this->db->trans_complete();			
            return true;
        } else {
            throw new APIexception("Error on pedido_model->savePedido. Unable to update Pedido", ERROR_SAVING_DATA, serialize($pedido));
        }

    }

    /**
     * Actualiza el campo albaran de destiono marca el pedido con estado convertido = 2
     *
     * @param $entityId
     * @param $pk_pedido
     * @param $fk_albaran
     *
     */
    function updateAlbaranDestino($entityId, $pk_pedido, $fk_albaran) {
        $q = new stdClass();
        $q->fk_albaran_destino = $fk_albaran;
        $q->estado = 2;
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('pk_pedido', $pk_pedido);


        return $this->db->update('pedidos_cab', $q);
    }

    /**
     * @param $entityId
     * @param $pedidoPk
     * @param $tarjeta
     * @param $importe
     *
     * Coge el siguiente numero de pedido de la serie predeterminada y actualiza la tabla
     */
    function updatePagoTarjeta($entityId, $pedidoPk, $tarjeta, $importe) {

        $q = new stdClass();
        $q->bool_conf_pago_tarjeta = 1;
        $q->tarjeta = $tarjeta;
        $q->importe_tarjeta = $importe;
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('pk_pedido', $pedidoPk);

        return $this->db->update('pedidos_cab', $q);

    }

    /**
     * Funcion que verifica si un pedido esta pagado con tarjeta
     *
     * @param $pedidoPk
     * @param $entityId
     * @return $pedido->bool_conf_pago_tarjeta
     */
    function verifyPagoTarjeta($entityId, $pedidoPk) {

        $this->db->where('pedidos_cab.pk_pedido', $pedidoPk);
        $this->db->where('pedidos_cab.fk_entidad', $entityId);
        $query = $this->db->get('pedidos_cab');

        $pedido = $query->row(0, 'pedido');

        if ($pedido)
            return $pedido->bool_conf_pago_tarjeta;
        else
            return 0;

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
            $q->num_pedido = $result->num_pedido + 1;
            $this->db->where('fk_entidad', $entityPk);
            $this->db->where('bool_predeterminada', 1);
            $this->db->where('anio = YEAR(NOW())');
            $this->db->where('serie', $result->serie);


            $this->db->update('series', $q);

            $this->db->trans_complete();

            $result->num_pedido =  $result->num_pedido + 1;
            return $result;

        } else {
            return null;
        }

    }


    /*
     * Elimina pedidos_cab en base a una condicion
     *
     * $condicion (campo='valor' and campo=valor)
     *
     */
    function delCabByCondition($entityId, $condicion){

        $this->db->set('estado', 0, false);

        $where = "(fk_entidad='".$entityId."') AND (".$condicion.")";
        $this->db->where($where);
        return $this->db->update('pedidos_cab');



        return false;
    }

    /*
     * Actualiza pedidos_cab y pedidos_lin en base a una condicion
     *
     * $condicion (campo='valor' AND campo=valor)
     * $data (campo=valor;campo=valor)
     *
     * IMPORTANTE: Usar prefijos para hacer referencia a las tablas (cab, lin)
     */
    function updateByCondition($entityId, $condicion, $data){

        $set = str_replace(";", ",", $data);
        $where = "(cab.fk_entidad='".$entityId."') AND (".$condicion.")";

        $query = "UPDATE pedidos_cab cab
                    JOIN pedidos_lin lin ON lin.fk_entidad = cab.fk_entidad AND fk_pedido_cab = pk_pedido AND lin.estado > 0
                    SET $set
                    WHERE $where
                 ";

        return $query = $this->db->query($query);

    }

    /*
     * Actualiza pedidos_cab en base a una condicion
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

        $this->db->update('pedidos_cab');



        return false;
    }

    /*
     * Elimina pedidos_lin en base a una condicion
     *
     * $condicion (campo='valor' and campo=valor)
     *
     */
    function delLineByCondition($entityId, $condicion){

        $this->db->set('estado', 0, false);

        $where = "(fk_entidad='".$entityId."') AND (".$condicion.")";
        $this->db->where($where);
        return $this->db->update('pedidos_lin');



        return false;
    }

    /*
     * Actualiza pedidos_lin en base a una condicion
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

        $this->db->update('pedidos_lin');



        return false;
    }

    /**
     * Funcion que devuelve un resumen anual agrupado por meses (numero de lineas y base imponible).
     *
     * @param $entityId
     * @param $year
     *
     */
    function getYearSummary($entityId, $year) {
        $q = "SELECT MONTH(fecha) AS mes, COUNT(*) AS num_reg, ROUND(SUM(base_imponible),2) AS base_imponible FROM pedidos_cab cab
                JOIN pedidos_lin lin ON lin.fk_entidad = cab.fk_entidad AND fk_pedido_cab = pk_pedido AND lin.estado > 0
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
        $q = "SELECT DAY(fecha) AS dia, COUNT(*) AS num_reg, ROUND(SUM(base_imponible), 2) AS base_imponible FROM pedidos_cab cab
                JOIN pedidos_lin lin ON lin.fk_entidad = cab.fk_entidad AND fk_pedido_cab = pk_pedido AND lin.estado > 0
                WHERE cab.fk_entidad = $entityId AND cab.estado > 0 AND YEAR(fecha) = $year AND MONTH(fecha) = $month
                GROUP BY DAY(fecha)";

        $query = $this->db->query($q);

        $result = $query->result();

        return array("resumen" => $result?$result:array());

        return $q;
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
            $return = 'pk_pedido';
        }

        $this->db->select('DISTINCT '.$return.' AS value,'.$field.' AS description', false);
        $this->db->from('pedidos_cab');
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