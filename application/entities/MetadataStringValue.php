<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class MeatadataStringValue extends eEntity {

    public $pk_string_value;
    public $fk_pais;
    public $fk_repository;
    public $fk_metadata_structure;
    public $fk_instance;
    public $string_value;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return "pk_string_value";
    }

    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "updated_at");
    }

    public function getTableName() {
        return "metadata_string_value";
    }

} 