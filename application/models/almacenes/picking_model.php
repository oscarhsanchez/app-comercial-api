<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_R_ART_ALM);
require_once(APPPATH.ENTITY_ALMACEN);
require_once(APPPATH.ENTITY_INVENTARIO);
require_once(APPPATH.ENTITY_INVENTARIO_LIN);
require_once(APPPATH.ENTITY_INCIDENCIA_PICKING);


class picking_model extends CI_Model {

    /**
     * Devuelve los albaranes pednientes de picking
     *
     * @param $entityId
     * @param $almacenPk
     * @param $fecha (Opcional)
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     *
     * @return array
     */
    function getPickingByAlbaran($entityId, $almacenPk, $fecha, $offset, $limit) {
        $q = "SELECT pk_albaran, fecha_entrega, cod_albaran, cod_cliente, raz_social, nif, poblacion  FROM albaranes_cab WHERE fk_entidad = $entityId AND picking_estado = 1 AND fk_almacen = '$almacenPk' ";


        if ($fecha)
            $q .= " AND fecha_entrega = '" . $fecha . "'";

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $q .= " LIMIT " . $limit . " OFFSET " . $offset;

        $query = $this->db->query($q);
        $result = $query->result();

        return array("picking" => $result?$result:array());

    }

    /**
     * Devuelve los albaranes pednientes de picking agrupados por clientes
     *
     * @param $entityId
     * @param $almacenPk
     * @param $fecha (Opcional)
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     *
     * @return array
     */
    function getPickingByCliente($entityId, $almacenPk, $fecha, $offset, $limit) {
        $q = "SELECT DISTINCT fk_cliente, cod_cliente, raz_social, nif, poblacion  FROM albaranes_cab WHERE fk_entidad = $entityId AND picking_estado = 1 AND fk_almacen = '$almacenPk' ";


        if ($fecha)
            $q .= " AND fecha_entrega = '" . $fecha . "'";

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $q .= " LIMIT " . $limit . " OFFSET " . $offset;

        $query = $this->db->query($q);
        $result = $query->result();

        return array("picking" => $result?$result:array());

    }

    /**
     * Devuelve los albaranes pednientes de picking agrupados por repartidor
     *
     * @param $entityId
     * @param $almacenPk
     * @param $fecha (Opcional)
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     *
     * @return array
     */
    function getPickingByRepartidor($entityId, $almacenPk, $fecha, $offset, $limit) {

        $q = "SELECT DISTINCT fk_repartidor, rusu.cod_usuario_entidad, apellidos, nombre, mail, telefono  FROM albaranes_cab cab
                 JOIN r_usu_emp rusu ON rusu.fk_entidad =  cab.fk_entidad AND fk_repartidor = pk_usuario_entidad
                 JOIN usuarios usu ON usu.id_usuario = rusu.id_usuario
                 WHERE cab.fk_entidad = $entityId AND picking_estado = 1 AND fk_almacen = '$almacenPk' ";


        if ($fecha)
            $q .= " AND fecha_entrega = '" . $fecha . "'";

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $q .= " LIMIT " . $limit . " OFFSET " . $offset;

        $query = $this->db->query($q);
        $result = $query->result();

        return array("picking" => $result?$result:array());

    }

    /**
     * Devuelve las lineas de un picking
     *
     * @param $entityId
     * @param $almacenPk
     * @param $fecha (Opcional)
     * @param $albaranPk (Opcional)
     * @param $clientePk (Opcional)
     * @param $repartidorPk (Opcional)
     *
     * @return array
     */
    function getPickingLines($entityId, $almacenPk, $fecha, $albaranPk, $clientePk, $repartidorPk) {

        $q = "SELECT pk_albaran, cod_albaran, cod_cliente, raz_social, nif, poblacion, fecha_pedido, hora_pedido, picking_fecha, picking_hora, cab.estado,
                       id_albaran_lin, lin.fk_articulo, cod_concepto, concepto, IFNULL(hist.cantidad*(-1), lin.cantidad) AS cantidad, IFNULL(hist.bool_picking_realizado, lin.bool_picking_realizado) AS bool_picking_realizado,
                       hist.lote, fecha_caducidad, IFNULL(stock, unidades) AS stock_final, stock_min,
                       seccion, pasillo, estanteria, balda, hueco, art.codigo_ean AS ean_articulo, lotes.codigo_ean AS ean_lote
                FROM albaranes_cab cab
                JOIN albaranes_lin lin ON cab.fk_entidad = cab.fk_entidad AND fk_albaran_cab = pk_albaran AND lin.estado > 0
                LEFT JOIN articulos art ON art.fk_entidad = cab.fk_entidad AND pk_articulo = lin.fk_articulo AND art.estado > 0
                LEFT JOIN historico_movimientos hist ON hist.fk_entidad = cab.fk_entidad AND hist.fk_articulo = lin.fk_articulo AND pk_albaran = pk_movimiento AND cab.fk_almacen = hist.fk_almacen AND hist.lote IS NOT NULL
                LEFT JOIN articulos_lotes lotes ON lotes.fk_entidad = cab.fk_entidad AND hist.fk_articulo = lotes.fk_articulo AND hist.lote = lotes.lote AND cab.fk_almacen = lotes.fk_almacen AND lotes.estado > 0
                LEFT JOIN r_art_alm rart ON rart.fk_entidad = cab.fk_entidad AND cab.fk_almacen = rart.fk_almacen AND rart.fk_articulo = lin.fk_articulo AND rart.estado > 0
                WHERE cab.estado > 0 AND cab.fk_entidad = $entityId AND picking_estado = 1 AND cab.fk_almacen = '$almacenPk'  AND lin.cantidad > 0";


        if ($fecha)
            $q .= " AND fecha_entrega = '" . $fecha . "'";

        if ($albaranPk)
            $q .= " AND pk_albaran = '" . $albaranPk . "' ";

        if ($clientePk)
            $q .= " AND cab.fk_cliente = '" . $clientePk . "' ";

        if ($repartidorPk)
            $q .= " AND cab.fk_repartidor = '" . $repartidorPk . "' ";


        $query = $this->db->query($q);
        $result = $query->result();

        return array("lines" => $result?$result:array());

    }



}

?>