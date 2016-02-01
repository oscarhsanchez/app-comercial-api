<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class usuario_geo extends eEntity {

    public $pk_usuario_geo;
    public $fk_usuario_entidad;
    public $fk_entidad;
    public $timestamp;
    public $longitud;
    public $latitud;
    public $created_at;
    public $updated_at;
    public $token;

    public function getPK() {
        return array ("pk_usuario_geo");
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "usuarios_geo";
    }

}

?>