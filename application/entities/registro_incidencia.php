<?php
require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class registro_incidencia extends eEntity {

    public $id;
    public $incidencia_id;
    public $descripcion;
    public $fk_usuario_entidad;
    public $nombre_usuario; //Solo para envio al terminal
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
        return array ("created_at", "update_at", "nombre_usuario");
    }

    public function getTableName() {
        return "registro_incidencia";
    }

}