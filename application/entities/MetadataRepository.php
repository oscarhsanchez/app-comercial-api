<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class MetadataRepository extends eEntity {

    public $pk_metadata_repository;
    public $fk_pais;
    public $name;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return "pk_metadata_repository";
    }

    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "updated_at");
    }

    public function getTableName() {
        return "metadata_repository";
    }

} 