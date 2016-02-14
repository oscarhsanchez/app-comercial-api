<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class MeatadataInstance extends eEntity {

    public $pk_instance;
    public $fk_pais;
    public $fk_repository;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return "fk_pais";
    }

    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "updated_at");
    }

    public function getTableName() {
        return "pk_metadata_instance";
    }

} 