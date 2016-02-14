<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class MeatadataStructure extends eEntity {

    public $pk_metadata_structure;
    public $fk_pais;
    public $fk_metadata_repository;
    public $field;
    public $field_type;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return "pk_metadata_structure";
    }

    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "updated_at");
    }

    public function getTableName() {
        return "metadata_structure";
    }

} 