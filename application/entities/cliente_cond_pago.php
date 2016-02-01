<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class cliente_cond_pago extends eEntity {

	public $id;
	public $fk_cliente;
	public $fk_cond_pago;
	public $estado;
	public $token;
	public $created_at;
	public $updated_at;
	
	public function getPK() {
		return array ("id");
	}
	
	public function setPK() {
	}
	
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "cliente_cond_pago";
	}

}

?>