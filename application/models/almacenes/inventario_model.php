<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

require_once(APPPATH.ENTITY_R_ART_ALM);
require_once(APPPATH.ENTITY_ALMACEN);
require_once(APPPATH.ENTITY_INVENTARIO);
require_once(APPPATH.ENTITY_INVENTARIO_LIN);


class inventario_model extends CI_Model {

    /**
     * Devuelve los inventarios de un almacen
     *
     * @param $entityId
     * @param $state
     * @param $almacenPk
     * @param $fecha (Opcional)
     * @param $offset (Opcional)
     * @param $limit (Opcional)
     *
     * @return array
     */
    function getAll($entityId, $state, $almacenPk, $fecha, $offset, $limit) {
        $this->db->where('fk_entidad', $entityId);
        $this->db->where('estado >=', $state);
        $this->db->where('fk_almacen', $almacenPk);

        if ($fecha)
            $this->db->where('fecha', $fecha);

        $offset = intval($offset);
        if (is_int($offset) && $limit)
            $this->db->limit($limit, $offset);

        $this->db->order_by("fecha", "desc");
        $this->db->order_by("hora", "desc");

        $query = $this->db->get('inventario_cab');

        $result = $query->result('inventario');

        return array("inventarios" => $result?$result:array());

    }

    /**
     * @param $entityPk
     *
     * Coge el siguiente numero de inventario y actualiza la tabla
     */
    function getCode($entityPk) {

        $this->db->trans_start();

        $this->db->where('fk_entidad', $entityPk);
        $query = $this->db->get('code');

        $result = $r_usu_cli = $query->row(0);

        if ($result) {
            $q = new stdClass();
            $q->last_inventario = $result->last_inventario + 1;
            $this->db->where('fk_entidad', $entityPk);

            $this->db->update('code', $q);

            $this->db->trans_complete();

            return str_pad($result->last_inventario + 1, 7, "0", STR_PAD_LEFT);

        } else {
            return null;
        }

    }

    /**
     * Devuelve las lineas de un inventario
     *
     * @param $inventarioPk
     *
     * @return array
     */
    function getLines($entityId, $inventarioPk) {

        $query = "SELECT lin.*, art.descripcion FROM inventario_lin lin
                    JOIN articulos art ON art.fk_entidad = $entityId AND fk_articulo = pk_articulo
                    WHERE fk_inventario_cab = '$inventarioPk'";

        $query = $this->db->query($query);

        $result = $query->result('inventarioLine');

        return array("lines" => $result?$result:array());

    }



}

?>