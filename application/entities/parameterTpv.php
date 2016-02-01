<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class parameterTpv extends eEntity {
	
	public $id;
	public $id_entidad;
	public $descripcion;
	public $clave;
	public $valor;
	public $bool_publico;
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
		return "parametro_tpv";
	}

}