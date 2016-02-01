<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class movimientoAlmacenLine extends eEntity {

    public $id_movimiento_lin;
    public $fk_movimiento_cab;
    public $fk_articulo;
    public $cantidad;
    public $cod_articulo;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("id_movimiento_lin");
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "movimientos_almacen_lin";
    }

}