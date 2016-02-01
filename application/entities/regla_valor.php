<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class regla_valor extends eEntity {

    public $id;
    public $regla_parametro_id;
    public $valor1;
    public $tipo1;
    public $valor2;
    public $tipo2;
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
        return "regla_valor";
    }

}