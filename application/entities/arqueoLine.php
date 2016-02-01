<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class arqueoLine extends eEntity {

	public $id_arqueo_caja_lin;
	public $id_arqueo_caja;
	public $fk_forma_pago;
	public $importe;
	public $importe_real;
	public $estado;
	public $created_at;
	public $updated_at;
	public $token;


	public function getPK() {
		return array ("id_arqueo_caja_lin");
	}
	public function setPK() {
		
	}
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "arqueos_caja_lin";
	}

}

?>