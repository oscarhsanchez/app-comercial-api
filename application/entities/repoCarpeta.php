<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class repoCarpeta extends eEntity {

    public $pk_carpeta;
    public $fk_carpeta_padre;
    public $fk_entidad;
    public $nombre;
    public $path;
    public $estatica;
    public $descripcion;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


    public function getPK() {
        return array ("pk_carpeta");
    }
    public function setPK() {
        //PK Compuesta
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at");
    }

    public function getTableName() {
        return "repo_carpetas";
    }

}