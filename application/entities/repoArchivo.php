<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class repoArchivo extends eEntity {

    public $pk_archivo;
    public $fk_carpeta_padre;
    public $fk_entidad;
    public $nombre;
    public $extension;
    public $path;
    public $disponible_en_terminal;
    public $disponible_en_terminal_desde;
    public $disponible_en_terminal_hasta;
    public $descripcion;
    public $size;
    public $aws_key;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;
	
    public $fileData;

    public function getPK() {
        return array ("pk_archivo");
    }
    public function setPK() {
        //PK Compuesta
    }
    //Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
    public function unSetProperties() {
        return array ("created_at", "update_at", "fileData");
    }

    public function getTableName() {
        return "repo_archivos";
    }

}