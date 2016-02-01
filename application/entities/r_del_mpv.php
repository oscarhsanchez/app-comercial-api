<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class r_del_mpv extends eEntity {

    public $id;
    public $fk_delegacion;
    public $cod_delegacion_stock;
    public $stock_total;
    public $fk_tipo_mpv;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("id");
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "delegacion_stock";
    }

}