<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Archivo extends eEntity {

    public $pk_archivo;
    public $fk_pais;
    public $nombre;
    public $path;
    public $url;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


	public function getPK() {
		return "pk_archivo";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
        return "archivos";
	}

}

?>