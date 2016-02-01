<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class inventarioLine extends eEntity {

    public $id_inventario_lin;
    public $fk_inventario_cab;
    public $fk_articulo;
    public $cod_articulo;
    public $cantidad_ant;
    public $cantidad_new;
    public $lote;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("id_inventario_lin");
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "inventario_lin";
    }

}