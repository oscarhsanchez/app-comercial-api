<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class arqueo extends eEntity {

	public $id_arqueo_caja;
	public $fk_entidad;
	public $fk_usuario;
	public $fk_delegacion;
	public $fk_terminal_tpv;
	public $fecha;
	public $hora;	
	public $estado;
	public $created_at;
	public $updated_at;
	public $token;


	public function getPK() {
		return array ("id_arqueo_caja");
	}
	public function setPK() {
		
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "arqueos_caja_cab";
	}

}

?>