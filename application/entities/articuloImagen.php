<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class articuloImagen extends eEntity {

    public $pk_art_imagen;
    public $fk_entidad;
    public $imagen;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;

    public function getPK() {
        return array ("pk_art_imagen");
    }
    public function setPK() {
       //Clave Autonumerica
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "art_imagenes";
    }

}

?>