<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Pais extends eEntity {

    public $pk_pais_entidad;
    public $fk_entidad;
    public $cod_pais;
    public $descripcion;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return "id_pais";
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "paises";
    }

} 