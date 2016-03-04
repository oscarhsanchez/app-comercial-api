<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Zona extends eEntity {

    public $pk_zona;
    public $nombre;
    public $tipo;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return "pk_categoria_propuesta";
    }
    public function setPK() {
        //Autonumerico
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "updated_at");
    }

    public function getTableName() {
        return "categorias_propuestas";
    }

} 