<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class SubtipoMedio extends eEntity {

    public $pk_subtipo;
    public $descripcion;
    public $estado;
    public $created_at;
    public $updated_at;
    public $token;


	public function getPK() {
		return "pk_subtipo";
	}

	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "updated_at");
	}

	public function getTableName() {
		return "subtipos_medios";
	}

}

?>