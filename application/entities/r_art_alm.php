<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class r_art_alm extends eEntity {

    public $id_r_art_alm;
    public $fk_almacen;
    public $fk_entidad;
    public $fk_articulo;
    public $stock_min;
    public $stock_max;
    public $unidades;
    public $coste_medio;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;

    public function getPK() {
        return array ("id_r_art_alm");
    }

    public function setPK() {
    }

    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "r_art_alm";
    }

}

?>