<?php

require_once(APPPATH.ENTITY_ESOCIAL_ENTITY);

class tarjeta extends eEntity {

	public $pk_tarjeta; 
	public $fk_entidad;
    public $fk_cliente;
	public $titular;
    public $num_tarjeta;
	public $mascara;
    public $validez;
	public $cod_seguridad;
    public $estado;
	public $created_at;
    public $updated_at;
	public $token;
	
	public function getPK() {
		return array ("pk_tarjeta");
	}
	
	public function setPK() {
	}
	
	//Este metodo los usamos para definir las propidades que queremos omitir durante la grabacion en bbdd
	public function unSetProperties() {
		return array ("created_at", "update_at");
	}

	public function getTableName() {
		return "cliente_tarjeta";
	}

}

?>