<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class Plaza extends eEntity {

    public $pk_plaza;
    public $fk_pais;
    public $fk_empresa;
    public $nombre;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


	public function getPK() {
		return array ("pk_plaza");
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "plazas";
	}

}

?>