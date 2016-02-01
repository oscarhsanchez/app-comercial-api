<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class provincia extends eEntity {

    public $pk_provincia_entidad;
    public $fk_entidad;
    public $fk_pais_entidad;
    public $cod_provincia;
    public $descripcion;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("id_provincia");
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "provincias";
    }

}