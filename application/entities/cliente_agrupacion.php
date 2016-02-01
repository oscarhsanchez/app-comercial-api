<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class cliente_agrupacion extends eEntity {

	public $pk_cliente_agrupacion;
	public $fk_entidad;
	public $cod_cliente_agrupacion;
	public $name;
	public $description;
	public $estado;
	public $created_at;
	public $updated_at;
	public $token;
	
	public function getPK() {
		return array ("pk_cliente_agrupacion");
	}
	
	public function setPK() {
	}
	
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "cliente_agrupacion";
	}

}

?>