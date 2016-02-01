<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class cliente_contacto extends eEntity {

	public $id;
	public $fk_cliente;
	public $nombre;
	public $telefono;
	public $mail;
	public $cargo;
	public $estado;
	public $created_at;
	public $updated_at;
	public $token;
	
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
		return "cliente_contacto";
	}

}

?>