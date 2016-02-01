<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class versionControl extends eEntity {
	
	public $id;	
	public $fk_entidad;
	public $fecha;
	public $version;
	public $version_db;
	public $archivo;
	public $estado;
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
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "control_versiones";
	}

}